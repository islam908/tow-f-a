<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMerchantRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isMerchant()) {
            abort(403, 'هذه الصفحة مخصصة للتاجر فقط.');
        }

        if (! $user->is_active) {
            abort(403, 'حساب التاجر غير نشط حاليًا.');
        }

        if ($user->subscription_end && $user->subscription_end->isPast()) {
            abort(403, 'انتهت صلاحية الاشتراك.');
        }

        return $next($request);
    }
}
