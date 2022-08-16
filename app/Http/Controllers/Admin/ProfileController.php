<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\Country;
use App\Models\Api\Setting;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Webpatser\Countries\Countries;

/**
 * @author Lara Jannoun <lara.jannoun@tedmob.com>
 * Class ProfileController
 */
class ProfileController extends Controller
{
    /**
     * Show the settings page.
     *
     * @return View
     */
    public function index() /*: View*/
    {
        $settings = Setting::pluck('meta_value','meta_name')->toArray();
        foreach (auth()->user()->access_sections as $section){
            $valid_sections[] = $section->section;
        }
        $valid_sections = implode(',',$valid_sections);
        return view('pages.profile.index',compact('settings','valid_sections'))
            ->with('section_name', 'profile')
            ->with('content_header', 'Profile');
    }

    public function export_items(){

        $items = DB::select("select items.code,items.price,items.discounted_price,items.name_en,items.name_ar,items.quantity,items.unit_type,items.unit_value,items.description_en,items.description_ar,
                      items.category_id,items.brand_id,items.product_type_id,items.is_available,items.expiry_date
                                                from items where items.deleted_at is null");
        $excelArray = [];
        $excelArray[] = ['SKU','Price','Discounted Price','Name En','Name Ar','Quantity','Unit Type','Unit Value','Description En','Description Ar','Category ID','Brand ID','Product Type ID','Is Available','Expiry Date'];
        $items = json_decode(json_encode($items), true);
        foreach ($items as $item){
            $item['is_available'] = $item['is_available'] ? 'yes' : 'no';
            $excelArray[] = $item;
        }

        $brands = DB::select("select id,name_en from brands");
        $brandsArray = [];
        $brandsArray[] = ['ID','Name'];
        $brands = json_decode(json_encode($brands), true);
        foreach ($brands as $brand){
            $brandsArray[] = $brand;
        }

        $product_types = DB::select("select id,name_en from product_types");
        $productTypesArray = [];
        $productTypesArray[] = ['ID','Name'];
        $product_types = json_decode(json_encode($product_types), true);
        foreach ($product_types as $product_type){
            $productTypesArray[] = $product_type;
        }

        $categories = DB::select("select id,name_en from categories");
        $categoriesArray = [];
        $categoriesArray[] = ['ID','Name'];
        $categories = json_decode(json_encode($categories), true);
        foreach ($categories as $category){
            $categoriesArray[] = $category;
        }

        // Generate and return the spreadsheet
        Excel::create('items', function($excel) use ($excelArray,$brandsArray,$productTypesArray,$categoriesArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('PharmaC Items');
            $excel->setCreator('Hassan Itani')->setCompany('TEDMOB');
            $excel->setDescription('PharmaC Items');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('items', function($sheet) use ($excelArray) {
                $sheet->fromArray($excelArray, null, 'A1', false, false);
            });

            $excel->sheet('categories', function($sheet) use ($categoriesArray) {
                $sheet->fromArray($categoriesArray, null, 'A1', false, false);
            });

            $excel->sheet('brands', function($sheet) use ($brandsArray) {
                $sheet->fromArray($brandsArray, null, 'A1', false, false);
            });

            $excel->sheet('product_types', function($sheet) use ($productTypesArray) {
                $sheet->fromArray($productTypesArray, null, 'A1', false, false);
            });

        })->download('xls');
    }

    /**
     * Change the auth user password.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                'confirmed'
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error_msg', 'Password must be at least 8 characters,must contain at least one lowercase letter,one uppercase letter,one digit,a special character');
        }

        auth()->user()->update(['password'=>request()->password , 'password_clear_text' => request()->password]);

        return redirect()->back()->with('success', 'Successfully updated the password');
    }

}
