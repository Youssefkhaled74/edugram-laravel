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
        .chart-wrap {
            position: relative;
            height: 320px;
            width: 100%;
        }
        .chart-wrap canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
    </style>
@endpush
@section('mainContent')
    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white_box mb_30">
                <form method="GET" action="{{ route('teacher.statistics.courses') }}">
                    <div class="row">
                        <div class="col-lg-4 mt-20">
                            <label class="primary_input_label">Ø§Ø­ØµØ§Ø¦ÙŠØ§Øª Ø§Ù„ÙƒÙˆØ±Ø³Ø§Øª</label>
                            <select class="primary_select" name="course_id">
                                <option value="">ÙƒÙ„ Ø§Ù„ÙƒÙˆØ±Ø³Ø§Øª</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (int)$selectedCourseId === (int)$course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 mt-40">
                            <button type="submit" class="primary-btn fix-gr-bg">Ø¹Ø±Ø¶</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row row-gap-24">
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['total_courses']) }}</h3><p>Ø§Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„ÙƒÙˆØ±Ø³Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['total_enrolled_students']) }}</h3><p>Ø¹Ø¯Ø¯ Ø§Ù„ØªØ³Ø¬ÙŠÙ„Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['unique_students_count']) }}</h3><p>Ø§Ù„Ø·Ù„Ø§Ø¨ Ø§Ù„ÙØ±ÙŠØ¯ÙŠÙ†</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ getPriceFormat($summary['total_revenue']) }}</h3><p>Ø§Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ø§ÙŠØ±Ø§Ø¯Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ getPriceFormat($summary['total_sales']) }}</h3><p>Ø§Ø¬Ù…Ø§Ù„ÙŠ Ø§Ù„Ù…Ø¨ÙŠØ¹Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ getPriceFormat($summary['avg_order_value']) }}</h3><p>Ù…ØªÙˆØ³Ø· Ù‚ÙŠÙ…Ø© Ø§Ù„Ø´Ø±Ø§Ø¡</p></div></div>
            </div>
            <div class="row row-gap-24 mt-2">
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['paid_enrollments']) }} / {{ translatedNumber($summary['free_enrollments']) }}</h3><p>Ù…Ø¯ÙÙˆØ¹ / Ù…Ø¬Ø§Ù†ÙŠ</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['active_students']) }} / {{ translatedNumber($summary['completed_students']) }}</h3><p>Ù†Ø´Ø· / Ù…ÙƒØªÙ…Ù„</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber(number_format($summary['avg_completion'], 2)) }}%</h3><p>Ù…ØªÙˆØ³Ø· Ø§ÙƒÙ…Ø§Ù„ Ø§Ù„ÙƒÙˆØ±Ø³Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber(number_format($summary['completion_students_rate'], 2)) }}%</h3><p>Ù†Ø³Ø¨Ø© Ø§Ù„Ø·Ù„Ø§Ø¨ Ø§Ù„Ù…ÙƒØªÙ…Ù„ÙŠÙ†</p></div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>Ø§Ø­ØµØ§Ø¦ÙŠØ§Øª ÙƒÙ„ ÙƒÙˆØ±Ø³</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>Ø§Ø³Ù… Ø§Ù„ÙƒÙˆØ±Ø³</th>
                                <th>Ø§Ù„Ø·Ù„Ø§Ø¨</th>
                                <th>Ø§Ù„Ø§ÙƒÙ…Ø§Ù„ %</th>
                                <th>Ø§Ù„Ù…Ø­Ø§Ø¶Ø±Ø§Øª</th>
                                <th>Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª</th>
                                <th>Ù…ØªÙˆØ³Ø· Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª</th>
                                <th>Ø§Ù„ÙˆØ§Ø¬Ø¨Ø§Øª</th>
                                <th>Ø§Ù„Ø§ÙŠØ±Ø§Ø¯</th>
                                <th>Ø§Ù„Ù…Ø¨ÙŠØ¹Ø§Øª</th>
                                <th>Ù…Ø¯ÙÙˆØ¹/Ù…Ø¬Ø§Ù†ÙŠ</th>
                                <th>Ù†Ø´Ø·/Ù…ÙƒØªÙ…Ù„</th>
                                <th>Ø§Ù„ØªÙ‚ÙŠÙŠÙ…</th>
                                <th>Ø§Ø¬Ø±Ø§Ø¡</th>
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
                                    <td>{{ getPriceFormat($row['total_sales']) }}</td>
                                    <td>{{ translatedNumber($row['paid_enrollments']) }} / {{ translatedNumber($row['free_enrollments']) }}</td>
                                    <td>{{ translatedNumber($row['active_students']) }} / {{ translatedNumber($row['completed_students']) }}</td>
                                    <td>{{ translatedNumber(number_format($row['rating_avg'], 2)) }} ({{ translatedNumber($row['rating_count']) }})</td>
                                    <td><a class="primary-btn small fix-gr-bg" href="{{ route('teacher.courses.analytics', $row['course']->id) }}">ØªÙØ§ØµÙŠÙ„</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="13" class="text-center">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª Ø­ØªÙ‰ Ø§Ù„Ø§Ù†</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-30 row-gap-24">
                <div class="col-lg-6"><div class="white-box"><h4>Enrollment over time</h4>@if(count($charts['enrollment']['data']))<div class="chart-wrap"><canvas id="enrollmentChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Revenue over time</h4>@if(count($charts['revenue']['data']))<div class="chart-wrap"><canvas id="revenueChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Completion percentage</h4>@if(count($charts['completion']['data']))<div class="chart-wrap"><canvas id="completionChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Quiz average score</h4>@if(count($charts['quiz_avg']['data']))<div class="chart-wrap"><canvas id="quizAvgChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Top courses by revenue</h4>@if(count($charts['top_revenue_courses']['data']))<div class="chart-wrap"><canvas id="topRevenueCoursesChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Top courses by students</h4>@if(count($charts['top_students_courses']['data']))<div class="chart-wrap"><canvas id="topStudentsCoursesChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>Ø§Ø®Ø± Ø§Ù„ØªØ³Ø¬ÙŠÙ„Ø§Øª</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead><tr><th>Ø§Ù„Ø·Ø§Ù„Ø¨</th><th>Ø§Ù„Ø¨Ø±ÙŠØ¯</th><th>Ø§Ù„ÙƒÙˆØ±Ø³</th><th>Ø§Ù„ØªØ§Ø±ÙŠØ®</th><th>Ø§Ù„Ù…Ø¨Ù„Øº</th></tr></thead>
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
                                <tr><td colspan="5" class="text-center">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª Ø­ØªÙ‰ Ø§Ù„Ø§Ù†</td></tr>
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
        const topRevenueCourses = @json($charts['top_revenue_courses']);
        const topStudentsCourses = @json($charts['top_students_courses']);

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
        drawBar('topRevenueCoursesChart', topRevenueCourses, 'Revenue');
        drawBar('topStudentsCoursesChart', topStudentsCourses, 'Students');
    </script>
@endpush

