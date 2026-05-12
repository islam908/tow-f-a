<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Account;
use App\Models\Customer;
use App\Models\OtpLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPortalController extends Controller
{
    public function showForgotPasswordForm(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::query()
            ->where('email', $payload['email'])
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'لا يوجد مدير بهذا البريد الإلكتروني.'])
                ->onlyInput('email');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Str::random(60), 'otp' => $otp, 'used' => false, 'created_at' => now()]
        );

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return redirect()
            ->route('admin.forgot-password.verify.form', ['email' => $user->email])
            ->with('status', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
    }

    public function showVerifyOtpForm(Request $request): View|RedirectResponse
    {
        $email = $request->get('email');

        if (! $email) {
            return redirect()->route('admin.forgot-password.form');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('used', false)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        $otpExpired = ! $record;

        return view('admin.auth.verify-otp', [
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
            ->where('role', User::ROLE_ADMIN)
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
            ->route('admin.login.form')
            ->with('status', 'تم إعادة تعيين كلمة المرور بنجاح. سجل الدخول الآن.');
    }

    public function showLoginForm(Request $request): RedirectResponse|View
    {
        if ($request->user() && $request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = User::query()
            ->where('email', $credentials['email'])
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            return back()
                ->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])
                ->onlyInput('email');
        }

        Auth::login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }

    public function dashboard(Request $request): View
    {
        $admin = $request->user();

        $totalMerchants = User::query()->where('role', User::ROLE_MERCHANT)->count();
        $activeMerchants = User::query()->where('role', User::ROLE_MERCHANT)->where('is_active', true)->count();
        $inactiveMerchants = $totalMerchants - $activeMerchants;
        $totalAccounts = Account::query()->count();
        $totalCustomers = Customer::query()->count();
        $otpToday = OtpLog::query()->whereDate('generated_at', now()->toDateString())->count();

        $expiringSoon = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->where('is_active', true)
            ->whereNotNull('subscription_end')
            ->whereBetween('subscription_end', [now(), now()->addDays(7)])
            ->count();

        $recentMerchants = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->withCount(['merchantAccounts', 'merchantCustomers'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'admin' => $admin,
            'totalMerchants' => $totalMerchants,
            'activeMerchants' => $activeMerchants,
            'inactiveMerchants' => $inactiveMerchants,
            'totalAccounts' => $totalAccounts,
            'totalCustomers' => $totalCustomers,
            'otpToday' => $otpToday,
            'expiringSoon' => $expiringSoon,
            'recentMerchants' => $recentMerchants,
        ]);
    }

    public function merchants(Request $request): View
    {
        $query = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->withCount(['merchantAccounts', 'merchantCustomers'])
            ->with(['merchantAccounts', 'merchantCustomers']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $merchants = $query->latest()->get();

        return view('admin.merchants.index', [
            'merchants' => $merchants,
            'search' => $search,
        ]);
    }



    public function storeMerchant(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'subscription_start' => ['nullable', 'date'],
            'subscription_end' => ['nullable', 'date', 'after_or_equal:subscription_start'],
        ]);

        User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'role' => User::ROLE_MERCHANT,
            'is_active' => (bool) ($payload['is_active'] ?? true),
            'subscription_start' => $payload['subscription_start'] ?? null,
            'subscription_end' => $payload['subscription_end'] ?? null,
        ]);

        return back()->with('status', 'تم إضافة التاجر بنجاح.');
    }

    public function editMerchant(Request $request, int $merchantId): View
    {
        $merchant = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->findOrFail($merchantId);

        $accountCount = Account::query()->where('merchant_id', $merchantId)->count();
        $customerCount = Customer::query()->where('merchant_id', $merchantId)->count();
        $otpCount = OtpLog::query()
            ->whereHas('customer', function ($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            })
            ->count();

        $recentCustomers = Customer::query()
            ->where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->with('account')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.merchants.edit', compact(
            'merchant', 'accountCount', 'customerCount', 'otpCount', 'recentCustomers'
        ));
    }

    public function toggleMerchant(Request $request, int $merchantId): RedirectResponse
    {
        $merchant = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->findOrFail($merchantId);

        $merchant->update([
            'is_active' => ! $merchant->is_active,
        ]);

        $status = $merchant->is_active ? 'تم تفعيل التاجر بنجاح.' : 'تم إيقاف التاجر بنجاح.';

        return back()->with('status', $status);
    }

    public function updateMerchant(Request $request, int $merchantId): RedirectResponse
    {
        $merchant = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->findOrFail($merchantId);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($merchantId)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'subscription_start' => ['nullable', 'date'],
            'subscription_end' => ['nullable', 'date', 'after_or_equal:subscription_start'],
        ]);

        $data = [
            'name' => $payload['name'],
            'email' => $payload['email'],
            'is_active' => (bool) ($payload['is_active'] ?? false),
            'subscription_start' => $payload['subscription_start'] ?? null,
            'subscription_end' => $payload['subscription_end'] ?? null,
        ];

        if (! empty($payload['password'])) {
            $data['password'] = Hash::make($payload['password']);
        }

        $merchant->update($data);

        return back()->with('status', 'تم تحديث بيانات التاجر بنجاح.');
    }

    public function deleteMerchant(Request $request, int $merchantId): RedirectResponse
    {
        $merchant = User::query()
            ->where('role', User::ROLE_MERCHANT)
            ->findOrFail($merchantId);

        $merchant->delete();

        return back()->with('status', 'تم حذف التاجر بنجاح.');
    }
}
