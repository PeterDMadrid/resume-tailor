<?php

namespace App\Services;

/**
 * Merges static resume config with the AI-tailored fields (headline,
 * summary, skills) into the flat array the Blade template consumes.
 * Experience / education / certifications always come straight from config.
 * Constant (always-on) skills are merged into every result.
 */
class ResumeAssembler
{
    public function __construct(private readonly ConstantSkills $constants) {}

    /**
     * @param  array{headline?:string,summary?:string,skills?:array}  $tailored
     * @return array<string,mixed>
     */
    public function build(array $tailored = []): array
    {
        $resume = config('resume');
        $skills = $tailored['skills'] ?? $resume['skill_pool'];

        return [
            'personal' => $resume['personal'],
            'headline' => $tailored['headline'] ?? $resume['default_headline'],
            'summary' => $tailored['summary'] ?? $resume['default_summary'],
            // Constants always appear, merged into their group (first).
            'skills' => $this->constants->mergeInto($skills),
            'experience' => $resume['experience'],
            'education' => $resume['education'],
            'certifications' => $resume['certifications'],
        ];
    }
}
