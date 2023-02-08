<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Models\Api\Item;

use Validator;

class MatchingController extends Controller
{
    /**
     * Get matching results
     * @param Request $request
     * @return \stdClass
     */
    public function index(Request $request) {
        
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }        
        
        $my_posts = Item::whereType('offer')->whereUserId($user->id)->orderBy('updated_at', 'desc')->get();
        $others_posts = Item::whereType('offer')->whereNotIn('user_id', $user->id)->orderBy('updated_at', 'desc')->get();
        
        dd($my_posts->first(), $others_posts->first());
        foreach($my_posts as $my_post)
        {
            foreach($others_posts as $others_post)
            {
                echo $my_post . '<br/>';
                echo $others_post . '<br/>';
                echo '<br/><br/>';
            }
        }

        return response()->json('');
    }

    function returnCondition($post_1, $return_post_1, $post_2, $return_post_2){
        $catMatch = $this->compareCategory($post_1, $return_post_2);
        $subCatMatch = $this->compareSubCategory($post_1, $return_post_2);
        $subSubSubCatMatch = $this->compareSubSubCategory($post_1, $return_post_2);
        $locationMatch = $this->compareLocation($post_1, $return_post_2);
        $priceMatch = $this->comparePrice($post_1, $return_post_2);
        $priceRentMatch = $this->compareRentPrice($post_1, $return_post_2);

        $secondCatMatch = $this->compareCategory($return_post_1, $post_2);
        $secondSubCatMatch = $this->compareSubCategory($return_post_1, $post_2);
        $secondSubSubSubCatMatch = $this->compareSubSubCategory($return_post_1, $post_2);
        $secondLocationMatch = $this->compareLocation($return_post_1, $post_2);
        $secondPriceMatch = $this->comparePrice($return_post_1, $post_2);

        $firstMatch = $catMatch && $subCatMatch && $subSubSubCatMatch && $locationMatch && $priceMatch;
        $secondMatch = $secondCatMatch && $secondSubCatMatch && $secondSubSubSubCatMatch && $secondLocationMatch && $secondPriceMatch;
        $firstRentMatch = $catMatch && $subCatMatch && $subSubSubCatMatch && $locationMatch && $priceRentMatch;

        // if($firstMatch && $secondMatch){
        //     $priority = 1;
        // }
        
        // if($firstRentMatch && $secondMatch){
        //     $label = 2;
        // }

        // if($catMatch && $subCatMatch && !$subSubSubCatMatch && $locationMatch && $priceMatch){
        //     $label = "1.a";
        // }
    }

    function compareCategory($post_1, $post_2){
        $response = false;
        if($post_1->category_id == $post_2->category_id){
            $response = true;
        }
    
        return $response;
    }

    function compareSubCategory($post_1, $post_2){
        $response = false;
        if($post_1->sub_category_id == $post_2->sub_category_id){
            $response = true;
        }
    
        return $response;
    }

    function compareSubSubCategory($post_1, $post_2){
        $response = false;
        if($post_1->sub_sub_category_id == $post_2->sub_sub_category_id){
            $response = true;
        }
    
        return $response;
    }

    function compareLocation($post_1, $post_2){
        $response = false;
        if($post_1->location_id == $post_2->location_id){
            $response = true;
        }
    
        return $response;
    }

    function comparePrice($post_1, $post_2){
        $response = false;
        if($post_1->price > $post_2->price_from && $post_1->price < $post_2->price_to){
            $response = true;
        }
    
        return $response;
    }

    function compareRentPrice($post_1, $post_2){
        $response = false;
        if($post_1->price > $post_2->price_from*24 && $post_1->price < $post_2->price_to*24){
            $response = true;
        }
    
        return $response;
    }

}
