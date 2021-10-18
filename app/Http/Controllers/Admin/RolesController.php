<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\Area;
use App\Models\Api\BonusSubject;
use App\Models\Api\Client;
use App\Models\Api\Country;
use App\Models\Api\EventType;
use App\Models\Api\Feature;
use App\Models\Api\Game;
use App\Models\Api\HappyLikelyList;
use App\Models\Api\Industry;
use App\Models\Api\MenuSection;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\ProductType;
use App\Models\Api\Region;
use App\Models\Api\Report;
use App\Models\Api\Role;
use App\Models\Api\RoleAccess;
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
 * Class RolesController
 */
class RolesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        $this->sections = implode(',',$this->sections);
//        if(auth()->user() == null || (strpos($this->sections,'all') === false) && strpos($this->sections,'users') === false){
//            abort(401);
//        }
    }
    /**
     * Show .
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = Role::get();
        return view('pages.roles.index', compact('items'))
            ->with('section_name', 'roles')
            ->with('content_header', 'Roles');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $menu_sections = MenuSection::whereIsVisible(1)->orderBy('menu_order','asc')->get();
        $operations = [
            ['meta_name' => 'View','meta_value' => 'view','info' => 'Show the Section in Side Menu'],
            ['meta_name' => 'Add','meta_value' => 'add','info' => 'Create New Restaurant'],
            ['meta_name' => 'Edit','meta_value' => 'edit','info' => 'Update Existing Restaurant'],
            ['meta_name' => 'Delete','meta_value' => 'delete','info' => 'Delete Existing Restaurant'],
            ['meta_name' => 'Publish','meta_value' => 'publish','info' => 'All Actions related to publish item: Add to dialogFlow,Sync...'],
            ['meta_name' => 'Export','meta_value' => 'export','info' => 'Export Data to Excel,CSV,...'],
            ['meta_name' => 'Bulk Push','meta_value' => 'bulk','info' => 'Send Bulk Notification'],
            ['meta_name' => 'Targeted Push','meta_value' => 'targeted','info' => 'Send Targeted Notification'],
        ];
        return view('pages.roles.create',compact('menu_sections','operations'))
            ->with('content_header', 'Add Role');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {
        $access = array_filter($request->all(), function($key) use ($request) {
            return strpos($key, '_') !== false && !empty($request->{$key}) && $key != '_token';
        }, ARRAY_FILTER_USE_KEY);

        $role = Role::create([
            'name' => $request->get('name'),
        ]);
        $sections = [];
        foreach ($access as $key => $section_access){
            $section_details = explode('_', $key);
            if(!isset($sections["$section_details[0]"])){
                $sections["$section_details[0]"] = $section_details[1];
            }else{
                $sections["$section_details[0]"] = $sections["$section_details[0]"].','.$section_details[1];
            }
        }
        foreach ($sections as $section_id => $operations){
            RoleAccess::create([
                'role_id' => $role->id,
                'section_id' => $section_id,
                'operations' => $operations,
            ]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = Role::findOrFail($id);
        $menu_sections = MenuSection::whereIsVisible(1)->orderBy('menu_order','asc')->get();
        $operations = [
            ['meta_name' => 'View','meta_value' => 'view','info' => 'Show the Section in Side Menu'],
            ['meta_name' => 'Add','meta_value' => 'add','info' => 'Create New Restaurant'],
            ['meta_name' => 'Edit','meta_value' => 'edit','info' => 'Update Existing Restaurant'],
            ['meta_name' => 'Delete','meta_value' => 'delete','info' => 'Delete Existing Restaurant'],
            ['meta_name' => 'Publish','meta_value' => 'publish','info' => 'All Actions related to publish item: Add to dialogFlow,Sync...'],
            ['meta_name' => 'Export','meta_value' => 'export','info' => 'Export Data to Excel,CSV,...'],
            ['meta_name' => 'Bulk Push','meta_value' => 'bulk','info' => 'Send Bulk Notification'],
            ['meta_name' => 'Targeted Push','meta_value' => 'targeted','info' => 'Send Targeted Notification'],
        ];
        return view('pages.roles.edit', compact('item','menu_sections','operations'))
            ->with('content_header', 'Update Role');
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
        $item = Role::findOrFail($id);
        $access = array_filter($request->all(), function($key) use ($request) {
            return strpos($key, '_') !== false && !empty($request->{$key}) && $key != '_token' && $key != '_method';
        }, ARRAY_FILTER_USE_KEY);
        $item->update([
            'name' => $request->get('name'),
        ]);
        $sections = [];
        foreach ($access as $key => $section_access){
            $section_details = explode('_', $key);
            if(!isset($sections["$section_details[0]"])){
                $sections["$section_details[0]"] = $section_details[1];
            }else{
                $sections["$section_details[0]"] = $sections["$section_details[0]"].','.$section_details[1];
            }
        }
        RoleAccess::whereRoleId($item->id)->delete();
        foreach ($sections as $section_id => $operations){
            RoleAccess::create([
                'role_id' => $item->id,
                'section_id' => $section_id,
                'operations' => $operations,
            ]);
        }
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Role::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role Successfully deleted');
    }

}

