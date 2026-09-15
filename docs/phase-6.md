# Phase 6 — Assembly + Tailored PDF Output

End-to-end: paste JD → submit → download a PDF with AI-tailored headline,
summary, and skills, and untouched static experience/education/certifications.

## Flow (`TailorController@store`)

1. `TailorRequest` validates input.
2. `GeminiService::tailor()` → `TailoredContent` (real or degraded fallback).
3. `ResumeAssembler::build($tailored->toArray())` merges with `config('resume')`.
4. `PdfGenerator::store()` renders the Blade and saves to the storage disk.
5. `Storage::download()` serves the saved file.

The controller stays thin: all work lives in the injected services.

## Components

- `app/Services/PdfGenerator.php` — `store($viewData)` renders
  `resume.template`, writes to disk, returns the storage-relative path.
  Filename is traceable: `{slug-name}-{YmdHis}-{rand}.pdf`.

## Storage

- Disk: `config('services.tailor.pdf_disk')` = `local`.
- Folder: `config('services.tailor.pdf_path')` = `resumes`.
- Resolves to `storage/app/private/resumes/` (Laravel `local` root).
- Gitignored via `storage/app/private/.gitignore`.
- Files are kept on disk (not just streamed) so Phase 7 can reference them.

## Verification (passed)

- Full pipeline (faked Gemini success) → valid PDF saved, `%PDF-`, ~1.27 MB.
- **Experience section byte-identical to `config('resume.experience')`** —
  nothing AI-generated leaked into it.
- Traceable filename generated under `resumes/`.
- Gemini failure (429) mid-flow → degraded fallback still produces a valid PDF,
  no exception, no broken download.

## Note

Live output quality (headline/summary tailoring, skill reordering across
different JDs) depends on replacing the `[PLACEHOLDER]` resume data with real
facts. The mechanics are verified.
