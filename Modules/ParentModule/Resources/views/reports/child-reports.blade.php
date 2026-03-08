@extends('parentmodule::layouts.app')

@section('title', 'تقرير - ' . $child->name)
@section('page-title', $child->name . ' - تقرير مفصل')
@section('page-subtitle', 'التقدم الأكاديمي والأداء')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('parent.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                العودة إلى التقارير
            </a>
        </div>
    </div>
    
    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img src="{{ $child->image ?? asset('public/demo/user/user.jpg') }}" 
                                 alt="{{ $child->name }}" 
                                 class="rounded-circle mr-3"
                                 width="80" height="80"
                                 style="border: 4px solid white;">
                            <div>
                                <h3 class="mb-1">{{ $child->name }}</h3>
                                <p class="mb-0">
                                    <i class="fas fa-envelope mr-2"></i>
                                    {{ $child->email }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('parent.reports.export', $child->id) }}" class="btn btn-light">
                                <i class="fas fa-download mr-2"></i>
                                تصدير التقرير
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x mb-2" style="opacity: 0.5;"></i>
                    <h2 class="mb-0">{{ $stats['total_courses'] }}</h2>
                    <p class="mb-0">الدورات المسجلة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-check fa-3x mb-2" style="opacity: 0.5;"></i>
                    <h2 class="mb-0">{{ $stats['total_quizzes'] }}</h2>
                    <p class="mb-0">الاختبارات المأخوذة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-2" style="opacity: 0.5;"></i>
                    <h2 class="mb-0">{{ $stats['average_score'] }}%</h2>
                    <p class="mb-0">المعدل العام</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x mb-2" style="opacity: 0.5;"></i>
                    <h2 class="mb-0">{{ $stats['attendance_rate'] }}%</h2>
                    <p class="mb-0">نسبة الحضور</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enrolled Courses -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book-open mr-2"></i>
                    الدورات المسجلة
                </div>
                <div class="card-body">
                    @if($enrolledCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>اسم الدورة</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>التقدم</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledCourses as $enrollment)
                                        @if($enrollment->course)
                                            <tr>
                                                <td>
                                                    <strong>{{ $enrollment->course->title }}</strong>
                                                </td>
                                                <td>
                                                    @if($enrollment->created_at instanceof \Carbon\Carbon)
                                                        {{ $enrollment->created_at->format('M d, Y') }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($enrollment->created_at)->format('M d, Y') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px; min-width: 150px;">
                                                        <div class="progress-bar bg-success" 
                                                             role="progressbar" 
                                                             style="width: {{ $enrollment->progress ?? 0 }}%;">
                                                            {{ $enrollment->progress ?? 0 }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">نشط</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book-open fa-3x mb-3" style="color: #e0e0e0;"></i>
                            <p class="text-muted">لا توجد دورات مسجلة حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quiz Results -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    نتائج الاختبارات
                </div>
                <div class="card-body">
                    @if($quizResults->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>اسم الاختبار</th>
                                        <th>التاريخ</th>
                                        <th>الدرجة</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizResults as $result)
                                        <tr>
                                            <td>
                                                <strong>{{ $result->quiz->title ?? 'غير متوفر' }}</strong>
                                            </td>
                                            <td>
                                                @if($result->created_at instanceof \Carbon\Carbon)
                                                    {{ $result->created_at->format('M d, Y H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($result->created_at)->format('M d, Y H:i') }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ $result->score ?? 0 }}%</strong>
                                            </td>
                                            <td>
                                                @if($result->score >= 80)
                                                    <span class="badge badge-success">ممتاز</span>
                                                @elseif($result->score >= 60)
                                                    <span class="badge badge-info">جيد</span>
                                                @elseif($result->score >= 50)
                                                    <span class="badge badge-warning">ناجح</span>
                                                @else
                                                    <span class="badge badge-danger">يحتاج إلى تحسين</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3" style="color: #e0e0e0;"></i>
                            <p class="text-muted">لا توجد نتائج اختبارات متاحة حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection