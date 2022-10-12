<?php

namespace App\Http\Controllers\Api;


use App;
use App\Models\Api\CheckoutLog;
use App\Models\Api\Item;
use App\Models\Api\Order;
use App\Models\Api\PackageItem;
use App\Models\Api\Setting;
use App\Models\Api\UserCard;
use App\Models\Api\Users;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\HITrait;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Error\ApiConnection;
use Stripe\Error\Authentication;
use Stripe\Error\Base;
use Stripe\Error\Card;
use Stripe\Error\InvalidRequest;
use Stripe\Error\RateLimit;
use Stripe\Stripe;
use Stripe\Token;

use Carbon\Carbon;
use DB;
use Exception;

class PaymentController extends Controller
{
    use HITrait;
    public function payment_methods(Request $request) {
        $user = $this->validate_user($request);
        $payment_methods = [];
        $payment_methods[] = [
            'code' => 'cash',
            'name' => 'Cash On Delivery',
            'image' => secure_asset('public/uploads/images/cod.png'),
        ];
        $payment_methods[] = [
            'code' => 'online',
            'name' => 'Online',
            'image' => secure_asset('public/uploads/images/credit.png'),
        ];
//        $payment_methods[] = [
//            'code' => 'knet',
//            'name' => 'KNET',
//            'image' => secure_asset('public/uploads/images/knet.png'),
//        ];
//        $payment_methods[] = [
//            'code' => 'credit',
//            'name' => 'Credit Card',
//            'image' => secure_asset('public/uploads/images/credit.png'),
//        ];
//        $payment_methods[] = [
//            'code' => 'amex',
//            'name' => 'Amex',
//            'image' => secure_asset('public/uploads/images/amex.png'),
//        ];
//        $payment_methods[] = [
//            'code' => 'Bookeey',
//            'name' => 'Bookeey',
//            'image' => secure_asset('public/uploads/images/bookeey.png'),
//        ];
        return response()->json($payment_methods);
    }

    public function success_response(Request $request) {
        if(!isset($request->merchantTxnId)){
            $result['message'] = "Something went wrong, Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        if(!isset($request->txnId)){
            $result['message'] = "Something went wrong, Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $order_id = trim($request->merchantTxnId);
        $order = CheckoutLog::whereOrderId($order_id)->orderBy('id','desc')->first();
        if(!$order){
            $result['message'] ="Something went wrong, Invoice Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $transaction_id = trim($request->txnId);
        $order->transaction_id = $transaction_id;
        $order->payment_status = 'failed';
        $order->save();

        $info = [];
        $info['function'] = '/paymentStatus';
        $string_to_hash = env('BOOKEEY_MID').'|'.env('BOOKEEY_SECRET_KEY');
        $hashed = hash('sha512', $string_to_hash);
        $info['data'] = [
            'mid' => env('BOOKEEY_MID'),
            'hashMac' => $hashed,
            'merchantTxnRefNo' => [$order_id],
        ];
        $response = parent::payment_connect($info);
        if(isset($response['error'])){
            $result['message'] = "Something went wrong,Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }

        $order->retrieve_request = json_encode($request->all());
        $order->retrieve_response = json_encode($response);
        $order->payment_status = 'success';
        $order->done = true;
        $order->save();
        /**
         * decrease items purchased quantity
         */
        $real_order = $order->order;
        if($real_order){
            $user = $real_order->user;
            $info = [];
            $info['to'] = $to = explode(',', env('ORDER_EMAILS'));
            $info['user'] = $user;
            $info['order'] = $real_order;
            $info['title'] = 'New Order';
            $info['order_link'] = secure_url('admin/orders?order_id='.$order->id);
            Mail::send('emails.booking', ['data' => $info], function ($message) use ($info) {
                $message->subject($info['title']);
                $message->to($info['to']);
            });
            if($user->email != null){
                $info = [];
                $info['to'] = $user->email;
                $info['user'] = $user;
                $info['order'] = $real_order;
                $info['subtotal'] = $real_order->total_price - $real_order->delivery_charge + $real_order->coupon_amount;
                $info['title'] = 'New Order';
                Mail::send('emails.user_booking', ['data' => $info], function ($message) use ($info) {
                    $message->subject($info['title']);
                    $message->to($info['to']);
                });
            }
            foreach (PackageItem::whereUserId($real_order->user_id)->whereOrderCode($real_order->code)->get() as $item){
                $item->order_id = $real_order->id;
                $item->save();
                $real_item = $item->item;
                if($real_item){
                    $real_item->quantity = $real_item->quantity - $item->quantity;
                    $real_item->save();
                }
            }
        }
        $user->cart_restaurant_id = null;
        $user->coupon_amount = null;
        $user->coupon_type = null;
        $user->coupon_id = null;
        $user->save();
        $result['message'] = 'Your Transaction has been successfully processed';
        $result['result'] = 'success';
        parent::redirect(secure_url('/api/order/check_purchased'),$result);

    }

    public function error_response(Request $request) {
        if(!isset($request->merchantTxnId)){
            $result['message'] = "Something went wrong,Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $order_id = trim($request->merchantTxnId);
        $error_message = $request->errorMessage;
        $info = [];
        $info['function'] = '/paymentStatus';
        $string_to_hash = env('BOOKEEY_MID').'|'.env('BOOKEEY_SECRET_KEY');
        $hashed = hash('sha512', $string_to_hash);
        $info['data'] = [
            'mid' => env('BOOKEEY_MID'),
            'hashMac' => $hashed,
            'merchantTxnRefNo' => [$order_id],
        ];
        $response = parent::payment_connect($info);
        $order = CheckoutLog::whereOrderId($order_id)->orderBy('id','desc')->first();
        if(!$order){
            $result['message'] ="Something went wrong, Invoice Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $order->retrieve_request = json_encode($request->all());
        $order->retrieve_response = json_encode($response);
        $order->payment_status = 'failed';
        $order->save();
        $result['message'] ="Something went wrong, Transaction not processed successfully";
        $result['result'] = 'failure';
        parent::redirect(secure_url('/api/order/check_purchased'),$result);
    }

    public function myfatoorah_get_payment_methods(Request $request){
        $user = $this->validate_user($request);
        if(Input::has('item_ids') ){
            $total_price = 0;
            foreach ($request->item_ids as $index=>$item_id){
                if(! $item = PackageItem::/*whereItemSizeId($request->size_ids[$index])->*/whereNull('order_id')->whereItemId($item_id)->whereUserId($user->id)->first()){
                    return parent::return_error('Restaurant not found in cart',405,'messages.item_not_found_in_cart');
                }
                $price = $item->item->price;
//                if($item->item->discounted_price != null){
//                    $price = $item->item->discounted_price;
//                }
                $price = $price * $item->quantity;
                $total_price += $price;
            }
        }else{
            $total_price = 0;
            foreach (PackageItem::whereNotNull('item_id')->whereNull('order_id')->whereUserId($user->id)->get() as $item){
                $price = $item->item->price;
//                if($item->item->discounted_price != null){
//                    $price = $item->item->discounted_price;
//                }
                $price = $price * $item->quantity;
                $total_price += $price;
            }
        }
        $original_total_price = $total_price;

        $coupon = $user->coupon;
        $discount_amount = 0;
        $coupon_code = null;
        if($coupon){
            //we will take the code from coupon table cz this field must not be edited from cms
            $coupon_code = $coupon->code;
            $nb_uses = Order::whereCouponId($user->coupon_id)->count();
            $now = Carbon::now()->timestamp;
            //if coupon still valid calculate the discount based on details saved in profile not from coupon table [could be updated since last time coupon applied in cart]
            if($nb_uses < $coupon->max_uses && $now <= $coupon->end_date && $now >= $coupon->start_date){
                if($user->coupon_type == 'percentage'){
                    $discount_amount = $total_price * ($user->coupon_amount/100);
                    $total_price = $total_price - ($total_price * ($user->coupon_amount/100));
                }else{
                    $total_price = $total_price - $user->coupon_amount;
                    $discount_amount = $user->coupon_amount;
                }
            }
        }else{
            //something went wrong with coupon [deleted]
            $user->coupon_amount = null;
            $user->coupon_type = null;
            $user->coupon_id = null;
            $user->save();
        }

        $settings = Setting::get();
        $delivery_threshold = null;
        $delivery_charge = 0;
        foreach ($settings as $setting){
            if($setting->meta_name == 'delivery_threshold'){
                $delivery_threshold = $setting->meta_value;
            }
            if($setting->meta_name == 'delivery_charge'){
                $delivery_charge = $setting->meta_value;
            }
        }
        if($delivery_threshold && $delivery_charge){
            if($total_price >= $delivery_threshold){
                $delivery_charge = 0;
            }
        }

        $new_total_price = $total_price + floatval($delivery_charge);

        $info = [];
        $info['function'] = '/v2/InitiatePayment';
        $info['data'] = [
            'InvoiceAmount' => number_format((float)$new_total_price, 2, '.', ''),
            'CurrencyIso' => 'KWD'
        ];

        $response = parent::myfatoorah_connect($info);
        $banks[] = [
            'PaymentMethodId' => -1,
            'PaymentMethodAr' => 'الدفع عند الاستلام',
            'PaymentMethodEn' => 'Cash on Delivery',
            'PaymentMethodCode' => 'cod',
            'IsDirectPayment' => false,
            'ServiceCharge' => 0,
            'TotalAmount' => $new_total_price,
            'CurrencyIso' => $user->currency,
            'ImageUrl' => secure_url('public/uploads/images/cod.png'),

        ];
        if(isset($response['IsSuccess']) && $response['IsSuccess'] == true){
            if(isset($response['Data']) && isset($response['Data']['PaymentMethods'])){
                $banks = array_merge($banks, $response['Data']['PaymentMethods']);
            }
        }
        return response()->json($banks);
    }

    public function show_myfatoorah_pg(Request $request)
    {
        if($access_token = parent::valid_access_token($request)) {
            if ($user = parent::get_user_by_access_token($access_token)) {
                if(!Input::has('order_id')){
                    return parent::return_error("Missing Order ID", 401, 'messages.missing_parameter');
                }
                if(!Input::has('payment_method_id')){
                    return parent::return_error("Missing Payment Method ID", 401, 'messages.missing_parameter');
                }
                if(!$order = CheckoutLog::whereUsersId($user->id)->whereId($request->order_id)->first()){
                    return parent::return_error("Invalid Order", 402, 'messages.invalid_parameters');
                }
                $payment_method_id = $request->payment_method_id;
                $order->payment_method_id = $payment_method_id;
                $order->save();
                if(!$order->insurance){
                    return parent::return_error("Invalid Order Insurance", 402, 'messages.invalid_parameters');
                }
                $amount = number_format((float)$order->insurance->price, 2, '.', '');
                $currency = $order->insurance->currency;
                if($currency == null || $amount == null || $amount <= 0){
                    return parent::return_error("Invalid Order Amount", 402, 'messages.invalid_parameters');
                }
                $description = null;
                if($order->insurance_type == 'third_party'){
                    $description = 'Third Party Insurance';
                }elseif ($order->insurance_type == 'travel'){
                    $description = 'Travel Insurance';
                }elseif ($order->insurance_type == 'motor_all_risk'){
                    $description = 'Motor All Risk Insurance';
                }
                $info = [];
                $info['function'] = '/v2/ExecutePayment';
                $expiry_date = Carbon::now()->addDays(1)->format('Y-m-d\TH:i:s.u');
                $info['data'] = [
                    'PaymentMethodId' => $payment_method_id,
                    'CustomerName' => $user->first_name .' ' . $user->last_name,
                    'DisplayCurrencyIso' => 'KWD',
                    'MobileCountryCode' => $user->country_code,
                    'CustomerMobile' => $user->primary_phone,
                    'CustomerEmail' => $user->email,
                    'InvoiceValue' => $amount,// for now we are not adding selected bank service charge
                    'CallBackUrl' => secure_url('api/myfatoorah_success_response'),
                    'ErrorUrl' => secure_url('api/myfatoorah_error_response'),
                    'Language' => 'en',//for now static, maybe later on when there is localization it will be dynamic
                    'CustomerReference' => $order->id,
                    'CustomerCivilId' => $user->id,
                    'UserDefinedField' => $user->id,//this will be returned in redirect along with the customerReference
                    'ExpireDate' => $expiry_date,
                    'CustomerAddress' => [
                        'Block' => '',
                        'Street' => '',
                        'HouseBuildingNo' => '',
                        'Address' => '',
                        'AddressInstructions' => '',
                    ],
                    'InvoiceItems' => [
                        [
                            'ItemName' => $description,
                            'Quantity' => 1,
                            'UnitPrice' => $amount,
                        ]
                    ]
                ];
                $order->myfatoorah_request = json_encode($info);
                $order->save();
                $response = parent::myfatoorah_connect($info);
                if(isset($response['error'])){
                    $order->myfatoorah_response = $response['error'];
                    $order->save();
                    return parent::return_error("Payment Execution Failure", 471, "Payment Execution Failure");
                }
                $payment_url = isset($response['Data']) && isset($response['Data']['PaymentURL']) ? $response['Data']['PaymentURL'] : null;
                $order->myfatoorah_response = json_encode($response);
                $order->myfatoorah_invoice_id = isset($response['Data']) && isset($response['Data']['InvoiceId']) ? $response['Data']['InvoiceId'] : null;
                $order->myfatoorah_payment_url = $payment_url;
                $order->save();
                return response()->json(['payment_url' => $payment_url]);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
        }
    }

    public function payment_success_response(Request $request) {
        $locale = App::getLocale();
        if(!isset($request->paymentId)){
            $result['message'] = "Something went wrong,Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $payment_id = trim($request->paymentId);
        $info = [];
        $info['function'] = '/rest/getOrderStatus.do';
        $info['data'] = [
            'username' => config('services.arabs.username'),
            'password' => config('services.arabs.password'),
            'orderId' => $payment_id,
            'language' => $locale
        ];
        $response = parent::payment_connect($info);
        if(isset($response['ErrorCode']) && $response['ErrorCode'] != '0'){
            $result['message'] = isset($response['ErrorMessage']) ? $response['ErrorMessage'] : "Something went wrong,Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        if(!isset($response['OrderNumber'])){
            $result['message'] ="Something went wrong, Invoice Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }

        $order_id = $response['OrderNumber'];
        $checkout_log = CheckoutLog::whereOrderId($order_id)->orderBy('id','desc')->first();
        if(!$checkout_log){
            $result['message'] ="Something went wrong, Invoice Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $checkout_log->retrieve_request = json_encode($request->all());
        $checkout_log->retrieve_response = json_encode($response);
        $checkout_log->save();

        if(!isset($response['clientId'])){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, User Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $user_id = $response['clientId'];
        $user = Users::whereId($user_id)->orderBy('id','desc')->first();
        if(!$user){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, User Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $order = Order::whereUserId($user_id)->whereId($order_id)->orderBy('id','desc')->first();
        if(!$order){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, Order Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        if(!isset($response['OrderStatus'])){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, Order Status not fetched";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $checkout_log->payment_status = $response['OrderStatus'];
        $checkout_log->done = true;
        $checkout_log->save();

        /**
         * link items to order and decrease quantity
         */
        foreach (PackageItem::whereUserId($order->user_id)->whereOrderCode($order->code)->get() as $item){
            $item->order_id = $order->id;
            $item->save();
            $real_item = $item->item;
            if($real_item){
                $new_quantity = $real_item->quantity - $item->quantity;
                /**
                 * check alert for low inventory stock
                 */
                if($new_quantity <= env('LOW_INVENTORY_STOCK')){

                    $info = [];
                    $info['to'] = $to = explode(',', env('LOW_INVENTORY_EMAILS'));
                    $info['user'] = $user;
                    $info['order'] = $order;
                    $info['icon'] = 'alert.png';
                    $info['message'] = 'Low Inventory Stock';
                    $info['item'] = $item;
                    $info['item_link'] = secure_url('admin/items?item_id='.$item->id);
                    $info['title'] = env('APP_NAME') . ' | Low Inventory Stock';
                    Mail::send('emails.low_stock', ['data' => $info], function ($message) use ($info) {
                        $message->subject($info['title']);
                        $message->to($info['to']);
                    });
                }

                $real_item->quantity = $new_quantity;
                $real_item->save();
            }
        }
        $info = [];
        $info['to'] = $to = explode(',', env('ORDER_EMAILS'));
        $info['user'] = $user;
        $info['order'] = $order;
        $info['title'] = 'New Order | Online';
        $info['order_link'] = secure_url('admin/orders?order_id='.$order->id);
        Mail::send('emails.booking', ['data' => $info], function ($message) use ($info) {
            $message->subject($info['title']);
            $message->to($info['to']);
        });
        if($user->email != null){
            $info = [];
            $info['to'] = $user->email;
            $info['user'] = $user;
            $info['order'] = $order;
            $info['subtotal'] = $order->total_price - $order->delivery_charge + $order->coupon_amount + $order->discount_amount;
            $info['title'] = 'New Order #'.$order->code;
            Mail::send('emails.user_booking', ['data' => $info], function ($message) use ($info) {
                $message->subject($info['title']);
                $message->to($info['to']);
            });
        }
        $result['message'] = 'Your Transaction has been successfully processed';
        $result['result'] = 'success';
        parent::redirect(secure_url('/api/order/check_purchased'),$result);

    }

    public function payment_error_response(Request $request) {
        $locale = App::getLocale();
        if(!isset($request->paymentId)){
            $result['message'] = "Something went wrong,Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $payment_id = trim($request->paymentId);
        $info = [];
        $info['function'] = '/rest/getOrderStatus.do';
        $info['data'] = [
            'username' => config('services.arabs.username'),
            'password' => config('services.arabs.password'),
            'orderId' => $payment_id,
            'language' => $locale
        ];
        $response = parent::payment_connect($info);
        if(isset($response['ErrorCode']) && $response['ErrorCode'] != '0'){
            $result['message'] = isset($response['ErrorMessage']) ? $response['ErrorMessage'] : "Something went wrong,Payment Failure";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        if(!isset($response['OrderNumber'])){
            $result['message'] ="Something went wrong, Invoice Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }

        $order_id = $response['OrderNumber'];
        $checkout_log = CheckoutLog::whereOrderId($order_id)->orderBy('id','desc')->first();
        if(!$checkout_log){
            $result['message'] ="Something went wrong, Invoice Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $checkout_log->retrieve_request = json_encode($request->all());
        $checkout_log->retrieve_response = json_encode($response);
        $checkout_log->save();

        if(!isset($response['clientId'])){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, User Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $user_id = $response['clientId'];
        $user = Users::whereId($user_id)->orderBy('id','desc')->first();
        if(!$user){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, User Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $order = Order::whereUserId($user_id)->whereId($order_id)->orderBy('id','desc')->first();
        if(!$order){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, Order Not Found";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        if(!isset($response['OrderStatus'])){
            $checkout_log->payment_status = 'failed';
            $checkout_log->save();
            $result['message'] ="Something went wrong, Order Status not fetched";
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        $checkout_log->payment_status = $response['OrderStatus'];
        $checkout_log->save();
        $result['message'] ="Something went wrong, Transaction not processed successfully";
        $result['result'] = 'failure';
        parent::redirect(secure_url('/api/order/check_purchased'),$result);
    }

    public function pay(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {
                if(!Input::has('insurance_type')){
                    return parent::return_error("Missing Insurance Type", 401, 'messages.missing_parameter');
                }
                $insurance_type = $request->insurance_type;
                $type_possible_values =  [ 'third_party' , 'motor_all_risk' , 'travel' ];
                if(!in_array($insurance_type,$type_possible_values)){
                    return parent::return_error("Insurance Type Wrong Value", 402, 'messages.invalid_parameters');
                }
                if(!Input::has('request_id')){
                    return parent::return_error("Missing Request ID", 401, 'messages.missing_parameter');
                }
                //------->>>>>>>>>>>>>>>>>>>>>>>>>> To remove paid status from below
                $insurance_request = null;
                if($insurance_type == 'third_party' && !$insurance_request=ThirdPartyInsuranceRequest::whereIn('status',['pending','expired','failed'])->whereId($request->request_id)->first()){
                    return parent::return_error("Invalid Insurance", 402, 'messages.invalid_parameters');
                }
                if($insurance_type == 'motor_all_risk' && !$insurance_request=MotorAllRiskInsuranceRequest::whereIn('status',['pending','expired','failed'])->whereId($request->request_id)->first()){
                    return parent::return_error("Invalid Insurance", 402, 'messages.invalid_parameters');
                }
                if($insurance_type == 'travel' && !$insurance_request=TravelInsuranceRequest::whereIn('status',['pending','expired','failed'])->whereId($request->request_id)->first()){
                    return parent::return_error("Invalid Insurance", 402, 'messages.invalid_parameters');
                }
                $additional_info = 'new';
                if($insurance_request->is_expired){
                    $additional_info = 'renew';
                }
                $order = CheckoutLog::create([
                    'users_id' => $user->id,
                    'insurance_id' => $insurance_request->id,
                    'insurance_type' => $insurance_type,
                    'total_price' => $insurance_request->price,
                    'additional_info' => $additional_info
                ]);
//                $is_lebanon = $insurance_request->currency == 'KWD' ? false : true;
//                if($order->insurance_type == 'third_party'){
//                    $info = [];
//                    $info['to'] = $to = explode(',', env('THIRD_PARTY_EMAILS'));
//                    $info['cc'] = $cc = array_filter(explode(',', env('THIRD_PARTY_CC_EMAILS')));
//                    $info['user'] = $user;
//                    $info['order'] = $order;
//                    $slug = ($is_lebanon)? '3rd_party_lebanon' : '3rd_party_kuwait';
//                    $particular_description = SummaryTerms::whereSlug($slug)->first();
//                    $info['particular_conditions'] = $particular_description ? $particular_description->description : '';
//                    $info['description'] = ($is_lebanon)? "This document takes effect for the duration of the vehicle's license in accordance with the provisions of the Lebanese Traffic Law, and expires at the end of this period." : "This document takes effect for the duration of the vehicle's license in accordance with the provisions of the Kuwaiti Traffic Law,
//                                                            and expires at the end of this period. A physical policy will be provided to you for renewing the registration.";
//                    $info['currency'] = ($is_lebanon)? 'USD' : 'KWD';
//                    $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
//                    $info['title'] = ($is_lebanon)? 'Motor Third Party Liability' : 'Motor Third Party Liability';
//                    Mail::send('emails.third_party', ['data' => $info], function ($message) use ($info,$insurance_request) {
//                        $message->subject($info['title']);
//                        $message->to($info['to']);
//                        $message->from(config('mail.from.address'), $info['from']);
//                        foreach ($insurance_request->images as $image){
//                            if($image->media_name != null){
//                                $message->attach($image->media_name, [
//                                    'as' => $image->meta_name.'.png'
//                                ]);
//                            }
//                        }
//                        //$message->to($info['cc']);
//                    });
//                }elseif ($order->insurance_type == 'travel'){
//                $info = [];
//                $info['to'] = $to = explode(',', env('TRAVEL_EMAILS'));
//                $info['cc'] = $cc = array_filter(explode(',', env('TRAVEL_CC_EMAILS')));
//                $info['user'] = $user;
//                $info['order'] = $order;
//                $slug = ($is_lebanon)? 'travel_lebanon' : 'travel_kuwait';
//                $particular_description = SummaryTerms::whereSlug($slug)->first();
//                $info['particular_conditions'] = $particular_description ? $particular_description->description : '';
//                $info['currency'] = ($is_lebanon)? 'USD' : 'KWD';
//                $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
//                $info['title'] = ($is_lebanon)? 'Travel Insurance' : 'Travel Insurance';
//                Mail::send('emails.travel', ['data' => $info], function ($message) use ($info,$insurance_request) {
//                    $message->subject($info['title']);
//                    $message->to($info['to']);
//                    $message->from(config('mail.from.address'), $info['from']);
//                    foreach ($insurance_request->members as $index => $member){
//                        $member_index = $index + 1;
//                        foreach ($member->images as $image){
//                            if($image->media_name != null){
//                                $message->attach($image->media_name, [
//                                    'as' => $image->meta_name.'_'.$member_index.'.png'
//                                ]);
//                            }
//                        }
//
//                    }
//                    //$message->to($info['cc']);
//                });
//
//                }elseif ($order->insurance_type == 'motor_all_risk'){
//                    $info = [];
//                    $info['to'] = $to = explode(',', env('MOTOR_ALL_RISK_EMAILS'));
//                    $info['cc'] = $cc = explode(',', env('MOTOR_ALL_RISK_CC_EMAILS'));
//                    $info['user'] = $user;
//                    $info['order'] = $order;
//                    $slug = ($is_lebanon)? 'motor_all_risk_lebanon' : 'motor_all_risk_kuwait';
//                    $particular_description = SummaryTerms::whereSlug($slug)->first();
//                    $info['particular_conditions'] = $particular_description ? $particular_description->description : '';
//                    $info['currency'] = ($is_lebanon)? 'USD' : 'KWD';
//                    $info['policy_fees'] = ($is_lebanon)? '5' : '2';
//                    $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
//                    $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
//                    $info['agency_repair'] = $insurance_request->model_year->type != 'category1' ? 'Outside Agency' : 'Agency';
//                    $info['title'] = ($is_lebanon)? 'Motor All Risks Policy' : 'Motor All Risks Policy';
//                    Mail::send('emails.motor_all_risk', ['data' => $info], function ($message) use ($info,$insurance_request) {
//                        $message->subject($info['title']);
//                        $message->to($info['to']);
//                        $message->from(config('mail.from.address'), $info['from']);
//                        foreach ($insurance_request->images as $image){
//                            if($image->media_name != null){
//                                $message->attach($image->media_name, [
//                                    'as' => $image->meta_name.'.png'
//                                ]);
//                            }
//                        }
//                        //$message->to($info['cc']);
//                    });
//                }
                return response()->json(['order_id' => $order->id,'currency' => $order->insurance->currency]);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
        }
    }

    public function payment_response(Request $request,$additional_info) {
        $locale = App::getLocale();
        try{
            /**
             * you need to update insurance table status
             */
            $additional_data = explode('_', $additional_info);
            $order_id = $additional_data[1];
            $user_id = $additional_data[0];
            $user = Users::whereId($user_id)->orderBy('id','desc')->first();

            if(!$user){
                $result['message'] = 'User Not Found';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/order/check_purchased'),$result);
            }
            $order = CheckoutLog::whereUsersId($user_id)->whereId($order_id)->orderBy('id','desc')->first();
            if(!$order){
                $result['message'] = 'Order Not Found';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/order/check_purchased'),$result);
            }
            $insurance_request = $order->insurance;
            if(!isset($request->resultIndicator)){// it's an error
                $order->payment_status = 'failed';
                $order->save();
                if($insurance_request){
                    $insurance_request->status = 'failed';
                    $insurance_request->save();
                }
                $result['message'] = 'Payment Failure: #'.$order_id;
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/order/check_purchased'),$result);
            }
            /**
             * here the completeCallback has been called
             * we need to check if payment success
             * and get payment response info by calling "RETRIEVE_ORDER"
             * then we need to create the order in magento
             */
            $data = [
                'apiUsername' => 'merchant.' . env('PAYMENT_MERCHANT_NUMBER'),
                'apiPassword' => env('PAYMENT_API_PASSWORD'),
                'apiOperation' => 'RETRIEVE_ORDER',
                'merchant' => env('PAYMENT_MERCHANT_NUMBER'),
                'order.id' => $order_id,
            ];
            $info['data'] = $data;
            $session_info = parent::manage_session($info);
            if(isset($session_info['error'])){
                $order->payment_status = 'failed';
                $order->save();
                if($insurance_request){
                    $insurance_request->status = 'failed';
                    $insurance_request->save();
                }
                $result['message'] = $session_info['error'];
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/order/check_purchased'),$result);
            }
            $order->retrieve_info = $session_info;
            $order->save();
            parse_str($session_info, $session_details);
            if(isset($session_details['error_cause'])){
                $order->payment_status = 'failed';
                $order->save();
                if($insurance_request){
                    $insurance_request->status = 'failed';
                    $insurance_request->save();
                }
                $result['message'] = $session_details['error_cause'];
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/order/check_purchased'),$result);
            }
            if(strtoupper($session_details['result']) != 'SUCCESS' || strtoupper($session_details['status'])== 'FAILED' || strtoupper($session_details['merchant']) != strtoupper(env('PAYMENT_MERCHANT_NUMBER'))){
                $result['message'] = 'Cannot Retrieve Order';
                $result['result'] = 'failure';
                $order->payment_status = 'failed';
                $order->save();
                if($insurance_request){
                    $insurance_request->status = 'failed';
                    $insurance_request->save();
                }
                parent::redirect(secure_url('/api/order/check_purchased'),$result);
            }

            $order->payment_status = 'success';
            $order->done = true;
            $order->save();
            $user->cart_restaurant_id = null;
            $user->coupon_amount = null;
            $user->coupon_type = null;
            $user->coupon_id = null;
            $user->save();
            /**
             * extend policy due date in case of renew
             */
            if($order->additional_info == 'renew'){
                if($order->insurance_type == 'third_party'){
                    $premium_due_date = Carbon::now()->addYears(1);
                }elseif ($order->insurance_type == 'travel'){
                    if($insurance_request && $insurance_request->period && $insurance_request->period->days == -1){
                        $premium_due_date = Carbon::now()->addYears(1);
                    }else{
                        $premium_due_date = Carbon::now()->addDays($insurance_request->period->days);
                    }
                }elseif ($order->insurance_type == 'motor_all_risk'){
                    $premium_due_date = Carbon::now()->addYears(1);
                }
                if($insurance_request){
                    $insurance_request->premium_due_date = $premium_due_date->getTimestamp();
                    $insurance_request->save();
                }
            }

            if($insurance_request){
                $insurance_request->status = 'paid';
                $insurance_request->save();
            }

            /**
             * adding points for each 1$ => 1 point
             */
//            $user->points()->create([
//                'points' => floor($order->total_price),
//                'description' => 'Payment Online Success'
//            ]);
//            $total = 0;
//            foreach ($user->points as $row) {
//                $total += $row->points;
//            }
//            $user->total_points = $total;
//            $user->save();

        } catch (Exception $e) {
            $result['message'] = 'Payment Failure: '.$e->getMessage();
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/order/check_purchased'),$result);
        }
        //update payment status
        $result['message'] = 'Success';
        $result['success'] = true;
        $result['data'] = null;
        $is_lebanon = $insurance_request->currency == 'KWD' ? false : true;
        if($order->insurance_type == 'third_party'){
            $info = [];
            $info['to'] = $to = explode(',', env('THIRD_PARTY_EMAILS'));
            $info['cc'] = $cc = array_filter(explode(',', env('THIRD_PARTY_CC_EMAILS')));
            $info['user'] = $user;
            $info['order'] = $order;
            $slug = ($is_lebanon)? '3rd_party_lebanon' : '3rd_party_kuwait';
            $particular_description = SummaryTerms::whereSlug($slug)->first();
            $info['particular_conditions'] = $particular_description ? $particular_description->{'description_'.$locale} : '';
            $info['description'] = ($is_lebanon)? "This document takes effect for the duration of the vehicle's license in accordance with the provisions of the Lebanese Traffic Law, and expires at the end of this period." : "This document takes effect for the duration of the vehicle's license in accordance with the provisions of the Kuwaiti Traffic Law,
                                                            and expires at the end of this period. A physical policy will be provided to you for renewing the registration.";
            $info['currency'] = ($is_lebanon)? 'USD' : 'KWD';
            $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
            $info['title'] = ($is_lebanon)? 'Motor Third Party Liability' : 'Motor Third Party Liability';
            Mail::send('emails.third_party', ['data' => $info], function ($message) use ($info,$insurance_request) {
                $message->subject($info['title']);
                $message->to($info['to']);
                $message->from(config('mail.from.address'), $info['from']);
                foreach ($insurance_request->images as $image){
                    if($image->media_name != null){
                        $message->attach($image->media_name, [
                            'as' => $image->meta_name.'.png'
                        ]);
                    }
                }
                //$message->to($info['cc']);
            });
        }elseif ($order->insurance_type == 'travel'){
            $info = [];
            $info['to'] = $to = explode(',', env('TRAVEL_EMAILS'));
            $info['cc'] = $cc = array_filter(explode(',', env('TRAVEL_CC_EMAILS')));
            $info['user'] = $user;
            $info['order'] = $order;
            $slug = ($is_lebanon)? 'travel_lebanon' : 'travel_kuwait';
            $particular_description = SummaryTerms::whereSlug($slug)->first();
            $info['particular_conditions'] = $particular_description ? $particular_description->{'description_'.$locale} : '';
            $info['currency'] = ($is_lebanon)? 'USD' : 'KWD';
            $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
            $info['title'] = ($is_lebanon)? 'Travel Insurance' : 'Travel Insurance';
            Mail::send('emails.travel', ['data' => $info], function ($message) use ($info,$insurance_request) {
                $message->subject($info['title']);
                $message->to($info['to']);
                $message->from(config('mail.from.address'), $info['from']);
                foreach ($insurance_request->members as $index => $member){
                    $member_index = $index + 1;
                    foreach ($member->images as $image){
                        if($image->media_name != null){
                            $message->attach($image->media_name, [
                                'as' => $image->meta_name.'_'.$member_index.'.png'
                            ]);
                        }
                    }

                }
                //$message->to($info['cc']);
            });
        }elseif ($order->insurance_type == 'motor_all_risk'){
            $info = [];
            $info['to'] = $to = explode(',', env('MOTOR_ALL_RISK_EMAILS'));
            $info['cc'] = $cc = explode(',', env('MOTOR_ALL_RISK_CC_EMAILS'));
            $info['user'] = $user;
            $info['order'] = $order;
            $slug = ($is_lebanon)? 'motor_all_risk_lebanon' : 'motor_all_risk_kuwait';
            $particular_description = SummaryTerms::whereSlug($slug)->first();
            $info['particular_conditions'] = $particular_description ? $particular_description->{'description_'.$locale} : '';
            $info['policy_fees'] = ($is_lebanon)? '5' : '2';
            $info['currency'] = ($is_lebanon)? 'USD' : 'KWD';
            $info['from'] = ($is_lebanon)? env('APP_NAME').' Lebanon' : env('APP_NAME').' Kuwait';
            $info['agency_repair'] = $insurance_request->model_year->type != 'category1' ? 'Outside Agency' : 'Agency';
            $info['title'] = ($is_lebanon)? 'Motor All Risks Policy' : 'Motor All Risks Policy';
            Mail::send('emails.motor_all_risk', ['data' => $info], function ($message) use ($info,$insurance_request) {
                $message->subject($info['title']);
                $message->to($info['to']);
                $message->from(config('mail.from.address'), $info['from']);
                foreach ($insurance_request->images as $image){
                    if($image->media_name != null){
                        $message->attach($image->media_name, [
                            'as' => $image->meta_name.'.png'
                        ]);
                    }
                }
                //$message->to($info['cc']);
            });
        }

        $result['message'] = 'Your Transaction has been successfully processed';
        $result['result'] = 'success';
        parent::redirect(secure_url('/api/order/check_purchased'),$result);
    }

    public function check_result(Request $request){
        /*
         * return message and success
         */
        if($request->result == 'success'){
            return parent::return_json([
                'message' => $request->message,
                'details' => $request->message,
                'data' => $request->data,
            ]);
        }else{
            return parent::return_error($request->message,499,'Payment Failure');
        }
    }

    public function flick_update(Request $request){
//        Order::create([
//            'location_notes' => json_encode($request->all())
//        ]);
        $id = isset($request->id) && !empty($request->id) ? $request->id : null;
        $order_id = isset($request->order) && !empty($request->order) ? $request->order : null;
        $partner_reference_id = isset($request->partnerReferenceID) && !empty($request->partnerReferenceID) ? $request->partnerReferenceID : null;
        $delivery_status = isset($request->deliveryStatus) && !empty($request->deliveryStatus) ? $request->deliveryStatus : null;
        $status = isset($request->status) && !empty($request->status) ? $request->status : null;
        $delivery_date = isset($request->deliveryDate) && !empty($request->deliveryDate) ? $request->deliveryDate : null;
        $pickup_date = isset($request->pickUpDate) && !empty($request->pickUpDate) ? $request->pickUpDate : null;
        $arrived_at_partner_date = isset($request->arrivedAtPartnerDate) && !empty($request->arrivedAtPartnerDate) ? $request->arrivedAtPartnerDate : null;
//        $user_id = isset($request->user) && isset($request->user['id']) ? $request->user['id'] : null;
        if($partner_reference_id){
//            $order = Order::whereId($partner_reference_id)->whereUserId($user_id)->first();
            $order = Order::whereId($partner_reference_id)->first();
            if($order){
                $user = $order->user;
                /**
                 * check alert for low inventory stock
                 * only in case of cash because online orders are being updated on payment success
                 */
                if($user && $order->payment_type == 'cash' && $delivery_status == 'delivered'){
                    foreach (PackageItem::whereUserId($order->user_id)->whereOrderId($order->id)->get() as $item){
                        $real_item = $item->item;
                        if($real_item){
                            $new_quantity = $real_item->quantity - $item->quantity;
                            /**
                             * check alert for low inventory stock
                             */
                            if($new_quantity <= env('LOW_INVENTORY_STOCK')){

                                $info = [];
                                $info['to'] = $to = explode(',', env('LOW_INVENTORY_EMAILS'));
                                $info['user'] = $user;
                                $info['order'] = $order;
                                $info['icon'] = 'alert.png';
                                $info['message'] = 'Low Inventory Stock';
                                $info['item'] = $item;
                                $info['item_link'] = secure_url('admin/items?item_id='.$item->id);
                                $info['title'] = env('APP_NAME') . ' | Low Inventory Stock';
                                Mail::send('emails.low_stock', ['data' => $info], function ($message) use ($info) {
                                    $message->subject($info['title']);
                                    $message->to($info['to']);
                                });
                            }

                            $real_item->quantity = $new_quantity;
                            $real_item->save();
                        }
                    }
                }

                $order->delivery_status = $delivery_status;
                $order->status = $status;
                $order->delivery_date = $delivery_date;
                $order->pickup_date = $pickup_date;
                $order->arrived_at_partner_date = $arrived_at_partner_date;
                $order->save();
            }
        }
        return parent::return_json(['status' => 'success']);
    }

    public function stripe_payment_response(Request $request) {
        try{
            $is_test = $request->type == '1' ? true : false;
            if($is_test){
                Stripe::setApiKey(env('STRIPE_TEST_API_KEY'));
            }else{
                Stripe::setApiKey(env('STRIPE_API_KEY'));
            }
            $user_id = $request->user_id;
            $order_id = $request->order_id;
            $post_id = $request->post_id;
            $user = Users::whereId($user_id)->first();
            if(!$user){
                $result['message'] = 'Unable to fetch user';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'),$result);
            }
            $order = Order::whereId($order_id)->first();
            if(!$user){
                $result['message'] = 'Unable to fetch order';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'),$result);
            }
            if($order->user_id != $user_id){
                $result['message'] = 'order and user does not match';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'),$result);
            }
            if($order->status != 'pending'){
                $result['message'] = 'Invalid order status';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'),$result);
            }
            $post = Item::whereId($post_id)->first();
            if(!$post){
                $result['message'] = 'Unable to fetch post';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'),$result);
            }
            if($post->user_id != $user_id){
                $result['message'] = 'post and user does not match';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'),$result);
            }

            /**
             * create customer if new user
             */
            if($user->customer_id != null){
                /**
                 * need to update the card
                 */
                $customer = Customer::retrieve($user->customer_id);

                if($request->stripeToken){
                    $token_card = Token::retrieve($request->stripeToken);

                    $customer = Customer::update($user->customer_id, [
                        'source' => $token_card->id,
                    ]);
                }
                $token_card = Customer::retrieveSource($user->customer_id, $customer->default_source);

                $customer = json_decode(json_encode($customer),True);
                $token_card = json_decode(json_encode($token_card),True);
                $token_card['card'] = $token_card;
            }else{

                $stripe_token = $request->stripeToken;

                $token_card = Token::retrieve($stripe_token);
//                $token = Token::create([
//                    "card" => array(
//                        "number" => $request->card_number,
//                        "exp_month" => $request->month,
//                        "exp_year" => $request->year,
//                        "cvc" => $request->cvv
//                    )
//                ]);
                $token_card = json_decode(json_encode($token_card),True);
                $customer = Customer::create([
                    'description' => 'Customer for '.$user->email,
                    'source' => $token_card['id']
                ]);
                $customer = json_decode(json_encode($customer),True);
                $user->customer_id = $customer['id'];
                $user->save();
            }
            if($user_card = UserCard::whereUserId($user->id)->first()){
                $user_card->card_token = $token_card['id'];
                $user_card->card = $token_card['card']['id'];
                $user_card->brand = $token_card['card']['brand'];
                $user_card->exp_month = $token_card['card']['exp_month'];
                $user_card->exp_year = $token_card['card']['exp_year'];
                $user_card->last4 = $token_card['card']['last4'];
                $user_card->funding = $token_card['card']['funding'];
                $user_card->country = $token_card['card']['country'];
                $user_card->cvc_check = $token_card['card']['cvc_check'];
                $user_card->finger_print = $token_card['card']['fingerprint'];
                $user_card->save();
            }else{
                $user_card = UserCard::create([
                    'user_id' => $user->id,
                    'card' => $token_card['card']['id'],
                    'card_token' => $token_card['id'],
                    'brand' => $token_card['card']['brand'],
                    'exp_month' => $token_card['card']['exp_month'],
                    'exp_year' => $token_card['card']['exp_year'],
                    'last4' => $token_card['card']['last4'],
                    'funding' => $token_card['card']['funding'],
                    'country' => $token_card['card']['country'],
                    'cvc_check' => $token_card['card']['cvc_check'],
                    'finger_print' => $token_card['card']['fingerprint'],

                ]);
            }
            $amount = $post->category->price* 100;
            // if($setting = Setting::whereMetaName('post_cost')->first()){
            //     $amount = $setting->meta_value * 100;
            // }
            $response = Charge::create([
                "amount" => $amount,
                "currency" => strtolower(env('APP_CURRENCY')),
                "source" => $customer['default_source'],
                "customer" => $customer['id'],
                "description" => $post->status == 'expired' ? "Renew for ".$user->email." #".$post_id." Order#".$order_id : "Charge for ".$user->email." #".$post_id." Order#".$order_id
            ]);
            $order->status = $response['status'];
            $order->total_price = $response['amount'];
            $order->payment_return_request = json_encode($request->all());
            $order->payment_return_response = json_encode($response);
            $order->save();
            $response = json_decode(json_encode($response),True);
            if($response['status'] == 'succeeded'){
                $post->status = "in_review";
                $post->save();
                $result['message'] = 'Thank you for bartering with us. Your post will be reviewed within 24 hours. You will be updated once it is live.';
                $result['result'] = 'success';
                if($user->email != null){
                    $info = [];
                    $info['to'] = $user->email;
                    $info['user'] = $user;
                    $info['post'] = $post;
                    $info['amount'] = $response['amount'];
                    $info['title'] = 'Payment Successfull';
                    try {
                        Mail::send('emails.payment_successful', ['data' => $info], function ($message) use ($info) {
                            $message->subject($info['title']);
                            $message->to($info['to']);
                        });
                    }catch (\Exception $ex){

                    }
                }
                                
                parent::redirect(secure_url('/api/check_result'), $result);

            }else{
                $result['message'] = 'Payment Failure';
                $result['result'] = 'failure';
                parent::redirect(secure_url('/api/check_result'), $result);
            }
        }catch(Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $result['message'] = $err['message'];
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
        } catch (RateLimit $e) {
            $result['message'] = 'Too many requests made to the API too quickly';
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
        } catch (InvalidRequest $e) {
            $result['message'] = 'Invalid parameters were supplied to Stripe API';
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
        } catch (Authentication $e) {
            $result['message'] = 'Authentication with Stripe API failed';
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
            // (maybe you changed API keys recently)
        } catch (ApiConnection $e) {
            $result['message'] = 'Network communication with Stripe failed';
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
        } catch (Base $e) {
            $result['message'] = 'Base Error';
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
            // Display a very generic error to the user, and maybe send
            // yourself an email
        } catch (Exception $e) {
            $result['message'] = 'Error!';
            $result['result'] = 'failure';
            parent::redirect(secure_url('/api/check_result'),$result);
            // Something else happened, completely unrelated to Stripe
        }
        $result['message'] = "User does not exist";
        $result['result'] = 'failure';
        parent::redirect(secure_url('/api/check_result'),$result);
    }
}
