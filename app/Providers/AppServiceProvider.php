<?php

namespace App\Providers;


use Illuminate\Support\Facades\Auth;
use App\Models\notifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        View::composer('administrator.*', function ($view) {

            $notifs= DB::table('notifications')->where('for_user_id', '=',Auth::user()->id)->get();
            $view->with('notifications', $notifs);
        });
 
    }

    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
