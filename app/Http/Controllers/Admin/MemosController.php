<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\BusinessNews;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\Job;
use App\Models\Api\LocateUs;
use App\Models\Api\Media;
use App\Models\Api\Memo;
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

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class MemosController
 */
class MemosController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || !in_array('all',$this->sections)){
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
        $items = Memo::orderBy('memo_datetime','asc')->get();

        return view('pages.memos.index', compact('items'))
            ->with('section_name', 'memos')
            ->with('content_header', 'Memos');
    }

    public function publish($id){
        $item = Memo::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.memos.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    public function delete_single_image($id,$type)
    {
        $item = Memo::findOrFail($id);
        if($type == 'image'){
            if($item->getImageName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'memos' . DIRECTORY_SEPARATOR . $item->getImageName();
                File::delete($filePath);
            }
            $item->image = null;
        }else{
            if($item->getPdfName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'memos' . DIRECTORY_SEPARATOR . $item->getPdfName();
                File::delete($filePath);
            }
            $item->pdf = null;
        }

        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the Media');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.memos.create')
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
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents' . DIRECTORY_SEPARATOR . 'memos';
        $fileName1 = null;
        if($request->hasFile('pdf')) {
            if($request->file('pdf')->isValid()) {
                $file1 = $request->file('pdf');
                $fileName1 = str_slug(str_random(20)) . '.' . $file1->getClientOriginalExtension();
                $file1->move($destinationDir1, $fileName1);
            }
        }
        Memo::create([
            'memo_datetime' => $item_datetime,
            'pdf' => $fileName1,
            'caption' => empty($request->caption) ? null : trim($request->caption),
//            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description)
        ]);
        DB::table('users')
            ->update([
                'unread_memos_count' => DB::raw('unread_memos_count + 1'),
            ]);
        return redirect()->route('admin.memos.index')
            ->with('success', 'Successfully create a new Memo');
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
        $item = Memo::findOrFail($id);

        return view('pages.memos.edit', compact('item'))
            ->with('content_header', 'Update Memo');
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
        $item = Memo::findOrFail($id);

        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $destinationDir1 = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'memos';
        $fileName1 = null;
        if($item->getPdfName() != null) {
            $fileName1 = $item->getPdfName();
        }
        if($request->hasFile('pdf')) {
            if($request->file('pdf')->isValid()) {
                $file1 = $request->file('pdf');
                $fileName1 = str_slug(str_random(20)) . '.' . $file1->getClientOriginalExtension();
                $file1->move($destinationDir1, $fileName1);
                if($item->getPdfName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'memos' . DIRECTORY_SEPARATOR . $item->getPdfName();
                    File::delete($filePath);
                }
            }
        }
        $item->update([
            'memo_datetime' => $item_datetime,
            'pdf' => $fileName1,
            'caption' => empty($request->caption) ? null : trim($request->caption),
//            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description)
        ]);
        return redirect()->route('admin.memos.index')
            ->with('success', 'Successfully updated the Memo');
    }

    public function destroy($id)
    {
        $item = Memo::findOrFail($id);
        if($item->getPdfName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/documents'  . DIRECTORY_SEPARATOR . 'memos' . DIRECTORY_SEPARATOR . $item->getPdfName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.memos.index')
            ->with('success', 'Successfully deleted the Memo');
    }
}

