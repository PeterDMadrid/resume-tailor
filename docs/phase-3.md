# Phase 3 — Blade Resume Template + PDF Rendering

Renders a correctly laid-out resume PDF from static config. No AI yet.

## Components

- `resources/views/resume/template.blade.php` — single-column layout,
  `DejaVu Sans` font, **inline `<style>` block** (dompdf's most reliable mode).
  Consumes: `$personal, $headline, $summary, $skills, $experience,
  $education, $certifications`.
- `app/Services/ResumeAssembler.php` — merges static `config('resume')`
  with tailored fields (headline/summary/skills). Falls back to
  `default_headline` / `default_summary` / `skill_pool` when a field is missing.
  Reused by Phase 6.
- `app/Http/Controllers/ResumeController.php` — thin. `preview()` builds data
  via the assembler and streams the PDF. Placeholder tailored values for now.
- Route: `GET /_verify/pdf` (throwaway).

## dompdf notes / constraints

- No flexbox or grid. Layout is single-column block flow.
- `page-break-inside: avoid` on each job keeps roles from splitting across pages.
- First render is ~1.2 MB because the DejaVu font family is embedded — expected.
- Skills render grouped: `Group: a, b, c` per row.

## Verification (passed)

- `GET /_verify/pdf` returns a valid PDF (`%PDF-` header, ~1.27 MB).
- Rendered HTML contains headline, summary, and all sections
  (Experience, Education, Certifications, skill groups).

## Still placeholder

Content is `[PLACEHOLDER]` from `config/resume.php`. Replace with real data,
then re-check page breaks at realistic content volume (long bullets, 20+ skills).
