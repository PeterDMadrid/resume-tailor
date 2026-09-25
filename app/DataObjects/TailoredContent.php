<?php

namespace App\DataObjects;

/**
 * Typed container for the three AI-generated fields.
 * `skills` mirrors the grouped skill_pool shape: [group => string[]].
 */
class TailoredContent
{
    /**
     * @param  array<string,string[]>  $skills
     */
    public function __construct(
        public readonly string $headline,
        public readonly string $summary,
        public readonly array $skills,
        public readonly string $emailTitle = '',   // AI-generated subject/title for the outreach email
        public readonly string $emailMessage = '', // AI-generated short outreach message body
        public readonly bool $degraded = false, // true when built from fallback defaults
        public readonly ?string $raw = null,    // raw Gemini response JSON, for history/debugging
    ) {}

    /** success when AI produced valid output; degraded when it fell back. */
    public function status(): string
    {
        return $this->degraded ? 'degraded' : 'success';
    }

    /** Build from config defaults when AI is unavailable or invalid. */
    public static function fallback(): self
    {
        return new self(
            headline: config('resume.default_headline'),
            summary: config('resume.default_summary'),
            skills: config('resume.skill_pool'),
            emailTitle: config('resume.default_email_title'),
            emailMessage: config('resume.default_email_message'),
            degraded: true,
        );
    }

    /** Shape consumed by ResumeAssembler::build(). */
    public function toArray(): array
    {
        return [
            'headline' => $this->headline,
            'summary' => $this->summary,
            'skills' => $this->skills,
        ];
    }
}
