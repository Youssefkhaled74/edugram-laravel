<?php

namespace Modules\Chat\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlockUser extends Model
{
    use Tenantable;

    protected $table = 'chat_block_users';

    protected $fillable = [
        'block_by',
        'block_to'
    ];

    protected static function newFactory()
    {
        return \Modules\Chat\Database\factories\BlockUserFactory::new();
    }
}
