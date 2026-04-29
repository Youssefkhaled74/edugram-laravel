@extends('backend.master')
@push('styles')
    <style>
        .stats-card h3 {
            font-size: 22px;
            margin-bottom: 4px;
        }
        .stats-card p {
            margin: 0;
            color: #6c7293;
        }
    </style>
@endpush
@section('mainContent')
    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white_box mb_30">
                <form method="GET" action="{{ route('teacher.statistics.index') }}">
                    <div class="row">
                        <div class="col-lg-4 mt-20">
                            <label class="primary_input_label">إحصائيات الكورسات</label>
                            <select class="primary_select" name="course_id">
                                <option value="">كل الكورسات</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (int)$selectedCourseId === (int)$course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 mt-40">
                            <button type="submit" class="primary-btn fix-gr-bg">عرض</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row row-gap-24">
                <div class="col-md-6 col-xl-2">
                    <div class="white-box stats-card">
                        <h3>{{ translatedNumber($summary['total_courses']) }}</h3>
                        <p>إجمالي الكورسات</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="white-box stats-card">
                        <h3>{{ translatedNumber($summary['total_enrolled_students']) }}</h3>
                        <p>عدد الطلاب المسجلين</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="white-box stats-card">
                        <h3>{{ getPriceFormat($summary['total_revenue']) }}</h3>
                        <p>إجمالي الإيرادات</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="white-box stats-card">
                        <h3>{{ translatedNumber(number_format($summary['avg_completion'], 2)) }}%</h3>
                        <p>متوسط إكمال الكورسات</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="white-box stats-card">
                        <h3>{{ translatedNumber(number_format($summary['avg_quiz_score'], 2)) }}</h3>
                        <p>متوسط درجات الاختبارات</p>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="white-box stats-card">
                        <h3>{{ translatedNumber($summary['assignments_submitted']) }}</h3>
                        <p>عدد الواجبات المسلمة</p>
                    </div>
                </div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20">
                    <h3>إحصائيات الكورسات</h3>
                </div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>اسم الكورس</th>
                                <th>عدد الطلاب</th>
                                <th>نسبة الإكمال</th>
                                <th>عدد المحاضرات</th>
                                <th>عدد الاختبارات</th>
                                <th>متوسط درجات الاختبارات</th>
                                <th>الواجبات المسلمة</th>
                                <th>إجمالي الإيرادات</th>
                                <th>التقييم</th>
                                <th>إجراء</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($courseRows as $row)
                                <tr>
                                    <td>{{ $row['course']->title }}</td>
                                    <td>{{ translatedNumber($row['students_count']) }}</td>
                                    <td>{{ translatedNumber(number_format($row['completion_percentage'], 2)) }}%</td>
                                    <td>{{ translatedNumber($row['lectures_count']) }}</td>
                                    <td>{{ translatedNumber($row['quizzes_count']) }}</td>
                                    <td>{{ translatedNumber(number_format($row['quiz_avg_score'], 2)) }}</td>
                                    <td>{{ translatedNumber($row['assignments_submitted']) }}</td>
                                    <td>{{ getPriceFormat($row['total_revenue']) }}</td>
                                    <td>{{ translatedNumber(number_format($row['rating_avg'], 2)) }} ({{ translatedNumber($row['rating_count']) }})</td>
                                    <td>
                                        <a class="primary-btn small fix-gr-bg" href="{{ route('teacher.courses.analytics', $row['course']->id) }}">
                                            عرض الإحصائيات
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">لا توجد بيانات حتى الآن</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-30 row-gap-24">
                <div class="col-lg-6">
                    <div class="white-box">
                        <h4>Enrollment over time</h4>
                        @if(count($charts['enrollment']['data']))
                            <canvas id="enrollmentChart"></canvas>
                        @else
                            <p class="mt-3">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="white-box">
                        <h4>Revenue over time</h4>
                        @if(count($charts['revenue']['data']))
                            <canvas id="revenueChart"></canvas>
                        @else
                            <p class="mt-3">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="white-box">
                        <h4>Completion percentage</h4>
                        @if(count($charts['completion']['data']))
                            <canvas id="completionChart"></canvas>
                        @else
                            <p class="mt-3">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="white-box">
                        <h4>Quiz average score</h4>
                        @if(count($charts['quiz_avg']['data']))
                            <canvas id="quizAvgChart"></canvas>
                        @else
                            <p class="mt-3">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20">
                    <h3>آخر الطلاب المسجلين</h3>
                </div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>الطالب</th>
                                <th>البريد</th>
                                <th>الكورس</th>
                                <th>تاريخ التسجيل</th>
                                <th>المبلغ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentEnrollments as $enroll)
                                <tr>
                                    <td>{{ $enroll->user->name }}</td>
                                    <td>{{ $enroll->user->email }}</td>
                                    <td>{{ $enroll->course->title }}</td>
                                    <td>{{ showDate($enroll->created_at) }}</td>
                                    <td>{{ getPriceFormat($enroll->reveune > 0 ? $enroll->reveune : $enroll->purchase_price) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد بيانات حتى الآن</td>
                                </tr>
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
            const el = document.getElementById(id);
            if (!el) return;
            new Chart(el, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{label: label, data: data.data, borderColor: '#5D78FF', backgroundColor: 'rgba(93,120,255,.12)', fill: true}]
                },
                options: {responsive: true, maintainAspectRatio: false}
            });
        }

        function drawBar(id, data, label) {
            const el = document.getElementById(id);
            if (!el) return;
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{label: label, data: data.data, backgroundColor: '#34c38f'}]
                },
                options: {responsive: true, maintainAspectRatio: false}
            });
        }

        drawLine('enrollmentChart', enrollment, 'Enrollment');
        drawLine('revenueChart', revenue, 'Revenue');
        drawBar('completionChart', completion, 'Completion %');
        drawBar('quizAvgChart', quizAvg, 'Quiz Avg');
    </script>
@endpush

