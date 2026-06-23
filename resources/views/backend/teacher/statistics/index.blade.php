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
                            <label class="primary_input_label">{{__('courses.Course Statistics')}}</label>
                            <select class="primary_select" name="course_id">
                                <option value="">{{__('common.All')}} {{__('courses.Courses')}}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (int)$selectedCourseId === (int)$course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 mt-40">
                            <button type="submit" class="primary-btn fix-gr-bg">{{__('common.View')}}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row row-gap-24">
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['total_courses']) }}</h3><p>{{__('common.Total Courses')}}</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['total_enrolled_students']) }}</h3><p>{{__('common.Total Enrolled')}}</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['unique_students_count']) }}</h3><p>{{__('dashboard.Unique Students')}}</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ getPriceFormat($summary['total_revenue']) }}</h3><p>{{__('courses.Total Revenue')}}</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ getPriceFormat($summary['total_sales']) }}</h3><p>{{__('common.Total Sales')}}</p></div></div>
                <div class="col-md-6 col-xl-2"><div class="white-box stats-card"><h3>{{ getPriceFormat($summary['avg_order_value']) }}</h3><p>{{__('dashboard.Avg Order Value')}}</p></div></div>
            </div>
            <div class="row row-gap-24 mt-2">
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['paid_enrollments']) }} / {{ translatedNumber($summary['free_enrollments']) }}</h3><p>{{__('common.Paid')}} / {{__('common.Free')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber($summary['active_students']) }} / {{ translatedNumber($summary['completed_students']) }}</h3><p>{{__('common.Active')}} / {{__('common.Completed')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber(number_format($summary['avg_completion'], 2)) }}%</h3><p>{{__('courses.Avg Completion')}}</p></div></div>
                <div class="col-md-6 col-xl-3"><div class="white-box stats-card"><h3>{{ translatedNumber(number_format($summary['completion_students_rate'], 2)) }}%</h3><p>{{__('courses.Completion Rate')}}</p></div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>{{__('courses.Course Statistics')}}</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>{{__('courses.Course Title')}}</th>
                                <th>{{__('common.Students')}}</th>
                                <th>{{__('courses.Completion')}} %</th>
                                <th>{{__('courses.Lectures')}}</th>
                                <th>{{__('courses.Quizzes')}}</th>
                                <th>{{__('courses.Quiz Avg')}}</th>
                                <th>{{__('courses.Assignments')}}</th>
                                <th>{{__('courses.Revenue')}}</th>
                                <th>{{__('courses.Sales')}}</th>
                                <th>{{__('common.Paid')}}/{{__('common.Free')}}</th>
                                <th>{{__('common.Active')}}/{{__('common.Completed')}}</th>
                                <th>{{__('common.Rating')}}</th>
                                <th>{{__('common.Action')}}</th>
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
                                    <td><a class="primary-btn small fix-gr-bg" href="{{ route('teacher.courses.analytics', $row['course']->id) }}">{{__('common.Details')}}</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="13" class="text-center">{{__('dashboard.No Results Found')}}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-30 row-gap-24">
                <div class="col-lg-6"><div class="white-box"><h4>{{__('dashboard.Enrollment over time')}}</h4>@if(count($charts['enrollment']['data']))<div class="chart-wrap"><canvas id="enrollmentChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('dashboard.Revenue over time')}}</h4>@if(count($charts['revenue']['data']))<div class="chart-wrap"><canvas id="revenueChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('courses.Completion Percentage')}}</h4>@if(count($charts['completion']['data']))<div class="chart-wrap"><canvas id="completionChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('courses.Quiz Average Score')}}</h4>@if(count($charts['quiz_avg']['data']))<div class="chart-wrap"><canvas id="quizAvgChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('dashboard.Top Courses by Revenue')}}</h4>@if(count($charts['top_revenue_courses']['data']))<div class="chart-wrap"><canvas id="topRevenueCoursesChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
                <div class="col-lg-6"><div class="white-box"><h4>{{__('dashboard.Top Courses by Students')}}</h4>@if(count($charts['top_students_courses']['data']))<div class="chart-wrap"><canvas id="topStudentsCoursesChart"></canvas></div>@else <p class="mt-3">{{__('dashboard.Insufficient Data')}}</p>@endif</div></div>
            </div>

            <div class="white-box mt-30">
                <div class="main-title mb-20"><h3>{{__('dashboard.Recent Enrolls')}}</h3></div>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead><tr><th>{{__('common.Student')}}</th><th>{{__('common.Email')}}</th><th>{{__('courses.Course')}}</th><th>{{__('common.Date')}}</th><th>{{__('common.Price')}}</th></tr></thead>
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
                                <tr><td colspan="5" class="text-center">{{__('dashboard.No Results Found')}}</td></tr>
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
