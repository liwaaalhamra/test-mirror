<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\City;
use App\Models\Api\Event;
use App\Models\Api\Game;
use App\Models\Api\Media;
use App\Models\Api\News;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\Songs;
use App\Models\Api\Venue;
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
 * Class EventsController
 */
class EventsController extends Controller
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
        $items = Event::latest()->get();
        return view('pages.events.index', compact('items'))
            ->with('section_name', 'events')
            ->with('content_header', 'events');
    }

    public function manage_public($id){
        $item = Event::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.events.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_private($id){
        $item = Event::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.events.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    public function publish($id){
        $item = Event::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.events.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.events.create')
            ->with('content_header', 'Add Event');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'events';
        $input = $request->all();
        $files = $input['file'];
        if(empty($files[0])){
            $files = [];
        }
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item = Event::create([
            'name' => trim($request->name),
            'short_description' => empty($request->short_description) ? null : trim($request->short_description),
            'description' => empty($request->description) ? null : trim($request->description),
            'item_datetime' => $item_datetime,
            'link' => empty($request->link) ? null : trim($request->link),
            'latitude' => empty($request->latitude) ? null : trim($request->latitude),
            'longitude' => empty($request->longitude) ? null : trim($request->longitude),
            'points' => empty($request->points) ? null : trim($request->points),
        ]);

        foreach ($files as $file){
            $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationDir, $fileName);
            Media::create([
                'meta_id' => $item->id,
                'meta_name' => 'events',
                'media_name' => $fileName,
                'media_type' => 'images',
            ]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Successfully created the Event');
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
        $item = Event::findOrFail($id);

        return view('pages.events.edit', compact('item'))
            ->with('content_header', 'Update Event');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $item = Event::findOrFail($input['item_id']);
        $files = $input['file'];
        if(empty($files[0])){
            $files = [];
        }
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'events';
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item->update([
            'name' => trim($request->name),
            'short_description' => empty($request->short_description) ? null : trim($request->short_description),
            'description' => empty($request->description) ? null : trim($request->description),
            'item_datetime' => $item_datetime,
            'link' => empty($request->link) ? null : trim($request->link),
            'latitude' => empty($request->latitude) ? null : trim($request->latitude),
            'longitude' => empty($request->longitude) ? null : trim($request->longitude),
            'points' => empty($request->points) ? null : trim($request->points),
        ]);
        foreach ($files as $file){
            $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationDir, $fileName);
            Media::create([
                'meta_id' => $item->id,
                'meta_name' => 'events',
                'media_name' => $fileName,
                'media_type' => 'images',
            ]);
        }
        return redirect()->route('admin.events.index')
            ->with('success', 'Successfully updated the Event');
    }

    public function get_uploaded_images(Request $request){
        $input = $request->all();
        $item_id = $input['item_id'];
        $item = Event::findOrFail($item_id);
        $files = $item->images;
        $result = [];
        foreach ($files as $file){
            $obj['name'] = $file->getMediaName();
            $obj['size'] = filesize(public_path('/uploads/images/events/'.$file->getMediaName()));
            $result[] = $obj;
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function delete_images(Request $request){

        $input = $request->all();
        $item_id = $input['id'];
        $file_name = $input['file_name'];
        $item = Event::whereId($item_id)->first();
        $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $file_name;
        if(file_exists($filePath)) {
            File::delete($filePath);
        }
        Media::whereMetaName('events')->whereMetaId($item_id)->whereMediaName($file_name)->delete();
    }

    public function destroy($id)
    {
        $item = Event::findOrFail($id);

        $images = Media::whereMetaId($id)->whereMetaName('events')->get();
        foreach ($images as $media){
            if($media->media_name != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $media->media_name;
                File::delete($filePath);
            }
            $media->delete();
        }
        $item->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Successfully deleted the Event');
    }
}

