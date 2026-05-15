<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
    public function boot(): void
    {
        Gate::define('admin', function (User $user, $class, $roles) {
            if ($user->superuser) {
                return true;
            }
            // Verifica si el usuario tiene alguno de los roles permitidos en Aimeos
            foreach ((array) $roles as $role) {
                if ($user->hasAimeosGroup($role)) {
                    return true;
                }
            }
            return false;
        });
    }
}
