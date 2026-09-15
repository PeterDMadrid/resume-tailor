<?php

/*
|--------------------------------------------------------------------------
| Resume Data (single source of truth)
|--------------------------------------------------------------------------
| Static, single-user resume facts. Read via config('resume.*').
| REPLACE every [PLACEHOLDER] value below with your real data.
|
| skill_pool is load-bearing: it is the ONLY set of skills the AI may use.
| If a skill is not listed here, the AI cannot put it on your resume.
| Keep it complete and honest.
*/

return [

    'personal' => [
        'name' => '[PLACEHOLDER] Your Name',
        'email' => '[PLACEHOLDER] you@example.com',
        'phone' => '[PLACEHOLDER] +63 900 000 0000',
        'location' => '[PLACEHOLDER] City, Country',
        'links' => [
            // label => url
            'LinkedIn' => 'https://linkedin.com/in/your-handle',
            'GitHub' => 'https://github.com/your-handle',
            'Portfolio' => 'https://your-site.example',
        ],
    ],

    // Most recent first.
    'education' => [
        [
            'institution' => '[PLACEHOLDER] University Name',
            'degree' => '[PLACEHOLDER] BS in Something',
            'dates' => '[PLACEHOLDER] 2018 - 2022',
        ],
    ],

    // Flat list of certifications (strings).
    'certifications' => [
        '[PLACEHOLDER] Certification Name — Issuer (Year)',
    ],

    // Most recent first. Each role's bullets stay UNTOUCHED by AI.
    'experience' => [
        [
            'title' => '[PLACEHOLDER] Job Title',
            'company' => '[PLACEHOLDER] Company Name',
            'location' => '[PLACEHOLDER] City, Country',
            'date_range' => '[PLACEHOLDER] Jan 2023 - Present',
            'current' => true,
            'bullets' => [
                '[PLACEHOLDER] Accomplishment with a measurable result.',
                '[PLACEHOLDER] Another responsibility or impact.',
            ],
        ],
        [
            'title' => '[PLACEHOLDER] Previous Job Title',
            'company' => '[PLACEHOLDER] Previous Company',
            'location' => '[PLACEHOLDER] City, Country',
            'date_range' => '[PLACEHOLDER] 2021 - 2023',
            'current' => false,
            'bullets' => [
                '[PLACEHOLDER] Accomplishment with a measurable result.',
            ],
        ],
    ],

    /*
    | Grouped skill pool. Group name => list of skills.
    | The AI selects/reorders FROM these; it may never invent new ones.
    | Add/remove groups freely — the template renders whatever is here.
    */
    'skill_pool' => [
        'Languages' => [
            '[PLACEHOLDER] PHP', 'JavaScript', 'SQL',
        ],
        'Frameworks' => [
            '[PLACEHOLDER] Laravel', 'Vue.js',
        ],
        'Tools' => [
            '[PLACEHOLDER] Git', 'Docker', 'Linux',
        ],
    ],

    // Used when AI is unavailable or its output fails validation.
    'default_headline' => '[PLACEHOLDER] Full-Stack Developer',
    'default_summary' => '[PLACEHOLDER] Short 2-3 sentence professional summary '
        .'used as a safe fallback when AI tailoring is not available.',

];
