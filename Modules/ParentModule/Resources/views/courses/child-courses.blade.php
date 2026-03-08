@extends('parentmodule::layouts.app')

@section('title', 'الدورات - ' . $child->name)
@section('page-title', $child->name . ' - الدورات')
@section('page-subtitle', 'عرض وإدارة الدورات المسجلة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('parent.children.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                العودة إلى الأبناء
            </a>
        </div>
    </div>
    
    <!-- Student Info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ $child->image ?? asset('public/demo/user/user.jpg') }}" 
                             alt="{{ $child->name }}" 
                             class="rounded-circle mr-3"
                             width="60" height="60"
                             style="border: 3px solid white;">
                        <div>
                            <h4 class="mb-1">{{ $child->name }}</h4>
                            <p class="mb-0">
                                <i class="fas fa-book mr-2"></i>
                                {{ $enrolledCourses->count() }} دورة مسجلة
                            </p>
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
                    @if($enrolledCourses->count() > 0)
                        <div class="row">
                            @foreach($enrolledCourses as $enrollment)
                                @php
                                    $course = $enrollment->course ?? null;
                                @endphp
                                @if($course)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <img src="{{ 'https://portals.maqraatalaqsa.com/'.$course->image ?? asset('public/demo/course.jpg') }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $course->title }}"
                                                 style="height: 200px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $course->title }}</h5>
                                                <p class="card-text text-muted">
                                                   
                                                </p>
                                                
                                                <div class="mb-3">
                                                    <small class="text-muted">التقدم</small>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" 
                                                             role="progressbar" 
                                                             style="width: {{ $enrollment->progress ?? 0 }}%;" 
                                                             aria-valuenow="{{ $enrollment->progress ?? 0 }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ $enrollment->progress ?? 0 }}%
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        تم التسجيل: {{ $enrollment->created_at->format('M d, Y') }}
                                                    </small>
                                                    <a href="{{ route('parent.courses.show', ['courseId' => $course->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        عرض
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-4x mb-3" style="color: #e0e0e0;"></i>
                            <h5 class="text-muted">لا توجد دورات مسجلة</h5>
                            <p class="text-muted">هذا الطالب لم يسجل في أي دورات بعد.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    

</div>
@endsection