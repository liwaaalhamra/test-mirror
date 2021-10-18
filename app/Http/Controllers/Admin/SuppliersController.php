<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResetRequest;
use App\Http\Requests\SupplierRequest;
use App\Models\Api\Brand;
use App\Models\Api\Category;
use App\Models\Api\CollectionCategory;
use App\Models\Api\Country;
use App\Models\Api\Craving;
use App\Models\Api\Cuisine;
use App\Models\Api\CuratedCollection;
use App\Models\Api\Item;
use App\Models\Api\Media;
use App\Models\Api\PackageItem;
use App\Models\Api\ProductType;
use App\Models\Api\Restaurant;
use App\Models\Api\RestaurantEventType;
use App\Models\Api\RestaurantService;
use App\Models\Api\RestaurantTiming;
use App\Models\Api\Service;
use App\Models\Api\SubCategory;
use App\Models\Api\SupplierFoodGroup;
use App\Models\Api\SupplierSubCategory;
use App\Models\Api\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Propaganistas\LaravelPhone\PhoneNumber;


/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class SuppliersController
 */
class SuppliersController extends Controller
{
    var $section_name = 'suppliers';
    var $display_name;
    public function __construct()
    {
        parent::__construct();
        $this->display_name = str_replace('_',' ',ucwords($this->section_name,'_'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = SupplierFoodGroup::where(function($query) use ($request){
            if ($request->has('item_id')){
                $item_id = $request->get('item_id');
                $query->whereId($item_id);
            }
            if ($request->has('category_id')){
                $category_id = $request->get('category_id');
                $query->whereCategoryId($category_id);
            }

            if(!empty($request->min) && !empty($request->max)){
                $from = Carbon::createFromTimestamp(strtotime($request->min))->format('Y-m-d');
                $to = Carbon::createFromTimestamp(strtotime($request->max))->format('Y-m-d');
                $query->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to);
            }
        })->orderBy('item_order','desc')->get();



        $selected_category = 'all';
        if ($request->has('category_id')){
            $selected_category = $request->get('category_id');
        }

        $from = null;
        $to = null;
        if(!empty($request->min) && !empty($request->max)){
            $from = strtotime($request->min);
            $to = strtotime($request->max);
        }

        $categories = SupplierSubCategory::orderBy('item_order','asc')->get();

        return view('pages.'.$this->section_name.'.index', compact('items','selected_category','from','to','categories'))
            ->with('section_name', $this->section_name)
            ->with('content_header', $this->display_name);
    }

    public function reset($id)
    {
        $item = SupplierFoodGroup::findOrFail($id);

        return view('pages.'.$this->section_name.'.reset', compact('item'))
            ->with('content_header', 'Reset Password');
    }

    public function reset_password(ResetRequest $request)
    {
        $user = SupplierFoodGroup::findOrFail($request->user_id);
        $user->password = hash('sha256', $request->password);
        $user->save();
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Password has been reset successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = SupplierSubCategory::orderBy('item_order','asc')->get();
        $countries = Country::where('visibility','=',1)->get();
        return view('pages.'.$this->section_name.'.create',compact('categories','countries'))
            ->with('content_header', 'Add '.$this->display_name);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SupplierRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'food_groups';
        if(SupplierFoodGroup::whereUsername($request->username)->first()){
            return response()->json(['username' => 'Username already exists'])->setStatusCode(400);
        }
        $image = null;
        $width = null;
        $height = null;
        $files = $request->file;
        if(empty($files[0])){
            $files = [];
        }

        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return response()->json(['document' => 'Document Corrupted'])->setStatusCode(400);
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return response()->json(['document' => 'Invalid Media Type'])->setStatusCode(400);
                }
                $image = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $image);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$image);
            }
        }
        $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents' . DIRECTORY_SEPARATOR . 'food_groups';
        $pdf = null;
        if($request->hasFile('pdf')) {
            if($request->file('pdf')->isValid()) {
                $file1 = $request->file('pdf');
                $validator = Validator::make($request->all(), [
                    'pdf' => 'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/pdf',
                ]);
                if($validator->fails()){
                    return response()->json(['document' => 'Invalid Media Type'])->setStatusCode(400);
                }
                $pdf = str_slug(str_random(20)) . '.' . $file1->getClientOriginalExtension();
                $file1->move($destinationDir1, $pdf);
            }
        }
        $has_reserve_table = 0;
        if(isset($request->has_reserve_table) && $request->has_reserve_table == 'on'){
            $has_reserve_table = 1;
        }
        $is_closed = 0;
        if(isset($request->is_closed) && $request->is_closed == 'on'){
            $is_closed = 1;
        }
        $has_delivery = 0;
        if(isset($request->has_delivery) && $request->has_delivery == 'on'){
            $has_delivery = 1;
        }
        $is_partner = 0;
        if(isset($request->is_partner) && $request->is_partner == 'on'){
            $is_partner = 1;
        }

        $new_country_code = null;
        $new_phone_number = null;
        $country_id = 368;
        $phone_number = $request->phone_number;
        $country_code = $request->country_code;
        $request->merge([ 'mobile_number' => $country_code.$phone_number]);

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'phone:IQ,LB',
        ]);

        if ($validator->fails()) {
            return response()->json(['phone' => $validator->errors()->first('mobile_number')])->setStatusCode(400);
        }
        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
        $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
        $country_id = $country ? $country->id : $country_id;
        $new_country_code = '+'.$phone_details->getCountryCode();
        $new_phone_number = $phone_details->getNationalNumber();

        $max_order = DB::table('supplier_food_groups')->max('item_order');
        $order = $max_order + 1;
        $item = SupplierFoodGroup::create([
            'latitude' => isset($request->latitude) ? $request->get('latitude') : null,
            'longitude' => isset($request->longitude) ? $request->get('longitude') : null,
            'address' => isset($request->address) ? $request->get('address') : null,
            'share_link' => isset($request->share_link) ? $request->get('share_link') : null,
            'username' => trim($request->get('username')),
            'password' => hash('sha256', $request->password),
            'country_id' => $country_id,
            'country_code' => $new_country_code,
            'phone_number' => $new_phone_number,
            'pdf' => $pdf,
            'image' => $image,
            'width' => $width,
            'height' => $height,
            'is_closed' => $is_closed,
            'item_order' => $order,
            'category_id' => !empty($request->category_id) ? $request->category_id : null,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        foreach ($files as $single_file){
            try{
                $data = file_get_contents($single_file->getPathname());
                $base64 = base64_encode($data);
                $bin = base64_decode($base64);
                $im = imageCreateFromString($bin);
            }catch (\Exception $ex){
                continue;
            }
            $image = str_slug(str_random(20)) . '.' . $single_file->getClientOriginalExtension();
            $single_file->move($destinationDir, $image);
            list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$image);
            Media::create([
                'meta_id' => $item->id,
                'meta_name' => 'food_groups',
                'media_name' => $image,
                'media_type' => 'images',
                'width' => $width,
                'height' => $height,
            ]);
        }

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully created the '.$this->display_name);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    public function duplicate($item_id){
        $item = SupplierFoodGroup::findOrFail($item_id);
        $name = 'Copy';
        if($item){
            $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'food_groups';
            $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'food_groups';
            $destinationDirItem = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'items';
            $name = $item->name_en.' Copy';
            $cloned_item = $item->replicate();
            $cloned_item->name_en = $name;
            $now = Carbon::now()->timestamp;
            if($item->getMediaName() != null) {
                $new_fileName = $now.'_'.$item->getMediaName();
                copy($destinationDir.'/'.$item->getMediaName(), $destinationDir.'/'.$new_fileName);
                $cloned_item->image = $new_fileName;
                $cloned_item->width = $item->width;
                $cloned_item->height = $item->height;
            }
            if($item->getPdfName() != null) {
                $new_pdffileName = $now.'_'.$item->getPdfName();
                copy($destinationDir1.'/'.$item->getPdfName(), $destinationDir1.'/'.$new_pdffileName);
                $cloned_item->pdf = $new_pdffileName;
            }
            $cloned_item->save();
            foreach ($item->images as $image){
                $new_image_name = $now.'_'.$image->getMediaName();
                Media::create([
                    'meta_id' => $cloned_item->id,
                    'meta_name' => 'food_groups',
                    'media_name' => $new_image_name,
                    'media_type' => 'images',
                    'width' => $image->width,
                    'height' => $image->height,
                ]);
                copy($destinationDir.'/'.$image->getMediaName(), $destinationDir.'/'.$new_image_name);
            }
//            foreach (RestaurantEventType::whereItemId($item->id)->get() as $event_type){
//                $cloned_event_type = $event_type->replicate();
//                $cloned_event_type->item_id = $cloned_item->id;
//                $cloned_event_type->save();
//            }
//            foreach (RestaurantTiming::whereItemId($item->id)->whereType('delivery')->get() as $opening_hour){
//                $cloned_opening_hour = $opening_hour->replicate();
//                $cloned_opening_hour->item_id = $cloned_item->id;
//                $cloned_opening_hour->save();
//            }
//            foreach (RestaurantTiming::whereItemId($item->id)->whereType('opening')->get() as $delivery_hour){
//                $cloned_delivery_hour = $delivery_hour->replicate();
//                $cloned_delivery_hour->item_id = $cloned_item->id;
//                $cloned_delivery_hour->save();
//            }
            foreach (SubCategory::whereCategoryId($item->id)->whereCategoryType('supplier')->get() as $menu_category){
                $cloned_menu_category = $menu_category->replicate();
                $cloned_menu_category->category_id = $cloned_item->id;
                $cloned_menu_category->save();
            }
//            foreach (RestaurantService::whereItemId($item->id)->get() as $service){
//                $cloned_service = $service->replicate();
//                $cloned_service->item_id = $cloned_item->id;
//                $cloned_service->save();
//            }
            foreach (Item::whereSupplierFoodGroupId($item->id)->get() as $menu_item){
                $cloned_menu_item = $menu_item->replicate();
                $cloned_menu_item->supplier_food_group_id = $cloned_item->id;
                if($menu_item->getMediaName() != null) {
                    $new_fileNameItem = $now.'_'.$menu_item->getMediaName();
                    copy($destinationDirItem.'/'.$menu_item->getMediaName(), $destinationDirItem.'/'.$new_fileNameItem);
                    $cloned_menu_item->image = $new_fileNameItem;
                    $cloned_menu_item->width = $menu_item->width;
                    $cloned_menu_item->height = $menu_item->height;
                }
                $cloned_menu_item->save();
            }
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', $this->display_name.' Successfully cloned as '.$name);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $meta_name
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function edit($item_id)
    {
        $item = SupplierFoodGroup::findOrFail($item_id);
        $categories = SupplierSubCategory::orderBy('item_order','asc')->get();
        $countries = Country::where('visibility','=',1)->get();
        return view('pages.'.$this->section_name.'.edit', compact('item','categories','countries'))
            ->with('content_header', 'Update '.$this->display_name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SupplierRequest $request
     * @param $item_id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request,$item_id)
    {
        $item = SupplierFoodGroup::findOrFail($item_id);
        if(SupplierFoodGroup::whereUsername($request->username)->where('id','<>',$item_id)->first()){
            return response()->json(['username' => 'Username already exists'])->setStatusCode(400);
        }
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'food_groups';
        $files = $request->file;
        if(empty($files[0])){
            $files = [];
        }
        $fileName = null;
        $width = null;
        $height = null;
        if($item->getMediaName() != null) {
            $fileName = $item->getMediaName();
            $width = $item->width;
            $height = $item->height;
        }
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return response()->json(['document' => 'Document Corrupted'])->setStatusCode(400);
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return response()->json(['document' => 'Invalid Media Type'])->setStatusCode(400);
                }
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
                if($item->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }

        $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'food_groups';
        $pdf = null;
        if($item->getPdfName() != null) {
            $pdf = $item->getPdfName();
        }
        if($request->hasFile('pdf')) {
            if($request->file('pdf')->isValid()) {
                $file1 = $request->file('pdf');
                $validator = Validator::make($request->all(), [
                    'pdf' => 'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/pdf',
                ]);
                if($validator->fails()){
                    return response()->json(['document' => 'Invalid Media Type'])->setStatusCode(400);
                }
                $pdf = str_slug(str_random(20)) . '.' . $file1->getClientOriginalExtension();
                $file1->move($destinationDir1, $pdf);
                if($item->getPdfName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getPdfName();
                    File::delete($filePath);
                }
            }
        }

        foreach ($files as $file){
            try{
                $data = file_get_contents($file->getPathname());
                $base64 = base64_encode($data);
                $bin = base64_decode($base64);
                $im = imageCreateFromString($bin);
            }catch (\Exception $ex){
                continue;
            }
            $image = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationDir, $image);
            list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$image);
            Media::create([
                'meta_id' => $item->id,
                'meta_name' => 'food_groups',
                'media_name' => $image,
                'media_type' => 'images',
                'width' => $width,
                'height' => $height,
            ]);
        }

        $has_reserve_table = 0;
        if(isset($request->has_reserve_table) && $request->has_reserve_table == 'on'){
            $has_reserve_table = 1;
        }
        $is_closed = 0;
        if(isset($request->is_closed) && $request->is_closed == 'on'){
            $is_closed = 1;
        }
        $has_delivery = 0;
        if(isset($request->has_delivery) && $request->has_delivery == 'on'){
            $has_delivery = 1;
        }
        $is_partner = 0;
        if(isset($request->is_partner) && $request->is_partner == 'on'){
            $is_partner = 1;
        }

        $new_country_code = null;
        $new_phone_number = null;
        $country_id = 368;
        $phone_number = $request->phone_number;
        $country_code = $request->country_code;
        $request->merge([ 'mobile_number' => $country_code.$phone_number]);

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'phone:IQ,LB',
        ]);

        if ($validator->fails()) {
            return response()->json(['phone' => $validator->errors()->first('mobile_number')])->setStatusCode(400);
        }
        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
        $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
        $country_id = $country ? $country->id : $country_id;
        $new_country_code = '+'.$phone_details->getCountryCode();
        $new_phone_number = $phone_details->getNationalNumber();

        $item->update([
            'latitude' => isset($request->latitude) ? $request->get('latitude') : null,
            'longitude' => isset($request->longitude) ? $request->get('longitude') : null,
            'address' => isset($request->address) ? $request->get('address') : null,
            'share_link' => isset($request->share_link) ? $request->get('share_link') : null,
            'username' => trim($request->get('username')),
            'country_id' => $country_id,
            'country_code' => $new_country_code,
            'phone_number' => $new_phone_number,
            'pdf' => $pdf,
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'is_closed' => $is_closed,
            'item_order' => empty(trim($request->item_order)) ? null : trim($request->item_order),
            'category_id' => !empty($request->category_id) ? $request->category_id : null,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully updated the '.$this->display_name);
    }

    public function get_uploaded_images(Request $request){
        $input = $request->all();
        $item_id = $input['item_id'];
        $item = SupplierFoodGroup::findOrFail($item_id);
        $files = $item->images;
        $result = [];
        foreach ($files as $file){
            $obj['name'] = $file->getMediaName();
            $file_path = public_path('uploads/images/food_groups/'.$file->getMediaName());
            if(file_exists($file_path)) {
                $obj['size'] = filesize($file_path);
                $result[] = $obj;
            }else{
                Media::whereMetaName('food_groups')->whereMetaId($item_id)->whereMediaType('images')->whereMediaName($file->getMediaName())->delete();
            }
            
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function delete_images(Request $request){

        $input = $request->all();
        $item_id = $input['id'];
        $file_name = $input['file_name'];
        $item = Restaurant::whereId($item_id)->first();
        $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'food_groups' . DIRECTORY_SEPARATOR . $file_name;
        if(file_exists($filePath)) {
            File::delete($filePath);
        }
        Media::whereMetaName('food_groups')->whereMetaId($item_id)->whereMediaName($file_name)->delete();
    }

    public function delete_single_image($item_id,$type)
    {
        $item = SupplierFoodGroup::findOrFail($item_id);
        if($type == 'image'){
            if($item->getMediaName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'food_groups' . DIRECTORY_SEPARATOR . $item->getMediaName();
                File::delete($filePath);
            }
            $item->image = null;
            $item->width = null;
            $item->height = null;
        }else{
            if($item->getPdfName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'food_groups' . DIRECTORY_SEPARATOR . $item->getPdfName();
                File::delete($filePath);
            }
            $item->pdf = null;
        }

        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the file');
    }

    public function publish($item_id){
        $item = SupplierFoodGroup::findOrFail($item_id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', $this->display_name.' Successfully updated');
    }

    public function destroy($item_id)
    {
        $item = SupplierFoodGroup::findOrFail($item_id);
        if($item->item_order != null){
            $all_items = SupplierFoodGroup::where('item_order','>',$item->item_order)->get();
            foreach ($all_items as $single_item){
                $single_item->item_order = $single_item->item_order - 1;
                $single_item->save();
            }
        }
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'food_groups' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        if($item->getPdfName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'food_groups' . DIRECTORY_SEPARATOR . $item->getPdfName();
            File::delete($filePath);
        }
        $images = Media::whereMetaId($item_id)->whereMetaName('food_groups')->get();
        foreach ($images as $media){
            if($media->getMediaName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'food_groups' . DIRECTORY_SEPARATOR . $media->getMediaName();
                File::delete($filePath);
            }
            $media->delete();
        }

        $item->delete();
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully deleted the '.$this->display_name);

    }
}

