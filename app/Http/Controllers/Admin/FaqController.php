<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\FAQ;
use App\Models\Api\Game;
use App\Models\Api\News;
use App\Models\Api\Obituary;
use App\Models\Api\Player;
use App\Models\Api\PublicHoliday;
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

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class FaqController
 */
class FaqController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqs = FAQ::orderBy('item_order','asc')->get();

        return view('pages.faqs.index', compact('faqs'))
            ->with('section_name', 'faq')
            ->with('content_header', 'FAQs');
    }

    public function publish($id){
        $item = FAQ::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.faq.index')
            ->with('success', 'Status Successfully updated');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.faqs.create')
            ->with('content_header', 'Add FAQ Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $max_order = DB::table('faq')->max('item_order');
        $order = $max_order + 1;
        FAQ::create([
            'item_order' => $order,
            'question_en' => empty($request->question_en) ? null : trim($request->question_en),
            'question_ar' => empty($request->question_ar) ? null : trim($request->question_ar),
            'answer_en' => empty($request->answer_en) ? null : trim($request->answer_en),
            'answer_ar' => empty($request->answer_ar) ? null : trim($request->answer_ar),
        ]);

        return redirect()->route('admin.faq.index')
            ->with('success', 'Successfully create a new FAQ Restaurant');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $faq = FAQ::findOrFail($id);
        return view('pages.faqs.edit', compact('faq'))
            ->with('content_header', 'Update FAQ Restaurant');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $faq = FAQ::findOrFail($id);

        $faq->update([
            'question_en' => empty($request->question_en) ? null : trim($request->question_en),
            'question_ar' => empty($request->question_ar) ? null : trim($request->question_ar),
            'answer_en' => empty($request->answer_en) ? null : trim($request->answer_en),
            'answer_ar' => empty($request->answer_ar) ? null : trim($request->answer_ar),
            'item_order' => empty($request->item_order) ? 1 : trim($request->item_order),
        ]);
        return redirect()->route('admin.faq.index')
            ->with('success', 'Successfully updated the FAQ Restaurant');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = FAQ::findOrFail($id);
        $all_items = FAQ::where('item_order','>',$faq->item_order)->get();
        foreach ($all_items as $single_item){
            $single_item->item_order = $single_item->item_order - 1;
            $single_item->save();
        }
        $faq->delete();

        return redirect()->route('admin.faq.index')
            ->with('success', 'Successfully deleted the FAQ Restaurant');
    }
}

