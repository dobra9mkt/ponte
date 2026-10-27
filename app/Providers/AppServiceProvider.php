<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Fortify::ignoreRoutes();
    }

    public function boot(): void
    {
        $producao = $this->app->isProduction();

        Model::shouldBeStrict(! $producao);
        DB::prohibitDestructiveCommands($producao);

        if ($producao) {
            URL::forceHttps();
        }

        Password::defaults(fn () => Password::min(10)->uncompromised());

        RateLimiter::for('senha', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('convite', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('uploads', fn (Request $request) => Limit::perMinute(10)->by((string) $request->user()?->id));
        RateLimiter::for('geral', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
    }
}
