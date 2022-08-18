<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\BonusSubject;
use App\Models\Api\Client;
use App\Models\Api\EventType;
use App\Models\Api\FillTheFormDepartment;
use App\Models\Api\Game;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\Quiz;
use App\Models\Api\Report;
use App\Models\Api\Store;
use App\Models\Api\Suggestion;
use App\Models\Api\Survey;
use App\Models\Api\Terms;
use App\Models\Api\TypeBrand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class SurveysController
 */
class SurveysController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * Show .
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = Survey::get();
        return view('pages.surveys.index', compact('items'))
            ->with('section_name', 'surveys')
            ->with('content_header', 'Survey');
    }

    public function publish($id){
        $item = Survey::find($id);
        if($item){
            if($item->is_published){
                $item->is_published = 0;
            }else{
                DB::table('surveys')->update(array('is_published' => 0));
                $item->is_published = 1;
            }
            $item->save();
        }
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey activated successfully');
    }

    public function manage_public($id){
        $item = Survey::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_private($id){
        $item = Survey::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_both($id){
        $item = Survey::find($id);
        if($item){
            $item->target = 'both';
            $item->save();
        }
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        return view('pages.surveys.create')
            ->with('content_header', 'Add New Survey');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {
        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }

        $category = Survey::create([
            'name' => $request->get('name'),
            'intro_space' => empty($request->intro_space) ? null : trim($request->intro_space),
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
        ]);
        /**
         * redirect to question create page
         */
        return view('pages.surveys.questions.create',compact('category'))
            ->with('content_header', 'Add Question');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = Survey::findOrFail($id);
        return view('pages.surveys.edit', compact('item'))
            ->with('content_header', 'Update Survey');
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
        $item = Survey::findOrFail($id);

        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }
        $item->update([
            'name' => $request->get('name'),
            'intro_space' => empty($request->intro_space) ? null : trim($request->intro_space),
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
        ]);
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Survey::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey Successfully deleted');
    }

}

