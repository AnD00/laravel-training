<?php

namespace App\Http\Requests\Job;

use App\Http\Requests\ApiRequest;
use App\Rules\Urlencode;

class SearchRequest extends ApiRequest
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
            'q' => ['required', new Urlencode],
        ];
    }
}
