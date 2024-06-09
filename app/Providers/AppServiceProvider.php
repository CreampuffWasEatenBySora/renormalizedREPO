<?php

namespace App\Providers;


use Illuminate\Support\Facades\Auth;
use App\Models\barangay_residents;
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

            $adminID= DB::table('barangay_residents')->select('id')->where('UUID', '=', Auth::user()->UUID)->first();
            $notifs= DB::table('notifications')->where('for_user_id', '=', $adminID->id)->get();
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
