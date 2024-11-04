<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Api\Country;
use App\Models\Api\Region;
use Illuminate\View\View;
use Validator;



class CountryController extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    /**
     * Show .
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = Country::get();
        return view('pages.countries.index', compact('items'))
            ->with('section_name', 'countries')
            ->with('content_header', 'Countries');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $countries = Country::get();
        return view('pages.countries.create',compact('countries'))
            ->with('content_header', 'Add Country');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'flag' => 'required',
            'name' => 'required',
            'full_name' => 'required',
            'visibility' => 'required',
            'capital' => 'required',
            'country_code' => 'required',
        ]);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images/flags';

        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('flag')) {
            if($request->file('flag')->isValid()) {
                $file = $request->file('flag');
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
                    'flag' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }

                $fileName = $request->country_code . '.' . $file->getClientOriginalExtension();

                $file->move($destinationDir, $fileName);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            }
        }
        $maxId = Country::max('id');

        $country = new Country();
        $country->id                = (int) $maxId + 1;
        $country->flag              = $fileName;
        $country->name              = $request->name;
        $country->full_name         = $request->full_name;
        $country->visibility        = $request->visibility;
        $country->capital           = $request->capital;
        $country->country_code      = $request->country_code;
        $country->region_code       = $request->region_code;
        $country->sub_region_code   = $request->sub_region_code;
        $country->calling_code      = $request->calling_code;
        $country->currency          = $request->currency;
        $country->currency_code     = $request->currency_code;
        $country->currency_sub_unit = $request->currency_sub_unit;
        $country->currency_symbol   = $request->currency_symbol;
        $country->currency_decimals = $request->currency_decimals;
        $country->save();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Country Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = Country::findOrFail($id);
        return view('pages.countries.edit', compact('item'))
            ->with('content_header', 'Update Country');
    }

    /**
     * Update.
     *
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function update($id, Request $request) /*: RedirectResponse*/
    {
        $this->validate($request, [
            'name' => 'required',
            'full_name' => 'required',
            'visibility' => 'required',
            'capital' => 'required',
            'country_code' => 'required',
        ]);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images/flags';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('flag')) {
            if($request->file('flag')->isValid()) {
                $file = $request->file('flag');
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
                    'flag' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $fileName = $request->country_code . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            }
        }

        $country = new Country();
        $country->flag              = $fileName ?? $country->flag;
        $country->name              = $request->name;
        $country->full_name         = $request->full_name;
        $country->visibility        = $request->visibility;
        $country->capital           = $request->capital;
        $country->country_code      = $request->country_code;
        $country->region_code       = $request->region_code;
        $country->sub_region_code   = $request->sub_region_code;
        $country->calling_code      = $request->calling_code;
        $country->currency          = $request->currency;
        $country->currency_code     = $request->currency_code;
        $country->currency_sub_unit = $request->currency_sub_unit;
        $country->currency_symbol   = $request->currency_symbol;
        $country->currency_decimals = $request->currency_decimals;
        $country->save();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Country Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Country::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Country Successfully deleted');
    }

}
