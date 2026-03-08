@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-right: 4px solid #28a745; border-radius: 10px;">
        <i class="fas fa-check-circle ml-2"></i>
        <strong>نجاح!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق" style="left: auto; right: 15px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-right: 4px solid #dc3545; border-radius: 10px;">
        <i class="fas fa-exclamation-circle ml-2"></i>
        <strong>خطأ!</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق" style="left: auto; right: 15px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-right: 4px solid #ffc107; border-radius: 10px;">
        <i class="fas fa-exclamation-triangle ml-2"></i>
        <strong>تحذير!</strong> {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق" style="left: auto; right: 15px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-right: 4px solid #17a2b8; border-radius: 10px;">
        <i class="fas fa-info-circle ml-2"></i>
        <strong>معلومة!</strong> {{ session('info') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق" style="left: auto; right: 15px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-right: 4px solid #dc3545; border-radius: 10px;">
        <i class="fas fa-times-circle ml-2"></i>
        <strong>يرجى تصحيح الأخطاء التالية:</strong>
        <ul class="mb-0 mt-2" style="padding-right: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق" style="left: auto; right: 15px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif