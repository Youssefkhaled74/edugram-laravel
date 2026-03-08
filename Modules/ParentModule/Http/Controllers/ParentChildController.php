<?php

namespace Modules\ParentModule\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\ParentModule\Models\ParentModel;
use Modules\ParentModule\Models\ParentChild;
use Modules\ParentModule\Models\ParentStudentRequest;
use Modules\RolePermission\Entities\Role;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Quiz\Entities\StudentTakeOnlineQuiz;

class ParentChildController extends Controller
{
    /**
     * Display all children.
     */
    public function index()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $children = $parent->children()
            ->withPivot([
                'relationship_type',
                'is_primary_parent',
                'can_make_payments',
                'can_view_grades',
                'can_view_attendance',
                'can_communicate_teachers',
                'can_enroll_courses',
                'status'
            ])
            ->get();

        $pendingRequests = $parent->pendingRequests()->get();

        return view('parentmodule::children.index', compact('children', 'pendingRequests', 'parent'));
    }

    /**
     * Show form to add a child.
     */
    public function create()
    {
        return view('parentmodule::children.create');
    }

    /**
     * Store a new child request.
     */
    public function store(Request $request)
    {
        // First, validate registration type
        $request->validate([
            'registration_type' => 'required|in:new,existing',
        ]);

        // Build validation rules based on registration type
        $rules = [
            'relationship_type' => 'required|in:father,mother,guardian,other',
            'is_primary_parent' => 'nullable|boolean',
        ];

        if ($request->registration_type === 'new') {
            // Rules for new student registration
            $rules['name'] = 'required|string|max:191';
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8';
            $rules['phone'] = 'nullable|string|max:100';
            $rules['date_of_birth'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:male,female';
        } else {
            // Rules for linking existing student
            $rules['student_id'] = 'required|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get parent profile - use firstOrCreate to avoid duplicate
            $parent = ParentModel::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'is_verified' => false,
                    'status' => 'active',
                    'lms_id' => 1
                ]
            );

            if ($request->registration_type === 'new') {
                // Create new student account directly
                $student = $this->createNewStudent($request);
                
                if (!$student) {
                    return redirect()->back()
                        ->with('error', 'Failed to create student account.')
                        ->withInput();
                }
                
                $message = 'Student account created successfully!';
            } else {
                // Link to existing student
                $student = User::find($request->student_id);
                
                if (!$student) {
                    return redirect()->back()
                        ->with('error', 'Student not found.')
                        ->withInput();
                }
                
                // Check if already linked
                $exists = ParentChild::where('parent_id', $parent->id)
                    ->where('student_id', $student->id)
                    ->exists();
                
                if ($exists) {
                    return redirect()->back()
                        ->with('error', 'This student is already linked to your account.')
                        ->withInput();
                }
                
                $message = 'Student linked successfully!';
            }

            // Create parent-child relationship (ACTIVE by default - no approval needed)
            ParentChild::create([
                'parent_id' => $parent->id,
                'student_id' => $student->id,
                'relationship_type' => $request->relationship_type,
                'is_primary_parent' => $request->is_primary_parent ?? false,
                'can_make_payments' => true,
                'can_view_grades' => true,
                'can_view_attendance' => true,
                'can_communicate_teachers' => true,
                'can_enroll_courses' => true,
                'status' => 'active', // Direct activation - no approval needed
                'approved_by' => Auth::id(), // Self-approved
                'approved_at' => now(),
                'email_verified_at' => now(),
                'email_verify' => 1,
                'lms_id' => 1
            ]);

            return redirect()->route('parent.children.index')
                ->with('success', $message . ' You can now manage their courses and view reports.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add student: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create a new student account.
     */
    private function createNewStudent(Request $request)
    {
        try {
            // Get student role
            $studentRole = Role::where('name', 'Student')->first();
            
            if (!$studentRole) {
                $studentRole = Role::where('type', 2)->first(); // Type 2 is usually student
            }
            
            if (!$studentRole) {
                throw new \Exception('Student role not found in the system.');
            }

            // Create user account for student
            $student = User::create([
                'role_id' => $studentRole->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'status' => 1, // Active
                'is_active' => true,
                'email_verify' => '0',
                'referral' => Str::random(10),
                'lms_id' => 1
            ]);

            return $student;
            
        } catch (\Exception $e) {
            \Log::error('Failed to create student: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Display child details.
     * FIXED: Corrected CourseEnrolled model namespace references
     */
    public function show($id)
    {
        try {
            // Get parent profile
            $parent = ParentModel::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'is_verified' => false,
                    'status' => 'active',
                    'lms_id' => 1
                ]
            );
            
            // Check if this child belongs to the parent
            $relationship = ParentChild::where('parent_id', $parent->id)
                ->where('student_id', $id)
                ->first();
            
            if (!$relationship) {
                return redirect()->route('parent.children.index')
                    ->with('error', 'Child not found or does not belong to you.');
            }
            
            // Get the child/student user
            $child = User::find($id);
            
            if (!$child) {
                return redirect()->route('parent.children.index')
                    ->with('error', 'Student not found.');
            }
            
            // FIXED: Get enrolled courses using correct model
            $enrolledCourses = [];
            try {
                $enrolledCourses = CourseEnrolled::where('user_id', $child->id)
                    ->with('course')
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Failed to get enrolled courses for child ' . $child->id . ': ' . $e->getMessage());
            }
            
            // Get quiz results (if the model exists)
            $quizResults = [];
            try {
                $quizResults = StudentTakeOnlineQuiz::where('student_id', $child->id)
                    ->with('quiz')
                    ->latest()
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Failed to get quiz results for child ' . $child->id . ': ' . $e->getMessage());
            }
            
            // Get attendance records (if the model exists)
            $attendanceRecords = [];
            if (class_exists('\Modules\Attendance\Entities\StudentAttendance')) {
                try {
                    $attendanceRecords = \Modules\Attendance\Entities\StudentAttendance::where('student_id', $child->id)
                        ->latest()
                        ->limit(10)
                        ->get();
                } catch (\Exception $e) {
                    \Log::error('Failed to get attendance for child ' . $child->id . ': ' . $e->getMessage());
                }
            }
            
            // Calculate statistics
            $stats = [
                'total_courses' => count($enrolledCourses),
                'completed_courses' => 0,
                'in_progress_courses' => count($enrolledCourses),
                'total_quizzes' => count($quizResults),
                'average_score' => 0,
            ];
            
            // Calculate average quiz score
            if (count($quizResults) > 0) {
                $totalScore = 0;
                foreach ($quizResults as $result) {
                    $totalScore += $result->score ?? 0;
                }
                $stats['average_score'] = round($totalScore / count($quizResults), 2);
            }
            
            return view('parentmodule::children.show', compact(
                'child',
                'parent',
                'relationship',
                'enrolledCourses',
                'quizResults',
                'attendanceRecords',
                'stats'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('parent.children.index')
                ->with('error', 'Failed to load child details: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit child permissions.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $relationship = ParentChild::where('parent_id', $parent->id)
            ->where('student_id', $id)
            ->first();

        if (!$relationship) {
            return redirect()->route('parent.children.index')
                ->with('error', 'Child not found.');
        }

        $child = $relationship->student;

        return view('parentmodule::children.edit', compact('child', 'relationship', 'parent'));
    }

    /**
     * Update child permissions.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'can_make_payments' => 'nullable|boolean',
            'can_view_grades' => 'nullable|boolean',
            'can_view_attendance' => 'nullable|boolean',
            'can_communicate_teachers' => 'nullable|boolean',
            'can_enroll_courses' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $relationship = ParentChild::where('parent_id', $parent->id)
            ->where('student_id', $id)
            ->first();

        if (!$relationship) {
            return redirect()->route('parent.children.index')
                ->with('error', 'Child not found.');
        }

        $relationship->update([
            'can_make_payments' => $request->has('can_make_payments'),
            'can_view_grades' => $request->has('can_view_grades'),
            'can_view_attendance' => $request->has('can_view_attendance'),
            'can_communicate_teachers' => $request->has('can_communicate_teachers'),
            'can_enroll_courses' => $request->has('can_enroll_courses'),
        ]);

        return redirect()->route('parent.children.index')
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Remove a child from parent's account.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $relationship = ParentChild::where('parent_id', $parent->id)
            ->where('student_id', $id)
            ->first();

        if (!$relationship) {
            return redirect()->route('parent.children.index')
                ->with('error', 'Child not found.');
        }

        $relationship->delete();

        return redirect()->route('parent.children.index')
            ->with('success', 'Child removed from your account successfully.');
    }

    /**
     * Cancel a pending student request.
     */
    public function cancelRequest($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $request = ParentStudentRequest::where('id', $id)
            ->where('parent_id', $parent->id)
            ->first();

        if (!$request) {
            return redirect()->route('parent.children.index')
                ->with('error', 'Request not found.');
        }

        $request->update(['status' => 'cancelled']);

        return redirect()->route('parent.children.index')
            ->with('success', 'Request cancelled successfully.');
    }
}