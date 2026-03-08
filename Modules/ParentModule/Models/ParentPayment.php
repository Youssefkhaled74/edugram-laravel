<?php

namespace Modules\ParentModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;

class ParentPayment extends Model
{
    use HasFactory;

    protected $table = 'parent_payments';

    protected $fillable = [
        'parent_id',
        'student_id',
        'order_id',
        'course_id',
        'enrollment_id',
        'amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'payment_status',
        'transaction_id',
        'gateway_response',
        'payment_date',
        'invoice_number',
        'invoice_path',
        'description',
        'notes',
        'refund_amount',
        'refund_date',
        'refund_reason',
        'lms_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'refund_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent that made this payment.
     */
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Get the student for whom this payment was made.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course associated with this payment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the enrollment associated with this payment.
     */
    public function enrollment()
    {
        return $this->belongsTo(CourseEnrolled::class, 'enrollment_id');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_date' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'notes' => $reason
        ]);
    }

    /**
     * Process refund.
     */
    public function processRefund($amount, $reason = null)
    {
        $this->update([
            'payment_status' => 'refunded',
            'refund_amount' => $amount,
            'refund_date' => now(),
            'refund_reason' => $reason
        ]);
    }

    /**
     * Generate invoice number.
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV-PAR-';
        $date = now()->format('Ymd');
        $lastInvoice = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, -4)) + 1) : 1;
        
        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if payment is refunded.
     */
    public function isRefunded()
    {
        return $this->payment_status === 'refunded';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}

