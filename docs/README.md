# Resume Tailor — Overview

Paste a job description; the app uses Gemini to tailor your resume headline,
summary, and skills, then renders a PDF. Experience/education/certifications
stay static and truthful. Every run is saved and re-downloadable.

## Flow

`/tailor` (form) → validate → `GeminiService` → `ResumeAssembler` →
`PdfGenerator` (save to storage) → download + `TailoringRun` record.
Past runs at `/tailor/history`.

## Key pieces

| Concern            | Where                                         |
| ------------------ | --------------------------------------------- |
| Resume facts       | `config/resume.php` (edit this)               |
| Gemini + config    | `config/services.php` → `services.gemini`     |
| Input limits       | `config/services.php` → `services.tailor`     |
| AI call + validate | `app/Services/GeminiService.php`              |
| Prompt + schema    | `app/Services/ResumeTailorPrompt.php`         |
| Merge data         | `app/Services/ResumeAssembler.php`            |
| Render + store PDF | `app/Services/PdfGenerator.php`               |
| History            | `app/Models/TailoringRun.php`                 |
| Template           | `resources/views/resume/template.blade.php`   |

Per-phase notes: `docs/phase-1.md` … `docs/phase-8.md`.

## Before generating a real resume

`config/resume.php` still has `[PLACEHOLDER]` values. Replace **all** of them
with your real data. In particular, `skill_pool` is the only set of skills the
AI may use — a skill not listed there can never appear on a generated resume.

## Guarantees

- Skills are filtered to `skill_pool`; the AI cannot invent skills.
- Gemini failures (bad key, 429, 5xx, timeout, malformed) degrade gracefully
  to config defaults and still produce a PDF (`status = degraded`).
- Experience/education/certifications come straight from config, untouched.
