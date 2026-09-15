<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skills' => ['array'],
            'skills.*' => ['array'],
            'skills.*.*' => ['string', 'max:80'],
        ];
    }

    /**
     * Kept skills as [group => name[]], trimmed and pruned of empty groups.
     *
     * @return array<string,string[]>
     */
    public function groupedSkills(): array
    {
        $grouped = [];
        foreach ((array) $this->input('skills', []) as $group => $items) {
            $names = array_values(array_filter(array_map('trim', (array) $items), fn ($n) => $n !== ''));
            if ($names !== []) {
                $grouped[(string) $group] = $names;
            }
        }

        return $grouped;
    }
}
