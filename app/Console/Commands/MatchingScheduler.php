<?php
    
namespace App\Console\Commands;

use App\Models\Api\UserMatchingRecord;
use App\Models\Api\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
    
class MatchingScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:cron';
     
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Matching Products';
     
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
     
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // \Log::info("Cron is working fine!");
      
        /*
           Write your database logic we bellow:
           Item::create(['name'=>'hello new']);
        */

        $users = Item::where('id','>',0)->get()->pluck('user_id','user_id');
        // UserMatchingRecord::where('id', '>', 0)->delete();
        Log::info("cron start");
        foreach($users as $user){
            $result = $this->matchingResult($user);
            $set_data = new UserMatchingRecord();
            $set_data->user_id = $user;
            $set_data->matching_records = json_encode($result);
            $set_data->save();
        }
        Log::info("cron end");
    }

    public function matchingResult($user)
    {
        $matching_ids = [];
        $merged_array = [];
        $p1 = [];
        $p2 = [];
        $p3 = [];
        $p4 = [];
        $p5 = [];
        $p6 = [];
        $p7 = [];
        $p8 = [];
        $p9 = [];
        $p10 = [];
        $p11 = [];
        $p12 = [];
        $p13 = [];
        $p14 = [];
        $p15 = [];
        $p16 = [];
        $p17 = [];
        $p18 = [];
        $p19 = [];
        $p20 = [];
        $p21 = [];
        $p22 = [];
        $p23 = [];
        $p24 = [];
        $p25 = [];
        $p26 = [];
        $p27 = [];
        $p28 = [];
        $p29 = [];
        $p30 = [];
        $p31 = [];
        $p32 = [];
        $p33 = [];
        $p34 = [];
        $p35 = [];
        $p36 = [];
        $p37 = [];
        $p38 = [];
        $p39 = [];
        $p40 = [];
        $p41 = [];
        $p42 = [];
        $p43 = [];
        $p44 = [];
        $p45 = [];
        $p46 = [];
        $p47 = [];
        $p48 = [];
        $matches = collect();



        $my_posts = Item::whereUserId($user)->whereType('offer')->get();
        foreach ($my_posts as $my_post) {
            // Labels of priorities section 9
            $label_9 = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.sub_category_id', '=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price],
                ['return_items.price_to', '>=', $my_post->price],
            ];
            $label_9_b = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.sub_category_id', '=', $my_post->sub_category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price],
                ['return_items.price_to', '>=', $my_post->price],
            ];
            $label_9_c = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price],
                ['return_items.price_to', '>=', $my_post->price],
            ];
            $label_9_e = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.price_from', '<=', $my_post->price],
                ['return_items.price_to', '>=', $my_post->price],
            ];
            $label_9_d = [
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price],
                ['return_items.price_to', '>=', $my_post->price],
            ];
            $label_9_f = [
                ['return_items.price_from', '<=', $my_post->price],
                ['return_items.price_to', '>=', $my_post->price],
            ];

            // Labels of priorities section 10
            $label_10 = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from']],
                ['items.price', '<=', $my_post->return_post['price_to']],
            ];
            $label_10_b = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '=', $my_post->return_post['sub_category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from']],
                ['items.price', '<=', $my_post->return_post['price_to']],
            ];
            $label_10_c = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from']],
                ['items.price', '<=', $my_post->return_post['price_to']],
            ];
            $label_10_e = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.price', '>=', $my_post->return_post['price_from']],
                ['items.price', '<=', $my_post->return_post['price_to']],
            ];
            $label_10_d = [
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from']],
                ['items.price', '<=', $my_post->return_post['price_to']],
            ];
            $label_10_f = [
                ['items.price', '>=', $my_post->return_post['price_from']],
                ['items.price', '<=', $my_post->return_post['price_to']],
            ];
            
        /////////////////////////////////////////////////////////
       // DOUBLE MATCH : P1 => P9 , P16 => P31 , P36 => P46   //
      // SINGLE MATCH : P10 => P15 , P32 => P35 , P47 => P48 //
     /////////////////////////////////////////////////////////

            // PRIORITY1   
            $matching_posts_p1 = Item::where('items.user_id', '!=', $user)
            ->where($label_10)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p1->isEmpty()){
                foreach ($matching_posts_p1 as $matching_post_p1) {
                    $value1 = [$matching_post_p1->id => 'priority_1'];
                    array_push($p1, $value1);
                }
                $matches->push(...$matching_posts_p1);
            }

            //  PRIORITY2
            $matching_posts_p2 = Item::where('items.user_id', '!=', $user)
            ->where($label_10)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_b)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p2->isEmpty()){
                foreach ($matching_posts_p2 as $matching_post_p2) {
                $value2 = [$matching_post_p2->id => 'priority_2'];
                    array_push($p2, $value2);
                }
                $matches->push(...$matching_posts_p2);
            }
            
            // PRIORITY3
            $matching_posts_p3 = Item::where('items.user_id', '!=', $user)
            ->where($label_10)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_c)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p3->isEmpty()){
                foreach ($matching_posts_p3 as $matching_post_p3) {
                $value3 = [$matching_post_p3->id => 'priority_3'];
                    array_push($p3, $value3);
                }
                $matches->push(...$matching_posts_p3);
            }

            // PRIORITY4
            $matching_posts_p4 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_b)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p4->isEmpty()){
                foreach ($matching_posts_p4 as $matching_post_p4) {
                $value4 = [$matching_post_p4->id => 'priority_4'];
                    array_push($p4, $value4);
                }
                $matches->push(...$matching_posts_p4);
            }

            // PRIORITY5
            $matching_posts_p5 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_c)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p5->isEmpty()){
                foreach ($matching_posts_p5 as $matching_post_p5) {
                $value5 = [$matching_post_p5->id => 'priority_5'];
                    array_push($p5, $value5);
                }
                $matches->push(...$matching_posts_p5);
            }

            // PRIORITY6
            $matching_posts_p6 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_b)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_b)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p6->isEmpty()){
                foreach ($matching_posts_p6 as $matching_post_p6) {
                $value6 = [$matching_post_p6->id => 'priority_6'];
                array_push($p6, $value6);
                }
                $matches->push(...$matching_posts_p6);
            }

            // PRIORITY7
            $matching_posts_p7 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_b)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_c)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p7->isEmpty()){
                foreach ($matching_posts_p7 as $matching_post_p7) {
                $value7 = [$matching_post_p7->id => 'priority_7'];
                    array_push($p7, $value7);
                }
                $matches->push(...$matching_posts_p7);
            }

            // PRIORITY8
            $matching_posts_p8 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_c)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_b)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p8->isEmpty()){
                foreach ($matching_posts_p8 as $matching_post_p8) {
                $value8 = [$matching_post_p8->id => 'priority_8'];
                array_push($p8, $value8);
                }
                $matches->push(...$matching_posts_p8);
            }

            // PRIORITY9
            $matching_posts_p9 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_c)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_c)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p9->isEmpty()){
                foreach ($matching_posts_p9 as $matching_post_p9) {
                $value9 = [$matching_post_p9->id => 'priority_9'];
                array_push($p9, $value9);
                }
                $matches->push(...$matching_posts_p9);
            }

            // PRIORITY10
            $matching_posts_p10 = Item::where('user_id', '!=', $user)->where('type', 'offer')->where([
                ['category_id', '=', $my_post->return_post['category_id']],
                ['sub_category_id', '=', $my_post->return_post['sub_category_id']],
                ['sub_sub_category_id', '=', $my_post->return_post['sub_sub_category_id']],
                ['location_id', '=', $my_post->return_post['location_id']],
                ['price', '>=', $my_post->return_post['price_from']],
                ['price', '<=', $my_post->return_post['price_to']],
            ])->orderBy('created_at','desc')->get();
            if(!$matching_posts_p10->isEmpty()){
                foreach ($matching_posts_p10 as $matching_post_p10) {
                $value10 = [$matching_post_p10->id => 'priority_10'];
                array_push($p10, $value10);
                }
                $matches->push(...$matching_posts_p10);
            }

            // PRIORITY11
            $data_p11 = Item::where('user_id', '!=', $user)->where('type', 'return')->where([
                ['category_id', '=', $my_post->category_id],
                ['sub_category_id', '=', $my_post->sub_category_id],
                ['sub_sub_category_id', '=', $my_post->sub_sub_category_id],
                ['location_id', '=', $my_post->location_id],
                ['price_from', '<=', $my_post->price],
                ['price_to', '>=', $my_post->price],
            ])->orderBy('created_at','desc')->get(['id']);
            $ids_p11 = [];
            foreach($data_p11 as $d_p11){
                $array_p11 = [$d_p11->id - 1];
                array_push($ids_p11,$array_p11);
            }
            $matching_posts_p11 = Item::whereIn('id',$ids_p11)->get();
            if(!$matching_posts_p11->isEmpty()){
                foreach ($matching_posts_p11 as $matching_post_p11) {
                $value11 = [$matching_post_p11->id => 'priority_11'];
                array_push($p11, $value11);
                }
                $matches->push(...$matching_posts_p11);
            }

            // PRIORITY12
            $matching_posts_p12 = Item::where('user_id', '!=', $user)->where('type', 'offer')->where([
                ['category_id', '=', $my_post->return_post['category_id']],
                ['sub_category_id', '=', $my_post->return_post['sub_category_id']],
                ['location_id', '=', $my_post->return_post['location_id']],
                ['price', '>=', $my_post->return_post['price_from']],
                ['price', '<=', $my_post->return_post['price_to']],
            ])->orderBy('created_at','desc')->get();
            if(!$matching_posts_p12->isEmpty()){
                foreach ($matching_posts_p12 as $matching_post_p12) {
                $value12 = [$matching_post_p12->id => 'priority_12'];
                array_push($p12, $value12);
                }
                $matches->push(...$matching_posts_p12);
            }

            // PRIORITY13
            $data_p13 = Item::where('user_id', '!=', $user)->where('type', 'return')->where([
                ['category_id', '=', $my_post->category_id],
                ['sub_category_id', '=', $my_post->sub_category_id],
                ['location_id', '=', $my_post->location_id],
                ['price_from', '<=', $my_post->price],
                ['price_to', '>=', $my_post->price],
            ])->orderBy('created_at','desc')->get();
            $ids_p13 = [];
            foreach($data_p13 as $d_p13){
                $array_p13 = [$d_p13->id - 1];
                array_push($ids_p13,$array_p13);
            }
            $matching_posts_p13 = Item::whereIn('id',$ids_p13)->get();
            if(!$matching_posts_p13->isEmpty()){
                foreach ($matching_posts_p13 as $matching_post_p13) {
                $value13 = [$matching_post_p13->id => 'priority_13'];
                array_push($p13, $value13);
                }
                $matches->push(...$matching_posts_p13);
            }

            // PRIORITY14
            $matching_posts_p14 = Item::where('user_id', '!=', $user)->where('type', 'offer')->where([
                ['category_id', '=', $my_post->return_post['category_id']],
                ['location_id', '=', $my_post->return_post['location_id']],
                ['price', '>=', $my_post->return_post['price_from']],
                ['price', '<=', $my_post->return_post['price_to']],
            ])->orderBy('created_at','desc')->get();
            if(!$matching_posts_p14->isEmpty()){
                foreach ($matching_posts_p14 as $matching_post_p14) {
                $value14 = [$matching_post_p14->id => 'priority_14'];
                array_push($p14, $value14);
                }
                $matches->push(...$matching_posts_p14);
            }

            // PRIORITY15
            $data_p15 = Item::where('user_id', '!=', $user)->where('type', 'return')->where([
                ['category_id', '=', $my_post->category_id],
                ['location_id', '=', $my_post->location_id],
                ['price_from', '<=', $my_post->price],
                ['price_to', '>=', $my_post->price],
            ])->orderBy('created_at','desc')->get();
            $ids_p15 = [];
            foreach($data_p15 as $d_p15){
                $array_p15 = [$d_p15->id - 1];
                array_push($ids_p15,$array_p15);
            }
            $matching_posts_p15 = Item::whereIn('id',$ids_p15)->get();
            if(!$matching_posts_p15->isEmpty()){
                foreach ($matching_posts_p15 as $matching_post_p15) {
                $value15 = [$matching_post_p15->id => 'priority_15'];
                array_push($p15, $value15);
                }
                $matches->push(...$matching_posts_p15);
            }

            // PRIORITY16
            $matching_posts_p16 = Item::where('items.user_id', '!=', $user)
            ->where($label_10)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_e)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p16->isEmpty()){
                foreach ($matching_posts_p16 as $matching_post_p16) {
                $value16 = [$matching_post_p16->id => 'priority_16'];
                    array_push($p16, $value16);
                }
                $matches->push(...$matching_posts_p16);
            }

            // PRIORITY17
            $matching_posts_p17 = Item::where('items.user_id', '!=', $user)
            ->where($label_10)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_d)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p17->isEmpty()){
                foreach ($matching_posts_p17 as $matching_post_p17) {
                $value17 = [$matching_post_p17->id => 'priority_17'];
                array_push($p17, $value17);
                }
                $matches->push(...$matching_posts_p17);
            }

            // PRIORITY18
            $matching_posts_p18 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_b)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_e)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p18->isEmpty()){
                foreach ($matching_posts_p18 as $matching_post_p18) {
                $value18 = [$matching_post_p18->id => 'priority_18'];
                array_push($p18, $value18);
                }
                $matches->push(...$matching_posts_p18);
            }

            // PRIORITY19
            $matching_posts_p19 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_b)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_d)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p19->isEmpty()){
                foreach ($matching_posts_p19 as $matching_post_p19) {
                $value19 = [$matching_post_p19->id => 'priority_19'];
                    array_push($p19, $value19);
                }
                $matches->push(...$matching_posts_p19);
            }

            // PRIORITY20
            $matching_posts_p20 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_c)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_e)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p20->isEmpty()){
                foreach ($matching_posts_p20 as $matching_post_p20) {
                $value20 = [$matching_post_p20->id => 'priority_20'];
                    array_push($p20, $value20);
                }
                $matches->push(...$matching_posts_p20);
            }

            // PRIORITY21
            $matching_posts_p21 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_c)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_d)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p21->isEmpty()){
                foreach ($matching_posts_p21 as $matching_post_p21) {
                $value21 = [$matching_post_p21->id => 'priority_21'];
                    array_push($p21, $value21);
                }
                $matches->push(...$matching_posts_p21);
            }

            // PRIORITY22
            $matching_posts_p22 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_e)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p22->isEmpty()){
                foreach ($matching_posts_p22 as $matching_post_p22) {
                $value22 = [$matching_post_p22->id => 'priority_22'];
                array_push($p22, $value22);
                }
                $matches->push(...$matching_posts_p22);
            }

            // PRIORITY23
            $matching_posts_p23 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_d)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p23->isEmpty()){
                foreach ($matching_posts_p23 as $matching_post_p23) {
                $value23 = [$matching_post_p23->id => 'priority_23'];
                    array_push($p23, $value23);
                }
                $matches->push(...$matching_posts_p23);
            }

            // PRIORITY24
            $matching_posts_p24 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_e)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_b)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p24->isEmpty()){
                foreach ($matching_posts_p24 as $matching_post_p24) {
                $value24 = [$matching_post_p24->id => 'priority_24'];
                    array_push($p24, $value24);
                }
                $matches->push(...$matching_posts_p24);
            }

            // PRIORITY25
            $matching_posts_p25 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_d)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_b)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p25->isEmpty()){
                foreach ($matching_posts_p25 as $matching_post_p25) {
                $value25 = [$matching_post_p25->id => 'priority_25'];
                    array_push($p25, $value25);
                }
                $matches->push(...$matching_posts_p25);
            }

            // PRIORITY26
            $matching_posts_p26 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_e)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_c)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p26->isEmpty()){
                foreach ($matching_posts_p26 as $matching_post_p26) {
                $value26 = [$matching_post_p26->id => 'priority_26'];
                    array_push($p26, $value26);
                }
                $matches->push(...$matching_posts_p26);
            }

            // PRIORITY27
            $matching_posts_p27 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_d)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_c)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p27->isEmpty()){
                foreach ($matching_posts_p27 as $matching_post_p27) {
                $value27 = [$matching_post_p27->id => 'priority_27'];
                    array_push($p27, $value27);
                }
                $matches->push(...$matching_posts_p27);
            }

            // PRIORITY28
            $matching_posts_p28 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_e)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_e)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p28->isEmpty()){
                foreach ($matching_posts_p28 as $matching_post_p28) {
                $value28 = [$matching_post_p28->id => 'priority_28'];
                    array_push($p28, $value28);
                }
                $matches->push(...$matching_posts_p28);
            }
            
            // PRIORITY29
            $matching_posts_p29 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_e)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_d)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p29->isEmpty()){
                foreach ($matching_posts_p29 as $matching_post_p29) {
                $value29 = [$matching_post_p29->id => 'priority_29'];
                    array_push($p29, $value29);
                }
                $matches->push(...$matching_posts_p29);
            }

            // PRIORITY30
            $matching_posts_p30 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_d)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_e)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p30->isEmpty()){
                foreach ($matching_posts_p30 as $matching_post_p30) {
                $value30 = [$matching_post_p30->id => 'priority_30'];
                    array_push($p30, $value30);
                }
                $matches->push(...$matching_posts_p30);
            }

            // PRIORITY31
            $matching_posts_p31 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_d)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_d)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p31->isEmpty()){
                foreach ($matching_posts_p31 as $matching_post_p31) {
                $value31 = [$matching_post_p31->id => 'priority_31'];
                    array_push($p31, $value31);
                }
                $matches->push(...$matching_posts_p31);
            }

            // PRIORITY32
            $matching_posts_p32 = Item::where('user_id', '!=', $user)->where('type', 'offer')->where([
                ['category_id', '=', $my_post->return_post['category_id']],
                ['price', '>=', $my_post->return_post['price_from']],
                ['price', '<=', $my_post->return_post['price_to']],
            ])->orderBy('created_at','desc')->get();
            if(!$matching_posts_p32->isEmpty()){
                foreach ($matching_posts_p32 as $matching_post_p32) {
                $value32 = [$matching_post_p32->id => 'priority_32'];
                    array_push($p32, $value32);
                }
                $matches->push(...$matching_posts_p32);
            }

            // PRIORITY33
            $matching_posts_p33 = Item::where('user_id', '!=', $user)->where('type', 'offer')->where([
                ['location_id', '=', $my_post->return_post['location_id']],
                ['price', '>=', $my_post->return_post['price_from']],
                ['price', '<=', $my_post->return_post['price_to']],
            ])->orderBy('created_at','desc')->get();
            if(!$matching_posts_p33->isEmpty()){
                foreach ($matching_posts_p33 as $matching_post_p33) {
                $value33 = [$matching_post_p33->id => 'priority_33'];
                    array_push($p33, $value33);
                }
                $matches->push(...$matching_posts_p33);
            }

            // PRIORITY34
            $data_p34 = Item::where('user_id', '!=', $user)->where('type', 'return')->where([
                ['category_id', '=', $my_post->category_id],
                ['price_from', '<=', $my_post->price],
                ['price_to', '>=', $my_post->price],
            ])->orderBy('created_at','desc')->get();
            $ids_p34 = [];
            foreach($data_p34 as $d_p34){
                $array_p34 = [$d_p34->id - 1];
                array_push($ids_p34,$array_p34);
            }
            $matching_posts_p34 = Item::whereIn('id',$ids_p34)->get();
            if(!$matching_posts_p34->isEmpty()){
                foreach ($matching_posts_p34 as $matching_post_p34) {
                $value34 = [$matching_post_p34->id => 'priority_34'];
                    array_push($p34, $value34);
                }
                $matches->push(...$matching_posts_p34);
            }

            // PRIORITY35
            $data_p35 = Item::where('user_id', '!=', $user)->where('type', 'return')->where([
                ['location_id', '=', $my_post->location_id],
                ['price_from', '<=', $my_post->price],
                ['price_to', '>=', $my_post->price],
            ])->orderBy('created_at','desc')->get();
            $ids_p35 = [];
            foreach($data_p35 as $d_p35){
                $array_p35 = [$d_p35->id - 1];
                array_push($ids_p35,$array_p35);
            }
            $matching_posts_p35 = Item::whereIn('id',$ids_p35)->get();
            if(!$matching_posts_p35->isEmpty()){
                foreach ($matching_posts_p35 as $matching_post_p35) {
                $value35 = [$matching_post_p35->id => 'priority_35'];
                    array_push($p35, $value35);
                }
                $matches->push(...$matching_posts_p35);
            }

            // PRIORITY36
            $matching_posts_p36 = Item::where('items.user_id', '!=', $user)
            ->where($label_10)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_f)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p36->isEmpty()){
                foreach ($matching_posts_p36 as $matching_post_p36) {
                $value36 = [$matching_post_p36->id => 'priority_36'];
                    array_push($p36, $value36);
                }
                $matches->push(...$matching_posts_p36);
            }

            // PRIORITY37
            $matching_posts_p37 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_b)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_f)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p37->isEmpty()){
                foreach ($matching_posts_p37 as $matching_post_p37) {
                $value37 = [$matching_post_p37->id => 'priority_37'];
                    array_push($p37, $value37);
                }
                $matches->push(...$matching_posts_p37);
            }

            // PRIORITY38
            $matching_posts_p38 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_c)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_f)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p38->isEmpty()){
                foreach ($matching_posts_p38 as $matching_post_p38) {
                $value38 = [$matching_post_p38->id => 'priority_38'];
                    array_push($p38, $value38);
                }
                $matches->push(...$matching_posts_p38);
            }

            // PRIORITY39
            $matching_posts_p39 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_e)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_f)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p39->isEmpty()){
                foreach ($matching_posts_p39 as $matching_post_p39) {
                $value39 = [$matching_post_p39->id => 'priority_39'];
                    array_push($p39, $value39);
                }
                $matches->push(...$matching_posts_p39);
            }

            // PRIORITY40
            $matching_posts_p40 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_d)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_f)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p40->isEmpty()){
                foreach ($matching_posts_p40 as $matching_post_p40) {
                $value40 = [$matching_post_p40->id => 'priority_40'];
                    array_push($p40, $value40);
                }
                $matches->push(...$matching_posts_p40);
            }

            // PRIORITY 41
            $matching_posts_p41 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_f)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_f)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p41->isEmpty()){
                foreach ($matching_posts_p41 as $matching_post_p41) {
                $value41 = [$matching_post_p41->id => 'priority_41'];
                    array_push($p41, $value41);
                }
                $matches->push(...$matching_posts_p41);
            }

            // PRIORITY42
            $matching_posts_p42 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_f)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p42->isEmpty()){
                foreach ($matching_posts_p42 as $matching_post_p42) {
                $value42 = [$matching_post_p42->id => 'priority_42'];
                    array_push($p42, $value42);
                }
                $matches->push(...$matching_posts_p42);
            }

            // PRIORITY43
            $matching_posts_p43 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_f)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_b)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p43->isEmpty()){
                foreach ($matching_posts_p43 as $matching_post_p43) {
                $value43 = [$matching_post_p43->id => 'priority_43'];
                    array_push($p43, $value43);
                }
                $matches->push(...$matching_posts_p43);
            }

            // PRIORITY44
            $matching_posts_p44 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_f)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_c)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p44->isEmpty()){
                foreach ($matching_posts_p44 as $matching_post_p44) {
                $value44 = [$matching_post_p44->id => 'priority_44'];
                    array_push($p44, $value44);
                }
                $matches->push(...$matching_posts_p44);
            }

            // PRIORITY45
            $matching_posts_p45 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_f)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_e)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p45->isEmpty()){
                foreach ($matching_posts_p45 as $matching_post_p45) {
                $value45 = [$matching_post_p45->id => 'priority_45'];
                    array_push($p45, $value45);
                }
                $matches->push(...$matching_posts_p45);
            }
            
            // PRIORITY46
            $matching_posts_p46 = Item::where('items.user_id', '!=', $user)
            ->where($label_10_f)
            ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
            ->where($label_9_d)
            ->orderBy('items.created_at','desc')
            ->get(['items.id']);
            if(!$matching_posts_p46->isEmpty()){
                foreach ($matching_posts_p46 as $matching_post_p46) {
                $value46 = [$matching_post_p46->id => 'priority_46'];
                    array_push($p46, $value46);
                }
                $matches->push(...$matching_posts_p46);
            }

            // PRIORITY47
            $matching_posts_p47 = Item::where('user_id', '!=', $user)->where('type', 'offer')->where([
                ['price', '>=', $my_post->return_post['price_from']],
                ['price', '<=', $my_post->return_post['price_to']],
            ])->orderBy('created_at','desc')->get();
            if(!$matching_posts_p47->isEmpty()){
                foreach ($matching_posts_p47 as $matching_post_p47) {
                $value47 = [$matching_post_p47->id => 'priority_47'];
                    array_push($p47, $value47);
                }
                $matches->push(...$matching_posts_p47);
            }

            // PRIORITY48
            $data_p48 = Item::where('user_id', '!=', $user)->where('type', 'return')->where([
                ['price_from', '<=', $my_post->price],
                ['price_to', '>=', $my_post->price],
            ])->orderBy('created_at','desc')->get();
            $ids_p48 = [];
            foreach($data_p48 as $d_p48){
                $array_p48 = [$d_p48->id - 1];
                array_push($ids_p48,$array_p48);
            }
            $matching_posts_p48 = Item::whereIn('id',$ids_p48)->get();
            if(!$matching_posts_p48->isEmpty()){
                foreach ($matching_posts_p48 as $matching_post_p48) {
                $value48 = [$matching_post_p48->id => 'priority_48'];
                    array_push($p48, $value48);
                }
                $matches->push(...$matching_posts_p48);
            }
        }

        // $merged_array = array_merge($merged_array, array_reverse($p1));
        // $merged_array = array_merge($merged_array, array_reverse($p2));
        // $merged_array = array_merge($merged_array, array_reverse($p3));
        // $merged_array = array_merge($merged_array, array_reverse($p4));
        // $merged_array = array_merge($merged_array, array_reverse($p5));
        // $merged_array = array_merge($merged_array, array_reverse($p6));
        // $merged_array = array_merge($merged_array, array_reverse($p7));
        // $merged_array = array_merge($merged_array, array_reverse($p8));
        // $merged_array = array_merge($merged_array, array_reverse($p9));
        // $merged_array = array_merge($merged_array, array_reverse($p10));
        // $merged_array = array_merge($merged_array, array_reverse($p11));
        // $merged_array = array_merge($merged_array, array_reverse($p12));
        // $merged_array = array_merge($merged_array, array_reverse($p13));
        // $merged_array = array_merge($merged_array, array_reverse($p14));
        // $merged_array = array_merge($merged_array, array_reverse($p15));
        // $merged_array = array_merge($merged_array, array_reverse($p16));
        // $merged_array = array_merge($merged_array, array_reverse($p17));
        // $merged_array = array_merge($merged_array, array_reverse($p18));
        // $merged_array = array_merge($merged_array, array_reverse($p19));
        // $merged_array = array_merge($merged_array, array_reverse($p20));
        // $merged_array = array_merge($merged_array, array_reverse($p21));
        // $merged_array = array_merge($merged_array, array_reverse($p22));
        // $merged_array = array_merge($merged_array, array_reverse($p23));
        // $merged_array = array_merge($merged_array, array_reverse($p24));
        // $merged_array = array_merge($merged_array, array_reverse($p25));
        // $merged_array = array_merge($merged_array, array_reverse($p26));
        // $merged_array = array_merge($merged_array, array_reverse($p27));
        // $merged_array = array_merge($merged_array, array_reverse($p28));
        // $merged_array = array_merge($merged_array, array_reverse($p29));
        // $merged_array = array_merge($merged_array, array_reverse($p30));
        // $merged_array = array_merge($merged_array, array_reverse($p31));
        // $merged_array = array_merge($merged_array, array_reverse($p32));
        // $merged_array = array_merge($merged_array, array_reverse($p33));
        // $merged_array = array_merge($merged_array, array_reverse($p34));
        // $merged_array = array_merge($merged_array, array_reverse($p35));
        // $merged_array = array_merge($merged_array, array_reverse($p36));
        // $merged_array = array_merge($merged_array, array_reverse($p37));
        // $merged_array = array_merge($merged_array, array_reverse($p38));
        // $merged_array = array_merge($merged_array, array_reverse($p39));
        // $merged_array = array_merge($merged_array, array_reverse($p40));
        // $merged_array = array_merge($merged_array, array_reverse($p41));
        // $merged_array = array_merge($merged_array, array_reverse($p42));
        // $merged_array = array_merge($merged_array, array_reverse($p43));
        // $merged_array = array_merge($merged_array, array_reverse($p44));
        // $merged_array = array_merge($merged_array, array_reverse($p45));
        // $merged_array = array_merge($merged_array, array_reverse($p46));
        // $merged_array = array_merge($merged_array, array_reverse($p47));
        // $merged_array = array_merge($merged_array, array_reverse($p48));

        $merged_array = array_merge($merged_array, $p1);
        $merged_array = array_merge($merged_array, $p2);
        $merged_array = array_merge($merged_array, $p3);
        $merged_array = array_merge($merged_array, $p4);
        $merged_array = array_merge($merged_array, $p5);
        $merged_array = array_merge($merged_array, $p6);
        $merged_array = array_merge($merged_array, $p7);
        $merged_array = array_merge($merged_array, $p8);
        $merged_array = array_merge($merged_array, $p9);
        $merged_array = array_merge($merged_array, $p10);
        $merged_array = array_merge($merged_array, $p11);
        $merged_array = array_merge($merged_array, $p12);
        $merged_array = array_merge($merged_array, $p13);
        $merged_array = array_merge($merged_array, $p14);
        $merged_array = array_merge($merged_array, $p15);
        $merged_array = array_merge($merged_array, $p16);
        $merged_array = array_merge($merged_array, $p17);
        $merged_array = array_merge($merged_array, $p18);
        $merged_array = array_merge($merged_array, $p19);
        $merged_array = array_merge($merged_array, $p20);
        $merged_array = array_merge($merged_array, $p21);
        $merged_array = array_merge($merged_array, $p22);
        $merged_array = array_merge($merged_array, $p23);
        $merged_array = array_merge($merged_array, $p24);
        $merged_array = array_merge($merged_array, $p25);
        $merged_array = array_merge($merged_array, $p26);
        $merged_array = array_merge($merged_array, $p27);
        $merged_array = array_merge($merged_array, $p28);
        $merged_array = array_merge($merged_array, $p29);
        $merged_array = array_merge($merged_array, $p30);
        $merged_array = array_merge($merged_array, $p31);
        $merged_array = array_merge($merged_array, $p32);
        $merged_array = array_merge($merged_array, $p33);
        $merged_array = array_merge($merged_array, $p34);
        $merged_array = array_merge($merged_array, $p35);
        $merged_array = array_merge($merged_array, $p36);
        $merged_array = array_merge($merged_array, $p37);
        $merged_array = array_merge($merged_array, $p38);
        $merged_array = array_merge($merged_array, $p39);
        $merged_array = array_merge($merged_array, $p40);
        $merged_array = array_merge($merged_array, $p41);
        $merged_array = array_merge($merged_array, $p42);
        $merged_array = array_merge($merged_array, $p43);
        $merged_array = array_merge($merged_array, $p44);
        $merged_array = array_merge($merged_array, $p45);
        $merged_array = array_merge($merged_array, $p46);
        $merged_array = array_merge($merged_array, $p47);
        $merged_array = array_merge($merged_array, $p48);

        array_push($matching_ids, $merged_array);

        $uniqueKeys = array_map(function ($item) {
            return array_unique(array_keys($item));
        }, $matching_ids[0]);

        $uniqueKeys = array_merge(...$uniqueKeys);

        $uniqueValues = array_unique(array_values($uniqueKeys));
        Log::info($matching_ids);
        // dd($matching_ids);
        return array_values($uniqueValues);
    }
}