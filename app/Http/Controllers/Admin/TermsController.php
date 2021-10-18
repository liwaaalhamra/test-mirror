<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\SummaryTerms;
use App\Models\Api\Terms;
use File;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class TermsController
 */
class TermsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Show the terms page.
     *
     * @return View
     */
    public function index() /*: View*/
    {
        $terms = null;
        $privacy = null;
        $restaurant_terms = null;
        $restaurant_privacy = null;
        $supplier_terms = null;
        $supplier_privacy = null;
        $all = Terms::get()->map(function ($value, $key) use (&$terms,&$privacy,&$restaurant_privacy,&$restaurant_terms,&$supplier_privacy,&$supplier_terms) {
            $terms = $value->type == 'terms' ? $value : $terms;
            $privacy = $value->type == 'privacy' ? $value : $privacy;
            $restaurant_terms = $value->type == 'restaurant_terms' ? $value : $restaurant_terms;
            $restaurant_privacy = $value->type == 'restaurant_privacy' ? $value : $restaurant_privacy;
            $supplier_terms = $value->type == 'supplier_terms' ? $value : $supplier_terms;
            $supplier_privacy = $value->type == 'supplier_privacy' ? $value : $supplier_privacy;
            return $value;
        })->values();
        return view('pages.terms.index',compact('terms','privacy','restaurant_terms','restaurant_privacy','supplier_terms','supplier_privacy'))
            ->with('section_name', 'terms')
            ->with('content_header', 'Terms & Conditions');
    }

    /**
     * Change Terms.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function set(Request $request)
    {
        $this->validate($request, [
            'description_en' => 'required',
            'description_ar' => 'required',
            'privacy_en' => 'required',
            'privacy_ar' => 'required',
            'supplier_description_en' => 'required',
            'supplier_description_ar' => 'required',
            'supplier_privacy_en' => 'required',
            'supplier_privacy_ar' => 'required',
            'restaurant_description_en' => 'required',
            'restaurant_description_ar' => 'required',
            'restaurant_privacy_en' => 'required',
            'restaurant_privacy_ar' => 'required',
        ]);
//        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'terms';
//        $fileName = null;
//        if($terms){
//            $fileName = $terms->getImageName();
//        }
//        // check if a file exist and is valid.
//        if($request->hasFile('image')) {
//            if($request->file('image')->isValid()) {
//                $file = $request->file('image');
//                try{
//                    $data = file_get_contents($file->getPathname());
//                    $base64 = base64_encode($data);
//                    $bin = base64_decode($base64);
//                    $im = imageCreateFromString($bin);
//                }catch (\Exception $ex){
//                    return redirect()->back()
//                        ->with('error_msg', 'Document Corrupted');
//                }
//                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
//                $validator = Validator::make($request->all(), [
//                    'image' => 'mimetypes:image/jpeg,image/png',
//                ]);
//                if($validator->fails()){
//                    return redirect()->back()
//                        ->with('warning', 'Invalid Media Type');
//                }
//                $file->move($destinationDir, $fileName);
//
//                if($terms && $terms->getImageName() != null) {
//                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'terms' . DIRECTORY_SEPARATOR . $terms->getImageName();
//                    File::delete($filePath);
//                }
//
//            }
//        }
        Terms::updateOrCreate(['type' => 'terms'],[
            'description_en' => isset($request->description_en) && !empty($request->description_en) ? trim($request->description_en) : null,
            'description_ar' => isset($request->description_ar) && !empty($request->description_ar) ? trim($request->description_ar) : null,
        ]);
        Terms::updateOrCreate(['type' => 'privacy'],[
            'description_en' => isset($request->privacy_en) && !empty($request->privacy_en) ? trim($request->privacy_en) : null,
            'description_ar' => isset($request->privacy_ar) && !empty($request->privacy_ar) ? trim($request->privacy_ar) : null,
        ]);
        Terms::updateOrCreate(['type' => 'restaurant_terms'],[
            'description_en' => isset($request->restaurant_description_en) && !empty($request->restaurant_description_en) ? trim($request->restaurant_description_en) : null,
            'description_ar' => isset($request->restaurant_description_ar) && !empty($request->restaurant_description_ar) ? trim($request->restaurant_description_ar) : null,
        ]);
        Terms::updateOrCreate(['type' => 'restaurant_privacy'],[
            'description_en' => isset($request->restaurant_privacy_en) && !empty($request->restaurant_privacy_en) ? trim($request->restaurant_privacy_en) : null,
            'description_ar' => isset($request->restaurant_privacy_ar) && !empty($request->restaurant_privacy_ar) ? trim($request->restaurant_privacy_ar) : null,
        ]);
        Terms::updateOrCreate(['type' => 'supplier_terms'],[
            'description_en' => isset($request->supplier_description_en) && !empty($request->supplier_description_en) ? trim($request->supplier_description_en) : null,
            'description_ar' => isset($request->supplier_description_ar) && !empty($request->supplier_description_ar) ? trim($request->supplier_description_ar) : null,
        ]);
        Terms::updateOrCreate(['type' => 'supplier_privacy'],[
            'description_en' => isset($request->supplier_privacy_en) && !empty($request->supplier_privacy_en) ? trim($request->supplier_privacy_en) : null,
            'description_ar' => isset($request->supplier_privacy_ar) && !empty($request->supplier_privacy_ar) ? trim($request->supplier_privacy_ar) : null,
        ]);

        return redirect()->back()->with('success', 'Successfully updated Terms & Privacy');
    }

    public function destroy()
    {
        $terms = Terms::first();
        if($terms->getImageName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'terms' . DIRECTORY_SEPARATOR . $terms->getImageName();
            File::delete($filePath);
        }
        $terms->image = null;
        $terms->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }
}
