<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Account;
use App\Models\Customer;
use App\Models\OtpLog;
use App\Models\User;
use App\Services\TwoFactorSecretParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class MerchantPortalController extends Controller
{
    public function __construct(private readonly TwoFactorSecretParser $secretParser)
    {
    }

    public function showForgotPasswordForm(): View
    {
        return view('merchant.auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::query()
            ->where('email', $payload['email'])
            ->where('role', User::ROLE_MERCHANT)
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'لا يوجد تاجر بهذا البريد الإلكتروني.'])
                ->onlyInput('email');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Str::random(60), 'otp' => $otp, 'used' => false, 'created_at' => now()]
        );

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return redirect()
            ->route('merchant.forgot-password.verify.form', ['email' => $user->email])
            ->with('status', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
    }

    public function showVerifyOtpForm(Request $request): View|RedirectResponse
    {
        $email = $request->get('email');

        if (! $email) {
            return redirect()->route('merchant.forgot-password.form');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('used', false)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        $otpExpired = ! $record;

        return view('merchant.auth.verify-otp', [
            'email' => $email,
            'otpExpired' => $otpExpired,
        ]);
    }

    public function verifyOtpAndReset(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $payload['email'])
            ->where('otp', $payload['otp'])
            ->where('used', false)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if (! $record) {
            return back()
                ->withErrors(['otp' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.'])
                ->onlyInput('email', 'otp');
        }

        $user = User::query()
            ->where('email', $payload['email'])
            ->where('role', User::ROLE_MERCHANT)
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'المستخدم غير موجود.'])
                ->onlyInput('email');
        }

        $user->update([
            'password' => Hash::make($payload['password']),
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $payload['email'])
            ->update(['used' => true]);

        return redirect()
            ->route('merchant.login.form')
            ->with('status', 'تم إعادة تعيين كلمة المرور بنجاح. سجل الدخول الآن.');
    }

    public function showLoginForm(Request $request): RedirectResponse|View
    {
        if ($request->user() && $request->user()->isMerchant()) {
            return redirect()->route('merchant.dashboard');
        }

        return view('merchant.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $merchant = User::query()
            ->where('email', $credentials['email'])
            ->where('role', User::ROLE_MERCHANT)
            ->first();

        if (! $merchant || ! Hash::check($credentials['password'], $merchant->password)) {
            return back()
                ->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])
                ->onlyInput('email');
        }

        if (! $merchant->is_active) {
            return back()
                ->withErrors(['email' => 'حساب التاجر غير نشط حاليًا.'])
                ->onlyInput('email');
        }

        if ($merchant->subscription_end && $merchant->subscription_end->isPast()) {
            return back()
                ->withErrors(['email' => 'انتهت صلاحية الاشتراك.'])
                ->onlyInput('email');
        }

        Auth::login($merchant, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('merchant.dashboard');
    }

    public function showProfileForm(Request $request): View
    {
        return view('merchant.profile.edit', [
            'merchant' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $merchant = $request->user();

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data = ['name' => $payload['name']];

        if (! empty($payload['password'])) {
            $data['password'] = Hash::make($payload['password']);
        }

        $merchant->update($data);

        return back()->with('status', 'تم تحديث بيانات حسابك بنجاح.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('merchant.login.form');
    }

    public function dashboard(Request $request): View
    {
        $merchant = $request->user();
        $merchantId = $merchant->id;

        $accountCount = Account::query()->where('merchant_id', $merchantId)->count();
        $customerCount = Customer::query()->where('merchant_id', $merchantId)->count();
        $activeCustomerCount = Customer::query()
            ->where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->count();

        $otpToday = OtpLog::query()
            ->whereHas('customer', fn ($q) => $q->where('merchant_id', $merchantId))
            ->whereDate('generated_at', now()->toDateString())
            ->count();

        $otpThisMonth = OtpLog::query()
            ->whereHas('customer', fn ($q) => $q->where('merchant_id', $merchantId))
            ->whereMonth('generated_at', now()->month)
            ->whereYear('generated_at', now()->year)
            ->count();

        $topAccounts = Account::query()
            ->where('merchant_id', $merchantId)
            ->withCount('customers')
            ->orderBy('customers_count', 'desc')
            ->take(5)
            ->get();

        $exhaustedCustomers = Customer::query()
            ->where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->whereColumn('usage_count', '>=', 'usage_limit')
            ->count();

        return view('merchant.dashboard', [
            'merchant' => $merchant,
            'accountCount' => $accountCount,
            'customerCount' => $customerCount,
            'activeCustomerCount' => $activeCustomerCount,
            'otpToday' => $otpToday,
            'otpThisMonth' => $otpThisMonth,
            'topAccounts' => $topAccounts,
            'exhaustedCustomers' => $exhaustedCustomers,
        ]);
    }

    public function accounts(Request $request): View
    {
        $accounts = Account::query()
            ->where('merchant_id', $request->user()->id)
            ->withCount('customers')
            ->latest()
            ->get();

        return view('merchant.accounts.index', [
            'accounts' => $accounts,
        ]);
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $payload = $this->validateAccount($request);

        Account::create([
            'merchant_id' => $request->user()->id,
            'label' => $payload['label'],
            'email' => $payload['email'] ?? null,
            'username' => $payload['username'] ?? null,
            'password_encrypted' => $payload['password'],
            'secret_key_encrypted' => $payload['secret_key'],
        ]);

        return back()->with('status', 'تم إنشاء الحساب بنجاح.');
    }

    public function editAccount(Request $request, int $accountId): View
    {
        $account = $this->findMerchantAccount($request, $accountId);

        return view('merchant.accounts.edit', [
            'account' => $account,
        ]);
    }

    public function updateAccount(Request $request, int $accountId): RedirectResponse
    {
        $account = $this->findMerchantAccount($request, $accountId);
        $payload = $this->validateAccount($request);

        $account->update([
            'label' => $payload['label'],
            'email' => $payload['email'] ?? null,
            'username' => $payload['username'] ?? null,
            'password_encrypted' => $payload['password'],
            'secret_key_encrypted' => $payload['secret_key'],
        ]);

        return redirect()
            ->route('merchant.accounts.edit', ['accountId' => $account->id])
            ->with('status', 'تم تحديث الحساب بنجاح.');
    }

    public function deleteAccount(Request $request, int $accountId): RedirectResponse
    {
        $account = $this->findMerchantAccount($request, $accountId);
        $account->delete();

        return back()->with('status', 'تم حذف الحساب بنجاح.');
    }

    public function customers(Request $request): View
    {
        $merchantId = $request->user()->id;

        $accounts = Account::query()
            ->where('merchant_id', $merchantId)
            ->orderBy('label')
            ->get();

        $customers = Customer::query()
            ->where('merchant_id', $merchantId)
            ->with('account:id,label,merchant_id')
            ->latest()
            ->get();

        return view('merchant.customers.index', [
            'accounts' => $accounts,
            'customers' => $customers,
        ]);
    }

    public function storeCustomer(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:120'],
            'usage_limit' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $account = $this->findMerchantAccount($request, (int) $payload['account_id']);

        Customer::create([
            'merchant_id' => $request->user()->id,
            'account_id' => $account->id,
            'name' => $payload['name'],
            'token' => $this->generateUniqueCustomerToken($request->user()->name, $payload['name']),
            'usage_limit' => (int) $payload['usage_limit'],
            'usage_count' => 0,
            'is_active' => (bool) ($payload['is_active'] ?? true),
        ]);

        return back()->with('status', 'تم إنشاء العميل بنجاح.');
    }

    public function updateCustomer(Request $request, int $customerId): RedirectResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $payload = $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:120'],
            'usage_limit' => ['required', 'integer', 'min:1'],
            'usage_count' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $account = $this->findMerchantAccount($request, (int) $payload['account_id']);

        if ((int) $payload['usage_count'] > (int) $payload['usage_limit']) {
            return back()->withErrors([
                'usage_count' => 'عدد الاستخدامات لا يمكن أن يتجاوز حد الاستخدام.',
            ]);
        }

        $customer->update([
            'account_id' => $account->id,
            'name' => $payload['name'],
            'usage_limit' => (int) $payload['usage_limit'],
            'usage_count' => (int) $payload['usage_count'],
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return back()->with('status', 'تم تحديث بيانات العميل بنجاح.');
    }

    public function deleteCustomer(Request $request, int $customerId): RedirectResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);
        $customer->delete();

        return back()->with('status', 'تم حذف العميل بنجاح.');
    }

    public function storeCustomersBulk(Request $request): RedirectResponse
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
        $created = 0;

        for ($i = 1; $i <= (int) $payload['count']; $i++) {
            $name = $payload['name_prefix'].' #'.$i;

            Customer::create([
                'merchant_id' => $merchantId,
                'account_id' => $account->id,
                'name' => $name,
                'token' => $this->generateUniqueCustomerToken($request->user()->name, $name),
                'usage_limit' => (int) $payload['usage_limit'],
                'usage_count' => 0,
                'is_active' => $isActive,
            ]);

            $created++;
        }

        return back()->with('status', "تم إنشاء {$created} عميل بنجاح.");
    }

    public function renewCustomerBalance(Request $request, int $customerId): RedirectResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $customer->update([
            'usage_count' => 0,
            'is_active' => true,
        ]);

        return back()->with('status', 'تم تجديد رصيد العميل بنجاح.');
    }

    public function regenerateCustomerToken(Request $request, int $customerId): RedirectResponse
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $customer->update([
            'token' => $this->generateUniqueCustomerToken($request->user()->name, $customer->name),
        ]);

        return back()->with('status', 'تم إعادة توليد الرابط بنجاح.');
    }

    public function showAccountCreateForm(Request $request): View
    {
        return view('merchant.accounts.create');
    }

    public function showCustomerCreateForm(Request $request): View
    {
        $accounts = Account::query()
            ->where('merchant_id', $request->user()->id)
            ->orderBy('label')
            ->get();

        return view('merchant.customers.create', [
            'accounts' => $accounts,
        ]);
    }

    public function showBulkCreateForm(Request $request): View
    {
        $accounts = Account::query()
            ->where('merchant_id', $request->user()->id)
            ->orderBy('label')
            ->get();

        return view('merchant.customers.bulk', [
            'accounts' => $accounts,
        ]);
    }

    public function showCustomerEditForm(Request $request, int $customerId): View
    {
        $customer = $this->findMerchantCustomer($request, $customerId);

        $accounts = Account::query()
            ->where('merchant_id', $request->user()->id)
            ->orderBy('label')
            ->get();

        return view('merchant.customers.edit', [
            'customer' => $customer,
            'accounts' => $accounts,
        ]);
    }

    private function validateAccount(Request $request): array
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

    private function findMerchantCustomer(Request $request, int $customerId): Customer
    {
        return Customer::query()
            ->where('merchant_id', $request->user()->id)
            ->findOrFail($customerId);
    }

    private function generateUniqueCustomerToken(string $merchantName, string $customerName): string
    {
        // استبدال المسافات والرموز الخاصة بشرطة مع الحفاظ على اللغة العربية
        $mName = preg_replace('/[^\p{L}\p{N}]+/u', '-', $merchantName);
        $cName = preg_replace('/[^\p{L}\p{N}]+/u', '-', $customerName);
        
        $base = trim($mName . '-' . $cName, '-');
        $base = Str::limit($base, 50, ''); // تحديد الطول لتجنب أخطاء قاعدة البيانات

        do {
            $token = $base . '-' . random_int(10000, 99999);
        } while (Customer::query()->where('token', $token)->exists());

        return $token;
    }
}
