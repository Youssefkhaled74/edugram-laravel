<?php

namespace Modules\ParentModule\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ParentModule\Models\ParentModel;
use Modules\ParentModule\Models\ParentChild;

class ParentReportController extends Controller
{
    /**
     * Display reports for all children.
     */
    public function index()
    {
        try {
            $parent = ParentModel::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'is_verified' => false,
                    'status' => 'active',
                    'lms_id' => 1
                ]
            );
            
            $children = $parent->children()
                ->wherePivot('status', 'active')
                ->get();
            
            return view('parentmodule::reports.index', compact('parent', 'children'));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Failed to load reports: ' . $e->getMessage());
        }
    }

    /**
     * Display reports for a specific child.
     */
    public function childReports($childId)
    {
        try {
            $parent = ParentModel::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'is_verified' => false,
                    'status' => 'active',
                    'lms_id' => 1
                ]
            );
            
            // Verify this child belongs to the parent
            $relationship = ParentChild::where('parent_id', $parent->id)
                ->where('student_id', $childId)
                ->where('status', 'active')
                ->first();
            
            if (!$relationship) {
                return redirect()->route('parent.reports.index')
                    ->with('error', 'Child not found or does not belong to you.');
            }
            
            $child = User::find($childId);
            
            if (!$child) {
                return redirect()->route('parent.reports.index')
                    ->with('error', 'Student not found.');
            }
            
            // Get enrolled courses
            $enrolledCourses = collect();
            if (class_exists('\App\Models\CourseEnrolled')) {
                $enrolledCourses = \App\Models\CourseEnrolled::where('user_id', $child->id)
                    ->with('course')
                    ->get();
            }
            
            // Get quiz results
            $quizResults = collect();
            if (class_exists('\Modules\Quiz\Entities\StudentTakeOnlineQuiz')) {
                $quizResults = \Modules\Quiz\Entities\StudentTakeOnlineQuiz::where('student_id', $child->id)
                    ->with('quiz')
                    ->latest()
                    ->get();
            }
            
            // Get attendance records
            $attendanceRecords = collect();
            if (class_exists('\Modules\Attendance\Entities\StudentAttendance')) {
                $attendanceRecords = \Modules\Attendance\Entities\StudentAttendance::where('student_id', $child->id)
                    ->latest()
                    ->limit(20)
                    ->get();
            }
            
            // Calculate statistics
            $stats = [
                'total_courses' => $enrolledCourses->count(),
                'total_quizzes' => $quizResults->count(),
                'average_score' => $quizResults->count() > 0 ? round($quizResults->avg('score'), 2) : 0,
                'attendance_rate' => 0,
            ];
            
            return view('parentmodule::reports.child-reports', compact(
                'parent',
                'child',
                'enrolledCourses',
                'quizResults',
                'attendanceRecords',
                'stats'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.reports.index')
                ->with('error', 'Failed to load child reports: ' . $e->getMessage());
        }
    }

    /**
     * Export report for a specific child.
     */
    public function exportReport($childId)
    {
        try {
            $parent = ParentModel::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'is_verified' => false,
                    'status' => 'active',
                    'lms_id' => 1
                ]
            );
            
            // Verify this child belongs to the parent
            $relationship = ParentChild::where('parent_id', $parent->id)
                ->where('student_id', $childId)
                ->where('status', 'active')
                ->first();
            
            if (!$relationship) {
                return redirect()->route('parent.reports.index')
                    ->with('error', 'Child not found or does not belong to you.');
            }
            
            $child = User::find($childId);
            
            if (!$child) {
                return redirect()->route('parent.reports.index')
                    ->with('error', 'Student not found.');
            }
            
            // TODO: Implement PDF export functionality
            
            return redirect()->back()
                ->with('info', 'Report export feature coming soon!');
            
        } catch (\Exception $e) {
            return redirect()->route('parent.reports.index')
                ->with('error', 'Failed to export report: ' . $e->getMessage());
        }
    }
}
