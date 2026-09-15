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
                <a href="{{ route('tailor.create') }}"
                   class="rounded-md px-3 py-1.5 font-medium transition hover:bg-slate-100 {{ request()->routeIs('tailor.create') ? 'bg-slate-100 text-slate-900' : 'text-slate-600' }}">
                    Tailor
                </a>
                <a href="{{ route('tailor.index') }}"
                   class="rounded-md px-3 py-1.5 font-medium transition hover:bg-slate-100 {{ request()->routeIs('tailor.index') ? 'bg-slate-100 text-slate-900' : 'text-slate-600' }}">
                    History
                </a>
            </div>
        </div>
    </nav>

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
