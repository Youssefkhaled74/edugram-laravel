<?php

namespace Modules\Invoice\Entities;

use App\User;
use Illuminate\Support\Facades\Crypt;
use Modules\Payment\Entities\Checkout;
use Illuminate\Database\Eloquent\Model;
use Modules\Invoice\Entities\InvoiceCourse;
use Modules\Invoice\Entities\InvoiceOfflinePayment;

class Invoice extends Model
{
    protected $guarded = ['id'];

    public function checkout()
    {
        return $this->belongsTo(Checkout::class, 'checkout_id', 'id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }
    public function courses()
    {
        return $this->hasMany(InvoiceCourse::class, 'invoice_id', 'id');
    }
    public function billing()
    {
        return $this->belongsTo(InvoiceBilling::class, 'billing_detail_id', 'id')->withDefault();
    }
    public function offlinePayment()
    {
        return $this->hasOne(InvoiceOfflinePayment::class, 'invoice_id', 'id')->where('status', 0);
    }
    public function encryptId()
    {
        return Crypt::encrypt($this->id);
    }
}
