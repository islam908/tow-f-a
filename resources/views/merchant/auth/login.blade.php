@extends('layouts.merchant')

@section('title', 'تسجيل دخول التاجر')

@section('content')
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1>تسجيل دخول التاجر</h1>
                <p class="text-muted">سجل الدخول لإدارة الحسابات والعملاء والروابط الخاصة.</p>
            </div>

            <form method="post" action="{{ route('merchant.login') }}" class="login-form">
                @csrf

                <div class="field">
                    <label for="email">البريد الإلكتروني</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="merchant@example.com" required autofocus>
                </div>

                <div class="field">
                    <label for="password">كلمة المرور</label>
                    <input id="password" type="password" name="password" placeholder="••••••••" required>
                    <a href="{{ route('merchant.forgot-password.form') }}" class="forgot-link">نسيت كلمة السر؟</a>
                </div>

                <button type="submit" class="btn btn-primary login-submit">دخول إلى اللوحة</button>
            </form>

            <div class="login-footer">
                <span class="chip">منصة OTP Hub</span>
                <span class="chip chip-brand">إدارة حسابات 2FA</span>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .login-page {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            padding: 2rem 2rem 1.8rem;
            box-shadow: var(--shadow-lg);
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 24px var(--primary-glow);
        }

        .login-header h1 {
            margin-bottom: 0.3rem;
        }

        .login-form {
            display: grid;
            gap: 1rem;
        }

        .login-submit {
            width: 100%;
            padding: 0.7rem;
            font-size: 0.95rem;
            margin-top: 0.3rem;
        }

        .forgot-link {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: underline;
            text-underline-offset: 2px;
            margin-top: 0.2rem;
            display: inline-block;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
        }

        .login-footer {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1.2rem;
            flex-wrap: wrap;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem 1.2rem;
            }
        }
    </style>
@endpush
