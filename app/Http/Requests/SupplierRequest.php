<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Route;

class SupplierRequest extends Request
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
        $route_name = Route::currentRouteName();
        if(strpos($route_name,'store') !== false){//create
            return [
                'password' => [
                    'required',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                    'confirmed'
                ],
                'username' => 'required|unique:restaurants,username',
                'category_id' => 'required',
                'name_en' => 'required',
                'name_ar' => 'required',
//                'cost' => 'required',
                'country_code' => 'required',
                'phone_number' => 'required',
            ];

        }else{//update
            return [
                'username' => 'required',
                'category_id' => 'required',
                'name_en' => 'required',
                'name_ar' => 'required',
//                'cost' => 'required',
                'country_code' => 'required',
                'phone_number' => 'required',
            ];
        }
    }
}
