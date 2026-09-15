<?php

namespace App\Services;

/**
 * Merges static resume config with the AI-tailored fields (headline,
 * summary, skills) into the flat array the Blade template consumes.
 * Experience / education / certifications always come straight from config.
 */
class ResumeAssembler
{
    /**
     * @param  array{headline?:string,summary?:string,skills?:array}  $tailored
     * @return array<string,mixed>
     */
    public function build(array $tailored = []): array
    {
        $resume = config('resume');

        return [
            'personal' => $resume['personal'],
            'headline' => $tailored['headline'] ?? $resume['default_headline'],
            'summary' => $tailored['summary'] ?? $resume['default_summary'],
            'skills' => $tailored['skills'] ?? $resume['skill_pool'],
            'experience' => $resume['experience'],
            'education' => $resume['education'],
            'certifications' => $resume['certifications'],
        ];
    }
}
