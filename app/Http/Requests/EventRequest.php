<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;

class EventRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'latitude' => 'required_with:longitude',
            'longitude' => 'required_with:latitude',
            'start_datetime' => 'required_with:end_datetime',
            'end_datetime' => 'required_with:start_datetime',
        ];
    }
}
