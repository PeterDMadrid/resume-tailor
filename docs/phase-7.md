# Phase 7 — Persist History

Every tailoring run is recorded so it can be reviewed and re-downloaded
without re-calling the API.

## Schema — `tailoring_runs`

| column               | type       | notes                                  |
| -------------------- | ---------- | -------------------------------------- |
| `job_description`    | text       | full JD (not truncated)                |
| `company_name`       | string?    | from the form                          |
| `job_title`          | string?    | from the form                          |
| `generated_headline` | string     | AI or fallback                         |
| `generated_summary`  | text       | AI or fallback                         |
| `generated_skills`   | json       | grouped skills; cast to `array`        |
| `pdf_path`           | string?    | storage-relative path                  |
| `model_used`         | string?    | e.g. `gemini-3.1-flash-lite`           |
| `status`             | string     | `success` \| `degraded` \| `failed`    |
| `raw_response`       | longtext?  | raw Gemini JSON, for debugging         |
| `timestamps`         |            |                                        |

## Components

- `app/Models/TailoringRun.php` — `generated_skills` cast to `array`.
- `TailoredContent` now carries `raw` (raw JSON) and `status()`
  (`success` / `degraded`).
- `GeminiService` attaches the raw response on the success path.
- `TailorController@store` persists the run **after** the PDF is stored,
  **before** serving the download.

## Design notes

- No orphaned rows: a run is recorded only after a PDF is produced.
  A Gemini failure degrades (still produces a PDF) and is stored with
  `status = degraded`, never lost.
- `failed` status is reserved for future non-graceful failures.
- Full JD is stored (useful, negligible cost at prototype scale).

## Verification (passed)

- Run creates a row with all fields populated.
- `generated_skills` round-trips as an array.
- `raw_response` present; `pdf_path` points to an existing file.
- Two submissions → two distinct rows and two distinct PDFs (no overwrite).
