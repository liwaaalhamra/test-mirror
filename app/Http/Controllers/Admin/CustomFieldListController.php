<?php

namespace App\Http\Controllers\Admin;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use App\Http\Controllers\Controller;
    use App\Models\Api\Country;
    use App\Models\Api\Region;
    use Illuminate\View\View;
    use App\Models\Api\CustomFieldList;
    use Validator;

class CustomFieldListController extends Controller
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
        $items = CustomFieldList::orderBy('field_name','asc')->get()->unique('field_name');
            return view('pages.lists.index', compact('items'))
            ->with('section_name', 'lists')
            ->with('content_header', 'Lists');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $refs = CustomFieldList::where('parent_id',null)->get()->unique('field_name');
        return view('pages.lists.create',compact('refs'))
            ->with('content_header', 'Add List');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if(!$request->field_name && !$request->field_name_list)
        {
            return redirect()->back()
            ->with('success', 'Field Name is required');
        }
        $fieldName = null;
        if($request->field_name){
            $fieldName = str_replace(' ', '_', $request->field_name);
        }
        $country = new CustomFieldList();
        $country->field_name = $fieldName ?? $request->field_name_list;
        $country->parent_id = $request->parent_id ?? null;
        $country->name_en = $request->name_en;
        $country->name_ar = $request->name_ar;
        $country->save();

        return redirect()->route('admin.lists.index')
            ->with('success', 'List Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        try{
        $item = CustomFieldList::findOrFail($id);
        $items = CustomFieldList::where('field_name',$item->field_name)->orderBy('field_name','asc')->get();
        $refs = CustomFieldList::latest()->get()->unique('field_name');

        return view('pages.lists.edit', compact('item','items','refs'))
        ->with('content_header', 'Update list');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.lists.index')->with('success', 'List deleted');
        }
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
        if(!$request->field_name)
        {
            return redirect()->back()
            ->with('success', 'Field Name is required');
        }
        foreach($request->id as $key => $data)
        {
            $fieldName = str_replace(' ', '_', $request->field_name[$key]);
            $country = CustomFieldList::findOrFail($data);
            $country->field_name = $fieldName ?? $request->field_name_list;
            $country->parent_id = $request->parent_id[$key];
            $country->name_en = $request->name_en[$key];
            $country->name_ar = $request->name_ar[$key];
            $country->save();
        }

        return redirect()->route('admin.lists.index')
            ->with('success', 'List Successfully Updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = CustomFieldList::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully'
        ]);
    }

}
