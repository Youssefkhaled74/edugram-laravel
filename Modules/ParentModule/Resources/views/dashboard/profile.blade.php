@extends('parentmodule::layouts.app')

@section('title', 'ملفي الشخصي')
@section('page-title', 'ملفي الشخصي')
@section('page-subtitle', 'إدارة معلومات حسابك')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات الملف الشخصي</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('parent.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="text-center mb-4">
                            <img src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('public/demo/user/user.jpg') }}" 
                                 alt="الصورة الشخصية" 
                                 class="rounded-circle mb-3" 
                                 width="120" 
                                 height="120"
                                 id="profile-preview">
                            <div>
                                <label for="image" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-camera mr-1"></i> تغيير الصورة
                                </label>
                                <input type="file" id="image" name="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">الاسم الكامل *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', Auth::user()->name) }}" 
                                           required>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني *</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', Auth::user()->email) }}" 
                                           required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', Auth::user()->phone) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="national_id">رقم الهوية الوطنية</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="national_id" 
                                           name="national_id" 
                                           value="{{ old('national_id', $parent->national_id ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="occupation">المهنة</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="occupation" 
                                           name="occupation" 
                                           value="{{ old('occupation', $parent->occupation ?? '') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="workplace">جهة العمل</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="workplace" 
                                           name="workplace" 
                                           value="{{ old('workplace', $parent->workplace ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="2">{{ old('address', $parent->address ?? '') }}</textarea>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">جهة اتصال الطوارئ</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">اسم جهة الاتصال</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="emergency_contact_name" 
                                           name="emergency_contact_name" 
                                           value="{{ old('emergency_contact_name', $parent->emergency_contact_name ?? '') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact">هاتف جهة الاتصال</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="emergency_contact" 
                                           name="emergency_contact" 
                                           value="{{ old('emergency_contact', $parent->emergency_contact ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> تحديث الملف الشخصي
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#profile-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection