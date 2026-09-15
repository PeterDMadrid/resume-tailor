# Phase 1 — Project Foundation

Running Laravel app with SQLite, dompdf installed, and Gemini env structured.

## What was done

- Installed `barryvdh/laravel-dompdf` (v3.1.x) — single-column resume PDFs.
- Added `services.gemini` config in `config/services.php`. **Always** read via
  `config('services.gemini.*')`, never `env()` in controllers/services.
- Set `DB_DATABASE` to an absolute SQLite path in `.env`.
  `.env.example` keeps it commented (path is machine-specific).
- Confirmed `.gitignore` excludes `.env`; `database/.gitignore` excludes `*.sqlite*`.

## Gemini config keys

| config key                          | env var                   |
| ----------------------------------- | ------------------------- |
| `services.gemini.key`               | `GEMINI_API_KEY`          |
| `services.gemini.model`             | `GEMINI_MODEL`            |
| `services.gemini.base_url`          | `GEMINI_BASE_URL`         |
| `services.gemini.timeout`           | `GEMINI_TIMEOUT`          |
| `services.gemini.max_output_tokens` | `GEMINI_MAX_OUTPUT_TOKENS`|
| `services.gemini.temperature`       | `GEMINI_TEMPERATURE`      |

## Verification (passed)

Temporary routes in `routes/web.php` (remove after this phase):

- `GET /` → 200
- `GET /_verify/gemini` → `key_present: true`, model/base_url correct
- `GET /_verify/db` → `sqlite_version` returned against the configured DB

Run locally: `php artisan serve` then visit the routes above.

> The `/_verify/*` routes are throwaway. They get removed once the real UI exists.
