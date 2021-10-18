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
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\SalesRequests;
use App\Models\Api\StatusModificationRequest;
use App\Models\Api\Suggestion;
use App\Models\Api\Support;
use App\Models\Api\Terms;
use App\Models\Api\TotalLossReport;
use App\Models\Api\Users;
use App\Models\Api\UserSuggestion;
use App\Traits\HITrait;
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

class SuggestionController extends Controller
{
    use HITrait;
    public function index(Request $request) {
        if(!Input::has('message')){
            return parent::return_error("Missing Message", 401, 'messages.missing_parameter');
        }
//        if(!Input::has('title')){
//            return parent::return_error("Missing Title", 401, 'messages.missing_parameter');
//        }

        if(!$request->header('token')){
            if(!Input::has('email')){
                return parent::return_error("Missing Email", 401, 'messages.missing_parameter');
            }
            $user_id = null;
            $email = $request->email;
        }else{
//            if($access_token = parent::valid_access_token($request)) {
//                if($user = parent::get_user_by_access_token($access_token)) {
                    $user = $this->validate_user($request);
                    $user_id = $user->id;
                    $email = null;
//                }else{
//                    return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
//                }
//            }else{
//                return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
//            }
        }
        Suggestion::create([
            'message' => $request->message,
            'user_id' => $user_id,
            'email' => $request->email,
            'title' => empty($request->title) ? null : $request->title
        ]);
        $info = [];
        $info['to'] = $to = explode(',', env('SUGGESTION_EMAILS'));
        $info['user'] = $user;
        $info['body_title'] = empty($request->title) ? null : $request->title;
        $info['message'] = $request->message;
        $info['email'] = $request->email;
        $info['message'] = "A Suggestion Message has been sent to you with the following info:";
        $info['title'] = 'Suggestion';
        Mail::send('emails.suggestion', ['data' => $info], function ($message) use ($info) {
            $message->subject($info['title']);
            $message->to($info['to']);
        });
        $message = utf8_decode(utf8_encode(Lang::get('messages.request_submitted_successfully')));
        return response()->json(['status' => $message]);
    }

    public function total_loss_report(Request $request) {
//        if($access_token = parent::valid_access_token($request)) {
//            if($user = parent::get_user_by_access_token($access_token)) {
                $user = $this->validate_user($request);
                if(!Input::has('message')){
                    return parent::return_error("Missing Message", 401, 'messages.missing_parameter');
                }
                if(!Input::has('policy_number')){
                    return parent::return_error("Missing Policy Number", 401, 'messages.missing_parameter');
                }
                if(!Input::has('phone_number')){
                    return parent::return_error("Missing Phone Number", 401, 'messages.missing_parameter');
                }
                if(!Input::has('email')){
                    return parent::return_error("Missing Email", 401, 'messages.missing_parameter');
                }
                if(!filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL)){
                    return parent::return_error("Invalid Email", 402, 'messages.invalid_parameters');
                }
                TotalLossReport::create([
                    'message' => $request->message,
                    'user_id' => $user->id,
                    'email' => trim($request->email),
                    'phone_number' => trim($request->phone_number),
                    'policy_number' => trim($request->policy_number),
                ]);

                $message = utf8_decode(utf8_encode(Lang::get('messages.request_submitted_successfully')));
                return response()->json(['status' => $message]);
//            }else{
//                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
//            }
//        }else{
//            return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
//        }


    }
}
