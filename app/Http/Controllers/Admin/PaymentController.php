<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\CheckoutLog;
use App\Models\Api\Order;
use App\Models\Api\Payment;
use App\Models\Api\PaymentLog;

use App\Models\Api\Users;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class PaymentController
 */
class PaymentController extends Controller
{
    /**
     * Show the payment page.
     *
     * @return View
     */
    public function show(Request $request,$user_id = null,$order_id = null) /*: View*/
    {

        if($user_id != null && $order_id != null){

            if(!$user = Users::whereId($user_id)->first()){
                abort(401);
            }

            if(!$order = CheckoutLog::whereUsersId($user_id)->whereId($order_id)->first()){
                abort(401);
            }

            if(!$order->insurance){
                abort(401);
            }

            $amount = number_format((float)$order->insurance->price, 2, '.', '');
            $currency = $order->insurance->currency;
            if($currency == null || $amount == null || $amount <= 0){
                abort(401);
            }
            $description = null;
            if($order->insurance_type == 'third_party'){
                $description = 'Third Party Insurance';
            }elseif ($order->insurance_type == 'travel'){
                $description = 'Travel Insurance';
            }elseif ($order->insurance_type == 'motor_all_risk'){
                $description = 'Motor All Risk Insurance';
            }

            $return_url = url('/api/payment_response').'/'.$user_id.'_'.$order_id;
            if($order->session_id == null){

                $data = [
                    'apiUsername' => 'merchant.' . env('PAYMENT_MERCHANT_NUMBER'),
                    'apiPassword' => env('PAYMENT_API_PASSWORD'),
//            'interaction.returnUrl' => $return_url,
                    'apiOperation' => 'CREATE_CHECKOUT_SESSION',
                    'merchant' => env('PAYMENT_MERCHANT_NUMBER'),
                    'order.id' => $order_id,
                    'order.currency' => $currency,
                    'order.amount' => $amount
                ];
                $info['data'] = $data;
                $session_info = parent::manage_session($info);
                if(isset($session_info['error'])){
                    abort(401);
                }
                dd($data,$session_info,$return_url,env('PAYMENT_SESSION_BASE_URL'),http_build_query($data));
                $order->session_info = $session_info;
                parse_str($session_info, $session_details);
                if(isset($session_details['error_cause'])){
                    abort(401);
                }
                $order->session_id = $session_details['session_id'];
                $order->save();
                if($session_details['result'] != 'SUCCESS' || strtoupper($session_details['merchant']) != strtoupper(env('PAYMENT_MERCHANT_NUMBER'))){
                    abort(401);
                }


                $data = [
                    'session_id' => $session_details['session_id'],
                    'amount' => $amount,
                    'description' => $description,
                    'currency' => $currency,
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'return_url' => $return_url
                ];
            }else{
                $data = [
                    'session_id' => $order->session_id,
                    'amount' => $amount,
                    'description' => $description,
                    'currency' => $currency,
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'return_url' => $return_url
                ];
            }

            $return_url = url('/api/payment_response').'/'.$user_id.'_'.$order_id;
            $data['return_url'] = $return_url;
            return view('pages.payment.index', compact('data'));
        }else{
            abort(401);
        }
    }

}
