<?php

namespace Modules\Jitsi\Entities;

use App\Traits\Tenantable;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\VirtualClass\Entities\VirtualClass;

class JitsiMeeting extends Model
{
    use Tenantable;

    protected $guarded = [];
    public function isRunning(){
       return $this->belongsTo();
    }

    public function class()
    {
        return $this->belongsTo(VirtualClass::class, 'class_id')->withDefault();
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id')->withDefault();
    }
}
