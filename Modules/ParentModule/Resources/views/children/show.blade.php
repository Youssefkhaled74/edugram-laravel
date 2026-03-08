@extends('parentmodule::layouts.app')

@section('title', 'تفاصيل الطفل')
@section('page-title', $child->name)
@section('page-subtitle', 'معلومات الطالب وتقدمه')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('parent.children.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                العودة إلى قائمة الأطفال
            </a>
        </div>
    </div>
    
    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user mr-2"></i>
                    معلومات الطالب
                </div>
                <div class="card-body text-center">
                    <img src="{{ $child->image ?? asset('public/demo/user/user.jpg') }}" 
                         alt="{{ $child->name }}" 
                         class="rounded-circle mb-3"
                         width="120" height="120"
                         style="border: 4px solid #667eea;">
                    <h4 class="mb-2">{{ $child->name }}</h4>
                    <p class="text-muted mb-1">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ $child->email }}
                    </p>
                    @if($child->phone)
                    <p class="text-muted mb-1">
                        <i class="fas fa-phone mr-2"></i>
                        {{ $child->phone }}
                    </p>
                    @endif
                    @if($child->date_of_birth)
                    <p class="text-muted mb-1">
                        <i class="fas fa-birthday-cake mr-2"></i>
                        {{ \Carbon\Carbon::parse($child->date_of_birth)->format('Y-m-d') }}
                    </p>
                    @endif
                    <hr>
                    <p class="mb-1">
                        <strong>العلاقة:</strong> 
                        <span class="badge badge-info">{{ ucfirst($relationship->relationship_type) }}</span>
                    </p>
                    <p class="mb-0">
                        <strong>الحالة:</strong> 
                        <span class="badge badge-success">{{ ucfirst($relationship->status) }}</span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $stats['total_courses'] }}</h3>
                                    <p class="mb-0">الدورات المسجلة</p>
                                </div>
                                <i class="fas fa-book fa-3x" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $stats['total_quizzes'] }}</h3>
                                    <p class="mb-0">الاختبارات التي تم إجراؤها</p>
                                </div>
                                <i class="fas fa-clipboard-check fa-3x" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $stats['average_score'] }}%</h3>
                                    <p class="mb-0">المعدل العام للدرجات</p>
                                </div>
                                <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $stats['in_progress_courses'] }}</h3>
                                    <p class="mb-0">قيد التقدم</p>
                                </div>
                                <i class="fas fa-spinner fa-3x" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
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
                    @if(count($enrolledCourses) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>اسم الدورة</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>الحالة</th>
                                        <th>التقدم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledCourses as $enrollment)
                                        <tr>
                                            <td>
                                                <strong>{{ $enrollment->course->title ?? 'غير متوفر' }}</strong>
                                            </td>
                                            <td>{{ $enrollment->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge badge-success">نشط</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $enrollment->progress ?? 0 }}%;" 
                                                         aria-valuenow="{{ $enrollment->progress ?? 0 }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $enrollment->progress ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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
    
    <!-- Recent Quiz Results -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    نتائج الاختبارات الأخيرة
                </div>
                <div class="card-body">
                    @if(count($quizResults) > 0)
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
                                            <td>{{ $result->quiz->title ?? 'غير متوفر' }}</td>
                                            <td>{{ $result->created_at->format('Y-m-d H:i') }}</td>
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
@endsection