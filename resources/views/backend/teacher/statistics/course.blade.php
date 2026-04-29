@extends('backend.master')
@section('mainContent')
    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-30">
                <div class="main-title">
                    <h3>عرض الإحصائيات - {{ $course->title }}</h3>
                </div>
            </div>

            <div class="row row-gap-24">
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['students_count']) }}</h4><p>عدد الطلاب المسجلين</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['completion_percentage'], 2)) }}%</h4><p>نسبة إكمال الكورس</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['lectures_count']) }}</h4><p>عدد المحاضرات</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['quizzes_count']) }}</h4><p>عدد الاختبارات</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['quiz_avg_score'], 2)) }}</h4><p>متوسط درجات الاختبارات</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['assignments_submitted']) }}</h4><p>عدد الواجبات المسلمة</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ getPriceFormat($metrics['total_revenue']) }}</h4><p>إجمالي إيرادات الكورس</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['rating_avg'],2)) }} ({{ translatedNumber($metrics['rating_count']) }})</h4><p>تقييمات الكورس</p></div></div>
            </div>

            <div class="row mt-30 row-gap-24">
                <div class="col-lg-6"><div class="white-box"><h4>Enrollment over time</h4>@if(count($charts['enrollment']['data']))<canvas id="enrollmentChart"></canvas>@else <p class="mt-3">لا توجد بيانات كافية لعرض الرسم البياني</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Revenue over time</h4>@if(count($charts['revenue']['data']))<canvas id="revenueChart"></canvas>@else <p class="mt-3">لا توجد بيانات كافية لعرض الرسم البياني</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Completion percentage</h4><canvas id="completionChart"></canvas></div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Quiz average score</h4><canvas id="quizAvgChart"></canvas></div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>آخر الطلاب المسجلين</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>الطالب</th><th>البريد</th><th>تاريخ التسجيل</th><th>المبلغ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentEnrollments as $enroll)
                                <tr>
                                    <td>{{ $enroll->user->name }}</td>
                                    <td>{{ $enroll->user->email }}</td>
                                    <td>{{ showDate($enroll->created_at) }}</td>
                                    <td>{{ getPriceFormat($enroll->reveune > 0 ? $enroll->reveune : $enroll->purchase_price) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">لا توجد بيانات حتى الآن</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{asset('public/backend/vendors/chartlist/Chart.min.js')}}"></script>
    <script>
        const enrollment = @json($charts['enrollment']);
        const revenue = @json($charts['revenue']);
        const completion = @json($charts['completion']);
        const quizAvg = @json($charts['quiz_avg']);

        function drawLine(id, data, label) {
            const el = document.getElementById(id); if (!el) return;
            new Chart(el, {type: 'line', data: {labels: data.labels, datasets: [{label: label, data: data.data, borderColor: '#5D78FF', backgroundColor: 'rgba(93,120,255,.12)', fill: true}]}, options: {responsive: true, maintainAspectRatio: false}});
        }
        function drawBar(id, data, label) {
            const el = document.getElementById(id); if (!el) return;
            new Chart(el, {type: 'bar', data: {labels: data.labels, datasets: [{label: label, data: data.data, backgroundColor: '#34c38f'}]}, options: {responsive: true, maintainAspectRatio: false}});
        }
        drawLine('enrollmentChart', enrollment, 'Enrollment');
        drawLine('revenueChart', revenue, 'Revenue');
        drawBar('completionChart', completion, 'Completion %');
        drawBar('quizAvgChart', quizAvg, 'Quiz Avg');
    </script>
@endpush

