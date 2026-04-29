<?php

namespace App\Http\Controllers;

use App\LessonComplete;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\CourseSetting\Entities\Lesson;
use Modules\Quiz\Entities\QuizMarking;

class TeacherStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $teacherId = $this->teacherIdOrFail();
        $courseQuery = Course::query()->where('user_id', $teacherId)->where('type', 1);
        $selectedCourseId = (int)$request->get('course_id', 0);
        if ($selectedCourseId > 0) {
            $courseQuery->where('id', $selectedCourseId);
        }

        $courses = $courseQuery->with(['enrolls.user', 'reviews'])->latest()->get();
        $courseIds = $courses->pluck('id')->toArray();

        $courseRows = [];
        foreach ($courses as $course) {
            $courseRows[] = $this->buildCourseMetrics($course);
        }

        $summary = [
            'total_courses' => $courses->count(),
            'total_enrolled_students' => array_sum(array_column($courseRows, 'students_count')),
            'total_revenue' => array_sum(array_column($courseRows, 'total_revenue')),
            'avg_completion' => $this->avg(array_column($courseRows, 'completion_percentage')),
            'avg_quiz_score' => $this->avg(array_column($courseRows, 'quiz_avg_score')),
            'assignments_submitted' => array_sum(array_column($courseRows, 'assignments_submitted')),
        ];

        $recentEnrollments = CourseEnrolled::query()
            ->whereIn('course_id', $courseIds)
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->take(10)
            ->get();

        $charts = $this->buildTeacherCharts($courseRows, $courseIds);

        return view('backend.teacher.statistics.index', [
            'courses' => $courses,
            'courseRows' => $courseRows,
            'summary' => $summary,
            'charts' => $charts,
            'recentEnrollments' => $recentEnrollments,
            'selectedCourseId' => $selectedCourseId,
        ]);
    }

    public function courseAnalytics(Course $course)
    {
        $teacherId = $this->teacherIdOrFail();
        if ((int)$course->user_id !== (int)$teacherId) {
            abort(403);
        }

        $course->load(['enrolls.user', 'reviews']);
        $metrics = $this->buildCourseMetrics($course);

        $recentEnrollments = CourseEnrolled::query()
            ->where('course_id', $course->id)
            ->with(['user:id,name,email'])
            ->latest()
            ->take(10)
            ->get();

        $charts = $this->buildCourseCharts($metrics, $course->id);

        return view('backend.teacher.statistics.course', [
            'course' => $course,
            'metrics' => $metrics,
            'charts' => $charts,
            'recentEnrollments' => $recentEnrollments,
        ]);
    }

    private function teacherIdOrFail(): int
    {
        $user = Auth::user();
        if ((int)$user->role_id !== 2) {
            abort(403);
        }
        return (int)$user->id;
    }

    private function buildCourseMetrics(Course $course): array
    {
        $enrolls = CourseEnrolled::query()->where('course_id', $course->id)->get();
        $studentsCount = $enrolls->count();
        $lessonCount = Lesson::query()->where('course_id', $course->id)->count();

        $quizIds = Lesson::query()->where('course_id', $course->id)
            ->whereNotNull('quiz_id')
            ->where('quiz_id', '>', 0)
            ->pluck('quiz_id')
            ->toArray();
        if (!empty($course->quiz_id)) {
            $quizIds[] = (int)$course->quiz_id;
        }
        $quizIds = array_values(array_unique(array_filter($quizIds)));

        $completionPercentages = [];
        foreach ($enrolls as $enroll) {
            $completionPercentages[] = (float)$course->userTotalPercentage($enroll->user_id, $course->id);
        }
        $completionPercentage = $this->avg($completionPercentages);

        $quizAverage = 0.0;
        if (!empty($quizIds) && Schema::hasTable('quiz_markings')) {
            $quizAverage = (float)QuizMarking::query()->whereIn('quiz_id', $quizIds)->avg('marks');
        }

        $assignmentsSubmitted = $this->getAssignmentsSubmittedCount($course->id);
        $courseRevenue = (float)$enrolls->sum('reveune');
        if ($courseRevenue <= 0) {
            $courseRevenue = (float)$enrolls->sum('purchase_price');
        }

        $ratingAvg = (float)CourseReveiw::query()
            ->where('course_id', $course->id)
            ->where('status', 1)
            ->avg('star');
        $ratingCount = (int)CourseReveiw::query()
            ->where('course_id', $course->id)
            ->where('status', 1)
            ->count();

        return [
            'course' => $course,
            'students_count' => $studentsCount,
            'completion_percentage' => round($completionPercentage, 2),
            'lectures_count' => $lessonCount,
            'quizzes_count' => count($quizIds),
            'quiz_avg_score' => round($quizAverage, 2),
            'assignments_submitted' => $assignmentsSubmitted,
            'total_revenue' => round($courseRevenue, 2),
            'rating_avg' => round($ratingAvg, 2),
            'rating_count' => $ratingCount,
        ];
    }

    private function buildTeacherCharts(array $courseRows, array $courseIds): array
    {
        $enrollmentSeries = CourseEnrolled::query()
            ->whereIn('course_id', $courseIds)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $revenueSeries = CourseEnrolled::query()
            ->whereIn('course_id', $courseIds)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(COALESCE(NULLIF(reveune,0), purchase_price, 0)) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'enrollment' => [
                'labels' => $enrollmentSeries->pluck('period')->toArray(),
                'data' => $enrollmentSeries->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            ],
            'revenue' => [
                'labels' => $revenueSeries->pluck('period')->toArray(),
                'data' => $revenueSeries->pluck('total')->map(fn($v) => round((float)$v, 2))->toArray(),
            ],
            'completion' => [
                'labels' => array_map(fn($row) => strip_tags((string)$row['course']->title), $courseRows),
                'data' => array_map(fn($row) => (float)$row['completion_percentage'], $courseRows),
            ],
            'quiz_avg' => [
                'labels' => array_map(fn($row) => strip_tags((string)$row['course']->title), $courseRows),
                'data' => array_map(fn($row) => (float)$row['quiz_avg_score'], $courseRows),
            ],
        ];
    }

    private function buildCourseCharts(array $metrics, int $courseId): array
    {
        $enrollmentSeries = CourseEnrolled::query()
            ->where('course_id', $courseId)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $revenueSeries = CourseEnrolled::query()
            ->where('course_id', $courseId)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(COALESCE(NULLIF(reveune,0), purchase_price, 0)) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'enrollment' => [
                'labels' => $enrollmentSeries->pluck('period')->toArray(),
                'data' => $enrollmentSeries->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            ],
            'revenue' => [
                'labels' => $revenueSeries->pluck('period')->toArray(),
                'data' => $revenueSeries->pluck('total')->map(fn($v) => round((float)$v, 2))->toArray(),
            ],
            'completion' => [
                'labels' => ['completion'],
                'data' => [(float)$metrics['completion_percentage']],
            ],
            'quiz_avg' => [
                'labels' => ['quiz_avg'],
                'data' => [(float)$metrics['quiz_avg_score']],
            ],
        ];
    }

    private function getAssignmentsSubmittedCount(int $courseId): int
    {
        // TODO: Normalize this when Assignment module model is available in this installation.
        if (Schema::hasTable('infix_submitted_homeworks') && Schema::hasTable('infix_homeworks')) {
            return (int)DB::table('infix_submitted_homeworks')
                ->join('infix_homeworks', 'infix_homeworks.id', '=', 'infix_submitted_homeworks.homework_id')
                ->where('infix_homeworks.course_id', $courseId)
                ->count();
        }

        return 0;
    }

    private function avg(array $values): float
    {
        $values = array_filter($values, fn($v) => is_numeric($v));
        $count = count($values);
        if ($count === 0) {
            return 0;
        }
        return (float)(array_sum($values) / $count);
    }
}

