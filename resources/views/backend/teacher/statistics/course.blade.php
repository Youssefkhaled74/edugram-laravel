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
            <div class="white-box mb-30"><div class="main-title"><h3>{{__('courses.Course Statistics')}} - {{ $course->title }}</h3></div></div>

            <div class="row row-gap-24">
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['students_count']) }}</h4><p>{{__('common.Total Enrolled')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(count($metrics['student_ids'])) }}</h4><p>{{__('dashboard.Unique Students')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['completion_percentage'], 2)) }}%</h4><p>{{__('courses.Completion Rate')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['active_students']) }} / {{ translatedNumber($metrics['completed_students']) }}</h4><p>{{__('common.Active')}} / {{__('common.Completed')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['lectures_count']) }}</h4><p>{{__('courses.Lectures')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['quizzes_count']) }}</h4><p>{{__('courses.Quizzes')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['quiz_avg_score'], 2)) }}</h4><p>{{__('courses.Quiz Avg')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['assignments_submitted']) }}</h4><p>{{__('courses.Assignments')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ getPriceFormat($metrics['total_revenue']) }}</h4><p>{{__('courses.Revenue')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ getPriceFormat($metrics['total_sales']) }}</h4><p>{{__('common.Total Sales')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber($metrics['paid_enrollments']) }} / {{ translatedNumber($metrics['free_enrollments']) }}</h4><p>{{__('common.Paid')}} / {{__('common.Free')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box"><h4>{{ translatedNumber(number_format($metrics['rating_avg'],2)) }} ({{ translatedNumber($metrics['rating_count']) }})</h4><p>{{__('common.Rating')}}</p></div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>{{__('dashboard.Student Watch Time')}}</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead><tr>
                                <th>{{__('common.Student')}}</th>
                                <th>{{__('common.Email')}}</th>
                                <th>{{__('courses.Watch Time')}}</th>
                                <th>{{__('courses.Completed')}} / {{__('courses.Total')}}</th>
                                <th>{{__('courses.Completion')}} %</th>
                            </tr></thead>
                            <tbody>
                            @forelse($studentWatchData as $data)
                                @php
                                    $hours = intdiv($data['watch_seconds'], 3600);
                                    $mins = intdiv($data['watch_seconds'] % 3600, 60);
                                    $watchFormatted = $hours > 0 ? $hours . 'h ' . $mins . 'm' : $mins . ' ' . __('common.Minutes');
                                @endphp
                                <tr>
                                    <td>{{ $data['user']->name }}</td>
                                    <td>{{ $data['user']->email }}</td>
                                    <td>{{ $watchFormatted }}</td>
                                    <td>{{ translatedNumber($data['completed_lessons']) }} / {{ translatedNumber($data['total_lessons']) }}</td>
                                    <td>{{ translatedNumber($data['completion_percentage']) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">{{__('dashboard.No Results Found')}}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-30 row-gap-24">
                <div class="col-lg-6"><div class="white-box"><h4>{{__('dashboard.Enrollment over time')}}</h4>@if(count($charts['enrollment']['data']))<div class="chart-wrap"><canvas id="enrollmentChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('dashboard.Revenue over time')}}</h4>@if(count($charts['revenue']['data']))<div class="chart-wrap"><canvas id="revenueChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('courses.Completion Percentage')}}</h4><div class="chart-wrap"><canvas id="completionChart"></canvas></div></div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('courses.Quiz Average Score')}}</h4><div class="chart-wrap"><canvas id="quizAvgChart"></canvas></div></div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>{{__('dashboard.Recent Enrolls')}}</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead><tr><th>{{__('common.Student')}}</th><th>{{__('common.Email')}}</th><th>{{__('common.Date')}}</th><th>{{__('common.Price')}}</th></tr></thead>
                            <tbody>
                            @forelse($recentEnrollments as $enroll)
                                <tr>
                                    <td>{{ $enroll->user->name }}</td>
                                    <td>{{ $enroll->user->email }}</td>
                                    <td>{{ showDate($enroll->created_at) }}</td>
                                    <td>{{ getPriceFormat($enroll->reveune > 0 ? $enroll->reveune : $enroll->purchase_price) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">{{__('dashboard.No Results Found')}}</td></tr>
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
