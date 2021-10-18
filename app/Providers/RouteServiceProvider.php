<?php

namespace App\Providers;

use App\Models\Api\Boutique;
use App\Models\Api\CorporateActivityItem;
use App\Models\Api\Item;
use App\Models\Api\Restaurant;
use App\Models\Api\Section;
use App\Models\Api\SubCategory;
use App\Models\Api\TipsItem;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The namespace for the Admin routes.
     *
     * @var string
     */
    protected $adminNamespace = 'App\Http\Controllers\Admin';

    /**
     * The namespace for the Api routes.
     *
     * @var string
     */
    protected $apiNamespace = 'App\Http\Controllers\Api';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
        $router->model('item_id', Item::class, function($value){
            return Item::findOrFail($value);
        });
//        $router->model('tips_item_id', Restaurant::class, function($value){
//            return TipsItem::findOrFail($value);
//        });
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        // Default routes that ships with laravel.
        $this->mapWebRoutes($router);

        // Admin specific routes.
        $this->mapAdminWebRoutes($router);

        // API specific routes.
        $this->mapApiRoutes($router);

//        $router->group(['namespace' => $this->namespace], function ($router) {
//            require app_path('Http/routes.php');
//        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
        ], function ($router) {
            require app_path('Http/routes.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapApiRoutes($router)
    {
        $router->group([
            'namespace' => $this->apiNamespace,
            'prefix' => 'api'
        ], function ($router) {
            require app_path('Http/Routes/api.php');
        });
    }

    /**
     * Define the "admin" routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapAdminWebRoutes($router)
    {
        $router->group([
            'namespace' => $this->adminNamespace,
            'middleware' => ['web', 'auth','has_access'],
            'prefix' => 'admin'
        ], function ($router) {
            require app_path('Http/Routes/admin.php');
        });
    }
}
