<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\CosmeticCategory;

use App\Models\Api\Game;
use App\Models\Api\GameCompetitor;
use App\Models\Api\Inbox;
use App\Models\Api\Restaurant;

use App\Models\Api\Quiz;
use App\Models\Api\QuizAnswer;
use App\Models\Api\QuizQuestion;
use App\Models\Api\SurveyAnswer;
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
 * Class SurveyAnswersController
 */
class SurveyAnswersController extends Controller
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
        $category = SurveyQuestion::findOrFail($id);
        $items = SurveyAnswer::whereSurveyQuestionId($id)->get();
        return view('pages.surveys.questions.answers.index', compact('category','items'))
            ->with('section_name', 'surveys')
            ->with('content_header', 'Answers');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = SurveyQuestion::findOrFail($id);
        return view('pages.surveys.questions.answers.create',compact('category'))
            ->with('content_header', 'Add Answer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $category = SurveyQuestion::findOrFail($id);
        $item = SurveyAnswer::create([
            'answer' => $request->answer,
            'survey_question_id' => $id,
        ]);

        return redirect()->route('admin.survey_questions.answers.index',$id)
            ->with('success', 'Successfully create the Answer');
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
        $item = SurveyAnswer::findOrFail($item_id);
        $category = SurveyQuestion::findOrFail($category_id);
        return view('pages.surveys.questions.answers.edit', compact('item','category'))
            ->with('content_header', 'Update Answer');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$category_id,$item_id)
    {
        $item = SurveyAnswer::findOrFail($item_id);
        $item->update([
            'answer' => $request->answer,
        ]);

        return redirect()->route('admin.survey_questions.answers.index',$category_id)
            ->with('success', 'Successfully updated the Answer');
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
        $item = SurveyAnswer::findOrFail($item_id);
        $item->delete();

        return redirect()->route('admin.survey_questions.answers.index',$category_id)
            ->with('success', 'Successfully deleted the Answer');
    }
}

