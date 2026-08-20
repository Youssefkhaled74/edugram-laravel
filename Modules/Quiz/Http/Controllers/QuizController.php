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
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle($group->title ?: 'Question Group');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(14, 14, 14);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->setImageScale(1.25);
        $pdf->setFontSubsetting(true);
        $pdf->setCellHeightRatio(1.35);
        $pdf->setLanguageArray([
            'a_meta_charset' => 'UTF-8',
            'a_meta_dir' => 'ltr',
            'a_meta_language' => 'ar',
            'w_page' => 'page',
        ]);
        // Keep page coordinates LTR and apply RTL only to individual Arabic cells.
        $pdf->setRTL(false);
        $pdf->setFont('dejavusans', '', 11, '', true);
        $pdf->AddPage();
        $this->renderPrintableGroupPdf($pdf, $group);
        $this->renderPdfPageNumbers($pdf);
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
        $leftMargin = $pdf->getMargins()['left'];
        $rightMargin = $pdf->getMargins()['right'];
        $usableWidth = $pdf->getPageWidth() - $leftMargin - $rightMargin;

        $pdf->SetFillColor(15, 118, 110);
        $pdf->Rect($leftMargin, $pdf->GetY(), $usableWidth, 24, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('dejavusans', 'B', 20, '', true);
        $this->renderPdfCell($pdf, $group->title, $usableWidth - 12, 14, 'C', 0, false, 1, $leftMargin + 6, $pdf->GetY() + 5);
        $pdf->SetY($pdf->GetY() + 7);

        $questionCount = (string)($group->questions_count ?? $group->questions->count());
        $metaItems = [
            [trans('quiz.Question Group'), $group->title],
            [trans('quiz.Total Questions'), $questionCount],
            [trans('common.Date'), showDate($group->created_at)],
        ];
        $metaGap = 2;
        $metaWidth = ($usableWidth - ($metaGap * 2)) / 3;
        $metaY = $pdf->GetY();

        foreach ($metaItems as $index => [$label, $value]) {
            $metaX = $leftMargin + (($metaWidth + $metaGap) * $index);
            $pdf->SetFillColor(248, 250, 252);
            $pdf->SetDrawColor(226, 232, 240);
            $pdf->Rect($metaX, $metaY, $metaWidth, 17, 'DF');
            $pdf->SetTextColor(71, 85, 105);
            $pdf->SetFont('dejavusans', 'B', 8.5, '', true);
            $this->renderPdfCell($pdf, $label, $metaWidth - 6, 5, 'C', 0, false, 1, $metaX + 3, $metaY + 2);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->SetFont('dejavusans', '', 9.5, '', true);
            $this->renderPdfCell($pdf, $value, $metaWidth - 6, 6, 'C', 0, false, 1, $metaX + 3, $metaY + 8);
        }

        $pdf->SetY($metaY + 23);

        foreach ($group->questions as $index => $question) {
            $this->renderPdfQuestion($pdf, $question, $index + 1);
        }
    }

    private function renderPdfQuestion(TCPDF $pdf, $question, int $number): void
    {
        $leftMargin = $pdf->getMargins()['left'];
        $usableWidth = $pdf->getPageWidth() - $leftMargin - $pdf->getMargins()['right'];
        $headerLeftWidth = floor($usableWidth / 2);
        $headerRightWidth = $usableWidth - $headerLeftWidth;
        $title = trans('quiz.Question') . ' ' . $number;
        $typeLabel = getQuestionType($question->type);
        if (!empty($question->marks)) {
            $typeLabel .= ' | ' . trans('quiz.Marks') . ': ' . $question->marks;
        }

        $questionParts = $this->extractPdfContentParts($question->question);
        $questionImage = $this->resolvePdfImagePath($question->image);
        $detailItemCount = match ($question->type) {
            'M' => ($question->questionMuInSerial ?? collect())->count(),
            'O' => ($question->questionSortingOptionsSerial ?? collect())->count(),
            'X', 'P' => ($question->questionMuInSerial ?? collect())->where('type', 1)->count(),
            'C' => ($question->questionMuInSerial ?? collect())->groupBy('group')->count(),
            default => 1,
        };
        $minimumQuestionHeight = 31 + (min(8, $detailItemCount) * 6);
        if (!blank($question->explanation)) {
            $minimumQuestionHeight += 11;
        }
        $minimumQuestionHeight += collect($questionParts)->where('type', 'math')->count() * 18;
        if ($questionImage) {
            $imageSize = $this->getPdfImageSize($questionImage, $usableWidth);
            $minimumQuestionHeight += ($imageSize['height'] ?? 0) + 6;
        }
        $pageCapacity = $pdf->getPageHeight() - $pdf->getMargins()['top'] - $pdf->getMargins()['bottom'] - 1;
        $this->ensurePdfSpace($pdf, min($minimumQuestionHeight, $pageCapacity));

        $pdf->SetFillColor(248, 250, 252);
        $pdf->SetDrawColor(219, 228, 240);
        $pdf->SetLineWidth(0.2);
        $headerY = $pdf->GetY();
        $pdf->SetFont('dejavusans', '', 11, '', true);
        $this->renderPdfCell($pdf, $typeLabel, $headerLeftWidth, 10, $this->pdfAlignment($typeLabel), 1, true, 0, $leftMargin, $headerY);
        $pdf->SetFont('dejavusans', 'B', 13, '', true);
        $this->renderPdfCell($pdf, $title, $headerRightWidth, 10, 'R', 1, true, 1, $leftMargin + $headerLeftWidth, $headerY);

        $pdf->SetFont('dejavusans', 'B', 11, '', true);
        $pdf->Ln(2);
        $this->renderPdfCell($pdf, trans('quiz.Question'), $usableWidth, 0, 'R');
        $pdf->SetFont('dejavusans', '', 11, '', true);
        if (!empty($questionParts)) {
            $this->renderPdfContentParts($pdf, $questionParts, $usableWidth, 11);
        }

        if ($questionImage) {
            $this->renderPdfImage($pdf, $questionImage);
        }

        $this->renderPdfQuestionDetails($pdf, $question);

        $explanationParts = $this->extractPdfContentParts($question->explanation);
        if (!empty($explanationParts)) {
            $pdf->Ln(1);
            $pdf->SetFont('dejavusans', 'B', 10, '', true);
            $this->renderPdfCell($pdf, trans('quiz.Explanation'), $usableWidth, 0, 'R');
            $pdf->SetFont('dejavusans', '', 10, '', true);
            $this->renderPdfContentParts($pdf, $explanationParts, $usableWidth, 10);
        }

        $pdf->Ln(4);
        $pdf->SetDrawColor(226, 232, 240);
        $pdf->Line($leftMargin, $pdf->GetY(), $leftMargin + $usableWidth, $pdf->GetY());
        $pdf->Ln(5);
    }

    private function renderPdfQuestionDetails(TCPDF $pdf, $question): void
    {
        if ($question->type === 'M') {
            $this->renderPdfListSection($pdf, trans('quiz.Options'), ($question->questionMuInSerial ?? collect())->map(function ($option) {
                $text = $option->title;
                if ((int)$option->status === 1) {
                    $text = "\u{2713} " . $text;
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
        $usableWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
        $this->renderPdfCell($pdf, $label, $usableWidth, 0, 'R');
        $pdf->SetFont('dejavusans', '', 10, '', true);

        foreach ($items as $item) {
            $item = (string)$item;
            $prefix = $this->pdfAlignment($item) === 'R' ? "\u{2022} " : '- ';
            $this->renderPdfCell($pdf, $prefix . $item, $usableWidth, 0, $this->pdfAlignment($item));
        }
    }

    private function renderPdfImage(TCPDF $pdf, string $path): void
    {
        $pdf->Ln(2);
        $leftMargin = $pdf->getMargins()['left'];
        $rightMargin = $pdf->getMargins()['right'];
        $usableWidth = $pdf->getPageWidth() - $leftMargin - $rightMargin;
        $imageSize = $this->getPdfImageSize($path, $usableWidth);
        if (!$imageSize) {
            return;
        }

        $imageWidth = $imageSize['width'];
        $imageHeight = $imageSize['height'];
        $this->ensurePdfSpace($pdf, $imageHeight + 6);
        $imageX = $leftMargin + (($usableWidth - $imageWidth) / 2);
        $imageY = $pdf->GetY();
        $pdf->Image($path, $imageX, $imageY, $imageWidth, $imageHeight, '', '', '', true, 150, '', false, false, 1, false, false, false);
        $pdf->SetY($imageY + $imageHeight + 4);
    }

    private function getPdfImageSize(string $path, float $usableWidth): ?array
    {
        $dimensions = @getimagesize($path);
        if (!$dimensions || $dimensions[0] <= 0 || $dimensions[1] <= 0) {
            return null;
        }

        $scale = min(min(72, $usableWidth) / $dimensions[0], 72 / $dimensions[1]);

        return [
            'width' => $dimensions[0] * $scale,
            'height' => $dimensions[1] * $scale,
        ];
    }

    private function renderPdfCell(
        TCPDF $pdf,
        ?string $text,
        float $width,
        float $height = 0,
        string $alignment = 'R',
        int $border = 0,
        bool $fill = false,
        int $lineBreak = 1,
        ?float $x = null,
        ?float $y = null
    ): void {
        $text = $this->pdfText($text);
        $pdf->setTempRTL($this->pdfAlignment($text) === 'R' ? 'R' : 'L');
        $pdf->MultiCell($width, $height, $text, $border, $alignment, $fill, $lineBreak, $x, $y, true, 0, false, true);
        $pdf->setTempRTL(false);
    }

    private function ensurePdfSpace(TCPDF $pdf, float $requiredHeight): void
    {
        $bottomLimit = $pdf->getPageHeight() - $pdf->getMargins()['bottom'];
        if ($pdf->GetY() + $requiredHeight > $bottomLimit) {
            $pdf->AddPage();
        }
    }

    private function renderPdfPageNumbers(TCPDF $pdf): void
    {
        $pageCount = $pdf->getNumPages();
        $leftMargin = $pdf->getMargins()['left'];
        $usableWidth = $pdf->getPageWidth() - $leftMargin - $pdf->getMargins()['right'];
        $pdf->SetAutoPageBreak(false);

        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->setPage($page);
            $pdf->SetDrawColor(226, 232, 240);
            $pdf->Line($leftMargin, $pdf->getPageHeight() - 11, $leftMargin + $usableWidth, $pdf->getPageHeight() - 11);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->SetFont('dejavusans', '', 8.5, '', true);
            $this->renderPdfCell($pdf, $page . ' / ' . $pageCount, $usableWidth, 6, 'C', 0, false, 1, $leftMargin, $pdf->getPageHeight() - 9);
        }

        $pdf->SetAutoPageBreak(true, 15);
    }

    private function renderPdfContentParts(TCPDF $pdf, array $parts, float $width, float $fontSize): void
    {
        foreach ($parts as $part) {
            if (($part['type'] ?? '') === 'math') {
                $this->renderPdfMath($pdf, $part, $width, $fontSize + 1);
                continue;
            }

            $text = trim((string)($part['text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $pdf->SetFont('dejavusans', '', $fontSize, '', true);
            $this->renderPdfCell($pdf, $text, $width, 0, $this->pdfAlignment($text));
        }
    }

    private function extractPdfContentParts(?string $html): array
    {
        if (blank($html)) {
            return [];
        }

        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', (string)$html);
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', (string)$html);
        $previousState = libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?><div id="pdf-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        $mathParts = [];

        foreach (['//br', '//p', '//div', '//li', '//tr', '//table', '//ul', '//ol'] as $query) {
            foreach (iterator_to_array($xpath->query($query) ?: []) as $node) {
                if ($node->parentNode) {
                    $node->parentNode->insertBefore($dom->createTextNode("\n"), $node->nextSibling);
                }
            }
        }

        $equationQuery = "//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation ') or contains(concat(' ', normalize-space(@class), ' '), ' note-math ')]";
        foreach (iterator_to_array($xpath->query($equationQuery) ?: []) as $equationNode) {
            $latexNode = $xpath->query(
                ".//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation-latex-src ') or contains(concat(' ', normalize-space(@class), ' '), ' note-latex ')]",
                $equationNode
            )?->item(0);
            $annotationNode = $xpath->query(".//*[local-name()='annotation' and @encoding='application/x-tex']", $equationNode)?->item(0);
            $mathNode = $xpath->query(".//*[local-name()='math']", $equationNode)?->item(0);
            $latex = trim(html_entity_decode(
                (string)($latexNode?->textContent ?: $annotationNode?->textContent ?: ''),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            ));
            $ast = $this->pdfMathAstFromDom($mathNode);
            $index = count($mathParts);
            $mathParts[] = [
                'type' => 'math',
                'ast' => $ast,
                'latex' => $latex,
            ];
            $equationNode->parentNode?->replaceChild($dom->createTextNode("\n[[PDF_MATH_{$index}]]\n"), $equationNode);
        }

        foreach ([
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' note-equation-latex-src ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' note-latex ')]",
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
        $chunks = preg_split('/\[\[PDF_MATH_(\d+)\]\]/', $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $parts = [];

        foreach ($chunks as $chunkIndex => $chunk) {
            if ($chunkIndex % 2 === 1) {
                $mathIndex = (int)$chunk;
                if (isset($mathParts[$mathIndex])) {
                    $parts[] = $mathParts[$mathIndex];
                }
                continue;
            }

            $chunk = trim($chunk);
            if ($chunk !== '') {
                $parts[] = ['type' => 'text', 'text' => $chunk];
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        return $parts;
    }

    private function pdfMathAstFromDom(?\DOMNode $node): ?array
    {
        if (!$node) {
            return null;
        }

        if ($node instanceof \DOMText) {
            $text = trim($node->nodeValue ?? '');
            return $text === '' ? null : ['type' => 'text', 'text' => $text, 'style' => ''];
        }

        if (!$node instanceof \DOMElement) {
            return null;
        }

        $tag = strtolower($node->localName ?: $node->nodeName);
        if ($tag === 'annotation') {
            return null;
        }

        $children = [];
        foreach ($node->childNodes as $childNode) {
            $child = $this->pdfMathAstFromDom($childNode);
            if ($child) {
                $children[] = $child;
            }
        }

        if (in_array($tag, ['mi', 'mn', 'mo', 'mtext', 'ms'], true)) {
            return [
                'type' => 'text',
                'text' => trim((string)$node->textContent),
                'style' => $tag === 'mi' && $node->getAttribute('mathvariant') !== 'normal' ? 'I' : '',
                'operator' => $tag === 'mo',
            ];
        }

        if ($tag === 'mspace') {
            return ['type' => 'space', 'em' => $this->pdfMathEmValue($node->getAttribute('width'), 0.35)];
        }

        if ($tag === 'mfrac' && count($children) >= 2) {
            return ['type' => 'fraction', 'numerator' => $children[0], 'denominator' => $children[1]];
        }

        if ($tag === 'msqrt') {
            return ['type' => 'root', 'base' => $this->pdfMathGroup($children), 'index' => null];
        }

        if ($tag === 'mroot' && count($children) >= 2) {
            return ['type' => 'root', 'base' => $children[0], 'index' => $children[1]];
        }

        if ($tag === 'msup' && count($children) >= 2) {
            return ['type' => 'scripts', 'base' => $children[0], 'sub' => null, 'sup' => $children[1]];
        }

        if ($tag === 'msub' && count($children) >= 2) {
            return ['type' => 'scripts', 'base' => $children[0], 'sub' => $children[1], 'sup' => null];
        }

        if ($tag === 'msubsup' && count($children) >= 3) {
            return ['type' => 'scripts', 'base' => $children[0], 'sub' => $children[1], 'sup' => $children[2]];
        }

        if ($tag === 'mover' && count($children) >= 2) {
            return ['type' => 'limits', 'base' => $children[0], 'under' => null, 'over' => $children[1]];
        }

        if ($tag === 'munder' && count($children) >= 2) {
            return ['type' => 'limits', 'base' => $children[0], 'under' => $children[1], 'over' => null];
        }

        if ($tag === 'munderover' && count($children) >= 3) {
            return ['type' => 'limits', 'base' => $children[0], 'under' => $children[1], 'over' => $children[2]];
        }

        if ($tag === 'mfenced') {
            $open = $node->hasAttribute('open') ? $node->getAttribute('open') : '(';
            $close = $node->hasAttribute('close') ? $node->getAttribute('close') : ')';
            return $this->pdfMathGroup(array_merge([
                ['type' => 'text', 'text' => $open, 'style' => '', 'operator' => false],
            ], $children, [
                ['type' => 'text', 'text' => $close, 'style' => '', 'operator' => false],
            ]));
        }

        if ($tag === 'semantics') {
            return $children[0] ?? null;
        }

        return $this->pdfMathGroup($children);
    }

    private function pdfMathGroup(array $children): ?array
    {
        $children = array_values(array_filter($children));
        if (empty($children)) {
            return null;
        }

        return count($children) === 1 ? $children[0] : ['type' => 'row', 'children' => $children];
    }

    private function pdfMathEmValue(string $value, float $fallback): float
    {
        return preg_match('/-?[0-9.]+/', $value, $matches) === 1 ? (float)$matches[0] : $fallback;
    }

    private function renderPdfMath(TCPDF $pdf, array $part, float $width, float $fontSize): void
    {
        $ast = $part['ast'] ?? null;
        if (!$ast) {
            $fallback = $this->readablePdfLatex((string)($part['latex'] ?? ''));
            if ($fallback !== '') {
                $pdf->SetFont('dejavusans', '', max(8, $fontSize - 1), '', true);
                $this->renderPdfCell($pdf, $fallback, $width, 0, 'C');
            }
            return;
        }

        $layout = $this->layoutPdfMath($pdf, $ast, $fontSize);
        if ($layout['width'] > $width) {
            $scaledFontSize = max(5.5, $fontSize * ($width / $layout['width']));
            $layout = $this->layoutPdfMath($pdf, $ast, $scaledFontSize);
        }

        $blockHeight = max(6, $layout['height'] + $layout['depth'] + 4);
        $this->ensurePdfSpace($pdf, $blockHeight);
        $startY = $pdf->GetY();
        $startX = $pdf->getMargins()['left'] + max(0, ($width - $layout['width']) / 2);
        $baseline = $startY + 2 + $layout['height'];
        $this->drawPdfMathLayout($pdf, $layout, $startX, $baseline);
        $pdf->SetY($startY + $blockHeight);
        $pdf->setTempRTL(false);
    }

    private function layoutPdfMath(TCPDF $pdf, array $node, float $fontSize): array
    {
        $type = $node['type'] ?? 'text';
        $em = $fontSize * 0.352778;

        if ($type === 'space') {
            return [
                'type' => 'space',
                'width' => max(0, (float)($node['em'] ?? 0.35) * $em),
                'height' => 0.0,
                'depth' => 0.0,
            ];
        }

        if ($type === 'text') {
            $text = (string)($node['text'] ?? '');
            $style = (string)($node['style'] ?? '');
            $pdf->SetFont('dejavusans', $style, $fontSize, '', true);
            $operatorPadding = !empty($node['operator']) && !in_array($text, ['(', ')', '[', ']', '{', '}', '|', '‖'], true) ? $em * 0.12 : 0;

            return [
                'type' => 'text',
                'text' => $text,
                'style' => $style,
                'font_size' => $fontSize,
                'padding' => $operatorPadding,
                'width' => $pdf->GetStringWidth($text) + ($operatorPadding * 2),
                'height' => $em * 0.82,
                'depth' => $em * 0.22,
            ];
        }

        if ($type === 'row') {
            $children = [];
            $cursorX = 0.0;
            $height = 0.0;
            $depth = 0.0;
            foreach ($node['children'] ?? [] as $childNode) {
                $child = $this->layoutPdfMath($pdf, $childNode, $fontSize);
                $children[] = ['layout' => $child, 'x' => $cursorX];
                $cursorX += $child['width'];
                $height = max($height, $child['height']);
                $depth = max($depth, $child['depth']);
            }

            return [
                'type' => 'row',
                'children' => $children,
                'width' => $cursorX,
                'height' => $height,
                'depth' => $depth,
            ];
        }

        if ($type === 'fraction') {
            $numerator = $this->layoutPdfMath($pdf, $node['numerator'], $fontSize * 0.9);
            $denominator = $this->layoutPdfMath($pdf, $node['denominator'], $fontSize * 0.9);
            $padding = max(0.7, $em * 0.18);
            $gap = max(0.6, $em * 0.15);
            $rule = max(0.18, $em * 0.055);
            $lineOffset = $em * 0.18;
            $width = max($numerator['width'], $denominator['width']) + ($padding * 2);
            $numeratorBaseline = -$lineOffset - $gap - $numerator['depth'];
            $denominatorBaseline = -$lineOffset + $rule + $gap + $denominator['height'];

            return [
                'type' => 'fraction',
                'numerator' => $numerator,
                'denominator' => $denominator,
                'numerator_baseline' => $numeratorBaseline,
                'denominator_baseline' => $denominatorBaseline,
                'line_y' => -$lineOffset,
                'rule' => $rule,
                'width' => $width,
                'height' => -($numeratorBaseline - $numerator['height']),
                'depth' => $denominatorBaseline + $denominator['depth'],
            ];
        }

        if ($type === 'root') {
            $base = $this->layoutPdfMath($pdf, $node['base'], $fontSize);
            $index = !empty($node['index']) ? $this->layoutPdfMath($pdf, $node['index'], $fontSize * 0.52) : null;
            $pdf->SetFont('dejavusans', '', $fontSize * 1.12, '', true);
            $rootWidth = $pdf->GetStringWidth('√') + 0.2;
            $height = max($base['height'] + 0.8, $em * 1.08);
            if ($index) {
                $height = max($height, $base['height'] + $index['height'] + $index['depth'] * 0.35);
            }

            return [
                'type' => 'root',
                'base' => $base,
                'index' => $index,
                'font_size' => $fontSize * 1.12,
                'root_width' => $rootWidth,
                'width' => $rootWidth + $base['width'] + 0.4,
                'height' => $height,
                'depth' => max($base['depth'], $em * 0.18),
            ];
        }

        if ($type === 'scripts') {
            $base = $this->layoutPdfMath($pdf, $node['base'], $fontSize);
            $sub = !empty($node['sub']) ? $this->layoutPdfMath($pdf, $node['sub'], $fontSize * 0.68) : null;
            $sup = !empty($node['sup']) ? $this->layoutPdfMath($pdf, $node['sup'], $fontSize * 0.68) : null;
            $scriptWidth = max($sub['width'] ?? 0, $sup['width'] ?? 0);
            $supBaseline = $sup ? -max($base['height'] * 0.72, $sup['depth'] + ($em * 0.32)) : 0;
            $subBaseline = $sub ? max($base['depth'] + ($em * 0.24), $sub['height'] * 0.72) : 0;

            return [
                'type' => 'scripts',
                'base' => $base,
                'sub' => $sub,
                'sup' => $sup,
                'sup_baseline' => $supBaseline,
                'sub_baseline' => $subBaseline,
                'script_x' => $base['width'] + 0.25,
                'width' => $base['width'] + ($scriptWidth > 0 ? $scriptWidth + 0.35 : 0),
                'height' => max($base['height'], $sup ? -($supBaseline - $sup['height']) : 0),
                'depth' => max($base['depth'], $sub ? $subBaseline + $sub['depth'] : 0),
            ];
        }

        if ($type === 'limits') {
            $base = $this->layoutPdfMath($pdf, $node['base'], $fontSize * 1.05);
            $under = !empty($node['under']) ? $this->layoutPdfMath($pdf, $node['under'], $fontSize * 0.66) : null;
            $over = !empty($node['over']) ? $this->layoutPdfMath($pdf, $node['over'], $fontSize * 0.66) : null;
            $width = max($base['width'], $under['width'] ?? 0, $over['width'] ?? 0) + 0.5;
            $gap = max(0.45, $em * 0.12);
            $overBaseline = $over ? -$base['height'] - $gap - $over['depth'] : 0;
            $underBaseline = $under ? $base['depth'] + $gap + $under['height'] : 0;

            return [
                'type' => 'limits',
                'base' => $base,
                'under' => $under,
                'over' => $over,
                'over_baseline' => $overBaseline,
                'under_baseline' => $underBaseline,
                'width' => $width,
                'height' => max($base['height'], $over ? -($overBaseline - $over['height']) : 0),
                'depth' => max($base['depth'], $under ? $underBaseline + $under['depth'] : 0),
            ];
        }

        return $this->layoutPdfMath($pdf, ['type' => 'text', 'text' => '', 'style' => ''], $fontSize);
    }

    private function drawPdfMathLayout(TCPDF $pdf, array $layout, float $x, float $baseline): void
    {
        $type = $layout['type'];
        if ($type === 'space') {
            return;
        }

        if ($type === 'text') {
            if ($layout['text'] === '') {
                return;
            }
            $pdf->SetFont('dejavusans', $layout['style'], $layout['font_size'], '', true);
            $pdf->setTempRTL('L');
            $pdf->Text($x + $layout['padding'], $baseline - $layout['height'], $layout['text'], 0, false, true, 0, 0, 'L', false, '', 0, false, 'T', 'M', true);
            return;
        }

        if ($type === 'row') {
            foreach ($layout['children'] as $child) {
                $this->drawPdfMathLayout($pdf, $child['layout'], $x + $child['x'], $baseline);
            }
            return;
        }

        if ($type === 'fraction') {
            $numX = $x + (($layout['width'] - $layout['numerator']['width']) / 2);
            $denX = $x + (($layout['width'] - $layout['denominator']['width']) / 2);
            $this->drawPdfMathLayout($pdf, $layout['numerator'], $numX, $baseline + $layout['numerator_baseline']);
            $this->drawPdfMathLayout($pdf, $layout['denominator'], $denX, $baseline + $layout['denominator_baseline']);
            $pdf->SetLineWidth($layout['rule']);
            $pdf->Line($x, $baseline + $layout['line_y'], $x + $layout['width'], $baseline + $layout['line_y']);
            $pdf->SetLineWidth(0.2);
            return;
        }

        if ($type === 'root') {
            $pdf->SetFont('dejavusans', '', $layout['font_size'], '', true);
            $pdf->setTempRTL('L');
            $pdf->Text($x, $baseline - $layout['height'] + 0.15, '√', 0, false, true, 0, 0, 'L', false, '', 0, false, 'T', 'M', true);
            $baseX = $x + $layout['root_width'];
            $this->drawPdfMathLayout($pdf, $layout['base'], $baseX, $baseline);
            $pdf->SetLineWidth(0.18);
            $pdf->Line($baseX - 0.25, $baseline - $layout['base']['height'] - 0.45, $baseX + $layout['base']['width'] + 0.25, $baseline - $layout['base']['height'] - 0.45);
            $pdf->SetLineWidth(0.2);
            if ($layout['index']) {
                $this->drawPdfMathLayout($pdf, $layout['index'], $x, $baseline - $layout['base']['height'] + 0.15);
            }
            return;
        }

        if ($type === 'scripts') {
            $this->drawPdfMathLayout($pdf, $layout['base'], $x, $baseline);
            if ($layout['sup']) {
                $this->drawPdfMathLayout($pdf, $layout['sup'], $x + $layout['script_x'], $baseline + $layout['sup_baseline']);
            }
            if ($layout['sub']) {
                $this->drawPdfMathLayout($pdf, $layout['sub'], $x + $layout['script_x'], $baseline + $layout['sub_baseline']);
            }
            return;
        }

        if ($type === 'limits') {
            $baseX = $x + (($layout['width'] - $layout['base']['width']) / 2);
            $this->drawPdfMathLayout($pdf, $layout['base'], $baseX, $baseline);
            if ($layout['over']) {
                $overX = $x + (($layout['width'] - $layout['over']['width']) / 2);
                $this->drawPdfMathLayout($pdf, $layout['over'], $overX, $baseline + $layout['over_baseline']);
            }
            if ($layout['under']) {
                $underX = $x + (($layout['width'] - $layout['under']['width']) / 2);
                $this->drawPdfMathLayout($pdf, $layout['under'], $underX, $baseline + $layout['under_baseline']);
            }
        }
    }

    private function readablePdfLatex(string $latex): string
    {
        $latex = trim($latex);
        if ($latex === '') {
            return '';
        }

        for ($iteration = 0; $iteration < 8; $iteration++) {
            $previous = $latex;
            $latex = preg_replace('/\\\\frac\s*\{([^{}]*)\}\s*\{([^{}]*)\}/u', '($1)/($2)', $latex) ?? $latex;
            $latex = preg_replace('/\\\\sqrt\s*\[([^\]]+)\]\s*\{([^{}]*)\}/u', '$1√($2)', $latex) ?? $latex;
            $latex = preg_replace('/\\\\sqrt\s*\{([^{}]*)\}/u', '√($1)', $latex) ?? $latex;
            if ($latex === $previous) {
                break;
            }
        }

        $latex = strtr($latex, [
            '\\infty' => '∞',
            '\\int' => '∫',
            '\\sum' => '∑',
            '\\prod' => '∏',
            '\\times' => '×',
            '\\cdot' => '·',
            '\\pm' => '±',
            '\\leq' => '≤',
            '\\geq' => '≥',
            '\\neq' => '≠',
            '\\pi' => 'π',
            '\\theta' => 'θ',
            '\\alpha' => 'α',
            '\\beta' => 'β',
            '\\left' => '',
            '\\right' => '',
            '\\,' => ' ',
            '\\;' => ' ',
        ]);
        $latex = preg_replace('/_\{([^{}]+)\}/u', '_($1)', $latex) ?? $latex;
        $latex = preg_replace('/\^\{([^{}]+)\}/u', '^($1)', $latex) ?? $latex;
        $latex = str_replace(['{', '}'], ['(', ')'], $latex);
        $latex = preg_replace('/\\\\([a-zA-Z]+)/', '$1', $latex) ?? $latex;

        return trim(preg_replace('/\s+/', ' ', $latex) ?? $latex);
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
