<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Requests\BannerRequest;
use App\Models\Api\Banner;
use App\Models\Api\CostLevel;
use App\Models\Api\Service;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class CostsController
 */
class CostsController extends Controller
{
    var $section_name = 'cost_levels';
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
        $items = CostLevel::orderBy('amount','asc')->get();
        return view('pages.'.$this->section_name.'.index', compact('items'))
            ->with('section_name', $this->section_name)
            ->with('content_header', ucwords($this->display_name));
    }

    public function manage_public($id){
        $item = CostLevel::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }
    public function manage_both($id){
        $item = CostLevel::find($id);
        if($item){
            $item->target = 'both';
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', ucwords($this->display_name).' Status Successfully updated');
    }
    public function manage_private($id){
        $item = CostLevel::find($id);
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
        CostLevel::create([
            'amount' => $request->amount
        ]);

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully create a new '.ucwords($this->display_name));
    }

    public function delete_single_image($id)
    {
        $item = CostLevel::findOrFail($id);
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
        $item = CostLevel::find($id);
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
        $item = CostLevel::findOrFail($id);
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
        $item = CostLevel::findOrFail($id);

        $item->update([
            'amount' => $request->amount
        ]);
        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully updated the '.ucwords($this->display_name));
    }

    public function destroy($id)
    {
        $item = CostLevel::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.index')
            ->with('success', 'Successfully deleted the '.ucwords($this->display_name));
    }
}

