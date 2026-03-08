<?php

namespace Modules\Chat\Entities;

use App\Traits\Tenantable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{

    use SoftDeletes, Tenantable;

    protected $table = 'chat_conversations';

    protected $fillable = [
        'from_id',
        'to_id',
        'message',
        'message_type',
        'status',
        'file_name',
        'to_type',
        'reply_to',
        'initial',
        'original_file_name',
        'reply',
        'forward',
        'deleted_by_to'
    ];

    protected static function newFactory()
    {
        return \Modules\Chat\Database\factories\ConversationFactory::new();
    }

    public function getCreatedAtDiffHumanAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function reply()
    {
        return $this->belongsTo(Conversation::class, 'reply', 'id');
    }

    public function forwardFrom()
    {
        return $this->belongsTo(Conversation::class, 'forward', 'id', );
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function forMe()
    {
        return $this->to_id === auth()->id();
    }

    public function fromMe()
    {
        return $this->from_id === auth()->id();
    }
}
