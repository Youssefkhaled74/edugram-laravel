@if(session('is_impersonating', false))
<div class="alert alert-warning alert-dismissible fade show sticky-top mb-0" role="alert" style="border-radius: 0; z-index: 1050;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <i class="fas fa-user-shield mr-2"></i>
                <strong>وضع التمثيل:</strong> 
                أنت مسجل الدخول حالياً كـ <strong>{{ auth()->user()->name }}</strong>.
                <small class="ml-2 text-muted">
                    (ولي الأمر: {{ session('impersonating_parent_name') }})
                </small>
            </div>
            <div class="col-md-4 text-right">
                <form method="POST" action="{{ route('parent.impersonation.return') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-dark">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        العودة إلى لوحة ولي الأمر
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif