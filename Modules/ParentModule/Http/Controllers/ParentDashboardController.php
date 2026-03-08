<?php

namespace Modules\ParentModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ParentModule\Models\ParentModel;
use Modules\ParentModule\Models\ParentChild;
use Modules\ParentModule\Models\ParentPayment;
use Modules\ParentModule\Models\ParentNotification;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Quiz\Entities\StudentTakeOnlineQuiz;

class ParentDashboardController extends Controller
{
    /**
     * Display parent dashboard.
     * FIXED: Corrected CourseEnrolled model namespace references
     */
    public function index()
    {
        // Get or create parent profile using firstOrCreate
        $parent = ParentModel::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'is_verified' => false,
                'status' => 'active',
                'lms_id' => 1
            ]
        );
        
        // Initialize default values
        $childrenCount = 0;
        $activeCourses = 0;
        $pendingPayments = 0;
        $unreadNotifications = 0;
        $children = collect();
        $recentActivities = collect();
        
        // Get children count (only active relationships)
        $childrenCount = ParentChild::where('parent_id', $parent->id)
            ->where('status', 'active')
            ->count();
        
        // Only proceed if we have children
        if ($childrenCount > 0) {
            // Get all children IDs
            $childrenIds = ParentChild::where('parent_id', $parent->id)
                ->where('status', 'active')
                ->pluck('student_id')
                ->toArray();

            // FIXED: Use CourseEnrolled directly instead of \App\Models\CourseEnrolled
            try {
                $activeCourses = CourseEnrolled::whereIn('user_id', $childrenIds)->count();
            } catch (\Exception $e) {
                \Log::error('Failed to get active courses count: ' . $e->getMessage());
                $activeCourses = 0;
            }
            
            // Get children with their details
            $children = $parent->children()
                ->wherePivot('status', 'active')
                ->get()
                ->map(function($child) {
                    // FIXED: Use CourseEnrolled directly
                    try {
                        $child->enrolled_courses_count = CourseEnrolled::where('user_id', $child->id)->count();
                    } catch (\Exception $e) {
                        \Log::error('Failed to get enrolled courses for child ' . $child->id . ': ' . $e->getMessage());
                        $child->enrolled_courses_count = 0;
                    }
                    return $child;
                });
            
            // FIXED: Use CourseEnrolled directly for recent activities
            try {
                $recentEnrollments = CourseEnrolled::whereIn('user_id', $childrenIds)
                    ->with(['course', 'user'])
                    ->latest()
                    ->limit(5)
                    ->get();
                
                foreach ($recentEnrollments as $enrollment) {
                    if ($enrollment->course && $enrollment->user) {
                        $recentActivities->push([
                            'type' => 'enrollment',
                            'student_name' => $enrollment->user->name,
                            'course_name' => $enrollment->course->title,
                            'date' => $enrollment->created_at,
                            'icon' => 'fa-book',
                            'color' => 'primary'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to get recent enrollments: ' . $e->getMessage());
            }
        }
        
        // Get pending payments
        try {
            $pendingPayments = ParentPayment::where('parent_id', $parent->id)
                ->where('payment_status', 'pending')
                ->count();
        } catch (\Exception $e) {
            \Log::error('Failed to get pending payments: ' . $e->getMessage());
            $pendingPayments = 0;
        }
        
        // Get unread notifications
        try {
            $unreadNotifications = ParentNotification::where('parent_id', $parent->id)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            \Log::error('Failed to get unread notifications: ' . $e->getMessage());
            $unreadNotifications = 0;
        }

        return view('parentmodule::dashboard.index', compact(
            'parent',
            'childrenCount',
            'activeCourses',
            'pendingPayments',
            'unreadNotifications',
            'children',
            'recentActivities'
        ));
    }

    /**
     * Display parent profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        return view('parentmodule::dashboard.profile', compact('user', 'parent'));
    }

    /**
     * Update parent profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'national_id' => 'nullable|string|max:191',
            'occupation' => 'nullable|string|max:191',
            'workplace' => 'nullable|string|max:191',
            'emergency_contact' => 'nullable|string|max:191',
            'emergency_contact_name' => 'nullable|string|max:191',
            'emergency_contact_relation' => 'nullable|string|max:191',
            'preferred_contact_method' => 'nullable|in:email,phone,sms,whatsapp',
        ]);

        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        // Update user information
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
        ]);

        // Update parent information
        if ($parent) {
            $parent->update([
                'national_id' => $request->national_id,
                'occupation' => $request->occupation,
                'workplace' => $request->workplace,
                'emergency_contact' => $request->emergency_contact,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_relation' => $request->emergency_contact_relation,
                'preferred_contact_method' => $request->preferred_contact_method ?? 'email',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Display all notifications.
     */
    public function notifications()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $notifications = $parent->notifications()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('parentmodule::dashboard.notifications', compact('notifications', 'parent'));
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $notification = ParentNotification::where('id', $id)
            ->where('parent_id', $parent->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $parent->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'All notifications marked as read.');
    }

    /**
     * Display payment history.
     */
    public function paymentHistory()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payments = $parent->payments()
            ->with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_paid' => $parent->payments()->completed()->sum('amount'),
            'total_pending' => $parent->payments()->pending()->sum('amount'),
            'total_refunded' => $parent->payments()->where('payment_status', 'refunded')->sum('refund_amount'),
        ];

        return view('parentmodule::dashboard.payment-history', compact('payments', 'stats', 'parent'));
    }

    /**
     * Download payment invoice.
     */
    public function downloadInvoice($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payment = ParentPayment::where('id', $id)
            ->where('parent_id', $parent->id)
            ->first();

        if (!$payment) {
            return redirect()->back()
                ->with('error', 'Payment not found.');
        }

        if (!$payment->invoice_path || !file_exists(public_path($payment->invoice_path))) {
            return redirect()->back()
                ->with('error', 'Invoice not available.');
        }

        return response()->download(public_path($payment->invoice_path));
    }
}