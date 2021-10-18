<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Requests\BannerRequest;
use App\Http\Requests\EventRequest;
use App\Http\Requests\NewsRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\BusinessNews;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
use App\Models\Api\Event;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\Restaurant;
use App\Models\Api\LocateUs;
use App\Models\Api\Media;
use App\Models\Api\News;
use App\Models\Api\OfferPromotion;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\Songs;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class NewsController
 */
class NewsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        $this->sections = implode(',',$this->sections);
//        if(auth()->user() == null || (strpos($this->sections,'all') === false) && strpos($this->sections,'users') === false){
//            abort(401);
//        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = News::orderBy('news_datetime','asc')->get();
        $accessible_sections = $this->sections;
        return view('pages.news.index', compact('items','accessible_sections'))
            ->with('section_name', 'news')
            ->with('content_header', 'News');
    }

    public function manage_public($id){
        $item = News::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.news.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_private($id){
        $item = News::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.news.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.news.create')
            ->with('content_header', 'Add Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsRequest $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'news';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $image_name = uniqid().".jpg";
                    $width = 1366;
                    $height = 768;

                //Create an object of 'Image' class and call to 'upload_image' function which we are going to use here for our process.
                    $imgObj = new ImageHelper();
                    $upload = $imgObj->upload_image('image', $destinationDir, $image_name, $width, $height);

//                    $data = file_get_contents($file->getPathname());
//                    $base64 = base64_encode($data);
//                    $bin = base64_decode($base64);
//                    $im = imageCreateFromString($bin);

                }catch (\Exception $ex){
                    return redirect()->back()
                        ->with('error_msg', $ex->getMessage());
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
            }
        }
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item_id = null;
        if(isset($request->code)){
            $shop_item = Restaurant::whereCode($request->code)->first();
            if($shop_item){
                $item_id = $shop_item->id;
            }
        }
        News::create([
            'admin_id' => auth()->user()->id,
            'news_datetime' => $item_datetime,
            'item_id' => $item_id,
            'target' => 'private',
            'media' => $fileName == null ? 'https://www.youtube.com/watch?v='.trim($request->youtube_id) : $fileName,
            'thumbnail' => $fileName == null ? 'http://img.youtube.com/vi/'.trim($request->youtube_id).'/0.jpg' : null,
            'media_type' => $fileName == null ? 'video' : 'image',
            'youtube_id' => empty($request->youtube_id) ? null : trim($request->youtube_id),
            'caption_en' => empty($request->caption_en) ? null : trim($request->caption_en),
            'caption_ar' => empty($request->caption_ar) ? null : trim($request->caption_ar),
//            'link' => empty($request->link) ? null : trim($request->link),
            'description_en' => empty($request->description_en) ? null : trim($request->description_en),
            'description_ar' => empty($request->description_ar) ? null : trim($request->description_ar),
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'Successfully create a new Restaurant');
    }

    public function delete_single_image($id)
    {
        $item = News::findOrFail($id);
        if($item->media_type == 'image' && $item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->media = null;
        $item->media_type = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    public function publish($id){
        $item = News::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.news.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    public function points($id){
        $item = News::find($id);
        if($item){
            $item->redeemable = !$item->redeemable;
            $item->save();
        }
        return redirect()->route('admin.news.index')
            ->with('success', 'Restaurant Status Successfully updated');
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
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = News::findOrFail($id);
        $code = null;
        if($item->item_id != null && $item->item){
            $code = $item->item->code;
        }
        return view('pages.news.edit', compact('item','code'))
            ->with('content_header', 'Update Restaurant');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsRequest $request, $id)
    {
        $item = News::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'news';
        $fileName = null;
        if($item->media_type == 'image' && $item->getMediaName() != null) {
            $fileName = $item->getMediaName();
        }
        // check if a file exist and is valid.
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
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                if($item->media_type == 'image' && $item->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }
        if(!empty($request->youtube_id)){
            if($item->media_type == 'image' && $item->getMediaName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . $item->getMediaName();
                File::delete($filePath);
            }
        }
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item_id = null;
        if(isset($request->code)){
            $shop_item = Restaurant::whereCode($request->code)->first();
            if($shop_item){
                $item_id = $shop_item->id;
            }
        }
        $item->update([
            'news_datetime' => $item_datetime,
            'item_id' => $item_id,
            'media' => $fileName == null ? 'https://www.youtube.com/watch?v='.trim($request->youtube_id) : $fileName,
            'thumbnail' => $fileName == null ? 'http://img.youtube.com/vi/'.trim($request->youtube_id).'/0.jpg' : null,
            'media_type' => $fileName == null ? 'video' : 'image',
            'youtube_id' => empty($request->youtube_id) ? null : trim($request->youtube_id),
            'caption_en' => empty($request->caption_en) ? null : trim($request->caption_en),
            'caption_ar' => empty($request->caption_ar) ? null : trim($request->caption_ar),
//            'link' => empty($request->link) ? null : trim($request->link),
            'description_en' => empty($request->description_en) ? null : trim($request->description_en),
            'description_ar' => empty($request->description_ar) ? null : trim($request->description_ar),
        ]);
        return redirect()->route('admin.news.index')
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function destroy($id)
    {
        $item = News::findOrFail($id);
        if($item->media_type == 'image' && $item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Successfully deleted an Restaurant');
    }
}

