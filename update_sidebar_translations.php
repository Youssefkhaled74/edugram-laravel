<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\RolePermission\Entities\Permission;
use Modules\SidebarManager\Entities\PermissionSection;

$updates = [
    // ====== قسم المستخدمين ======
    'student.student_list' => ['ar' => 'رفع قائمة طلاب'],
    'students' => ['ar' => 'إدارة المستخدمين'],
    'student.student_import' => ['ar' => 'رفع قائمة طلاب'],
    'admin.instructor.payout' => ['ar' => 'مستحقات المدرسين المالية'],
    'students' => ['ar' => 'إدارة الطلاب'],

    // ====== قسم التعليم ======
    'course.category' => ['ar' => 'المراحل الدراسية'],
    'getAllCourse' => ['ar' => 'جميع المواد والدورات'],
    'course.level' => ['ar' => 'الصفوف الدراسية'],
    'question-group' => ['ar' => 'تصنيفات الأسئلة'],
    'quiz.report' => ['ar' => 'نتائج الاختبارات'],
    'virtual-class.index' => ['ar' => 'الحصص المباشرة (Live)'],

    // ====== المدفوعات ======
    'coupons.manage' => ['ar' => 'أكواد الخصم'],
    'coupons.common' => ['ar' => 'خصومات عامة'],
    'coupons.single' => ['ar' => 'خصومات خاصة'],
    'coupons.invite' => ['ar' => 'برنامج دعوة صديق'],
    'payment.received_online' => ['ar' => 'المدفوعات الإلكترونية'],
    'offlinePayment' => ['ar' => 'الدفع النقدي/الكاش'],

    // ====== المحتوى والواجهة ======
    'frontend_CMS' => ['ar' => 'إدارة المحتوى'],
    'appearance' => ['ar' => 'شكل الموقع'],
    'appearance.themes-font.index' => ['ar' => 'خطوط الموقع'],
    'gamification' => ['ar' => 'نظام النقاط والشارات'],
    'page-builder' => ['ar' => 'منشئ الصفحات'],
    'sidebar-manager' => ['ar' => 'قوائم التنقل'],

    // ====== الرسائل والتواصل ======
    'communications' => ['ar' => 'الرسائل الخاصة'],
    'qa' => ['ar' => 'أسئلة واستفسارات الطلاب'],

    // ====== الإعدادات ======
    'settings' => ['ar' => 'الإعدادات'],
    'setting.email_setup' => ['ar' => 'إعدادات الإيميل'],
    'EmailTemp' => ['ar' => 'رسائل البريد التلقائية'],
    'setting.api_setting' => ['ar' => 'إعدادات واجهة API'],
    'setting.seo_setting' => ['ar' => 'إعدادات الظهور في جوجل'],
    'pusher.setting' => ['ar' => 'إعدادات الدفع (Pusher)'],
    'setting.instructor_setup' => ['ar' => 'إعدادات المدرسين'],
    'setting.commission' => ['ar' => 'نسبة أرباح المنصة'],

    // ====== أقسام إضافية ======
    'certificate.index' => ['ar' => 'طلبات الشهادات المطبوعة'],
    'image_gallery' => ['ar' => 'مكتبة الملفات والصور'],
    'chat' => ['ar' => 'المحادثات'],
    'chat.invitation' => ['ar' => 'طلبات المحادثة'],
    'chat.blocked_user' => ['ar' => 'المحظورين من الدردشة'],
    'offlinePayment.pending' => ['ar' => 'مدفوعات تنتظر التأكيد'],
];

$count = 0;
foreach ($updates as $route => $translations) {
    $perm = Permission::where('route', $route)->first();
    if ($perm) {
        $name = $perm->getTranslation('name', 'en') ?? $perm->name;
        foreach ($translations as $locale => $trans) {
            $perm->setTranslation('name', $locale, $trans);
        }
        $perm->save();
        echo "✅ Permission [{$route}]: {$name} → {$translations['ar']}\n";
        $count++;
    } else {
        echo "❌ Permission [{$route}]: Not found\n";
    }
}

// Update section names
$sectionUpdates = [
    1 => ['ar' => 'الرئيسية'],  // Dashboard
    2 => ['ar' => 'المستخدمين'],
    3 => ['ar' => 'التعليم'],
    4 => ['ar' => 'المدفوعات'],
    5 => ['ar' => 'المحتوى والواجهة'],
    6 => ['ar' => 'التواصل'],
    7 => ['ar' => 'الإعدادات'],
];

foreach ($sectionUpdates as $sectionId => $translations) {
    $section = PermissionSection::find($sectionId);
    if ($section) {
        foreach ($translations as $locale => $trans) {
            $section->setTranslation('name', $locale, $trans);
        }
        $section->save();
        echo "✅ Section [{$sectionId}]: {$translations['ar']}\n";
        $count++;
    }
}

echo "\n🎯 Total updates: {$count}\n";
