<?php

namespace App\Providers;

use App\Models\FoodLog;
use App\Policies\FoodLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        FoodLog::class => FoodLogPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
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
    
}
