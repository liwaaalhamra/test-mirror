<?php

namespace App\Http\Controllers\Api;

use App;
use App\Helpers\PushHelper;
use App\Http\Controllers\Controller;
use App\Models\Api\Area;
use App\Models\Api\Country;
use App\Models\Api\Region;
use App\Models\Api\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;
use Validator;

class LocationController extends Controller
{

    /**
     * Get Regions
     * @param Request $request
     * @return \stdClass
     */
    public function regions(Request $request) {
//        if(!Input::has('country_id')){
//            return parent::return_error("Missing Country ID", 401, 'messages.missing_parameter');
//        }
//        $regions = Region::whereCountryId($request->country_id)->get();
        $locale = App::getLocale();
        if($locale == 'en'){
            $regions = Region::orderByRaw("LOWER(name_en) asc")->get()->map(function ($value, $key) {
                $value->areas;
                return $value;
            });
        }else{
            $regions = Region::orderByRaw("LOWER(name_ar) asc")->get()->map(function ($value, $key) {
                $value->areas;
                return $value;
            });
        }

        return response()->json($regions);
    }

    public function add(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {
                if(!Input::has('nickname')){
                    return parent::return_error("Missing Nickname", 401, 'messages.missing_parameter');
                }
//                if(!Input::has('area')){
//                    return parent::return_error("Missing Area", 401, 'messages.missing_parameter');
//                }
                if(!Input::has('tag')){
                    return parent::return_error("Missing Tag", 401, 'messages.missing_parameter');
                }
                if(!Input::has('region_id')){
                    return parent::return_error("Missing Region ID", 401, 'messages.missing_parameter');
                }
                if(!$region = Region::where('id','=',$request->region_id)->first()){
                    return parent::return_error("Invalid Region", 402, 'messages.invalid_parameters');
                }
                if(!Input::has('area_id')){
                    return parent::return_error("Missing Area ID", 401, 'messages.missing_parameter');
                }
                if(!$city = Area::whereRegionId($region->id)->where('id','=',$request->area_id)->first()){
                    return parent::return_error("Invalid Area", 402, 'messages.invalid_parameters');
                }
                $tag = strtolower($request->tag);
                if(!in_array($tag, ['work','home','other'])){
                    return parent::return_error("Invalid Tag", 402, 'messages.invalid_parameters');
                }
                $location_type = isset($request->location_type) ? trim($request->location_type) : null;
                $street = isset($request->street) ? trim($request->street) : null;
                $floor = isset($request->floor) ? trim($request->floor) : null;
                $block = isset($request->block) ? trim($request->block) : null;
                $avenue = isset($request->avenue) ? trim($request->avenue) : null;
                $apartment = isset($request->apartment) ? trim($request->apartment) : null;
                $nickname = isset($request->nickname) ? trim($request->nickname) : null;
                $notes = isset($request->notes ) ? trim($request->notes ) : null;
                $building = isset($request->building) ? trim($request->building) : null;
                $latitude = isset($request->latitude) ? floatval($request->latitude) : null;
                $longitude = isset($request->longitude) ? floatval($request->longitude) : null;
                $area = isset($request->area) ? $request->area : null;
                $region_id = isset($request->region_id) ? $request->region_id : null;
                $area_id = isset($request->area_id) ? $request->area_id : null;
                $country_code = null;
                $mobile_number = null;
                if(isset($request->country_code) && isset($request->mobile_number)){
                    $mobile_number = $request->mobile_number;
                    $country_code = $request->country_code;
                    $request->merge([ 'mobile_number' => $country_code.$mobile_number]);

                    $validator = Validator::make($request->all(), [
                        'mobile_number' => 'phone:KW,LB,AE,QA',
                    ]);

                    if ($validator->fails()) {
                        return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
                    }
                    try{
                        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
                    }catch (NumberParseException $exception){
                        return parent::return_error($exception->getMessage(),403,$exception->getMessage());
                    }
                    $country_code = '+'.$phone_details->getCountryCode();
                    $mobile_number = $phone_details->getNationalNumber();
                }


//                if(!Input::has('country_id')){
//                    return parent::return_error("Missing Country ID", 401, "Missing Parameters");
//                }
//                if(!$country = Country::where('id','=',$request->country_id)->first()){
//                    return parent::return_error("Invalid Country", 402, "Invalid Parameters");
//                }

                /**
                 * check if first location to set it as default or check if no default address exists then set it as default
                 */
                $is_default = 1;
                if(count($user->locations) > 0 && UserLocation::whereIsDefault(1)->whereUsersId($user->id)->count() > 0){
                    $is_default = 0;
                }
                $location = UserLocation::create([
                    'tag' => $tag,
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'notes' => $notes,
                    'region_id' => $region_id,
                    'city_id' => $area_id,
                    'street' => $street,
                    'nickname' => $nickname,
                    'floor' => $floor,
                    'area' => $area,
                    'block' => $block,
                    'avenue' => $avenue,
                    'apartment' => $apartment,
                    'country_code' => $country_code,
                    'mobile_number' => $mobile_number,
                    'building' => $building,
                    'users_id' => $user->id,
                    'is_default' => $is_default,
                    'location_type' => $location_type,
                ]);
                return response($location->fresh(), 200);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 100, 'messages.authentication_missing');
        }

    }

    public function edit(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {

                if(!Input::has('nickname')){
                    return parent::return_error("Missing Nickname", 401, 'messages.missing_parameter');
                }
//                if(!Input::has('area')){
//                    return parent::return_error("Missing Area", 401, 'messages.missing_parameter');
//                }
                if(!Input::has('tag')){
                    return parent::return_error("Missing Tag", 401, 'messages.missing_parameter');
                }
                if(!Input::has('region_id')){
                    return parent::return_error("Missing REgion ID", 401, 'messages.missing_parameter');
                }
                if(!$region = Region::where('id','=',$request->region_id)->first()){
                    return parent::return_error("Invalid Region", 402, 'messages.invalid_parameters');
                }
                if(!Input::has('area_id')){
                    return parent::return_error("Missing Area ID", 401, 'messages.missing_parameter');
                }
                if(!$city = Area::whereRegionId($region->id)->where('id','=',$request->area_id)->first()){
                    return parent::return_error("Invalid Area", 402, 'messages.invalid_parameters');
                }
                $tag = strtolower($request->tag);
                if(!in_array($tag, ['work','home','other'])){
                    return parent::return_error("Invalid Tag", 402, 'messages.invalid_parameters');
                }
                $location_type = isset($request->location_type) ? trim($request->location_type) : null;
                $street = isset($request->street) ? trim($request->street) : null;
                $floor = isset($request->floor) ? trim($request->floor) : null;
                $block = isset($request->block) ? trim($request->block) : null;
                $avenue = isset($request->avenue) ? trim($request->avenue) : null;
                $apartment = isset($request->apartment) ? trim($request->apartment) : null;
                $nickname = isset($request->nickname) ? trim($request->nickname) : null;
                $notes = isset($request->notes ) ? trim($request->notes ) : null;
                $building = isset($request->building) ? trim($request->building) : null;
                $latitude = isset($request->latitude) ? floatval($request->latitude) : null;
                $longitude = isset($request->longitude) ? floatval($request->longitude) : null;
                $area = isset($request->area) ? $request->area : null;
                $region_id = isset($request->region_id) ? $request->region_id : null;
                $area_id = isset($request->area_id) ? $request->area_id : null;
                $country_code = null;
                $mobile_number = null;
                if(isset($request->country_code) && isset($request->mobile_number)){
                    $mobile_number = $request->mobile_number;
                    $country_code = $request->country_code;
                    $request->merge([ 'mobile_number' => $country_code.$mobile_number]);

                    $validator = Validator::make($request->all(), [
                        'mobile_number' => 'phone:KW,LB,AE,QA',
                    ]);

                    if ($validator->fails()) {
                        return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
                    }
                    try{
                        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
                    }catch (NumberParseException $exception){
                        return parent::return_error($exception->getMessage(),403,$exception->getMessage());
                    }

                    $country_code = '+'.$phone_details->getCountryCode();
                    $mobile_number = $phone_details->getNationalNumber();
                }
//                if(!Input::has('country_id')){
//                    return parent::return_error("Missing Country ID", 401, "Missing Parameters");
//                }
//                if(!$country = Country::where('id','=',$request->country_id)->first()){
//                    return parent::return_error("Invalid Country", 402, "Invalid Parameters");
//                }


                if(!$location = UserLocation::whereUsersId($user->id)->whereId($request->location_id)->first()){
                    return parent::return_error("Location does not exist", 405, 'messages.invalid_parameters');
                }

                $location->update([
                    'tag' => $tag,
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'notes' => $notes,
                    'region_id' => $region_id,
                    'city_id' => $area_id,
                    'street' => $street,
                    'nickname' => $nickname,
                    'floor' => $floor,
                    'block' => $block,
                    'area' => $area,
                    'avenue' => $avenue,
                    'apartment' => $apartment,
                    'country_code' => $country_code,
                    'mobile_number' => $mobile_number,
                    'building' => $building,
                    'location_type' => $location_type,
                ]);

                return response($location->fresh(), 200);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 100, 'messages.authentication_missing');
        }
    }

    public function delete(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {
                if(!Input::has('location_id')){
                    return parent::return_error("Missing Location ID", 401, 'messages.missing_parameter');
                }
                if(!$location = UserLocation::whereUsersId($user->id)->whereId($request->location_id)->first()){
                    return parent::return_error("Location does not exist", 405, 'messages.invalid_parameters');
                }
                /**
                 * check if deleted location is default => set any other location
                 */
                $is_default = $location->is_default;
                $count = count($user->locations);
                $location->delete();
                if($is_default && $count > 1 ){
                    $any_user_location = UserLocation::whereUsersId($user->id)->first();
                    $any_user_location->is_default = 1;
                    $any_user_location->save();
                }
                return response($user->fresh('locations')->locations, 200);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 100, 'messages.authentication_missing');
        }

    }

    public function get(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {
                return response($user->locations, 200);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 100, 'messages.authentication_missing');
        }
    }

    public function set_as_default(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {
                if(!Input::has('location_id')){
                    return parent::return_error("Missing Location ID", 401, 'messages.missing_parameter');
                }
                if(!$location = UserLocation::whereUsersId($user->id)->whereId($request->location_id)->first()){
                    return parent::return_error("Location does not exist", 405, 'messages.invalid_parameters');
                }
                UserLocation::where('id','<>',$request->location_id)->whereIsDefault(1)->update(['is_default' => 0]);
                $location->is_default = 1;
                $location->save();
                return response($user->locations, 200);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 100, 'messages.authentication_missing');
        }
    }
}
