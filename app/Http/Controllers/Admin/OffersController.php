<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Requests\BannerRequest;
use App\Models\Api\Offer;
use App\Models\Api\Restaurant;
use App\Models\Api\SubCategory;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class OffersController
 */
class OffersController extends Controller
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
        $items = Offer::orderBy('item_order','asc')->get();
        $accessible_sections = $this->sections;
        return view('pages.offers.index', compact('items','accessible_sections'))
            ->with('section_name', 'offers')
            ->with('content_header', 'Offers');
    }

    public function manage_public($id){
        $item = Offer::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.offers.index')
            ->with('success', 'Status Successfully updated');
    }
    public function manage_private($id){
        $item = Offer::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.offers.index')
            ->with('success', 'Status Successfully updated');
    }

    public function manage_both($id){
        $item = Offer::find($id);
        if($item){
            $item->target = 'both';
            $item->save();
        }
        return redirect()->route('admin.offers.index')
            ->with('success', 'Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $items = Restaurant::orderBy('item_order','asc')->get();
        return view('pages.offers.create',compact('items'))
            ->with('content_header', 'Add New Offer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerRequest $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'offers';
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
        $max_order = DB::table('offers')->max('item_order');
        $order = $max_order + 1;
        Offer::create([
            'target' => 'private',
            'item_order' => $order,
            'item_id' => empty($request->item_id) ? null : $request->item_id,
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'link' => empty($request->link) ? null : trim($request->link),
            'title_en' => empty($request->title_en) ? null : trim($request->title_en),
            'title_ar' => empty($request->title_ar) ? null : trim($request->title_ar),
            'subtitle_en' => empty($request->subtitle_en) ? null : trim($request->subtitle_en),
            'subtitle_ar' => empty($request->subtitle_ar) ? null : trim($request->subtitle_ar)
        ]);

        return redirect()->route('admin.offers.index')
            ->with('success', 'Successfully create a new Restaurant');
    }

    public function delete_single_image($id)
    {
        $item = Offer::findOrFail($id);
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'offers' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->width = null;
        $item->height = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    public function publish($id){
        $item = Offer::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.offers.index')
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
        $item = Offer::findOrFail($id);
        $items = Restaurant::orderBy('item_order','asc')->get();
        return view('pages.offers.edit', compact('item','items'))
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
        $item = Offer::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'offers';
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
                if($item->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'offers' . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }

        $item->update([
            'item_order' => $request->item_order,
            'item_id' => empty($request->item_id) ? null : $request->item_id,
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'link' => empty($request->link) ? null : trim($request->link),
            'title_en' => empty($request->title_en) ? null : trim($request->title_en),
            'title_ar' => empty($request->title_ar) ? null : trim($request->title_ar),
            'subtitle_en' => empty($request->subtitle_en) ? null : trim($request->subtitle_en),
            'subtitle_ar' => empty($request->subtitle_ar) ? null : trim($request->subtitle_ar)
        ]);
        return redirect()->route('admin.offers.index')
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function destroy($id)
    {
        $item = Offer::findOrFail($id);
        $items = Offer::where('item_order','>',$item->item_order)->get();
        foreach ($items as $single_item){
            $single_item->item_order = $single_item->item_order - 1;
            $single_item->save();
        }
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'offers' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.offers.index')
            ->with('success', 'Successfully deleted an Restaurant');
    }
}

