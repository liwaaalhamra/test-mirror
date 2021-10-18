<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PollRequest;
use App\Models\Api\CosmeticCategory;

use App\Models\Api\Game;
use App\Models\Api\GameCompetitor;
use App\Models\Api\Inbox;
use App\Models\Api\Restaurant;

use App\Models\Api\Poll;
use App\Models\Api\PollOption;
use App\Models\Api\Quiz;
use App\Models\Api\QuizQuestion;
use App\Models\Api\TypeBrand;
use App\Models\Api\UserGame;
use App\Models\Api\UserPush;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;


/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class PollOptionsController
 */
class PollOptionsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $category = Poll::findOrFail($id);
        $items = PollOption::wherePollId($id)->get();
        return view('pages.polls.options.index', compact('category','items'))
            ->with('section_name', 'polls')
            ->with('content_header', 'Options');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = Poll::findOrFail($id);
        if($category->options()->count() >= 2){
            return redirect()->back()->with('error_msg', 'Each poll has 2 options only');
        }
        return view('pages.polls.options.create',compact('category'))
            ->with('content_header', 'Add Option');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $poll = Poll::findOrFail($id);
        $category = PollOption::create([
            'name' => trim($request->name),
            'poll_id' => $id,
        ]);

        return redirect()->route('admin.polls.options.index',$id)
            ->with('success', 'Successfully create the Option');
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
     * @param  int  $category_id
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function edit($category_id,$item_id)
    {
        $item = PollOption::findOrFail($item_id);
        $category = Poll::findOrFail($category_id);
        return view('pages.polls.options.edit', compact('item','category'))
            ->with('content_header', 'Update Option');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PollRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(PollRequest $request,$category_id,$item_id)
    {
        $item = PollOption::findOrFail($item_id);
        $item->update([
            'name' => trim($request->name),
        ]);

        return redirect()->route('admin.polls.options.index',$category_id)
            ->with('success', 'Successfully updated the Option');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $category_id
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($category_id,$item_id)
    {
        $item = PollOption::findOrFail($item_id);
        $item->delete();

        return redirect()->route('admin.polls.options.index',$category_id)
            ->with('success', 'Successfully deleted the Option');
    }
}

