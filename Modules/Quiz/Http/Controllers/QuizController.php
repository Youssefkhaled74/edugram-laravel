<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\AdvanceQuiz\Http\Controllers\AdvanceQuizGroupController;
use Modules\Quiz\Entities\QuestionGroup;

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
