<?php

namespace Modules\ParentModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentNotification extends Model
{
    use HasFactory;

    protected $table = 'parent_notifications';

    protected $fillable = [
        'parent_id',
        'student_id',
        'notification_type',
        'priority',
        'title',
        'message',
        'action_url',
        'action_text',
        'related_id',
        'related_type',
        'is_read',
        'read_at',
        'sent_via_email',
        'sent_via_sms',
        'email_sent_at',
        'sms_sent_at',
        'lms_id'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_via_email' => 'boolean',
        'sent_via_sms' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'sms_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent that owns this notification.
     */
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Get the student related to this notification.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to filter by notification type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Mark as sent via email.
     */
    public function markEmailSent()
    {
        $this->update([
            'sent_via_email' => true,
            'email_sent_at' => now()
        ]);
    }

    /**
     * Mark as sent via SMS.
     */
    public function markSmsSent()
    {
        $this->update([
            'sent_via_sms' => true,
            'sms_sent_at' => now()
        ]);
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute()
    {
        $icons = [
            'grade' => 'fa-star',
            'attendance' => 'fa-calendar-check',
            'payment' => 'fa-credit-card',
            'enrollment' => 'fa-user-plus',
            'certificate' => 'fa-certificate',
            'quiz' => 'fa-question-circle',
            'assignment' => 'fa-tasks',
            'announcement' => 'fa-bullhorn',
            'message' => 'fa-envelope',
            'alert' => 'fa-exclamation-triangle'
        ];

        return $icons[$this->notification_type] ?? 'fa-bell';
    }

    /**
     * Get notification color based on priority.
     */
    public function getColorAttribute()
    {
        $colors = [
            'low' => 'info',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger'
        ];

        return $colors[$this->priority] ?? 'primary';
    }

    /**
     * Get time ago format.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}

