@extends('parentmodule::layouts.app')

@section('title', 'إدارة الأبناء')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">أبنائي</h1>
    <a href="{{ route('parent.children.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة ابن
    </a>
</div>

<!-- Pending Requests -->
@if($pendingRequests->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-clock"></i> الطلبات المعلقة
        </h6>
    </div>
    <div class="card-body">
        @foreach($pendingRequests as $request)
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $request->student_name }}</strong> - {{ $request->student_email }}
                    <br>
                    <small class="text-muted">تم الإرسال: {{ $request->created_at->diffForHumans() }}</small>
                </div>
                <form method="POST" action="{{ route('parent.children.cancel-request', $request->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        إلغاء الطلب
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Children List -->
<div class="row">
    @forelse($children as $child)
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user"></i> {{ $child->name }}
                    </h6>
                    @if($child->pivot->is_primary_parent)
                        <span class="badge badge-light">ولي الأمر الأساسي</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1">
                            <i class="fas fa-envelope text-primary"></i> 
                            <strong>البريد الإلكتروني:</strong> {{ $child->email }}
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-phone text-primary"></i> 
                            <strong>الهاتف:</strong> {{ $child->phone ?? 'غير محدد' }}
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-heart text-primary"></i> 
                            <strong>صلة القرابة:</strong> 
                            @switch($child->pivot->relationship_type)
                                @case('father') الأب @break
                                @case('mother') الأم @break
                                @case('guardian') الوصي @break
                                @default أخرى
                            @endswitch
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border-right">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $child->enrolled_courses_count ?? 0 }}
                                </div>
                                <div class="small text-gray-500">الدورات المسجلة</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                <div class="small text-gray-500">الشهادات</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold mb-2">الصلاحيات:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small>
                                    <i class="fas {{ $child->pivot->can_view_grades ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    عرض الدرجات
                                </small>
                            </div>
                            <div class="col-6">
                                <small>
                                    <i class="fas {{ $child->pivot->can_make_payments ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    إجراء المدفوعات
                                </small>
                            </div>
                            <div class="col-6">
                                <small>
                                    <i class="fas {{ $child->pivot->can_view_attendance ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    عرض الحضور
                                </small>
                            </div>
                            <div class="col-6">
                                <small>
                                    <i class="fas {{ $child->pivot->can_enroll_courses ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    تسجيل الدورات
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100 mb-2" role="group">
                        <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="{{ route('parent.courses.child', $child->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-book"></i> الدورات
                        </a>
                        <a href="{{ route('parent.children.edit', $child->id) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                data-toggle="modal" data-target="#deleteModal{{ $child->id }}">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                    
                    <!-- Login as Child Button -->
                    <form method="POST" action="{{ route('parent.impersonation.login', $child->id) }}" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm w-100" 
                                onclick="return confirm('سيتم تسجيل دخولك كـ {{ $child->name }}. هل تريد المتابعة؟')">
                            <i class="fas fa-user-shield"></i> الدخول كابن
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal{{ $child->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        هل أنت متأكد من إزالة <strong>{{ $child->name }}</strong> من قائمة أبنائك؟
                        <br><br>
                        <small class="text-muted">لا يمكن التراجع عن هذا الإجراء.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <form method="POST" action="{{ route('parent.children.destroy', $child->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">لم تقم بإضافة أي أبناء بعد</h4>
                    <p class="text-gray-400 mb-4">ابدأ بإضافة ابنك الأول لمتابعة تقدمه الأكاديمي</p>
                    <a href="{{ route('parent.children.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة ابن الآن
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@endsection