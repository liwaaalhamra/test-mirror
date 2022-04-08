<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\AtyappCategory;
use App\Models\Api\Category;
use App\Models\Api\CategoryFish;
use App\Models\Api\CorporateActivity;
use App\Models\Api\CorporateActivityItem;
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
 * Class SubCategoriesController
 */
class SubCategoriesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        $this->sections = implode(',',$this->sections);
//        if(auth()->user() == null || (strpos($this->sections,'all') === false && strpos($this->sections,'users') === false) ){
//            abort(401);
//        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $category = Category::findOrFail($id);
        $items = $category->sub_categories()->orderby('item_order','asc')->get();
        return view('pages.categories.sub_categories.index', compact('category','items'))
            ->with('section_name', 'sub_categories')
            ->with('content_header', 'Sub Categories');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.categories.sub_categories.create',compact('category'))
            ->with('content_header', 'Add Sub Category');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $category = Category::findOrFail($id);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'categories';

        $image = null;
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
            }
        }
        $max_order = DB::table('sub_categories')->where('category_id','=',$id)->max('item_order');
        $order = $max_order + 1;
        $item = $category->sub_categories()->create([
//            'image' => $image,
            'item_order' => $order,
            'is_published' => 1,
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
//            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
//            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ]);

        return redirect()->route('admin.categories.sub_categories.index',$id)
            ->with('success', 'Successfully create the Sub Category');
    }

    public function publish($id){
        $item = SubCategory::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->back()
            ->with('success', 'Sub Category Status Successfully updated');
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
        $item = SubCategory::findOrFail($item_id);
        $category = Category::findOrFail($category_id);
        return view('pages.categories.sub_categories.edit', compact('item','category'))
            ->with('content_header', 'Update Sub Category');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$category_id,$item_id)
    {
        $item = SubCategory::findOrFail($item_id);

        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories';

        $image = null;
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
            }
        }

        /**
         * delete old file
         */
        if($image != null && $item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }

        $data = [
            'item_order' => empty(trim($request->item_order)) ? null : trim($request->item_order),
            'name_en' => empty(trim($request->name_en)) ? null : trim($request->name_en),
            'name_ar' => empty(trim($request->name_ar)) ? null : trim($request->name_ar),
            'description_en' => empty(trim($request->description_en)) ? null : trim($request->description_en),
            'description_ar' => empty(trim($request->description_ar)) ? null : trim($request->description_ar),
        ];

        if($image != null){
            $data['image'] = $image;
        }

        $item->update($data);

        return redirect()->route('admin.categories.sub_categories.index',$item->category_id)
            ->with('success', 'Successfully updated the Sub Category');
    }

    public function delete_single_image($item_id)
    {
        $item = SubCategory::findOrFail($item_id);
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }


    public function destroy($category_id,$item_id)
    {
        $item = SubCategory::findOrFail($item_id);

        $category = Category::findOrFail($category_id);
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.categories.sub_categories.index',$category_id)
            ->with('success', 'Successfully deleted the Sub Category');
    }
}

