<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Game;
use App\Models\Api\Latest;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\Rating;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\SalesRequests;
use App\Models\Api\StatusModificationRequest;
use App\Models\Api\Suggestion;
use App\Models\Api\Support;
use App\Models\Api\Terms;
use App\Models\Api\Users;
use App\Models\Api\UserSuggestion;
use App\Traits\HITrait;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\Constraints\PageConstraint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\NetworkHelper;
use App\Helpers\ResponseHelper;
use Lang;
use Validator;

class RatingController extends Controller
{
    use HITrait;
    public function index(Request $request) {
        if(!$request->header('token')){
            if(!Input::has('full_name')){
                return parent::return_error("Missing Full Name", 401, 'messages.missing_parameter');
            }
            if(!Input::has('email')){
                return parent::return_error("Missing Email", 401, 'messages.missing_parameter');
            }
            if(!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){
                return parent::return_error("Invalid Email", 402, 'messages.invalid_parameters');
            }
            $user_id = null;
            $full_name = $request->full_name;
            $email = $request->email;
            $now = Carbon::now()->month;
            if(Rating::whereMonth('created_at','=',$now)->whereEmail($email)->first()){
                return parent::return_error("you can rate once per month",  425, 'messages.rate_once');
            }
        }else{
//            if($access_token = parent::valid_access_token($request)) {
//                if($user = parent::get_user_by_access_token($access_token)) {
                    $user = $this->validate_user($request);
                    $user_id = $user->id;
                    $full_name = null;
                    $email = null;
                    $now = Carbon::now()->month;
                    if(Rating::whereMonth('created_at','=',$now)->whereUserId($user->id)->first()){
                        return parent::return_error("you can rate once per month",  425, 'messages.rate_once');
                    }
//                }else{
//                    return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
//                }
//            }else{
//                return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
//            }
        }

        if(!Input::has('comment')){
            return parent::return_error("Missing Comment", 401, 'messages.missing_parameter');
        }
        if(!Input::has('stars')){
            return parent::return_error("Missing stars", 401, 'messages.missing_parameter');
        }
        if($request->stars < 1 || $request->stars > 5){
            return parent::return_error("Invalid Stars", 402, 'messages.invalid_parameters');
        }

        Rating::create([
            'comment' => $request->comment,
            'full_name' => $full_name,
            'email' => $email,
            'user_id' => $user_id,
            'stars' => $request->stars
        ]);
//                $info = [];
//                $info['to'] = $to = explode(',', env('STATUS_MODIFICATION_EMAILS'));
//                $info['user'] = $user;
//                $info['body_title'] = $request->title;
//                $info['message'] = $request->message;
//                $info['title'] = 'CASINO App - Request For Status Modification';
//                Mail::send('emails.status_modification', ['data' => $info], function ($message) use ($info) {
//                    $message->subject($info['title']);
//                    $message->to($info['to']);
//                });
        $message = utf8_decode(utf8_encode(Lang::get('messages.request_submitted_successfully')));
        return response()->json(['status' => $message]);
    }
}
