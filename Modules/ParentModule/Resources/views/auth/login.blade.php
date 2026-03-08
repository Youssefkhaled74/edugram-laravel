@extends('parentmodule::layouts.guest')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="card shadow-lg" style="max-width: 450px; margin: 50px auto; border-radius: 20px;">
    <div class="card-header text-center">
        <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
        <h4 class="mb-0">تسجيل الدخول</h4>
        <small>بوابة أولياء الأمور</small>
    </div>
    <div class="card-body p-4">
        @include('parentmodule::partials.alerts')
        
        <form method="POST" action="{{ route('parent.login.submit') }}">
            @csrf
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope ml-2"></i>البريد الإلكتروني</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       placeholder="أدخل بريدك الإلكتروني"
                       required 
                       autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock ml-2"></i>كلمة المرور</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="أدخل كلمة المرور"
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    تذكرني
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-sign-in-alt ml-2"></i> تسجيل الدخول
            </button>
            
            <hr>
            
            <div class="text-center">
                <p class="mb-2">
                    <a href="{{ route('parent.forgot-password') }}" class="text-primary">
                        <i class="fas fa-question-circle ml-1"></i>
                        هل نسيت كلمة المرور؟
                    </a>
                </p>
                <p class="mb-0">هل ليس لديك حساب؟</p>
                <a href="{{ route('parent.register') }}" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fas fa-user-plus ml-2"></i>
                    إنشاء حساب جديد
                </a>
            </div>
        </form>
    </div>
</div>
@endsection