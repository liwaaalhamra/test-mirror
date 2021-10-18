<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\AboutUs;
use App\Models\Api\Area;
use App\Models\Api\Expert;
use App\Models\Api\ExpertArea;
use App\Models\Api\Garage;
use App\Models\Api\Inbox;
use App\Models\Api\Media;
use App\Models\Api\Order;
use App\Models\Api\OrderStatus;
use App\Models\Api\Region;
use App\Models\Api\Restaurant;
use App\Models\Api\UserExpertRequest;
use App\Models\Api\UserExpertRequestThirdParty;
use App\Models\Api\UserPush;
use App\Models\Api\UserReservation;
use App\Models\Api\VehicleBrand;
use App\Traits\HITrait;
use Carbon\Carbon;
use Config;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Lang;
use libphonenumber\NumberParseException;
use Mail;
use Validator;
use Propaganistas\LaravelPhone\PhoneNumber;

class RestaurantsController extends Controller
{
    use HITrait;

    public function login(Request $request){
        if(!Input::has('username')){
            return parent::return_error('Missing Username',401,'messages.missing_parameter');
        }
        if(!Input::has('password')){
            return parent::return_error('Missing Password',401,'messages.missing_parameter');
        }
        $hashed_password = hash('sha256', $request->password);
        $merchant = Restaurant::where('username','=',$request->username)->where('password','=',$hashed_password)->first();
        if(!$merchant){
            return parent::return_error("Invalid Credentials", 405, "Invalid Credentials");
        }
        $access_token = str_random(64);
        $merchant->access_token = $access_token;
        $merchant->expired_at = Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s');
        $merchant->save();
        $merchant_response = $merchant->fresh();
        $merchant_response->token = $access_token;
        return parent::return_json($merchant_response);
    }

    public function get(Request $request) {
        $user = $this->validate_merchant($request);
        $profile['access_token'] = $user->access_token;
        $profile['is_closed'] = $user->is_closed;
        $profile['name'] = $user->name;
        $profile['image'] = $user->image;
        $profile['width'] = $user->width;
        $profile['height'] = $user->height;
        $profile['description'] = $user->description;
        return parent::return_json($profile);
    }

    public function reset_orders(Request $request) {
        $merchant = $this->validate_merchant($request);
        /**
         * set order as delivered if status is not new or rejected or delivered
         * new_order bcz no action still taken
         * rejected bcz already rejected, we cannot set it as delivered
         * delivered bcz it's already delivered
         */
        foreach (Order::whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereNotIn('status',['new_order','rejected','delivered'])->get() as $cleared_order){
            $cleared_order->status = 'delivered';
            $status_at = $cleared_order->status_at;
            if(isset($status_at['delivered_at'])){
                $status_at['delivered_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }else{
                $status_at['delivered_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }
            $cleared_order->status_at = $status_at;
            $cleared_order->save();
            /**
             * send push to user
             */
            $merchant_full_name = $merchant->name_en;
            $merchant_phone_number = $merchant->country_code.$merchant->phone_number;
            $title = 'Order Delivered';
            $message = 'Thanks for using '.env('APP_NAME').', please write a review of our restaurant.';
            $contents = ["en" => $message];
            $headings = ["en" => $title];

            if($user_push = UserPush::where('type','=','user')->whereUsersId($cleared_order->user_id)->first()){
                $player_id = $user_push->player_id;
                $info['data']['type'] = 'merchant_order_delivered';
                $info['data']['restaurant_id'] = $merchant->id;
                $info['contents'] = $contents;
                $info['headings'] = $headings;
                $info['player_ids'] = [$player_id];
                if(isset($request->date)){
                    $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                }
                $result = parent::oneSignal($info);
            }
        }
        $merchant->reset_at = Carbon::now()->timestamp;
        $merchant->save();
        return parent::return_json(['status' => 'Orders cleared successfully']);
    }

    public function logout(Request $request){
        $merchant = $this->validate_merchant($request);
        $merchant->access_token = null;
        $merchant->save();
        UserPush::whereType('restaurant')->where("users_id", "=", $merchant->id)->delete();
        $message = utf8_decode(utf8_encode(Lang::get('messages.logout_success')));
        return parent::return_json(['status' => $message]);
    }

    public function set_status(Request $request){
        $merchant = $this->validate_merchant($request);

        $merchant->is_closed = $request->status == 'on' ? true : false;
        $merchant->save();
        return parent::return_json(['status' => 'Status changed successfully']);
    }

    public function set_merchant_push(Request $request) {
        $merchant = $this->validate_merchant($request);
        if(!Input::has('player_id')){
            return parent::return_error('Player ID required!',400,'messages.missing_parameter');
        }
        if(!parent::validate_player_id($request->player_id)){
            return parent::return_error('Invalid Player ID',401,'messages.invalid_parameters');
        }
        /**
         * get onesignal user details
         */
        $details = parent::oneSignalViewUser($request->player_id);
        if($user_push = UserPush::where('type','=','restaurant')->whereUsersId($merchant->id)->first()){
            $user_push->device_model = isset($details['error']) ? null : $details['device_model'];
            $user_push->device_type = isset($details['error']) ? null : $details['device_type'];
            $user_push->identifier = isset($details['error']) ? null : $details['identifier'];
            $user_push->details = isset($details['error']) ? $details['error'] : json_encode($details);
            $user_push->player_id = Input::get('player_id');
            $user_push->type = 'restaurant';
            $user_push->save();
        }else{
            $user_push = UserPush::create([
                'player_id' => $request->get('player_id'),
                'users_id' => $merchant->id,
                'type' => 'restaurant',
                'device_model' => isset($details['error']) ? null : $details['device_model'],
                'device_type' => isset($details['error']) ? null : $details['device_type'],
                'identifier' => isset($details['error']) ? null : $details['identifier'],
                'details' => isset($details['error']) ? $details['error'] : json_encode($details),
            ]);
        }
        $message = utf8_decode(utf8_encode(Lang::get('messages.request_submitted_successfully')));
        return response()->json(['status' => $message]);
    }

    public function all_requests(Request $request){
        $merchant = $this->validate_merchant($request);
        /**
         * check new_order more than x time, to be rejected automatically + send push to user
         */
        $rejection_minutes = config('services.restaurant_rejection_minutes');
        $rejection_date = Carbon::now()->subMinutes($rejection_minutes)->format('Y-m-d H:i:s');
        foreach (Order::whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereStatus('new_order')->where('created_at','<=',$rejection_date)->get() as $rejected_order){
            $rejected_order->status = 'rejected';
            $status_at = $rejected_order->status_at;
            if(isset($status_at['rejected_at'])){
                $status_at['rejected_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }else{
                $status_at['rejected_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }
            $rejected_order->status_at = $status_at;
            $rejected_order->status_reason = 'Auto Rejection after '.$rejection_minutes.' min';
            $rejected_order->save();
            /**
             * send push to user
             */
            $merchant_full_name = $merchant->name_en;
            $merchant_phone_number = $merchant->country_code.$merchant->phone_number;
            $title = 'Order Canceled';
            $message = 'Sorry, '.$merchant_full_name.' is facing problem at the time being, please try again later';
            $contents = ["en" => $message];
            $headings = ["en" => $title];

            if($user_push = UserPush::where('type','=','user')->whereUsersId($rejected_order->user_id)->first()){
                $player_id = $user_push->player_id;
                $info['data']['type'] = 'merchant_order_rejected';
                $info['data']['restaurant_id'] = $merchant->id;
                $info['contents'] = $contents;
                $info['headings'] = $headings;
                $info['player_ids'] = [$player_id];
                if(isset($request->date)){
                    $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                }
                $result = parent::oneSignal($info);
            }
        }
        /**
         * check on its way, if x time passed, set as delivered
         */
        $delivery_minutes = config('services.restaurant_delivery_minutes');
        $delivery_date = Carbon::now()->subMinutes($delivery_minutes)->format('Y-m-d H:i:s');
        foreach (Order::whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereStatus('on_its_way')->where('created_at','<=',$delivery_date)->get() as $delivered_order){
            $delivered_order->status = 'delivered';
            $status_at = $delivered_order->status_at;
            if(isset($status_at['delivered_at'])){
                $status_at['delivered_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }else{
                $status_at['delivered_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }
            $delivered_order->status_at = $status_at;
            $delivered_order->status_reason = 'Auto Delivery after '.$delivery_minutes.' min';
            $delivered_order->save();
            /**
             * send push to user
             */
            $merchant_full_name = $merchant->name_en;
            $merchant_phone_number = $merchant->country_code.$merchant->phone_number;
            $title = 'Order Delivered';
            $message = 'Thanks for using '.env('APP_NAME').', please write a review of our restaurant.';
            $contents = ["en" => $message];
            $headings = ["en" => $title];

            if($user_push = UserPush::where('type','=','user')->whereUsersId($delivered_order->user_id)->first()){
                $player_id = $user_push->player_id;
                $info['data']['type'] = 'merchant_order_delivered';
                $info['data']['restaurant_id'] = $merchant->id;
                $info['contents'] = $contents;
                $info['headings'] = $headings;
                $info['player_ids'] = [$player_id];
                if(isset($request->date)){
                    $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                }
                $result = parent::oneSignal($info);
            }
        }
        $status = isset($request->status) ? $request->status : null;
        $reset_date = Carbon::createFromTimestamp($merchant->reset_at)->format('Y-m-d H:i:s');
        if($status){
            if($status == 'table_reservation'){
                $all_requests = UserReservation::where('created_at','>=',$reset_date)->whereItemId($merchant->id)->latest()->paginate(10);
            }else{
                $all_requests = Order::where('created_at','>=',$reset_date)->whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereStatus($status)->latest()->paginate(10);
            }
            return parent::return_json($all_requests);
        }else{
            $all_requests = [];
            $order_statuses = OrderStatus::orderBy('item_order','asc')->get();
            foreach ($order_statuses as $order_status){
                $query = Order::where('created_at','>=',$reset_date)->whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereStatus($order_status->status);
                $all_requests[] = [
                    'type' => $order_status->status,
                    'label' => $order_status->label,
                    'count' => $query->count(),
                    'total' => 'Total: '.number_format((float)$query->sum('total_price'), 0, '.', ',').' '.env('APP_CURRENCY'),
                    'requests' => $query->latest()->get(),
                ];
            }
            $table_reservations_query = UserReservation::where('created_at','>=',$reset_date)->whereItemId($merchant->id);
            $all_requests[] = [
                'type' => 'table_reservations',
                'label' => 'Table Reservations',
                'total' => null,
                'count' => $table_reservations_query->count(),
                'requests' => $table_reservations_query->latest()->get(),
            ];

            return parent::return_json($all_requests);
        }
    }

    public function set_order_status(Request $request){
        $merchant = $this->validate_merchant($request);
        if(!Input::has('request_id')){
            return parent::return_error('Missing Request ID',401,'messages.missing_parameter');
        }
        if(!Input::has('status')){
            return parent::return_error('Missing Status',401,'messages.missing_parameter');
        }
        if(!$order_status = OrderStatus::whereStatus($request->status)->first()){
            return parent::return_error("Status Wrong Value", 402, 'messages.invalid_parameters');
        }
        $request_id = $request->request_id;
        if(!$order = Order::whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereId($request_id)->first()){
            return parent::return_error("Invalid Order", 402, "Invalid Order");
        }
        $old_status = $order->status;
        $new_status = $request->status;
        $user = $order->user;
        if(!$user){
            return parent::return_error("User Not Found", 402, "User Not Found");
        }
        $status_at = $order->status_at;
        if(isset($status_at[$new_status.'_at'])){
            $status_at[$new_status.'_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{
            $status_at[$new_status.'_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $order->status_at = $status_at;
        $order->status = $new_status;
        $order->save();

        /**
         * send push to user
         */
        $merchant_full_name = $merchant->name_en;
        $merchant_phone_number = $merchant->country_code.$merchant->phone_number;
        $title = 'Order '.$order_status->label;
        $message = $merchant_full_name.' has changed your order status to: ['.$order_status->label.'], you can contact him on '.$merchant_phone_number;
        if($new_status == 'delivered'){
            $message = 'Thanks for using '.env('APP_NAME').', please write a review of our restaurant.';
        }

        $contents = ["en" => $message];
        $headings = ["en" => $title];

        if($user_push = UserPush::where('type','=','user')->whereUsersId($user->id)->first()){
            $player_id = $user_push->player_id;
            $info['data']['type'] = 'merchant_order_'.$new_status;
            $info['data']['restaurant_id'] = $merchant->id;
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = [$player_id];
            if(isset($request->date)){
                $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
            }
            $result = parent::oneSignal($info);
        }
        /**
         * insert in db even if user not registered to oneSignal
         * he can access his inbox anytime
         */
        $code = Controller::number_generator(6);
        Inbox::create([
            'user_type' => 'user',
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'image' => null,
            'type' => 'targeted',
            'code' => $code,
            'meta_type' => 'order',
            'meta_value' => $order->id,
        ]);
        $currency = env('APP_CURRENCY');
        unset($order->items);
        $order->items =  $order->pre_items()
            ->get()->map(function($value, $key) use ($currency) {
                $value->final_price_formatted = number_format($value->final_price,0) . ' ' . $currency;
                $value->currency = $currency;
                $value->price_formatted = number_format($value->price,0) . ' ' . $currency;
                $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,0) . ' ' . $currency : null;
                $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,0) . ' ' . $currency : null;
                return $value;
            })->values();
        return response()->json($order);
    }

    public function order_details(Request $request){
        $merchant = $this->validate_merchant($request);
        if(!Input::has('request_id')){
            return parent::return_error('Missing Request ID',401,'messages.missing_parameter');
        }
        $request_id = $request->request_id;
        if(!$order = Order::whereMerchantType('restaurant')->whereMerchantId($merchant->id)->whereType('regular')->whereId($request_id)->first()){
            return parent::return_error("Invalid Request", 402, "Invalid Request");
        }
        /**
         * they need items for confirmation screen
         * and in book api items will returned as empty in case of online because order_id still null
         */
        $currency = env('APP_CURRENCY');
        unset($order->items);
        $order->items =  $order->pre_items()
            ->get()->map(function($value, $key) use ($currency) {
                $value->final_price_formatted = number_format($value->final_price,0) . ' ' . $currency;
                $value->currency = $currency;
                $value->price_formatted = number_format($value->price,0) . ' ' . $currency;
                $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,0) . ' ' . $currency : null;
                $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,0) . ' ' . $currency : null;
                return $value;
            })->values();
        return response()->json($order);
    }

    public function set_reservation_status(Request $request){
        $merchant = $this->validate_merchant($request);
        if(!Input::has('request_id')){
            return parent::return_error('Missing Request ID',401,'messages.missing_parameter');
        }
        if(!Input::has('status')){
            return parent::return_error('Missing Status',401,'messages.missing_parameter');
        }
        $status_possible_values = ['accept','reject','cancel'];
        if(!in_array($request->status,$status_possible_values)){
            return parent::return_error("Status Wrong Value", 402, 'messages.invalid_parameters');
        }
        $request_id = $request->request_id;
        if(!$user_reservation = UserReservation::whereId($request_id)->whereItemId($merchant->id)->first()){
            return parent::return_error("Invalid Reservation", 402, "Invalid Reservation");
        }
        $user = $user_reservation->user;
        if(!$user){
            return parent::return_error("User Not Found", 402, "User Not Found");
        }
        /**
         * check if request expired first
         */
        if($user_reservation->reservation_datetime < Carbon::now()->timestamp){
            $user_reservation->status = 'expired';
            $user_reservation->expired_at = Carbon::now()->timestamp;
            $user_reservation->save();
            return parent::return_error("This Request has expired", 402, "This Request has expired");
        }else{

            if($request->status == 'accept'){
                $user_reservation->status = $request->status;
                $user_reservation->accepted_at = Carbon::now()->timestamp;
                $user_reservation->save();
            }elseif($request->status == 'reject'){
                $user_reservation->status = $request->status;
                $user_reservation->rejected_at = Carbon::now()->timestamp;
                $user_reservation->save();
            }elseif($request->status == 'cancel'){
                $user_reservation->status = $request->status;
                $user_reservation->canceled_at = Carbon::now()->timestamp;
                $user_reservation->save();
            }
            /**
             * send push to user
             */
            $merchant_full_name = $merchant->name_en;
            $merchant_phone_number = $merchant->country_code.$merchant->phone_number;
            $title = null;
            $message = null;
            if($request->status == 'accept'){
                $title = 'Reservation Request Accepted';
                $message = $merchant_full_name.' has accepted your reservation, you can contact him on '.$merchant_phone_number;
                $contents = ["en" => $message];
                $headings = ["en" => $title];
            }elseif($request->status == 'reject'){
                $title = 'Reservation Request Declined';
                $message = $merchant_full_name.' is unable to accept your reservation, kindly try again later';
                $contents = ["en" => $message];
                $headings = ["en" => $title];
            }elseif($request->status == 'cancel'){
                $title = 'Reservation Request Cancelled';
                $message = $merchant_full_name.' has canceled your reservation, you can contact him on '.$merchant_phone_number;
                $contents = ["en" => $message];
                $headings = ["en" => $title];
            }
            if($user_push = UserPush::where('type','=','user')->whereUsersId($user->id)->first()){
                $player_id = $user_push->player_id;
                $info['data']['type'] = 'merchant_reservation_'.$request->status;
                $info['data']['restaurant_id'] = $merchant->id;
                $info['contents'] = $contents;
                $info['headings'] = $headings;
                $info['player_ids'] = [$player_id];
                if(isset($request->date)){
                    $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                }
                $result = parent::oneSignal($info);
            }
            /**
             * insert in db even if user not registered to oneSignal
             * he can access his inbox anytime
             */
            $code = Controller::number_generator(6);
            Inbox::create([
                'user_type' => 'user',
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'image' => null,
                'type' => 'targeted',
                'code' => $code,
                'meta_type' => 'restaurant',
                'meta_value' => $merchant->id,
            ]);
        }
        $reset_date = Carbon::createFromTimestamp($merchant->reset_at)->format('Y-m-d H:i:s');
        $all_requests = UserReservation::where('created_at','>=',$reset_date)->whereItemId($merchant->id)->orderBy('id','desc')->get();
        return parent::return_json($all_requests);
    }



}
