<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Models\Api\Ad;
use App\Models\Api\AtyappCategory;
use App\Models\Api\Banner;
use App\Models\Api\Category;
use App\Models\Api\CollectionCategory;
use App\Models\Api\CorporateActivity;
use App\Models\Api\CuratedCollection;
use App\Models\Api\Game;
use App\Models\Api\Media;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\ServiceCategory;
use App\Models\Api\Songs;
use App\Models\Api\TipsCategory;
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
 * Class CategoriesController
 */
class CategoriesController extends Controller
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
        $categories = Category::orderby('item_order','asc')
            ->get();

        return view('pages.categories.index', compact('categories'))
            ->with('section_name', 'categories')
            ->with('content_header', 'Categories');
    }

    public function list_collections($category_id){
        $collections = CuratedCollection::whereIsPublished(1)->whereCategoryId($category_id)->orderby('item_order','asc')->pluck('name_en','id')->tojson();
        return $collections;
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
            $item = Category::whereId($id)->first();
            $item->item_order = $key + 1;
            $item->save();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.categories.create')
            ->with('content_header', 'Add Category');
    }

    public function publish($id){
        $item = Category::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category Status Successfully updated');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'categories';
        $fileName = null;
        $width = null;
        $height = null;
        // check if a file exist and is valid.
        if($request->hasFile('image-file')) {
            if($request->file('image-file')->isValid()) {
                $file = $request->file('image-file');
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
                    'image-file' => 'mimetypes:image/jpeg,image/png',
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
        $type = 'products';
        if(isset($request->type) && $request->type == 'on'){
            $type = 'services';
        }
        $max_order = DB::table('categories')->max('item_order');
        $order = $max_order + 1;
        Category::create([
            'item_order' => $order,
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'type' => $type,
            'is_published' => 1,
            'name_en' => empty($request->name_en) ? null : trim($request->name_en),
            'name_ar' => empty($request->name_ar) ? null : trim($request->name_ar),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Successfully create a new Category');
    }

    public function delete_single_image($id)
    {
        $category = Category::findOrFail($id);
        if($category->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->getMediaName();
            File::delete($filePath);
        }
        $category->image = null;
        $category->width = null;
        $category->height = null;
        $category->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
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
        $category = Category::findOrFail($id);

        return view('pages.categories.edit', compact('category'))
            ->with('content_header', 'Update Category');
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
        $category = Category::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories';
        $fileName = null;
        $width = null;
        $height = null;
        if($category->getMediaName() != null) {
            $fileName = $category->getMediaName();
            $width = $category->width;
            $height = $category->height;
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
                if($category->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->getMediaName();
                    File::delete($filePath);
                }
            }
        }
        $type = 'products';
        if(isset($request->type) && $request->type == 'on'){
            $type = 'services';
        }
        $category->update([
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
            'type' => $type,
            'name_en' => empty($request->name_en) ? null : trim($request->name_en),
//            'name_ar' => empty($request->name_ar) ? null : trim($request->name_ar),
            'item_order' => empty($request->item_order) ? 1 : trim($request->item_order),
        ]);
        return redirect()->route('admin.categories.index')
            ->with('success', 'Successfully updated the Category');
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {

        $category = CollectionCategory::findOrFail($id);
        $all_items = CollectionCategory::where('item_order','>',$category->item_order)->get();
        foreach ($all_items as $single_item){
            $single_item->item_order = $single_item->item_order - 1;
            $single_item->save();
        }
        if($category->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->getMediaName();
            File::delete($filePath);
        }
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Successfully deleted the Category');
    }
}

