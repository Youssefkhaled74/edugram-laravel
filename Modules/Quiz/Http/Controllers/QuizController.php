<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Com\Tecnick\Pdf\Font\Import as TcpdfFontImport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\AdvanceQuiz\Http\Controllers\AdvanceQuizGroupController;
use Modules\Quiz\Entities\QuestionGroup;
use TCPDF;

class QuizController extends Controller
{
    private function ensureTeacher()
    {
        if ((int)Auth::user()->role_id !== 2) {
            abort(403);
        }
    }

    private function canAccessGroup(QuestionGroup $group): bool
    {
        $user = Auth::user();
        if ((int)$user->role_id !== 2) {
            return true;
        }
        return (int)$group->user_id === (int)$user->id;
    }

    private function canManageOwnGroup(QuestionGroup $group): bool
    {
        return (int)$group->user_id === (int)Auth::id();
    }

    public function teacherIndex()
    {
        $this->ensureTeacher();
        $groups = QuestionGroup::where('active_status', 1)
            ->where('user_id', Auth::id())
            ->withCount('questions')
            ->latest()
            ->get();
        return view('quiz::index', compact('groups'));
    }

    public function teacherCreate()
    {
        return $this->teacherIndex();
    }

    public function teacherStore(Request $request)
    {
        $this->ensureTeacher();
        $request->validate(['title' => 'required|string|max:255']);
        $group = new QuestionGroup();
        $group->title = $request->title;
        $group->user_id = Auth::id();
        $group->save();
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->route('teacher.question-banks.index');
    }

    public function teacherEdit($bank)
    {
        $this->ensureTeacher();
        $group = QuestionGroup::findOrFail($bank);
        if (!$this->canManageOwnGroup($group)) {
            abort(403);
        }
        $groups = QuestionGroup::where('active_status', 1)
            ->where('user_id', Auth::id())
            ->withCount('questions')
            ->latest()
            ->get();
        return view('quiz::index', compact('groups', 'group'));
    }

    public function teacherShow($bank)
    {
        return $this->teacherEdit($bank);
    }

    public function teacherUpdate(Request $request, $bank)
    {
        $this->ensureTeacher();
        $request->validate(['title' => 'required|string|max:255']);
        $group = QuestionGroup::findOrFail($bank);
        if (!$this->canManageOwnGroup($group)) {
            abort(403);
        }
        $group->title = $request->title;
        $group->save();
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->route('teacher.question-banks.index');
    }

    public function teacherPrint($bank)
    {
        $this->ensureTeacher();
        $group = $this->teacherOwnPrintableGroup($bank);

        return $this->streamPrintableGroup($group);
    }

    public function teacherDestroy($bank)
    {
        $this->ensureTeacher();
        $group = QuestionGroup::with('questions.quizAssign')->findOrFail($bank);
        if (!$this->canManageOwnGroup($group)) {
            abort(403);
        }
        $used = $group->questions->filter(function ($q) {
            return $q->quizAssign->count() > 0;
        })->count();
        if ($used > 0) {
            Toastr::error(trans('quiz.You cannot delete this question because it has been used in') . ' quiz', trans('common.Failed'));
            return redirect()->back();
        }
        $questionIds = $group->questions->pluck('id')->toArray();
        if (!empty($questionIds)) {
            \Modules\Quiz\Entities\QuestionBankMuOption::whereIn('question_bank_id', $questionIds)->delete();
            \Modules\Quiz\Entities\MatchingTypeQuestionAssign::whereIn('question_id', $questionIds)->delete();
            \Modules\Quiz\Entities\QuestionBank::whereIn('id', $questionIds)->delete();
        }
        $group->delete();
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->route('teacher.question-banks.index');
    }

    private function teacherOwnPrintableGroup($groupId): QuestionGroup
    {
        $group = QuestionGroup::findOrFail($groupId);
        if (!$this->canManageOwnGroup($group)) {
            abort(403);
        }

        return $group;
    }

    private function getPrintableGroup(int $groupId): QuestionGroup
    {
        return QuestionGroup::withCount('questions')
            ->with([
                'questions' => function ($query) {
                    $query->orderBy('id')
                        ->with([
                            'image_media',
                            'questionMuInSerial.image_media',
                            'questionSortingOptionsSerial.image_media',
                            'matchingOptions',
                        ]);
                },
            ])
            ->findOrFail($groupId);
    }

    private function streamPrintableGroup(QuestionGroup $group)
    {
        $group = $this->getPrintableGroup((int)$group->id);
        $fileName = Str::slug($group->title ?: 'question-group') . '.pdf';
        $this->bootstrapTcpdfFonts();
        $html = view('quiz::print_group', [
            'group' => $group,
            'pdfEngine' => 'tcpdf',
        ])->render();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle($group->title ?: 'Question Group');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(true, 12);
        $pdf->setImageScale(1.25);
        $pdf->setFontSubsetting(true);
        $pdf->setCellHeightRatio(1.35);
        $pdf->setLanguageArray([
            'a_meta_charset' => 'UTF-8',
            'a_meta_dir' => 'rtl',
            'a_meta_language' => 'ar',
            'w_page' => 'page',
        ]);
        $pdf->setRTL(true);
        $pdf->setFont('dejavusans', '', 11, '', true);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();

        return response($pdf->Output($fileName, 'S'), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    private function renderPrintableGroupPdf(TCPDF $pdf, QuestionGroup $group): void
    {
        $pdf->SetTextColor(15, 23, 42);
        $pdf->SetFont('dejavusans', 'B', 22, '', true);
        $pdf->MultiCell(0, 0, $this->pdfText($group->title), 0, 'R', false, 1);
        $pdf->Ln(3);

        $this->renderPdfMetaRow($pdf, trans('quiz.Question Group'), $group->title);
        $this->renderPdfMetaRow($pdf, trans('quiz.Total Questions'), (string)($group->questions_count ?? $group->questions->count()));
        $this->renderPdfMetaRow($pdf, trans('common.Date'), showDate($group->created_at));
        $pdf->Ln(2);
        $pdf->SetDrawColor(219, 228, 240);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);

        foreach ($group->questions as $index => $question) {
            $this->renderPdfQuestion($pdf, $question, $index + 1);
        }
    }

    private function renderPdfMetaRow(TCPDF $pdf, string $label, string $value): void
    {
        $labelWidth = 45;
        $valueWidth = 145;
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('dejavusans', 'B', 11, '', true);
        $pdf->MultiCell($labelWidth, 8, $this->pdfText($label), 0, 'R', false, 0);
        $pdf->SetFont('dejavusans', '', 11, '', true);
        $pdf->MultiCell($valueWidth, 8, $this->pdfText($value), 0, 'R', false, 1);

        $pdf->SetXY($startX, max($startY + 8, $pdf->GetY()));
    }

    private function renderPdfQuestion(TCPDF $pdf, $question, int $number): void
    {
        $title = trans('quiz.Question') . ' ' . $number;
        $typeLabel = getQuestionType($question->type);
        if (!empty($question->marks)) {
            $typeLabel .= ' | ' . trans('quiz.Marks') . ': ' . $question->marks;
        }

        $pdf->SetFillColor(248, 250, 252);
        $pdf->SetDrawColor(219, 228, 240);
        $pdf->SetLineWidth(0.2);
        $pdf->SetFont('dejavusans', 'B', 13, '', true);
        $pdf->Cell(95, 10, $this->pdfText($title), 1, 0, 'R', true);
        $pdf->SetFont('dejavusans', '', 11, '', true);
        $pdf->Cell(95, 10, $this->pdfText($typeLabel), 1, 1, 'L', true);

        $pdf->SetFont('dejavusans', 'B', 11, '', true);
        $pdf->Ln(2);
        $pdf->MultiCell(0, 0, $this->pdfText(trans('quiz.Question')), 0, 'R', false, 1);
        $pdf->SetFont('dejavusans', '', 11, '', true);
        $pdf->MultiCell(0, 0, $this->pdfText($this->extractPdfPlainText($question->question)), 0, $this->pdfAlignment($question->question), false, 1);

        $questionImage = $this->resolvePdfImagePath($question->image);
        if ($questionImage) {
            $this->renderPdfImage($pdf, $questionImage);
        }

        $this->renderPdfQuestionDetails($pdf, $question);

        $explanation = $this->extractPdfPlainText($question->explanation);
        if ($explanation !== '') {
            $pdf->Ln(1);
            $pdf->SetFont('dejavusans', 'B', 10, '', true);
            $pdf->MultiCell(0, 0, $this->pdfText(trans('quiz.Explanation')), 0, 'R', false, 1);
            $pdf->SetFont('dejavusans', '', 10, '', true);
            $pdf->MultiCell(0, 0, $this->pdfText($explanation), 0, $this->pdfAlignment($question->explanation), false, 1);
        }

        $pdf->Ln(5);
    }

    private function renderPdfQuestionDetails(TCPDF $pdf, $question): void
    {
        if ($question->type === 'M') {
            $this->renderPdfListSection($pdf, trans('quiz.Options'), ($question->questionMuInSerial ?? collect())->map(function ($option) {
                $text = $option->title;
                if ((int)$option->status === 1) {
                    $text .= ' [' . trans('quiz.Correct Answer') . ']';
                }
                return $text;
            })->all());
            return;
        }

        if ($question->type === 'O') {
            $this->renderPdfListSection($pdf, trans('quiz.Sorting'), ($question->questionSortingOptionsSerial ?? collect())->pluck('title')->all());
            return;
        }

        if ($question->type === 'X' || $question->type === 'P') {
            $items = [];
            $prompts = ($question->questionMuInSerial ?? collect())->where('type', 1)->values();
            $answers = ($question->questionMuInSerial ?? collect())->where('type', 0)->keyBy('id');
            $pairs = ($question->matchingOptions ?? collect())->groupBy('option_id');
            foreach ($prompts as $prompt) {
                $answerText = ($pairs->get($prompt->id) ?? collect())
                    ->map(fn ($pair) => optional($answers->get($pair->answer_id))->title)
                    ->filter()
                    ->implode(' | ');
                $items[] = trim($prompt->title . ' => ' . ($answerText !== '' ? $answerText : '-'));
            }
            $this->renderPdfListSection($pdf, $question->type === 'X' ? trans('quiz.Matching') : trans('quiz.Puzzle'), $items);
            return;
        }

        if ($question->type === 'C') {
            $items = [];
            foreach (($question->questionMuInSerial ?? collect())->groupBy('group')->sortKeys() as $options) {
                $choices = $options->pluck('title')->filter()->implode(' | ');
                $correct = optional($options->firstWhere('status', 1))->title ?: '-';
                $items[] = trans('quiz.Options') . ': ' . $choices . ' | ' . trans('quiz.Correct Answer') . ': ' . $correct;
            }
            $this->renderPdfListSection($pdf, trans('quiz.Cloze question'), $items);
            return;
        }

        if ($question->type === 'T') {
            $answer = (string)$question->trueFalse === '1' ? trans('quiz.True') : trans('quiz.False');
            $this->renderPdfListSection($pdf, trans('quiz.Correct Answer'), [$answer]);
            return;
        }

        if ($question->type === 'F') {
            $items = collect(preg_split('/[\r\n,]+/', (string)$question->suitable_words))
                ->map(fn ($word) => trim((string)$word))
                ->filter()
                ->values()
                ->all();
            $this->renderPdfListSection($pdf, trans('quiz.Fill In The Blanks'), $items ?: [trans('quiz.Fill In The Blanks')]);
            return;
        }

        if ($question->type === 'S' || $question->type === 'L') {
            $this->renderPdfListSection($pdf, trans('quiz.answer'), [$question->type === 'S' ? trans('quiz.Short Answer') : trans('quiz.Long Answer')]);
        }
    }

    private function renderPdfListSection(TCPDF $pdf, string $label, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $pdf->Ln(2);
        $pdf->SetFont('dejavusans', 'B', 10, '', true);
        $pdf->MultiCell(0, 0, $this->pdfText($label), 0, 'R', false, 1);
        $pdf->SetFont('dejavusans', '', 10, '', true);

        foreach ($items as $item) {
            $pdf->MultiCell(0, 0, $this->pdfText('- ' . $item), 0, $this->pdfAlignment((string)$item), false, 1);
        }
    }

    private function renderPdfImage(TCPDF $pdf, string $path): void
    {
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $pdf->Ln(2);
        $pdf->Image($path, '', '', 55, 0, '', '', '', true, 150, '', false, false, 1, false, false, false);
        $pdf->Ln(35);
        $pdf->SetXY($startX, max($startY + 35, $pdf->GetY()));
    }

    private function extractPdfPlainText(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', (string)$html);
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', (string)$html);
        $previousState = libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?><div id="pdf-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);

        foreach (['//br', '//p', '//div', '//li', '//tr', '//table', '//ul', '//ol'] as $query) {
            foreach (iterator_to_array($xpath->query($query) ?: []) as $node) {
                if ($node->parentNode) {
                    $node->parentNode->insertBefore($dom->createTextNode("\n"), $node->nextSibling);
                }
            }
        }

        foreach (iterator_to_array($xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation ')]") ?: []) as $equationNode) {
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

            $equationNode->parentNode?->replaceChild($dom->createTextNode(' ' . $latexSource . ' '), $equationNode);
        }

        foreach ([
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation-latex-src ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' katex ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' katex-mathml ')]",
            '//img',
        ] as $query) {
            foreach (iterator_to_array($xpath->query($query) ?: []) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        $root = $dom->getElementById('pdf-root');
        $text = $root ? html_entity_decode(strip_tags($dom->saveHTML($root) ?: ''), ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
        $text = preg_replace("/(\r\n|\r)/", "\n", $text) ?? $text;
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        $text = trim($text);

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        return $text;
    }

    private function pdfText(?string $value): string
    {
        return trim((string)$value);
    }

    private function pdfAlignment(?string $value): string
    {
        return preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u', strip_tags((string)$value)) === 1 ? 'R' : 'L';
    }

    private function resolvePdfImagePath(?string $path): ?string
    {
        if (blank($path) || Str::startsWith((string)$path, ['http://', 'https://', 'data:image'])) {
            return null;
        }

        $normalizedPath = str_replace('\\', '/', (string)$path);
        $candidates = [];

        if (Str::startsWith($normalizedPath, 'public/')) {
            $candidates[] = base_path($normalizedPath);
        }

        $candidates[] = public_path(ltrim($normalizedPath, '/'));
        $candidates[] = base_path(ltrim($normalizedPath, '/'));

        foreach ($candidates as $candidate) {
            $realPath = realpath($candidate);
            if ($realPath && is_file($realPath)) {
                return str_replace('\\', '/', $realPath);
            }
        }

        return null;
    }

    private function bootstrapTcpdfFonts(): void
    {
        $fontDirectory = $this->tcpdfFontDirectory();

        if (!is_dir($fontDirectory) && !mkdir($fontDirectory, 0755, true) && !is_dir($fontDirectory)) {
            throw new \RuntimeException('Unable to create the TCPDF font directory.');
        }

        $this->defineTcpdfConstant('K_TCPDF_EXTERNAL_CONFIG', true);
        $this->defineTcpdfConstant('K_PATH_FONTS', str_replace('\\', '/', $fontDirectory) . '/');
        $this->defineTcpdfConstant('PDF_FONT_NAME_MAIN', 'dejavusans');
        $this->defineTcpdfConstant('PDF_FONT_NAME_DATA', 'dejavusans');

        foreach ($this->tcpdfFontImports() as $artifact => $sourcePath) {
            if (!file_exists($fontDirectory . DIRECTORY_SEPARATOR . $artifact) && $sourcePath) {
                new TcpdfFontImport($sourcePath, $fontDirectory, 'TrueTypeUnicode', '', 32, 3, 1, false);
            }
        }
    }

    private function tcpdfFontImports(): array
    {
        return [
            'dejavusans.json' => $this->resolveFirstExistingPath([
                public_path('fonts/DejaVuSans.ttf'),
                resource_path('fonts/DejaVuSans.ttf'),
                base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf'),
            ]),
            'dejavusansb.json' => $this->resolveFirstExistingPath([
                base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf'),
            ]),
            'dejavusansi.json' => $this->resolveFirstExistingPath([
                base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Oblique.ttf'),
            ]),
            'dejavusansbi.json' => $this->resolveFirstExistingPath([
                base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-BoldOblique.ttf'),
            ]),
        ];
    }

    private function resolveFirstExistingPath(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (is_string($path) && $path !== '' && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function tcpdfFontDirectory(): string
    {
        return storage_path('app/tcpdf-fonts');
    }

    private function tcpdfFontArtifactName(string $fontPath): ?string
    {
        $fileName = pathinfo($fontPath, PATHINFO_FILENAME);
        if ($fileName === '') {
            return null;
        }

        $normalized = preg_replace('/[^a-z0-9_]/', '', strtolower($fileName));
        if (!is_string($normalized) || $normalized === '') {
            return null;
        }

        $normalized = str_replace(['bold', 'oblique', 'italic', 'regular'], ['b', 'i', 'i', ''], $normalized);

        return $normalized . '.json';
    }

    private function defineTcpdfConstant(string $name, string $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    public function index()
    {
        try {
            if (isModuleActive('AdvanceQuiz')) {
                $AdvanceQuizGroupController = new AdvanceQuizGroupController();
                return $AdvanceQuizGroupController->index();
            } else {
                $query = QuestionGroup::query();
                if ((int)Auth::user()->role_id === 2) {
                    $query->where('user_id', Auth::id());
                }
                if (isModuleActive('Organization') && Auth::user()->isOrganization()) {
                    $query->whereHas('user', function ($q) {
                        $q->where('organization_id', Auth::id());
                        $q->orWhere('user_id', Auth::id());
                    });
                }
                $groups = $query->withCount('questions')->latest()->get();
                return view('quiz::index', compact('groups'));
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function store(Request $request)
    {
        if (isModuleActive('AdvanceQuiz')) {
            $rules = [
                'title' => ['required', Rule::unique('question_groups', 'title')->when(isModuleActive('LmsSaas'), function ($q) {
                    return $q->where('lms_id', app('institute')->id);
                })],
                'code' => 'required|unique:question_groups',
                'parent_id' => 'nullable'
            ];
        } else {
            $rules = [
                'title' => 'required',
            ];
        }

        $this->validate($request, $rules, validationMessage($rules));

        try {
            if (isModuleActive('AdvanceQuiz')) {
                $AdvanceQuizGroupController = new AdvanceQuizGroupController();
                $result = $AdvanceQuizGroupController->createOrUpdate($request);
            } else {
                $group = new QuestionGroup();
                $group->title = $request->title;
                $group->user_id = Auth::id();
                $result = $group->save();
            }

            if ($result) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function show($id)
    {
        if (isModuleActive('AdvanceQuiz')) {
            return redirect('quiz/question-group');
        }
        try {
            $user = Auth::user();
            $group = QuestionGroup::findOrFail($id);
            if (!$this->canAccessGroup($group)) {
                Toastr::error('لا تملك صلاحية الوصول', trans('common.Failed'));
                return redirect()->route('question-group');
            }
            $query = QuestionGroup::where('active_status', 1);
            if ((int)$user->role_id === 2) {
                $query->where('user_id', Auth::id());
            }
            if (isModuleActive('Organization') && $user->isOrganization()) {
                $query->whereHas('user', function ($q) {
                    $q->where('organization_id', Auth::id());
                    $q->orWhere('user_id', Auth::id());
                });
            }
            $groups = $query->withCount('questions')->latest()->get();
            return view('quiz::index', compact('groups', 'group'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function print($id)
    {
        if (isModuleActive('AdvanceQuiz')) {
            return redirect('quiz/question-group');
        }

        $group = QuestionGroup::findOrFail($id);
        if (!$this->canAccessGroup($group)) {
            abort(403);
        }

        return $this->streamPrintableGroup($group);
    }

    public function update(Request $request, $id)
    {

        $rules = [
            'title' => ['required'],
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            if (isModuleActive('AdvanceQuiz')) {
                $AdvanceQuizGroupController = new AdvanceQuizGroupController();
                $result = $AdvanceQuizGroupController->createOrUpdate($request, $request->id);
            } else {
                $group = QuestionGroup::findOrFail($request->id);
                if (!$this->canAccessGroup($group) || (int)$group->user_id === 1) {
                    Toastr::error('لا تملك صلاحية الوصول', trans('common.Failed'));
                    return redirect()->route('question-group');
                }
                $group->title = $request->title;
                $result = $group->save();
            }
            if ($result) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect('quiz/question-group');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function destroy($id)
    {
        if (demoCheckById($id,[1,2,3])) {
            return redirect()->back();
        }

        try {
            if (isModuleActive('AdvanceQuiz')) {
                $group = QuestionGroup::findOrFail($id);
                if (!$this->canAccessGroup($group) || ((int)Auth::user()->role_id === 2 && (int)$group->user_id === 1)) {
                    Toastr::error('لا تملك صلاحية الوصول', trans('common.Failed'));
                    return redirect()->route('question-group');
                }
                $childs = $group->getAllChildIds($group);
                $group->delete();
                foreach ($childs as $child) {
                    $b = QuestionGroup::where('id', $child)->first();
                    $b->delete();
                }
            } else {
                $group = QuestionGroup::findOrFail($id);
                if (!$this->canAccessGroup($group) || ((int)Auth::user()->role_id === 2 && (int)$group->user_id === 1)) {
                    Toastr::error('لا تملك صلاحية الوصول', trans('common.Failed'));
                    return redirect()->route('question-group');
                }
                $group = $group->delete();
            }

            if ($group) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect('quiz/question-group');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
