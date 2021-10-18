<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Requests\BannerRequest;
use App\Models\Api\Banner;
use App\Models\Api\Craving;
use App\Models\Api\Service;
use App\Models\Api\Tag;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class CravingsController
 */
class CravingsController extends Controller
{
    var $section_name = 'top_cravings';
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
    public function index()
    {
        $items = Craving::orderBy('item_order','asc')->get();
        return view('pages.'.$this->section_name.'.index', compact('items'))
            ->with('section_name', $this->section_name)
            ->with('content_header', ucwords($this->display_name));
    }

    public function manage_public($id){
        $item = Craving::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }
    public function manage_both($id){
        $item = CravingCraving::find($id);
        if($item){
            $item->target = 'both';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }
    public function manage_private($id){
        $item = Craving::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.'.$this->section_name.'.create')
            ->with('content_header', 'Add '.ucwords($this->display_name));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'cravings';
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
        $max_order = DB::table($this->section_name)->max('item_order');
        $order = $max_order + 1;
        Craving::create([
            'item_order' => $order,
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'name_en' => empty($request->name_en) ? null : trim($request->name_en),
            'name_ar' => empty($request->name_ar) ? null : trim($request->name_ar),
        ]);

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully create a new '.ucwords($this->display_name));
    }

    public function delete_single_image($id)
    {
        $item = Craving::findOrFail($id);
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'cravings' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->width = null;
        $item->height = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    public function publish($id){
        $item = Craving::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
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
        $item = Craving::findOrFail($id);
        return view('pages.'.$this->section_name.'.edit', compact('item'))
            ->with('content_header', 'Update '.$this->display_name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Craving::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'cravings';
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
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'cravings' . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }

        $item->update([
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'item_order' => $request->item_order,
            'name_en' => empty($request->name_en) ? null : trim($request->name_en),
            'name_ar' => empty($request->name_ar) ? null : trim($request->name_ar),
        ]);
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully updated the '.ucwords($this->display_name));
    }

    public function destroy($id)
    {
        $item = Craving::findOrFail($id);
        $items = Craving::where('item_order','>',$item->item_order)->get();
        foreach ($items as $single_item){
            $single_item->item_order = $single_item->item_order - 1;
            $single_item->save();
        }
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'cravings' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully deleted the '.ucwords($this->display_name));
    }
}

