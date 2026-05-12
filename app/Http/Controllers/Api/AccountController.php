<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\TwoFactorSecretParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class AccountController extends Controller
{
    public function __construct(private readonly TwoFactorSecretParser $secretParser)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $accounts = Account::query()
            ->where('merchant_id', $request->user()->id)
            ->withCount('customers')
            ->latest()
            ->get();

        return response()->json([
            'data' => $accounts,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->validatePayload($request);

        $account = Account::create([
            'merchant_id' => $request->user()->id,
            'label' => $payload['label'],
            'email' => $payload['email'] ?? null,
            'username' => $payload['username'] ?? null,
            'password_encrypted' => $payload['password'],
            'secret_key_encrypted' => $payload['secret_key'],
        ]);

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح.',
            'data' => $account,
        ], 201);
    }

    public function update(Request $request, int $accountId): JsonResponse
    {
        $account = $this->findMerchantAccount($request, $accountId);
        $payload = $this->validatePayload($request);

        $account->update([
            'label' => $payload['label'],
            'email' => $payload['email'] ?? null,
            'username' => $payload['username'] ?? null,
            'password_encrypted' => $payload['password'],
            'secret_key_encrypted' => $payload['secret_key'],
        ]);

        return response()->json([
            'message' => 'تم تحديث الحساب بنجاح.',
            'data' => $account->fresh(),
        ]);
    }

    public function destroy(Request $request, int $accountId): JsonResponse
    {
        $account = $this->findMerchantAccount($request, $accountId);
        $account->delete();

        return response()->json([
            'message' => 'تم حذف الحساب بنجاح.',
        ]);
    }

    private function validatePayload(Request $request): array
    {
        $payload = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:username'],
            'username' => ['nullable', 'string', 'max:255', 'required_without:email'],
            'password' => ['required', 'string', 'max:255'],
            'secret_key' => ['required', 'string', 'max:4000'],
        ]);

        try {
            $payload['secret_key'] = $this->secretParser->normalize($payload['secret_key']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'secret_key' => $exception->getMessage(),
            ]);
        }

        return $payload;
    }

    private function findMerchantAccount(Request $request, int $accountId): Account
    {
        return Account::query()
            ->where('merchant_id', $request->user()->id)
            ->findOrFail($accountId);
    }
}
