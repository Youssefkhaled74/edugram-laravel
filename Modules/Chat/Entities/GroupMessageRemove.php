<?php

namespace Modules\Chat\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Tenantable;


class GroupMessageRemove extends Model
{
    use Tenantable;

    protected $table = 'chat_group_message_removes';


    protected $fillable = [
        'group_message_recipient_id',
        'user_id'
    ];

    protected static function newFactory()
    {
        return \Modules\Chat\Database\factories\GroupMessageRemoveFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
