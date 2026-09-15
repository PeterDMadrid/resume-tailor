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
     * @param  bool  $mergeConstants  false when the user has explicitly edited skills
     * @return array<string,mixed>
     */
    public function build(array $tailored = [], bool $mergeConstants = true): array
    {
        $resume = config('resume');
        $skills = $tailored['skills'] ?? $resume['skill_pool'];

        // Constants merge in normally, but not when the user trimmed skills by hand.
        if ($mergeConstants) {
            $skills = $this->constants->mergeInto($skills);
        }

        return [
            'personal' => $resume['personal'],
            'headline' => $tailored['headline'] ?? $resume['default_headline'],
            'summary' => $tailored['summary'] ?? $resume['default_summary'],
            'skills' => $skills,
            'experience' => $resume['experience'],
            'education' => $resume['education'],
            'certifications' => $resume['certifications'],
        ];
    }
}
