<?php

namespace Modules\ParentModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'national_id',
        'occupation',
        'workplace',
        'address',
        'emergency_contact',
        'emergency_contact_name',
        'is_verified',
        'verification_documents',
        'status',
        'lms_id'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the parent profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all children associated with this parent.
     */
    public function children()
    {
        return $this->belongsToMany(
            User::class,
            'parent_children',
            'parent_id',
            'student_id'
        )->withPivot([
            'relationship_type',
            'is_primary_parent',
            'can_make_payments',
            'can_view_grades',
            'can_view_attendance',
            'can_communicate_teachers',
            'can_enroll_courses',
            'status',
            'approved_by',
            'approved_at'
        ])->withTimestamps();
    }

    /**
     * Get all active children.
     */
    public function activeChildren()
    {
        return $this->children()->wherePivot('status', 'active');
    }

    /**
     * Get all parent-child relationships.
     */
    public function parentChildren()
    {
        return $this->hasMany(ParentChild::class, 'parent_id');
    }

    /**
     * Get all payments made by this parent.
     */
    public function payments()
    {
        return $this->hasMany(ParentPayment::class, 'parent_id');
    }

    /**
     * Get all notifications for this parent.
     */
    public function notifications()
    {
        return $this->hasMany(ParentNotification::class, 'parent_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    /**
     * Get all student requests made by this parent.
     */
    public function studentRequests()
    {
        return $this->hasMany(ParentStudentRequest::class, 'parent_id');
    }

    /**
     * Get pending student requests.
     */
    public function pendingRequests()
    {
        return $this->studentRequests()->where('status', 'pending');
    }

    /**
     * Check if parent can perform action for a specific child.
     */
    public function canPerformAction($studentId, $action)
    {
        $relationship = $this->parentChildren()
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        if (!$relationship) {
            return false;
        }

        $permissionMap = [
            'make_payments' => 'can_make_payments',
            'view_grades' => 'can_view_grades',
            'view_attendance' => 'can_view_attendance',
            'communicate_teachers' => 'can_communicate_teachers',
            'enroll_courses' => 'can_enroll_courses',
        ];

        $permission = $permissionMap[$action] ?? null;

        return $permission ? $relationship->{$permission} : false;
    }

    /**
     * Get total amount paid by this parent.
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('payment_status', 'completed')->sum('amount');
    }

    /**
     * Get total pending payments.
     */
    public function getTotalPendingAttribute()
    {
        return $this->payments()->where('payment_status', 'pending')->sum('amount');
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadNotificationCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Get full name from user relationship.
     */
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    /**
     * Get email from user relationship.
     */
    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get phone from user relationship.
     */
    public function getPhoneAttribute()
    {
        return $this->user ? $this->user->phone : null;
    }
}

