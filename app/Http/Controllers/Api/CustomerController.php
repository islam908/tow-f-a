<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query()
            ->where('merchant_id', $request->user()->id)
            ->with('account:id,merchant_id,label,email,username')
            ->latest();

        if ($request->filled('account_id')) {
            $query->where('account_id', (int) $request->query('account_id'));
        }

        return response()->json([
            'data' => $query->get()->map(function (Customer $customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'account_id' => $customer->account_id,
                    'account' => $customer->account,
                    'token' => $customer->token,
                    'info_url' => url('/info/'.$customer->token),
                    'usage_limit' => $customer->usage_limit,
                    'usage_count' => $customer->usage_count,
                    'remaining_usage' => $customer->remainingUsage(),
                    'is_active' => $customer->is_active,
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at,
                ];
            }),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:120'],
            'usage_limit' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $account = $this->findMerchantAccount($request, (int) $payload['account_id']);

        $customer = Customer::create([
            'merchant_id' => $request->user()->id,
            'account_id' => $account->id,
            'name' => $payload['name'],
            'token' => $this->generateUniqueToken($request->user()->name, $payload['name']),
            'usage_limit' => (int) $payload['usage_limit'],
            'usage_count' => 0,
            'is_active' => (bool) ($payload['is_active'] ?? true),
        ]);

        return response()->json([
            'message' => 'تم إنشاء العميل بنجاح.',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'token' => $customer->token,
                'info_url' => url('/info/'.$customer->token),
                'usage_limit' => $customer->usage_limit,
                'usage_count' => $customer->usage_count,
                'remaining_usage' => $customer->remainingUsage(),
                'is_active' => $customer->is_active,
            ],
        ], 201);
    }

    public function update(Request $request, int $customerId): JsonResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $payload = $request->validate([
            'account_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'usage_limit' => ['sometimes', 'required', 'integer', 'min:1'],
            'usage_count' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ]);

        if (array_key_exists('account_id', $payload)) {
            $payload['account_id'] = $this->findMerchantAccount($request, (int) $payload['account_id'])->id;
        }

        $newLimit = array_key_exists('usage_limit', $payload)
            ? (int) $payload['usage_limit']
            : $customer->usage_limit;

        $newCount = array_key_exists('usage_count', $payload)
            ? (int) $payload['usage_count']
            : $customer->usage_count;

        if ($newCount > $newLimit) {
            return response()->json([
                'message' => 'عدد الاستخدامات لا يمكن أن يتجاوز حد الاستخدام.',
            ], 422);
        }

        $customer->update($payload);

        $customer = $customer->fresh(['account:id,merchant_id,label,email,username']);

        return response()->json([
            'message' => 'تم تحديث بيانات العميل بنجاح.',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'account_id' => $customer->account_id,
                'account' => $customer->account,
                'token' => $customer->token,
                'info_url' => url('/info/'.$customer->token),
                'usage_limit' => $customer->usage_limit,
                'usage_count' => $customer->usage_count,
                'remaining_usage' => $customer->remainingUsage(),
                'is_active' => $customer->is_active,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
            ],
        ]);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'name_prefix' => ['required', 'string', 'max:80'],
            'count' => ['required', 'integer', 'min:1', 'max:100'],
            'usage_limit' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $account = $this->findMerchantAccount($request, (int) $payload['account_id']);
        $merchantId = $request->user()->id;
        $isActive = (bool) ($payload['is_active'] ?? true);
        $customers = [];

        for ($i = 1; $i <= (int) $payload['count']; $i++) {
            $name = $payload['name_prefix'].' #'.$i;

            $customer = Customer::create([
                'merchant_id' => $merchantId,
                'account_id' => $account->id,
                'name' => $name,
                'token' => $this->generateUniqueToken($request->user()->name, $name),
                'usage_limit' => (int) $payload['usage_limit'],
                'usage_count' => 0,
                'is_active' => $isActive,
            ]);

            $customers[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'token' => $customer->token,
                'info_url' => url('/info/'.$customer->token),
                'usage_limit' => $customer->usage_limit,
            ];
        }

        return response()->json([
            'message' => 'تم إنشاء '.count($customers).' عميل بنجاح.',
            'data' => $customers,
        ], 201);
    }

    public function renewBalance(Request $request, int $customerId): JsonResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $customer->update([
            'usage_count' => 0,
            'is_active' => true,
        ]);

        $customer = $customer->fresh();

        return response()->json([
            'message' => 'تم تجديد رصيد العميل بنجاح.',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'usage_limit' => $customer->usage_limit,
                'usage_count' => $customer->usage_count,
                'remaining_usage' => $customer->remainingUsage(),
                'is_active' => $customer->is_active,
            ],
        ]);
    }

    public function regenerateToken(Request $request, int $customerId): JsonResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $customer->update([
            'token' => $this->generateUniqueToken($request->user()->name, $customer->name),
        ]);

        $customer = $customer->fresh();

        return response()->json([
            'message' => 'تم إعادة توليد الرابط بنجاح.',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'token' => $customer->token,
                'info_url' => url('/info/'.$customer->token),
            ],
        ]);
    }

    public function destroy(Request $request, int $customerId): JsonResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);
        $customer->delete();

        return response()->json([
            'message' => 'تم حذف العميل بنجاح.',
        ]);
    }

    private function findMerchantAccount(Request $request, int $accountId): Account
    {
        return Account::query()
            ->where('merchant_id', $request->user()->id)
            ->findOrFail($accountId);
    }

    private function findMerchantCustomer(Request $request, int $customerId): Customer
    {
        return Customer::query()
            ->where('merchant_id', $request->user()->id)
            ->findOrFail($customerId);
    }

    private function generateUniqueToken(string $merchantName, string $customerName): string
    {
        $mName = preg_replace('/[^\p{L}\p{N}]+/u', '-', $merchantName);
        $cName = preg_replace('/[^\p{L}\p{N}]+/u', '-', $customerName);
        
        $base = trim($mName . '-' . $cName, '-');
        $base = \Illuminate\Support\Str::limit($base, 50, '');

        do {
            $token = $base . '-' . random_int(10000, 99999);
        } while (Customer::query()->where('token', $token)->exists());

        return $token;
    }
}
