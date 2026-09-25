<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Job description input limits + PDF storage. Read via config('services.tailor.*').
    'tailor' => [
        'jd_max_length' => (int) env('JD_MAX_LENGTH', 20000),
        'jd_min_length' => 50,
        'pdf_disk' => env('RESUME_PDF_DISK', 'local'),
        'pdf_path' => env('RESUME_PDF_PATH', 'resumes'),
        // Max JD-relevant skills to add beyond the pool (0 = pool-only).
        'max_extra_skills' => (int) env('TAILOR_MAX_EXTRA_SKILLS', 6),
    ],

    // n8n webhook. Fired after PDF generation when a company_email is present.
    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL'),
    ],

    // Gemini API config. Read via config('services.gemini.*'), never env() in app code.
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 60),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 2048),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.3),
    ],

];
