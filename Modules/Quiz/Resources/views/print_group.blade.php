<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $group->title }} - {{ __('common.Print') }}</title>
    @php
        $fontPath = str_replace('\\', '/', public_path('fonts/DejaVuSans.ttf'));

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
    @endphp
    <style>
        @font-face {
            font-family: 'QuizPdfArabic';
            src: url('{{ $fontPath }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            margin: 28px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: 'QuizPdfArabic', DejaVu Sans, sans-serif;
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
            object-fit: contain;
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

        .empty-state {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
            padding: 20px;
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
            $questionImage = $printImage($question->image);
            $questionDirection = $direction($question->question);
            $questionAlignment = $alignment($question->question);
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
                <div class="question-content" dir="{{ $questionDirection }}" style="text-align: {{ $questionAlignment }};">
                    <span class="section-label">{{ __('quiz.Question') }}</span>
                    {!! $question->question !!}
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

                @if(!blank(strip_tags((string)$question->explanation)))
                    <div class="explanation" dir="{{ $direction($question->explanation) }}" style="text-align: {{ $alignment($question->explanation) }};">
                        <span class="section-label">{{ __('quiz.Explanation') }}</span>
                        {!! $question->explanation !!}
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">{{ __('quiz.Question Bank List') }}: 0</div>
    @endforelse
</body>
</html>
