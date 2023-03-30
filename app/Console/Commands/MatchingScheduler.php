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

        $users = Item::get()->pluck('user_id','user_id');
        // UserMatchingRecord::where('id', '>', 0)->delete();
        Log::info('cron');

        foreach($users as $user){
            $result = $this->matchingResult($user);

            $set_data = new UserMatchingRecord();
            $set_data->user_id = $user;
            $set_data->matching_records = json_encode($result[0]);
            $set_data->save();
        }

    }

    public function matchingResult($user)
    {
        $my_posts = Item::whereUserId($user)->whereType('offer')->get();
        $others_posts = Item::where('user_id', '!=', $user)->whereType('offer')->get();
        foreach ($my_posts as $my_post) {
            $result = $this->returnResult($my_post, $others_posts);
        }
        if (!$result) {
            return null;
        }
        return $result;
    }
    
    function returnResult($post_1, $post_2)
    {
        $matches = [];

        $priority_1 = $this->priority_1($post_1, $post_2);
        if($priority_1 != null){
            $result = $priority_1;
        }else{
            $result = [];
        }
        

        $priority_2 = $this->priority_2($post_1, $post_2);
        if($priority_2 != null){
            $result0 = array_merge($result ,$priority_2);
        }else{
            $result0 = array_merge($result ,[]);
        }


        $priority_3 = $this->priority_3($post_1, $post_2);
        if($priority_3 != null){
            $result1 = array_merge($result0 ,$priority_3);
        }else{
            $result1 = array_merge($result0 ,[]);
        }

        
        $priority_4 = $this->priority_4($post_1, $post_2);
        if($priority_4 != null){
            $result2 = array_merge($result1 ,$priority_4);
        }else{
            $result2 = array_merge($result1 ,[]);
        }


        $priority_5 = $this->priority_5($post_1, $post_2);
        if($priority_5 != null){
            $result3 = array_merge($result2 ,$priority_5);
        }else{
            $result3 = array_merge($result2 ,[]);
        }

        
        $priority_6 = $this->priority_6($post_1, $post_2);
        if($priority_6 != null){
            $result4 = array_merge($result3 ,$priority_6);
        }else{
            $result4 = array_merge($result3 ,[]);
        }

        
        $priority_7 = $this->priority_7($post_1, $post_2);
        if($priority_7 != null){
            $result5 = array_merge($result4 ,$priority_7);
        }else{
            $result5 = array_merge($result4 ,[]);
        }
        

        $priority_8 = $this->priority_8($post_1, $post_2);
        if($priority_8 != null){
            $result6 = array_merge($result5 ,$priority_8);
        }else{
            $result6 = array_merge($result5 ,[]);
        }
        

        $priority_9 = $this->priority_9($post_1, $post_2);
        if($priority_9 != null){
            $result7 = array_merge($result6 ,$priority_9);
        }else{
            $result7 = array_merge($result6 ,[]);
        }
        

        $priority_10 = $this->priority_10($post_1, $post_2);
        if($priority_10 != null){
            $result8 = array_merge($result7 ,$priority_10);
        }else{
            $result8 = array_merge($result7 ,[]);
        }
        

        $priority_11 = $this->priority_11($post_1, $post_2);
        if($priority_11 != null){
            $result9 = array_merge($result8 ,$priority_11);
        }else{
            $result9 = array_merge($result8 ,[]);
        }
        

        $priority_12 = $this->priority_12($post_1, $post_2);
        if($priority_12 != null){
            $result10 = array_merge($result9 ,$priority_12);
        }else{
            $result10 = array_merge($result9 ,[]);
        }
        

        $priority_13 = $this->priority_13($post_1, $post_2);
        if($priority_13 != null){
            $result11 = array_merge($result10 ,$priority_13);
        }else{
            $result11 = array_merge($result10 ,[]);
        }
        

        $priority_14 = $this->priority_14($post_1, $post_2);
        if($priority_14 != null){
            $result12 = array_merge($result11 ,$priority_14);
        }else{
            $result12 = array_merge($result11 ,[]);
        }
        

        $priority_15 = $this->priority_15($post_1, $post_2);
        if($priority_15 != null){
            $result13 = array_merge($result12 ,$priority_15);
        }else{
            $result13 = array_merge($result12 ,[]);
        }
        

        $priority_16 = $this->priority_16($post_1, $post_2);
        if($priority_16 != null){
            $result14 = array_merge($result13 ,$priority_16);
        }else{
            $result14 = array_merge($result13 ,[]);
        }
        

        $priority_17 = $this->priority_17($post_1, $post_2);
        if($priority_17 != null){
            $result15 = array_merge($result14 ,$priority_17);
        }else{
            $result15 = array_merge($result14 ,[]);
        }
        

        $priority_18 = $this->priority_18($post_1, $post_2);
        if($priority_18 != null){
            $result16 = array_merge($result15 ,$priority_18);
        }else{
            $result16 = array_merge($result15 ,[]);
        }
        

        $priority_19 = $this->priority_19($post_1, $post_2);
        if($priority_19 != null){
            $result17 = array_merge($result16 ,$priority_19);
        }else{
            $result17 = array_merge($result16 ,[]);
        }
        

        $priority_20 = $this->priority_20($post_1, $post_2);
        if($priority_20 != null){
            $result18 = array_merge($result17 ,$priority_20);
        }else{
            $result18 = array_merge($result17 ,[]);
        }
        

        $priority_21 = $this->priority_21($post_1, $post_2);
        if($priority_21 != null){
            $result19 = array_merge($result18 ,$priority_21);
        }else{
            $result19 = array_merge($result18 ,[]);
        }
        

        $priority_22 = $this->priority_22($post_1, $post_2);
        if($priority_22 != null){
            $result20 = array_merge($result19 ,$priority_22);
        }else{
            $result20 = array_merge($result19 ,[]);
        }
        

        $priority_23 = $this->priority_23($post_1, $post_2);
        if($priority_23 != null){
            $result21 = array_merge($result20 ,$priority_23);
        }else{
            $result21 = array_merge($result20 ,[]);
        }
        

        $priority_24 = $this->priority_24($post_1, $post_2);
        if($priority_24 != null){
            $result22 = array_merge($result21 ,$priority_24);
        }else{
            $result22 = array_merge($result21 ,[]);
        }
        

        $priority_25 = $this->priority_25($post_1, $post_2);
        if($priority_25 != null){
            $result23 = array_merge($result22 ,$priority_25);
        }else{
            $result23 = array_merge($result22 ,[]);
        }
        

        $priority_26 = $this->priority_26($post_1, $post_2);
        if($priority_26 != null){
            $result24 = array_merge($result23 ,$priority_26);
        }else{
            $result24 = array_merge($result23 ,[]);
        }
        

        $priority_27 = $this->priority_27($post_1, $post_2);
        if($priority_27 != null){
            $result25 = array_merge($result24 ,$priority_27);
        }else{
            $result25 = array_merge($result24 ,[]);
        }
        

        $priority_28 = $this->priority_28($post_1, $post_2);
        if($priority_28 != null){
            $result26 = array_merge($result25 ,$priority_28);
        }else{
            $result26 = array_merge($result25 ,[]);
        }
        
        $priority_29 = $this->priority_29($post_1, $post_2);
        if($priority_29 != null){
            $result27 = array_merge($result26 ,$priority_29);
        }else{
            $result27 = array_merge($result26 ,[]);
        }
        

        $priority_30 = $this->priority_30($post_1, $post_2);
        if($priority_30 != null){
            $result28 = array_merge($result27 ,$priority_30);
        }else{
            $result28 = array_merge($result27 ,[]);
        }
        

        $priority_31 = $this->priority_31($post_1, $post_2);
        if($priority_31 != null){
            $result29 = array_merge($result28 ,$priority_31);
        }else{
            $result29 = array_merge($result28 ,[]);
        }
        

        $priority_32 = $this->priority_32($post_1, $post_2);
        if($priority_32 != null){
            $result30 = array_merge($result29 ,$priority_32);
        }else{
            $result30 = array_merge($result29 ,[]);
        }
        

        $priority_33 = $this->priority_33($post_1, $post_2);
        if($priority_33 != null){
            $result31 = array_merge($result30 ,$priority_33);
        }else{
            $result31 = array_merge($result30 ,[]);
        }
        

        $priority_34 = $this->priority_34($post_1, $post_2);
        if($priority_34 != null){
            $result32 = array_merge($result31 ,$priority_34);
        }else{
            $result32 = array_merge($result31 ,[]);
        }
        

        $priority_35 = $this->priority_35($post_1, $post_2);
        if($priority_35 != null){
            $result33 = array_merge($result32 ,$priority_35);
        }else{
            $result33 = array_merge($result32 ,[]);
        }
        

        $priority_36 = $this->priority_36($post_1, $post_2);
        if($priority_36 != null){
            $result34 = array_merge($result33 ,$priority_36);
        }else{
            $result34 = array_merge($result33 ,[]);
        }
        

        $priority_37 = $this->priority_37($post_1, $post_2);
        if($priority_37 != null){
            $result35 = array_merge($result34 ,$priority_37);
        }else{
            $result35 = array_merge($result34 ,[]);
        }
        

        $priority_38 = $this->priority_38($post_1, $post_2);
        if($priority_38 != null){
            $result36 = array_merge($result35 ,$priority_38);
        }else{
            $result36 = array_merge($result35 ,[]);
        }
        

        $priority_39 = $this->priority_39($post_1, $post_2);
        if($priority_39 != null){
            $result37 = array_merge($result36 ,$priority_39);
        }else{
            $result37 = array_merge($result36 ,[]);
        }
        

        $priority_40 = $this->priority_40($post_1, $post_2);
        if($priority_40 != null){
            $result38 = array_merge($result37 ,$priority_40);
        }else{
            $result38 = array_merge($result37 ,[]);
        }
          

        $final_result = array_unique($result38);
        array_push($matches, $final_result);

        return $matches;
    }

    function priority_1($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
       return array_values($filteredResponce);
    }
    function priority_2($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_3($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_4($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_5($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_6($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_7($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_8($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_9($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_10($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if ($item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
            && $item['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
            && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        
        return array_values($filteredResponce);
    }
    function priority_11($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_12($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_13($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_14($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_15($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_16($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_17($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_18($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_19($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_20($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_21($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_22($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_23($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_24($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_25($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_26($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_27($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_28($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_29($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_30($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_31($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] == $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_32($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] == $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_33($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_34($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_35($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] == $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_36($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_37($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] == $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] != $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_38($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post['category_id'] != $post_1['category_id'] && $item->return_post['sub_category_id'] != $post_1['sub_category_id'] && $item->return_post['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item->return_post['location_id'] == $post_1['location_id'] && $item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']
                && $item['category_id'] != $post_1->return_post['category_id'] && $item['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item['location_id'] != $post_1->return_post['location_id']
                && $item['price_from'] <= $post_1->return_post['price'] && $item['price_to'] >= $post_1->return_post['price']
            ) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_39($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if ($item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
    function priority_40($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if ($item->return_post['price_from'] <= $post_1['price'] && $item->return_post['price_to'] >= $post_1['price']) {
                return $item->id;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        return array_values($filteredResponce);
    }
}