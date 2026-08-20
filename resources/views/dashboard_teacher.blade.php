@extends('backend.master')

@push('styles')
    <style>
        .teacher-dashboard-landing {
            direction: rtl;
        }

        .teacher-dashboard-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f7f9fc 100%);
            border-radius: 32px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .teacher-dashboard-hero__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .teacher-dashboard-hero__eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(19, 78, 74, 0.08);
            color: #0f766e;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .teacher-dashboard-hero__header h2 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .teacher-dashboard-hero__header p {
            margin: 0;
            color: #475569;
            font-size: 15px;
            line-height: 1.9;
            max-width: 680px;
        }

        .teacher-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .teacher-dashboard-card {
            position: relative;
            min-height: 215px;
            padding: 24px;
            border-radius: 26px;
            overflow: hidden;
            color: #fff !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none !important;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.14);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .teacher-dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 46px rgba(15, 23, 42, 0.2);
        }

        .teacher-dashboard-card::before,
        .teacher-dashboard-card::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .teacher-dashboard-card::before {
            background: linear-gradient(190deg, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 44%);
        }

        .teacher-dashboard-card::after {
            top: 38%;
            height: 42%;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.24));
            transform: skewY(-4deg) scale(1.2);
            transform-origin: center;
        }

        .teacher-dashboard-card__icon,
        .teacher-dashboard-card__content,
        .teacher-dashboard-card__value {
            position: relative;
            z-index: 1;
        }

        .teacher-dashboard-card__icon {
            display: flex;
            justify-content: flex-end;
            font-size: 56px;
            line-height: 1;
        }

        .teacher-dashboard-card__content h3 {
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .teacher-dashboard-card__content p {
            color: rgba(255, 255, 255, 0.84);
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
        }

        .teacher-dashboard-card__value {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            font-size: 14px;
            font-weight: 700;
        }

        .teacher-dashboard-card--emerald {
            background: linear-gradient(180deg, #119463 0%, #1f6d6d 100%);
        }

        .teacher-dashboard-card--crimson {
            background: linear-gradient(180deg, #d52f43 0%, #96224e 100%);
        }

        .teacher-dashboard-card--olive {
            background: linear-gradient(180deg, #2ca13d 0%, #45623d 100%);
        }

        .teacher-dashboard-card--violet {
            background: linear-gradient(180deg, #7f84c6 0%, #6654a0 100%);
        }

        .teacher-dashboard-card--fuchsia {
            background: linear-gradient(180deg, #d10687 0%, #90106b 100%);
        }

        .teacher-dashboard-card--orange {
            background: linear-gradient(180deg, #ff7308 0%, #b65028 100%);
        }

        .teacher-dashboard-card--teal {
            background: linear-gradient(180deg, #2ba8c1 0%, #315f8e 100%);
        }

        .teacher-dashboard-card--indigo {
            background: linear-gradient(180deg, #3550da 0%, #3b2f8d 100%);
        }

        .teacher-summary-card {
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        }

        .teacher-summary-card .teacher-summary-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .teacher-summary-card .teacher-summary-card__head h4 {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 800;
        }

        .teacher-summary-list {
            display: grid;
            gap: 12px;
        }

        .teacher-summary-list__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-radius: 16px;
            background: #f8fafc;
            padding: 14px 16px;
            color: #334155;
            font-size: 15px;
        }

        .teacher-summary-list__row strong {
            color: #0f172a;
            font-size: 15px;
            font-weight: 800;
        }

        @media (max-width: 1399px) {
            .teacher-dashboard-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 991px) {
            .teacher-dashboard-hero {
                padding: 22px;
                border-radius: 24px;
            }

            .teacher-dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .teacher-dashboard-card {
                min-height: 200px;
            }
        }

        @media (max-width: 575px) {
            .teacher-dashboard-hero__header h2 {
                font-size: 24px;
            }

            .teacher-dashboard-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .teacher-dashboard-card {
                min-height: 180px;
                padding: 20px;
                border-radius: 22px;
            }

            .teacher-dashboard-card__icon {
                font-size: 46px;
            }

            .teacher-dashboard-card__content h3 {
                font-size: 24px;
            }
        }
    </style>
@endpush

@section('mainContent')
    @php
        $teacherShortcutCards = collect([
            [
                'title' => 'كورساتي',
                'description' => 'عرض وتحديث جميع الكورسات الخاصة بك.',
                'value' => translatedNumber($teacherDashboard['my_courses'] ?? 0) . ' كورس',
                'url' => \Illuminate\Support\Facades\Route::has('getAllCourse') && permissionCheck('getAllCourse') ? route('getAllCourse') : null,
                'icon' => 'fas fa-book-open',
                'variant' => 'emerald',
            ],
            [
                'title' => 'إنشاء كورس',
                'description' => 'ابدأ كورس جديد وأضف المحتوى من لوحة المعلم.',
                'value' => translatedNumber($teacherDashboard['draft_courses'] ?? 0) . ' مسودة',
                'url' => \Illuminate\Support\Facades\Route::has('course.store') && permissionCheck('course.store') ? route('course.store') : null,
                'icon' => 'fas fa-plus-circle',
                'variant' => 'crimson',
            ],
            [
                'title' => 'بنك الأسئلة',
                'description' => 'إدارة مجموعات الأسئلة الخاصة بك بسهولة.',
                'value' => translatedNumber($teacherDashboard['question_groups'] ?? 0) . ' مجموعة',
                'url' => \Illuminate\Support\Facades\Route::has('teacher.question-banks.index') ? route('teacher.question-banks.index') : null,
                'icon' => 'fas fa-file-signature',
                'variant' => 'olive',
            ],
            [
                'title' => 'إنشاء بنك أسئلة',
                'description' => 'أضف مجموعة جديدة ورتب أسئلتك بسرعة.',
                'value' => translatedNumber($teacherDashboard['questions_count'] ?? 0) . ' سؤال',
                'url' => \Illuminate\Support\Facades\Route::has('teacher.question-banks.create') ? route('teacher.question-banks.create') : null,
                'icon' => 'fas fa-project-diagram',
                'variant' => 'violet',
            ],
            [
                'title' => 'الإحصائيات العامة',
                'description' => 'تابع التسجيلات والمبيعات والأداء العام.',
                'value' => translatedNumber($teacherDashboard['enrolled_students'] ?? 0) . ' طالب',
                'url' => \Illuminate\Support\Facades\Route::has('teacher.statistics.index') ? route('teacher.statistics.index') : null,
                'icon' => 'fas fa-chalkboard-teacher',
                'variant' => 'fuchsia',
            ],
            [
                'title' => 'تقارير الكورسات',
                'description' => 'افتح تقارير الكورسات والتحليلات التفصيلية.',
                'value' => translatedNumber($teacherDashboard['pending_review_courses'] ?? 0) . ' قيد المراجعة',
                'url' => \Illuminate\Support\Facades\Route::has('teacher.statistics.courses') ? route('teacher.statistics.courses') : null,
                'icon' => 'fas fa-clipboard-check',
                'variant' => 'orange',
            ],
            [
                'title' => 'ملخص الأرباح',
                'description' => 'راجع الإيرادات والمبيعات وطلبات السحب بسرعة.',
                'value' => getPriceFormat($teacherDashboard['total_revenue'] ?? 0, false),
                'url' => '#teacher-finance-summary',
                'icon' => 'fas fa-chart-line',
                'variant' => 'teal',
            ],
            [
                'title' => 'آخر التسجيلات',
                'description' => 'شاهد أحدث الطلاب المسجلين في كورساتك.',
                'value' => translatedNumber($recentEnroll->count()) . ' تسجيل',
                'url' => '#teacher-recent-enrollments',
                'icon' => 'fas fa-desktop',
                'variant' => 'indigo',
            ],
        ])->filter(function ($card) {
            return !blank($card['url']);
        })->values();
    @endphp

    <section class="sms-breadcrumb mb-10 white-box">
        <div class="container-fluid p-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h1 class="text-uppercase mb-0">{{ __('common.Dashboard') }}</h1>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0 teacher-dashboard-landing">
        @if($teacherShortcutCards->count())
            <div class="teacher-dashboard-hero">
                <div class="teacher-dashboard-hero__header">
                    <div>
                        <span class="teacher-dashboard-hero__eyebrow">لوحة المعلم</span>
                        <h2>وصول سريع لأهم أقسام الحساب</h2>
                        <p>تم ترتيب الصفحة الرئيسية على شكل بطاقات كبيرة مثل التصميم المرجعي، حتى يصل المعلم إلى أهم الأدوات من أول شاشة داخل لوحة التحكم.</p>
                    </div>
                </div>

                <div class="teacher-dashboard-grid">
                    @foreach($teacherShortcutCards as $card)
                        <a href="{{ $card['url'] }}" class="teacher-dashboard-card teacher-dashboard-card--{{ $card['variant'] }}">
                            <div class="teacher-dashboard-card__icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>

                            <div class="teacher-dashboard-card__content">
                                <h3>{{ $card['title'] }}</h3>
                                <p>{{ $card['description'] }}</p>
                            </div>

                            <div class="teacher-dashboard-card__value">
                                {{ $card['value'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="row row-gap-4 mt-3">
            <div class="col-lg-6" id="teacher-finance-summary">
                <div class="white_box teacher-summary-card h-100">
                    <div class="teacher-summary-card__head">
                        <h4>ملخص الأرباح</h4>
                        <a href="{{ route('teacher.statement.export') }}" class="primary-btn small fix-gr-bg text-nowrap">
                            تحميل كشف الحساب Excel
                        </a>
                    </div>

                    <div class="teacher-summary-list">
                        <div class="teacher-summary-list__row">
                            <span>إجمالي المبيعات</span>
                            <strong>{{ getPriceFormat($teacherDashboard['total_sales'] ?? 0, false) }}</strong>
                        </div>
                        <div class="teacher-summary-list__row">
                            <span>إجمالي الإيرادات</span>
                            <strong>{{ getPriceFormat($teacherDashboard['total_revenue'] ?? 0, false) }}</strong>
                        </div>
                        <div class="teacher-summary-list__row">
                            <span>طلبات سحب قيد الانتظار</span>
                            <strong>{{ translatedNumber($teacherDashboard['pending_withdrawals'] ?? 0) }}</strong>
                        </div>
                        <div class="teacher-summary-list__row">
                            <span>طلبات سحب معتمدة</span>
                            <strong>{{ translatedNumber($teacherDashboard['approved_withdrawals'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="white_box teacher-summary-card h-100">
                    <div class="teacher-summary-card__head">
                        <h4>ملخص المحتوى</h4>
                    </div>

                    <div class="teacher-summary-list">
                        <div class="teacher-summary-list__row">
                            <span>إجمالي الكورسات</span>
                            <strong>{{ translatedNumber($teacherDashboard['my_courses'] ?? 0) }}</strong>
                        </div>
                        <div class="teacher-summary-list__row">
                            <span>كورسات بانتظار المراجعة</span>
                            <strong>{{ translatedNumber($teacherDashboard['pending_review_courses'] ?? 0) }}</strong>
                        </div>
                        <div class="teacher-summary-list__row">
                            <span>الكورسات المسودة</span>
                            <strong>{{ translatedNumber($teacherDashboard['draft_courses'] ?? 0) }}</strong>
                        </div>
                        <div class="teacher-summary-list__row">
                            <span>الطلاب المسجلون</span>
                            <strong>{{ translatedNumber($teacherDashboard['enrolled_students'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-gap-4 mt-3" id="teacher-recent-enrollments">
            <div class="col-lg-12">
                <div class="white_box chart_box">
                    <div class="white_box_tittle list_header">
                        <h4>آخر التسجيلات</h4>
                    </div>
                    <div class="QA_table mb_30">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>{{ __('common.SL') }}</th>
                                <th>الطالب</th>
                                <th>الكورس</th>
                                <th>السعر</th>
                                <th>الإيراد</th>
                                <th>{{ __('common.Date') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentEnroll as $enroll)
                                <tr>
                                    <td>{{ translatedNumber($loop->iteration) }}</td>
                                    <td>{{ $enroll->user->name ?? '-' }}</td>
                                    <td>{{ $enroll->course->title ?? '-' }}</td>
                                    <td>{{ getPriceFormat((float) ($enroll->purchase_price ?? 0), false) }}</td>
                                    <td>{{ getPriceFormat((float) ($enroll->reveune ?? 0), false) }}</td>
                                    <td>{{ showDate($enroll->created_at) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('common.No data available in the table') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
