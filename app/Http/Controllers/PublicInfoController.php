<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;

class PublicInfoController extends Controller
{
    public function show(string $token): View
    {
        $customer = Customer::query()
            ->with('account')
            ->where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        return view('public.info', [
            'customer' => $customer,
            'account' => $customer->account,
            'remainingUsage' => $customer->remainingUsage(),
            'isExhausted' => $customer->remainingUsage() < 1,
            'otpEndpoint' => route('api.public.otp.generate', ['token' => $customer->token]),
        ]);
    }
}
