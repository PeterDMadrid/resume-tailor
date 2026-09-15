<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailoringRun extends Model
{
    protected $fillable = [
        'job_description', 'company_name', 'job_title',
        'generated_headline', 'generated_summary', 'generated_skills',
        'pdf_path', 'model_used', 'status', 'raw_response',
    ];

    protected $casts = [
        'generated_skills' => 'array',
    ];
}
