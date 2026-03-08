<?php

namespace Modules\Invoice\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingUpdateRequestForm extends FormRequest
{
    public function rules()
    {
        return [
            "tracking_id"=>'required|string',
            "billing_detail_id"=>'required|integer',
            "first_name"=>'required|string',
            "last_name"=>'required',
            "company_name"=>'sometimes|nullable',
            "country"=>'sometimes|nullable|integer',
            "state"=>'sometimes|nullable|integer',
            "city"=>'sometimes|nullable|string',
            "address1"=>'sometimes|nullable',
            "address2"=>'sometimes|nullable',
            "zip_code"=>'sometimes|nullable|integer',
            "details"=>'sometimes|nullable',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
