<?php

namespace Modules\Chat\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Tenantable;


class Invitation extends Model
{
    use Tenantable;

    protected $table = 'chat_invitations';

    protected $fillable = [
        'from',
        'to',
        'status'
    ];

    protected static function newFactory()
    {
        return \Modules\Chat\Database\factories\InvitationFactory::new();
    }

    public function requestFrom()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function requestTo()
    {
        return $this->belongsTo(User::class, 'to');
    }
}
