# Phase 9 — Tailwind UI

Styled the app with Tailwind v4 (already wired via `@tailwindcss/vite`) and
added a result page. No backend logic changed except the post-submit redirect.

## Views

- `resources/views/layouts/app.blade.php` — shared layout: nav (Tailor /
  History), `@vite` assets, flash areas (`error` / `status`).
- `tailor/form.blade.php` — restyled JD form.
- `tailor/index.blade.php` — restyled history table with status badges +
  pagination.
- `tailor/result.blade.php` — new: shows tailored headline/summary/skills,
  a Download button, and a degraded-mode notice.

## Flow change

`store()` now redirects to `tailor.result` instead of forcing an immediate
download. The PDF is downloaded from the result page (or from History).
This matches the storage-first model and gives on-screen confirmation.

- `/` now redirects to `/tailor`.
- New route: `GET /tailor/result/{run}` → `tailor.result`.

## Assets

- Tailwind v4 via `@tailwindcss/vite`; entry `resources/css/app.css`
  (`@import 'tailwindcss'`), font Instrument Sans.
- Build: `npm run build` → `public/build/` (app CSS ~60 kB).
- Dev: run `npm run dev` alongside `php artisan serve` for HMR.

## Verification (passed)

- `npm run build` compiles (fontaine warning is harmless/optional).
- `/` → 302 → `/tailor`.
- Form + History render 200 with compiled CSS + Tailwind classes.
- Full flow: submit form → 302 → `/tailor/result/{id}` (run persisted) →
  result page 200 with headline/summary/skills + Download button.
