<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConstantSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'max:60'],
            'name' => [
                'required', 'string', 'max:80',
                // No duplicate name within the same group.
                Rule::unique('constant_skills')->where('group', $this->input('group')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'That skill already exists in this group.',
        ];
    }
}
