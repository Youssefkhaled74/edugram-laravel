<?php

namespace Modules\Invoice\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequestForm extends FormRequest
{
    public function rules()
    {
        return [
            'student'=>'required|integer',
            'payment_type'=>'required|integer',
            'courses'=>'required|array',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
