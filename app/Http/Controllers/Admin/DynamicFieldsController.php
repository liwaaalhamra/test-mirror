<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\BonusSubject;
use App\Models\Api\Brand;
use App\Models\Api\Category;
use App\Models\Api\Client;
use App\Models\Api\ContactUsCategory;
use App\Models\Api\CustomField;
use App\Models\Api\CustomFieldList;
use App\Models\Api\EventType;
use App\Models\Api\Game;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\Report;
use App\Models\Api\Store;
use App\Models\Api\SubCategory;
use App\Models\Api\SubSubCategory;
use App\Models\Api\Suggestion;
use App\Models\Api\TalkToUsCategory;
use App\Models\Api\Terms;
use App\Models\Api\TypeBrand;
use Carbon\Carbon;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class DynamicFieldsController
 */
class DynamicFieldsController extends Controller
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
        $items = CustomField::get()->map(function ($value, $key){
            $value->field_name = strtolower(str_slug($value->field_name));
            $value->extra_field_name = strtolower(str_slug($value->extra_field_name));
            $value->save();
            return $value;
        });
        return view('pages.dynamic_fields.index', compact('items'))
            ->with('section_name', 'dynamic_fields')
            ->with('content_header', 'Dynamic Fields');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $categories = Category::get();
        $sub_categories = SubCategory::get();
        $sub_sub_categories = SubSubCategory::get();
        $field_types = [
            'boolean' => 'True/False',
            'list' => 'Dropdown List',
            'integer' => 'Number',
            'double' => 'Number with Decimals',
            'text' => 'Free Text'
        ];
        $endpoints = [
            'Conditions' => 'lists/conditions',
            'Fuel Types' => 'lists/fuel_types',
            'Body Types' => 'lists/body_types',
            'Transmission Types' => 'lists/transmission_types',
            'Door Types' => 'lists/door_types',
            'Colors' => 'lists/colors',
            'Completion' => 'lists/completion',
            'Bedrooms' => 'lists/bedrooms',
            'Furnished Types' => 'lists/furnished',
            'Landlord/Agent' => 'lists/property_person_types',
            'Bathrooms number' => 'lists/bathrooms',
            'Amenities' => 'lists/amenities',
            'Home Brands' => 'lists/home_brands',
            'Ages' => 'lists/ages',
            'Electronics Brands' => 'lists/electronics_brands',
            'Delivery Time Interval' => 'lists/delivery_time',
            'Online/Face to Face' => 'lists/communication_types',
            'Programming Languages' => 'lists/programming_languages',
            'Session Duration Interval' => 'lists/session_durations',
            'Service Duration Interval' => 'lists/service_durations',
            'Delivery Types' => 'lists/delivery_types',
            'Online/Events' => 'lists/dj_types',
            'Quantities Interval up to 20' => 'lists/quantities1',
            'Quantities Interval up to 10' => 'lists/quantities2',
            'Meal Types' => 'lists/meal_types',
            'Meal Delivery Types' => 'lists/meal_delivery_types',
            'Car Brands' => 'lists/car_brands',
            'Car Models' => 'lists/car_models',
        ];
        $field_names = [
            'Sell & Rent' => 'is-sell',
        ];
        return view('pages.dynamic_fields.create',compact('sub_sub_categories','field_types','sub_categories','categories','endpoints', 'field_names'))
            ->with('content_header', 'Add New Dynamic Field');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {
        foreach ($request->parent_ids as $parent_id){
            $parent_details = explode('_', $parent_id);
            $category_id = $parent_details[0];
            $sub_category_id = $parent_details[1];
            $field_name = $request->field_name;
            $field_type = $request->field_type;
            $field_title = $request->field_title;
            $field_endpoint = isset($request->field_endpoint) && !empty($request->field_endpoint) && $field_type == 'list' ? $request->field_endpoint : null ;
            $in_return = false;
            if(isset($request->in_return) && $request->in_return == 'on'){
                $in_return = true;
            }
            $in_filters = false;
            if(isset($request->in_filters) && $request->in_filters == 'on'){
                $in_filters = true;
            }
            $has_extra_field = false;
            if(isset($request->has_extra_field) && $request->has_extra_field == 'on'){
                $has_extra_field = true;
            }
            $extra_field_name = null;
            $extra_field_type = null;
            $extra_field_title = null;
            $extra_field_endpoint = null;
            if($has_extra_field){
                $extra_field_name = $request->extra_field_name;
                $extra_field_type = $request->extra_field_type;
                $extra_field_title = $request->extra_field_title;
                $extra_field_endpoint = isset($request->extra_field_endpoint) && !empty($request->extra_field_endpoint) && $extra_field_type == 'list' ? $request->extra_field_endpoint : null ;
            }
            $item_order = CustomField::whereCategoryId($category_id)->whereSubCategoryId($sub_category_id)->max('item_order');
            CustomField::updateOrCreate([
                    'category_id' => $category_id,
                    'sub_category_id' => $sub_category_id,
                    'field_name' => $field_name,
                ]
                ,[
                'item_order' => $item_order + 1,
                'in_return' => $in_return,
                'in_filters' => $in_filters,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
                'sub_sub_category_id' => null,
                'field_name' => $field_name,
                'field_type' => $field_type,
                'field_title' => $field_title,
                'field_endpoint' => $field_endpoint,
                'has_extra_field' => $has_extra_field,
                'extra_field_name' => $extra_field_name,
                'extra_field_type' => $extra_field_type,
                'extra_field_title' => $extra_field_title,
                'extra_field_endpoint' => $extra_field_endpoint,
            ]);
        }

        return redirect()->route('admin.dynamic_fields.index')
            ->with('success', 'Dynamic Field Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = CustomField::findOrFail($id);
        $item_parent_id = $item->category_id."_".$item->sub_category_id;
        $categories = Category::get();
        $sub_categories = SubCategory::get();
        $sub_sub_categories = SubSubCategory::get();
        $field_types = [
            'boolean' => 'True/False',
            'list' => 'Dropdown List',
            'integer' => 'Number',
            'double' => 'Number with Decimals',
            'text' => 'Free Text'
        ];
        $endpoints = [
            'Conditions' => 'lists/conditions',
            'Fuel Types' => 'lists/fuel_types',
            'Body Types' => 'lists/body_types',
            'Transmission Types' => 'lists/transmission_types',
            'Door Types' => 'lists/door_types',
            'Colors' => 'lists/colors',
            'Completion' => 'lists/completion',
            'Bedrooms' => 'lists/bedrooms',
            'Furnished Types' => 'lists/furnished',
            'Landlord/Agent' => 'lists/property_person_types',
            'Bathrooms number' => 'lists/bathrooms',
            'Amenities' => 'lists/amenities',
            'Home Brands' => 'lists/home_brands',
            'Ages' => 'lists/ages',
            'Electronics Brands' => 'lists/electronics_brands',
            'Delivery Time Interval' => 'lists/delivery_time',
            'Online/Face to Face' => 'lists/communication_types',
            'Programming Languages' => 'lists/programming_languages',
            'Session Duration Interval' => 'lists/session_durations',
            'Service Duration Interval' => 'lists/service_durations',
            'Delivery Types' => 'lists/delivery_types',
            'Online/Events' => 'lists/dj_types',
            'Quantities Interval up to 20' => 'lists/quantities1',
            'Quantities Interval up to 10' => 'lists/quantities2',
            'Meal Types' => 'lists/meal_types',
            'Meal Delivery Types' => 'lists/meal_delivery_types',
            'Car Brands' => 'lists/car_brands',
            'Car Models' => 'lists/car_models',
        ];
        $field_names = [
            'Sell & Rent' => 'is-sell',
        ];
        return view('pages.dynamic_fields.edit', compact('item','endpoints','categories','sub_categories','sub_sub_categories','field_types','item_parent_id', 'field_names'))
            ->with('content_header', 'Update Dynamic Field');
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
        $item = CustomField::findOrFail($id);
        $parent_id = $request->parent_id;
        $parent_details = explode('_', $parent_id);
        $category_id = $parent_details[0];
        $sub_category_id = $parent_details[1];
        $field_name = $request->field_name;
        $field_type = $request->field_type;
        $field_title = $request->field_title;
        $field_endpoint = isset($request->field_endpoint) && !empty($request->field_endpoint) && $field_type == 'list' ? $request->field_endpoint : null ;
        $in_return = false;
        if(isset($request->in_return) && $request->in_return == 'on'){
            $in_return = true;
        }
        $in_filters = false;
        if(isset($request->in_filters) && $request->in_filters == 'on'){
            $in_filters = true;
        }
        $has_extra_field = false;
        if(isset($request->has_extra_field) && $request->has_extra_field == 'on'){
            $has_extra_field = true;
        }
        $extra_field_name = null;
        $extra_field_type = null;
        $extra_field_title = null;
        $extra_field_endpoint = null;
        if($has_extra_field){
            $extra_field_name = $request->extra_field_name;
            $extra_field_type = $request->extra_field_type;
            $extra_field_title = $request->extra_field_title;
            $extra_field_endpoint = isset($request->extra_field_endpoint) && !empty($request->extra_field_endpoint) && $extra_field_type == 'list' ? $request->extra_field_endpoint : null ;
        }
        $item->update([
                'in_return' => $in_return,
                'in_filters' => $in_filters,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
                'sub_sub_category_id' => null,
                'field_name' => $field_name,
                'field_type' => $field_type,
                'field_title' => $field_title,
                'field_endpoint' => $field_endpoint,
                'has_extra_field' => $has_extra_field,
                'extra_field_name' => $extra_field_name,
                'extra_field_type' => $extra_field_type,
                'extra_field_title' => $extra_field_title,
                'extra_field_endpoint' => $extra_field_endpoint,
            ]);
        return redirect()->route('admin.dynamic_fields.index')
            ->with('success', 'Dynamic Field Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = CustomField::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.dynamic_fields.index')
            ->with('success', 'Dynamic Field Successfully deleted');
    }

}

