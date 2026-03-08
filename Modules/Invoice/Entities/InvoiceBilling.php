<?php

namespace Modules\Invoice\Entities;

use App\City;
use App\State;
use App\Country;
use Illuminate\Database\Eloquent\Model;

class InvoiceBilling extends Model
{
    protected $guarded = ['id'];
    
    public function country()
    {
        return $this->belongsTo(Country::class, 'country')->withDefault();
    }

    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country')->withDefault();
    }
    public function stateDetails()
    {
        return $this->belongsTo(State::class, 'state')->withDefault();
    }

    public function cityDetails()
    {
        return $this->belongsTo(City::class, 'city')->withDefault();
    }
}
