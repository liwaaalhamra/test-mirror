<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResetRequest;
use App\Http\Requests\RestaurantRequest;
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
 * Class RestaurantsController
 */
class RestaurantsController extends Controller
{
    var $section_name = 'restaurants';
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
        $items = Restaurant::where(function($query) use ($request){
            if ($request->has('item_id')){
                $item_id = $request->get('item_id');
                $query->whereId($item_id);
            }
            if ($request->has('category_id')){
                $category_id = $request->get('category_id');
                $query->whereCategoryId($category_id);
            }
            if ($request->has('collection_id')){
                $collection_id = $request->get('collection_id');
                $query->whereSubCategoryId($collection_id);
            }

            if ($request->has('tag_ids')){
                $tag_ids = $request->get('tag_ids');
                $query->Where(function ($query) use($tag_ids) {
                    foreach($tag_ids as $id){
                        $query->orwhere('tag_ids', 'like',  '%' . $id .'%');
                    }
                });
            }
            if ($request->has('craving_ids')){
                $craving_ids = $request->get('craving_ids');
                $query->Where(function ($query) use($craving_ids) {
                    foreach($craving_ids as $id){
                        $query->orwhere('craving_ids', 'like',  '%' . $id .'%');
                    }
                });
            }

            if ($request->has('cuisine_ids')){
                $cuisine_ids = $request->get('cuisine_ids');
                $query->Where(function ($query) use($cuisine_ids) {
                    foreach($cuisine_ids as $id){
                        $query->orwhere('cuisine_ids', 'like',  '%' . $id .'%');
                    }
                });
            }
            if(!empty($request->min) && !empty($request->max)){
                $from = Carbon::createFromTimestamp(strtotime($request->min))->format('Y-m-d');
                $to = Carbon::createFromTimestamp(strtotime($request->max))->format('Y-m-d');
                $query->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to);
            }
        })->orderBy('item_order','desc')->get();

        if ($request->has('service_ids')){
            $service_ids = $request->get('service_ids');
            $items = $items->filter(function ($item,$item_key) use ($service_ids){
                $restaurant_service_ids = $item->services()->pluck('restaurant_services.service_id')->toarray();
                return !array_diff($service_ids, $restaurant_service_ids);
            });
        }


        $selected_category = 'all';
        if ($request->has('category_id')){
            $selected_category = $request->get('category_id');
        }

        $selected_collection = 'all';
        if ($request->has('collection_id')){
            $selected_collection = $request->get('collection_id');
        }

        $selected_services = [];
        if ($request->has('service_ids')){
            $selected_services = $request->get('service_ids');
        }
        $selected_cuisines = [];
        if ($request->has('cuisine_ids')){
            $selected_cuisines = $request->get('cuisine_ids');
        }
        $selected_cravings = [];
        if ($request->has('craving_ids')){
            $selected_cravings = $request->get('craving_ids');
        }
        $selected_tags = [];
        if ($request->has('tag_ids')){
            $selected_tags = $request->get('tag_ids');
        }

        $from = null;
        $to = null;
        if(!empty($request->min) && !empty($request->max)){
            $from = strtotime($request->min);
            $to = strtotime($request->max);
        }

        $categories = CollectionCategory::orderBy('item_order','asc')->get();
        $collections = CuratedCollection::orderBy('item_order','asc')->get();
        $services = Service::orderBy('item_order','asc')->get();
        $tags = Tag::orderBy('item_order','asc')->get();
        $cuisines = Cuisine::orderBy('item_order','asc')->get();
        $cravings = Craving::orderBy('item_order','asc')->get();

        return view('pages.'.$this->section_name.'.index', compact('items','selected_brand','selected_category',
            'selected_product_type','from','to','categories','collections','services','tags','cuisines','cravings',
            'selected_collection','selected_cravings','selected_cuisines','selected_services','selected_tags'))
            ->with('section_name', $this->section_name)
            ->with('content_header', $this->display_name);
    }

    public function reset($id)
    {
        $item = Restaurant::findOrFail($id);

        return view('pages.'.$this->section_name.'.reset', compact('item'))
            ->with('content_header', 'Reset Password');
    }

    public function collections($category_id){
        $collections = CuratedCollection::whereCategoryId($category_id)->pluck('name_en','id')->tojson();
        return $collections;
    }

    public function reset_password(ResetRequest $request)
    {
        $user = Restaurant::findOrFail($request->user_id);
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
        $categories = CollectionCategory::orderBy('item_order','asc')->get();
        $services = Service::orderBy('item_order','asc')->get();
        $tags = Tag::orderBy('item_order','asc')->get();
        $cuisines = Cuisine::orderBy('item_order','asc')->get();
        $cravings = Craving::orderBy('item_order','asc')->get();
        $countries = Country::where('visibility','=',1)->get();
        return view('pages.'.$this->section_name.'.create',compact('services','categories','tags','cuisines','cravings','countries'))
            ->with('content_header', 'Add '.$this->display_name);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RestaurantRequest $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . $this->section_name;
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
        $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents' . DIRECTORY_SEPARATOR . $this->section_name;
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

        $max_order = DB::table($this->section_name)->max('item_order');
        $order = $max_order + 1;
        $item = Restaurant::create([
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
            'is_partner' => $is_partner,
            'has_delivery' => $has_delivery,
            'is_closed' => $is_closed,
            'has_reserve_table' => $has_reserve_table,
            'item_order' => $order,
            'cost' => !empty($request->cost) ? $request->cost : null,
            'table_persons' => !empty($request->table_persons) ? $request->table_persons : null,
            'cuisine_ids' => !empty($request->cuisine_ids) ? $request->cuisine_ids : null,
            'tag_ids' => !empty($request->tag_ids) ? $request->tag_ids : null,
            'craving_ids' => !empty($request->craving_ids) ? $request->craving_ids : null,
            'category_id' => !empty($request->category_id) ? $request->category_id : null,
            'sub_category_id' => !empty($request->sub_category_id) ? $request->sub_category_id : null,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        foreach ($request->service_ids as $service_id){
            RestaurantService::create([
                'item_id' => $item->id,
                'service_id' => $service_id
            ]);
        }


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
                'meta_name' => $this->section_name,
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $meta_name
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function edit($item_id)
    {
        $item = Restaurant::findOrFail($item_id);
        $categories = CollectionCategory::orderBy('item_order','asc')->get();
        $sub_categories = CuratedCollection::whereCategoryId($item->category_id)->orderBy('item_order','asc')->get();
        $services = Service::orderBy('item_order','asc')->get();
        $tags = Tag::orderBy('item_order','asc')->get();
        $cuisines = Cuisine::orderBy('item_order','asc')->get();
        $cravings = Craving::orderBy('item_order','asc')->get();
        $countries = Country::where('visibility','=',1)->get();
        $restaurant_service_ids = $item->services()->pluck('restaurant_services.service_id')->toarray();
        return view('pages.'.$this->section_name.'.edit', compact('item','services','categories','tags','cuisines','cravings','countries','sub_categories','restaurant_service_ids'))
            ->with('content_header', 'Update '.$this->display_name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RestaurantRequest $request
     * @param $item_id
     * @return \Illuminate\Http\Response
     */
    public function update(RestaurantRequest $request,$item_id)
    {
        $item = Restaurant::findOrFail($item_id);
        if(Restaurant::whereUsername($request->username)->where('id','<>',$item_id)->first()){
            return response()->json(['username' => 'Username already exists'])->setStatusCode(400);
        }
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name;
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

        $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . $this->section_name;
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
                'meta_name' => $this->section_name,
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
            'is_partner' => $is_partner,
            'has_delivery' => $has_delivery,
            'is_closed' => $is_closed,
            'has_reserve_table' => $has_reserve_table,
            'item_order' => empty(trim($request->item_order)) ? null : trim($request->item_order),
            'cost' => !empty($request->cost) ? $request->cost : null,
            'table_persons' => !empty($request->table_persons) ? $request->table_persons : null,
            'cuisine_ids' => !empty($request->cuisine_ids) ? $request->cuisine_ids : null,
            'tag_ids' => !empty($request->tag_ids) ? $request->tag_ids : null,
            'craving_ids' => !empty($request->craving_ids) ? $request->craving_ids : null,
            'category_id' => !empty($request->category_id) ? $request->category_id : null,
            'sub_category_id' => !empty($request->sub_category_id) ? $request->sub_category_id : null,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        RestaurantService::whereItemId($item->id)->delete();
        foreach ($request->service_ids as $service_id){
            RestaurantService::create([
                'item_id' => $item->id,
                'service_id' => $service_id
            ]);
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully updated the '.$this->display_name);
    }

    public function get_uploaded_images(Request $request){
        $input = $request->all();
        $item_id = $input['item_id'];
        $item = Restaurant::findOrFail($item_id);
        $files = $item->images;
        $result = [];
        foreach ($files as $file){
            $obj['name'] = $file->getMediaName();
            $file_path = public_path('uploads/images/'.$this->section_name.'/'.$file->getMediaName());
            if(file_exists($file_path)) {
                $obj['size'] = filesize($file_path);
                $result[] = $obj;
            }else{
                Media::whereMetaName($this->section_name)->whereMetaId($item_id)->whereMediaType('images')->whereMediaName($file->getMediaName())->delete();
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
        $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $file_name;
        if(file_exists($filePath)) {
            File::delete($filePath);
        }
        Media::whereMetaName($this->section_name)->whereMetaId($item_id)->whereMediaName($file_name)->delete();
    }

    public function delete_single_image($item_id,$type)
    {
        $item = Restaurant::findOrFail($item_id);
        if($type == 'image'){
            if($item->getMediaName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
                File::delete($filePath);
            }
            $item->image = null;
            $item->width = null;
            $item->height = null;
        }else{
            if($item->getPdfName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getPdfName();
                File::delete($filePath);
            }
            $item->pdf = null;
        }

        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the file');
    }

    public function duplicate($item_id){
        $item = Restaurant::findOrFail($item_id);
        $name = 'Copy';
        if($item){
            $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . $this->section_name;
            $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name;
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
                    'meta_name' => $this->section_name,
                    'media_name' => $new_image_name,
                    'media_type' => 'images',
                    'width' => $image->width,
                    'height' => $image->height,
                ]);
                copy($destinationDir.'/'.$image->getMediaName(), $destinationDir.'/'.$new_image_name);
            }
            foreach (RestaurantEventType::whereItemId($item->id)->get() as $event_type){
                $cloned_event_type = $event_type->replicate();
                $cloned_event_type->item_id = $cloned_item->id;
                $cloned_event_type->save();
            }
            foreach (RestaurantTiming::whereItemId($item->id)->whereType('delivery')->get() as $opening_hour){
                $cloned_opening_hour = $opening_hour->replicate();
                $cloned_opening_hour->item_id = $cloned_item->id;
                $cloned_opening_hour->save();
            }
            foreach (RestaurantTiming::whereItemId($item->id)->whereType('opening')->get() as $delivery_hour){
                $cloned_delivery_hour = $delivery_hour->replicate();
                $cloned_delivery_hour->item_id = $cloned_item->id;
                $cloned_delivery_hour->save();
            }
            foreach (SubCategory::whereCategoryId($item->id)->whereCategoryType('restaurant')->get() as $menu_category){
                $cloned_menu_category = $menu_category->replicate();
                $cloned_menu_category->category_id = $cloned_item->id;
                $cloned_menu_category->save();
                foreach (Item::whereSubCategoryId($menu_category->id)->get() as $menu_item){
                    $cloned_menu_item = $menu_item->replicate();
                    $cloned_menu_item->category_id = $cloned_item->id;
                    $cloned_menu_item->sub_category_id = $cloned_menu_category->id;
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
            foreach (RestaurantService::whereItemId($item->id)->get() as $service){
                $cloned_service = $service->replicate();
                $cloned_service->item_id = $cloned_item->id;
                $cloned_service->save();
            }

        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', $this->display_name.' Successfully cloned as '.$name);
    }

    public function publish($item_id){
        $item = Restaurant::findOrFail($item_id);
        if($item){
            $item->is_available = !$item->is_available;
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', $this->display_name.' Successfully updated');
    }

    public function destroy($item_id)
    {
        $item = Restaurant::findOrFail($item_id);
        if($item->item_order != null){
            $all_items = Restaurant::where('item_order','>',$item->item_order)->get();
            foreach ($all_items as $single_item){
                $single_item->item_order = $single_item->item_order - 1;
                $single_item->save();
            }
        }
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        if($item->getPdfName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getPdfName();
            File::delete($filePath);
        }
        $images = Media::whereMetaId($item_id)->whereMetaName($this->section_name)->get();
        foreach ($images as $media){
            if($media->getMediaName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $media->getMediaName();
                File::delete($filePath);
            }
            $media->delete();
        }

        $item->delete();
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully deleted the '.$this->display_name);

    }
}

