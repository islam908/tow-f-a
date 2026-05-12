<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('otp-public', function (Request $request) {
            $token = (string) ($request->route('token') ?? $request->input('token', 'unknown'));
            $key = $token.'|'.$request->ip();

            return Limit::perMinute(8)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'عدد طلبات رمز التحقق كبير جدًا. حاول مرة أخرى بعد دقيقة.',
                    ], 429, $headers);
                });
        });
    }
}
