@extends('layouts.app')

@section('title', 'Resume ready')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Your resume is ready</h1>
            <p class="mt-1 text-sm text-slate-500">
                Tailored {{ $run->created_at->diffForHumans() }}
                @if ($run->job_title) for <span class="font-medium text-slate-700">{{ $run->job_title }}</span> @endif
                @if ($run->company_name) at <span class="font-medium text-slate-700">{{ $run->company_name }}</span> @endif
            </p>
        </div>
        <a href="{{ route('tailor.download', $run) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-slate-700">
            Download PDF
        </a>
    </div>

    @if ($run->status === 'degraded')
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <p class="font-medium">AI tailoring was unavailable.</p>
            <p class="mt-0.5">This resume used your default headline, summary, and full skill list. The PDF is still valid — try again later for a JD-tailored version.</p>
        </div>
    @endif

    <div class="space-y-5">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400">Headline</h2>
            <p class="mt-1 text-lg font-medium text-slate-900">{{ $run->generated_headline }}</p>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400">Summary</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $run->generated_summary }}</p>
        </section>

        @if ($run->email_title || $run->email_message)
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400">Outreach email</h2>
                    @if ($run->company_email)
                        <span class="text-xs text-slate-400">Sent to {{ $run->company_email }} via webhook</span>
                    @else
                        <span class="text-xs text-slate-400">Preview only — no company email was provided</span>
                    @endif
                </div>

                @if ($run->email_title)
                    <p class="mt-3 text-xs font-medium text-slate-500">Subject</p>
                    <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $run->email_title }}</p>
                @endif

                @if ($run->email_message)
                    <p class="mt-3 text-xs font-medium text-slate-500">Message</p>
                    <p class="mt-0.5 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $run->email_message }}</p>
                @endif
            </section>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400">Skills</h2>
                <span class="text-xs text-slate-400">Remove any skill, then regenerate.</span>
            </div>

            <form method="POST" action="{{ route('tailor.update', $run) }}" data-skills-form class="mt-3">
                @csrf
                @method('PATCH')

                <div class="space-y-3">
                    @foreach ($run->generated_skills as $group => $items)
                        <div data-skill-group>
                            <p class="text-sm font-medium text-slate-700">{{ $group }}</p>
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @foreach ($items as $skill)
                                    <span data-skill-chip
                                          class="inline-flex items-center gap-1 rounded-md bg-slate-100 py-1 pl-2.5 pr-1 text-xs font-medium text-slate-600">
                                        <input type="hidden" name="skills[{{ $group }}][]" value="{{ $skill }}">
                                        {{ $skill }}
                                        <button type="button" data-skill-remove title="Remove"
                                                class="grid h-4 w-4 place-items-center rounded text-slate-400 transition hover:bg-slate-200 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" data-skills-submit
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700 disabled:opacity-50">
                        Regenerate PDF
                    </button>
                    <span data-skills-dirty class="hidden text-xs text-amber-600">Unsaved changes — regenerate to apply.</span>
                </div>
            </form>
        </section>
    </div>

    <div class="mt-6 flex items-center gap-4 text-sm">
        <a href="{{ route('tailor.create') }}" class="font-medium text-slate-900 underline">Tailor another</a>
        <a href="{{ route('tailor.index') }}" class="text-slate-500 hover:text-slate-800">View history</a>
    </div>
@endsection
