<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Resume Tailor')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 text-slate-800 antialiased">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3">
            <a href="{{ route('tailor.create') }}" class="flex items-center gap-2 font-semibold text-slate-900">
                <span class="grid h-7 w-7 place-items-center rounded-md bg-slate-900 text-sm text-white">RT</span>
                Resume Tailor
            </a>
            <div class="flex items-center gap-1 text-sm">
                {{-- Gemini token usage. Real tokens from the API's usageMetadata. --}}
                <div data-gemini-usage data-usage-url="{{ route('gemini.usage') }}"
                     title="Gemini tokens used today / lifetime (from the API usageMetadata)."
                     class="mr-1 hidden items-center gap-1.5 rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs text-slate-600 sm:inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-indigo-500">
                        <path fill-rule="evenodd" d="M11.3 1.046a1 1 0 0 1 .7 1.19L10.72 8H15a1 1 0 0 1 .82 1.573l-6 8.5A1 1 0 0 1 8 17.5l1.28-5.5H5a1 1 0 0 1-.82-1.573l6-8.5a1 1 0 0 1 1.12-.381Z" clip-rule="evenodd" />
                    </svg>
                    <span>
                        <span data-usage-today>{{ number_format($geminiUsage['today']['total']) }}</span> tokens today
                        <span class="text-slate-400">(<span data-usage-lifetime>{{ number_format($geminiUsage['lifetime']['total']) }}</span> total)</span>
                    </span>
                    <button type="button" data-usage-refresh title="Refresh usage"
                            class="grid h-4 w-4 place-items-center rounded text-slate-400 transition hover:bg-slate-200 hover:text-slate-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5" data-usage-refresh-icon>
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h1.633a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v3.181a.75.75 0 0 0 1.5 0v-1.284l.29.288a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V3.989a.75.75 0 0 0-1.5 0v1.29l-.29-.294A7 7 0 0 0 3.26 8.223a.75.75 0 1 0 1.449.39 5.5 5.5 0 0 1 9.201-2.466l.312.311H12.59a.75.75 0 0 0 0 1.5h3.181a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <a href="{{ route('tailor.create') }}"
                   class="rounded-md px-3 py-1.5 font-medium transition hover:bg-slate-100 {{ request()->routeIs('tailor.create') ? 'bg-slate-100 text-slate-900' : 'text-slate-600' }}">
                    Tailor
                </a>
                <a href="{{ route('tailor.index') }}"
                   class="rounded-md px-3 py-1.5 font-medium transition hover:bg-slate-100 {{ request()->routeIs('tailor.index') ? 'bg-slate-100 text-slate-900' : 'text-slate-600' }}">
                    History
                </a>
                <a href="{{ route('constants.index') }}"
                   class="rounded-md px-3 py-1.5 font-medium transition hover:bg-slate-100 {{ request()->routeIs('constants.index') ? 'bg-slate-100 text-slate-900' : 'text-slate-600' }}">
                    Constants
                </a>
                <button type="button" data-preview-open
                        class="ml-1 inline-flex items-center gap-1.5 rounded-md bg-gradient-to-r from-indigo-500 to-violet-500 px-3 py-1.5 font-medium text-white shadow-sm ring-1 ring-inset ring-white/20 transition hover:from-indigo-400 hover:to-violet-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.65 1.65 0 0 1 0-1.18C1.885 6.6 4.51 4.5 10 4.5s8.115 2.1 9.336 4.91a1.65 1.65 0 0 1 0 1.18C18.115 13.4 15.49 15.5 10 15.5S1.885 13.4.664 10.59ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                    </svg>
                    Preview
                </button>
            </div>
        </div>
    </nav>

    {{-- Preview modal: shows the resume rendered from config only (no AI). --}}
    <div data-preview-modal class="fixed inset-0 z-50 hidden">
        <div data-preview-close class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="flex h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Template preview</h2>
                        <p class="text-xs text-slate-500">Rendered from your resume config — no AI tailoring.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('tailor.preview') }}" target="_blank"
                           class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                            Open in new tab
                        </a>
                        <button type="button" data-preview-close
                                class="grid h-8 w-8 place-items-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 bg-slate-100">
                    {{-- iframe src is set on open (lazy) so the PDF only renders when needed. --}}
                    <iframe data-preview-frame title="Resume template preview" class="h-full w-full"></iframe>
                </div>
            </div>
        </div>
    </div>

    <main class="mx-auto max-w-4xl px-4 py-8">
        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
