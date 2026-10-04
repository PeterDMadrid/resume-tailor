<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $personal['name'] }} — Resume</title>
    {{-- Inline styles only: dompdf is most reliable this way. Single-column, Inter. --}}
    @php
        // Absolute file:// URIs so dompdf resolves the embedded fonts regardless of CWD.
        $fontUri = static fn (string $file) => 'file://'.str_replace('\\', '/', resource_path('fonts/'.$file));
    @endphp
    <style>
        @font-face {
            font-family: "Inter";
            font-weight: normal;
            font-style: normal;
            src: url("{{ $fontUri('Inter-Regular.ttf') }}") format("truetype");
        }
        @font-face {
            font-family: "Inter";
            font-weight: bold;
            font-style: normal;
            src: url("{{ $fontUri('Inter-Bold.ttf') }}") format("truetype");
        }
        @font-face {
            font-family: "Inter";
            font-weight: normal;
            font-style: italic;
            src: url("{{ $fontUri('Inter-Italic.ttf') }}") format("truetype");
        }
        @font-face {
            font-family: "Inter";
            font-weight: bold;
            font-style: italic;
            src: url("{{ $fontUri('Inter-BoldItalic.ttf') }}") format("truetype");
        }

        @page { margin: 36pt 36pt; }

        * { box-sizing: border-box; }

        body {
            font-family: "Inter", sans-serif;
            font-size: 10.5px;
            line-height: 1.3;
            color: #1a1a1a;
            margin: 0;
        }

        /* Header */
        .name { font-size: 22px; font-weight: bold; margin: 0 0 2px; }
        .headline { font-size: 12px; color: #444; margin: 0 0 6px; }
        .contact { font-size: 9.5px; color: #333; }
        .contact-row { margin-bottom: 1pt; }
        .contact .sep { color: #aaa; padding: 0 5px; }

        /* Section */
        .section { margin-top: 3pt; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
            border-bottom: 0.75pt solid #000;
            padding-bottom: 3pt;
            margin-bottom: 5pt;
        }

        p { margin: 0 0 4px; }

        /* Experience */
        .job { margin-top: 4pt; page-break-inside: avoid; }
        /* First job sits right under the section rule (section-title is the
           actual first child, so :first-child never matched). */
        .section-title + .job { margin-top: 0; }
        .job-head { width: 100%; }
        .job-title { font-weight: bold; font-size: 10.5px; }
        .job-company { color: #333; }
        .job-meta { color: #666; font-size: 9.5px; font-style: italic; }
        .job-meta-right { float: right; font-style: normal; }
        ul { margin: 3pt 0 0; padding-left: 16px; }
        li { line-height: 1.3; margin-bottom: 2.5pt; }

        /* Skills (grouped, aligned two-column table) */
        .skills-table { width: 100%; border-collapse: collapse; line-height: 1.3; }
        .skills-table td { vertical-align: top; padding: 0 0 1pt; }
        .skill-label { width: 130pt; font-weight: bold; padding-right: 10pt; white-space: nowrap; }

        /* Education / certs */
        .edu-item { margin-bottom: 5px; }
        .edu-degree { font-weight: bold; }
        .cert-line { margin: 0; }
        .cert-line .sep { color: #aaa; padding: 0 5px; }

        .clear { clear: both; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="name">{{ $personal['name'] }}</div>
    <div class="headline">{{ $headline }}</div>
    @php
        // Build each contact row as whole items joined by a separator placed
        // only *between* items — avoids a dangling "|" at a wrap point.
        $primary = array_values(array_filter([
            $personal['location'] ?? null,
            $personal['email'] ?? null,
            $personal['phone'] ?? null,
        ]));
        $linkItems = [];
        foreach ($personal['links'] ?? [] as $label => $url) {
            $linkItems[] = preg_replace('#^https?://#', '', $url);
        }
        $sep = '<span class="sep">|</span>';
    @endphp
    <div class="contact">
        @if ($primary)
            <div class="contact-row">{!! implode($sep, array_map('e', $primary)) !!}</div>
        @endif
        @if ($linkItems)
            <div class="contact-row">{!! implode($sep, array_map('e', $linkItems)) !!}</div>
        @endif
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
            <table class="skills-table">
                @foreach ($skills as $group => $items)
                    <tr>
                        <td class="skill-label">{{ $group }}</td>
                        <td>{{ implode(', ', $items) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    {{-- Experience --}}
    @if (!empty($experience))
        <div class="section">
            <div class="section-title">Experience</div>
            @foreach ($experience as $job)
                <div class="job">
                    <div class="job-head">
                        <span class="job-meta job-meta-right">{{ trim($job['date_range'] . (!empty($job['location']) ? ' · ' . $job['location'] : '')) }}</span>
                        <span class="job-title">{{ $job['title'] }}</span>,
                        <span class="job-company">{{ $job['company'] }}</span>
                    </div>
                    <div class="clear"></div>
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
                    <span class="job-meta job-meta-right">{{ $edu['dates'] }}</span>
                    <span class="edu-degree">{{ $edu['degree'] }}</span> — {{ $edu['institution'] }}
                    <div class="clear"></div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Certifications --}}
    @if (!empty($certifications))
        <div class="section">
            <div class="section-title">Certifications</div>
            <p class="cert-line">{!! implode('<span class="sep">·</span>', array_map('e', $certifications)) !!}</p>
        </div>
    @endif

</body>
</html>
