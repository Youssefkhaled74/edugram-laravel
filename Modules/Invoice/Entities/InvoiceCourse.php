<?php

namespace Modules\Invoice\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\CourseSetting\Entities\Course;

class InvoiceCourse extends Model
{
    protected $guarded = ['id'];
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

}
