<?php

return [
    'name' => 'ParentModule',
    'version' => '1.0.0',
    'description' => 'Parent Module for managing children education, courses, payments and reports',
    
    // Module settings
    'settings' => [
        'auto_approve_requests' => false, // Auto approve parent-child link requests
        'require_verification' => true, // Require parent verification
        'allow_multiple_parents' => true, // Allow multiple parents per child
        'notification_channels' => ['email', 'sms', 'whatsapp'], // Available notification channels
    ],
    
    // Permission settings
    'default_permissions' => [
        'can_make_payments' => true,
        'can_view_grades' => true,
        'can_view_attendance' => true,
        'can_communicate_teachers' => true,
        'can_enroll_courses' => true,
    ],
    
    // Payment settings
    'payment' => [
        'currency' => 'USD',
        'invoice_prefix' => 'INV-PAR-',
        'auto_generate_invoice' => true,
    ],
    
    // Notification settings
    'notifications' => [
        'send_enrollment_notification' => true,
        'send_payment_notification' => true,
        'send_grade_notification' => true,
        'send_certificate_notification' => true,
        'send_quiz_result_notification' => true,
    ],
    
    // Report settings
    'reports' => [
        'activity_timeline_days' => 30,
        'enable_pdf_export' => true,
    ],
];

