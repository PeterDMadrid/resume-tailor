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

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-400">Skills</h2>
            <div class="mt-3 space-y-3">
                @foreach ($run->generated_skills as $group => $items)
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $group }}</p>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach ($items as $skill)
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-6 flex items-center gap-4 text-sm">
        <a href="{{ route('tailor.create') }}" class="font-medium text-slate-900 underline">Tailor another</a>
        <a href="{{ route('tailor.index') }}" class="text-slate-500 hover:text-slate-800">View history</a>
    </div>
@endsection
