# Phase 10 — Constant (always-on) Skills

Pin skills that must appear on every resume regardless of the job description.
Managed via a `/constants` nav page (DB-backed, no code editing).

## Why

The AI drops irrelevant skills. For a construction JD it would remove PHP,
Laravel, etc. Constant skills are force-included so they always show.

## Behaviour (Option 1 — merge into groups)

Constants are merged into their group, appearing first within it, deduped
case-insensitively. Example — constants PHP (Languages) + Laravel (Frameworks),
AI picks Docker/Linux for a construction JD:

```
Languages:   PHP
Frameworks:  Laravel
Tools:       Docker, Linux
```

They read as normal members of each group; no separate "pinned" section.

## Components

- Migration `create_constant_skills_table` — `group`, `name`, unique(group,name).
- `app/Models/ConstantSkill.php`.
- `app/Services/ConstantSkills.php` — `grouped()`, `names()`,
  `mergeInto($grouped)` (constants first, deduped).
- `app/Http/Controllers/ConstantsController.php` — thin: `index/store/destroy`.
- `app/Http/Requests/ConstantSkillRequest.php` — group/name required,
  unique per group.
- `resources/views/tailor/constants.blade.php` — add form (group datalist of
  `skill_pool` groups + free text) and grouped list with delete.
- Nav item **Constants** in `layouts/app.blade.php`.
- Routes: `constants.index` (GET), `constants.store` (POST),
  `constants.destroy` (DELETE).

## Injection points (single source of truth)

- `ResumeAssembler::build()` calls `ConstantSkills::mergeInto()` on **every**
  path — AI success, degraded fallback, and template preview. One change,
  full coverage.
- `ResumeTailorPrompt::allowedSkills()` includes constant names, so the AI
  treats them as valid.
- `GeminiService::filterToPool()` allows constants through (not stripped if
  the AI returns them).

## Verification (passed)

- Construction-style tailoring keeps PHP/Laravel, merged into their groups.
- Degraded fallback and preview both include constants.
- Merge order: constant first, no duplicate when the AI also picks it.
- `/constants` CRUD via HTTP: add → shows grouped; delete → back to empty.
- Nav item present; `npm run build` compiles.
