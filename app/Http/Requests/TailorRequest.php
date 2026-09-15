<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TailorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_description' => [
                'required', 'string',
                'min:'.config('services.tailor.jd_min_length'),
                'max:'.config('services.tailor.jd_max_length'),
            ],
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        $max = config('services.tailor.jd_max_length');

        return [
            'job_description.required' => 'Paste a job description to continue.',
            'job_description.min' => 'That job description looks too short to tailor against.',
            'job_description.max' => "Job description is too long (max {$max} characters).",
        ];
    }
}
