@extends('parentmodule::layouts.app')

@section('title', 'إضافة طفل')
@section('page-title', 'إضافة طفل')
@section('page-subtitle', 'تسجيل طالب جديد أو ربط طالب موجود')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-plus mr-2"></i>
                    إضافة طفل إلى حسابك
                </div>
                <div class="card-body">
                    @include('parentmodule::partials.alerts')
                    
                    <form method="POST" action="{{ route('parent.children.store') }}">
                        @csrf
                        
                        <!-- Registration Type Selection -->
                        <div class="form-group">
                            <label><strong>نوع التسجيل</strong></label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="registration_type" value="new" checked onchange="toggleRegistrationType()">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    إنشاء حساب طالب جديد
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="registration_type" value="existing" onchange="toggleRegistrationType()">
                                    <i class="fas fa-link mr-2"></i>
                                    ربط طالب موجود
                                </label>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- New Student Registration Form -->
                        <div id="new-student-form">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user mr-2"></i>
                                معلومات الطالب
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">الاسم الكامل <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}"
                                               placeholder="أدخل الاسم الكامل للطالب">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               placeholder="student@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">كلمة المرور <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="8 أحرف على الأقل">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">رقم الهاتف</label>
                                        <input type="text" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone') }}"
                                               placeholder="رقم الهاتف">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">تاريخ الميلاد</label>
                                        <input type="date" 
                                               class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" 
                                               name="date_of_birth" 
                                               value="{{ old('date_of_birth') }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">الجنس</label>
                                        <select class="form-control @error('gender') is-invalid @enderror" 
                                                id="gender" 
                                                name="gender">
                                            <option value="">اختر الجنس</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Existing Student Link Form -->
                        <div id="existing-student-form" style="display: none;">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-link mr-2"></i>
                                ربط بطالب موجود
                            </h5>
                            
                            <div class="form-group">
                                <label for="student_id">البريد الإلكتروني أو رقم هوية الطالب <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('student_id') is-invalid @enderror" 
                                       id="student_search" 
                                       placeholder="أدخل البريد الإلكتروني أو رقم هوية الطالب">
                                <input type="hidden" name="student_id" id="student_id">
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    يجب أن يكون لدى الطالب حساب مسبق في النظام.
                                </small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Relationship Information -->
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-heart mr-2"></i>
                            معلومات العلاقة
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="relationship_type">العلاقة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('relationship_type') is-invalid @enderror" 
                                            id="relationship_type" 
                                            name="relationship_type" 
                                            required>
                                        <option value="">اختر العلاقة</option>
                                        <option value="father" {{ old('relationship_type') == 'father' ? 'selected' : '' }}>أب</option>
                                        <option value="mother" {{ old('relationship_type') == 'mother' ? 'selected' : '' }}>أم</option>
                                        <option value="guardian" {{ old('relationship_type') == 'guardian' ? 'selected' : '' }}>ولي أمر</option>
                                        <option value="other" {{ old('relationship_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                    @error('relationship_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_primary_parent" 
                                               name="is_primary_parent" 
                                               value="1"
                                               {{ old('is_primary_parent') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_primary_parent">
                                            أنا الوالد/الوصي الأساسي
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>ملاحظة:</strong> سيتم إنشاء حساب الطالب فوراً وستتمكن من إدارة دوراته، ومتابعة درجاته، وإجراء الدفعات.
                        </div>
                        
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check mr-2"></i>
                                إضافة طفل
                            </button>
                            <a href="{{ route('parent.children.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times mr-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleRegistrationType() {
    const registrationType = document.querySelector('input[name="registration_type"]:checked').value;
    const newForm = document.getElementById('new-student-form');
    const existingForm = document.getElementById('existing-student-form');
    
    if (registrationType === 'new') {
        newForm.style.display = 'block';
        existingForm.style.display = 'none';
        
        // Make new student fields required
        document.getElementById('name').required = true;
        document.getElementById('email').required = true;
        document.getElementById('password').required = true;
        document.getElementById('student_search').required = false;
    } else {
        newForm.style.display = 'none';
        existingForm.style.display = 'block';
        
        // Make existing student field required
        document.getElementById('name').required = false;
        document.getElementById('email').required = false;
        document.getElementById('password').required = false;
        document.getElementById('student_search').required = true;
    }
}
</script>
@endpush