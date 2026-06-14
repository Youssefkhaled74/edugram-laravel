<?php

namespace Modules\CourseSetting\Http\Controllers;

use App\Traits\SendNotification;
use App\Traits\UploadMedia;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Modules\Assignment\Entities\InfixAssignment;
use Modules\Certificate\Entities\Certificate;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\Chapter;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseExercise;
use Modules\CourseSetting\Entities\CourseLevel;
use Modules\CourseSetting\Entities\Lesson;
use Modules\Localization\Entities\Language;
use Modules\Quiz\Entities\OnlineQuiz;

class CourseAssignmentController extends Controller
{
    use UploadMedia, SendNotification;

    public function AssignmentStore(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        $validate_rules = [
            'course_id' => 'required|integer',
            'chapter_id' => 'required|integer',
            'title' => 'required|max:255',
            'marks' => 'required|numeric|min:0',
            'min_parcentage' => 'required|min:0',
            'description' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $course = $this->resolveCourseForUser((int)$request->course_id);
            if (!$course) {
                Toastr::error('لا تملك صلاحية الوصول لهذا الكورس', trans('common.Failed'));
                return redirect()->back();
            }
            $chapter = Chapter::where('course_id', $course->id)->find($request->chapter_id);
            if (!$chapter) {
                Toastr::error('هذا الفصل غير موجود في الكورس', trans('common.Failed'));
                return redirect()->back();
            }

            $assignment = new InfixAssignment();
            $assignment->title = $request->title;
            $assignment->course_id = $course->id;
            $assignment->marks = (int)$request->marks;
            $assignment->min_parcentage = (int)$request->min_parcentage;
            $assignment->description = $request->description;
            $assignment->assignment_from = 2;
            $assignment->created_by = Auth::user()->id;
            $assignment->last_date_submission = date('Y-m-d', strtotime($request->last_date_submission));
            if (Schema::hasColumn($assignment->getTable(), 'status')) {
                $assignment->status = 0;
            }
            if (Schema::hasColumn($assignment->getTable(), 'approval_status')) {
                $assignment->approval_status = 'pending_review';
            }
            $assignment->save();
            if ($request->attachment) {
                $assignment->attachment = $this->generateLink($request->attachment, $assignment->id, get_class($assignment), 'attachment');
            }
            $assignment->save();

            if (isset($course) && isset($chapter)) {
                $lesson = null;
                if (!empty($request->lesson_id)) {
                    $lesson = Lesson::where('id', (int)$request->lesson_id)
                        ->where('course_id', $course->id)
                        ->where('chapter_id', $chapter->id)
                        ->first();
                }
                if (!$lesson) {
                    $lesson = new Lesson();
                    $lesson->course_id = (int)$course->id;
                    $lesson->chapter_id = (int)$chapter->id;
                    $lesson->name = $assignment->title;
                }
                $lesson->assignment_id = (int)$assignment->id;
                $lesson->is_quiz = 0;
                $lesson->is_assignment = 1;
                $lesson->is_lock = (int)$request->is_lock;
                $lesson->save();

                if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                    foreach ($course->enrollUsers as $user) {
                        $this->sendNotification('Course_Assignment_Added', $user, [
                            'time' => Carbon::now()->format('d-M-Y, g:i A'),
                            'course' => $course->getTranslation('title', $user->language_code ?? config('app.fallback_locale')),
                            'chapter' => $chapter->name,
                            'assignment' => $assignment->title,
                        ]);

                    }
                }
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }

            Toastr::error(trans('frontend.Invalid Request'), trans('common.Failed'));
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }

    }

    public function AssignmentUpdate(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        $validate_rules = [
            'course_id' => 'required|integer',
            'chapter_id' => 'required|integer',
            'title' => 'required|max:255',
            'marks' => 'required|numeric|min:0',
            'min_parcentage' => 'required|min:0',
            'description' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $course = $this->resolveCourseForUser((int)$request->course_id);
            if (!$course) {
                Toastr::error('لا تملك صلاحية الوصول', trans('common.Failed'));
                return redirect()->back();
            }

            $assignment = InfixAssignment::find($request->id);
            if (!$assignment) {
                Toastr::error(trans('frontend.Invalid Request'), trans('common.Failed'));
                return redirect()->back();
            }
            $assignment->title = $request->title;
            $assignment->course_id = $course->id;
            $assignment->marks = (int)$request->marks;
            $assignment->min_parcentage = (int)$request->min_parcentage;
            $assignment->description = $request->description;
            $assignment->last_date_submission = date('Y-m-d', strtotime($request->last_date_submission));
            $assignment->attachment = null;
            if (Schema::hasColumn($assignment->getTable(), 'status') && Auth::user()->role_id == 2) {
                $assignment->status = 0;
            }
            if (Schema::hasColumn($assignment->getTable(), 'approval_status') && Auth::user()->role_id == 2) {
                $assignment->approval_status = 'pending_review';
            }
            $assignment->save();

            $lesson = Lesson::where('id', (int)$request->lesson_id)
                ->where('course_id', $course->id)
                ->where('chapter_id', (int)$request->chapter_id)
                ->first();
            if ($lesson){
                $lesson->assignment_id = (int)$assignment->id;
                $lesson->is_assignment = 1;
                $lesson->is_lock = (int)$request->is_lock;
                $lesson->save();
            }


            if ($request->attachment) {
                $assignment->attachment = $this->generateLink($request->attachment, $assignment->id, get_class($assignment), 'attachment');
            }
            $assignment->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('courseDetails', $course->id);
        } catch (\Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }

    }


    public function CourseAssignmentShow($id, $chapter_id, $lesson_id)
    {
        try {
            $data = [];
            $data['edit_assignment_id'] = $lesson_id;
            $data['chapter_id'] = $chapter_id;

            // return $data;
            $user = Auth::user();
            $course = $this->resolveCourseForUser((int)$id);
            if (!$course) {
                Toastr::error('لا تملك صلاحية الوصول', trans('common.Failed'));
                return redirect()->back();
            }

            if ($course->type == 1) {

                if ($user->role_id == 2) {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->where('created_by', $user->id)->latest()->get();
                } else {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->latest()->get();
                }

            } else {
                if ($user->role_id == 2) {
                    $quizzes = OnlineQuiz::where('created_by', $user->id)->where('active_status', 1)->get();
                } else {
                    $quizzes = OnlineQuiz::where('active_status', 1)->get();

                }
            }

            $courseSettingController = new CourseSettingController;
            $vdocipher_list = [];
            $chapters = Chapter::where('course_id', $id)->orderBy('position', 'asc')->with('lessons')->get();

            $categories = Category::get();
            $instructors = User::where('role_id', 2)->get();
            $languages = Language::select('id', 'native', 'code')
                ->where('status', '=', 1)
                ->get();
            $course_exercises = CourseExercise::where('course_id', $id)->get();

            $video_list = [];

            $levels = CourseLevel::where('status', 1)->get();
            if (Auth::user()->role_id == 2) {
                $certificates = Certificate::where('created_by', Auth::user()->id)->latest()->get();
            } else {
                $certificates = Certificate::latest()->get();
            }
            $lesson = Lesson::where('id', $lesson_id)->where('course_id', $course->id)->first();
            $edit = $lesson ? InfixAssignment::where('id', $lesson->assignment_id)->first() : null;

            return view('coursesetting::course_details', compact('data', 'edit', 'levels', 'vdocipher_list', 'video_list', 'course', 'chapters', 'categories', 'instructors', 'languages', 'course_exercises', 'quizzes', 'certificates', 'lesson'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    private function resolveCourseForUser(int $courseId): ?Course
    {
        if (Auth::user()->role_id == 2) {
            return Course::where('id', $courseId)->where('user_id', Auth::id())->first();
        }
        return Course::find($courseId);
    }


}
