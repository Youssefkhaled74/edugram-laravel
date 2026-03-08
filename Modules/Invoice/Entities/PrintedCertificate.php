<?php

namespace Modules\Invoice\Entities;

use Illuminate\Database\Eloquent\Model;

class PrintedCertificate extends Model
{
    protected $fillable = ['title', 'price'];
}
