<?php

namespace Modules\Invoice\Entities;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = ['prefix', 'footer_text'];
}
