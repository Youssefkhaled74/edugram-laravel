@extends('parentmodule::layouts.app')

@section('title', 'لوحة تحكم ولي الأمر')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        مرحباً بعودتك، {{ auth()->user()->name }}!
    </h1>
</div>

<!-- Content Row - Statistics -->
<div class="row">
    
    <!-- Children Count Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            أبنائي
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $childrenCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Courses Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            الدورات النشطة
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCourses }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Payments Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            المدفوعات المعلقة
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingPayments }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notifications Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            الإشعارات
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unreadNotifications }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Content Row - Children & Activities -->
<div class="row">
    
    <!-- Children List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    أبنائي
                </h6>
                <a href="{{ route('parent.children.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> إضافة ابن
                </a>
            </div>
            <div class="card-body">
                @if($children->count() > 0)
                    @foreach($children as $child)
                        <div class="child-card mb-3 p-3 border rounded">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h5 class="mb-1">{{ $child->name }}</h5>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-envelope"></i> {{ $child->email }}
                                    </p>
                                    <div class="d-flex">
                                        <span class="badge badge-success mr-2">
                                            نشط
                                        </span>
                                        <span class="badge badge-info">
                                            {{ $child->enrolled_courses_count ?? 0 }} دورة مسجلة
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-5 text-right">
                                    <div class="btn-group-vertical btn-group-sm w-100" role="group">
                                        <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-primary mb-1">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                        <a href="{{ route('parent.courses.child', $child->id) }}" class="btn btn-outline-success mb-1">
                                            <i class="fas fa-book"></i> عرض الدورات
                                        </a>
                                        <form method="POST" action="{{ route('parent.impersonation.login', $child->id) }}" class="mb-1">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm w-100" 
                                                    onclick="return confirm('سيتم تسجيل دخولك كـ {{ $child->name }}. هل تريد المتابعة؟')">
                                                <i class="fas fa-user-shield"></i> الدخول كابن
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">لم تقم بإضافة أي أبناء بعد</p>
                        <a href="{{ route('parent.children.create') }}" class="btn btn-primary">
                            أضف ابنك الأول الآن
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    الأنشطة الأخيرة
                </h6>
            </div>
            <div class="card-body">
                @if($recentActivities->count() > 0)
                    @foreach($recentActivities as $activity)
                        <div class="activity-item mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="activity-icon mr-3">
                                    <i class="fas {{ $activity['icon'] ?? 'fa-circle' }} text-{{ $activity['color'] ?? 'primary' }}"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="mb-1">
                                        <strong>{{ $activity['student_name'] }}</strong>
                                        {{ $activity['course_name'] }}
                                    </p>
                                    <small class="text-muted">
                                        {{ $activity['date']->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-gray-300 mb-2"></i>
                        <p class="text-gray-500 small">لا توجد أنشطة حديثة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
</div>

@endsection

@push('scripts')
<script>
    // Dashboard specific JavaScript
    console.log('Parent Dashboard loaded');
</script>
@endpush