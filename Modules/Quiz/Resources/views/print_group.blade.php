<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $group->title }} - {{ __('common.Print') }}</title>
    @php
        $isTcpdf = ($pdfEngine ?? '') === 'tcpdf';
        $bodyFontFamily = $isTcpdf ? 'dejavusans' : "'QuizPdfArabic', DejaVu Sans, sans-serif";
        $bodyFontPath = str_replace('\\', '/', public_path('fonts/vazir.ttf'));
        if (!file_exists(public_path('fonts/vazir.ttf'))) {
            $bodyFontPath = str_replace('\\', '/', public_path('fonts/DejaVuSans.ttf'));
        }

        $katexCss = '';

        $resolvePdfAssetPath = static function (?string $path): ?string {
            if (blank($path)) {
                return null;
            }

            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', 'data:image'])) {
                return $path;
            }

            $normalizedPath = str_replace('\\', '/', $path);
            $candidates = [];

            if (\Illuminate\Support\Str::startsWith($normalizedPath, 'public/')) {
                $candidates[] = base_path($normalizedPath);
            }

            $candidates[] = public_path(ltrim($normalizedPath, '/'));
            $candidates[] = base_path(ltrim($normalizedPath, '/'));

            foreach ($candidates as $candidate) {
                $realPath = realpath($candidate);
                if ($realPath) {
                    return str_replace('\\', '/', $realPath);
                }
            }

            return $path;
        };

        $hasArabic = static function (?string $value): bool {
            return preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u', strip_tags((string)$value)) === 1;
        };

        $direction = static function (?string $value) use ($hasArabic): string {
            return $hasArabic($value) ? 'rtl' : 'ltr';
        };

        $alignment = static function (?string $value) use ($hasArabic): string {
            return $hasArabic($value) ? 'right' : 'left';
        };

        $printImage = static function (?string $path) use ($resolvePdfAssetPath): ?string {
            return $resolvePdfAssetPath($path);
        };

        if (!$isTcpdf && file_exists(public_path('backend/css/katex.min.css'))) {
            $katexCss = file_get_contents(public_path('backend/css/katex.min.css')) ?: '';
            $katexCss = preg_replace_callback('/url\(([^)]+)\)/', static function ($matches) {
                $relativePath = trim($matches[1], '\'" ');
                $absolutePath = realpath(public_path('backend/css/' . $relativePath));

                if (!$absolutePath) {
                    return $matches[0];
                }

                return "url('" . str_replace('\\', '/', $absolutePath) . "')";
            }, $katexCss);
        }

        $optionSortKey = static function ($option): string {
            return sprintf('%08d-%08d', (int)($option->option_index ?? 0), (int)$option->id);
        };

        $formatSuitableWords = static function (?string $words): array {
            return collect(preg_split('/[\r\n,]+/', (string)$words))
                ->map(fn ($word) => trim((string)$word))
                ->filter()
                ->values()
                ->all();
        };

        $sanitizePrintableHtml = static function (?string $html) use ($resolvePdfAssetPath, $isTcpdf): string {
            if (blank($html)) {
                return '';
            }

            $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', (string)$html);

            $previousState = libxml_use_internal_errors(true);
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML('<?xml encoding="utf-8" ?><div id="printable-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $xpath = new \DOMXPath($dom);

            if ($isTcpdf) {
                $equationNodes = iterator_to_array($xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation ')]") ?: []);
                foreach ($equationNodes as $equationNode) {
                    $latexSource = '';
                    $nextSibling = $equationNode->nextSibling;
                    while ($nextSibling) {
                        if ($nextSibling instanceof \DOMElement) {
                            $classes = ' ' . preg_replace('/\s+/', ' ', trim((string)$nextSibling->getAttribute('class'))) . ' ';
                            if (str_contains($classes, ' note-equation-latex-src ')) {
                                $latexSource = trim(html_entity_decode($nextSibling->textContent ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                break;
                            }
                        }
                        $nextSibling = $nextSibling->nextSibling;
                    }

                    if ($latexSource === '') {
                        $latexSource = trim(html_entity_decode($equationNode->textContent ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    }

                    $fallbackTag = str_contains($equationNode->textContent ?? '', "\n") ? 'div' : 'span';
                    $fallbackNode = $dom->createElement($fallbackTag);
                    $fallbackNode->setAttribute('class', 'math-fallback');
                    $fallbackNode->appendChild($dom->createTextNode($latexSource));
                    $equationNode->parentNode?->replaceChild($fallbackNode, $equationNode);
                }
            }

            foreach ([
                "//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation-latex-src ')]",
                "//*[contains(concat(' ', normalize-space(@class), ' '), ' katex-mathml ')]",
                "//*[contains(concat(' ', normalize-space(@class), ' '), ' katex ')]",
            ] as $query) {
                $nodes = iterator_to_array($xpath->query($query) ?: []);
                foreach ($nodes as $node) {
                    $node->parentNode?->removeChild($node);
                }
            }

            foreach (iterator_to_array($xpath->query('//img[@src]') ?: []) as $imageNode) {
                $resolvedSource = $resolvePdfAssetPath($imageNode->getAttribute('src'));
                if ($resolvedSource) {
                    $imageNode->setAttribute('src', $resolvedSource);
                }
            }

            $root = $dom->getElementById('printable-root');
            $output = '';

            if ($root) {
                foreach ($root->childNodes as $childNode) {
                    $output .= $dom->saveHTML($childNode);
                }
            }

            libxml_clear_errors();
            libxml_use_internal_errors($previousState);

            if ($isTcpdf) {
                $output = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $output) ?? $output;
                $output = preg_replace('/\b[a-z-]+\s*:\s*auto\s*;?/i', '', $output) ?? $output;
                $output = preg_replace('/text-rendering\s*:\s*[^;]+;?/i', '', $output) ?? $output;
                $output = preg_replace('/object-fit\s*:\s*[^;]+;?/i', '', $output) ?? $output;
                $output = preg_replace('/<(span|div)([^>]*)>\s*<\/\1>/i', '', $output) ?? $output;
            }

            return $output;
        };
    @endphp
    <style>
        @if(!$isTcpdf)
            @font-face {
                font-family: 'QuizPdfArabic';
                src: url('{{ $bodyFontPath }}') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
        @endif

        {!! $katexCss !!}

        @page {
            margin: 28px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: {!! $bodyFontFamily !!};
            font-size: 12px;
            line-height: 1.6;
            direction: rtl;
            text-align: right;
        }

        .page-header {
            border-bottom: 2px solid #dbe4f0;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .page-title {
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .meta-table {
            border-collapse: collapse;
            width: 100%;
        }

        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .meta-label {
            color: #475569;
            font-weight: 700;
            width: 140px;
        }

        .question-card {
            border: 1px solid #dbe4f0;
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .question-card:last-child {
            margin-bottom: 0;
        }

        .question-head {
            background: #f8fafc;
            border-bottom: 1px solid #dbe4f0;
            padding: 10px 14px;
        }

        .question-head table {
            border-collapse: collapse;
            width: 100%;
        }

        .question-head td {
            vertical-align: top;
        }

        .question-number {
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
        }

        .question-type {
            color: #475569;
            font-size: 11px;
            text-align: left;
        }

        .question-body {
            padding: 14px;
        }

        .question-content,
        .explanation,
        .text-block {
            margin-bottom: 12px;
            word-break: break-word;
        }

        .rendered-html {
            line-height: 1.8;
        }

        .rendered-html p,
        .rendered-html ul,
        .rendered-html ol,
        .rendered-html table,
        .rendered-html blockquote {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .rendered-html img {
            max-width: 100%;
            @if(!$isTcpdf)
                height: auto;
            @endif
        }

        .question-content:last-child,
        .explanation:last-child,
        .text-block:last-child {
            margin-bottom: 0;
        }

        .section-label {
            color: #0f172a;
            display: block;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .question-image,
        .option-image {
            border: 1px solid #dbe4f0;
            border-radius: 6px;
            display: block;
            margin-top: 8px;
            max-height: 220px;
            max-width: 100%;
            @if(!$isTcpdf)
                object-fit: contain;
            @endif
            padding: 4px;
        }

        .option-list,
        .plain-list {
            margin: 0;
            padding: 0 18px 0 0;
        }

        .option-item,
        .plain-list li {
            margin-bottom: 8px;
        }

        .option-item.correct {
            color: #166534;
            font-weight: 700;
        }

        .badge {
            background: #dcfce7;
            border-radius: 999px;
            color: #166534;
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            margin-right: 6px;
            padding: 2px 8px;
        }

        .pair-table,
        .cloze-table {
            border-collapse: collapse;
            width: 100%;
        }

        .pair-table th,
        .pair-table td,
        .cloze-table th,
        .cloze-table td {
            border: 1px solid #dbe4f0;
            padding: 8px 10px;
            vertical-align: top;
        }

        .pair-table th,
        .cloze-table th {
            background: #f8fafc;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
        }

        .answer-note {
            background: #f8fafc;
            border-right: 4px solid #cbd5e1;
            border-radius: 6px;
            color: #334155;
            padding: 10px 12px;
        }

        .math-fallback {
            direction: ltr;
            text-align: left;
            font-family: dejavusans;
            white-space: pre-wrap;
        }

        .empty-state {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
            padding: 20px;
        }

        .note-equation-latex-src,
        .katex-mathml {
            display: none !important;
        }

        .note-equation,
        .katex-display,
        .katex {
            direction: ltr !important;
            text-align: left !important;
        }

        .katex-display {
            margin: 0.75em 0 !important;
            white-space: normal !important;
        }

        .katex {
            font-size: 1.08em !important;
        }

        .katex .base {
            white-space: nowrap !important;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h1 class="page-title">{{ $group->title }}</h1>
        <table class="meta-table">
            <tr>
                <td class="meta-label">{{ __('quiz.Question Group') }}</td>
                <td>{{ $group->title }}</td>
            </tr>
            <tr>
                <td class="meta-label">{{ __('quiz.Total Questions') }}</td>
                <td>{{ $group->questions_count ?? $group->questions->count() }}</td>
            </tr>
            <tr>
                <td class="meta-label">{{ __('common.Date') }}</td>
                <td>{{ showDate($group->created_at) }}</td>
            </tr>
        </table>
    </div>

	    @forelse($group->questions as $questionIndex => $question)
	        @php
	            $questionHtml = $sanitizePrintableHtml($question->question);
	            $explanationHtml = $sanitizePrintableHtml($question->explanation);
	            $questionImage = $printImage($question->image);
	            $questionDirection = $direction(strip_tags($questionHtml));
	            $questionAlignment = $alignment(strip_tags($questionHtml));
	            $multipleChoiceOptions = $question->questionMuInSerial ?? collect();
	            $sortingOptions = $question->questionSortingOptionsSerial ?? collect();
	            $clozeGroups = ($question->questionMuInSerial ?? collect())->groupBy('group')->sortKeys();
            $matchingPrompts = ($question->questionMuInSerial ?? collect())
                ->where('type', 1)
                ->sortBy($optionSortKey)
                ->values();
            $matchingAnswers = ($question->questionMuInSerial ?? collect())
                ->where('type', 0)
                ->sortBy($optionSortKey)
                ->values()
                ->keyBy('id');
            $matchingPairs = ($question->matchingOptions ?? collect())->groupBy('option_id');
            $suitableWords = $formatSuitableWords($question->suitable_words);
        @endphp
        <div class="question-card">
            <div class="question-head">
                <table>
                    <tr>
                        <td class="question-number">{{ __('quiz.Question') }} {{ $questionIndex + 1 }}</td>
                        <td class="question-type">
                            {{ getQuestionType($question->type) }}
                            @if(!empty($question->marks))
                                | {{ __('quiz.Marks') }}: {{ $question->marks }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="question-body">
	                <div class="question-content rendered-html" dir="{{ $questionDirection }}" style="text-align: {{ $questionAlignment }};">
	                    <span class="section-label">{{ __('quiz.Question') }}</span>
	                    {!! $questionHtml !!}
	                    @if($questionImage)
	                        <img class="question-image" src="{{ $questionImage }}" alt="Question image">
	                    @endif
	                </div>

                @if($question->type === 'M')
                    <div class="text-block">
                        <span class="section-label">{{ __('quiz.Options') }}</span>
                        <ol class="option-list">
                            @foreach($multipleChoiceOptions as $option)
                                @php
                                    $optionImage = $printImage($option->image);
                                    $optionDirection = $direction($option->title);
                                    $optionAlignment = $alignment($option->title);
                                @endphp
                                <li class="option-item {{ (int)$option->status === 1 ? 'correct' : '' }}"
                                    dir="{{ $optionDirection }}"
                                    style="text-align: {{ $optionAlignment }};">
                                    @if((int)$option->status === 1)
                                        <span class="badge">{{ __('quiz.Correct Answer') }}</span>
                                    @endif
                                    {{ $option->title }}
                                    @if($optionImage)
                                        <img class="option-image" src="{{ $optionImage }}" alt="Option image">
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @elseif($question->type === 'O')
                    <div class="text-block">
                        <span class="section-label">{{ __('quiz.Sorting') }}</span>
                        <ol class="plain-list">
                            @foreach($sortingOptions as $option)
                                <li dir="{{ $direction($option->title) }}" style="text-align: {{ $alignment($option->title) }};">
                                    {{ $option->title }}
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @elseif($question->type === 'X' || $question->type === 'P')
                    <div class="text-block">
                        <span class="section-label">
                            {{ $question->type === 'X' ? __('quiz.Matching') : __('quiz.Puzzle') }}
                        </span>
                        <table class="pair-table">
                            <thead>
                                <tr>
                                    <th>{{ __('quiz.Question') }}</th>
                                    <th>{{ __('quiz.answer') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matchingPrompts as $prompt)
                                    @php
                                        $promptAnswers = ($matchingPairs->get($prompt->id) ?? collect())
                                            ->map(fn ($pair) => $matchingAnswers->get($pair->answer_id))
                                            ->filter();
                                        $promptImage = $printImage($prompt->image);
                                    @endphp
                                    <tr>
                                        <td dir="{{ $direction($prompt->title) }}" style="text-align: {{ $alignment($prompt->title) }};">
                                            {{ $prompt->title }}
                                            @if($promptImage)
                                                <img class="option-image" src="{{ $promptImage }}" alt="Prompt image">
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="plain-list">
                                                @forelse($promptAnswers as $answer)
                                                    @php
                                                        $answerImage = $printImage($answer->image);
                                                    @endphp
                                                    <li dir="{{ $direction($answer->title) }}" style="text-align: {{ $alignment($answer->title) }};">
                                                        {{ $answer->title }}
                                                        @if($answerImage)
                                                            <img class="option-image" src="{{ $answerImage }}" alt="Answer image">
                                                        @endif
                                                    </li>
                                                @empty
                                                    <li>-</li>
                                                @endforelse
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($question->type === 'C')
                    <div class="text-block">
                        <span class="section-label">{{ __('quiz.Cloze question') }}</span>
                        <table class="cloze-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('quiz.Options') }}</th>
                                    <th>{{ __('quiz.Correct Answer') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clozeGroups as $groupIndex => $options)
                                    @php
                                        $correctOption = $options->firstWhere('status', 1);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <ul class="plain-list">
                                                @foreach($options as $option)
                                                    <li dir="{{ $direction($option->title) }}" style="text-align: {{ $alignment($option->title) }};">
                                                        {{ $option->title }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td dir="{{ $direction(optional($correctOption)->title) }}" style="text-align: {{ $alignment(optional($correctOption)->title) }};">
                                            {{ optional($correctOption)->title ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($question->type === 'T')
                    <div class="answer-note">
                        <span class="section-label">{{ __('quiz.Correct Answer') }}</span>
                        {{ (string)$question->trueFalse === '1' ? __('quiz.True') : __('quiz.False') }}
                    </div>
                @elseif($question->type === 'F')
                    <div class="text-block">
                        <span class="section-label">{{ __('quiz.Fill In The Blanks') }}</span>
                        @if(!empty($suitableWords))
                            <ul class="plain-list">
                                @foreach($suitableWords as $word)
                                    <li dir="{{ $direction($word) }}" style="text-align: {{ $alignment($word) }};">{{ $word }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="answer-note">{{ __('quiz.Fill In The Blanks') }}</div>
                        @endif
                    </div>
                @elseif($question->type === 'S' || $question->type === 'L')
                    <div class="answer-note">
                        <span class="section-label">{{ __('quiz.answer') }}</span>
                        {{ $question->type === 'S' ? __('quiz.Short Answer') : __('quiz.Long Answer') }}
                    </div>
                @endif

	                @if(!blank(strip_tags($explanationHtml)))
	                    <div class="explanation rendered-html" dir="{{ $direction(strip_tags($explanationHtml)) }}" style="text-align: {{ $alignment(strip_tags($explanationHtml)) }};">
	                        <span class="section-label">{{ __('quiz.Explanation') }}</span>
	                        {!! $explanationHtml !!}
	                    </div>
	                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">{{ __('quiz.Question Bank List') }}: 0</div>
    @endforelse
</body>
</html>
