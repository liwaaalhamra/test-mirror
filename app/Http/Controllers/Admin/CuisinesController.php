<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Requests\BannerRequest;
use App\Models\Api\Banner;
use App\Models\Api\Craving;
use App\Models\Api\Cuisine;
use App\Models\Api\Service;
use App\Models\Api\Tag;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class CuisinesController
 */
class CuisinesController extends Controller
{
    var $section_name = 'cuisines';
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
        $items = Cuisine::orderBy('item_order','asc')->get();
        return view('pages.'.$this->section_name.'.index', compact('items'))
            ->with('section_name', $this->section_name)
            ->with('content_header', ucwords($this->display_name));
    }

    public function manage_public($id){
        $item = Cuisine::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }
    public function manage_both($id){
        $item = Cuisine::find($id);
        if($item){
            $item->target = 'both';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }
    public function manage_private($id){
        $item = Cuisine::find($id);
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
        $max_order = DB::table($this->section_name)->max('item_order');
        $order = $max_order + 1;
        Cuisine::create([
            'item_order' => $order,
            'name_en' => empty($request->name_en) ? null : trim($request->name_en),
            'name_ar' => empty($request->name_ar) ? null : trim($request->name_ar),
        ]);

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully create a new '.ucwords($this->display_name));
    }

    public function delete_single_image($id)
    {
        $item = Cuisine::findOrFail($id);
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

    public function publish($id){
        $item = Cuisine::find($id);
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
        $item = Cuisine::findOrFail($id);
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
        $item = Cuisine::findOrFail($id);

        $item->update([
            'item_order' => $request->item_order,
            'name_en' => empty($request->name_en) ? null : trim($request->name_en),
            'name_ar' => empty($request->name_ar) ? null : trim($request->name_ar),
        ]);
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully updated the '.ucwords($this->display_name));
    }

    public function destroy($id)
    {
        $item = Cuisine::findOrFail($id);
        $items = Cuisine::where('item_order','>',$item->item_order)->get();
        foreach ($items as $single_item){
            $single_item->item_order = $single_item->item_order - 1;
            $single_item->save();
        }
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully deleted the '.ucwords($this->display_name));
    }
}

