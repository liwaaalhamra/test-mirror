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
use App\Models\Api\Poll;
use App\Models\Api\PollOption;
use App\Models\Api\Quiz;
use App\Models\Api\Report;
use App\Models\Api\Store;
use App\Models\Api\Suggestion;
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
 * Class PollsController
 */
class PollsController extends Controller
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
        $items = Poll::get();
        return view('pages.polls.index', compact('items'))
            ->with('section_name', 'polls')
            ->with('content_header', 'Polls');
    }

    public function publish($id){
        $item = Poll::find($id);
        if($item){
            if($item->is_published){
                $item->is_published = 0;
            }else{
                DB::table('polls')->update(array('is_published' => 0));
                $item->is_published = 1;
            }
            $item->save();
        }
        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll activated successfully');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        return view('pages.polls.create')
            ->with('content_header', 'Add New Poll');
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
        $category = Poll::create([
            'question' => $request->get('question'),
            'points' => $request->get('points'),
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
        ]);
        PollOption::create([
            'name' => 'YES',
            'poll_id' => $category->id,
        ]);
        PollOption::create([
            'name' => 'NO',
            'poll_id' => $category->id,
        ]);
        return redirect()->route('admin.polls.index')
            ->with('success', 'Successfully create a new Poll');
        /**
         * redirect to option create page
         */
//        return view('pages.polls.options.create',compact('category'))
//            ->with('content_header', 'Add Option');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = Poll::findOrFail($id);
        return view('pages.polls.edit', compact('item'))
            ->with('content_header', 'Update Poll');
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
        $item = Poll::findOrFail($id);
        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }
        $item->update([
            'question' => $request->get('question'),
            'points' => $request->get('points'),
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
        ]);
        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Poll::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll Successfully deleted');
    }

}

