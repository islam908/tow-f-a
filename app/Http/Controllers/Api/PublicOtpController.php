<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OtpLog;
use App\Services\TOTPService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PublicOtpController extends Controller
{
    public function __construct(private readonly TOTPService $totpService)
    {
    }

    public function generate(Request $request, string $token): JsonResponse
    {
        return $this->handleGenerate($request, $token);
    }

    public function generateFromRequest(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'token' => ['required', 'string', 'max:64'],
        ]);

        return $this->handleGenerate($request, $payload['token']);
    }

    private function handleGenerate(Request $request, string $token): JsonResponse
    {
        return DB::transaction(function () use ($request, $token) {
            $customer = Customer::query()
                ->where('token', $token)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (! $customer) {
                return response()->json([
                    'message' => 'الرابط غير صالح أو غير نشط.',
                ], 404);
            }

            if ($customer->remainingUsage() < 1) {
                return response()->json([
                    'message' => 'لا يوجد رصيد متبقٍ لهذا الرابط.',
                ], 422);
            }

            $account = $customer->account()
                ->select(['id', 'merchant_id', 'secret_key_encrypted'])
                ->first();

            if (! $account || $account->merchant_id !== $customer->merchant_id) {
                return response()->json([
                    'message' => 'تعذر العثور على إعدادات الحساب.',
                ], 404);
            }

            try {
                $secret = $account->secret_key_encrypted;
                $otp = $this->totpService->generateCurrentCode($secret);
            } catch (DecryptException|InvalidArgumentException $exception) {
                return response()->json([
                    'message' => 'صيغة المفتاح السري غير صحيحة.',
                ], 422);
            }

            $customer->increment('usage_count');
            $customer->refresh();

            OtpLog::create([
                'customer_id' => $customer->id,
                'generated_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'تم توليد رمز التحقق بنجاح.',
                'otp_code' => $otp['code'],
                'expires_in' => $otp['expires_in'],
                'expires_at' => $otp['expires_at']->toIso8601String(),
                'remaining_usage' => $customer->remainingUsage(),
            ]);
        });
    }
}
