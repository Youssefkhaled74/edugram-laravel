<?php

namespace Modules\Jitsi\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;


class JitsiMeetingUser extends Model
{
    use Tenantable;

    protected static $flushCacheOnUpdate = true;

    protected $fillable = [];
}
