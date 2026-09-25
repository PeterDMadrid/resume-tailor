<?php

namespace App\Http\Controllers;

use App\Http\Requests\TailorRequest;
use App\Http\Requests\UpdateSkillsRequest;
use App\Models\TailoringRun;
use App\Services\GeminiService;
use App\Services\PdfGenerator;
use App\Services\ResumeAssembler;
use Barryvdh\DomPDF\Facade\Pdf;
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

            $url = config('services.n8n.webhook_url');

            // Log the intended payload regardless of whether a URL is set,
            // so you can inspect what would be sent. Size only for the blob.
            Log::info('Company-email webhook payload prepared.', [
                'run_id' => $run->id,
                'url' => $url ?: '(not configured)',
                'company_email' => $run->company_email,
                'job_title' => $run->job_title,
                'email_title' => $run->email_title,
                'email_message' => $run->email_message,
                'job_description_chars' => strlen((string) $run->job_description),
                'pdf_base64_bytes' => strlen($pdfBase64), // the blob itself is not logged
            ]);

            if (empty($url)) {
                Log::info('Company-email webhook not sent: N8N_WEBHOOK_URL is not configured.', [
                    'run_id' => $run->id,
                ]);

                return; // nothing to POST to
            }

            $response = Http::post($url, $payload);

            if ($response->successful()) {
                Log::info('Company-email webhook delivered.', [
                    'run_id' => $run->id,
                    'status' => $response->status(),
                ]);
            }

            if ($response->failed()) {
                Log::warning('Company-email webhook returned a non-success status.', [
                    'run_id' => $run->id,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Company-email webhook failed.', [
                'run_id' => $run->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Result page for a completed run.
    public function result(TailoringRun $run)
    {
        return view('tailor.result', compact('run'));
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
