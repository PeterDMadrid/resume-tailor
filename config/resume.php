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
            'company' => 'Axiom Systems & Margallo Review Center',
            'location' => 'Quezon City',
            'date_range' => 'June 2026 - Present',
            'current' => true,
            'bullets' => [
                'Axiom Systems: Delivered a multi-seller B2B SaaS e-commerce platform for 20+ sellers using Laravel, Vue 3, and MySQL, with storefronts, product management, checkout, payments, order handling, and automated payment synchronization through gateway webhooks.',
                'Margallo Review Center: Replaced fragmented manual processes with a Laravel/Vue 3/MySQL LMS covering enrollment, learning, payment verification, and administration, with role-based access for admins, staff, and students and a centralized payment approval workflow.',
            ],
        ],
        [
            'title' => 'Junior Web Developer',
            'company' => 'Digimax IT Solutions',
            'location' => 'Quezon City',
            'date_range' => 'Nov 2025 - June 2026',
            'current' => false,
            'bullets' => [
                'Delivered custom accounting systems (Laravel, Vue.js) for 8 clients, matching each client\'s accounting workflow.',
                'Translated client-specific accounting requirements into working features, shortening turnaround from request to delivery.',
                'Automated receipt-to-invoice, credit memo, and report generation for 2 airline clients over SSH; eliminated manual document entry, saving ~20 hrs/week.',
                'Cut per-client setup time ~40% by building configurable settings module.',
                'Standardized reusable architecture across 10+ concurrent projects; reduced duplicated code and sped up feature delivery.',
                'Migrated data between mismatched schemas for 5+ deployments with full traceability and zero data loss.',
                'Owned production deployments to Linux servers via SSH; diagnosed and fixed live issues to keep client accounting operations running.',
                'Integrated AI-assisted workflow (Claude, Kiro) into daily development: drafted feature specs, reviewed code for bugs and edge cases, and generated documentation, shortening delivery cycles on client projects.',
                'Documented modules and deployment steps so teammates could maintain and extend client systems without handover delays.',
            ],
        ],
        [
            'title' => 'Intern Software Developer',
            'company' => 'HiPe Japan Inc',
            'location' => 'Eastwood, Quezon City',
            'date_range' => 'Feb 2024 - June 2024',
            'current' => false,
            'bullets' => [
                'Led 4-person team through 4 Agile sprints; shipped Laravel microblogging prototype in 8 weeks and demoed to engineering panel.',
                'Resolved 15+ production UI bugs with senior engineer; cut user-reported issues ~30%.',
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
            'PHP',
            'JavaScript',
            'TypeScript',
            'Python',
            'SQL',
            'VB.NET',
            'HTML5',
            'CSS3',
        ],
        'AI' => [
            'API (RAG/embeddings)',
            'AI API Integration',
            'n8n Automation',
        ],
        'CI/CD' => [
            'GitHub Actions',
        ],
        'Frameworks' => [
            'Laravel',
            'Vue 3',
            'Next.js',
            'Django',
            'ASP.NET WebForms (VB.NET)',
        ],
        'Databases' => [
            'MySQL',
            'MS SQL Server',
            'PostgreSQL',
        ],
        'Servers & Deployments' => [
            'Linux (Ubuntu)',
            'Apache2',
            'Nginx',
            'SSH',
            'Server Deployment',
        ],
        'Cloud & DevOps' => [
            'AWS EC2',
            'Cloudflare R2',
            'PM2',
            'Docker',
        ],
        'Security & Data Handling' => [
            'Data Migration',
            'Backup & Restore',
            'Information Security',
            'Access Control (RBAC)',
            'Authentication & Authorization',
        ],
        'API & Integration' => [
            'REST API Design & Development',
            'Third-Party API Integration',
            'Payment Gateway Integration',
            'Webhook Integration',
            'OAuth2 / API Key Authentication',
            'JSON / XML Data Handling',
        ],
    ],

    // Used when AI is unavailable or its output fails validation.
    'default_headline' => 'Full-Stack Software Engineer (Laravel & Vue.js)',
    'default_summary' => 'Full-stack software engineer specializing in Laravel and Vue.js. '
        . 'Experience across accounting systems, LMS platforms, and enterprise data migration. '
        . 'Skilled in application architecture, database design, and Linux server deployment.',

    // Fallback email fields (used when AI is unavailable). Sent with the webhook.
    'default_email_title' => 'Full-Stack Software Engineer (Laravel & Vue.js)',
    'default_email_message' => 'Hi, I came across your opening and believe my full-stack '
        . 'experience with Laravel and Vue.js is a strong fit. My tailored resume is attached '
        . 'for your review. I would welcome the chance to discuss the role.',

];
