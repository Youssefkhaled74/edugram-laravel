@extends('backend.master')

@section('mainContent')
    <section class="sms-breadcrumb mb-10 white-box">
        <div class="container-fluid p-0">
            <div class="d-flex flex-wrap justify-content-between">
                <h1 class="text-uppercase">{{ __('common.Dashboard') }}</h1>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0">
        <div class="row row-gap-4 justify-content-center mt-0">
            <div class="col-md-6 col-lg-4 col-xl-3">
                <a href="{{ route('getAllCourse') }}" class="d-block h-100">
                    <div class="white-box single-summery h-100">
                        <div class="d-flex justify-content-between gap-20">
                            <div>
                                <h3>كورساتي</h3>
                                <p class="mb-0">كل الكورسات</p>
                            </div>
                            <h1 class="gradient-color2">{{ translatedNumber($teacherDashboard['my_courses'] ?? 0) }}</h1>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="white-box single-summery h-100">
                    <div class="d-flex justify-content-between gap-20">
                        <div>
                            <h3>بانتظار الموافقة</h3>
                            <p class="mb-0">كورسات قيد المراجعة</p>
                        </div>
                        <h1 class="gradient-color2">{{ translatedNumber($teacherDashboard['pending_review_courses'] ?? 0) }}</h1>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="white-box single-summery h-100">
                    <div class="d-flex justify-content-between gap-20">
                        <div>
                            <h3>المسودات</h3>
                            <p class="mb-0">كورسات غير منشورة</p>
                        </div>
                        <h1 class="gradient-color2">{{ translatedNumber($teacherDashboard['draft_courses'] ?? 0) }}</h1>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="white-box single-summery h-100">
                    <div class="d-flex justify-content-between gap-20">
                        <div>
                            <h3>الطلاب المسجلون</h3>
                            <p class="mb-0">إجمالي التسجيلات</p>
                        </div>
                        <h1 class="gradient-color2">{{ translatedNumber($teacherDashboard['enrolled_students'] ?? 0) }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-gap-4 mt-3">
            <div class="col-lg-6">
                <div class="white_box chart_box h-100">
                    <div class="white_box_tittle list_header d-flex justify-content-between align-items-center">
                        <h4>ملخص الإيرادات</h4>
                        <a href="{{ route('teacher.statement.export') }}" class="primary-btn small fix-gr-bg text-nowrap">
                            تحميل كشف الحساب Excel
                        </a>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>إجمالي المبيعات</span>
                        <strong>{{ getPriceFormat($teacherDashboard['total_sales'] ?? 0, false) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>إجمالي الإيرادات</span>
                        <strong>{{ getPriceFormat($teacherDashboard['total_revenue'] ?? 0, false) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>طلبات سحب قيد الانتظار</span>
                        <strong>{{ translatedNumber($teacherDashboard['pending_withdrawals'] ?? 0) }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="white_box chart_box h-100">
                    <div class="white_box_tittle list_header">
                        <h4>ملخص التقارير</h4>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>التسجيلات الأخيرة</span>
                        <strong>{{ translatedNumber($recentEnroll->count()) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>طلبات السحب المعتمدة</span>
                        <strong>{{ translatedNumber($teacherDashboard['approved_withdrawals'] ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>كورسات قيد المراجعة</span>
                        <strong>{{ translatedNumber($teacherDashboard['pending_review_courses'] ?? 0) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-gap-4 mt-3">
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

