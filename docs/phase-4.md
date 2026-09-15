# Phase 4 — JD Input Form

Paste a job description, submit, controller validates and receives it. No AI/PDF yet.

## Components

- `app/Http/Controllers/TailorController.php` — thin.
  `create()` shows the form; `store(TailorRequest)` returns the validated
  payload (temporary — Phase 6 wires Gemini + PDF).
- `app/Http/Requests/TailorRequest.php` — validation:
  - `job_description`: required, string, min `50`, max `JD_MAX_LENGTH` (20000)
  - `job_title`, `company_name`: optional strings (max 255)
- `resources/views/tailor/form.blade.php` — textarea + optional title/company,
  `@csrf`, validation error box, `old()` repopulation.
- Routes: `GET /tailor` (`tailor.create`), `POST /tailor` (`tailor.store`).
- `config('services.tailor.*')` — jd_min/max_length + pdf disk/path
  (read from env, never env() in app code).

## Decisions applied

- Extra optional inputs: `job_title`, `company_name` (improve tailoring; reused in Phase 7).
- JD cap kept at 20000 chars, enforced in validation (no silent truncation).

## Verification (passed)

- `GET /tailor` renders the form (200).
- Empty submit → invalid (required).
- < 50 chars → invalid (min).
- ~3000-char JD → valid; controller echoes intact data.
- 25000 chars → invalid with a clear max message.
- CSRF active on the route (POST without token → 419).
