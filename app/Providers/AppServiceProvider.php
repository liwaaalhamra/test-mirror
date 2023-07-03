<?php

namespace App\Providers;

use App\Models\Api\Users;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('complex', function ($attribute, $value, $parameters) {
            return preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[_#?!@$%^&*]).{8,}$/', $value);
        }, 'Password must be a combination of at least eight numbers, letters, upper case, and symbols (like @ & $)');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
            error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
        }
    }
}
