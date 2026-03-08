<?php

namespace Modules\ParentModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentStudentRequest extends Model
{
    use HasFactory;

    protected $table = 'parent_student_requests';

    protected $fillable = [
        'parent_id',
        'student_email',
        'student_id',
        'student_name',
        'student_dob',
        'student_national_id',
        'relationship_type',
        'request_type',
        'status',
        'supporting_documents',
        'admin_notes',
        'rejection_reason',
        'processed_by',
        'processed_at',
        'lms_id'
    ];

    protected $casts = [
        'student_dob' => 'date',
        'supporting_documents' => 'array',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent that made this request.
     */
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Get the student associated with this request.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the admin who processed this request.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Approve this request and create parent-child relationship.
     */
    public function approve($processedBy, $adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $processedBy,
            'processed_at' => now(),
            'admin_notes' => $adminNotes
        ]);

        // Create parent-child relationship
        if ($this->student_id) {
            ParentChild::create([
                'parent_id' => $this->parent_id,
                'student_id' => $this->student_id,
                'relationship_type' => $this->relationship_type,
                'status' => 'active',
                'approved_by' => $processedBy,
                'approved_at' => now(),
                'lms_id' => $this->lms_id
            ]);
        }

        return true;
    }

    /**
     * Reject this request.
     */
    public function reject($processedBy, $rejectionReason, $adminNotes = null)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $processedBy,
            'processed_at' => now(),
            'rejection_reason' => $rejectionReason,
            'admin_notes' => $adminNotes
        ]);

        return true;
    }

    /**
     * Cancel this request.
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if request is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if request is for linking existing student.
     */
    public function isLinkExisting()
    {
        return $this->request_type === 'link_existing';
    }

    /**
     * Check if request is for creating new student.
     */
    public function isCreateNew()
    {
        return $this->request_type === 'create_new';
    }
}

