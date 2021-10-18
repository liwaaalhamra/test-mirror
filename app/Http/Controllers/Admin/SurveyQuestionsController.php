<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\CosmeticCategory;

use App\Models\Api\Game;
use App\Models\Api\GameCompetitor;
use App\Models\Api\Inbox;
use App\Models\Api\Restaurant;

use App\Models\Api\Quiz;
use App\Models\Api\QuizQuestion;
use App\Models\Api\Survey;
use App\Models\Api\SurveyQuestion;
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
 * Class SurveyQuestionsController
 */
class SurveyQuestionsController extends Controller
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
        $category = Survey::findOrFail($id);
        $items = SurveyQuestion::whereSurveyId($id)->get();
        return view('pages.surveys.questions.index', compact('category','items'))
            ->with('section_name', 'surveys')
            ->with('content_header', 'Questions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = Survey::findOrFail($id);
        if($category->questions()->count() >= 1){
            return redirect()->back()->with('error_msg', 'Each survey can have only 1 question');
        }
        return view('pages.surveys.questions.create',compact('category'))
            ->with('content_header', 'Add Question');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $survey = Survey::findOrFail($id);
        $category = SurveyQuestion::create([
            'question' => $request->question,
            'type' => 'single',//$request->type,
            'survey_id' => $id,

        ]);

        /**
         * redirect to create answer page if type not free text
         */
        if($request->type == 'free_text'){
            return redirect()->route('admin.surveys.questions.index',$id)
                ->with('success', 'Successfully create the Question');
        }else{
            return view('pages.surveys.questions.answers.create',compact('category'))
                ->with('content_header', 'Add Answer');
        }
    }

    public function publish($id){
        $question = SurveyQuestion::find($id);
        if($question){
            $question->is_published = !$question->is_published;
            $question->save();
        }
        return redirect()->route('admin.surveys.questions.index',$question->survey_id)
            ->with('success', 'Successfully updated the status of Question');
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
        $item = SurveyQuestion::findOrFail($item_id);
        $category = Survey::findOrFail($category_id);
        return view('pages.surveys.questions.edit', compact('item','category'))
            ->with('content_header', 'Update Question');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$category_id,$item_id)
    {
        $item = SurveyQuestion::findOrFail($item_id);
        $item->update([
            'question' => $request->question,
            'type' => 'single',//$request->type,
        ]);

        return redirect()->route('admin.surveys.questions.index',$category_id)
            ->with('success', 'Successfully updated the Question');
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
        $item = SurveyQuestion::findOrFail($item_id);
        $item->delete();

        return redirect()->route('admin.surveys.questions.index',$category_id)
            ->with('success', 'Successfully deleted the Question');
    }
}

