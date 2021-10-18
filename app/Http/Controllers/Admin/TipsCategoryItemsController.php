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
 * Class TipsCategoryItemsController
 */
class TipsCategoryItemsController extends Controller
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
        $category = TipsCategory::findOrFail($id);
        $items = $category->items()->orderby('item_order','asc')->get();
        return view('pages.tips_categories.items.index', compact('category','items'))
            ->with('section_name', 'tips_categories')
            ->with('content_header', 'Items');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = TipsCategory::findOrFail($id);
        return view('pages.tips_categories.items.create',compact('category'))
            ->with('content_header', 'Add Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $category = TipsCategory::findOrFail($id);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'tips';

        $image = null;
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
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
        $max_order = DB::table('tips_items')->where('category_id','=',$id)->max('item_order');
        $order = $max_order + 1;
        $item = $category->items()->create([
            'image' => $image,
            'item_order' => $order,
            'name' => empty(trim($request->name)) ? null : trim($request->name),
            'description' => empty(trim($request->description)) ? null : trim($request->description),
        ]);

        return redirect()->route('admin.tips_categories.items.index',$id)
            ->with('success', 'Successfully create the Restaurant');
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
        $item = TipsItem::findOrFail($item_id);
        $category = TipsCategory::findOrFail($category_id);
        return view('pages.tips_categories.items.edit', compact('item','category'))
            ->with('content_header', 'Update Restaurant');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$category_id,$item_id)
    {
        $item = TipsItem::findOrFail($item_id);

        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'tips';

        $image = null;
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
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
        if($image != null && $item->getImageName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'tips' . DIRECTORY_SEPARATOR . $item->getImageName();
            File::delete($filePath);
        }

        $data = [
            'item_order' => empty(trim($request->item_order)) ? null : trim($request->item_order),
            'name' => empty(trim($request->name)) ? null : trim($request->name),
            'description' => empty(trim($request->description)) ? null : trim($request->description),
        ];

        if($image != null){
            $data['image'] = $image;
        }

        $item->update($data);

        return redirect()->route('admin.tips_categories.items.index',$item->category_id)
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function delete_single_image(TipsItem $item)
    {
        if($item->getImageName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'tips' . DIRECTORY_SEPARATOR . $item->getImageName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }


    public function destroy($category_id,$item_id)
    {
        $item = TipsItem::findOrFail($item_id);

        $category = TipsCategory::findOrFail($category_id);
        if($item->getImageName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'tips' . DIRECTORY_SEPARATOR . $item->getImageName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.tips_categories.items.index',$category_id)
            ->with('success', 'Successfully deleted an Restaurant from Category');
    }
}

