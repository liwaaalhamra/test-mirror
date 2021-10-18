<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\AboutUs;
use App\Models\Api\CollectionCategory;
use App\Models\Api\Item;
use App\Models\Api\Restaurant;
use App\Models\Api\RestaurantTiming;
use App\Models\Api\UserRating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class AboutUsController
 */
class AboutUsController extends Controller
{
    /**
     * AboutUsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the about us page.
     *
     * @return View
     */
    public function index() /*: View*/
    {
        $about_us = AboutUs::first();
        return view('pages.about_us.index',compact('about_us'))
            ->with('section_name', 'about_us')
            ->with('content_header', 'About Us');
    }

    /**
     * Change About Us.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function set(Request $request)
    {
        $this->validate($request, [
            'description_en' => 'required',
//            'description_ar' => 'required',
        ]);
        $about_us = AboutUs::first();
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'about_us';
        $fileName = null;
        if($about_us){
            $fileName = $about_us->image;
        }
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
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $validator = Validator::make($request->all(), [
                    'image-file' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $file->move($destinationDir, $fileName);

                if($about_us && $about_us->image != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'about_us' . DIRECTORY_SEPARATOR . $about_us->image;
                    File::delete($filePath);
                }

            }
        }
        if($about_us){
            $about_us->update([
                'image' => $fileName,
                'description_en' => isset($request->description_en) && !empty($request->description_en) ? trim($request->description_en) : null,
                'description_ar' => isset($request->description_ar) && !empty($request->description_ar) ? trim($request->description_ar) : null,
                'message' => isset($request->message) && !empty($request->message) ? trim($request->message) : null,
                'twitter' => isset($request->twitter) && !empty($request->twitter) ? trim($request->twitter) : null,
                'facebook' => isset($request->facebook) && !empty($request->facebook) ? trim($request->facebook) : null,
                'instagram' => isset($request->instagram) && !empty($request->instagram) ? trim($request->instagram) : null,
                'email' => isset($request->email) && !empty($request->email) ? trim($request->email) : null,
                'website' => isset($request->website) && !empty($request->website) ? trim($request->website) : null,
            ]);
        }else{
            AboutUs::create([
                'image' => $fileName,
                'description_en' => isset($request->description_en) && !empty($request->description_en) ? trim($request->description_en) : null,
                'description_ar' => isset($request->description_ar) && !empty($request->description_ar) ? trim($request->description_ar) : null,
                'message' => isset($request->message) && !empty($request->message) ? trim($request->message) : null,
                'twitter' => isset($request->twitter) && !empty($request->twitter) ? trim($request->twitter) : null,
                'facebook' => isset($request->facebook) && !empty($request->facebook) ? trim($request->facebook) : null,
                'instagram' => isset($request->instagram) && !empty($request->instagram) ? trim($request->instagram) : null,
                'email' => isset($request->email) && !empty($request->email) ? trim($request->email) : null,
                'website' => isset($request->website) && !empty($request->website) ? trim($request->website) : null,
            ]);
        }

        return redirect()->back()->with('success', 'About Us Successfully updated');
    }

    public function destroy()
    {
        $about_us = AboutUs::first();
        if($about_us->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'about_us' . DIRECTORY_SEPARATOR . $about_us->image;
            File::delete($filePath);
        }
        $about_us->image = null;
        $about_us->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }
}
