<?php

namespace App\Providers;
use App\Models\Loket;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('layout.navbar_admin', function ($view) {
            $loket = null;

            if (Session::has('id_loket')) {
                $loket = Loket::find(Session::get('id_loket'));
            }

            $view->with('loket', $loket);
        });
    }
}