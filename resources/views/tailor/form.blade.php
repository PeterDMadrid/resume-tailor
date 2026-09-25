@extends('layouts.app')

@section('title', 'Tailor a resume')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Tailor your resume</h1>
        <p class="mt-1 text-sm text-slate-500">
            Paste a job description. We tailor your headline, summary, and skills to it.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">Please fix the following:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tailor.store') }}"
          class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="job_title" class="mb-1 block text-sm font-medium text-slate-700">
                    Target job title <span class="font-normal text-slate-400">(optional)</span>
                </label>
                <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}"
                       placeholder="e.g. Backend Developer"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
            </div>
            <div>
                <label for="company_name" class="mb-1 block text-sm font-medium text-slate-700">
                    Company <span class="font-normal text-slate-400">(optional)</span>
                </label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                       placeholder="e.g. Acme Inc."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
            </div>
        </div>

        <div>
            <label for="company_email" class="mb-1 block text-sm font-medium text-slate-700">
                Company email <span class="font-normal text-slate-400">(optional)</span>
            </label>
            <input type="email" id="company_email" name="company_email" value="{{ old('company_email') }}"
                   placeholder="e.g. jobs@acme.com"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
            <p class="mt-1 text-xs text-slate-400">
                If set, the tailored resume is sent to this address via webhook.
            </p>
        </div>

        <div>
            <label for="job_description" class="mb-1 block text-sm font-medium text-slate-700">
                Job description
            </label>
            <textarea id="job_description" name="job_description" rows="12"
                      placeholder="Paste the full job description here..."
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">{{ old('job_description') }}</textarea>
            <p class="mt-1 text-xs text-slate-400">
                Max {{ number_format(config('services.tailor.jd_max_length')) }} characters.
            </p>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-slate-700">
                Tailor my resume
            </button>
        </div>
    </form>

    {{-- Send a dummy payload to n8n to verify the webhook without calling Gemini. --}}
    <form method="POST" action="{{ route('tailor.test-webhook') }}" class="mt-4 space-y-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3">
        @csrf
        <p class="text-xs text-slate-500">
            Test the n8n webhook with a dummy payload (no AI call, no PDF generated).
        </p>
        <div>
            <label for="test_email_title" class="mb-1 block text-xs font-medium text-slate-600">
                Email title <span class="font-normal text-slate-400">(optional — leave blank for default)</span>
            </label>
            <input type="text" id="test_email_title" name="email_title" value="{{ old('email_title') }}"
                   placeholder="Test Email Title - Full-Stack Engineer"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
        </div>
        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">
                Send test webhook
            </button>
        </div>
    </form>
@endsection
