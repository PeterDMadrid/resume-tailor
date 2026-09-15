@extends('layouts.app')

@section('title', 'Tailoring history')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">History</h1>
            <p class="mt-1 text-sm text-slate-500">Past tailoring runs, newest first.</p>
        </div>
        <a href="{{ route('tailor.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
            + New resume
        </a>
    </div>

    @if ($runs->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <p class="text-sm text-slate-500">No tailoring runs yet.</p>
            <a href="{{ route('tailor.create') }}" class="mt-2 inline-block text-sm font-medium text-slate-900 underline">
                Create your first one
            </a>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Job title</th>
                        <th class="px-4 py-3 font-medium">Company</th>
                        <th class="px-4 py-3 font-medium">Headline</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($runs as $run)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $run->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $run->job_title ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $run->company_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ Str::limit($run->generated_headline, 45) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $styles = [
                                        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'degraded' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    ][$run->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20';
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $styles }}">
                                    {{ $run->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('tailor.download', $run) }}"
                                       class="font-medium text-slate-900 underline decoration-slate-300 underline-offset-2 hover:decoration-slate-900">
                                        Download
                                    </a>
                                    <form method="POST" action="{{ route('tailor.destroy', $run) }}"
                                          onsubmit="return confirm('Delete this run and its PDF? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:text-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $runs->links() }}</div>
    @endif
@endsection
