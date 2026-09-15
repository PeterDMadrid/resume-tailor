# Phase 5 — Gemini Integration + JSON Validation

Sends the JD + skill pool to Gemini and reliably returns validated,
grouped tailored content. Failures degrade to config defaults, never crash.

## Components

- `app/DataObjects/TailoredContent.php` — typed object for `headline`,
  `summary`, `skills` (grouped). `degraded` flag marks fallback results.
  `::fallback()` builds from `default_headline` / `default_summary` / `skill_pool`.
- `app/Services/ResumeTailorPrompt.php` — builds the request body:
  prompt text, `generationConfig` (temperature, maxOutputTokens),
  **enforced `responseSchema`** (`responseMimeType: application/json`).
  `allowedSkills()` flattens the grouped pool for the constraint set.
- `app/Services/GeminiService.php` — `tailor()` orchestrates: call → validate
  → `TailoredContent`, or `fallback()` on any failure. Never throws to caller.

## Safety guarantees

1. **Skill-pool enforcement (load-bearing).** `filterToPool()` keeps only
   skills that exist in `skill_pool` (case-insensitive), regroups them under
   their config group, preserving AI ordering. Invented skills are dropped.
2. **Enforced schema + own validation.** API-level `responseSchema` (belt)
   plus manual checks (suspenders): strip ```` ```json ```` fences, decode,
   verify all three keys + types.
3. **Graceful degradation.** Invalid key, 429, 5xx, timeout, or malformed
   output → `TailoredContent::fallback()` (`degraded = true`). Raw response is
   logged on validation failure for diagnosis.
4. **Prompt-injection resistance.** JD is wrapped as reference data with an
   explicit instruction not to follow embedded commands. The skill-pool filter
   is the real mechanical guard.

## Config

- HTTP: `Http::retry(2, 500, throw: false)` handles transient 429/5xx.
- Reads `config('services.gemini.*')`. Endpoint:
  `POST {base_url}/models/{model}:generateContent`, header `x-goog-api-key`.

## Verification (passed)

- Real backend JD → `degraded: false`, tailored headline/summary, regrouped skills.
- JD demanding Rust/Kubernetes/Go/Terraform (not in pool) → **none appear**.
- Invalid key / HTTP 500 / HTTP 429 / malformed text → fallback, no exception.
- Valid response with one fabricated skill → that skill filtered, rest kept.

> Testing note: run each `Http::fake()` scenario in its own process. Chaining
> multiple fakes with `->retry()` in one script produced a false failure; the
> service was correct when each case ran in isolation.

> Placeholder note: skill_pool still has `[PLACEHOLDER] PHP` etc. The AI returns
> `PHP`, which won't match the placeholder string, so it gets filtered. Once real
> skill names replace the placeholders, matches will be exact.
