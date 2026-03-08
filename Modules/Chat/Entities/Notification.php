<?php

namespace Modules\Chat\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;
use Ramsey\Uuid\Uuid;
use App\Traits\Tenantable;


class Notification extends Model
{
    use Tenantable;

    protected $table = 'chat_notifications';

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }

    protected $casts = [
        'data' => 'array',
    ];

    protected static function newFactory()
    {
        return \Modules\Chat\Database\factories\NotificationFactory::new();
    }

    public function getDataAttribute($value) {
        return json_decode($value);
    }
}
