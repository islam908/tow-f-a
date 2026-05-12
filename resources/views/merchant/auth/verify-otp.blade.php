@extends('layouts.merchant')

@section('title', 'التحقق من الرمز')

@section('content')
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1>التحقق من الرمز</h1>
                <p class="text-muted">أدخل رمز التحقق المرسل إلى بريدك الإلكتروني وكلمة المرور الجديدة.</p>
            </div>

            @if($otpExpired)
                <div class="alert alert-warning" style="margin-bottom:1rem;">
                    <span>⚠️</span>
                    <span>انتهت صلاحية رمز التحقق. <a href="{{ route('merchant.forgot-password.form') }}" style="color:var(--primary);font-weight:700;">أعد إرسال الطلب</a></span>
                </div>
            @endif

            <form method="post" action="{{ route('merchant.forgot-password.verify') }}" class="login-form">
                @csrf

                <input type="hidden" name="email" value="{{ $email }}">

                <div class="field">
                    <label for="otp">رمز التحقق (6 أرقام)</label>
                    <input id="otp" type="text" name="otp" value="{{ old('otp') }}" placeholder="000000" required autofocus maxlength="6" inputmode="numeric" pattern="[0-9]{6}" style="text-align:center;font-size:1.4rem;letter-spacing:8px;font-weight:700;direction:ltr;">
                </div>

                <div class="field">
                    <label for="password">كلمة المرور الجديدة</label>
                    <input id="password" type="text" name="password" placeholder="أدخل كلمة المرور الجديدة" required>
                </div>

                <div class="field">
                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" type="text" name="password_confirmation" placeholder="تأكيد كلمة المرور الجديدة" required>
                </div>

                <button type="submit" class="btn btn-primary login-submit" {{ $otpExpired ? 'disabled' : '' }}>🔄 إعادة تعيين كلمة المرور</button>
            </form>

            <div class="login-footer">
                <a href="{{ route('merchant.forgot-password.form') }}" class="btn btn-ghost" style="width:100%;">← إعادة إرسال الرمز</a>
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

        .alert-warning {
            border-radius: var(--radius);
            padding: 0.75rem 0.9rem;
            border: 1px solid #fde68a;
            font-size: 0.88rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            background: var(--warning-bg);
            color: var(--warning);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem 1.2rem;
            }
        }
    </style>
@endpush
