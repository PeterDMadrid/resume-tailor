<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailoringRun extends Model
{
    protected $fillable = [
        'job_description', 'company_name', 'job_title', 'company_email',
        'email_title', 'email_message',
        'generated_headline', 'generated_summary', 'generated_skills',
        'pdf_path', 'model_used', 'status', 'raw_response',
    ];

    protected $casts = [
        'generated_skills' => 'array',
    ];
}
