<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\ContactUs;
use App\Models\Api\ContactUsCategory;
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
use App\Models\Api\TalkToUsCategory;
use App\Models\Api\Terms;
use App\Models\Api\Users;
use App\Models\Api\UserSuggestion;
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

class ContactUsController extends Controller
{
    public function submit(Request $request) {
        if(!Input::has('message')){
            return parent::return_error("Missing Message", 401, 'messages.missing_parameter');
        }
//        if(!Input::has('title')){
//            return parent::return_error("Missing Title", 401, 'messages.missing_parameter');
//        }
        if(!Input::has('category_id')){
            return parent::return_error("Missing Category ID", 401, 'messages.missing_parameter');
        }
        if(!$category = ContactUsCategory::whereId($request->category_id)->first()){
            return parent::return_error("Invalid Category", 402, 'messages.invalid_parameters');
        }
        if(!$request->header('token')){
            if(!Input::has('email')){
                return parent::return_error("Missing Email", 401, 'messages.missing_parameter');
            }
            $user = null;
            $user_id = null;
            $email = $request->email;
        }else{
            if($access_token = parent::valid_access_token($request)) {
                if($user = parent::get_user_by_access_token($access_token)) {
                    $user_id = $user->id;
                    $email = null;
                }else{
                    return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
                }
            }else{
                return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
            }
        }
        ContactUs::create([
            'message' => $request->message,
            'category_id' => $category->id,
            'user_id' => $user_id,
            'email' => $email,
            'title' => $request->title
        ]);
        $info = [];
        // $info['body_title'] = $request->title;
        $info['to'] = $to = explode(',', env('SUGGESTION_EMAILS'));
        $info['title'] = 'Contact Us';
        $info['message'] = "A New Feedback has been sent to you with the following info:";
        $info['user'] = $user;
        $info['body_message'] = $request->message;
        $info['email'] = $email;
        $info['category_name'] = $category->name;
       Mail::send('emails.suggestion', ['data' => $info], function ($message) use ($info) {
           $message->subject($info['title']);
           $message->to($info['to']);
       });

        $client_info = [];
        $client_info['to'] = $to = $user->email;
        $client_info['user'] = $user;
//        $client_info['body_title'] = $request->title;
        $client_info['body_message'] = $request->message;
        $client_info['email'] = $request->email;
        $client_info['category_name'] = $category->name;
        $client_info['message'] = "Thank you for the following feedback:";
        $client_info['title'] = 'Contact Us';
       Mail::send('emails.user_suggestion', ['data' => $client_info], function ($client_message) use ($client_info) {
           $client_message->subject($client_info['title']);
           $client_message->to($client_info['to']);
       });

        $message = utf8_decode(utf8_encode(Lang::get('messages.request_submitted_successfully')));
        return response()->json(['status' => $message]);
    }

    public function categories(Request $request) {
        $categories = ContactUsCategory::get();
        return response()->json($categories);
    }
}
