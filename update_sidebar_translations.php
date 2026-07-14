<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\RolePermission\Entities\Permission;
use Modules\SidebarManager\Entities\PermissionSection;

$updates = [
    // ====== قسم المستخدمين ======
    'student.student_list' => ['ar' => 'قائمة الطلاب'],
    'student.student_import' => ['ar' => 'رفع قائمة طلاب'],
    'admin.instructor.payout' => ['ar' => 'مستحقات المدرسين المالية'],
    'students' => ['ar' => 'إدارة الطلاب'],
    'student.setting' => ['ar' => 'إعدادات تسجيل الطلاب'],
    'department' => ['ar' => 'الأقسام الإدارية'],
    'role' => ['ar' => 'الأدوار والصلاحيات'],
    'delete_request' => ['ar' => 'طلبات حذف الحسابات'],

    // ====== قسم التعليم ======
    'course.category' => ['ar' => 'المراحل الدراسية'],
    'getAllCourse' => ['ar' => 'جميع المواد والدورات'],
    'course.level' => ['ar' => 'الصفوف الدراسية'],
    'question-group' => ['ar' => 'تصنيفات الأسئلة'],
    'question-bank-list' => ['ar' => 'مخزن الأسئلة'],
    'question.import' => ['ar' => 'رفع أسئلة من ملف'],
    'quiz.report' => ['ar' => 'نتائج الاختبارات'],
    'virtual-class.index' => ['ar' => 'الحصص المباشرة'],
    'virtual-class.class_list' => ['ar' => 'قائمة الحصص المباشرة'],

    // ====== المدفوعات ======
    'coupons.manage' => ['ar' => 'أكواد الخصم'],
    'coupons.common' => ['ar' => 'خصومات عامة'],
    'coupons.single' => ['ar' => 'خصومات خاصة'],
    'coupons.invite' => ['ar' => 'أكواد دعوة الأصدقاء'],
    'coupons.referral' => ['ar' => 'برنامج دعوة صديق'],
    'payment.received_online' => ['ar' => 'المدفوعات الإلكترونية'],
    'offlinePayment' => ['ar' => 'الدفع النقدي/الكاش'],
    'offlinePayment.pending' => ['ar' => 'مدفوعات تنتظر التأكيد'],

    // ====== المحتوى والواجهة ======
    'frontend_CMS' => ['ar' => 'إدارة المحتوى'],
    'frontend.slider' => ['ar' => 'البانر الرئيسي'],
    'appearance' => ['ar' => 'شكل الموقع'],
    'appearance.theme_color' => ['ar' => 'ألوان الموقع'],
    'appearance.themes-font.index' => ['ar' => 'خطوط الموقع'],
    'gamification' => ['ar' => 'نظام النقاط والشارات'],
    'page-builder' => ['ar' => 'منشئ الصفحات'],
    'sidebar-manager' => ['ar' => 'قوائم التنقل'],
    'blog.post.review' => ['ar' => 'تقييمات الطلاب للدروس'],

    // ====== الرسائل والتواصل ======
    'communications' => ['ar' => 'الرسائل الخاصة'],
    'blog.comment' => ['ar' => 'تعليقات الدروس'],
    'qa' => ['ar' => 'أسئلة واستفسارات الطلاب'],

    // ====== الإعدادات ======
    'settings' => ['ar' => 'الإعدادات'],
    'setting.email_setup' => ['ar' => 'إعدادات الإيميل'],
    'EmailTemp' => ['ar' => 'رسائل البريد التلقائية'],
    'setting.api_setting' => ['ar' => 'إعدادات واجهة API'],
    'setting.seo_setting' => ['ar' => 'إعدادات الظهور في جوجل'],
    'setting.cookie_setting' => ['ar' => 'إعدادات الخصوصية'],
    'pusher.setting' => ['ar' => 'إعدادات Pusher'],
    'setting.instructor_setup' => ['ar' => 'إعدادات المدرسين'],
    'setting.commission' => ['ar' => 'نسبة أرباح المنصة'],

    // ====== أقسام إضافية ======
    'certificate.index' => ['ar' => 'طلبات الشهادات المطبوعة'],
    'image_gallery' => ['ar' => 'مكتبة الملفات والصور'],
    'image_gallery.upload' => ['ar' => 'رفع ملف جديد'],
    'chat' => ['ar' => 'المحادثات'],
    'chat.invitation' => ['ar' => 'طلبات المحادثة'],
    'chat.blocked_user' => ['ar' => 'المحظورين من الدردشة'],
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
