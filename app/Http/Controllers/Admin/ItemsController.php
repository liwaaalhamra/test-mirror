<?php

namespace App\Http\Controllers\Admin;


use App\Models\Api\Category;
use App\Models\Api\Item;
use App\Models\Api\Media;
use App\Models\Api\Restaurant;
use App\Models\Api\PackageItem;
use App\Models\Api\SubCategory;
use App\Models\Api\SubSubCategory;
use App\Models\Api\UserPush;
use App\Models\Api\Inbox;
use App\Models\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Mail;


/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class ItemsController
 */
class ItemsController extends Controller
{
    var $section_name = 'items';
    var $display_name;
    public function __construct()
    {
        parent::__construct();
        $this->display_name = 'Post';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Item::where(function($query) use ($request){
            if ($request->has('item_id')){
                $item_id = $request->get('item_id');
                $query->whereId($item_id);
            }
            if ($request->has('category_id')){
                $category_id = $request->get('category_id');
                $query->whereCategoryId($category_id);
            }
            if ($request->has('sub_category_id')){
                $sub_category_id = $request->get('sub_category_id');
                $query->whereSubCategoryId($sub_category_id);
            }
            if(!empty($request->min) && !empty($request->max)){
                $from = Carbon::createFromTimestamp(strtotime($request->min))->format('Y-m-d');
                $to = Carbon::createFromTimestamp(strtotime($request->max))->format('Y-m-d');
                $query->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to);
            }
        })->orderBy('item_order','asc')->get();

        $selected_category = 'all';
        if ($request->has('category_id')){
            $selected_category = $request->get('category_id');
        }

        $selected_sub_category = 'all';
        if ($request->has('sub_category_id')){
            $selected_sub_category = $request->get('sub_category_id');
        }
        $selected_sub_sub_category = 'all';
        if ($request->has('sub_sub_category_id')){
            $selected_sub_sub_category = $request->get('sub_sub_category_id');
        }

        $from = null;
        $to = null;
        if(!empty($request->min) && !empty($request->max)){
            $from = strtotime($request->min);
            $to = strtotime($request->max);
        }

        $categories = Category::get();
        $sub_categories = SubCategory::get();
        $sub_sub_categories = SubSubCategory::get();

        return view('pages.'.$this->section_name.'.index', compact('items','selected_category','from','to',
            'categories','sub_categories','selected_sub_category','sub_sub_categories','selected_sub_sub_category'))
            ->with('section_name', $this->section_name)
            ->with('content_header', $this->display_name);
    }

    /**
     * Reorder Items.
     *
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request){
        $input = $request->all();
        $ids = $input['ids'];
        $ids = explode(',',$ids);
        foreach ($ids as $key => $id){
            $item = Restaurant::whereId($id)->first();
            $item->item_order = $key + 1;
            $item->save();
        }
    }


    public function menu_categories($category_id){
        $menu_categories = SubCategory::whereCategoryType('restaurant')->whereCategoryId($category_id)->pluck('name_en','id')->tojson();
        return $menu_categories;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sub_categories = SubCategory::whereCategoryType('restaurant')->orderBy('item_order','asc')->get();
        $categories = Restaurant::orderBy('item_order','asc')->get();
        return view('pages.'.$this->section_name.'.create',compact('sub_categories','categories'))
            ->with('content_header', 'Add '.$this->display_name);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . $this->section_name;
//        if(Item::whereCode($request->code)->first()){
//            return redirect()->back()
//                ->with('error_msg', 'SKU already exists');
//        }
        $image = null;
        $width = null;
        $height = null;
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return redirect()->back()
                        ->with('error_msg', 'Document Corrupted');
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $image = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $image);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$image);
            }
        }
//        $is_gift = 0;
//        if(isset($request->is_gift) && $request->is_gift == 'on'){
//            $is_gift = 1;
//        }
        $max_order = DB::table($this->section_name)->whereCategoryId($request->category_id)->whereSubCategoryId($request->sub_category_id)->max('item_order');
        $order = $max_order + 1;
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item = Item::create([
            'image' => $image,
            'width' => $width,
            'height' => $height,
//            'is_gift' => $is_gift,
            'item_order' => $order,
            'code' => !empty($request->code) ? $request->code : null,
            'share_link' => !empty($request->share_link) ? $request->share_link : null,
            'category_id' => !empty($request->category_id) ? $request->category_id : null,
            'brand_id' => !empty($request->brand_id) ? $request->brand_id : null,
            'product_type_id' => !empty($request->product_type_id) ? $request->product_type_id : null,
            'sub_category_id' => !empty($request->sub_category_id) ? $request->sub_category_id : null,
            'is_available' => 0,
            'expiry_date' => $item_datetime,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'unit_type' => empty(trim($request->unit_type)) ? null : trim($request->unit_type),
            'unit_value' => empty(trim($request->unit_value)) ? null : trim($request->unit_value),
            'price' => empty($request->price) ? null : $request->price,
            'discounted_price' => isset($request->discounted_price) && $request->discounted_price > 0 ? $request->discounted_price : null,
            'quantity' => empty($request->quantity) ? null : $request->quantity,
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        $info = [];
        $info['to'] = $to = explode(',', env('NEW_ITEMS_EMAILS'));
        $info['icon'] = 'new.png';
        $info['message'] = 'NEW '.$this->display_name;
        $info['item'] = $item;
        $info['cms_user'] = auth()->user();
        $info['item_link'] = secure_url('admin/'.$this->section_name.'?item_id='.$item->id);
        $info['title'] = env('APP_NAME') . ' | NEW '.$this->display_name;
        Mail::send('emails.new_item', ['data' => $info], function ($message) use ($info) {
            $message->subject($info['title']);
            $message->to($info['to']);
        });

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
        //
        $items = Item::whereCategoryId($id)->orderBy('item_order','asc')->get();
        $accessible_sections = $this->sections;
        return view('pages.'.$this->section_name.'.index', compact('items','accessible_sections'))
            ->with('section_name', $this->section_name)
            ->with('content_header', $this->display_name);
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
        $item = Item::findOrFail($item_id);
        $sub_categories = SubCategory::whereCategoryId($item->category_id)->orderBy('item_order','asc')->get();
        $categories = Restaurant::orderBy('item_order','asc')->get();
        return view('pages.'.$this->section_name.'.edit', compact('item','sub_categories','categories'))
            ->with('content_header', 'Update '.$this->display_name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$item_id)
    {
        $item = Item::findOrFail($item_id);
        if(Item::where('id','<>',$item_id)->whereCode($request->code)->first()){
            return redirect()->back()
                ->with('error_msg', 'SKU already exists');
        }
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name;

        $image = null;
        $width = null;
        $height = null;
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return redirect()->back()
                        ->with('error_msg', 'Document Corrupted');
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $image = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $image);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$image);
            }
        }

        /**
         * delete old pdf
         */
        if($image != null && $item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
//        $is_gift = 0;
//        if(isset($request->is_gift) && $request->is_gift == 'on'){
//            $is_gift = 1;
//        }

        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }

        $data = [
//            'is_gift' => $is_gift,
            'item_order' => empty(trim($request->item_order)) ? null : trim($request->item_order),
            'share_link' => empty(trim($request->share_link)) ? null : trim($request->share_link),
            'code' => !empty($request->code) ? $request->code : null,
            'category_id' => !empty($request->category_id) ? $request->category_id : null,
            'sub_category_id' => !empty($request->sub_category_id) ? $request->sub_category_id : null,
            'expiry_date' => $item_datetime,
            'brand_id' => !empty($request->brand_id) ? $request->brand_id : null,
            'product_type_id' => !empty($request->product_type_id) ? $request->product_type_id : null,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'unit_type' => empty(trim($request->unit_type)) ? null : trim($request->unit_type),
            'unit_value' => empty(trim($request->unit_value)) ? null : trim($request->unit_value),
            'price' => empty($request->price) ? null : $request->price,
            'discounted_price' => isset($request->discounted_price) && $request->discounted_price > 0 ? $request->discounted_price : null,
            'quantity' => empty($request->quantity) ? null : $request->quantity,
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ];
        if($image != null){
            $data['image'] = $image;
            $data['width'] = $width;
            $data['height'] = $height;
        }

        $item->update($data);
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully updated the '.$this->display_name);
    }

    public function delete_single_image(Item $item)
    {
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->width = null;
        $item->height = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    public function publish(Item $item){
        if($item){
            $item->is_available = !$item->is_available;
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', $this->display_name.' Successfully updated');
    }

    public function approve(Item $item){
        if($item){
            if($item->status == 'in_review'){
                $title = "Post Approval!";
                $message = "Your post has been approved, you can start bartering!";
            }else{
                $title = "Post Rejection!";
                $message = "Your post has been rejected, please make sure your post information ethical and legit!";
            }
            if($user_push = UserPush::whereUsersId($item->user_id)->first()){
                $players_ids[] = $user_push->player_id;
                $contents = ["en" => $message];
                $headings = ["en" => $title];
                $info['contents'] = $contents;
                $info['headings'] = $headings;
                $info['player_ids'] = $players_ids;
                $info['data'] = ['meta_id' => $item->id,'type' => 'post'];
                $result = parent::oneSignal($info);

                $code = Controller::number_generator(6);

                Inbox::create([
                    'user_id' => $item->user_id,
                    'title' => $title,
                    'message' => $message,
                    'image' => null,
                    'type' => 'targeted',
                    'code' => $code
                ]);

                //send email to user with response
                $user = User::findOrFail($item->user_id);
                if($user && $user->email){
                    $info = [];
                    $info['to'] = $user->email;
                    $info['icon'] = 'alert.png';
                    $info['message'] = $message;
                    $info['cms_user'] = auth()->user();
                    $info['title'] = $title;
                    Mail::send('emails.barter_response', ['data' => $info], function ($message) use ($info) {
                        $message->subject($info['title']);
                        $message->to($info['to']);
                    });
                }
              
            }

            $item->status = $item->status == 'in_review' ? 'approved' : 'in_review';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', $this->display_name.' Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $meta_name
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($item_id)
    {
        $item = Item::findOrFail($item_id);
        if($item->item_order != null){
            $all_items = Item::whereSubSubCategoryId($item->sub_sub_category_id)->whereSubCategoryId($item->sub_category_id)->whereCategoryId($item->category_id)->where('item_order','>',$item->item_order)->get();
            foreach ($all_items as $single_item){
                $single_item->item_order = $single_item->item_order - 1;
                $single_item->save();
            }
        }
        $images = Media::whereMetaId($item_id)->whereMetaName('items')->get();
        foreach ($images as $media){
            if($media->media_name != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $media->meta_id . DIRECTORY_SEPARATOR . $media->meta_name . DIRECTORY_SEPARATOR .$media->media_name;
                File::delete($filePath);
            }
            $media->delete();
        }
        $item->delete();
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully deleted the '.$this->display_name);

    }
}

