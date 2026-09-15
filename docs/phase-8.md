# Phase 8 — History List View + Re-download

A page listing past runs (newest first) with a re-download link per run.

## Components

- `TailorController@index` — `TailoringRun::latest()->paginate(15)`.
- `TailorController@download(TailoringRun $run)` — route-model binding;
  verifies the file exists on disk before serving. Missing file →
  redirect back with an `error` flash (no 500). Serves the **stored** PDF
  (never regenerated).
- `resources/views/tailor/index.blade.php` — table with date, job title,
  company, headline, status badge, download link; empty state; pagination;
  flash-error area.
- Routes:
  - `GET /tailor/history` → `tailor.index`
  - `GET /tailor/history/{run}/download` → `tailor.download`

## Cleanup done in this phase

- Removed all throwaway `/_verify/*` routes (gemini, db, resume, pdf).
- Deleted `ResumeController` (only served the Phase 3 preview route).

## Verification (passed)

- Empty history shows the "No tailoring runs" message.
- Runs listed newest first; both test rows appear.
- Download of an existing file → `StreamedResponse` (the saved PDF).
- Download after deleting the file → clean redirect + error flash, not a 500.
- `php artisan route:list` shows only the four real `tailor.*` routes.
