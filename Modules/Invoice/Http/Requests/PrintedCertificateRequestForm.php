<?php

namespace Modules\Invoice\Http\Requests;

use App\Traits\ValidationMessage;
use Illuminate\Foundation\Http\FormRequest;

class PrintedCertificateRequestForm extends FormRequest
{
    use ValidationMessage;

    public function rules()
    {
        return [
            'title'=>'sometimes|nullable',
            'price'=>'required|numeric',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
