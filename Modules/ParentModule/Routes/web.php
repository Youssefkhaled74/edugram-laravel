<?php

use Illuminate\Support\Facades\Route;
use Modules\ParentModule\Http\Controllers\ParentAuthController;
use Modules\ParentModule\Http\Controllers\ParentDashboardController;
use Modules\ParentModule\Http\Controllers\ParentChildController;
use Modules\ParentModule\Http\Controllers\ParentCourseController;
use Modules\ParentModule\Http\Controllers\ParentReportController;
use Modules\ParentModule\Http\Controllers\ParentPaymentController;

/*
|--------------------------------------------------------------------------
| Parent Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the parent module.
| All routes are prefixed with 'parent' and use the parent middleware.
|
*/
require __DIR__ . '/impersonation.php';
// Parent Authentication Routes (Guest only)
Route::group(['prefix' => 'parent', 'as' => 'parent.'], function () {
    
    // Registration
    Route::get('register', [ParentAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [ParentAuthController::class, 'register'])->name('register.submit');
    
    // Login
    Route::get('login', [ParentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [ParentAuthController::class, 'login'])->name('login.submit');
    
    // Forgot Password
    Route::get('forgot-password', [ParentAuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('forgot-password', [ParentAuthController::class, 'forgotPassword'])->name('forgot-password.submit');
    
});

// Parent Authenticated Routes
Route::group([
    'prefix' => 'parent',
    'as' => 'parent.',
    'middleware' => ['auth', 'parent']
], function () {
    
    // Logout
    Route::post('logout', [ParentAuthController::class, 'logout'])->name('logout');
    Route::get('logout', [ParentAuthController::class, 'logout'])->name('logout.get');
    
    // Dashboard
    Route::get('dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('profile', [ParentDashboardController::class, 'profile'])->name('profile');
    Route::post('profile', [ParentDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Change Password
    Route::get('change-password', [ParentAuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('change-password', [ParentAuthController::class, 'changePassword'])->name('change-password.submit');
    
    // Notifications
    Route::get('notifications', [ParentDashboardController::class, 'notifications'])->name('notifications');
    Route::post('notifications/{id}/read', [ParentDashboardController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('notifications/read-all', [ParentDashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    
    // Children Management
    Route::group(['prefix' => 'children', 'as' => 'children.'], function () {
        Route::get('/', [ParentChildController::class, 'index'])->name('index');
        Route::get('create', [ParentChildController::class, 'create'])->name('create');
        Route::post('store', [ParentChildController::class, 'store'])->name('store');
        Route::get('{id}', [ParentChildController::class, 'show'])->name('show');
        Route::get('{id}/edit', [ParentChildController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [ParentChildController::class, 'update'])->name('update');
        Route::delete('{id}', [ParentChildController::class, 'destroy'])->name('destroy');
        Route::post('request/{id}/cancel', [ParentChildController::class, 'cancelRequest'])->name('request.cancel');
    });
    
    // Courses
    Route::group(['prefix' => 'courses', 'as' => 'courses.'], function () {
        Route::get('/', [ParentCourseController::class, 'index'])->name('index');
        Route::get('search', [ParentCourseController::class, 'search'])->name('search');
        Route::get('child/{childId}', [ParentCourseController::class, 'childCourses'])->name('child');
        Route::get('/{courseId}', [ParentCourseController::class, 'show'])->name('show');
        Route::get('child/{childId}/available', [ParentCourseController::class, 'availableCourses'])->name('available');
		//Route::get('/{courseId}', [ParentCourseController::class, 'show'])->name('parent.courses.show');
		Route::post('enroll', [ParentCourseController::class, 'enroll'])->name('enroll');

    });
    
    // Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/', [ParentReportController::class, 'index'])->name('index');
        Route::get('child/{childId}', [ParentReportController::class, 'childReports'])->name('child');
        Route::get('child/{childId}/quiz-results', [ParentReportController::class, 'quizResults'])->name('quiz-results');
        Route::get('child/{childId}/quiz-results/{resultId}', [ParentReportController::class, 'quizResultDetail'])->name('quiz-result-detail');
        Route::get('child/{childId}/certificates', [ParentReportController::class, 'certificates'])->name('certificates');
        Route::get('child/{childId}/certificates/{certificateId}/download', [ParentReportController::class, 'downloadCertificate'])->name('certificate.download');
        Route::get('child/{childId}/export', [ParentReportController::class, 'exportReport'])->name('export');
    });
    
    // Payments
    Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
        Route::get('history', [ParentPaymentController::class, 'history'])->name('history');
        Route::get('{paymentId}', [ParentPaymentController::class, 'show'])->name('show');
        Route::get('enroll/{childId}/{courseId}', [ParentPaymentController::class, 'enrollCourse'])->name('enroll');
        Route::post('enroll/{childId}/{courseId}', [ParentPaymentController::class, 'processEnrollment'])->name('enroll.process');
        Route::get('pending/{paymentId}', [ParentPaymentController::class, 'pendingPayment'])->name('pending');
        Route::get('success/{paymentId}', [ParentPaymentController::class, 'paymentSuccess'])->name('success');
        Route::get('failed/{paymentId}', [ParentPaymentController::class, 'paymentFailed'])->name('failed');
    });
    
    // Payment History (alternative route)
    Route::get('payment-history', [ParentDashboardController::class, 'paymentHistory'])->name('payment-history');
    Route::get('payment-history/{id}/invoice', [ParentDashboardController::class, 'downloadInvoice'])->name('payment.invoice');
    
});

