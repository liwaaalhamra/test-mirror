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
            'new_password' => 'required|confirmed'

        ];
    }
    public function response(array $errors)
    {
        $data = array("error" => array("debugger" => array_values($errors)[0][0], "code" => 401, "message" => 'Invalid Parameters'));
        return (new Response(json_encode($data), 400))->header('Content-Type', "JSON");
    }
}
