<?php

namespace Modules\Invoice\Http\Requests;

use App\Traits\ValidationMessage;
use Illuminate\Foundation\Http\FormRequest;

class OfflinePaymentRequestForm extends FormRequest
{
    use ValidationMessage;

    public function rules()
    {
        return [
            'method'=>'required',
            'tracking'=>'required',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'type' => 'required',
            'account_number' => 'required',
            'account_holder' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
