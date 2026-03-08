@extends('parentmodule::layouts.app')

@section('title', 'التقارير')
@section('page-title', 'التقارير والتقدم الدراسي')
@section('page-subtitle', 'عرض التقدم الأكاديمي وتقارير أبنائك')

@section('content')
<div class="container-fluid">
    <!-- Children Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-filter mr-2"></i>
                        التصفية حسب الطفل:
                    </h6>
                    <div class="btn-group flex-wrap" role="group">
                        <a href="{{ route('parent.reports.index') }}" class="btn btn-outline-primary active">
                            <i class="fas fa-users mr-2"></i>
                            جميع الأبناء
                        </a>
                        @if(isset($children))
                            @foreach($children as $child)
                                <a href="{{ route('parent.reports.child', $child->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-user mr-2"></i>
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Children Progress Overview -->
    @if(isset($children) && $children->count() > 0)
        <div class="row">
            @foreach($children as $child)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-graduate mr-2"></i>
                                    {{ $child->name }}
                                </div>
                                <a href="{{ route('parent.reports.child', $child->id) }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-eye mr-1"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                // Get enrolled courses count
                                $enrolledCount = 0;
                                if (class_exists('\App\Models\CourseEnrolled')) {
                                    $enrolledCount = \App\Models\CourseEnrolled::where('user_id', $child->id)->count();
                                }
                                
                                // Get quiz results count
                                $quizCount = 0;
                                $avgScore = 0;
                                if (class_exists('\Modules\Quiz\Entities\StudentTakeOnlineQuiz')) {
                                    $quizResults = \Modules\Quiz\Entities\StudentTakeOnlineQuiz::where('student_id', $child->id)->get();
                                    $quizCount = $quizResults->count();
                                    if ($quizCount > 0) {
                                        $avgScore = round($quizResults->avg('score'), 2);
                                    }
                                }
                            @endphp
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded">
                                        <h3 class="mb-1 text-primary">{{ $enrolledCount }}</h3>
                                        <small class="text-muted">الدورات</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded">
                                        <h3 class="mb-1 text-success">{{ $quizCount }}</h3>
                                        <small class="text-muted">الاختبارات</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded">
                                        <h3 class="mb-1 text-warning">{{ $avgScore }}%</h3>
                                        <small class="text-muted">متوسط الدرجة</small>
                                    </div>
                                </div>
                            </div>
                            
                            @if($enrolledCount > 0)
                                <hr>
                                <h6 class="mb-3">النشاط الأخير</h6>
                                @php
                                    $recentCourses = collect();
                                    if (class_exists('\App\Models\CourseEnrolled')) {
                                        $recentCourses = \App\Models\CourseEnrolled::where('user_id', $child->id)
                                            ->with('course')
                                            ->latest()
                                            ->limit(3)
                                            ->get();
                                    }
                                @endphp
                                
                                @if($recentCourses->count() > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach($recentCourses as $enrollment)
                                            @if($enrollment->course)
                                                <li class="mb-2">
                                                    <i class="fas fa-book text-primary mr-2"></i>
                                                    <strong>{{ $enrollment->course->title }}</strong>
                                                      

                                                    <small class="text-muted ml-4">
                                                        تم التسجيل: 
                                                        @if($enrollment->created_at instanceof \Carbon\Carbon)
                                                            {{ $enrollment->created_at->format('M d, Y') }}
                                                        @else
                                                            {{ \Carbon\Carbon::parse($enrollment->created_at)->format('M d, Y') }}
                                                        @endif
                                                    </small>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-info-circle text-muted fa-2x mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد دورات مسجلة حتى الآن</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('parent.reports.child', $child->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    التقرير الكامل
                                </a>
                                <a href="{{ route('parent.courses.child', $child->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-book mr-1"></i>
                                    عرض الدورات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-plus fa-4x mb-3" style="color: #e0e0e0;"></i>
                        <h5 class="text-muted">لم يتم العثور على أبناء</h5>
                        <p class="text-muted mb-4">يجب عليك إضافة أبناء إلى حسابك لعرض تقاريرهم.</p>
                        <a href="{{ route('parent.children.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-2"></i>
                            إضافة طفل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection