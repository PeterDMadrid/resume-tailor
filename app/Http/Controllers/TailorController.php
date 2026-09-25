<?php

namespace App\Http\Controllers;

use App\Http\Requests\TailorRequest;
use App\Http\Requests\UpdateSkillsRequest;
use App\Models\TailoringRun;
use App\Services\GeminiService;
use App\Services\PdfGenerator;
use App\Services\ResumeAssembler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TailorController extends Controller
{
    // Show the JD input form.
    public function create()
    {
        return view('tailor.form');
    }

    // History: past runs, newest first.
    public function index()
    {
        $runs = TailoringRun::latest()->paginate(25);

        return view('tailor.index', compact('runs'));
    }

    // Delete a run and its stored PDF.
    public function destroy(TailoringRun $run, PdfGenerator $pdf)
    {
        if ($run->pdf_path) {
            Storage::disk($pdf->disk())->delete($run->pdf_path);
        }

        $run->delete();

        return back()->with('status', 'Run deleted.');
    }

    // Re-download a stored PDF; verify it still exists on disk first.
    public function download(TailoringRun $run, PdfGenerator $pdf)
    {
        $disk = Storage::disk($pdf->disk());

        if (! $run->pdf_path || ! $disk->exists($run->pdf_path)) {
            return back()->with('error', 'That PDF is no longer available on disk.');
        }

        return $disk->download($run->pdf_path, $pdf->downloadName());
    }

    // Full flow: validate -> tailor via AI -> assemble -> render+store PDF -> download.
    public function store(
        TailorRequest $request,
        GeminiService $gemini,
        ResumeAssembler $assembler,
        PdfGenerator $pdf,
    ) {
        $data = $request->validated();

        $tailored = $gemini->tailor(
            $data['job_description'],
            $data['job_title'] ?? null,
            $data['company_name'] ?? null,
        );

        $viewData = $assembler->build($tailored->toArray());
        $path = $pdf->store($viewData);

        // Record the run before serving (only reached after a successful PDF).
        $run = TailoringRun::create([
            'job_description' => $data['job_description'],
            'company_name' => $data['company_name'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'company_email' => $data['company_email'] ?? null,
            'email_title' => $tailored->emailTitle,
            'email_message' => $tailored->emailMessage,
            'generated_headline' => $tailored->headline,
            'generated_summary' => $tailored->summary,
            'generated_skills' => $tailored->skills,
            'pdf_path' => $path,
            'model_used' => config('services.gemini.model'),
            'status' => $tailored->status(),
            'raw_response' => $tailored->raw,
        ]);

        // Optional: notify n8n. Never let a webhook failure break the flow.
        $this->fireCompanyEmailWebhook($run, $pdf);

        // Land on a result page (download available there + in history).
        return redirect()->route('tailor.result', $run);
    }

    /**
     * Fire the n8n webhook when a company_email was provided on the run.
     * Synchronous by design (single-user prototype). Failures are logged,
     * never thrown, so the user's PDF download is unaffected.
     */
    private function fireCompanyEmailWebhook(TailoringRun $run, PdfGenerator $pdf): void
    {
        if (empty($run->company_email)) {
            return; // behave exactly as before
        }

        try {
            $disk = Storage::disk($pdf->disk());

            if (! $run->pdf_path || ! $disk->exists($run->pdf_path)) {
                Log::warning('Company-email webhook skipped: PDF missing on disk.', [
                    'run_id' => $run->id,
                    'pdf_path' => $run->pdf_path,
                ]);

                return;
            }

            $pdfBase64 = base64_encode($disk->get($run->pdf_path));

            $payload = [
                'company_email' => $run->company_email,
                'job_title' => $run->job_title,
                'job_description' => $run->job_description,
                'email_title' => $run->email_title,
                'email_message' => $run->email_message,
                'resume_pdf_base64' => $pdfBase64,
            ];

            $this->sendWebhook($payload, ['run_id' => $run->id]);
        } catch (\Throwable $e) {
            Log::warning('Company-email webhook failed.', [
                'run_id' => $run->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * POST a payload to the configured n8n webhook. Logs the intended payload
     * (metadata only for the PDF blob) whether or not a URL is set, so it can
     * be inspected. Returns a short human-readable status string.
     *
     * @param  array<string,mixed>  $payload
     * @param  array<string,mixed>  $logContext  extra fields for log lines (e.g. run_id)
     */
    private function sendWebhook(array $payload, array $logContext = []): string
    {
        $url = config('services.n8n.webhook_url');

        Log::info('n8n webhook payload prepared.', array_merge($logContext, [
            'url' => $url ?: '(not configured)',
            'company_email' => $payload['company_email'] ?? null,
            'job_title' => $payload['job_title'] ?? null,
            'email_title' => $payload['email_title'] ?? null,
            'email_message' => $payload['email_message'] ?? null,
            'job_description_chars' => strlen((string) ($payload['job_description'] ?? '')),
            'pdf_base64_bytes' => strlen((string) ($payload['resume_pdf_base64'] ?? '')),
        ]));

        if (empty($url)) {
            Log::info('n8n webhook not sent: N8N_WEBHOOK_URL is not configured.', $logContext);

            return 'N8N_WEBHOOK_URL is not configured — payload was logged but not sent.';
        }

        $response = Http::post($url, $payload);

        if ($response->successful()) {
            Log::info('n8n webhook delivered.', array_merge($logContext, ['status' => $response->status()]));

            return "Webhook delivered (HTTP {$response->status()}).";
        }

        Log::warning('n8n webhook returned a non-success status.', array_merge($logContext, ['status' => $response->status()]));

        return "Webhook returned HTTP {$response->status()}.";
    }

    /**
     * Send a dummy payload to n8n to verify the integration without calling
     * Gemini or generating a real PDF. Fired from a button on the form.
     */
    public function testWebhook(Request $request, ResumeAssembler $assembler)
    {
        // Render the real untailored resume (same as the preview: config only,
        // no AI) so n8n receives an actual resume PDF, not a placeholder.
        $realPdf = Pdf::loadView('resume.template', $assembler->build())->output();

        // Let the tester override the email title; fall back to a default.
        $emailTitle = trim((string) $request->input('email_title'));
        if ($emailTitle === '') {
            $emailTitle = 'Test Email Title - Full-Stack Engineer';
        }

        $payload = [
            'company_email' => 'petermadrid0421@gmail',
            'job_title' => 'Test Job Title',
            'job_description' => 'This is a dummy job description used to test the n8n webhook payload.',
            'email_title' => $emailTitle,
            'email_message' => 'Hi, this is a test message to verify the webhook payload reaches n8n correctly.',
            'resume_pdf_base64' => base64_encode($realPdf),
        ];

        try {
            $result = $this->sendWebhook($payload, ['test' => true]);

            return back()->with('status', "Test webhook: {$result}");
        } catch (\Throwable $e) {
            Log::warning('Test webhook failed.', ['error' => $e->getMessage()]);

            return back()->with('error', "Test webhook failed: {$e->getMessage()}");
        }
    }

    // Result page for a completed run.
    public function result(TailoringRun $run)
    {
        return view('tailor.result', compact('run'));
    }

    // Current Gemini usage as JSON (for the navbar refresh button).
    public function geminiUsage(\App\Services\GeminiUsage $usage)
    {
        return response()->json($usage->snapshot());
    }

    // Regenerate the PDF for a run using a trimmed set of skills (skills-only
    // edit). Overwrites the same run's stored PDF. Headline/summary unchanged.
    public function update(TailoringRun $run, UpdateSkillsRequest $request, ResumeAssembler $assembler, PdfGenerator $pdf)
    {
        $skills = $request->groupedSkills();

        if ($skills === []) {
            return back()->with('error', 'Keep at least one skill.');
        }

        $viewData = $assembler->build([
            'headline' => $run->generated_headline,
            'summary' => $run->generated_summary,
            'skills' => $skills,
        ], mergeConstants: false); // respect the user's trimmed list exactly

        $disk = Storage::disk($pdf->disk());
        if ($run->pdf_path) {
            $disk->delete($run->pdf_path); // overwrite: remove old file
        }

        $run->update([
            'generated_skills' => $skills,
            'pdf_path' => $pdf->store($viewData),
        ]);

        // Re-fire the webhook so n8n gets the regenerated PDF. No-ops when the
        // run has no company_email. Refresh so the new pdf_path is read.
        $this->fireCompanyEmailWebhook($run->fresh(), $pdf);

        return redirect()->route('tailor.result', $run)->with('status', 'Skills updated and PDF regenerated.');
    }

    // Preview the resume rendered from config only (no AI). Streamed inline
    // so it can be embedded in the preview modal's iframe.
    public function preview(ResumeAssembler $assembler, PdfGenerator $generator)
    {
        $pdf = Pdf::loadView('resume.template', $assembler->build());

        return $pdf->stream($generator->downloadName()); // inline (Content-Disposition: inline)
    }
}
