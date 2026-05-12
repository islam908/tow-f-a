<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MerchantAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $merchant = User::query()
            ->where('email', $payload['email'])
            ->where('role', User::ROLE_MERCHANT)
            ->first();

        if (! $merchant || ! Hash::check($payload['password'], $merchant->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        if (! $merchant->is_active) {
            return response()->json([
                'message' => 'حساب التاجر غير نشط حاليًا.',
            ], 403);
        }

        if ($merchant->subscription_end && $merchant->subscription_end->isPast()) {
            return response()->json([
                'message' => 'انتهت صلاحية الاشتراك.',
            ], 403);
        }

        $token = $merchant
            ->createToken($payload['device_name'] ?? 'merchant-api')
            ->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token' => $token,
            'token_type' => 'Bearer',
            'merchant' => $merchant,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'merchant' => $request->user(),
        ]);
    }
}
