<?php

namespace App\Services;

use App\DataObjects\TailoredContent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Calls Gemini to tailor resume header content, then validates the response.
 * Never throws to the caller: any failure degrades to config defaults.
 */
class GeminiService
{
    public function __construct(
        private readonly ResumeTailorPrompt $prompt,
        private readonly ConstantSkills $constants,
    ) {}

    public function tailor(string $jobDescription, ?string $jobTitle = null, ?string $companyName = null): TailoredContent
    {
        try {
            $response = $this->call(
                $this->prompt->build($jobDescription, $jobTitle, $companyName)
            );

            if ($response === null) {
                return TailoredContent::fallback();
            }

            return $this->validate($response) ?? TailoredContent::fallback();
        } catch (Throwable $e) {
            Log::warning('Gemini tailoring failed', ['error' => $e->getMessage()]);

            return TailoredContent::fallback();
        }
    }

    /** Performs the HTTP call. Returns decoded body array, or null on failure. */
    private function call(array $body): ?array
    {
        $model = config('services.gemini.model');
        $url = rtrim(config('services.gemini.base_url'), '/')."/models/{$model}:generateContent";

        $res = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->timeout(config('services.gemini.timeout'))
            ->retry(2, 500, throw: false) // 2 tries, 500ms backoff; handles transient 429/5xx
            ->post($url, $body);

        if ($res->failed()) {
            Log::warning('Gemini HTTP error', ['status' => $res->status(), 'body' => $res->body()]);

            return null;
        }

        return $res->json();
    }

    /**
     * Validation layer: extract text, strip fences, decode, verify keys/types,
     * filter skills to the allowed pool, regroup per config. Returns null if invalid.
     */
    private function validate(array $response): ?TailoredContent
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (! is_string($text)) {
            return $this->rejectAndLog('no text part', $response);
        }

        $decoded = json_decode($this->stripFences($text), true);

        if (! is_array($decoded)
            || ! isset($decoded['headline'], $decoded['summary'], $decoded['skills'])
            || ! is_string($decoded['headline'])
            || ! is_string($decoded['summary'])
            || ! is_array($decoded['skills'])) {
            return $this->rejectAndLog('missing/invalid keys', $response);
        }

        $skills = $this->filterToPool($decoded['skills']);

        if ($skills === []) {
            return $this->rejectAndLog('no valid skills after filtering', $response);
        }

        return new TailoredContent(
            headline: trim($decoded['headline']),
            summary: trim($decoded['summary']),
            skills: $skills,
            raw: json_encode($response),
        );
    }

    /** Removes ```json ... ``` fences the model may add despite JSON mode. */
    private function stripFences(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        return trim($text);
    }

    /**
     * Keeps only skills that genuinely exist in skill_pool (case-insensitive),
     * then regroups them under their config group, preserving AI ordering.
     *
     * @param  array<int,mixed>  $returned
     * @return array<string,string[]>
     */
    private function filterToPool(array $returned): array
    {
        // Map lowercased skill => [group, canonical name]. Pool + constants.
        $lookup = [];
        foreach ((array) config('resume.skill_pool') as $group => $skills) {
            foreach ($skills as $skill) {
                $lookup[mb_strtolower($skill)] = [$group, $skill];
            }
        }
        foreach ($this->constants->grouped() as $group => $skills) {
            foreach ($skills as $skill) {
                $lookup[mb_strtolower($skill)] = [$group, $skill];
            }
        }

        $grouped = [];
        foreach ($returned as $skill) {
            if (! is_string($skill)) {
                continue;
            }
            $key = mb_strtolower(trim($skill));
            if (isset($lookup[$key])) {
                [$group, $canonical] = $lookup[$key];
                $grouped[$group][] = $canonical;
            }
        }

        // De-dupe within each group.
        foreach ($grouped as $group => $items) {
            $grouped[$group] = array_values(array_unique($items));
        }

        return $grouped;
    }

    private function rejectAndLog(string $reason, array $response): null
    {
        Log::warning('Gemini validation failed', ['reason' => $reason, 'raw' => $response]);

        return null;
    }
}
