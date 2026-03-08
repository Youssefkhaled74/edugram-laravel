@extends('parentmodule::layouts.app')

@section('title', $course->title ?? 'تفاصيل الدورة')
@section('page-title', $course->title ?? 'تفاصيل الدورة')
@section('page-subtitle', 'معلومات الدورة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('parent.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                العودة إلى الدورات
            </a>
        </div>
    </div>
    
    <!-- Course Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <img src="{{ 'https://portals.maqraatalaqsa.com/'.$course->image ?? asset('public/demo/course.jpg') }}" 
                     class="card-img-top" 
                     alt="{{ $course->title }}"
                     style="height: 300px; object-fit: cover;">
                <div class="card-body">
                    <h2 class="card-title">{{ $course->title }}</h2>
                    
                    @if(isset($instructor) && $instructor)
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tie mr-2"></i>
                        المدرس: <strong>{{ $instructor->name }}</strong>
                    </p>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                <p class="mb-0"><strong>المدة</strong></p>
                                <p class="mb-0">{{ $course->duration ?? 'غير متوفر' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-signal fa-2x text-success mb-2"></i>
                                <p class="mb-0"><strong>المستوى</strong></p>
                                <p class="mb-0">{{ $course->level ?? 'جميع المستويات' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <p class="mb-0"><strong>عدد الطلاب</strong></p>
                                <p class="mb-0">{{ $course->total_enrolled ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>
                                <p class="mb-0"><strong>السعر</strong></p>
                                <p class="mb-0">
                                    @if(isset($course->price) && $course->price > 0)
                                        ${{ number_format($course->price, 2) }}
                                    @else
                                        <span class="badge badge-success">مجاني</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>حول هذه الدورة</h5>
					<div>
					{!! $course->about ?? 'لا يوجد وصف متاح.' !!}
					</div>
                    
                    
                    @if($course->outcomes)
                    <hr>
                    <h5>ما ستتعلمه</h5>
                    <div>{!! $course->outcomes !!}</div>
                    @endif
                    
                    @if($course->requirements)
                    <hr>
                    <h5>المتطلبات</h5>
                    <div>{!! $course->requirements !!}</div>
                    @endif
                    
                    @if(isset($children) && $children->count() > 0)
                    <hr>
                    <h5>تسجيل طفل في هذه الدورة</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        اختر أحد أطفالك لتسجيله في هذه الدورة.
                    </div>
                    <form action="{{ route('parent.courses.enroll') }}" method="POST">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">
                                        <i class="fas fa-user mr-2"></i>
                                        اختر الطفل
                                    </label>
                                    <select name="student_id" id="student_id" class="form-control" required>
                                        <option value="">-- اختر طفلاً --</option>
                                        @foreach($children as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    تسجيل الطفل في الدورة
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <hr>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        يجب إضافة أطفال إلى حسابك قبل تسجيلهم في الدورات.
                        <a href="{{ route('parent.children.create') }}" class="btn btn-sm btn-primary ml-3">
                            <i class="fas fa-plus mr-2"></i>
                            إضافة طفل
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection