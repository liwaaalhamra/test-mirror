<?php

namespace App\Http\Controllers\Api;

use App;
use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\AboutUs;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Game;
use App\Models\Api\Latest;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\SalesRequests;
use App\Models\Api\Setting;
use App\Models\Api\Terms;
use App\Models\Api\Users;
use Illuminate\Foundation\Testing\Constraints\PageConstraint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\NetworkHelper;
use App\Helpers\ResponseHelper;
use Validator;

class SettingsController extends Controller
{
	/**
	 * Get Settings
	 * @param Request $request
	 * @return \stdClass
	 */
	public function index(Request $request) {
        $settings = Setting::all();
        $info = [];
        $version_update_title = null;
        $version_update_message = null;
        $locale = App::getLocale();
        foreach ($settings as $setting){
            $key = $setting->meta_name;
            $value = $setting->meta_value;
            if(in_array($key, ['ios_force_update','android_force_update'])){
                $value = ($value == 'on') ? true : false;
            }elseif (in_array($key, [])){
                continue;
            }elseif (in_array($key, []) || strpos($key,'width') !== false || strpos($key,'height') !== false){
                $value = intval($value);
            }elseif (in_array($key, ['android_version']) || strpos($key,'delivery') !== false){
                $value = floatval($value);
            }elseif (in_array($key, []) || strpos($key,'image') !== false){
                $value = secure_url('public/uploads/images/settings/'.$value);
            }elseif ($key == 'force_update_message_ar' && $locale == 'ar'){
                $version_update_message = $value;
            }elseif ($key == 'force_update_message_en' && $locale == 'en'){
                $version_update_message = $value;
            }elseif ($key == 'force_update_title_ar' && $locale == 'ar'){
                $version_update_title = $value;
            }elseif ($key == 'force_update_title_en' && $locale == 'en'){
                $version_update_title = $value;
            }elseif ($key == 'post_cost'){
                $value = env('APP_CURRENCY').' '.number_format(floatval($value));
            }elseif ($key == 'post_validity'){
                $value = $value.' Days';
            }
            $info['force_update_title'] = $version_update_title;
            $info['force_update_message'] = $version_update_message;
            $info[$key] = $value;
        }
        $info['currency'] = env('APP_CURRENCY');
        unset($info['force_update_message_ar']);
        unset($info['force_update_message_en']);
        unset($info['force_update_title_ar']);
        unset($info['force_update_title_en']);
        return response()->json($info);
	}
}
