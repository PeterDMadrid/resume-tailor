<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Renders the resume Blade to a PDF and stores it on the configured disk.
 * Returns the stored path so callers can serve or persist it.
 */
class PdfGenerator
{
    /**
     * @param  array<string,mixed>  $viewData  output of ResumeAssembler::build()
     * @return string  storage-relative path to the saved PDF
     */
    public function store(array $viewData): string
    {
        $pdf = Pdf::loadView('resume.template', $viewData);
        $path = $this->buildPath($viewData['personal']['name'] ?? 'resume');

        Storage::disk($this->disk())->put($path, $pdf->output());

        return $path;
    }

    public function disk(): string
    {
        return config('services.tailor.pdf_disk');
    }

    /**
     * Human-facing download name, e.g. "Peter_Madrid_Resume.pdf".
     * Derived from the resume name so it stays correct if the name changes.
     */
    public function downloadName(): string
    {
        $name = (string) config('resume.personal.name', 'Resume');
        // Collapse to alnum words, join with underscores.
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $clean = array_filter(array_map(fn ($p) => preg_replace('/[^A-Za-z0-9]/', '', $p), $parts));

        $base = $clean ? implode('_', $clean) : 'Resume';

        return "{$base}_Resume.pdf";
    }

    /** Traceable filename: {slug-name}-{timestamp}-{rand}.pdf under the configured folder. */
    private function buildPath(string $name): string
    {
        $slug = Str::slug($name) ?: 'resume';
        $file = sprintf('%s-%s-%s.pdf', $slug, now()->format('Ymd-His'), Str::random(6));

        return trim(config('services.tailor.pdf_path'), '/')."/{$file}";
    }
}
