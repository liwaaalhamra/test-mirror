<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\AtyappCategory;
use App\Models\Api\Category;
use App\Models\Api\CategoryFish;
use App\Models\Api\CollectionCategory;
use App\Models\Api\CorporateActivity;
use App\Models\Api\CorporateActivityItem;
use App\Models\Api\CuratedCollection;
use App\Models\Api\Fish;
use App\Models\Api\FishSize;
use App\Models\Api\Game;
use App\Models\Api\Restaurant;
use App\Models\Api\Media;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\ServiceCategory;
use App\Models\Api\Songs;
use App\Models\Api\SubCategory;
use App\Models\Api\TipsCategory;
use App\Models\Api\TipsItem;
use App\Models\Api\UserCart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;


/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class CuratedCollectionsController
 */
class CuratedCollectionsController extends Controller
{
    var $section_name = 'categories';
    var $sub_section_name = 'sub_categories';
    var $display_name = 'Collections';
    public function __construct()
    {
        parent::__construct();
//        $this->display_name = str_replace('_',' ',ucwords($this->section_name,'_'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $category = CollectionCategory::findOrFail($id);
        $items = CuratedCollection::whereCategoryId($id)->orderBy('item_order')->get();
        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.index', compact('category','items'))
            ->with('section_name', $this->section_name.'.'.$this->sub_section_name)
            ->with('content_header', $this->display_name);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = CollectionCategory::findOrFail($id);
        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.create',compact('category'))
            ->with('content_header', 'Add '.$this->display_name);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $category = CollectionCategory::findOrFail($id);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . $this->section_name;

        $fileName = null;
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
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            }
        }
        $is_featured = false;
        if(isset($request->is_featured) && $request->is_featured == 'on'){
            $is_featured = true;
        }
        $max_order = DB::table('curated_collections')->where('category_id','=',$id)->max('item_order');
        $order = $max_order + 1;
        $item = $category->collections()->create([
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'is_featured' => $is_featured,
            'item_order' => $order,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$id)
            ->with('success', 'Successfully create the '.$this->sub_section_name);
    }

    public function publish($id){
        $item = CuratedCollection::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->back()->with('success', 'Status Successfully updated');
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
     * @param  int  $category_id
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function edit($category_id,$item_id)
    {
        $item = CuratedCollection::findOrFail($item_id);
        $category = CollectionCategory::findOrFail($category_id);
        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.edit', compact('item','category'))
            ->with('content_header', 'Update '.$this->display_name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$category_id,$item_id)
    {
        $item = CuratedCollection::findOrFail($item_id);

        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name;

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
                if($fileName != null && $item->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }
        $is_featured = false;
        if(isset($request->is_featured) && $request->is_featured == 'on'){
            $is_featured = true;
        }
        $item->update([
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'is_featured' => $is_featured,
            'item_order' => empty(trim($request->item_order)) ? null : trim($request->item_order),
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$item->category_id)
            ->with('success', 'Successfully updated the '.$this->display_name);
    }

    public function delete_single_image($item_id)
    {
        $item = CuratedCollection::findOrFail($item_id);
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


    public function destroy($category_id,$item_id)
    {
        $item = CuratedCollection::findOrFail($item_id);

        $category = Category::findOrFail($category_id);
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . $this->section_name . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully deleted the '.$this->display_name);
    }
}

