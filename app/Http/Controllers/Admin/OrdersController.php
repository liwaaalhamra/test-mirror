<?php

namespace App\Http\Controllers\Admin;


use App\Models\Api\Item;
use App\Models\Api\Order;
use App\Models\Api\OrderStatus;
use App\Models\Api\PrivateHospital;
use App\Models\Api\PublicHospital;
use App\Models\Api\Restaurant;
use App\Models\Api\Role;
use App\Models\Api\SupplierFoodGroup;
use App\Models\Api\User;
use App\Models\Api\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ResetRequest;

/**
 * @author Hassan Itani
 * @package App\Http\Controllers\Admin
 */
class OrdersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $orders = Order::where(function($query) use ($request){
            if ($request->has('status')){
                $status = $request->get('status');
                $query->whereStatus($status);
            }
            if ($request->has('order_id')){
                $order_id = $request->get('order_id');
                $query->whereId($order_id);
            }
            if ($request->has('delivery_status')){
                $delivery_status = $request->get('delivery_status');
                $query->whereDeliveryStatus($delivery_status);
            }
            if ($request->has('payment_type')){
                $payment_type = $request->get('payment_type');
                $query->wherePaymentType($payment_type);
            }
            if ($request->has('merchant_type')){
                $merchant_type = $request->get('merchant_type');
                $query->whereMerchantType($merchant_type);
            }
            if ($request->has('user_id')){
                $user_id = $request->get('user_id');
                $query->whereUserId($user_id);
            }
            if ($request->has('restaurant_id')){
                $restaurant_id = $request->get('restaurant_id');
                $query->whereMerchantType('restaurant')->whereMerchantId($restaurant_id);
            }
            if ($request->has('supplier_id')){
                $supplier_id = $request->get('supplier_id');
                $query->whereMerchantType('supplier')->whereMerchantId($supplier_id);
            }
            if(!empty($request->min) && !empty($request->max)){
                $from = Carbon::createFromTimestamp(strtotime($request->min))->format('Y-m-d');
                $to = Carbon::createFromTimestamp(strtotime($request->max))->format('Y-m-d');
                $query->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to);
            }
        })->orderby('id','desc')->get();



        $selected_status = 'all';
        if ($request->has('status')){
            $selected_status = $request->get('status');
        }
        $selected_user = 'all';
        if ($request->has('user_id')){
            $selected_user = $request->get('user_id');
        }
        $selected_restaurant = 'all';
        if ($request->has('restaurant_id')){
            $selected_restaurant = $request->get('restaurant_id');
        }
        $selected_supplier = 'all';
        if ($request->has('supplier_id')){
            $selected_supplier = $request->get('supplier_id');
        }
        $selected_delivery_status = 'all';
        if ($request->has('delivery_status')){
            $selected_delivery_status = $request->get('delivery_status');
        }
        $selected_payment_type = 'all';
        if ($request->has('payment_type')){
            $selected_payment_type = $request->get('payment_type');
        }
        $selected_merchant_type = 'all';
        if ($request->has('merchant_type')){
            $selected_merchant_type = $request->get('merchant_type');
        }
        $from = null;
        $to = null;
        if(!empty($request->min) && !empty($request->max)){
            $from = strtotime($request->min);
            $to = strtotime($request->max);
        }

        $restaurants = Restaurant::get();
        $suppliers = SupplierFoodGroup::get();
        $users = Users::whereIsVerified(1)->get();
        $status = OrderStatus::orderby('item_order','asc')->get();
        $payment_types = ['cash','online'];
        $merchant_types = ['restaurant','supplier'];

        return view('pages.orders.index', compact('orders','status','delivery_status',
            'users','payment_types','from','to','suppliers','restaurants','selected_supplier','selected_restaurant',
            'selected_status','selected_delivery_status','selected_payment_type','selected_user','merchant_types','selected_merchant_type'))
            ->with('section_name', 'orders')
            ->with('content_header', 'Orders');
    }

    public function check_new_order(){

        $total_orders = auth()->user()->total_orders;
        $new_total_orders = Order::count();
        if($total_orders < $new_total_orders){
            $user = auth()->user();
            $user->total_orders = $new_total_orders;
            $user->save();
            return 'ok';
        }else{
            return 'ko';
        }
    }

    public function invoice($order_id)
    {
        $order = Order::findOrFail($order_id);
        $order_date = Carbon::createFromTimestamp($order->created_at)->format('d/m/Y');
        $delivery_date = $order->delivery_date ? Carbon::createFromTimestamp($order->delivery_date)->format('d/m/Y') : 'N/A';
        $pickup_date = $order->pickup_date ? Carbon::createFromTimestamp($order->pickup_date)->format('d/m/Y') : 'N/A';
        $arrived_at_partner_date = $order->arrived_at_partner_date ? Carbon::createFromTimestamp($order->arrived_at_partner_date)->format('d/m/Y') : 'N/A';

        $message = '<p>Following your order on <strong>'.$order_date.'</strong>, you can find below the details of the purchased items.</p>';
        $currency = $order->currency;
        $items =  Item::join('package_items', 'package_items.item_id','=','items.id')
            ->whereOrderCode($order->code)
            ->selectraw('items.*,package_items.quantity as quantity_in_cart,package_items.price as final_price')
            ->get()->map(function($value, $key) use ($currency) {
                $value->final_price_formatted = number_format($value->final_price,2) . ' ' . $currency;
                $value->currency = $currency;
                $value->price_formatted = number_format($value->price,2) . ' ' . $currency;
                $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,2) . ' ' . $currency : null;
                $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,2) . ' ' . $currency : null;
                return $value;
            })->values();

        $sub_total_amount = $order->total_price + $order->discount_amount;
        $delivery_charge = $order->delivery_charge;
        $discount_amount = -$order->discount_amount;
        $total_amount = $order->total_price + $order->delivery_charge;
        $total_amount_letters = ceil($total_amount);


//        $number_formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
//        $total_amount_letters = $number_formatter->format($total_amount_letters) .' Kuwaiti Dinar';
        $total_amount_letters = ' Kuwaiti Dinar';
        $total_amount = number_format((float)$total_amount, 0, '.', ',').' KWD';
        $discount_amount = number_format((float)$discount_amount, 0, '.', ',').' KWD';
        $delivery_charge = number_format((float)$delivery_charge, 0, '.', ',').' KWD';
        $sub_total_amount = number_format((float)$sub_total_amount, 0, '.', ',').' KWD';

        $user = $order->user;
        $invoice_number = 'INV-'.$order->code;
        $admin_username = ucwords(auth()->user()->first_name) .' '.ucwords(auth()->user()->last_name);
        $signature_department = env('INVOICE_SIGNATURE_DEPARTMENT');
        $signature_company = env('INVOICE_SIGNATURE_COMPANY');
        $vat = env('INVOICE_VAT');
        return view('pages.orders.invoice', compact('total_amount','total_amount_letters','admin_username','signature_company','signature_department',
            'vat','address','message','discount_amount','delivery_charge','sub_total_amount','order',
            'order_date','items','delivery_fees','total_order_amount','delivery_date','pickup_date','arrived_at_partner_date','invoice_number','user'))
            ->with('content_header', 'Order Invoice');
    }

    public function print_invoice($order_id)
    {
        $order = Order::findOrFail($order_id);
        $order_date = Carbon::createFromTimestamp($order->created_at)->format('d/m/Y');
        $delivery_date = $order->delivery_date ? Carbon::createFromTimestamp($order->delivery_date)->format('d/m/Y') : 'N/A';
        $pickup_date = $order->pickup_date ? Carbon::createFromTimestamp($order->pickup_date)->format('d/m/Y') : 'N/A';
        $arrived_at_partner_date = $order->arrived_at_partner_date ? Carbon::createFromTimestamp($order->arrived_at_partner_date)->format('d/m/Y') : 'N/A';

        $message = '<p>Following your order on <strong>'.$order_date.'</strong>, you can find below the details of the purchased items.</p>';
        $currency = $order->currency;
        $items =  Item::join('package_items', 'package_items.item_id','=','items.id')
            ->whereOrderCode($order->code)
            ->selectraw('items.*,package_items.quantity as quantity_in_cart,package_items.price as final_price')
            ->get()->map(function($value, $key) use ($currency) {
                $value->final_price_formatted = number_format($value->final_price,2) . ' ' . $currency;
                $value->currency = $currency;
                $value->price_formatted = number_format($value->price,2) . ' ' . $currency;
                $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,2) . ' ' . $currency : null;
                $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,2) . ' ' . $currency : null;
                return $value;
            })->values();

        $sub_total_amount = $order->total_price + $order->discount_amount;
        $delivery_charge = $order->delivery_charge;
        $discount_amount = -$order->discount_amount;
        $total_amount = $order->total_price + $order->delivery_charge;
        $total_amount_letters = ceil($total_amount);


//        $number_formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
//        $total_amount_letters = $number_formatter->format($total_amount_letters) .' Kuwaiti Dinar';
        $total_amount_letters = ' Kuwaiti Dinar';
        $total_amount = number_format((float)$total_amount, 0, '.', ',').' KWD';
        $discount_amount = number_format((float)$discount_amount, 0, '.', ',').' KWD';
        $delivery_charge = number_format((float)$delivery_charge, 0, '.', ',').' KWD';
        $sub_total_amount = number_format((float)$sub_total_amount, 0, '.', ',').' KWD';

        $user = $order->user;
        $invoice_number = 'INV-'.$order->code;
        $admin_username = ucwords(auth()->user()->first_name) .' '.ucwords(auth()->user()->last_name);
        $signature_department = env('INVOICE_SIGNATURE_DEPARTMENT');
        $signature_company = env('INVOICE_SIGNATURE_COMPANY');
        $vat = env('INVOICE_VAT');
        return view('pages.orders.print', compact('total_amount','total_amount_letters','admin_username','signature_company','signature_department',
            'vat','address','message','discount_amount','delivery_charge','sub_total_amount','order',
            'order_date','items','delivery_fees','total_order_amount','delivery_date','pickup_date','arrived_at_partner_date','invoice_number','user'))
            ->with('content_header', 'Order Invoice');
    }

    public function status($id,$status){
        $order = Order::find($id);
        if($order){
            $order->status = $status;
            $order->save();
        }
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order Status Successfully updated');
    }

    public function get_orders(){
        $orders = Order::get();
        if(auth()->user()->merchant_id != null){
//            DB::enableQueryLog();
            $orders = Order::wherePharmacyId(auth()->user()->merchant_id)
                ->Where(function ($query) {
                    $query->where('status', '=', 'approved')
                        ->where('payment_type', '=', 'online')
                        ->orWhere('payment_type','=','cash');
                })->get();
//            dd(DB::getQueryLog());
        }
        $data = [];
        $orders->map(function ($order, $index) use(&$data){
            $code = $order->code;
            $user_info = "First Name: <b>".$order->user->first_name."</b><br>
                Last Name: <b>".$order->user->last_name."</b><br>
                Phone Number: <b>".$order->user->primary_phone."</b><br>
                Email: <b>".$order->user->email."</b><br>
                Gender: <b>".ucwords($order->user->gender)."</b><br>
                DOB: <b>".ucwords($order->user->dob)."</b><br>";
            $payment_status = "";
            if($order->payment_type == 'online'){
                $payment_status = $order->status == 'pending' ? "<span class=\"label label-warning\">PENDING</span>" : "<span class=\"label label-success\">APPROVED</span>";
            }
            $payment_type = ucwords($order->payment_type);
            $tag = $order->tag ? $order->tag : 'Pickup From';
            $latlong = "";
            if($order->latitude != 0 && $order->longitude != 0){
                $latlong = "<span class=\"label label-info\">
                                    <a target=\"_blank\" href=\"http://maps.google.com/?q=$order->latitude,$order->longitude\"> <i aria-hidden=\"true\" style=\"display: inline-block\" class=\"fa fa-street-view\"></i>view Location</a>
                                </span>";
            }

            $location_info = "Tag: <b>".$tag."</b><br>
                            Notes: <b>".$order->location_notes."</b><br>
                            Address: <b>".$order->raw_address."</b><br>".$latlong;

            $total_price = $order->total_price." LBP";
            $delivery_charge = "0 LBP";
            if($order->delivery_charge) {
                $delivery_charge = $order->delivery_charge." LBP";
            }
            $notes = $order->notes;
            $pharmacy_action = "No Action";
            if($order->pharmacy_action == 'accept'){
                $pharmacy_action = "I have Items";
            }elseif ($order->pharmacy_action == 'reject'){
                $pharmacy_action = "I Don't Have Items";
            }
            $pharmacy_details = "";
            if($order->pharmacy){
                $pharmacy_details = "Name: <b>".$order->pharmacy->name."</b><br>
                                Description: <b>".$order->pharmacy->description."</b><br>
                                Phone Number: <b>".$order->pharmacy->phone_number."</b><br>
                                Address: <b>".$order->pharmacy->address."</b><br>";
            }
            $nearest_pharmacy = "Action Taken: <b>".$pharmacy_action."</b><br>".$pharmacy_details;
            $created_at = \Carbon\Carbon::createFromTimestamp($order->created_at)->format('Y/m/d H:i:s');
            $action_details = "";

            if(strpos($this->sections,'readonly') === false){
                if(auth()->user()->merchant_id !=null){
                    if($order->pharmacy_action != null){
                        if($order->pharmacy_action == 'accept'){
                            $action_details = "<a href=".route('admin.orders.action', [$order->id,'reject'])." class=\"btn btn-xs btn-danger\">
                                                I Don't Have Items
                                            </a>";
                        }else{
                            $action_details = "<a href=".route('admin.orders.action', [$order->id,'accept']) ." class=\"btn btn-xs btn-success\">
                                                I have Items
                                            </a>";
                        }
                    }else{

                        $action_details = "<a href=". route('admin.orders.action', [$order->id,'reject']) ." class=\"btn btn-xs btn-danger\">
                                            I Don't Have Items
                                        </a>
                                        <a href=".route('admin.orders.action', [$order->id,'accept']) ." class=\"btn btn-xs btn-success\">
                                            I have Items
                                        </a>";
                    }
                }
            }
            $action = $action_details."<a href=".secure_url('admin/orders/'.$order->id.'/items')." class=\"btn btn-xs btn-primary\"><i class=\"fa fa-arrows-alt\"> View Order Items</i></a>";
            $data[] = [
                $code,$user_info,$payment_status,
                $payment_type,$location_info,$total_price,
                $delivery_charge,$notes,$nearest_pharmacy,$created_at,$action
            ];
            return $order;
        });
        return json_encode($data);
    }

    /**
     * Accept/Reject order
     * @param integer $id
     * @return string
     */
    public function action($id,$status){
        $order = Order::findOrFail($id);
        $order->pharmacy_action = $status;
        $order->save();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order Status Successfully updated');
    }

    public function destroy($id)
    {
        $item = Order::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Successfully deleted the Order');
    }

    public function items($order_id)
    {
        $order = Order::findOrFail($order_id);
        if($order->merchant_type == 'restaurant'){
            $items = $order->pre_items()->latest()->get();
        }else{
            $items = $order->supplier_pre_items()->latest()->get();
        }
        return view('pages.orders.items', compact('order','items'))
            ->with('section_name', 'orders')
            ->with('content_header', 'Order Items');
    }

}
