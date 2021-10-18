<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Models\Api\Ad;
use App\Models\Api\AtyappCategory;
use App\Models\Api\Banner;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
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

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class TipsCategoryController
 */
class TipsCategoryController extends Controller
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
    public function index()
    {
        $categories = TipsCategory::orderby('item_order','asc')
            ->get();

        return view('pages.tips_categories.index', compact('categories'))
            ->with('section_name', 'tips_categories')
            ->with('content_header', 'Tips Categories');
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
            $item = TipsCategory::whereId($id)->first();
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
        return view('pages.tips_categories.create')
            ->with('content_header', 'Add Tips Category');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'categories';
//        $fileName = null;
//        // check if a file exist and is valid.
//        if($request->hasFile('image-file')) {
//            if($request->file('image-file')->isValid()) {
//                $file = $request->file('image-file');
//                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
//                $file->move($destinationDir, $fileName);
//            }
//        }
        $max_order = DB::table('tips_categories')->max('item_order');
        $order = $max_order + 1;
        TipsCategory::create([
            'item_order' => $order,
//            'image' => $fileName,
            'name' => empty($request->name) ? null : trim($request->name),
        ]);

        return redirect()->route('admin.tips_categories.index')
            ->with('success', 'Successfully create a new Category');
    }

    public function delete_single_image($id)
    {
//        $category = AtyappCategory::findOrFail($id);
//        if($category->image != null) {
//            $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->image;
//            File::delete($filePath);
//        }
//        $category->image = null;
//        $category->save();
//
//        return redirect()->back()->with('success', 'Successfully deleted the image');
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
        $category = TipsCategory::findOrFail($id);

        return view('pages.tips_categories.edit', compact('category'))
            ->with('content_header', 'Update Tips Category');
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
        $category = TipsCategory::findOrFail($id);

        // public/files/{random_file_name}
//        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'categories';
//        $fileName = null;
//        if($category->image != null) {
//            $fileName = $category->getMediaName();
//        }
//        // check if a file exist and is valid.
//        if($request->hasFile('image')) {
//            if($request->file('image')->isValid()) {
//                $file = $request->file('image');
//                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
//                $file->move($destinationDir, $fileName);
//                if($category->image != null) {
//                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->getMediaName();
//                    File::delete($filePath);
//                }
//            }
//        }
        $category->update([
//            'image' => $fileName,
            'name' => empty($request->name) ? null : trim($request->name),
            'item_order' => empty($request->item_order) ? 1 : trim($request->item_order),
        ]);
        return redirect()->route('admin.tips_categories.index')
            ->with('success', 'Successfully updated the Category');
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {

        $category = TipsCategory::findOrFail($id);
        $all_items = TipsCategory::where('item_order','>',$category->item_order)->get();
        foreach ($all_items as $single_item){
            $single_item->item_order = $single_item->item_order - 1;
            $single_item->save();
        }
//        if($category->image != null) {
//            $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->image;
//            File::delete($filePath);
//        }
        $category->delete();

        return redirect()->route('admin.tips_categories.index')
            ->with('success', 'Successfully deleted the Category');
    }
}

