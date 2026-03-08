<?php

namespace Modules\ParentModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentChild extends Model
{
    use HasFactory;

    protected $table = 'parent_children';

    protected $fillable = [
        'parent_id',
        'student_id',
        'relationship_type',
        'is_primary_parent',
        'can_make_payments',
        'can_view_grades',
        'can_view_attendance',
        'can_communicate_teachers',
        'can_enroll_courses',
        'status',
        'approved_by',
        'approved_at',
        'lms_id'
    ];

    protected $casts = [
        'is_primary_parent' => 'boolean',
        'can_make_payments' => 'boolean',
        'can_view_grades' => 'boolean',
        'can_view_attendance' => 'boolean',
        'can_communicate_teachers' => 'boolean',
        'can_enroll_courses' => 'boolean',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent that owns this relationship.
     */
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Get the student in this relationship.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who approved this relationship.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include active relationships.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include primary parents.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary_parent', true);
    }

    /**
     * Approve this parent-child relationship.
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'active',
            'approved_by' => $approvedBy,
            'approved_at' => now()
        ]);
    }

    /**
     * Suspend this parent-child relationship.
     */
    public function suspend()
    {
        $this->update(['status' => 'suspended']);
    }

    /**
     * Deactivate this parent-child relationship.
     */
    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Check if relationship is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if this is the primary parent.
     */
    public function isPrimary()
    {
        return $this->is_primary_parent;
    }
}

