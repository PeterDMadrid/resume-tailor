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
        'name' => 'Peter Kirsch Madrid',
        'email' => 'petermadrid0421@gmail.com',
        'phone' => '+639765296586',
        'location' => 'Quezon City',
        'links' => [
            'Portfolio' => 'https://peter-azure.vercel.app',
            'LinkedIn' => 'https://www.linkedin.com/in/peter-madrid-99752223b/',
        ],
    ],

    // Most recent first.
    'education' => [
        [
            'institution' => 'Technological Institute of the Philippines - Quezon City',
            'degree' => 'Bachelor of Science in Information Technology',
            'dates' => 'June 2020 - Aug 2025',
        ],
    ],

    // Flat list of certifications (strings).
    'certifications' => [
        'AWS Certified Cloud Practitioner',
        'IBM Enterprise Design Thinking Practitioner',
    ],

    // Most recent first. Each role's bullets stay UNTOUCHED by AI.
    'experience' => [
        [
            'title' => 'Freelance Software Developer',
            'company' => 'Axiom Systems (Freelance / B2B SaaS)',
            'location' => 'Quezon City',
            'date_range' => 'June 2026 - Present',
            'current' => false,
            'bullets' => [
                'Centralized enrollment, learning, payment verification, and administrative workflows into a single LMS for Margallo Review Center, reducing reliance on manual processing and giving staff a unified platform for managing students and courses.',
                'Developed and maintained a B2B SaaS e-commerce platform supporting 20+ sellers with online storefronts, product management, checkout, payments, orders, and customer workflows.',
            ],
        ],
        [
            'title' => 'Junior Web Developer',
            'company' => 'Digimax IT Solutions',
            'location' => '2/F 384 P. Tuazon Blvd., Project 4, Quezon City, 1109',
            'date_range' => 'Nov 2025 - June 2026',
            'current' => false,
            'bullets' => [
                'Built customized Computerized Accounting Systems (CAS) for multiple clients using Laravel and Vue.js, tailoring workflows to client-specific accounting requirements',
                'Engineered automated receipt-to-document pipeline for two airline clients via secure SSH, converting receipts into invoices, credit memos, and accounting reports',
                'Migrated data across CAS systems with differing schemas for 5+ client deployments, maintaining integrity and traceability',
                'Established reusable architecture patterns across 10+ concurrent projects, reducing code duplication',
                'Led development of configurable system settings module, cutting per-client setup time ~40%',
                'Used AI-assisted tools (Claude, Cursor) to speed up code review and documentation',
                'Managed server-side deployments via FileZilla, MobaXterm/Termius, direct SSH to Linux production servers',
            ],
        ],
        [
            'title' => 'Intern Software Developer',
            'company' => 'HiPe Japan Inc',
            'location' => 'Eastwood Global Plaza, Eastwood, Quezon City',
            'date_range' => 'Feb 2024 - June 2024',
            'current' => false,
            'bullets' => [
                'Built and deployed microblogging app in Laravel (MVC, RESTful architecture)',
                'Led 4-member team through 4 Agile sprints, delivering functional prototype in 8 weeks',
                'Fixed production UI bugs alongside senior engineer, reducing user-reported issues',
                'Presented technical demo to engineering panel',
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
            'PHP', 'JavaScript', 'TypeScript', 'Python',
        ],
        'AI' => [
            'API (RAG/embeddings)', 'Ollama (local LLM scoring)',
        ],
        'CI/CD' => [
            'GitHub Actions',
        ],
        'Frameworks' => [
            'Laravel', 'Vue 3', 'Next.js', 'Django', 'ASP.NET WebForms (VB.NET)',
        ],
        'Databases' => [
            'MySQL', 'MS SQL Server',
        ],
        'Servers & Deployments' => [
            'Linux (Ubuntu)', 'Apache2', 'Nginx', 'SSH', 'Server Deployment',
        ],
        'Cloud & DevOps' => [
            'EC2', 'Cloudflare R2', 'PM2', 'Nginx', 'Docker',
        ],
        'Security & Data Handling' => [
            'Data Migration', 'Backup & Restore', 'Information Security', 'Access Control (RBAC)',
        ],
        'API & Integration' => [
            'REST API Design & Development',
        ],
    ],

    // Used when AI is unavailable or its output fails validation.
    'default_headline' => 'Full-Stack Software Engineer (Laravel & Vue.js)',
    'default_summary' => 'Full-stack software engineer specializing in Laravel and Vue.js. '
        .'Experience across accounting systems, LMS platforms, and enterprise data migration. '
        .'Skilled in application architecture, database design, and Linux server deployment.',

];
