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

        $allStudentIds = [];
        foreach ($courseRows as $row) {
            $allStudentIds = array_merge($allStudentIds, $row['student_ids']);
        }
        $summary = [
            'total_courses' => $courses->count(),
            'total_enrolled_students' => array_sum(array_column($courseRows, 'students_count')),
            'unique_students_count' => count(array_unique($allStudentIds)),
            'total_revenue' => array_sum(array_column($courseRows, 'total_revenue')),
            'total_sales' => array_sum(array_column($courseRows, 'total_sales')),
            'paid_enrollments' => array_sum(array_column($courseRows, 'paid_enrollments')),
            'free_enrollments' => array_sum(array_column($courseRows, 'free_enrollments')),
            'active_students' => array_sum(array_column($courseRows, 'active_students')),
            'completed_students' => array_sum(array_column($courseRows, 'completed_students')),
            'avg_completion' => $this->avg(array_column($courseRows, 'completion_percentage')),
            'avg_quiz_score' => $this->avg(array_column($courseRows, 'quiz_avg_score')),
            'assignments_submitted' => array_sum(array_column($courseRows, 'assignments_submitted')),
        ];
        $summary['avg_order_value'] = $summary['paid_enrollments'] > 0
            ? round($summary['total_sales'] / $summary['paid_enrollments'], 2)
            : 0;
        $summary['completion_students_rate'] = $summary['total_enrolled_students'] > 0
            ? round(($summary['completed_students'] / $summary['total_enrolled_students']) * 100, 2)
            : 0;

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

    public function courseStatistics(Request $request)
    {
        return $this->index($request);
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

        $enrolls = CourseEnrolled::query()
            ->where('course_id', $course->id)
            ->with('user:id,name,email')
            ->get();

        $studentWatchData = [];
        $totalLessons = Lesson::query()->where('course_id', $course->id)->count();
        $totalCourseDuration = (int)Lesson::query()->where('course_id', $course->id)->sum('duration');

        foreach ($enrolls as $enroll) {
            $userId = (int)$enroll->user_id;
            $completed = LessonComplete::query()
                ->where('course_id', $course->id)
                ->where('user_id', $userId)
                ->where('status', 1)
                ->count();
            $watchSeconds = (int)LessonComplete::query()
                ->where('course_id', $course->id)
                ->where('user_id', $userId)
                ->sum('watch_time_seconds');
            $completion = $totalLessons > 0 ? ceil($completed / $totalLessons * 100) : 0;
            if ($completion > 100) $completion = 100;
            $studentWatchData[] = [
                'user' => $enroll->user,
                'completed_lessons' => $completed,
                'total_lessons' => $totalLessons,
                'completion_percentage' => $completion,
                'watch_seconds' => $watchSeconds,
            ];
        }

        usort($studentWatchData, fn($a, $b) => $b['watch_seconds'] <=> $a['watch_seconds']);

        return view('backend.teacher.statistics.course', [
            'course' => $course,
            'metrics' => $metrics,
            'charts' => $charts,
            'recentEnrollments' => $recentEnrollments,
            'studentWatchData' => $studentWatchData,
            'totalCourseDuration' => $totalCourseDuration,
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
        $studentIds = $enrolls->pluck('user_id')->filter()->unique()->values()->toArray();
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
        $activeStudents = 0;
        $completedStudents = 0;
        foreach ($enrolls as $enroll) {
            $completion = (float)$course->userTotalPercentage($enroll->user_id, $course->id);
            $completionPercentages[] = $completion;
            if ($completion > 0) {
                $activeStudents++;
            }
            if ($completion >= 100) {
                $completedStudents++;
            }
        }
        $completionPercentage = $this->avg($completionPercentages);

        $quizAverage = 0.0;
        if (!empty($quizIds) && Schema::hasTable('quiz_markings')) {
            $quizAverage = (float)QuizMarking::query()->whereIn('quiz_id', $quizIds)->avg('marks');
        }

        $assignmentsSubmitted = $this->getAssignmentsSubmittedCount($course->id);
        $courseRevenue = (float)$enrolls->sum('reveune');
        $courseSales = (float)$enrolls->sum('purchase_price');
        if ($courseRevenue <= 0) {
            $courseRevenue = (float)$enrolls->sum('purchase_price');
        }
        $paidEnrollments = (int)$enrolls->where('purchase_price', '>', 0)->count();
        $freeEnrollments = (int)$enrolls->where('purchase_price', '<=', 0)->count();

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
            'student_ids' => $studentIds,
            'paid_enrollments' => $paidEnrollments,
            'free_enrollments' => $freeEnrollments,
            'active_students' => $activeStudents,
            'completed_students' => $completedStudents,
            'completion_percentage' => round($completionPercentage, 2),
            'lectures_count' => $lessonCount,
            'quizzes_count' => count($quizIds),
            'quiz_avg_score' => round($quizAverage, 2),
            'assignments_submitted' => $assignmentsSubmitted,
            'total_revenue' => round($courseRevenue, 2),
            'total_sales' => round($courseSales, 2),
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
            'top_revenue_courses' => [
                'labels' => array_map(fn($row) => strip_tags((string)$row['course']->title), collect($courseRows)->sortByDesc('total_revenue')->take(8)->values()->all()),
                'data' => array_map(fn($row) => (float)$row['total_revenue'], collect($courseRows)->sortByDesc('total_revenue')->take(8)->values()->all()),
            ],
            'top_students_courses' => [
                'labels' => array_map(fn($row) => strip_tags((string)$row['course']->title), collect($courseRows)->sortByDesc('students_count')->take(8)->values()->all()),
                'data' => array_map(fn($row) => (int)$row['students_count'], collect($courseRows)->sortByDesc('students_count')->take(8)->values()->all()),
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
