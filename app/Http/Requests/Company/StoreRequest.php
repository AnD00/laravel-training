<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\ApiRequest;

class StoreRequest extends ApiRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:100'],
            'address' => ['required', 'max:200'],
        ];
    }
}
