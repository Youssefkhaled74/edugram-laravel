<?php

namespace Modules\ParentModule\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\ParentModule\Models\ParentModel;
use Modules\ParentModule\Models\ParentChild;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\Course;

class ParentCourseController extends Controller
{
    /**
     * Display all courses for all children.
     * FIXED: Corrected CourseEnrolled model namespace references
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
            
            $children = $parent->children()->wherePivot('status', 'active')->get();
            
            // FIXED: Get all enrolled courses for all children using correct model
            $allCourses = collect();
            
            try {
                foreach ($children as $child) {
                    $courses = CourseEnrolled::where('user_id', $child->id)
                        ->with('course')
                        ->get();
                    
                    foreach ($courses as $course) {
                        $course->student_name = $child->name;
                        $course->student_id = $child->id;
                    }
                    
                    $allCourses = $allCourses->merge($courses);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to get enrolled courses: ' . $e->getMessage());
            }
            
            return view('parentmodule::courses.index', compact('parent', 'children', 'allCourses'));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Failed to load courses: ' . $e->getMessage());
        }
    }

    /**
     * Display courses for a specific child.
     * FIXED: Corrected CourseEnrolled model namespace references
     */
    public function childCourses($childId)
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
                return redirect()->route('parent.courses.index')
                    ->with('error', 'Child not found or does not belong to you.');
            }
            
            $child = User::find($childId);
            
            if (!$child) {
                return redirect()->route('parent.courses.index')
                    ->with('error', 'Student not found.');
            }
            
            // FIXED: Get enrolled courses for this child using correct model
            $enrolledCourses = collect();
            $availableCourses = collect();
            
            try {
                $enrolledCourses = CourseEnrolled::where('user_id', $child->id)
                    ->with('course')
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Failed to get enrolled courses for child ' . $childId . ': ' . $e->getMessage());
            }
            
            // Get available courses (not enrolled)
            try {
                $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();
                
                $availableCourses = Course::where('status', 1)
                    ->whereNotIn('id', $enrolledCourseIds)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Failed to get available courses: ' . $e->getMessage());
            }
            
            return view('parentmodule::courses.child-courses', compact(
                'parent',
                'child',
                'enrolledCourses',
                'availableCourses'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.courses.index')
                ->with('error', 'Failed to load courses: ' . $e->getMessage());
        }
    }

    /**
     * Show course details.
     */
    public function show($courseId)
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
            
            try {
                $course = Course::find($courseId);
            } catch (\Exception $e) {
                return redirect()->route('parent.courses.index')
                    ->with('error', 'Course module not available.');
            }
            
            if (!$course) {
                return redirect()->route('parent.courses.index')
                    ->with('error', 'Course not found.');
            }
            
            // Get course instructor
            $instructor = null;
            if (isset($course->user_id) && $course->user_id) {
                $instructor = User::find($course->user_id);
            }
            
            // Get parent's children (ACTIVE relationships only)
            $children = $parent->children()
                ->wherePivot('status', 'active')
                ->get();
            
            return view('parentmodule::courses.show', compact('parent', 'course', 'instructor', 'children'));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.courses.index')
                ->with('error', 'Failed to load course details: ' . $e->getMessage());
        }
    }

    /**
     * Enroll a child in a course.
     * FIXED: Corrected CourseEnrolled model namespace references
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'student_id' => 'required|integer',
        ]);

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
                ->where('student_id', $request->student_id)
                ->where('status', 'active')
                ->first();
            
            if (!$relationship) {
                return redirect()->back()
                    ->with('error', 'You do not have permission to enroll this student.');
            }
            
            // Check if course exists
            try {
                $course = Course::find($request->course_id);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Course module not available.');
            }
            
            if (!$course) {
                return redirect()->back()
                    ->with('error', 'Course not found.');
            }
            
            // FIXED: Check if already enrolled using correct model
            try {
                $exists = CourseEnrolled::where('user_id', $request->student_id)
                    ->where('course_id', $request->course_id)
                    ->exists();
                
                if ($exists) {
                    return redirect()->back()
                        ->with('error', 'Student is already enrolled in this course.');
                }
                
                // FIXED: Create enrollment using correct model
                CourseEnrolled::create([
                    'user_id' => $request->student_id,
                    'course_id' => $request->course_id,
                    'enrolled_date' => now(),
                    'status' => 1,
                    'lms_id' => 1
                ]);
                
                $student = User::find($request->student_id);
                
                return redirect()->back()
                    ->with('success', $student->name . ' has been enrolled in ' . $course->title . ' successfully!');
                    
            } catch (\Exception $e) {
                \Log::error('Failed to enroll student: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Enrollment system not available.');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to enroll student: ' . $e->getMessage());
        }
    }

    /**
     * Search courses.
     */
    public function search(Request $request)
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
            
            $searchTerm = $request->input('q', '');
            
            $courses = Course::where('status', 1)
                ->where(function($query) use ($searchTerm) {
                    $query->where('title', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%');
                })
                ->paginate(12);
            
            $children = $parent->children()->wherePivot('status', 'active')->get();
            
            return view('parentmodule::courses.search', compact('parent', 'courses', 'children', 'searchTerm'));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.courses.index')
                ->with('error', 'Failed to search courses: ' . $e->getMessage());
        }
    }

    /**
     * Get available courses for a child.
     */
    public function availableCourses($childId)
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
                return redirect()->route('parent.courses.index')
                    ->with('error', 'Child not found or does not belong to you.');
            }
            
            $child = User::find($childId);
            
            if (!$child) {
                return redirect()->route('parent.courses.index')
                    ->with('error', 'Student not found.');
            }
            
            // Get enrolled course IDs
            $enrolledCourseIds = [];
            try {
                $enrolledCourseIds = CourseEnrolled::where('user_id', $child->id)
                    ->pluck('course_id')
                    ->toArray();
            } catch (\Exception $e) {
                \Log::error('Failed to get enrolled courses for child ' . $childId . ': ' . $e->getMessage());
            }
            
            // Get available courses
            $availableCourses = collect();
            try {
                $availableCourses = Course::where('status', 1)
                    ->whereNotIn('id', $enrolledCourseIds)
                    ->paginate(12);
            } catch (\Exception $e) {
                \Log::error('Failed to get available courses: ' . $e->getMessage());
            }
            
            return view('parentmodule::courses.available', compact(
                'parent',
                'child',
                'availableCourses'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.courses.index')
                ->with('error', 'Failed to load available courses: ' . $e->getMessage());
        }
    }
}