<?php

namespace Modules\ParentModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\ParentModule\Models\ParentModel;
use Modules\ParentModule\Models\ParentPayment;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use App\Models\User;

class ParentPaymentController extends Controller
{
    /**
     * Display payment page for course enrollment.
     */
    public function enrollCourse($childId, $courseId)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        // Verify parent has access to this child
        $child = $parent->activeChildren()->where('users.id', $childId)->first();

        if (!$child) {
            return redirect()->route('parent.courses.index')
                ->with('error', 'Child not found or you do not have permission.');
        }

        // Check if parent can make payments
        if (!$parent->canPerformAction($childId, 'make_payments')) {
            return redirect()->route('parent.courses.child', $childId)
                ->with('error', 'You do not have permission to make payments for this child.');
        }

        // Check if parent can enroll courses
        if (!$parent->canPerformAction($childId, 'enroll_courses')) {
            return redirect()->route('parent.courses.child', $childId)
                ->with('error', 'You do not have permission to enroll courses for this child.');
        }

        // Get course details
        $course = Course::find($courseId);

        if (!$course) {
            return redirect()->route('parent.courses.available', $childId)
                ->with('error', 'Course not found.');
        }

        // Check if already enrolled
        $existingEnrollment = CourseEnrolled::where('user_id', $childId)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('parent.courses.child', $childId)
                ->with('error', 'This child is already enrolled in this course.');
        }

        // Get available payment methods
        $paymentMethods = $this->getAvailablePaymentMethods();

        return view('parentmodule::payments.enroll-course', compact(
            'parent',
            'child',
            'course',
            'paymentMethods'
        ));
    }

    /**
     * Process course enrollment payment.
     */
    public function processEnrollment(Request $request, $childId, $courseId)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        // Verify parent has access to this child
        $child = $parent->activeChildren()->where('users.id', $childId)->first();

        if (!$child) {
            return redirect()->route('parent.courses.index')
                ->with('error', 'Child not found or you do not have permission.');
        }

        // Check permissions
        if (!$parent->canPerformAction($childId, 'make_payments') || 
            !$parent->canPerformAction($childId, 'enroll_courses')) {
            return redirect()->route('parent.courses.child', $childId)
                ->with('error', 'You do not have permission to complete this action.');
        }

        // Get course details
        $course = Course::find($courseId);

        if (!$course) {
            return redirect()->route('parent.courses.available', $childId)
                ->with('error', 'Course not found.');
        }

        // Check if already enrolled
        $existingEnrollment = CourseEnrolled::where('user_id', $childId)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('parent.courses.child', $childId)
                ->with('error', 'This child is already enrolled in this course.');
        }

        DB::beginTransaction();

        try {
            // If course is free, enroll directly
            if ($course->price == 0 || $course->price == null) {
                $this->enrollStudentInCourse($child, $course, $parent);
                
                DB::commit();
                
                return redirect()->route('parent.courses.child', $childId)
                    ->with('success', 'Child enrolled in course successfully!');
            }

            // For paid courses, create payment record
            $invoiceNumber = ParentPayment::generateInvoiceNumber();

            $payment = ParentPayment::create([
                'parent_id' => $parent->id,
                'student_id' => $childId,
                'course_id' => $courseId,
                'amount' => $course->price,
                'currency' => 'USD', // TODO: Get from settings
                'payment_method' => $request->payment_method,
                'payment_gateway' => $request->payment_method,
                'payment_status' => 'pending',
                'invoice_number' => $invoiceNumber,
                'description' => 'Course enrollment: ' . $course->title,
                'lms_id' => 1
            ]);

            // Redirect to payment gateway based on method
            $redirectUrl = $this->processPaymentGateway(
                $payment,
                $request->payment_method,
                $child,
                $course
            );

            DB::commit();

            if ($redirectUrl) {
                return redirect($redirectUrl);
            }

            // If no redirect (e.g., offline payment), show success message
            return redirect()->route('parent.payment.pending', $payment->id)
                ->with('success', 'Payment initiated. Please complete the payment process.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Payment processing failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display pending payment page.
     */
    public function pendingPayment($paymentId)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payment = ParentPayment::where('id', $paymentId)
            ->where('parent_id', $parent->id)
            ->with(['student', 'course'])
            ->first();

        if (!$payment) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Payment not found.');
        }

        return view('parentmodule::payments.pending', compact('payment', 'parent'));
    }

    /**
     * Handle payment success callback.
     */
    public function paymentSuccess(Request $request, $paymentId)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payment = ParentPayment::where('id', $paymentId)
            ->where('parent_id', $parent->id)
            ->with(['student', 'course'])
            ->first();

        if (!$payment) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Payment not found.');
        }

        DB::beginTransaction();

        try {
            // Mark payment as completed
            $payment->markAsCompleted($request->get('transaction_id'));

            // Enroll student in course
            $this->enrollStudentInCourse(
                User::find($payment->student_id),
                Course::find($payment->course_id),
                $parent,
                $payment
            );

            // TODO: Generate invoice PDF

            DB::commit();

            return redirect()->route('parent.courses.child', $payment->student_id)
                ->with('success', 'Payment successful! Child has been enrolled in the course.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('parent.payment.pending', $paymentId)
                ->with('error', 'Failed to complete enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment failure callback.
     */
    public function paymentFailed(Request $request, $paymentId)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payment = ParentPayment::where('id', $paymentId)
            ->where('parent_id', $parent->id)
            ->first();

        if ($payment) {
            $payment->markAsFailed($request->get('error_message'));
        }

        return redirect()->route('parent.payment.pending', $paymentId)
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Display payment history.
     */
    public function history()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payments = $parent->payments()
            ->with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('parentmodule::payments.history', compact('payments', 'parent'));
    }

    /**
     * View payment details.
     */
    public function show($paymentId)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        $payment = ParentPayment::where('id', $paymentId)
            ->where('parent_id', $parent->id)
            ->with(['student', 'course', 'enrollment'])
            ->first();

        if (!$payment) {
            return redirect()->route('parent.payment.history')
                ->with('error', 'Payment not found.');
        }

        return view('parentmodule::payments.show', compact('payment', 'parent'));
    }

    /**
     * Enroll student in course.
     */
    private function enrollStudentInCourse($student, $course, $parent, $payment = null)
    {
        $enrollment = CourseEnrolled::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'purchase_price' => $course->price ?? 0,
            'status' => 1,
            'reveune' => $course->price ?? 0,
            'lms_id' => 1
        ]);

        // Update payment with enrollment ID if payment exists
        if ($payment) {
            $payment->update(['enrollment_id' => $enrollment->id]);
        }

        // Update course enrollment count
        $course->increment('total_enrolled');

        return $enrollment;
    }

    /**
     * Get available payment methods.
     */
    private function getAvailablePaymentMethods()
    {
        // TODO: Get from payment settings
        return [
            'paypal' => 'PayPal',
            'stripe' => 'Credit/Debit Card (Stripe)',
            'bank_transfer' => 'Bank Transfer',
            'offline' => 'Offline Payment',
        ];
    }

    /**
     * Process payment through gateway.
     */
    private function processPaymentGateway($payment, $method, $child, $course)
    {
        // TODO: Integrate with actual payment gateways
        // This is a placeholder that should be replaced with actual gateway integration
        
        switch ($method) {
            case 'paypal':
                // Return PayPal redirect URL
                return null; // Implement PayPal integration
                
            case 'stripe':
                // Return Stripe checkout URL
                return null; // Implement Stripe integration
                
            case 'bank_transfer':
            case 'offline':
                // No redirect needed for offline payments
                return null;
                
            default:
                return null;
        }
    }
}

