@extends('parentmodule::layouts.app')

@section('title', 'تاريخ الدفعات')
@section('page-title', 'تاريخ الدفعات')
@section('page-subtitle', 'عرض جميع معاملات الدفع الخاصة بك')

@section('content')
<div class="container-fluid">
    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-success text-white">
                <h4 class="mb-0">${{ number_format($parent->total_paid ?? 0, 2) }}</h4>
                <p class="mb-0">إجمالي المدفوعات</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-warning text-white">
                <h4 class="mb-0">${{ number_format($parent->total_pending ?? 0, 2) }}</h4>
                <p class="mb-0">الدفعات المعلقة</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-info text-white">
                <h4 class="mb-0">{{ $payments->total() }}</h4>
                <p class="mb-0">إجمالي المعاملات</p>
            </div>
        </div>
    </div>
    
    <!-- Payment Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">جميع الدفعات</h5>
        </div>
        <div class="card-body p-0">
            @if($payments && $payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>التاريخ</th>
                                <th>الطالب</th>
                                <th>الدورة</th>
                                <th>المبلغ</th>
                                <th>طريقة الدفع</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->invoice_number ?? 'غير متوفر' }}</strong>
                                    </td>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>{{ $payment->student->name ?? 'غير متوفر' }}</td>
                                    <td>{{ $payment->course->title ?? 'دفع الدورة' }}</td>
                                    <td>
                                        <strong>${{ number_format($payment->amount, 2) }}</strong>
                                    </td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>
                                        @if($payment->payment_status == 'completed')
                                            <span class="badge badge-success">مكتمل</span>
                                        @elseif($payment->payment_status == 'pending')
                                            <span class="badge badge-warning">قيد الانتظار</span>
                                        @elseif($payment->payment_status == 'failed')
                                            <span class="badge badge-danger">فشل</span>
                                        @elseif($payment->payment_status == 'refunded')
                                            <span class="badge badge-info">تم الاسترداد</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($payment->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('parent.payment.show', $payment->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($payment->invoice_path)
                                            <a href="{{ route('parent.payment.invoice', $payment->id) }}" 
                                               class="btn btn-sm btn-outline-success"
                                               title="تحميل الفاتورة">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <h5>لا توجد دفعات حتى الآن</h5>
                    <p class="text-muted">سوف تظهر هنا سجلات دفعاتك</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection