<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
use App\Models\Api\Game;
use App\Models\Api\LocateUs;
use App\Models\Api\Media;
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
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class LocateUsController
 */
class LocateUsController extends Controller
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
        $locations = LocateUs::get();

        return view('pages.locate_us.index', compact('locations'))
            ->with('section_name', 'locate_us')
            ->with('content_header', 'Locations');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.locate_us.create')
            ->with('content_header', 'Add Location');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'locate_us';
        $fileName = null;
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
            }
        }

        LocateUs::create([
            'image' => $fileName,
            'title' => empty($request->name) ? null : trim($request->name),
            'latitude' => empty($request->latitude) ? 0 : trim($request->latitude),
            'longitude' => empty($request->longitude) ? 0 : trim($request->longitude),
            'address' => empty($request->description) ? null : trim($request->description)
        ]);

        return redirect()->route('admin.locate_us.index')
            ->with('success', 'Successfully create a new location');
    }

    public function delete_single_image($id)
    {
        $location = LocateUs::findOrFail($id);
        if($location->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'locate_us' . DIRECTORY_SEPARATOR . $location->image;
            File::delete($filePath);
        }
        $location->image = null;
        $location->save();

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
     * Add/Remove To/From Pickup List
     * @param integer $id
     * @return string
     */
    public function pickup($id){
        $location = LocateUs::findOrFail($id);
        $location->in_pickup_list = !$location->in_pickup_list;
        $location->save();
        return redirect()->route('admin.locate_us.index')
            ->with('success', 'Location Status Successfully updated');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $location = LocateUs::findOrFail($id);

        return view('pages.locate_us.edit', compact('location'))
            ->with('content_header', 'Update Location');
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
        $location = LocateUs::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'locate_us';
        $fileName = null;
        if($location->image != null) {
            $fileName = $location->image;
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
                if($location->image != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'locate_us' . DIRECTORY_SEPARATOR . $location->image;
                    File::delete($filePath);
                }
            }
        }
        $location->update([
            'image' => $fileName,
            'latitude' => empty($request->latitude) ? 0 : trim($request->latitude),
            'longitude' => empty($request->longitude) ? 0 : trim($request->longitude),
            'title' => empty($request->name) ? null : trim($request->name),
            'address' => empty($request->description) ? null : trim($request->description)
        ]);
        return redirect()->route('admin.locate_us.index')
            ->with('success', 'Successfully updated the Location');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = LocateUs::findOrFail($id);
        if($location->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'locate_us' . DIRECTORY_SEPARATOR . $location->image;
            File::delete($filePath);
        }
        $location->delete();

        return redirect()->route('admin.locate_us.index')
            ->with('success', 'Successfully deleted a Location');
    }
}

