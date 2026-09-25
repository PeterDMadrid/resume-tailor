<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Tracks actual Gemini token usage, read from each response's usageMetadata
 * (promptTokenCount / candidatesTokenCount / totalTokenCount).
 *
 * Keeps a per-day total (resets each calendar day) and a running lifetime
 * total. There is no live quota endpoint from Google, so these are the real
 * tokens consumed by THIS app's calls, accumulated locally.
 */
class GeminiUsage
{
    private const DAY_PREFIX = 'gemini_tokens:day:';

    private const LIFETIME_KEY = 'gemini_tokens:lifetime';

    /**
     * Record the tokens consumed by one call.
     *
     * @param  int  $prompt  promptTokenCount
     * @param  int  $output  candidatesTokenCount (+ thoughts, if any)
     * @param  int  $total   totalTokenCount
     */
    public function record(int $prompt, int $output, int $total): void
    {
        $dayKey = $this->dayKey();

        // Seed today's bucket with a 2-day TTL so old days expire on their own.
        if (! Cache::has($dayKey)) {
            Cache::put($dayKey, ['prompt' => 0, 'output' => 0, 'total' => 0, 'calls' => 0], now()->addDays(2));
        }

        $day = Cache::get($dayKey);
        $day['prompt'] += $prompt;
        $day['output'] += $output;
        $day['total'] += $total;
        $day['calls'] += 1;
        Cache::put($dayKey, $day, now()->addDays(2));

        // Lifetime total (forever).
        $lifetime = Cache::get(self::LIFETIME_KEY, ['prompt' => 0, 'output' => 0, 'total' => 0, 'calls' => 0]);
        $lifetime['prompt'] += $prompt;
        $lifetime['output'] += $output;
        $lifetime['total'] += $total;
        $lifetime['calls'] += 1;
        Cache::forever(self::LIFETIME_KEY, $lifetime);
    }

    /** Today's token totals. */
    public function today(): array
    {
        return Cache::get($this->dayKey(), ['prompt' => 0, 'output' => 0, 'total' => 0, 'calls' => 0]);
    }

    /** Running lifetime token totals. */
    public function lifetime(): array
    {
        return Cache::get(self::LIFETIME_KEY, ['prompt' => 0, 'output' => 0, 'total' => 0, 'calls' => 0]);
    }

    /**
     * A view/endpoint-friendly snapshot.
     *
     * @return array{date:string,today:array,lifetime:array}
     */
    public function snapshot(): array
    {
        return [
            'date' => now()->toDateString(),
            'today' => $this->today(),
            'lifetime' => $this->lifetime(),
        ];
    }

    private function dayKey(): string
    {
        return self::DAY_PREFIX.now()->toDateString();
    }
}
