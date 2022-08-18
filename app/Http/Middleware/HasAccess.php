<?php

namespace App\Http\Middleware;

use App;
use Config;
use Closure;
use Route;

class HasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->user()) {
            /**
             * get all operations foreach section
             */
            $role = auth()->user()->role;
            if($role){
                $accessible_routes = [];
                $accessible_routes[] = 'admin.medias.view_media';
//        photos.index
//        photos.create
//        photos.store
//        photos.show
//        photos.edit
//        photos.update
//        photos.destroy
                $current_route_name = Route::currentRouteName();
                foreach ($role->sections as $access){
                    $section_name = $access->section->name;
                    foreach ($access->operations_list as $operation){
                        $route = null;
                        switch ($operation) {
                            case "view":
                                if($section_name == 'orders'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.items.index';
                                } 
                                if($section_name == 'profile'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.index';
                                }
                                if($section_name == 'items'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.show';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu_categories';
                                }

                                if($section_name == 'supplier_items'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.show';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu_categories';
                                }
                                if($section_name == 'notification'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.push';
                                }
                                if($section_name == 'restaurants'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.collections';
                                }
                                if($section_name == 'suppliers'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories';
                                }
                                $accessible_routes[] = 'admin.'.$section_name.'.index';
                                break;
                            case "add":
                                $accessible_routes[] = 'admin.'.$section_name.'.create';
                                $accessible_routes[] = 'admin.'.$section_name.'.store';
                                break;
                            case "edit":
                                if($section_name == 'items'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.customizations.publish';
                                }
                                if($section_name == 'supplier_items'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_customizations.publish';
                                }
                                if($section_name == 'customizations'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.options.publish';
                                }
                                if($section_name == 'supplier_customizations'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_options.publish';
                                }
                                if($section_name == 'sub_categories'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.supplier_sub_categories.publish';
                                }
                                if($section_name == 'categories'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.delete_single';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.delete_single';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.sub_categories.destroy';
                                }
                                if($section_name == 'restaurants'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.timing.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.timing.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.timing.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.timing.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.timing.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.timing.destroy';

                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.event_types.publish';

                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.publish';
                                }
                                if($section_name == 'suppliers'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.index';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.create';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.store';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.edit';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.update';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.destroy';
                                    $accessible_routes[] = 'admin.'.$section_name.'.menu.publish';
                                }
                                if(in_array($section_name, ['cms_users','tags','top_cravings','services','items','news','offers','banners','user_rating','restaurants','suppliers'])){
                                    $accessible_routes[] = 'admin.'.$section_name.'.delete_single';
                                }
                                if(in_array($section_name, ['restaurants','suppliers'])){
                                    $accessible_routes[] = 'admin.'.$section_name.'.clone';
                                }
                                if(in_array($section_name, ['cms_users','restaurants','suppliers','users'])){
                                    $accessible_routes[] = 'admin.'.$section_name.'.reset';
                                    $accessible_routes[] = 'admin.'.$section_name.'.reset_password';
                                }
                                if($section_name == 'settings'){
                                    $accessible_routes[] = 'admin.app_settings';
                                    $accessible_routes[] = 'admin.'.$section_name.'.changepassword';
                                }
                                if($section_name == 'about_us'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.set';
                                    $accessible_routes[] = 'admin.'.$section_name.'.delete';
                                }
                                if($section_name == 'terms'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.set';
                                    $accessible_routes[] = 'admin.'.$section_name.'.delete';
                                }
                                $accessible_routes[] = 'admin.'.$section_name.'.edit';
                                $accessible_routes[] = 'admin.'.$section_name.'.update';
                                break;
                            case "delete":
                                $accessible_routes[] = 'admin.'.$section_name.'.destroy';
                                break;
                            case "publish":
                                if($section_name == 'orders'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.status';
                                    $accessible_routes[] = 'admin.'.$section_name.'.invoice';
                                    $accessible_routes[] = 'admin.'.$section_name.'.print_invoice';
                                }
                                if($section_name == 'users'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.block';
                                }
                                if($section_name == 'items'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.approve';
                                }
                                if(in_array($section_name, ['banners','offers','news'])){
                                    $accessible_routes[] = 'admin.'.$section_name.'.public';
                                    $accessible_routes[] = 'admin.'.$section_name.'.private';
                                    $accessible_routes[] = 'admin.'.$section_name.'.both';
                                }
                                if($section_name == 'cms_users'){
                                    $accessible_routes[] = 'admin.'.$section_name.'.lock';
                                }
                                $accessible_routes[] = 'admin.'.$section_name.'.publish';
                                break;
                            case "export":
                                $accessible_routes[] = 'admin.'.$section_name.'.export';
                                if($section_name == 'items'){
                                    $accessible_routes[] = 'admin.export_items';
                                    $accessible_routes[] = 'admin.import_items';
                                }
                                if($section_name == 'items'){
                                    $accessible_routes[] = 'admin.export_supplier_items';
                                    $accessible_routes[] = 'admin.import_supplier_items';
                                }
                                break;
                            case "bulk":
                                $accessible_routes[] = 'admin.'.$section_name.'.send_push';
                                break;
                            case "targeted":
                                $accessible_routes[] = 'admin.'.$section_name.'.send_single_push';
                                break;
                            default:
                                $accessible_routes[] = 'admin.'.$section_name.'.index';
                        }
                    }
                }
                if($current_route_name != null && !in_array($current_route_name,$accessible_routes)){
                    abort(401);
                }
            }

        }


        return $next($request);
    }
}
