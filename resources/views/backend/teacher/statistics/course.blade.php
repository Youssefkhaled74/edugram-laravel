@extends('backend.master')
@push('styles')
    <style>
        .chart-wrap { position: relative; height: 320px; width: 100%; }
        .chart-wrap canvas { width: 100% !important; height: 100% !important; display: block; }
    </style>
@endpush

@section('mainContent')
    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-30"><div class="main-title"><h3>ØªÙØ§ØµÙŠÙ„ Ø§Ø­ØµØ§Ø¦ÙŠØ§Øª Ø§Ù„ÙƒÙˆØ±Ø³ - {{ $course->title }}</h3></div></div>

            <div class="row row-gap-24">
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['students_count']) }}</h4><p>Ø¹Ø¯Ø¯ Ø§Ù„ØªØ³Ø¬ÙŠÙ„Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(count($metrics['student_ids'])) }}</h4><p>Ø§Ù„Ø·Ù„Ø§Ø¨ Ø§Ù„ÙØ±ÙŠØ¯ÙŠÙ†</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['completion_percentage'], 2)) }}%</h4><p>Ù†Ø³Ø¨Ø© Ø§ÙƒÙ…Ø§Ù„ Ø§Ù„ÙƒÙˆØ±Ø³</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['active_students']) }} / {{ translatedNumber($metrics['completed_students']) }}</h4><p>Ù†Ø´Ø· / Ù…ÙƒØªÙ…Ù„</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['lectures_count']) }}</h4><p>Ø¹Ø¯Ø¯ Ø§Ù„Ù…Ø­Ø§Ø¶Ø±Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['quizzes_count']) }}</h4><p>Ø¹Ø¯Ø¯ Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['quiz_avg_score'], 2)) }}</h4><p>Ù…ØªÙˆØ³Ø· Ø¯Ø±Ø¬Ø§Øª Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['assignments_submitted']) }}</h4><p>Ø¹Ø¯Ø¯ Ø§Ù„ÙˆØ§Ø¬Ø¨Ø§Øª Ø§Ù„Ù…Ø³Ù„Ù…Ø©</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ getPriceFormat($metrics['total_revenue']) }}</h4><p>Ø§ÙŠØ±Ø§Ø¯ Ø§Ù„Ù…Ø¯Ø±Ø³ Ù…Ù† Ø§Ù„ÙƒÙˆØ±Ø³</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ getPriceFormat($metrics['total_sales']) }}</h4><p>Ø§Ø¬Ù…Ø§Ù„ÙŠ Ù…Ø¨ÙŠØ¹Ø§Øª Ø§Ù„ÙƒÙˆØ±Ø³</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['paid_enrollments']) }} / {{ translatedNumber($metrics['free_enrollments']) }}</h4><p>Ù…Ø¯ÙÙˆØ¹ / Ù…Ø¬Ø§Ù†ÙŠ</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['rating_avg'],2)) }} ({{ translatedNumber($metrics['rating_count']) }})</h4><p>ØªÙ‚ÙŠÙŠÙ…Ø§Øª Ø§Ù„ÙƒÙˆØ±Ø³</p></div></div>
            </div>

            <div class="row mt-30 row-gap-24">
                <div class="col-lg-6"><div class="white-box"><h4>Enrollment over time</h4>@if(count($charts['enrollment']['data']))<div class="chart-wrap"><canvas id="enrollmentChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Revenue over time</h4>@if(count($charts['revenue']['data']))<div class="chart-wrap"><canvas id="revenueChart"></canvas></div>@else <p class="mt-3">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª ÙƒØ§ÙÙŠØ© Ù„Ø¹Ø±Ø¶ Ø§Ù„Ø±Ø³Ù… Ø§Ù„Ø¨ÙŠØ§Ù†ÙŠ</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Completion percentage</h4><div class="chart-wrap"><canvas id="completionChart"></canvas></div></div></div>
                <div class="col-lg-6"><div class="white-box"><h4>Quiz average score</h4><div class="chart-wrap"><canvas id="quizAvgChart"></canvas></div></div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>Ø§Ø®Ø± Ø§Ù„ØªØ³Ø¬ÙŠÙ„Ø§Øª</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead><tr><th>Ø§Ù„Ø·Ø§Ù„Ø¨</th><th>Ø§Ù„Ø¨Ø±ÙŠØ¯</th><th>ØªØ§Ø±ÙŠØ® Ø§Ù„ØªØ³Ø¬ÙŠÙ„</th><th>Ø§Ù„Ù…Ø¨Ù„Øº</th></tr></thead>
                            <tbody>
                            @forelse($recentEnrollments as $enroll)
                                <tr>
                                    <td>{{ $enroll->user->name }}</td>
                                    <td>{{ $enroll->user->email }}</td>
                                    <td>{{ showDate($enroll->created_at) }}</td>
                                    <td>{{ getPriceFormat($enroll->reveune > 0 ? $enroll->reveune : $enroll->purchase_price) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª Ø­ØªÙ‰ Ø§Ù„Ø§Ù†</td></tr>
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

