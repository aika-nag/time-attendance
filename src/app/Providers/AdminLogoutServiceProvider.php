<?php

namespace App\Providers;

use App\Models\Admin;
use App\Http\Controllers\AdminController;
use App\Responses\AdminLogoutResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Support\ServiceProvider;

class AdminLogoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
         $this->app->when(AdminController::class)
            ->needs(LogoutResponse::class)
            ->give(AdminLogoutResponse::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
