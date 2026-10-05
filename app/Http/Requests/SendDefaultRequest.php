<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for sending the DEFAULT (untailored) resume + default cover letter.
 * No job description or AI involved — only a destination company email is
 * required. Optional job title / company name are accepted for record-keeping.
 */
class SendDefaultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_email' => ['required', 'email'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_email.required' => 'Enter a company email to send the default resume.',
            'company_email.email' => 'Enter a valid company email address.',
        ];
    }
}
