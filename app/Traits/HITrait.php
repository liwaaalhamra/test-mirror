<?php

namespace App\Traits;
use App\Models\Api\Restaurant;
use App\Models\Api\SupplierFoodGroup;
use App\Models\Api\Users;
use App\Models\Api\UsersAccess;
use Carbon\Carbon;
use Config;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Lang;

trait HITrait {
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function verifyAndUpload(Request $request, $fieldname = 'image', $directory = 'images' ) {
        //not functional yet
        if( $request->hasFile( $fieldname ) ) {
            if (!$request->file($fieldname)->isValid()) {
                return redirect()->back()->withInput();
            }
            return $request->file($fieldname)->store($directory, 'public');
        }
        return null;
    }

    public function validate_user(Request $request,$excluded_user_ids = []){
        $access_token = $request->header("token");
        if(isset($access_token) && $access_token != "") {
            $user_access = UsersAccess::where("access_token", "=", $access_token)->first();
            if($user_access) {
                $global_excluded_user_ids = [];
                $all_excluded_user_ids =  array_merge($excluded_user_ids,$global_excluded_user_ids);
                if(in_array($user_access->user_id, $all_excluded_user_ids)){
                    return Users::where("id", "=", $user_access->user_id)->first();
                }
                if($user_access->expired_at->isPast()){
                    $error_data['error'] = array();
                    $error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get('messages.invalid_access_token')));
                    $error_data['error']['code']        = 101;
                    $error_data['error']['debugger']    = 'Expired Access Token';
                    $response = response()->json($error_data)->setStatusCode(400);
                    throw (new HttpResponseException($response));
                }
                /**
                 * refresh token
                 */
                $user_access->expired_at = Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s');
                $user_access->save();
                $user_id = $user_access->user_id;
                return Users::where("id", "=", $user_id)->first();
            } else {
                $error_data['error'] = array();
                $error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get('messages.invalid_access_token')));
                $error_data['error']['code']        = 101;
                $error_data['error']['debugger']    = 'Invalid Access Token';
                $response = response()->json($error_data)->setStatusCode(400);
                throw (new HttpResponseException($response));
            }
        } else {
            $error_data['error'] = array();
            $error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get('messages.authentication_missing')));
            $error_data['error']['code']        = 102;
            $error_data['error']['debugger']    = "Missing Access Token";
            $response = response()->json($error_data)->setStatusCode(400);
            throw (new HttpResponseException($response));
        }

    }

    public function validate_merchant(Request $request,$type = 'restaurant',$excluded_user_ids = []){

        $access_token = $request->header("token");
        if(isset($access_token) && $access_token != "") {
            if($type == 'restaurant'){
                $user_access = Restaurant::where("access_token", "=", $access_token)->first();
            }else{
                $user_access = SupplierFoodGroup::where("access_token", "=", $access_token)->first();
            }

            if($user_access) {

                $global_excluded_user_ids = [];
                $all_excluded_user_ids =  array_merge($excluded_user_ids,$global_excluded_user_ids);
                if(in_array($user_access->id, $all_excluded_user_ids)){
                    return $user_access;
                }

                if($user_access->expired_at->isPast()){

                    $error_data['error'] = array();
                    $error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get('messages.invalid_access_token')));
                    $error_data['error']['code']        = 101;
                    $error_data['error']['debugger']    = 'Expired Access Token';
                    $response = response()->json($error_data)->setStatusCode(400);
                    throw (new HttpResponseException($response));
                }
                /**
                 * refresh token
                 */
                $user_access->expired_at = Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s');
                $user_access->save();
                return $user_access->fresh();
            } else {

                $error_data['error'] = array();
                $error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get('messages.invalid_access_token')));
                $error_data['error']['code']        = 101;
                $error_data['error']['debugger']    = 'Invalid Access Token';
                $response = response()->json($error_data)->setStatusCode(400);
                throw (new HttpResponseException($response));
            }
        } else {
            $error_data['error'] = array();
            $error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get('messages.authentication_missing')));
            $error_data['error']['code']        = 102;
            $error_data['error']['debugger']    = "Missing Access Token";
            $response = response()->json($error_data)->setStatusCode(400);
            throw (new HttpResponseException($response));
        }

    }
}