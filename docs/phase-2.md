# Phase 2 — Base Resume Data Structure

Resume facts live in one authoritative, version-controlled place: `config/resume.php`.
Read via `config('resume.*')`. No DB table (single-user, static data).

## Sections

| key                | shape                                                        |
| ------------------ | ------------------------------------------------------------ |
| `personal`         | name, email, phone, location, links (label => url)           |
| `education`        | array of { institution, degree, dates }                      |
| `certifications`   | flat array of strings                                        |
| `experience`       | array of { title, company, location, date_range, current, bullets[] } |
| `skill_pool`       | grouped: group name => [skills] (**AI constraint set**)      |
| `default_headline` | string fallback                                              |
| `default_summary`  | string fallback                                              |

## Rules

- **skill_pool is load-bearing.** It is the only set of skills the AI may use.
  A skill not listed here can never appear on a generated resume. Keep it complete.
- Experience `bullets` are arrays and stay **untouched** by the AI.
- `current` (bool) marks the active role; `location` is per-role.
- Defaults are used when AI is unavailable or its output fails validation.

## Action required

`config/resume.php` currently holds `[PLACEHOLDER]` values. Replace every one
with your real data before generating a real resume.

## Verification (passed)

- `GET /_verify/resume` (throwaway) dumps the full config.
- Confirmed: all 7 sections present, experience bullets are arrays,
  skill_pool grouped into Languages/Frameworks/Tools, defaults present.
