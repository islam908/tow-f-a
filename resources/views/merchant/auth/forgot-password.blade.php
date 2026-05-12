@extends('layouts.merchant')

@section('title', 'استعادة كلمة المرور')

@section('content')
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1>استعادة كلمة المرور</h1>
                <p class="text-muted">أدخل بريدك الإلكتروني وكلمة المرور الجديدة.</p>
            </div>

            <form method="post" action="{{ route('merchant.forgot-password.send-otp') }}" class="login-form">
                @csrf

                <div class="field">
                    <label for="email">البريد الإلكتروني</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="merchant@example.com" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary login-submit">📩 إرسال رمز التحقق</button>
            </form>

            <div class="login-footer">
                <a href="{{ route('merchant.login.form') }}" class="btn btn-ghost" style="width:100%;">← العودة لتسجيل الدخول</a>
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
