<?php

namespace App\Providers;

use App\Services\Cmms\Adapters\FakeCmmsAdapter;
use App\Services\Cmms\Contracts\CmmsAdapterInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CmmsAdapterInterface::class, FakeCmmsAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
