@extends('parentmodule::layouts.app')

@section('title', 'جميع الدورات')
@section('page-title', 'الدورات')
@section('page-subtitle', 'عرض جميع الدورات المسجلة لأطفالك')

@section('content')
<div class="container-fluid">
    <!-- Children Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">التصفية حسب الطفل:</h6>
                    <div class="btn-group flex-wrap" role="group">
                        <a href="{{ route('parent.courses.index') }}" class="btn btn-outline-primary active">
                            <i class="fas fa-users mr-2"></i>
                            جميع الأطفال
                        </a>
                        @foreach($children as $child)
                            <a href="{{ route('parent.courses.child', ['childId' => $child->id]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-user mr-2"></i>
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- All Courses -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book-open mr-2"></i>
                    جميع الدورات المسجلة
                </div>
                <div class="card-body">
                    @if($allCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>اسم الدورة</th>
                                        <th>الطالب</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>التقدم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allCourses as $enrollment)
                                        @php
                                            $course = $enrollment->course ?? null;
                                        @endphp
                                        @if($course)
                                            <tr>
                                                <td>
                                                    <strong>{{ $course->title }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $enrollment->student_name }}
                                                    </span>
                                                </td>
                                                <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px; min-width: 100px;">
                                                        <div class="progress-bar bg-success" 
                                                             role="progressbar" 
                                                             style="width: {{ $enrollment->progress ?? 0 }}%;">
                                                            {{ $enrollment->progress ?? 0 }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('parent.courses.show', ['courseId' => $course->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        عرض
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-4x mb-3" style="color: #e0e0e0;"></i>
                            <h5 class="text-muted">لا توجد دورات</h5>
                            <p class="text-muted">أطفالك لم يسجلوا في أي دورات حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection