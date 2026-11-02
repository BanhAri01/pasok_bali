<?php

namespace App\Providers;

use App\Ai\AiGateway;
use App\Services\ProductCatalog;
use App\Support\CurrentBusiness;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CurrentBusiness::class);
        $this->app->scoped(ProductCatalog::class);
        $this->app->singleton(AiGateway::class);
    }

    public function boot(): void
    {
        Date::use(CarbonImmutable::class);

        if (is_file((string) config('pasok.ca_bundle'))) {
            Http::globalOptions(['verify' => config('pasok.ca_bundle')]);
        }

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
            URL::forceRootUrl((string) config('app.url'));
        }
    }
}
