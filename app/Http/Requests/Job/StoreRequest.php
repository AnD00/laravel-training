<?php

namespace App\Http\Requests\Job;

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
            'category' => ['required', 'max:20'],
            'detail' => ['required', 'max:2000'],
            'company_id' => ['required'],
        ];
    }
}
