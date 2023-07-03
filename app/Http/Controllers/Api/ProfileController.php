<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\ForgotRequest;
use App\Models\Api\Country;
use App\Models\Api\UserProfileRequest;
use App\Models\Api\Users;
use App\Models\Api\UsersAccess;
use App\Traits\HITrait;
use Carbon\Carbon;
use Config;
use DB;
use File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Mail;
use SoapFault;
use Validator;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

class ProfileController extends Controller
{
    use HITrait;
	/**
	 * Get User Profile
	 *
	 * @param Request $request
	 *
	 * @return \stdClass
	 */
	public function get(Request $request) {
        $user = $this->validate_user($request);
        $user->token = $user->access_token->access_token;
        unset($user->access_token);
        return parent::return_json($user);
	}

    public function delete(Request $request) {
        $user = $this->validate_user($request);

        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        /**
         * password clear text is now sha256
         */
        if( $user->password_clear_text != $request->password){
            return parent::return_error("Wrong Password", 405, "Wrong Password");
        }

        $user->delete();
        
        return parent::return_json(['status' => 'Account deleted successfully']);
    }

	public function reviews(Request $request) {
        $user = $this->validate_user($request);
        $reviews_list['all_reviews'] = $user->all_reviews;
        $reviews_list['dining_reviews'] = $user->dining_reviews;
        $reviews_list['delivery_reviews'] = $user->delivery_reviews;
        return parent::return_json($reviews_list);
	}

    public function set(Request $request){
        $user = $this->validate_user($request);
        $validator = Validator::make($request->all(), [
            'password' => [
//                'required',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                'confirmed'
            ]
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, 'Should contain 8 characters or more. At least one Upper case letter, one number and symbol (like ! @ # $ %..)');
        }

        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'profiles';
        $fileName = null;
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                if($user->image != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $user->getPhotoName();
                    File::delete($filePath);
                }
            }
        }

        $password = isset($request->password) ? $request->password : null;
        $first_name = null;
        $last_name = null;
        $full_name = null;
        $registration_number = null;
        $company_name = null;
        $gender = null;
        $dob = null;
        $email = null;
        $new_phone_number = null;
        $new_country_code = null;
        $country_id = null;
        $access_type = null;
        if (Input::has('first_name')) {
            $first_name = $request->first_name;
        }

        if (Input::has('last_name')) {
            $last_name = $request->last_name;
        }

        if (Input::has('full_name')) {
            $full_name = $request->full_name;
        }

        if (Input::has('access_type')) {
            $access_type = $request->access_type;
            $access_possible_values = ['company','individual'];
            if(!in_array($access_type,$access_possible_values)){
                return parent::return_error("Access Wrong Value", 402, 'messages.invalid_parameters');
            }
        }

        $gender = isset($request->gender) && !empty($request->gender) ? $request->gender : null;
        if(isset($request->dob) && !empty($request->dob)){
            $validator = Validator::make($request->all(), [
                'dob' => 'date_format:Y-m-d|before:today',
            ]);

            if ($validator->fails()) {
                return parent::return_error('DOB Invalid Format', 403, 'messages.invalid_dob');
            }
            $dob = $request->dob;
        }

        if(Input::has('gender')){
            $gender_possible_values = ['male','female','other'];
            if(!in_array($request->gender,$gender_possible_values)){
                return parent::return_error("Gender Wrong Value", 402, 'messages.invalid_parameters');
            }
        }

        if($user->account_type == 'email'){
            $email = $user->email;
            if(isset($request->country_code) && isset($request->phone_number)){
                $phone_number = $request->phone_number;
                $country_code = $request->country_code;
                $request->merge([ 'mobile_number' => $country_code.$phone_number]);

                $validator = Validator::make($request->all(), [
                    'mobile_number' => 'phone:KW,LB,AE,QA',
                ]);

                if ($validator->fails()) {
                    return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
                }
                $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
                $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
                $country_id = $country ? $country->id : null;
                $new_country_code = '+'.$phone_details->getCountryCode();
                $new_phone_number = $phone_details->getNationalNumber();

                $pin = parent::number_generator(6);
                $registration = parent::generate_tokens(4);

                $user->profile_registration = $registration;
                $user->profile_pin = $pin;
                try {
//            $result = parent::infobip_send_sms($data);
//            $result = parent::SendQpaySMS('Your Activation Code: '.$pin, $new_country_code.$new_phone_number,env('SMS_SOURCE'));
//            if(!$result){
//                return parent::return_error('Failed to Send Pin',455,'messages.failed_send_pin');
//            }
                } catch (SoapFault $fault) {
                    return parent::return_error('Failed to Send Pin:'.$fault->faultstring,455,'messages.failed_send_pin');
                }
            }
        }else{
            $new_phone_number = $user->primary_phone;
            $new_country_code = $user->country_code;
            $country_id = $user->country_id;
            // if($request->email){
            //     if(!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){
            //         return parent::return_error("Invalid Email", 402, 'messages.invalid_parameters');
            //     }
            //     $email = $request->email;

            //     $pin = parent::number_generator(6);
            //     $registration = parent::generate_tokens(4);

            //     $user->profile_registration = $registration;
            //     $user->profile_pin = $pin;
            //     $info = [];
            //     $info['to'] = Input::get('email');
            //     $info['pin'] = $pin;
            //     $info['title'] = 'Account Activation';
            //     $info['message1'] = 'Thank you for using '.env('APP_NAME').' App';
            //     $info['message2'] = 'Please use the following code to verify your account';
            //     $info['message3'] = utf8_decode(utf8_encode(Lang::get('messages.sincerely')));
            //     $info['message4'] = utf8_decode(utf8_encode(Lang::get('messages.team')));
            //     $info['full_name'] = '';
            //     $info['code'] = $pin;
            //     Mail::send('emails.activate_email', ['data' => $info], function ($message) use ($info) {
            //         $message->subject(env('APP_NAME')." | ".$info['title']);
            //         $message->to($info['to']);
            //     });
            // }
        }
        
        if($request->password){
            $user->password = $password;
            $user->password_clear_text = $password;
        }

        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->full_name = $full_name;
        $user->country_id = $country_id;
        $user->photo = $fileName;
        $user->primary_phone = $new_phone_number;
        $user->country_code = $new_country_code;
        $user->email = $email;
        $user->dob = $dob;
        $user->access_type = $access_type;
        $user->gender = $gender;
//        $user->registration_number = $registration_number;
//        $user->company_name = $company_name;
        $user->is_new_user = 0;
        $user->save();

        $new_user = Users::find($user->id);
        $new_user->token = $new_user->access_token->access_token;
        unset($new_user->access_token);
        return parent::return_json($new_user);
    }

    public function send_activation(Request $request){
        $user = $this->validate_user($request);
        $user->is_profile_verified = 0;
        $user->profile_verified_at = null;
        if($user->account_type == 'email'){
            if(isset($request->country_code) && isset($request->phone_number)){
                $phone_number = $request->phone_number;
                $country_code = $request->country_code;
                $request->merge([ 'mobile_number' => $country_code.$phone_number]);

                $validator = Validator::make($request->all(), [
                    'mobile_number' => 'phone:KW,LB,AE,QA',
                ]);

                if ($validator->fails()) {
                    return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
                }
                $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
                $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
                $country_id = $country ? $country->id : null;
                $new_country_code = '+'.$phone_details->getCountryCode();
                $new_phone_number = $phone_details->getNationalNumber();

                $pin = parent::number_generator(6);
                $registration = parent::generate_tokens(4);
                $user->country_id = $country_id;
                $user->primary_phone = $new_phone_number;
                $user->country_code = $new_country_code;
                $user->profile_registration = $registration;
                $user->profile_pin = $pin;
                $user->save();
                try {
//            $result = parent::infobip_send_sms($data);
//            $result = parent::SendQpaySMS('Your Activation Code: '.$pin, $new_country_code.$new_phone_number,env('SMS_SOURCE'));
//            if(!$result){
//                return parent::return_error('Failed to Send Pin',455,'messages.failed_send_pin');
//            }
                } catch (SoapFault $fault) {
                    return parent::return_error('Failed to Send Pin:'.$fault->faultstring,455,'messages.failed_send_pin');
                }
            }
        }else{
            if($request->email){
                if(!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){
                    return parent::return_error("Invalid Email", 402, 'messages.invalid_parameters');
                }
                $email = $request->email;

                $pin = parent::number_generator(6);
                $registration = parent::generate_tokens(4);

                $user->profile_registration = $registration;
                $user->profile_pin = $pin;
                $user->email = $email;
                $user->save();
                $info = [];
                $info['to'] = Input::get('email');
                $info['pin'] = $pin;
                $info['title'] = 'Account Activation';
                $info['message1'] = 'Thank you for using '.env('APP_NAME').' App';
                $info['message2'] = 'Please use the following code to verify your account';
                $info['message3'] = utf8_decode(utf8_encode(Lang::get('messages.sincerely')));
                $info['message4'] = utf8_decode(utf8_encode(Lang::get('messages.team')));
                $info['full_name'] = '';
                $info['code'] = $pin;
                Mail::send('emails.activate_email', ['data' => $info], function ($message) use ($info) {
                    $message->subject(env('APP_NAME')." | ".$info['title']);
                    $message->to($info['to']);
                });
            }
        }

        $return_body["pin"] = config('app.debug') ? $pin : null;
        $return_body["registration"] = $registration;
        return parent::return_json($return_body);
    }

    public function activate(Request $request){
        $user = $this->validate_user($request);
        $validator = Validator::make($request->all(), [
            'registration' => 'required',
            'pin' => 'required',
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }

        $registration = Input::get('registration');
        $pin = Input::get('pin');

        if($user->profile_registration == $registration && $user->profile_pin == $pin){
            $user->profile_registration = null;
            $user->profile_pin = null;
            $user->is_profile_verified = 1;
            $user->profile_verified_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();
            UsersAccess::where("user_id", "=", $user->id)->delete();
            $access_token = str_random(64);
            UsersAccess::create([
                'access_token' => $access_token,
                'user_id' => $user->id,
                'expired_at' => Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s')
            ]);
            $user->token = $access_token;
            return parent::return_json($user);
        }else{
            return parent::return_error('Registration not found or not valid',415,'messages.invalid_registration');
        }
    }

    /**
     * Set User Profile
     *
     * @param Request $request
     *
     * @return \stdClass
     */
    public function set_request(Request $request){
        $user = $this->validate_user($request);
//        if($access_token = parent::valid_access_token($request)) {
//            if($user = parent::get_user_by_access_token($access_token)) {
                $first_name = $request->first_name ? trim($request->first_name) : null;
                $last_name = $request->last_name ? trim($request->last_name) : null;
                $primary_phone = $request->primary_phone ? trim($request->primary_phone) : null;
                $blood_type = $request->blood_type ? trim($request->blood_type) : null;

                UserProfileRequest::create([
                    'blood_type' => $blood_type,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'primary_phone' => $primary_phone,
                    'user_id' => $user->id
                ]);
                $data = [
                    'to' => explode(',', env('PROFILE_MODIFICATION_EMAILS')),
                    'blood_type' => $blood_type,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'primary_phone' => $primary_phone,
                    'employee_id' => $user->employee_id,
                    'title' => 'Profile Change Request',
                    'message1' => 'A new request has been submitted, please check requested changes:',
                ];

//                Mail::send('emails.change_profile', ['data' => $data], function ($message) use ($data) {
//                    $message->subject(env('APP_NAME') .' | '.$data['title']);
//                    $message->to($data['to']);
//                });
                return parent::return_json(['status' => 'Request Sent Successfully']);
//            }else{
//                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
//            }
//        }else{
//            return parent::return_error("Missing Access Token Or Language ID", 102, 'messages.authentication_missing');
//        }
    }

    public function set_email(Request $request) {
        $user = $this->validate_user($request);
//        if($access_token = parent::valid_access_token($request)) {
//            if($user = parent::get_user_by_access_token($access_token)) {
                if(!Input::has('email')){
                    return parent::return_error('Missing Email',403,'messages.missing_parameter');
                }
                if(!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){
                    return parent::return_error("Invalid Email", 402, 'messages.invalid_parameters');
                }
                if(isset($user->email) && strtolower($user->email) == strtolower($request->email)){
                    return parent::return_error('Email already Verified', 488, 'messages.email_already_verified');
                }
                $user->email = $request->email;
                $user->save();
                $activation_code = str_random(64);
                $user->activation_code = $activation_code;
                $user->save();
                $data = [
                    'to' => $request->get('email'),
                    'code' => secure_url('/api/activate/'.$activation_code),
                    'user' => $user,
                    'title' => 'Email Verification',
                    'message1' => 'Thank you for using Mcdonalds App',
                    'message2' => 'Please click the below link to verify your email',
                    'message3' => utf8_decode(utf8_encode(Lang::get('messages.sincerely'))),
                    'message4' => utf8_decode(utf8_encode(Lang::get('messages.team'))),
                ];

//                Mail::send('emails.activate_email', ['data' => $data], function ($message) use ($data) {
//                    $message->subject(env('APP_NAME') . ' | '.$data['title']);
//                    $message->to($data['to']);
//                });
                return parent::return_json(['status' => utf8_decode(utf8_encode(Lang::get('messages.email_sent_success')))]);
//            }else{
//                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
//            }
//        }else{
//            return parent::return_error("Missing Access Token Or Language ID", 102, 'messages.authentication_missing');
//        }
    }

    public function activate_email($activation_code){
        if($user = Users::where('activation_code','=',$activation_code)->first()){
            if($user->is_verified == 1){
                abort(202);
            }
            /**
             * send email to mcdonalds for profile change request
             */
            $data = [
                'to' => explode(',', env('PROFILE_MODIFICATION_EMAILS')),
                'email' => $user->email,
                'gender' => null,
                'full_name' => null,
                'address' => null,
                'dob' => null,
                'employee_id' => $user->employee_id,
                'title' => 'Profile Change Request',
                'message1' => 'A new request has been submitted, please check requested changes:',
            ];
//
//            Mail::send('emails.change_profile', ['data' => $data], function ($message) use ($data) {
//                $message->subject(env('APP_NAME') .' | '.$data['title']);
//                $message->to($data['to']);
//            });
            $user->is_verified = 1;
            $user->save();
            abort(201);
        }else{
            abort(203);
        }
    }


    public function reset_password(ForgotRequest $request){
        if (empty($request->get('reset_code'))) {
            return parent::return_error("Missing Code", 401, 'messages.missing_parameter');
        }
        if (empty($request->get('verification'))) {
            return parent::return_error("Missing Verification", 401, 'messages.missing_parameter');
        }
        if(!$user = Users::where(['reset_code' => $request->reset_code,'registration' => $request->verification])->first()){
            return parent::return_error("Invalid Verification", 406, 'messages.invalid_code');
        }

        $user->password = $request->get('new_password');
        $user->reset_code = null;
//        $user->preventAttrSet = true;
        $user->password_clear_text = $request->get('new_password');
        $user->save();
        UsersAccess::where("user_id", "=", $user->id)->delete();
        $access_token = str_random(64);
        UsersAccess::create([
            'access_token' => $access_token,
            'user_id' => $user->id,
            'expired_at' => Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s')

        ]);
        $user->token = $user->access_token->access_token;
        unset($user->access_token);
        return parent::return_json($user);
    }

    public function changePassword(Request $request){
        $user = $this->validate_user($request);
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => [
                'required',
                // 'min:8',             // must be at least 8 characters in length
                // 'regex:/[a-z]/',      // must contain at least one lowercase letter
                // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
                // 'regex:/[0-9]/',      // must contain at least one digit
                // 'regex:/[@$!%*#?&]/', // must contain a special character
                'confirmed',
                'string',
                'complex'
            ]
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, 'Should contain 8 characters or more. At least one Upper case letter, one number and symbol (like ! @ # $ %..)');
        }

        if($request->old_password != $user->password_clear_text){
            return parent::return_error("Wrong Password!", 405, 'messages.wrong_password');
        }
        $new_password = hash('sha256', $request->get('new_password'));
        if($new_password == $user->password_clear_text){
            return parent::return_error("Cannot use same password", 405, "Cannot use same password");
        }
        $user->password = $request->get('new_password');
//        $user->preventAttrSet = true;
        $user->password_clear_text = $request->get('new_password');
        $user->save();
        $user->token = $user->access_token->access_token;
        unset($user->access_token);
        return parent::return_json(['status' => utf8_decode(utf8_encode(Lang::get('messages.password_changed_success')))]);
    }

    public function sendPasswordCode(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required'
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }
        $username = $request->username;
        $user = Users::where('email',$username)->orWhere('primary_phone', $username)->first();
        
        if(!$user){
            return parent::return_error("Email does not exist", 403, "User does not exist");
        }

        if($user->is_verified == 0){
            return parent::return_error("Registration not complete, please register again", 406, "Registration not complete, please register again");
        }

        if($user->is_blocked){
            return parent::return_error("Account Blocked", 407, "Account Blocked");
        }

        $reset_code = parent::number_generator(6);
        $registration = parent::generate_tokens(4);
        if($user->account_type == 'email'){
            $info = [];
            $info['to'] = $username;
            $info['title'] = 'Reset Password';
            $info['message1'] = 'You requested a password reset for your account : '.Input::get('email');
            $info['message2'] = 'Please use the following code in your application to reset your password';
            $info['message3'] = utf8_decode(utf8_encode(Lang::get('messages.sincerely')));
            $info['message4'] = utf8_decode(utf8_encode(Lang::get('messages.team')));

            $info['full_name'] = $user->full_name ? $user->full_name : $user->first_name .' '.$user->last_name;
            $info['code'] = $reset_code;
            Mail::send('emails.activate_email', ['data' => $info], function ($message) use ($info) {
                $message->subject(env('APP_NAME')." | ".$info['title']);
                $message->to($info['to']);
            });
        }else{
            try {
                $new_country_code = $user->country_code;
                $new_phone_number = $user->primary_phone;
//            $result = parent::infobip_send_sms($data);
//            $result = parent::SendQpaySMS('Your Password Reset Code: '.$reset_code, $new_country_code.$new_phone_number,env('SMS_SOURCE'));
//            if(!$result){
//                return parent::return_error('Failed to Send Pin',455,'messages.failed_send_pin');
//            }
            } catch (SoapFault $fault) {
                return parent::return_error('Failed to Send Pin:'.$fault->faultstring,455,'messages.failed_send_pin');
            }
        }

        $user->reset_code = $reset_code;
        $user->registration = $registration;
        $user->save();
        $message = utf8_decode(utf8_encode(Lang::get('messages.email_sent_success')));
        $return_body["reset_code"] = config('app.debug') ? $reset_code : null;
        $return_body["verification"] = $registration;
        $return_body["status"] = $message;
        return parent::return_json($return_body);
    }

    public function verify_reset_code(Request $request){
        if (empty($request->get('reset_code'))) {
            return parent::return_error("Missing Code", 401, 'messages.missing_parameter');
        }
        if (empty($request->get('verification'))) {
            return parent::return_error("Missing Verification", 401, 'messages.missing_parameter');
        }
        if(!$user = Users::where(['reset_code' => $request->reset_code,'registration' => $request->verification])->first()){
            return parent::return_error("Invalid Verification", 406, 'messages.invalid_code');
        }
        $registration = parent::generate_tokens(4);
        /**
         * generate new verification code for next step and set reset code as null
         * in next step check verification code and reset code must be null to make sure he passed by this step
         */
        $user->registration = $registration;
        $user->reset_code = null;
        $user->save();
        $message = 'Reset Code verified successfully';
        return parent::return_json(['status' => $message,'verification' => $registration]);
    }

    public function update_reset_password(ForgotRequest $request){
        if (empty($request->get('verification'))) {
            return parent::return_error("Missing Verification", 401, 'messages.missing_parameter');
        }
        if(!$user = Users::whereNull('reset_code')->where(['registration' => $request->verification])->first()){
            return parent::return_error("Invalid Verification", 406, 'messages.invalid_code');
        }

        $user->password = $request->get('new_password');
        $user->registration = null;
        $user->preventAttrSet = true;
        $user->password_clear_text = $request->get('new_password');
        $user->save();
        UsersAccess::where("user_id", "=", $user->id)->delete();
        $access_token = str_random(64);
        UsersAccess::create([
            'access_token' => $access_token,
            'user_id' => $user->id,
            'expired_at' => Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s')

        ]);
        $user->token = $user->access_token->access_token;
        unset($user->access_token);
        return parent::return_json($user);
    }
}
