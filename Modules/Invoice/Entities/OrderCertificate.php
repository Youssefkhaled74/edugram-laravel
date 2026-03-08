<?php

namespace Modules\Invoice\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\CourseSetting\Entities\Course;

class OrderCertificate extends Model
{
    protected $fillable = ['checkout_id', 'course_id', 'status', 'accepted', 'tracking', 'certificate_id', 'user_id', 'price', 'payment_status'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->withDefault();
    }
}
