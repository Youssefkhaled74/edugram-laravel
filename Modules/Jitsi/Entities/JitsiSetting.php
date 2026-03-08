<?php

namespace Modules\Jitsi\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class JitsiSetting extends Model
{
    use Tenantable;

    protected static $flushCacheOnUpdate = true;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('JitsiSetting_' . SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('JitsiSetting_' . SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('JitsiSetting_' . SaasDomain());
        });
    }


    public static function getData()
    {
        return Cache::rememberForever('JitsiSetting_' . SaasDomain(), function () {
            $setting = DB::table('jitsi_settings')->where('lms_id', SaasInstitute()->id)->first();
            if (!$setting) {
                $setting = DB::table('jitsi_settings')->first();
            }
            return $setting;
        });
    }
}
