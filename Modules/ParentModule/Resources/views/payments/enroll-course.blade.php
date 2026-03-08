@extends('parentmodule::layouts.app')

@section('title', 'التسجيل في الدورة')
@section('page-title', 'التسجيل في الدورة')
@section('page-subtitle', 'أكمل الدفع لتسجيل طفلك')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات الدورة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ $course->image ? asset($course->image) : asset('public/demo/course.jpg') }}" 
                                 class="img-fluid rounded" 
                                 alt="{{ $course->title }}">
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $course->title }}</h4>
                            <p class="text-muted">{{ $course->about }}</p>
                            
                            <div class="mb-2">
                                <i class="fas fa-user-tie mr-2"></i>
                                <strong>المُدرس:</strong> {{ $course->user->name ?? 'غير متوفر' }}
                            </div>
                            
                            <div class="mb-2">
                                <i class="fas fa-folder mr-2"></i>
                                <strong>الفئة:</strong> {{ $course->category->name ?? 'غير متوفر' }}
                            </div>
                            
                            <div class="mb-2">
                                <i class="fas fa-clock mr-2"></i>
                                <strong>المدة:</strong> {{ $course->duration ?? 'غير متوفر' }}
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 text-primary">
                                        @if($course->price > 0)
                                            ${{ number_format($course->price, 2) }}
                                        @else
                                            <span class="badge badge-success">مجاناً</span>
                                        @endif
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات الطالب</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ $child->image ? asset($child->image) : asset('public/demo/user/user.jpg') }}" 
                             alt="{{ $child->name }}" 
                             class="rounded-circle mr-3" 
                             width="60" 
                             height="60">
                        <div>
                            <h5 class="mb-0">{{ $child->name }}</h5>
                            <p class="text-muted mb-0">{{ $child->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($course->price > 0)
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">طريقة الدفع</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('parent.payment.enroll.process', [$child->id, $course->id]) }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label>اختر طريقة الدفع *</label>
                                @foreach($paymentMethods as $key => $method)
                                    <div class="custom-control custom-radio">
                                        <input type="radio" 
                                               id="payment_{{ $key }}" 
                                               name="payment_method" 
                                               class="custom-control-input" 
                                               value="{{ $key }}" 
                                               {{ old('payment_method', 'paypal') == $key ? 'checked' : '' }}
                                               required>
                                        <label class="custom-control-label" for="payment_{{ $key }}">
                                            {{ $method }}
                                        </label>
                                    </div>
                                @endforeach
                                @error('payment_method')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">المبلغ الإجمالي:</h5>
                                <h3 class="mb-0 text-primary">${{ number_format($course->price, 2) }}</h3>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                بالمضي قدماً في الدفع، فإنك توافق على تسجيل <strong>{{ $child->name }}</strong> في الدورة <strong>{{ $course->title }}</strong>.
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-lock mr-2"></i> المتابعة إلى الدفع
                            </button>
                            
                            <a href="{{ route('parent.courses.available', $child->id) }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-times mr-2"></i> إلغاء
                            </a>
                        </form>
                    </div>
                </div>
            @else
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <h5 class="text-success mb-3">هذه دورة مجانية!</h5>
                        <form action="{{ route('parent.payment.enroll.process', [$child->id, $course->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="free">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check mr-2"></i> سجل الآن
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection