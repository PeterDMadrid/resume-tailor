<?php

namespace App\Services;

use App\Models\ConstantSkill;

/**
 * Always-on skills. These are guaranteed to appear on every resume,
 * merged into their group (Option 1) regardless of the job description.
 */
class ConstantSkills
{
    /**
     * Grouped constants: [group => name[]].
     *
     * @return array<string,string[]>
     */
    public function grouped(): array
    {
        $grouped = [];
        foreach (ConstantSkill::orderBy('group')->orderBy('name')->get() as $skill) {
            $grouped[$skill->group][] = $skill->name;
        }

        return $grouped;
    }

    /** Flat list of every constant skill name (for the AI's allowed set). */
    public function names(): array
    {
        return ConstantSkill::pluck('name')->all();
    }

    /**
     * Merge constants into an already-grouped skills array. Constants come
     * first within their group; duplicates (case-insensitive) are removed.
     *
     * @param  array<string,string[]>  $grouped
     * @return array<string,string[]>
     */
    public function mergeInto(array $grouped): array
    {
        foreach ($this->grouped() as $group => $constants) {
            $existing = $grouped[$group] ?? [];
            $merged = array_merge($constants, $existing);

            // De-dupe case-insensitively, keep first occurrence (constants win).
            $seen = [];
            $result = [];
            foreach ($merged as $skill) {
                $key = mb_strtolower($skill);
                if (! isset($seen[$key])) {
                    $seen[$key] = true;
                    $result[] = $skill;
                }
            }

            $grouped[$group] = $result;
        }

        return $grouped;
    }
}
