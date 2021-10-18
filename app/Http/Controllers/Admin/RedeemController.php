<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\Game;
use App\Models\Api\Player;
use App\Models\Api\RedeemItem;
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
 * Class RedeemController
 */
class RedeemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = RedeemItem::select('id','image','title','description','is_activated','quantity','points','created_at as date')
            ->orderBy('item_order','asc')
            ->paginate(8);

        return view('pages.redeem_items.index', compact('items'))
            ->with('section_name', 'redeem_items')
            ->with('content_header', 'Redeem Items');
    }

    /**
     * Reorder Banners.
     *
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request){
        $input = $request->all();
        $ids = $input['ids'];
        $ids = explode(',',$ids);
        foreach ($ids as $key => $id){
            $item = RedeemItem::whereId($id)->first();
            $item->item_order = $key + 1;
            $item->save();
        }
    }

    public function toggle($user_id) {
        // if it's enabled -> disable and vice versa
        $item = RedeemItem::findOrFail($user_id);
        $item->is_activated = !$item->is_activated;
        $item->save();

        return redirect()->route('admin.redeem_items.index')
            ->with('success', 'Restaurant Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.redeem_items.create')
            ->with('content_header', 'Add Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'redeem';
        $fileName = null;
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
            }
        }
        $max_order = DB::table('redeem_items')->max('item_order');
        $order = $max_order + 1;
        RedeemItem::create([
            'image' => $fileName,
            'title' => empty($request->title) ? null : trim($request->title),
            'description' => empty($request->description) ? null : trim($request->description),
            'quantity' => $request->quantity,
            'points' => $request->points,
            'is_activated' => 0,
            'item_order' => $order
        ]);

        return redirect()->route('admin.redeem_items.index')
            ->with('success', 'Successfully create a new item');
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
        $item = RedeemItem::findOrFail($id);
        $extension = null;
        if($item->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'redeem' . DIRECTORY_SEPARATOR . $item->image;
            $extension = parent::getExtension($filePath);
        }
        return view('pages.redeem_items.edit', compact('item','extension'))
            ->with('content_header', 'Update Restaurant');
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
        $item = RedeemItem::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'redeem';
        $fileName = $item->image;
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
                if($item->image != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'redeem' . DIRECTORY_SEPARATOR . $item->image;
                    File::delete($filePath);
                }
            }
        }
        $item->update([
            'image' => $fileName,
            'title' => empty($request->title) ? null : trim($request->title),
            'description' => empty($request->description) ? null : trim($request->description),
            'quantity' => $request->quantity,
            'points' => $request->points,
        ]);
        return redirect()->route('admin.redeem_items.index')
            ->with('success', 'Successfully updated the item');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = RedeemItem::findOrFail($id);
//        $items = RedeemItem::where('item_order','>',$item->item_order)->get();
//        foreach ($items as $row){
//            $row->item_order = $row->item_order - 1;
//            $row->save();
//        }
        if($item->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'redeem' . DIRECTORY_SEPARATOR . $item->image;
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.redeem_items.index')
            ->with('success', 'Successfully deleted an item');
    }
}

