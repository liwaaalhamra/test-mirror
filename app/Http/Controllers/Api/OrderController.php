<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Area;
use App\Models\Api\Category;
use App\Models\Api\CheckoutLog;
use App\Models\Api\Coupon;
use App\Models\Api\CustomizationOption;
use App\Models\Api\Item;
use App\Models\Api\ItemCustomization;
use App\Models\Api\PackageItemOption;
use App\Models\Api\Restaurant;

use App\Models\Api\ItemSize;
use App\Models\Api\LocateUs;
use App\Models\Api\Order;
use App\Models\Api\OrderStatus;
use App\Models\Api\PackageItem;
use App\Models\Api\Region;
use App\Models\Api\Setting;
use App\Models\Api\UserFavorite;
use App\Models\Api\UserLocation;
use App\Traits\HITrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use HITrait;

	public function get(Request $request) {
        $user = $this->validate_user($request);
        $cart = $user->cart()->get()->filter(function ($item,$item_key) use ($user){
            $real_item = $item->item;
            if($real_item){
                if($real_item->quantity < 1){
                    PackageItemOption::wherePackageItemId($item->id)->delete();
                    $item->delete();
                }
                return $real_item->quantity > 0;
            }else{
                PackageItemOption::wherePackageItemId($item->id)->delete();
                $item->delete();
                return $item->quantity > 0;
            }
        });
        return response()->json($cart);
	}

    public function count(Request $request) {
        $user = $this->validate_user($request);
        $merchant_id = $user->cart_restaurant_id;
        $merchant_name = null;
        if($merchant_id){
            $merchant = Restaurant::whereId($merchant_id)->first();
            $merchant_name = $merchant ? $merchant->name : null;
        }
        return response()->json(['count' => $user->cart()->count(),'merchant_id' => $merchant_id,'merchant_name' => $merchant_name]);
    }

    public function add_item(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Item Id required!',401,'messages.missing_parameter');
        }
        if(! $item = Item::whereIsAvailable(1)->whereNull('supplier_food_group_id')->whereId($request->item_id)->first()){
            return parent::return_error('Item not found',405,'messages.item_not_found');
        }

        /**
         * if this item is in new restaurant -> clear the cart first
         */
        if($user->cart_restaurant_id && $item->category_id != $user->cart_restaurant_id){
            $package_item_ids = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereUserId($user->id)->pluck('id')->toarray();
            PackageItemOption::wherein('package_item_id',$package_item_ids)->delete();
            PackageItem::wherein('id',$package_item_ids)->delete();
        }

        $customizations_price = 0;
        $option_ids = [];
        if($request->option_ids){
            $option_ids = $request->option_ids;
            sort($option_ids);
            $option_ids = array_map('intval', $option_ids);
        }

        if(!Input::has('quantity')){
            return parent::return_error('Missing Quantity',401,'messages.missing_parameter');
        }
        if($item->quantity < $request->quantity){
            return parent::return_error('No Enough Quantity',405,'messages.no_enough_quantity');
        }

        $currency = env('APP_CURRENCY');
        /**
         * if item already exists with same customizations => increase quantity
         */
        $package_item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereItemId($request->item_id)->whereUserId($user->id)->get()
            ->filter(function ($p_item,$item_key) use ($option_ids){
                return $p_item->option_ids === $option_ids;
        })->first();
        if($package_item){
            if($item->quantity < $request->quantity + $package_item->quantity){
                return parent::return_error('No Enough Quantity',405,'messages.no_enough_quantity');
            }
            $package_item->quantity = $package_item->quantity + $request->quantity;
            $price = $item->price;
            $discounted_price = null;
            if($item->discounted_price != null){
                $discounted_price = $item->discounted_price;
            }
            $package_item->price = $price;
            $package_item->discounted_price = $discounted_price;
            $package_item->save();
        }else{
            /**
             * save the price at the time being
             */
            $price = $item->price;
            $discounted_price = null;
            if($item->discounted_price != null){
                $discounted_price = $item->discounted_price;
            }
            $package_item = PackageItem::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'notes' => isset($request->notes) ? trim($request->notes) : null,
                'quantity' => $request->quantity,
                'price' => $price,
                'discounted_price' => $discounted_price,
                'customizations_price' => $customizations_price,
                'restaurant_id' => $item->category_id,
                'category_id' => $item->restaurant ? $item->restaurant->category_id : null,
                'supplier_food_group_id' => $item->supplier_food_group ? $item->supplier_food_group_id : null,
                'sub_category_id' => $item->restaurant ? $item->restaurant->sub_category_id : null,
            ]);
        }

        /**
         * update user cart restaurant id
         */
        $user->cart_restaurant_id = $item->category_id;
        $user->save();


        foreach ($option_ids as $option_id){
            if(! $option = CustomizationOption::whereIsPublished(1)->whereId($option_id)->first()){
                return parent::return_error('Option not found',405,'Option not found');
            }
            $customizations_price += $option->price;
            PackageItemOption::updateOrCreate([
                'package_item_id' => $package_item->id,
                'customization_option_id' => $option_id,
            ],[
                'quantity' => 1,
                'price' => $option->price
            ]);
        }
        $package_item->customizations_price = $customizations_price;
        $package_item->save();

        $message = utf8_decode(utf8_encode(Lang::get('messages.item_added_to_cart')));
        $cart = $user->cart()->get()->map(function ($value, $key) use ($user,$currency) {
            $value->final_price_formatted = number_format($value->final_price,0) . ' ' . $currency;
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,0) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,0) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? '-'.number_format($value->discount_amount,0) . ' ' . $currency : null;

            return $value;
        });
        return response()->json($cart);
    }

    public function edit_item(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Item Id required!',401,'messages.missing_parameter');
        }
        if(! $package_item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereUserId($user->id)->whereId($request->item_id)->first()){
            return parent::return_error('Cart Item not found',405,'messages.item_not_found');
        }
        $item = $package_item->item;
        if(!$item){
            return parent::return_error('Item not found',405,'messages.item_not_found');
        }

        if(!Input::has('quantity')){
            return parent::return_error('Missing Quantity',401,'messages.missing_parameter');
        }
        if($request->quantity < 1){
            return parent::return_error('Quantity must have positive value',402,'messages.quantity_positive');
        }

        if($item->quantity < 1 || $item->quantity < $request->quantity){
            return parent::return_error('No Enough Quantity',405,'messages.no_enough_quantity');
        }

        /**
         * save the price at the time being
         */
        $price = $item->price;
        $discounted_price = null;
        if($item->discounted_price != null){
            $discounted_price = $item->discounted_price;
        }


        $customizations_price = 0;
        foreach ($package_item->options as $package_item_option){
            $customization_option = $package_item_option->customization_option;
            if(!$customization_option){
                continue;
            }
            $updated_price = $customization_option->price;
            $package_item_option->price = $updated_price;

            $package_item_option->save();
            /**
             * for now quantity will be always 1 bcz in design there is no quantity in customization
             */
            $customizations_price += $updated_price * $package_item_option->quantity;
        }

        $package_item->update([
            'quantity' => $request->quantity,
            'price' => $price,
            'discounted_price' => $discounted_price,
            'customizations_price' => $customizations_price,
            'restaurant_id' => $item->category_id,
            'category_id' => $item->restaurant ? $item->restaurant->category_id : null,
            'sub_category_id' => $item->restaurant ? $item->restaurant->sub_category_id : null,
            'supplier_food_group_id' => $item->supplier_food_group ? $item->supplier_food_group_id : null,
        ]);


        $currency = env('APP_CURRENCY');
        $cart = $user->cart()->get()->map(function ($value, $key) use ($user,$currency) {
            $value->final_price_formatted = number_format($value->final_price,0) . ' ' . $currency;
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,0) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,0) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? '-'.number_format($value->discount_amount,0) . ' ' . $currency : null;

            return $value;
        });
        return response()->json($cart);
    }

    public function delete_item(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Cart Id required!',401,'messages.missing_parameter');
        }
        if(! $item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereId(Input::get('item_id'))->whereUserId($user->id)->first()){
            return parent::return_error('Item not found in cart',405,'messages.item_not_found_in_cart');
        }
        PackageItemOption::wherePackageItemId($item->id)->delete();
        $item->delete();
        if($user->cart()->count() == 0){
            $user->cart_restaurant_id = null;
            $user->save();
        }
        return response()->json($user->cart);
    }

    public function delete_selected_items(Request $request){
        $user = $this->validate_user($request);
        foreach ($request->item_ids as $index=>$item_id){
            if(! $item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereId($item_id)->whereUserId($user->id)->first()){
                return parent::return_error('Item not found in cart',405,'messages.item_not_found_in_cart');
            }
            PackageItemOption::wherePackageItemId($item->id)->delete();
            $item->delete();
        }

        if($user->cart()->count() == 0){
            $user->cart_restaurant_id = null;
            $user->save();
        }


        return response()->json($user->cart);
    }

    public function clear(Request $request){
        $user = $this->validate_user($request);
        /**
         * if order_id is null, it means the item still in the cart
         */
        $package_item_ids = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereUserId($user->id)->pluck('id')->toarray();
        PackageItemOption::wherein('package_item_id',$package_item_ids)->delete();
        PackageItem::wherein('id',$package_item_ids)->delete();

        $user->cart_restaurant_id = null;
        $user->save();
        return parent::return_json(['status' => utf8_decode(utf8_encode(Lang::get('messages.clear_cart')))]);
    }

    public function order_status_list(Request $request){
        $user = $this->validate_user($request);
        $list = OrderStatus::select('id','status','label')->orderby('item_order','asc')->get();
        return parent::return_json($list);
    }

    public function total_price(Request $request){
        $user = $this->validate_user($request);
        $discount_amount = 0;

        if(Input::has('item_ids') ){
            $total_price = 0;
            foreach ($request->item_ids as $index=>$item_id){
                $item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereItemId($item_id)->whereUserId($user->id)->first();
                if($item){
                    $customizations_price = 0;
                    foreach ($item->options as $package_item_option){
                        $customization_option = $package_item_option->customization_option;
                        if(!$customization_option){
                            continue;
                        }
                        $updated_price = $customization_option->price;
                        $package_item_option->price = $updated_price;
                        $package_item_option->save();
                        /**
                         * for now quantity will be always 1 bcz in design there is no quantity in customization
                         */
                        $customizations_price += $updated_price * $package_item_option->quantity;
                    }
                    /**
                     * update customizations price in package items
                     */
                    $item->customizations_price = $customizations_price;
                    if($item->item){
                        $price = $item->item->price;
                        /**
                         * update price in package items
                         */
                        $item->price = $price;

                        if($item->item->discounted_price != null){
                            $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                            $price = $item->item->discounted_price;
                            /**
                             * update discounted price in package items
                             */
                            $item->discounted_price = $price;
                        }
                        $item->save();
                        $price = ($price + $customizations_price) * $item->quantity ;
                        $total_price += $price;
                    }else{
                        $item->delete();
                    }
                }
            }
        }else{
            $total_price = 0;
            $all_items = PackageItem::whereNotNull('item_id')->whereNull('supplier_food_group_id')->whereNull('order_id')->whereUserId($user->id)->get();
            foreach ($all_items as $item){
                $customizations_price = 0;
                foreach ($item->options as $package_item_option){
                    $customization_option = $package_item_option->customization_option;
                    if(!$customization_option){
                        continue;
                    }
                    $updated_price = $customization_option->price;
                    $package_item_option->price = $updated_price;
                    $package_item_option->save();
                    /**
                     * for now quantity will be always 1 bcz in design there is no quantity in customization
                     */
                    $customizations_price += $updated_price * $package_item_option->quantity;
                }
                /**
                 * update customizations price in package items
                 */
                $item->customizations_price = $customizations_price;
                if($item->item){
                    $price = $item->item->price;
                    /**
                     * update price in package items
                     */
                    $item->price = $price;
                    if($item->item->discounted_price != null){
                        $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                        $price = $item->item->discounted_price;
                        /**
                         * update discounted price in package items
                         */
                        $item->discounted_price = $price;
                    }
                    $item->save();
                    $price = ($price  + $customizations_price) * $item->quantity;
                    $total_price += $price;
                }else{
                    $item->delete();
                }
            }
        }
        $original_total_price = $total_price;

        $coupon = $user->coupon;

        $coupon_code = null;
        $coupon_amount = null;
        if($coupon){
            //we will take the code from coupon table cz this field must not be edited from cms
            $coupon_code = $coupon->code;
            $nb_uses = Order::whereCouponId($user->coupon_id)->count();
            $now = Carbon::now()->timestamp;
            //if coupon still valid calculate the discount based on details saved in profile not from coupon table [could be updated since last time coupon applied in cart]
            if($nb_uses < $coupon->max_uses && $now <= $coupon->end_date && $now >= $coupon->start_date){
                if($user->coupon_type == 'percentage'){
                    $coupon_amount += $total_price * ($user->coupon_amount/100);
                    $total_price = $total_price - ($total_price * ($user->coupon_amount/100));
                }else{
                    $total_price = $total_price - $user->coupon_amount;
                    $coupon_amount += $user->coupon_amount;
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
        $delivery_charge = isset($request->address_type) && $request->address_type == 'pickup' ? 0 : $delivery_charge;

        $currency = env('APP_CURRENCY');

        $new_total_price = $total_price + floatval($delivery_charge);
        $new_total_price = number_format($new_total_price,0) .' '.$currency;
        $total_price = number_format($total_price,0) .' '.$currency;

        $new_original_total_price = $original_total_price + floatval($delivery_charge);
        $new_original_total_price = number_format($new_original_total_price,0) .' '.$currency;
        $original_total_price = number_format($original_total_price,0) .' '.$currency;

        $delivery_charge = $delivery_charge ? number_format($delivery_charge,0) .' '.$currency : null;
        $discount_amount = $discount_amount ? '-'.number_format($discount_amount,0) .' '.$currency : null;
        $coupon_amount = $coupon_amount ? '-'.number_format($coupon_amount,0) .' '.$currency : null;

        if($user->cart()->count() == 0){
            $new_original_total_price = '0.00 '.$currency;
            $new_total_price = '0.00 '.$currency;
            $original_total_price = '0.00 '.$currency;
            $total_price = '0.00 '.$currency;
            $delivery_charge = null;
            $discount_amount = null;
            $coupon_amount = null;
            $coupon_code = null;
        }

        $data = [
            'original_total_price' => $new_original_total_price,
            'total_price' => $new_total_price,
            'original_sub_total' => $original_total_price,
            'sub_total' => $total_price,
            'delivery_charge' => $delivery_charge,
            'discount_amount' => $discount_amount,
            'coupon_amount' => $coupon_amount,
            'coupon_code' => $coupon_code,
        ];
        return parent::return_json($data);
    }

    public function apply_coupon_code(Request $request){
        $user = $this->validate_user($request);
        $discount_amount = 0;
        if(Input::has('item_ids') ){
            $total_price = 0;
            foreach ($request->item_ids as $index=>$item_id){
                if(! $item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereItemId($item_id)->whereUserId($user->id)->first()){
                    return parent::return_error('Item not found in cart',405,'messages.item_not_found_in_cart');
                }
                $customizations_price = 0;
                foreach ($item->options as $package_item_option){
                    $customization_option = $package_item_option->customization_option;
                    if(!$customization_option){
                        continue;
                    }
                    $updated_price = $customization_option->price;
                    $package_item_option->price = $updated_price;
                    $package_item_option->save();
                    /**
                     * for now quantity will be always 1 bcz in design there is no quantity in customization
                     */
                    $customizations_price += $updated_price * $package_item_option->quantity;
                }
                /**
                 * update customizations price in package items
                 */
                $item->customizations_price = $customizations_price;
                $price = $item->item->price;
                /**
                 * update price in package items
                 */
                $item->price = $price;
                if($item->item->discounted_price != null){
                    $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                    $price = $item->item->discounted_price;
                    /**
                     * update discounted price in package items
                     */
                    $item->discounted_price = $price;
                }
                $item->save();
                $price = ($price  + $customizations_price) * $item->quantity;
                $total_price += $price;
            }
        }else{
            $total_price = 0;
            foreach (PackageItem::whereNotNull('item_id')->whereNull('order_id')->whereNull('supplier_food_group_id')->whereUserId($user->id)->get() as $item){
                $price = $item->item->price;
                /**
                 * update price in package items
                 */
                $item->price = $price;
                $customizations_price = 0;
                foreach ($item->options as $package_item_option){
                    $customization_option = $package_item_option->customization_option;
                    if(!$customization_option){
                        continue;
                    }
                    $updated_price = $customization_option->price;
                    $package_item_option->price = $updated_price;
                    $package_item_option->save();
                    /**
                     * for now quantity will be always 1 bcz in design there is no quantity in customization
                     */
                    $customizations_price += $updated_price * $package_item_option->quantity;
                }
                /**
                 * update customizations price in package items
                 */
                $item->customizations_price = $customizations_price;
                if($item->item->discounted_price != null){
                    $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                    $price = $item->item->discounted_price;
                    /**
                     * update discounted price in package items
                     */
                    $item->discounted_price = $price;
                }
                $item->save();
                $price = ($price + $customizations_price) * $item->quantity;
                $total_price += $price;
            }
        }
        $original_total_price = $total_price;
        if(!Input::has('coupon_code')){
            return parent::return_error('Coupon Code required!',401,'messages.missing_parameter');
        }
        $coupon_code = Coupon::whereIsPublished(1)->whereCode($request->coupon_code)->first();
        if(!$coupon_code){
            return parent::return_error('Coupon not found',405,'messages.coupon_not_found');
        }
        $nb_uses = Order::whereCouponId($coupon_code->id)->count();
        if($nb_uses >= $coupon_code->max_uses){
            return parent::return_error('Coupon not found',406,'messages.coupon_not_found');
        }
        $now = Carbon::now()->timestamp;
        if($now > $coupon_code->end_date || $now < $coupon_code->start_date){
            return parent::return_error('Coupon not found',407,'messages.coupon_not_found');
        }
        $coupon_amount = 0;
        if($coupon_code->type == 'percentage'){
            $coupon_amount = $total_price * ($coupon_code->amount/100);
            $total_price = $total_price - ($total_price * ($coupon_code->amount/100));
        }else{
            $total_price = $total_price - $coupon_code->amount;
            $coupon_amount = $coupon_code->amount;
        }
        $user->coupon_id = $coupon_code->id;
        $user->coupon_amount = $coupon_code->amount;
        $user->coupon_type = $coupon_code->type;
        $user->save();

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
        $currency = env('APP_CURRENCY');

        $new_total_price = $total_price + floatval($delivery_charge);
        $new_total_price = number_format($new_total_price,0) .' '.$currency;
        $total_price = number_format($total_price,0) .' '.$currency;

        $new_original_total_price = $original_total_price + floatval($delivery_charge);
        $new_original_total_price = number_format($new_original_total_price,0) .' '.$currency;
        $original_total_price = number_format($original_total_price,0) .' '.$currency;

        $delivery_charge = $delivery_charge ? number_format($delivery_charge,0) .' '.$currency : null;
        $discount_amount = $discount_amount ? '-'.number_format($discount_amount,0) .' '.$currency : null;
        $coupon_amount = $coupon_amount ? '-'.number_format($coupon_amount,0) .' '.$currency : null;

        $data = [
            'original_total_price' => $new_original_total_price,
            'total_price' => $new_total_price,
            'original_sub_total' => $original_total_price,
            'sub_total' => $total_price,
            'delivery_charge' => $delivery_charge,
            'discount_amount' => $discount_amount,
            'coupon_amount' => $coupon_amount,
            'coupon_code' => $request->coupon_code,
        ];
        return parent::return_json($data);
    }

    public function remove_coupon_code(Request $request) {
        $user = $this->validate_user($request);

        $total_price = 0;
        $discount_amount = 0;
        $coupon_amount = 0;
        $coupon_code = null;
        foreach (PackageItem::whereNotNull('item_id')->whereNull('order_id')->whereNull('supplier_food_group_id')->whereUserId($user->id)->get() as $item){
            $price = $item->item->price;
            /**
             * update price in package items
             */
            $item->price = $price;
            $customizations_price = 0;
            foreach ($item->options as $package_item_option){
                $customization_option = $package_item_option->customization_option;
                if(!$customization_option){
                    continue;
                }
                $updated_price = $customization_option->price;
                $package_item_option->price = $updated_price;
                $package_item_option->save();
                /**
                 * for now quantity will be always 1 bcz in design there is no quantity in customization
                 */
                $customizations_price += $updated_price * $package_item_option->quantity;
            }
            /**
             * update customizations price in package items
             */
            $item->customizations_price = $customizations_price;
            if($item->item->discounted_price != null){
                $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                $price = $item->item->discounted_price;
                /**
                 * update discounted price in package items
                 */
                $item->discounted_price = $price;
            }
            $item->save();
            $price = ($price + $customizations_price) * $item->quantity;
            $total_price += $price;
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

        $user->coupon_id = null;
        $user->coupon_type = null;
        $user->coupon_amount = null;
        $user->save();

        $currency = env('APP_CURRENCY');

        $new_total_price = $total_price + floatval($delivery_charge);
        $new_total_price = number_format($new_total_price,0) .' '.$currency;
        $total_price = number_format($total_price,0) .' '.$currency;

        $delivery_charge = $delivery_charge ? number_format($delivery_charge,0) .' '.$currency : null;
        $discount_amount = $discount_amount ? '-'.number_format($discount_amount,0) .' '.$currency : null;
        $coupon_amount = $coupon_amount ? '-'.number_format($coupon_amount,0) .' '.$currency : null;

        $data = [
            'original_total_price' => $new_total_price,
            'total_price' => $new_total_price,
            'original_sub_total' => $total_price,
            'sub_total' => $total_price,
            'delivery_charge' => $delivery_charge,
            'discount_amount' => $discount_amount,
            'coupon_amount' => $coupon_amount,
            'coupon_code' => $coupon_code,
        ];
        return response()->json($data);
    }

    public function book(Request $request){
        $user = $this->validate_user($request);
        /**
         * check if restaurant is closed
         */
        if($user->cart_restaurant && $user->cart_restaurant->is_closed){
            return parent::return_error("Restaurant currently closed, please try again later", 444,"Restaurant currently closed, please try again later");
        }
        if($user->cart()->count() == 0){
            return parent::return_error("Your shopping cart is empty", 458,"Your shopping cart is empty");
        }
        $raw_address = null;
        $latitude = null;
        $longitude = null;
        $location_notes = null;

        if(!Input::has('payment_method_code')){
            return parent::return_error("Missing Payment Method Code", 401,'messages.missing_parameter');
        }
        $payment_method_code = $request->payment_method_code;
        if(!Input::has('address_type')){
            return parent::return_error("Missing Address Type", 401,'messages.missing_parameter');
        }
        $address_type = $request->address_type;
        if(!in_array($address_type,['delivery','pickup'])){
            return parent::return_error("Invalid Address Type", 401,'messages.invalid_parameters');
        }
        $latitude = null;
        $longitude = null;
        $street = null;
        $building = null;
        $raw_address = null;
        $region_id = null;
        $city_id = null;
        $location_id = null;
        $floor = null;
        $apartment = null;
        $avenue = null;
        $block = null;
        $tag = null;
        $nickname = null;
        if($address_type == 'delivery'){
            if(!Input::has('location_id')){
                return parent::return_error("Missing Location ID", 401,'messages.missing_parameter');
            }
            if(!$location = UserLocation::whereUsersId($user->id)->whereId($request->location_id)->first()){
                return parent::return_error("Location does not exist", 405, 'messages.invalid_parameters');
            }
            $latitude = $location->lat;
            $longitude = $location->lon;
            $street = $location->street;
            $floor = $location->floor;
            $building = $location->building;
            $raw_address = $location->notes;
            $region_id = $location->region_id;
            $city_id = $location->city_id;
            $location_id = $location->id;
            $apartment = $location->apartment;
            $block = $location->block;
            $avenue = $location->avenue;
            $tag = $location->tag;
            $nickname = $location->nickname;
        }


        $order_date = strtotime('now');
        if(Input::has('order_date')){
            $order_date = $request->order_date;
        }
        $order_time = null;
        if(Input::has('order_time')){
            $order_time = $request->order_time;
        }
        $with_cutlery = null;
        if(Input::has('with_cutlery')){
            $with_cutlery = $request->with_cutlery == 'yes' ? true : false;
        }
        $bring_change = null;
        if(Input::has('bring_change')){
            $bring_change = $request->bring_change == 'yes' ? true : false;
        }


        $friend_name = null;
        $friend_mobile_number = null;

//      $country_code = parent::getGeoCounty($current_latitude, $current_longitude);
        $notes = null;
        if(Input::has('notes')){
            $notes = $request->notes;
        }
        $discount_amount = 0;
        if(Input::has('item_ids')){
            $total_price = 0;
            foreach ($request->item_ids as $index=>$item_id){
                if(! $item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereItemId($item_id)->whereUserId($user->id)->first()){
                    return parent::return_error('Item not found in cart',405,'messages.item_not_found_in_cart');
                }
                $customizations_price = 0;
                foreach ($item->options as $package_item_option){
                    $customization_option = $package_item_option->customization_option;
                    if(!$customization_option){
                        continue;
                    }
                    $updated_price = $customization_option->price;
                    $package_item_option->price = $updated_price;
                    $package_item_option->save();
                    /**
                     * for now quantity will be always 1 bcz in design there is no quantity in customization
                     */
                    $customizations_price += $updated_price * $package_item_option->quantity;
                }
                /**
                 * update customizations price in package items
                 */
                $item->customizations_price = $customizations_price;
                if($item->item){
                    $price = $item->item->price;
                    /**
                     * update price in package items
                     */
                    $item->price = $price;

                    if($item->item->discounted_price != null){
                        $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                        $price = $item->item->discounted_price;
                        /**
                         * update discounted price in package items
                         */
                        $item->discounted_price = $price;
                    }
                    $item->save();
                    $price = ($price + $customizations_price) * $item->quantity;
                    $total_price += $price;
                }else{
                    $item->delete();
                }
            }
        }else{
            $total_price = 0;
            foreach (PackageItem::whereNotNull('item_id')->whereNull('supplier_food_group_id')->whereNull('order_id')->whereUserId($user->id)->get() as $item){
                $customizations_price = 0;
                foreach ($item->options as $package_item_option){
                    $customization_option = $package_item_option->customization_option;
                    if(!$customization_option){
                        continue;
                    }
                    $updated_price = $customization_option->price;
                    $package_item_option->price = $updated_price;
                    $package_item_option->save();
                    /**
                     * for now quantity will be always 1 bcz in design there is no quantity in customization
                     */
                    $customizations_price += $updated_price * $package_item_option->quantity;
                }
                /**
                 * update customizations price in package items
                 */
                $item->customizations_price = $customizations_price;
                if($item->item){
                    $price = $item->item->price;
                    /**
                     * update price in package items
                     */
                    $item->price = $price;

                    if($item->item->discounted_price != null){
                        $discount_amount = $discount_amount + ($price - $item->item->discounted_price);
                        $price = $item->item->discounted_price;
                        /**
                         * update discounted price in package items
                         */
                        $item->discounted_price = $price;
                    }
                    $item->save();
                    $price = ($price + $customizations_price) * $item->quantity;
                    $total_price += $price;
                }else{
                    $item->delete();
                }
            }
        }
        $coupon = $user->coupon;
        $coupon_amount = 0;
        $coupon_code = null;
        if($coupon){
            //we will take the code from coupon table cz this field must not be edited from cms
            $coupon_code = $coupon->code;
            $nb_uses = Order::whereCouponId($user->coupon_id)->count();
            $now = Carbon::now()->timestamp;
            //if coupon still valid calculate the discount based on details saved in profile not from coupon table [could be updated since last time coupon applied in cart]
            if($nb_uses < $coupon->max_uses && $now <= $coupon->end_date && $now >= $coupon->start_date){
                if($user->coupon_type == 'percentage'){
                    $coupon_amount += $total_price * ($user->coupon_amount/100);
                    $total_price = $total_price - ($total_price * ($user->coupon_amount/100));
                }else{
                    $total_price = $total_price - $user->coupon_amount;
                    $coupon_amount = $user->coupon_amount;
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
//                $currency_unit_points = 1;
        $call_us = '+965123123';
        foreach ($settings as $setting){
            if($setting->meta_name == 'delivery_threshold'){
                $delivery_threshold = $setting->meta_value;
            }
            if($setting->meta_name == 'call_us_number'){
                $call_us = $setting->meta_value;
            }
            if($setting->meta_name == 'delivery_charge'){
                $delivery_charge = $setting->meta_value;
            }
//                    if($setting->meta_name == 'currency_unit_points'){
//                        $currency_unit_points = $setting->meta_value;
//                    }
        }
        if($delivery_threshold && $delivery_charge){
            if($total_price >= $delivery_threshold){
                $delivery_charge = 0;
            }
        }
        /**
         * total price in order table is the final one including discount amount/ delivery charge/ coupon amount. this is easy for payment gateway
         * later on if you want to know the original total price you can remove delivery charge and discount amount
         */
        $total_price = $total_price + floatval($delivery_charge);
        $total_price = floatval(number_format((float)$total_price, 3, '.', ''));
        $currency = env('APP_CURRENCY');
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'new_order',
            'type' => 'regular',
            'merchant_type' => 'restaurant',
            'merchant_id' => $user->cart_restaurant_id,
            'order_date' => $order_date,
            'bring_change' => $bring_change,
            'with_cutlery' => $with_cutlery,
            'order_time' => $order_time,
            'payment_type' => $payment_method_code,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'raw_address' => $raw_address,
            'tag' => $tag,
            'nickname' => $nickname,
            'street' => $street,
            'building' => $building,
            'block' => $block,
            'avenue' => $avenue,
            'apartment' => $apartment,
            'floor' => $floor,
            'region_id' => $region_id,
            'city_id' => $city_id,
            'notes' => $notes,
            'location_id' => $location_id,
            'currency' => strtoupper($currency),
            'total_price' => $total_price,
            'delivery_charge' => floatval($delivery_charge),
            'coupon_id' => $coupon ? $coupon->id : null,
            'coupon_amount' => $coupon_amount,
            'discount_amount' => $discount_amount,
        ]);
        $payment_url = null;
        if($payment_method_code != 'cash'){

            $description = 'New Order by '.$user->first_name.' '.$user->last_name;
            $info = [];
            $info['function'] = '/register.do';
            $expiry_date = Carbon::now()->addDays(1)->format('Y-m-d\TH:i:s.u');
            $info['data'] = [
                'username' => config('services.arabs.username'),
                'password' => config('services.arabs.password'),
                'amount' => $total_price,
                'currency' => env('APP_CURRENCY_CODE'),
                'returnUrl' => secure_url('api/payment_success_response'),
                'failUrl' => secure_url('api/payment_error_response'),
                'orderNumber' => $order->id,
                'clientId' => $user->id,
                'language' => \App::getLocale(),
                'description' => $description
            ];
            $checkout = CheckoutLog::create([
                'users_id' => $user->id,
                'order_id' => $order->id,
                'payment_method' => $payment_method_code,
                'payment_request' => json_encode($info),
            ]);
            $response = parent::payment_connect($info);
            if(isset($response['errorCode']) && $response['errorCode'] != '0'){
                $checkout->payment_response = json_encode($response);
                $checkout->save();
                return parent::return_error($response['errorMessage'], 471, "Payment Execution Failure");
            }
            $payment_url = isset($response['formUrl']) ? $response['formUrl'] : null;
            $transaction_id = isset($response['orderId']) ? $response['orderId'] : null;
            $checkout->transaction_id = $transaction_id;
            $checkout->payment_url = $payment_url;
            $checkout->payment_response = json_encode($response);
            $checkout->save();
        }


//                $user->points()->create([
//                    'meta_name' => 'booking',
//                    'points' => $total_price * $currency_unit_points,
//                    'description' => 'New Order: #'.$order->code,
//                ]);

//                $total = 0;
//                foreach ($user->points as $row) {
//                    $total += $row->points;
//                }
//                $user->total_points = $total;
//                $user->save();


        if(Input::has('item_ids') ){
            $total_price = 0;
            foreach ($request->item_ids as $index=>$item_id){
                if(! $item = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereItemId($item_id)->whereUserId($user->id)->first()){
                    return parent::return_error('Item not found in cart',405,'messages.item_not_found_in_cart');
                }
                /**
                 * update order_id in case of cash only
                 * for online case, we will update order_id once payment success
                 */
                if($payment_method_code == 'cash'){
                    $item->order_id = $order->id;
                }

                $item->order_code = $order->code;
                $item->save();
            }
        }else{

            foreach (PackageItem::whereNotNull('item_id')->whereNull('supplier_food_group_id')->whereNull('order_id')->whereUserId($user->id)->get() as &$item){
                /**
                 * update order_id in case of cash only
                 * for online case, we will update order_id once payment success
                 */
                if($payment_method_code == 'cash'){
                    $item->order_id = $order->id;
                }

                $item->order_code = $order->code;
                $item->save();
            }

        }


        if($payment_method_code == 'cash'){
            $user->cart_restaurant_id = null;
            $user->coupon_amount = null;
            $user->coupon_type = null;
            $user->coupon_id = null;
            $user->save();

            $info = [];
            $info['to'] = $to = explode(',', env('ORDER_EMAILS'));
            $info['user'] = $user;
            $info['order'] = $order;
            $info['title'] = 'New Order | Cash on Delivery';
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
        }

        $order->coupon;
        $order->merchant;
        $order->user;
        $order->location;
        $order->items;
        $order->current_status = $order->status;
        $order->status = [];
        return parent::return_json([
            'status' => utf8_decode(utf8_encode(Lang::get('messages.order_submitted_successfully'))),
            'order_id' => $order->id,
            'payment_url' => $payment_url,
            'order' => $order,
        ]);
    }

    public function history(Request $request) {
        $user = $this->validate_user($request);
        $order_statuses = OrderStatus::selectraw('label,status')->whereIsPublished(1)->orderby('item_order','asc')->get()->toArray();
        $currency = env('APP_CURRENCY');
        $history = Order::whereType('regular')->whereUserId($user->id)->orderby('order_date','desc')->get()->map(function ($value, $key) use ($user,$order_statuses,$currency) {
            $value->total_price_formatted = number_format($value->total_price,0) . ' ' . $currency;
            $value->delivery_charge_formatted = $value->delivery_charge ? number_format($value->delivery_charge,0) . ' ' . $currency : null;
            $restaurant = $value->merchant;
            $value->merchant_name = $restaurant ? $restaurant->name : 'n/a';
            $value->merchant_image = $restaurant ? $restaurant->image : null;
            $value->merchant_phone_number = $restaurant ? $restaurant->country_code.$restaurant->phone_number : null;
            /**
             * check call merchant action
             * if minutes passed and order still not delivered, show call button
             */
            $now = Carbon::now();
            $value->can_call_merchant = false;
            if(!$value->is_completed && $now->diffInMinutes(Carbon::createFromTimestamp($value->created_at)) >= config('services.restaurant_call_minutes')){
                $value->can_call_merchant = true;
            }

            unset($value->merchant);
            foreach ($value['items'] as $item){
                $item->price_formatted = number_format($item->price,0) . ' ' . $currency;
                $item->final_price_formatted = number_format($item->final_price,0) . ' ' . $currency;

                $item->currency = $currency;
                $item->price_formatted = number_format($item->price,0) . ' ' . $currency;
                $item->discounted_price_formatted = $item->discounted_price ? number_format($item->discounted_price,0) . ' ' . $currency : null;
                $item->discount_amount_formatted = $item->discount_amount ? '-'.number_format($item->discount_amount,0) . ' ' . $currency : null;
            }
            return $value;
        });
        return response()->json($history);
    }

    public function reorder(Request $request) {
        $user = $this->validate_user($request);
        if(!$order = Order::whereType('regular')->whereUserId($user->id)->whereId($request->order_id)->first()){
            return parent::return_error("Order does not exist", 405, 'messages.item_not_found');
        }
        $user->cart_restaurant_id = $order->merchant_id;
        $user->save();
        /**
         * clear cart first
         */
        $package_item_ids = PackageItem::whereNull('order_id')->whereNull('supplier_food_group_id')->whereUserId($user->id)->pluck('id')->toarray();
        PackageItemOption::wherein('package_item_id',$package_item_ids)->delete();
        PackageItem::wherein('id',$package_item_ids)->delete();

        $complete_reorder = true;
        foreach ($order->items as $package_item){
            $item = $package_item->item;
            if(!$item){
                $complete_reorder = false;
                continue;
            }
            if($item->quantity < 1 || $item->quantity < $package_item->quantity){
                $complete_reorder = false;
                continue;
            }

            /**
             * save the price at the time being
             */
            $price = $item->price;
            $discounted_price = null;
            if($item->discounted_price != null){
                $discounted_price = $item->discounted_price;
            }


            $new_package_item = PackageItem::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'quantity' => $package_item->quantity,
                'price' => $price,
                'discounted_price' => $discounted_price,
                'restaurant_id' => $item->category_id,
                'category_id' => $item->restaurant ? $item->restaurant->category_id : null,
                'sub_category_id' => $item->restaurant ? $item->restaurant->sub_category_id : null,
                'supplier_food_group_id' => $item->supplier_food_group ? $item->supplier_food_group_id : null,
            ]);
            $customizations_price = 0;
            foreach ($package_item->options as $package_item_option){
                if(! $option = CustomizationOption::whereIsPublished(1)->whereId($package_item_option->customization_option_id)->first()){
                    continue;
                }
                $updated_price = $option->price;
                PackageItemOption::create([
                    'quantity' => $package_item_option->quantity,
                    'package_item_id' => $new_package_item->id,
                    'price' => $updated_price,
                    'customization_option_id' => $package_item_option->customization_option_id
                ]);
                /**
                 * for now quantity will be always 1 bcz in design there is no quantity in customization
                 */
                $customizations_price += $updated_price * $package_item_option->quantity;
            }

            $new_package_item->customizations_price = $customizations_price;
            $new_package_item->save();

        }
        $currency = env('APP_CURRENCY');
        $cart = $user->cart()->get()->map(function ($value, $key) use ($user,$currency) {
            $value->final_price_formatted = number_format($value->final_price,0) . ' ' . $currency;
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,0) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,0) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? '-'.number_format($value->discount_amount,0) . ' ' . $currency : null;

            return $value;
        });
        $message = $complete_reorder ? 'All items re-added successfully to your cart' : 'Some of your items are not valid anymore';
        return response()->json(['status' => $message,'cart' => $cart ]);
    }

    public function order_cart_items(Request $request){
        $user = $this->validate_user($request);
        if(!$order = Order::whereType('regular')->whereUserId($user->id)->whereId($request->order_id)->first()){
            return parent::return_error("Order does not exist", 405, 'messages.item_not_found');
        }
        /**
         * they need items for confirmation screen
         * and in book api items will returned as empty in case of online because order_id still null
         */
        $currency = env('APP_CURRENCY');
        $items =  $order->pre_items()
            ->get()->map(function($value, $key) use ($currency) {
                $value->final_price_formatted = number_format($value->final_price,0) . ' ' . $currency;
                $value->currency = $currency;
                $value->price_formatted = number_format($value->price,0) . ' ' . $currency;
                $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,0) . ' ' . $currency : null;
                $value->discount_amount_formatted = $value->discount_amount ? '-'.number_format($value->discount_amount,0) . ' ' . $currency : null;
                return $value;
            })->values();
        return response()->json($items);
    }
}
