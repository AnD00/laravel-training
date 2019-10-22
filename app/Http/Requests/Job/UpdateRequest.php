<?php

namespace App\Http\Requests\Job;

use App\Http\Requests\ApiRequest;

class UpdateRequest extends ApiRequest
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
            'name' => ['filled', 'max:100'],
            'category' => ['filled', 'max:20'],
            'detail' => ['filled', 'max:2000'],
        ];
    }
}
