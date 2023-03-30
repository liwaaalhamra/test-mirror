<?php

namespace App\Http\Controllers\api;

use App\Helpers\AuthHeaderHelper;
use App\Helpers\SMSHelper;
use App\Models\Api\Country;
use App\Models\Api\Media;
use App\Models\Api\Setting;
use App\Traits\HITrait;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Api\UserPush;
use App\Models\Api\Users;
use App\Models\Api\UsersAccess;


use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Lang;
use Jenssegers\Agent\Agent;
use Laravel\Socialite\Facades\Socialite;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;
use SoapClient;
use SoapFault;
use SoapHeader;
use Validator;


class LoginController extends Controller
{
    use HITrait;

    public function login_password(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }
        $username = $request->username;
        $password = $request->password;
        if($user = Users::whereAccountType('email')->whereRaw("LOWER(email) = ?",[ strtolower(trim($username))])->where('password',null)->first()){
            return parent::return_error("Registration Error", 400, "Your registration process was interrupted please reset your password first.");
        }
        if(filter_var($username, FILTER_VALIDATE_EMAIL)){
            if(!$user = Users::whereAccountType('email')->whereRaw("LOWER(email) = ?",[ strtolower(trim($username))])->first()){
                return parent::return_error("Email does not exist", 403, "User does not exist.");
            }
        }else{
            if(!$user = Users::whereAccountType('mobile')->where(DB::raw("CONCAT(`country_code`, `primary_phone`)"), '=', $username)->first()){
                return parent::return_error("Mobile Number does not exist", 403, "User does not exist or wrong password.");
            }
        }

        if($user->is_verified == 0){
            return parent::return_error("Registration not complete, please register again", 406, "Registration not complete, please register again");
        }

        if($user->is_blocked){
            return parent::return_error("Account Blocked", 407, "Account Blocked");
        }

        /**
         * password clear text is now sha256
         */
        if( $user->password_clear_text != $password){
            return parent::return_error("Wrong Password", 405, "Wrong Password");
        }


        $agent = new Agent();
        $device_name = $agent->device();
        $browser_name = $agent->browser();
        $browser_version = $agent->version($agent->browser());
        $platform_name = $agent->platform();
        $platform_version = $agent->version($agent->platform());
        $is_mobile = $agent->isMobile();
        $is_tablet = $agent->isTablet();
        $is_desktop = $agent->isDesktop();
        $ip = isset($agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR']) ? $agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR'] : null;
        $user->device_name = $device_name;
        $user->browser_name = $browser_name;
        $user->browser_version = $browser_version;
        $user->platform_name = $platform_name;
        $user->platform_version = $platform_version;
        $user->is_mobile = $is_mobile;
        $user->is_tablet = $is_tablet;
        $user->is_desktop = $is_desktop;
        $user->ip = $ip;
        $user->user_agent = $agent->getUserAgent();
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
    }

    public function login_email(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }
                
        $email = strtolower(trim(Input::get('email')));
        $user = Users::whereAccountType('email')->whereIsVerified(1)->whereEmail($email)->whereNull('deleted_at')->first();
        if($user){
            return parent::return_error("User Already Exists", 403, "User already exists");
        }

        $pin = parent::number_generator(6);
        $registration = parent::generate_tokens(4);
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

        $agent = new Agent();
        $device_name = $agent->device();
        $browser_name = $agent->browser();
        $browser_version = $agent->version($agent->browser());
        $platform_name = $agent->platform();
        $platform_version = $agent->version($agent->platform());
        $is_mobile = $agent->isMobile();
        $is_tablet = $agent->isTablet();
        $is_desktop = $agent->isDesktop();
        $ip = isset($agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR']) ? $agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR'] : null;

        /**
         * if user logged in using google/apple/ facebook(email)
         * account type will be email and concerned social id will be filled
         */
        $user = Users::updateOrCreate(['account_type' => 'email','email' => $email],
            [
                'is_verified' => 0,
                'verified_at' => null,
                'registration' => $registration,
                'pin' => $pin,
                'device_name' => $device_name,
                'browser_name' => $browser_name,
                'browser_version' => $browser_version,
                'platform_name' => $platform_name,
                'platform_version' => $platform_version,
                'is_mobile' => $is_mobile,
                'is_tablet' => $is_tablet,
                'is_desktop' => $is_desktop,
                'user_agent' => $agent->getUserAgent(),
                'ip' => $ip,
            ]);
        if($user->is_blocked){
            return parent::return_error("Account Blocked", 407, "Account Blocked");
        }
        try {
            Mail::send('emails.activate_email', ['data' => $info], function ($message) use ($info) {
                $message->subject(env('APP_NAME')." | ".$info['title']);
                $message->to($info['to']);
            });
        }catch (\Exception $ex){

        }



        $return_body["pin"] = config('app.debug') ? $pin : null;
        $return_body["registration"] = $registration;
        return parent::return_json($return_body);
    }

    public function login_mobile(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'country_code' => 'required',
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }

        $user = Users::whereAccountType('mobile')->whereIsVerified(1)->whereCountryCode($request->country_code)->wherePrimaryPhone($request->phone_number)->first();
        if($user){
            return parent::return_error("User Already Exists", 403, "User already exists");
        }

        $new_country_code = null;
        $new_phone_number = null;
        $country_id = 422; //by default
        $phone_number = $request->phone_number;
        $country_code = $request->country_code;
        $request->merge([ 'mobile_number' => $country_code.$phone_number]);

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'phone:KW,LB,AE,QA'
        ]);

        if ($validator->fails()) {
            return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
        }
        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
        $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
        $country_id = $country ? $country->id : $country_id;
        $new_country_code = '+'.$phone_details->getCountryCode();
        $new_phone_number = $phone_details->getNationalNumber();

        $pin = parent::number_generator(6);
        $pin = 111111;
        $registration = parent::generate_tokens(4);

        $agent = new Agent();
        $device_name = $agent->device();
        $browser_name = $agent->browser();
        $browser_version = $agent->version($agent->browser());
        $platform_name = $agent->platform();
        $platform_version = $agent->version($agent->platform());
        $is_mobile = $agent->isMobile();
        $is_tablet = $agent->isTablet();
        $is_desktop = $agent->isDesktop();
        $ip = isset($agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR']) ? $agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR'] : null;

        /**
         * if user logged in using facebook(mobile)
         * account type will be mobile and concerned social id will be filled
         */
        $user = Users::updateOrCreate(['account_type' => 'mobile','country_code' => $new_country_code,'primary_phone' => $new_phone_number],
            [
                'is_verified' => 0,
                'verified_at' => null,
                'country_id' => $country_id,
                'registration' => $registration,
                'pin' => $pin,
                'device_name' => $device_name,
                'browser_name' => $browser_name,
                'browser_version' => $browser_version,
                'platform_name' => $platform_name,
                'platform_version' => $platform_version,
                'is_mobile' => $is_mobile,
                'is_tablet' => $is_tablet,
                'is_desktop' => $is_desktop,
                'user_agent' => $agent->getUserAgent(),
                'ip' => $ip,
            ]);

        if($user->is_blocked){
            return parent::return_error("Account Blocked", 407, "Account Blocked");
        }
        try {
//            $result = parent::infobip_send_sms($data);
//            $result = parent::SendQpaySMS('Your Activation Code: '.$pin, $new_country_code.$new_phone_number,env('SMS_SOURCE'));
//            if(!$result){
//                return parent::return_error('Failed to Send Pin',455,'messages.failed_send_pin');
//            }
        } catch (SoapFault $fault) {
            return parent::return_error('Failed to Send Pin:'.$fault->faultstring,455,'messages.failed_send_pin');
        }

        $return_body["pin"] = config('app.debug') ? $pin : null;
        $return_body["registration"] = $registration;
        return parent::return_json($return_body);
    }

    public function login_google(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required'

        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }

        try {

            $googleAuthCode = Socialite::driver('google')->getAccessTokenResponse($request->code);
            $google_user = Socialite::driver('google')->userFromToken($googleAuthCode['access_token']);
            $agent = new Agent();
            $device_name = $agent->device();
            $browser_name = $agent->browser();
            $browser_version = $agent->version($agent->browser());
            $platform_name = $agent->platform();
            $platform_version = $agent->version($agent->platform());
            $is_mobile = $agent->isMobile();
            $is_tablet = $agent->isTablet();
            $is_desktop = $agent->isDesktop();
            $ip = isset($agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR']) ? $agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR'] : null;

            $user = Users::updateOrCreate(['google_id' => $google_user->id],
                [
                    'account_type' => 'email',
                    'email' => $google_user->email,
                    'is_verified' => 1,
                    'verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'registration' => null,
                    'pin' => null,
                    'device_name' => $device_name,
                    'browser_name' => $browser_name,
                    'browser_version' => $browser_version,
                    'platform_name' => $platform_name,
                    'platform_version' => $platform_version,
                    'is_mobile' => $is_mobile,
                    'is_tablet' => $is_tablet,
                    'is_desktop' => $is_desktop,
                    'user_agent' => $agent->getUserAgent(),
                    'ip' => $ip,
                ]);
            if($user->is_blocked){
                return parent::return_error("Account Blocked", 407, "Account Blocked");
            }
            UsersAccess::where("user_id", "=", $user->id)->delete();
            $access_token = str_random(64);
            UsersAccess::create([
                'access_token' => $access_token,
                'user_id' => $user->id,
                'expired_at' => Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s')

            ]);
            $user->token = $access_token;
            return parent::return_json($user);


        } catch(\Exception $e){
            return parent::return_error('Unprocessable Entity', 422, $e->getMessage());
        }
    }

    public function login_facebook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required'

        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }

        try {

            $facebook_user = Socialite::driver('facebook')->userFromToken($request->code);
            $agent = new Agent();
            $device_name = $agent->device();
            $browser_name = $agent->browser();
            $browser_version = $agent->version($agent->browser());
            $platform_name = $agent->platform();
            $platform_version = $agent->version($agent->platform());
            $is_mobile = $agent->isMobile();
            $is_tablet = $agent->isTablet();
            $is_desktop = $agent->isDesktop();
            $ip = isset($agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR']) ? $agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR'] : null;

            $user = Users::updateOrCreate(['facebook_id' => $facebook_user->id],
                [
                    'account_type' => 'email',
                    'email' => $facebook_user->email,
                    'is_verified' => 1,
                    'verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'registration' => null,
                    'pin' => null,
                    'device_name' => $device_name,
                    'browser_name' => $browser_name,
                    'browser_version' => $browser_version,
                    'platform_name' => $platform_name,
                    'platform_version' => $platform_version,
                    'is_mobile' => $is_mobile,
                    'is_tablet' => $is_tablet,
                    'is_desktop' => $is_desktop,
                    'user_agent' => $agent->getUserAgent(),
                    'ip' => $ip,
                ]);
            if($user->is_blocked){
                return parent::return_error("Account Blocked", 407, "Account Blocked");
            }
            UsersAccess::where("user_id", "=", $user->id)->delete();
            $access_token = str_random(64);
            UsersAccess::create([
                'access_token' => $access_token,
                'user_id' => $user->id,
                'expired_at' => Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s')

            ]);
            $user->token = $access_token;
            return parent::return_json($user);


        } catch(\Exception $e){
            return parent::return_error('Unprocessable Entity', 422, $e->getMessage());
        }
    }

    public function explore(Request $request){
        $validator = Validator::make($request->all(), [
            'udid' => 'required'

        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }
        $udid = $request->udid;

        $user = Users::where(['udid' => $udid])->orderBy('created_at','desc')->first();
        $agent = new Agent();
        $device_name = $agent->device();
        $browser_name = $agent->browser();
        $browser_version = $agent->version($agent->browser());
        $platform_name = $agent->platform();
        $platform_version = $agent->version($agent->platform());
        $is_mobile = $agent->isMobile();
        $is_tablet = $agent->isTablet();
        $is_desktop = $agent->isDesktop();
        $ip = isset($agent->getHttpHeaders()['HTTP_HOST']) ? $agent->getHttpHeaders()['HTTP_HOST'] : null;
        if(!$user) {
            $user = Users::create([
                'udid' => $udid,
                'device_name' => $device_name,
                'browser_name' => $browser_name,
                'browser_version' => $browser_version,
                'platform_name' => $platform_name,
                'platform_version' => $platform_version,
                'is_mobile' => $is_mobile,
                'is_tablet' => $is_tablet,
                'is_desktop' => $is_desktop,
                'user_agent' => $agent->getUserAgent(),
                'ip' => $ip,
            ]);
        }else{
            $user->device_name = $device_name;
            $user->browser_name = $browser_name;
            $user->browser_version = $browser_version;
            $user->platform_name = $platform_name;
            $user->platform_version = $platform_version;
            $user->is_mobile = $is_mobile;
            $user->is_tablet = $is_tablet;
            $user->is_desktop = $is_desktop;
            $user->user_agent = $agent->getUserAgent();
            $user->ip = $ip;
            $user->save();
        }
        UsersAccess::where("user_id", "=", $user->id)->delete();
        $access_token = str_random(64);
        /**
         * explore must be without expiry that's why we added a big number of minutes
         */
        UsersAccess::create([
            'access_token' => $access_token,
            'user_id' => $user->id,
            'expired_at' => Carbon::now()->addMinutes(200)->format('Y-m-d H:i:s')

        ]);
        $user->fresh();
        $user->token = $access_token;
        return parent::return_json($user);
    }

    public function register(Request $request) {
//        $lang = parent::setLang('en');
        $first_name = null;
        if(Input::has('first_name')){
           $first_name = trim($request->first_name);
        }
        $last_name = null;
        if(Input::has('last_name')){
            $last_name = trim($request->last_name);
        }
        $full_name = null;
        if(Input::has('full_name')){
            $full_name = trim($request->full_name);
        }

        if(!Input::has('email')){
            return parent::return_error('Missing Email',401,'messages.missing_parameter');
        }else{      
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255|unique:users,email',
            ]); 
        }

        if(!Input::has('password')){
            return parent::return_error('Missing Password',401,'messages.missing_parameter');
        }
        if(!Input::has('password_confirm')){
            return parent::return_error('Missing Password Confirmation',401,'messages.missing_parameter');
        }
        if($request->password != $request->password_confirm){
            return parent::return_error('Password Mismatch',401,'Password Mismatch');
        }
        if(!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){       
            return parent::return_error("Invalid Email", 402, 'messages.invalid_parameters');
        }

        $new_country_code = null;
        $new_phone_number = null;
        $country_id = 414;//by default bcz mobile number is optional and app is based in kuwait
        if(Input::has('phone_number') && Input::has('country_code')){
            $phone_number = $request->phone_number;
            $country_code = $request->country_code;
            $request->merge([ 'mobile_number' => $country_code.$phone_number]);

            $validator = Validator::make($request->all(), [
                'mobile_number' =>'phone:KW,LB,AE,QA',
            ]);

            if ($validator->fails()) {
                return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
            }
            $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
            $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
            $country_id = $country ? $country->id : null;
            $new_country_code = '+'.$phone_details->getCountryCode();
            $new_phone_number = $phone_details->getNationalNumber();
        }


        //substr(PhoneNumber::make($request->msisdn, 'IQ')->formatE164(), 1)
        if($request->password != $request->password_confirm){
            return parent::return_error('Invalid Password Confirmation',402,'messages.invalid_parameters');
        }
        $agent = new Agent();
        $device_name = $agent->device();
        $browser_name = $agent->browser();
        $browser_version = $agent->version($agent->browser());
        $platform_name = $agent->platform();
        $platform_version = $agent->version($agent->platform());
        $is_mobile = $agent->isMobile();
        $is_tablet = $agent->isTablet();
        $is_desktop = $agent->isDesktop();
        $ip = isset($agent->getHttpHeaders()['HTTP_HOST']) ? $agent->getHttpHeaders()['HTTP_HOST'] : null;
        if($user = Users::whereRaw("LOWER(email) = ?",[ strtolower(trim(Input::get('email')))])->first()){
            if($user->is_verified == 1){
                return parent::return_error('User Already Exists',419,'messages.user_already_exists');
            }else{
                $user->password = $request->password;
                $user->password_clear_text = $request->password;
                $user->device_name = $device_name;
                $user->browser_name = $browser_name;
                $user->browser_version = $browser_version;
                $user->platform_name = $platform_name;
                $user->platform_version = $platform_version;
                $user->is_mobile = $is_mobile;
                $user->is_tablet = $is_tablet;
                $user->is_desktop = $is_desktop;
                $user->user_agent = $agent->getUserAgent();
                $user->ip = $ip;
                $user->country_id = $country_id;
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->full_name = $full_name;
                $user->country_code = $new_country_code;
                $user->primary_phone = $new_phone_number;
                $user->save();
            }
        }else{
            if(Input::has('udid') && !empty($request->udid)){
                $user = Users::where(['udid' => $request->udid])->orderBy('created_at','desc')->first();
                if($user){
                    $user->email = strtolower(trim(Input::get('email')));
                    $user->password = $request->password;
                    $user->password_clear_text = $request->password;
                    $user->device_name = $device_name;
                    $user->browser_name = $browser_name;
                    $user->browser_version = $browser_version;
                    $user->platform_name = $platform_name;
                    $user->platform_version = $platform_version;
                    $user->is_mobile = $is_mobile;
                    $user->is_tablet = $is_tablet;
                    $user->is_desktop = $is_desktop;
                    $user->user_agent = $agent->getUserAgent();
                    $user->ip = $ip;
                    $user->country_id = $country_id;
                    $user->first_name = $first_name;
                    $user->last_name = $last_name;
                    $user->full_name = $full_name;
                    $user->country_code = $new_country_code;
                    $user->primary_phone = $new_phone_number;
                    $user->save();
                }else{//invalid udid so create new record
                    $user = Users::create([
                        'email' => strtolower(trim(Input::get('email'))),
                        'password' => $request->password,
                        'password_clear_text' => $request->password,
                        'device_name' => $device_name,
                        'browser_name' => $browser_name,
                        'browser_version' => $browser_version,
                        'platform_name' => $platform_name,
                        'platform_version' => $platform_version,
                        'is_mobile' => $is_mobile,
                        'is_tablet' => $is_tablet,
                        'is_desktop' => $is_desktop,
                        'user_agent' => $agent->getUserAgent(),
                        'ip' => $ip,
                        'country_id' => $country_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'full_name' => $full_name,
                        'country_code' => $new_country_code,
                        'primary_phone' => $new_phone_number,
                    ]);
                }

            }else{
                $user = Users::create([
                    'email' => strtolower(trim(Input::get('email'))),
                    'password' => $request->password,
                    'password_clear_text' => $request->password,
                    'device_name' => $device_name,
                    'browser_name' => $browser_name,
                    'browser_version' => $browser_version,
                    'platform_name' => $platform_name,
                    'platform_version' => $platform_version,
                    'is_mobile' => $is_mobile,
                    'is_tablet' => $is_tablet,
                    'is_desktop' => $is_desktop,
                    'user_agent' => $agent->getUserAgent(),
                    'ip' => $ip,
                    'country_id' => $country_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'full_name' => $full_name,
                    'country_code' => $new_country_code,
                    'primary_phone' => $new_phone_number,
                ]);
            }
        }


        $pin = parent::number_generator(6);//to be used in case of mobile number verification and email verification from now on
        $info = [];
        $info['to'] = Input::get('email');

        $info['pin'] = $pin;
        $info['title'] = 'Account Activation';
        $info['message1'] = 'Thank you for using '.env('APP_NAME').' App';
        $info['message2'] = 'Please use the following code to verify your account';
        $info['message3'] = utf8_decode(utf8_encode(Lang::get('messages.sincerely')));
        $info['message4'] = utf8_decode(utf8_encode(Lang::get('messages.team')));

        $info['full_name'] = isset($request->full_name) ? $request->full_name : $request->first_name .' '.$request->last_name;
        $info['code'] = $pin;
        Mail::send('emails.activate_email', ['data' => $info], function ($message) use ($info) {
            $message->subject(env('APP_NAME')." | ".$info['title']);
            $message->to($info['to']);
        });
        $registration = parent::generate_tokens(4);
        $user->registration = $registration;
        $user->pin = $pin;
        $user->save();

        /**
         * below response in case of mobile number flow + email flow
         */
        $return_body["pin"] = $pin;
        $return_body["registration"] = $registration;
        return parent::return_json($return_body);
    }

    public function resend_code(Request $request){
        if(!Input::has('registration')){
            return parent::return_error('Missing Registration',401,'messages.missing_parameter');
        }
        $registration = Input::get('registration');

        if(!Input::has('email')){
            return parent::return_error('Missing Email',401,'messages.missing_parameter');
        }
        $email = Input::get('email');
        if(!$user = Users::where(['email' => $email, 'is_verified' => 0])
            ->orderBy('created_at','desc')
            ->first()){
            return parent::return_error('Invalid Account',401,'Invalid Account');
        }

        if($user->registration != $registration){
            return parent::return_error('Registration not found or not valid',415,'messages.invalid_registration');
        }

        $pin = parent::number_generator(6);//to be used in case of mobile number verification and email verification from now on
        $info = [];
        $info['to'] = Input::get('email');

        $info['pin'] = $pin;
        $info['title'] = 'Account Activation';
        $info['message1'] = 'Thank you for using '.env('APP_NAME').' App';
        $info['message2'] = 'Please use the following code to verify your account';
        $info['message3'] = utf8_decode(utf8_encode(Lang::get('messages.sincerely')));
        $info['message4'] = utf8_decode(utf8_encode(Lang::get('messages.team')));

        $info['full_name'] = isset($request->full_name) ? $request->full_name : $request->first_name .' '.$request->last_name;
        $info['code'] = $pin;
        Mail::send('emails.activate_email', ['data' => $info], function ($message) use ($info) {
            $message->subject(env('APP_NAME')." | ".$info['title']);
            $message->to($info['to']);
        });
        $registration = parent::generate_tokens(4);
        $user->registration = $registration;
        $user->pin = $pin;
        $user->save();

        /**
         * below response in case of mobile number flow + email flow
         */
        $return_body["pin"] = $pin;
        $return_body["registration"] = $registration;
        return parent::return_json($return_body);

    }

    public function activate(Request $request){
        $validator = Validator::make($request->all(), [
            'registration' => 'required',
            'pin' => 'required',
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }

        $registration = Input::get('registration');
        $pin = Input::get('pin');

        $user = Users::where([
            'registration' => $registration,
            'pin' => $pin
        ])->orderBy('created_at','desc')
            ->first();
        if($user){
            $user->pin = null;
            $user->registration = null;
            $user->is_verified = 1;
            $user->verified_at = Carbon::now()->format('Y-m-d H:i:s');
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

    public function logout(Request $request){
        $user = $this->validate_user($request);
        UsersAccess::where("user_id", "=", $user->id)->delete();
        UserPush::where("users_id", "=", $user->id)->delete();
        $message = utf8_decode(utf8_encode(Lang::get('messages.logout_success')));
        return parent::return_json(['status' => $message]);

    }

    public function countries(Request $request) {
        $countries = Country::select('id','name','capital','flag','calling_code','iso_3166_2 as iso','citizenship')
            ->where('visibility','=',1)
            ->orderByRaw("LOWER(name) ASC")
            ->get();
        return response()->json($countries);
    }

    public function nationalities(Request $request) {
        $countries = Country::select('id','name','capital','flag','calling_code','iso_3166_2 as iso','citizenship')
            ->orderByRaw("LOWER(citizenship) ASC")
            ->get();
        return response()->json($countries);
    }

}
