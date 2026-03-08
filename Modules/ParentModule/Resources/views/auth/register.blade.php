@extends('parentmodule::layouts.guest')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="card shadow-lg" style="max-width: 650px; margin: 30px auto; border-radius: 20px;">
    <div class="card-header text-center">
        <i class="fas fa-user-plus fa-2x mb-2"></i>
        <h4 class="mb-0">إنشاء حساب ولي أمر جديد</h4>
        <small>انضم إلى بوابة أولياء الأمور</small>
    </div>
    <div class="card-body p-4">
        @include('parentmodule::partials.alerts')
        
        <form method="POST" action="{{ route('parent.register.submit') }}">
            @csrf
            
            <h6 class="text-primary mb-3">
                <i class="fas fa-user ml-2"></i>
                المعلومات الشخصية
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-id-card ml-2"></i>الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="أدخل اسمك الكامل"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="national_id"><i class="fas fa-id-badge ml-2"></i>رقم الهوية الوطنية</label>
                        <input type="text" 
                               class="form-control @error('national_id') is-invalid @enderror" 
                               id="national_id" 
                               name="national_id" 
                               value="{{ old('national_id') }}"
                               placeholder="رقم الهوية الوطنية">
                        @error('national_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <h6 class="text-primary mb-3 mt-3">
                <i class="fas fa-envelope ml-2"></i>
                معلومات الاتصال
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-at ml-2"></i>البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="example@email.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone ml-2"></i>رقم الجوال</label>
                        <input type="text" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               placeholder="05xxxxxxxx">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="address"><i class="fas fa-map-marker-alt ml-2"></i>العنوان</label>
                        <input type="text" 
                               class="form-control @error('address') is-invalid @enderror" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}"
                               placeholder="العنوان الكامل">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="city"><i class="fas fa-city ml-2"></i>المدينة</label>
                        <input type="text" 
                               class="form-control @error('city') is-invalid @enderror" 
                               id="city" 
                               name="city" 
                               value="{{ old('city') }}"
                               placeholder="المدينة">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="country"><i class="fas fa-flag ml-2"></i>الدولة</label>
                        <input type="text" 
                               class="form-control @error('country') is-invalid @enderror" 
                               id="country" 
                               name="country" 
                               value="{{ old('country') }}"
                               placeholder="الدولة">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <h6 class="text-primary mb-3 mt-3">
                <i class="fas fa-briefcase ml-2"></i>
                معلومات العمل
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="occupation"><i class="fas fa-user-tie ml-2"></i>المهنة</label>
                        <input type="text" 
                               class="form-control @error('occupation') is-invalid @enderror" 
                               id="occupation" 
                               name="occupation" 
                               value="{{ old('occupation') }}"
                               placeholder="المهنة">
                        @error('occupation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="workplace"><i class="fas fa-building ml-2"></i>جهة العمل</label>
                        <input type="text" 
                               class="form-control @error('workplace') is-invalid @enderror" 
                               id="workplace" 
                               name="workplace" 
                               value="{{ old('workplace') }}"
                               placeholder="جهة العمل">
                        @error('workplace')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <h6 class="text-primary mb-3 mt-3">
                <i class="fas fa-phone-square ml-2"></i>
                جهة اتصال للطوارئ
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emergency_contact_name"><i class="fas fa-user-friends ml-2"></i>اسم جهة الاتصال</label>
                        <input type="text" 
                               class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                               id="emergency_contact_name" 
                               name="emergency_contact_name" 
                               value="{{ old('emergency_contact_name') }}"
                               placeholder="الاسم">
                        @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emergency_contact"><i class="fas fa-mobile-alt ml-2"></i>رقم الهاتف</label>
                        <input type="text" 
                               class="form-control @error('emergency_contact') is-invalid @enderror" 
                               id="emergency_contact" 
                               name="emergency_contact" 
                               value="{{ old('emergency_contact') }}"
                               placeholder="رقم الطوارئ">
                        @error('emergency_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <h6 class="text-primary mb-3 mt-3">
                <i class="fas fa-lock ml-2"></i>
                كلمة المرور
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-key ml-2"></i>كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="8 أحرف على الأقل"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation"><i class="fas fa-check-circle ml-2"></i>تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="أعد إدخال كلمة المرور"
                               required>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">
                <i class="fas fa-user-plus ml-2"></i> إنشاء الحساب
            </button>
            
            <hr>
            
            <div class="text-center">
                <p class="mb-0">هل لديك حساب بالفعل؟</p>
                <a href="{{ route('parent.login') }}" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-sign-in-alt ml-2"></i>
                    تسجيل الدخول
                </a>
            </div>
        </form>
    </div>
</div>
@endsection