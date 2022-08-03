<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\CustomFieldList;
use App\Models\Api\Restaurant;
use App\Models\Api\SyndicatePayment;
use App\Models\Api\UserExpences;
use App\Models\Api\Users;
use App\Models\Api\UsersAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\View\View;
use App\Http\Requests;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use SoapFault;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class SheetsController
 * @package App\Http\Controllers\Admin
 */
class SheetsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }

    public function import_items(Request $request)
    {
        ini_set('max_execution_time', 300);
        if($request->hasFile('excel')) {
            if($request->file('excel')->isValid()) {
                //UploadDate::create(['last_upload_date' => Carbon::now()]);

                $sheets = Excel::load($request->file('excel'));
                $sheets_name = $sheets->getSheetNames();
                $sheets = $sheets->formatDates(true)
                    ->get()->toarray();
                /**
                 * for car models set startRow to 1
                 */
                $sheets = $sheets;
                dd($sheets);

//                foreach ($sheets as $item){
//                    if($item['brand_id'] == null || $item['brand_name'] == null){
//                        continue;
//                    }
//                    CustomFieldList::create([
//                        'field_name' => "car_models",
//                        'parent_id' => intval($item['brand_id']),
//                        'name_en' => isset($item['model_name']) && !empty($item['model_name']) ? trim($item['model_name']) : trim($item['brand_name']),
//                        'name_ar' => isset($item['model_name']) && !empty($item['model_name']) ? trim($item['model_name']) : trim($item['brand_name']),
//                    ]);
//                }
//                CustomFieldList::truncate();
                foreach ($sheets as $index => $sheet){
                    $sheet_name = $sheets_name[$index];

                    foreach ($sheet as $item){
                        CustomFieldList::create([
                            'field_name' => $sheet_name,
                            'name_en' => $item[0],
                            'name_ar' => $item[0],
                        ]);
                    }
                }
                dd('done');
//                DB::table('users')->delete();
                foreach ($items as $item){
                    if($item['sku'] == null || $item['price'] == null || $item['name_en'] == null
                        || $item['name_ar'] == null
                        || $item['quantity'] == null
                        || $item['category_id'] == null
                        || $item['is_available'] == null || $item['expiry_date'] == null){
                        continue;
                    }
                    if($existing_item = Restaurant::where('code','=',strtolower(trim($item['sku'])))->first()){
                        $existing_item->update([
                            'price' => intval($item['price']),
                            'code' => trim($item['sku']),
                            'image' => trim(strtolower($item['sku'])).'.png',
                            'discounted_price' => intval($item['discounted_price']),
                            'name_en' => trim($item['name_en']),
                            'name_ar' => trim($item['name_ar']),
                            'quantity' => intval($item['quantity']),
                            'unit_type' => isset($item['unit_type']) && !empty($item['unit_type']) ? trim($item['unit_type']) : null,
                            'unit_value' => isset($item['unit_value']) && !empty($item['unit_value']) ? trim($item['unit_value']) : null,
                            'description_en' => isset($item['description_en']) && !empty($item['description_en']) ? trim($item['description_en']) : null,
                            'description_ar' => isset($item['description_ar']) && !empty($item['description_ar']) ? trim($item['description_ar']) : null,
                            'category_id' => intval($item['category_id']),
                            'brand_id' => isset($item['brand_id']) && !empty($item['brand_id']) ? intval($item['brand_id']) : null,
                            'product_type_id' => isset($item['product_type_id']) && !empty($item['product_type_id']) ? intval($item['product_type_id']) : null,
                            'expiry_date' => strtotime($item['expiry_date']),
                            'is_available' => strtolower($item['is_available']) == 'yes' ? true : false,
                        ]);
                    }else{
                        $max_order = DB::table('items')->max('item_order');
                        $order = $max_order + 1;
                        Restaurant::create([
                            'item_order' => $order,
                            'code' => trim($item['sku']),
                            'price' => intval($item['price']),
                            'image' => trim(strtolower($item['sku'])).'.png',
                            'discounted_price' => intval($item['discounted_price']),
                            'name_en' => trim($item['name_en']),
                            'name_ar' => trim($item['name_ar']),
                            'quantity' => intval($item['quantity']),
                            'unit_type' => isset($item['unit_type']) && !empty($item['unit_type']) ? trim($item['unit_type']) : null,
                            'unit_value' => isset($item['unit_value']) && !empty($item['unit_value']) ? trim($item['unit_value']) : null,
                            'description_en' => isset($item['description_en']) && !empty($item['description_en']) ? trim($item['description_en']) : null,
                            'description_ar' => isset($item['description_ar']) && !empty($item['description_ar']) ? trim($item['description_ar']) : null,
                            'category_id' => intval($item['category_id']),
                            'brand_id' => isset($item['brand_id']) && !empty($item['brand_id']) ? intval($item['brand_id']) : null,
                            'product_type_id' => isset($item['product_type_id']) && !empty($item['product_type_id']) ? intval($item['product_type_id']) : null,
                            'expiry_date' => strtotime($item['expiry_date']),
                            'is_available' => strtolower($item['is_available']) == 'yes' ? true : false,
                        ]);
                    }
                }
                return redirect()->back()->with('success', 'File Successfully uploaded');
            }else{
                return redirect()->back()->with('error', 'Invalid File!.');
            }
        }else{
            return redirect()->back()->with('error', 'No File exists!.');
        }
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', 300);
        $user_ids = UsersAccess::pluck('user_id')->toArray();
        $users = Users::wherenull('cost_center')->whereNotIn('id', $user_ids)->get();
        foreach ($users as $user){

            $mobile_number = str_replace('-', '', trim($user->primary_phone));
            try {
                $string1 = 'Dear '.$user->first_name.', the BoB Community Loyalty App is now on Android and iOS!';
                $string2 = 'Click now on http://bit.ly/2q9AbUP if you still have not downloaded the app!';
                $string0 = "\n";
                $string3 = 'Username: '.intval($user->employee_id);
                $string4 = 'Password: '.$user->password_clear_text;
                $body = $string1.$string0.$string2.$string0.$string0.$string3.$string0.$string4;
                $result = parent::SendSMS($body, $mobile_number,'BoBStaff');
                $user->cost_center = 'sms';
                $user->save();
            } catch (SoapFault $fault) {
                return parent::return_error('Failed to Send Pin:'.$fault->faultstring,455,'messages.failed_send_pin');
            }
        }
//        if($request->hasFile('excel')) {
//            if($request->file('excel')->isValid()) {
//                //UploadDate::create(['last_upload_date' => Carbon::now()]);
//
//                $sheets = Excel::load($request->file('excel'))
//                    ->formatDates(true)
//                    ->get()->toarray();
////                DB::table('users')->delete();
//                foreach ($sheets as $item){
//                    if($item['emp_id'] == null || $item['name'] == null || $item['mobile_number'] == null){
//                        continue;
//                    }
//                    if($existing_user = Users::where('employee_id','=',intval($item['emp_id']))->first()){
//                        $full_name = explode(' ', $item['name']);
//                        $mobile_number = str_replace('-', '', trim($item['mobile_number']));
//                        try {
//                            $string1 = 'Dear '.$full_name[0].', the BoB Community Loyalty App is now on Android and iOS!';
//                            $string2 = 'Click now on http://bit.ly/2q9AbUP if you still have not downloaded the app!';
//                            $string0 = "\n";
//                            $string3 = 'Username: '.intval($item['emp_id']);
//                            $string4 = 'Password: '.$existing_user->password_clear_text;
//                            $body = $string1.$string0.$string2.$string0.$string0.$string3.$string0.$string4;
//                            $result = parent::SendSMS($body, $mobile_number,'BoBStaff');
//                        } catch (SoapFault $fault) {
//                            return parent::return_error('Failed to Send Pin:'.$fault->faultstring,455,'messages.failed_send_pin');
//                        }
//                    }
//                }
//                return redirect()->back()->with('success', 'File Successfully uploaded');
//            }else{
//                return redirect()->back()->with('error', 'Invalid File!.');
//            }
//        }else{
//            return redirect()->back()->with('error', 'No File exists!.');
//        }
    }

    /**
     * Store all excel sheet.
     *
     * @param Request $request
     * @return View
     */
    public function store1(Request $request)
    {
        ini_set('max_execution_time', 300);
        if($request->hasFile('excel')) {
            if($request->file('excel')->isValid()) {
                //UploadDate::create(['last_upload_date' => Carbon::now()]);

                $sheets = Excel::load($request->file('excel'))
                    ->formatDates(true)
                    ->get()->toarray();

//                DB::table('users')->delete();
                foreach ($sheets as $item){
                    if($item['employee_code'] == null){
                        continue;
                    }

                    if($existing_user = Users::where('employee_id','=',intval($item['employee_code']))->first()){
                        $existing_user->first_name = ($item['first_name'] == null) ? null : trim($item['first_name']);
                        $existing_user->last_name = ($item['last_name'] == null) ? null : trim($item['last_name']);
                        $existing_user->primary_phone = ($item['mobile_number'] == null) ? null : trim($item['mobile_number']);
                        $existing_user->blood_type = ($item['blood_type'] == null) ? null : trim($item['blood_type']);
                        $existing_user->gender = ($item['gender'] == null) ? null : trim($item['gender']);
                        $existing_user->save();
                    }else{
                        $password = str_random(10);
                        $user = Users::create([
                            'photo' => intval($item['employee_code']).'.jpg',
                            'employee_id' => intval($item['employee_code']),
                            'first_name' => $item['first_name'] == null ? null : trim($item['first_name']),
                            'last_name' => $item['last_name'] == null ? null : trim($item['last_name']),
                            'primary_phone' => $item['mobile_number'] == null ? null : trim($item['mobile_number']),
                            'blood_type' => $item['blood_type'] == null ? null : trim($item['blood_type']),
                            'gender' => $item['gender'] == null ? null : trim($item['gender']),
//                            'full_name' => $item['name'] == null ? null : trim($item['name']),
//                            'code' => $item['code'] == null ? null : intval($item['code']),
//                            'dob' => $item['d.o.b'] == null ? null : trim(date("Y-m-d", strtotime($item['d.o.b']))),
//                            'casa' => $item['address_casa'] == null ? null : trim($item['address_casa']),
//                            'city' => $item['address_city'] == null ? null : trim($item['address_city']),
//                            'address_details' => $item['address_details'] == null ? null : trim($item['address_details']),
//                            'mobile_number' => $item['phone_no._1'] == null ? null : trim($item['phone_no._1']),
//                            'mobile_number2' => $item['phone_no._2'] == null ? null : trim($item['phone_no._2']),
//                            'department' => $item['departmentsection'] == null ? null : trim($item['departmentsection']),
                            'password' => $password,
                            'password_clear_text' => $password
                        ]);
                    }
                }
                return redirect()->back()->with('success', 'File Successfully uploaded');
            }else{
                return redirect()->back()->with('error', 'Invalid File!.');
            }
        }else{
            return redirect()->back()->with('error', 'No File exists!.');
        }
    }
}
