@extends('layouts.app')

@section('title', 'Constant skills')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Constant skills</h1>
        <p class="mt-1 text-sm text-slate-500">
            Always-on skills. These appear on every resume — merged into their group —
            no matter the job description.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-[320px_1fr]">
        {{-- Add form --}}
        <form method="POST" action="{{ route('constants.store') }}"
              class="h-fit space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            @csrf
            <h2 class="text-sm font-semibold text-slate-900">Add a constant</h2>

            <div>
                <label for="group" class="mb-1 block text-xs font-medium text-slate-600">Group</label>
                <input list="group-options" id="group" name="group" value="{{ old('group') }}"
                       placeholder="e.g. Languages"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
                <datalist id="group-options">
                    @foreach ($groups as $g)
                        <option value="{{ $g }}"></option>
                    @endforeach
                </datalist>
                <p class="mt-1 text-xs text-slate-400">Pick a suggested group or type a new one.</p>
            </div>

            <div>
                <label for="name" class="mb-1 block text-xs font-medium text-slate-600">Skill</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="e.g. PHP"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
            </div>

            <button type="submit"
                    class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                Add constant
            </button>
        </form>

        {{-- Current constants --}}
        <div>
            @if ($constants->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                    No constant skills yet. Add one on the left to pin it to every resume.
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($constants as $group => $items)
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $group }}</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($items as $skill)
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 py-1 pl-2.5 pr-1 text-sm text-slate-700">
                                        {{ $skill->name }}
                                        <form method="POST" action="{{ route('constants.destroy', $skill) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Remove"
                                                    class="grid h-5 w-5 place-items-center rounded text-slate-400 transition hover:bg-slate-200 hover:text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
