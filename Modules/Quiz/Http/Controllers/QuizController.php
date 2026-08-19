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
        $html = view('quiz::print_group', [
            'group' => $group,
            'pdfEngine' => 'tcpdf',
        ])->render();

        $this->bootstrapTcpdfFonts();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle($group->title ?: 'Question Group');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->setImageScale(1.25);
        $pdf->setFontSubsetting(true);
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
