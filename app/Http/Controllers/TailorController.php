<?php

namespace App\Http\Controllers;

use App\Http\Requests\TailorRequest;
use App\Models\TailoringRun;
use App\Services\GeminiService;
use App\Services\PdfGenerator;
use App\Services\ResumeAssembler;
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
        $runs = TailoringRun::latest()->paginate(15);

        return view('tailor.index', compact('runs'));
    }

    // Re-download a stored PDF; verify it still exists on disk first.
    public function download(TailoringRun $run, PdfGenerator $pdf)
    {
        $disk = Storage::disk($pdf->disk());

        if (! $run->pdf_path || ! $disk->exists($run->pdf_path)) {
            return back()->with('error', 'That PDF is no longer available on disk.');
        }

        return $disk->download($run->pdf_path);
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
            'generated_headline' => $tailored->headline,
            'generated_summary' => $tailored->summary,
            'generated_skills' => $tailored->skills,
            'pdf_path' => $path,
            'model_used' => config('services.gemini.model'),
            'status' => $tailored->status(),
            'raw_response' => $tailored->raw,
        ]);

        // Land on a result page (download available there + in history).
        return redirect()->route('tailor.result', $run);
    }

    // Result page for a completed run.
    public function result(TailoringRun $run)
    {
        return view('tailor.result', compact('run'));
    }
}
