<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;


class ForgotRequest extends Request
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
//            'reset_code' => 'required',
            // 'new_password' => 'required|confirmed'
            'new_password' => [
                'required',
                'confirmed',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                'confirmed'
            ]
        ];
    }

    public function response(array $errors)
    {
        $data = array("error" => array("debugger" => array_values($errors)[0][0], "code" => 401, "message" => 'Password should contain 8 characters or more. At least one Upper case letter, one number and symbol (like ! @ # $ %..)'));
        return (new Response(json_encode($data), 400))->header('Content-Type', "JSON");
    }
}
