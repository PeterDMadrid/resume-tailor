<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $personal['name'] }} — Resume</title>
    {{-- Inline styles only: dompdf is most reliable this way. Single-column, DejaVu Sans. --}}
    <style>
        @page { margin: 32px 40px; }

        * { box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10.5px;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
        }

        /* Header */
        .name { font-size: 22px; font-weight: bold; margin: 0 0 2px; }
        .headline { font-size: 12px; color: #444; margin: 0 0 6px; }
        .contact { font-size: 9.5px; color: #333; }
        .contact span { white-space: nowrap; }
        .contact .sep { color: #aaa; padding: 0 4px; }

        /* Section */
        .section { margin-top: 8px; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2a2a2a;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
            margin-bottom: 6px;
        }

        p { margin: 0 0 4px; }

        /* Experience */
        .job { margin-bottom: 4px; page-break-inside: avoid; }
        .job-head { width: 100%; }
        .job-title { font-weight: bold; font-size: 10.5px; }
        .job-company { color: #333; }
        .job-meta { color: #666; font-size: 9.5px; font-style: italic; }
        .job-meta-right { float: right; font-style: normal; }
        ul { margin: 3px 0 0; padding-left: 16px; }
        li { margin-bottom: 2px; }

        /* Skills (grouped) */
        .skill-row { margin-bottom: 1px; }
        .skill-group { font-weight: bold; }

        /* Education / certs */
        .edu-item { margin-bottom: 5px; }
        .edu-degree { font-weight: bold; }
        .edu-meta { color: #666; font-size: 9.5px; }
        .cert-list { margin: 0; padding-left: 16px; }

        .clear { clear: both; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="name">{{ $personal['name'] }}</div>
    <div class="headline">{{ $headline }}</div>
    <div class="contact">
        <span>{{ $personal['location'] }}</span>
        <span class="sep">|</span>
        <span>{{ $personal['email'] }}</span>
        <span class="sep">|</span>
        <span>{{ $personal['phone'] }}</span>
        @foreach ($personal['links'] ?? [] as $label => $url)
            <span class="sep">|</span>
            <span>{{ $label }}: {{ preg_replace('#^https?://#', '', $url) }}</span>
        @endforeach
    </div>

    {{-- Summary --}}
    @if (!empty($summary))
        <div class="section">
            <div class="section-title">Summary</div>
            <p>{{ $summary }}</p>
        </div>
    @endif

    {{-- Skills (grouped) --}}
    @if (!empty($skills))
        <div class="section">
            <div class="section-title">Skills</div>
            @foreach ($skills as $group => $items)
                <div class="skill-row">
                    <span class="skill-group">{{ $group }}:</span>
                    {{ implode(', ', $items) }}
                </div>
            @endforeach
        </div>
    @endif

    {{-- Experience --}}
    @if (!empty($experience))
        <div class="section">
            <div class="section-title">Experience</div>
            @foreach ($experience as $job)
                <div class="job">
                    <div class="job-head">
                        <span class="job-meta job-meta-right">{{ $job['date_range'] }}</span>
                        <span class="job-title">{{ $job['title'] }}</span>,
                        <span class="job-company">{{ $job['company'] }}</span>
                    </div>
                    <div class="job-meta clear">
                        {{ $job['location'] ?? '' }}@if (!empty($job['current'])) &middot; Current @endif
                    </div>
                    @if (!empty($job['bullets']))
                        <ul>
                            @foreach ($job['bullets'] as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Education --}}
    @if (!empty($education))
        <div class="section">
            <div class="section-title">Education</div>
            @foreach ($education as $edu)
                <div class="edu-item">
                    <span class="edu-degree">{{ $edu['degree'] }}</span> — {{ $edu['institution'] }}
                    <span class="edu-meta">({{ $edu['dates'] }})</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Certifications --}}
    @if (!empty($certifications))
        <div class="section">
            <div class="section-title">Certifications</div>
            <ul class="cert-list">
                @foreach ($certifications as $cert)
                    <li>{{ $cert }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</body>
</html>
