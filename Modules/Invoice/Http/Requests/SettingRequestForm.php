<?php

namespace Modules\Invoice\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequestForm extends FormRequest
{

    public function rules()
    {
        return [
            'prefix'=>'required',
            'footer_text'=>'sometimes|nullable',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
