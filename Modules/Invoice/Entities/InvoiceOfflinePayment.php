<?php

namespace Modules\Invoice\Entities;

use App\User;
use Modules\Invoice\Entities\Invoice;
use Illuminate\Database\Eloquent\Model;

class InvoiceOfflinePayment extends Model
{
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id')->withDefault();
    }
}
