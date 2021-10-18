<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Requests\BannerRequest;
use App\Models\Api\Banner;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class BannersController
 */
class BannersController extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Banner::orderBy('banner_order','asc')->get();
        $accessible_sections = $this->sections;
        return view('pages.banners.index', compact('items','accessible_sections'))
            ->with('section_name', 'banners')
            ->with('content_header', 'Banners');
    }

    public function manage_public($id){
        $item = Banner::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.banners.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_both($id){
        $item = Banner::find($id);
        if($item){
            $item->target = 'both';
            $item->save();
        }
        return redirect()->route('admin.banners.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_private($id){
        $item = Banner::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.banners.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.banners.create')
            ->with('content_header', 'Add Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerRequest $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'banners';
        $fileName = null;
        $width = null;
        $height = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
//                    $image_name = uniqid().".jpg";
//                    $width = 1366;
//                    $height = 768;
//
//                    //Create an object of 'Image' class and call to 'upload_image' function which we are going to use here for our process.
//                    $imgObj = new ImageHelper();
//                    $upload = $imgObj->upload_image('image', $destinationDir, $image_name, $width, $height);

                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);

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
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            }
        }
        $max_order = DB::table('banners')->max('banner_order');
        $order = $max_order + 1;
        Banner::create([
            'target' => 'private',
            'banner_order' => $order,
            'banner_image' => $fileName,
            'width' => $width,
            'height' => $height,
//            'caption_en' => empty($request->caption_en) ? null : trim($request->caption_en),
//            'caption_ar' => empty($request->caption_ar) ? null : trim($request->caption_ar),
            'banner_link' => empty($request->link) ? null : trim($request->link),
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Successfully create a new Restaurant');
    }

    public function delete_single_image($id)
    {
        $item = Banner::findOrFail($id);
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'banners' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->banner_image = null;
        $item->width = null;
        $item->height = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    public function publish($id){
        $item = Banner::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.banners.index')
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
        $item = Banner::findOrFail($id);
        return view('pages.banners.edit', compact('item'))
            ->with('content_header', 'Update Restaurant');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request, $id)
    {
        $item = Banner::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'banners';
        $fileName = null;
        $width = null;
        $height = null;
        if($item->getMediaName() != null) {
            $fileName = $item->getMediaName();
            $width = $item->width;
            $height = $item->height;
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
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
                if($item->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'banners' . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }

        $item->update([
            'banner_image' => $fileName,
            'width' => $width,
            'height' => $height,
            'banner_order' => $request->item_order,
//            'caption_en' => empty($request->caption_en) ? null : trim($request->caption_en),
//            'caption_ar' => empty($request->caption_ar) ? null : trim($request->caption_ar),
            'banner_link' => empty($request->link) ? null : trim($request->link),
        ]);
        return redirect()->route('admin.banners.index')
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function destroy($id)
    {
        $item = Banner::findOrFail($id);
        $banners = Banner::where('banner_order','>',$item->banner_order)->get();
        foreach ($banners as $single_item){
            $single_item->banner_order = $single_item->banner_order - 1;
            $single_item->save();
        }
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'banners' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Successfully deleted an Restaurant');
    }
}

