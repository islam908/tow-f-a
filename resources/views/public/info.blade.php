<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بيانات الحساب — OTP Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Changa:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #0f766e;
            --primary-dark: #0d5e57;
            --primary-light: #14b8a6;
            --primary-glow: rgba(15, 118, 110, 0.15);
            --danger: #e11d48;
            --success: #16a34a;
            --success-bg: #f0fdf4;
            --warning: #d97706;
            --ink: #0f172a;
            --ink-2: #1e293b;
            --ink-soft: #64748b;
            --ink-muted: #94a3b8;
            --line: #e2e8f0;
            --panel: #ffffff;
            --paper: #f8fafc;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
            --shadow: 0 4px 20px rgba(15, 23, 42, 0.1);
            --shadow-lg: 0 12px 44px rgba(15, 23, 42, 0.14);
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html {
            -webkit-font-smoothing: antialiased;
        }

        body {
            margin: 0;
            min-height: 100dvh;
            background: linear-gradient(135deg, #f0fdf8 0%, #f8fafc 50%, #f0f4ff 100%);
            font-family: 'Cairo', sans-serif;
            color: var(--ink);
            text-align: right;
            padding: 1.2rem;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        h1, h2, h3 { font-family: 'Changa', sans-serif; line-height: 1.3; margin: 0; }
        h1 { font-size: 1.5rem; }
        h2 { font-size: 1.2rem; }

        .shell {
            width: min(1000px, 100%);
            margin: 1rem auto;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 1rem;
            align-items: start;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow);
        }

        .hero-card {
            background: linear-gradient(135deg, #fff 0%, #f9fffd 100%);
            border-color: #c8ddd6;
            position: relative;
            overflow: hidden;
        }

        .hero-card::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(15, 118, 110, 0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-card h1 {
            position: relative;
            z-index: 1;
        }

        .hero-card p {
            color: var(--ink-soft);
            margin-top: 0.25rem;
            position: relative;
            z-index: 1;
        }

        .info-grid {
            display: grid;
            gap: 0.7rem;
            margin-top: 1rem;
            position: relative;
            z-index: 1;
        }

        .info-row {
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 0.7rem 0.8rem;
            background: #fafdfc;
            display: grid;
            gap: 0.15rem;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            word-break: break-word;
        }

        .info-value.mono {
            direction: ltr;
            text-align: left;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 0.9rem;
            background: var(--paper);
            border: 1px dashed var(--line);
            padding: 0.2rem 0.4rem;
            border-radius: 6px;
            display: inline-block;
            max-width: 100%;
            overflow: auto;
        }

        .usage-section {
            margin-top: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .badge-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .badge-danger {
            background: #fff1f2;
            color: var(--danger);
            border: 1px solid #fecdd3;
        }

        .remaining-value {
            font-family: 'Changa', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* OTP Panel */
        .otp-card {
            position: relative;
            overflow: hidden;
        }

        .otp-card::after {
            content: '';
            position: absolute;
            inset: auto -80px -130px auto;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(15, 118, 110, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .otp-header {
            position: relative;
            z-index: 1;
            margin-bottom: 0.8rem;
        }

        .otp-header h2 {
            margin-bottom: 0.2rem;
        }

        .otp-header p {
            color: var(--ink-soft);
            font-size: 0.9rem;
            margin: 0;
        }

        .otp-action {
            position: relative;
            z-index: 1;
        }

        .otp-btn {
            width: 100%;
            border: none;
            border-radius: var(--radius-sm);
            padding: 0.8rem 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: 0 6px 24px var(--primary-glow);
        }

        .otp-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(15, 118, 110, 0.25);
        }

        .otp-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .otp-result {
            margin-top: 1rem;
            display: none;
        }

        .otp-result.is-visible {
            display: block;
            animation: fadeUp 0.35s ease;
        }

        .otp-code-box {
            background: linear-gradient(135deg, #f0fdfa, #f4fffb);
            border: 2px solid #a7d4cb;
            border-radius: var(--radius);
            padding: 1rem;
            text-align: center;
        }

        .otp-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .otp-code {
            font-family: 'Changa', sans-serif;
            font-size: clamp(2rem, 4.5vw, 2.8rem);
            letter-spacing: 0.15em;
            font-weight: 800;
            direction: ltr;
            text-align: center;
            color: var(--ink);
            margin: 0.2rem 0;
        }

        .otp-expiry {
            text-align: center;
            color: var(--ink-soft);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .otp-progress-track {
            margin-top: 0.6rem;
            height: 6px;
            border-radius: 999px;
            background: var(--line);
            overflow: hidden;
        }

        .otp-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            transition: width 1s linear;
            border-radius: 999px;
        }

        .otp-status {
            margin-top: 0.6rem;
            font-size: 0.88rem;
            color: var(--ink-soft);
            min-height: 1.3rem;
            text-align: center;
        }

        .otp-status.error {
            color: var(--danger);
            font-weight: 600;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 860px) {
            .shell {
                grid-template-columns: 1fr;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            body { padding: 0.7rem; }
            .card { padding: 0.9rem; }
            .shell { margin: 0; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="card hero-card">
            <h1>🔐 بيانات الوصول</h1>
            <p>مرحبًا {{ $customer->name }}، هذه بيانات الحساب المرتبط برابطك الخاص.</p>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">اسم الحساب</div>
                    <div class="info-value">{{ $account->label }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">البريد الإلكتروني</div>
                    <div class="info-value">{{ $account->email ?? '-' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">اسم المستخدم</div>
                    <div class="info-value">{{ $account->username ?? '-' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">كلمة المرور</div>
                    <div class="info-value"><span class="mono">{{ $account->password_encrypted }}</span></div>
                </div>

                <div class="info-row">
                    <div class="info-label">الاستخدام المتبقي</div>
                    <div class="usage-section">
                        <span id="remaining-usage" class="remaining-value">{{ $remainingUsage }}</span>
                        <span id="usage-badge" class="badge {{ $isExhausted ? 'badge-danger' : 'badge-success' }}">
                            {{ $isExhausted ? 'نفد الرصيد' : 'متاح' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section class="card otp-card">
            <div class="otp-header">
                <h2>🔄 رمز التحقق</h2>
                <p>اضغط الزر للحصول على رمز 2FA صالح لمدة 30 ثانية.</p>
            </div>

            <div class="otp-action">
                <button
                    id="otp-button"
                    class="otp-btn"
                    type="button"
                    data-exhausted="{{ $isExhausted ? '1' : '0' }}"
                    {{ $isExhausted ? 'disabled' : '' }}
                >
                    {{ $isExhausted ? '⛔ نفد الرصيد' : '🚀 احصل على رمز التحقق' }}
                </button>

                <div id="otp-result" class="otp-result {{ !$isExhausted ? '' : '' }}">
                    <div class="otp-code-box">
                        <div class="otp-label">رمز التحقق الحالي</div>
                        <div id="otp-code" class="otp-code">------</div>
                        <div id="otp-expiry" class="otp-expiry">ينتهي خلال -- ث</div>
                        <div class="otp-progress-track">
                            <div id="otp-progress-bar" class="otp-progress-fill" style="width:100%"></div>
                        </div>
                    </div>
                </div>

                <div id="otp-status" class="otp-status"></div>
            </div>
        </section>
    </main>

    <script>
        (() => {
            const endpoint = @json($otpEndpoint);
            const button = document.getElementById('otp-button');
            const otpResult = document.getElementById('otp-result');
            const otpCode = document.getElementById('otp-code');
            const otpExpiry = document.getElementById('otp-expiry');
            const otpProgressBar = document.getElementById('otp-progress-bar');
            const otpStatus = document.getElementById('otp-status');
            const remainingUsage = document.getElementById('remaining-usage');
            const usageBadge = document.getElementById('usage-badge');

            let countdownTimer = null;

            const setStatus = (message, error = false) => {
                otpStatus.textContent = message;
                otpStatus.classList.toggle('error', error);
            };

            const updateExhausted = (isExhausted) => {
                usageBadge.textContent = isExhausted ? 'نفد الرصيد' : 'متاح';
                usageBadge.className = 'badge ' + (isExhausted ? 'badge-danger' : 'badge-success');

                if (isExhausted) {
                    button.disabled = true;
                    button.textContent = '⛔ نفد الرصيد';
                    button.dataset.exhausted = '1';
                } else {
                    button.textContent = '🚀 احصل على رمز التحقق';
                    button.dataset.exhausted = '0';
                }
            };

            const startCountdown = (seconds) => {
                const total = seconds;
                let remaining = seconds;

                if (countdownTimer) clearInterval(countdownTimer);

                otpExpiry.textContent = `ينتهي خلال ${remaining} ث`;
                otpProgressBar.style.width = '100%';

                countdownTimer = setInterval(() => {
                    remaining -= 1;
                    const pct = Math.max(0, (remaining / total) * 100);
                    otpProgressBar.style.width = `${pct}%`;

                    if (remaining <= 0) {
                        clearInterval(countdownTimer);
                        countdownTimer = null;
                        otpExpiry.textContent = 'انتهت الصلاحية. اطلب رمزًا جديدًا.';
                        return;
                    }

                    otpExpiry.textContent = `ينتهي خلال ${remaining} ث`;
                }, 1000);
            };

            button.addEventListener('click', async () => {
                if (button.disabled) return;

                button.disabled = true;
                setStatus('جاري توليد رمز التحقق...');

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' }
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'فشل توليد رمز التحقق.');
                    }

                    otpResult.classList.add('is-visible');
                    otpCode.textContent = payload.otp_code;
                    startCountdown(payload.expires_in);

                    remainingUsage.textContent = payload.remaining_usage;
                    updateExhausted(payload.remaining_usage < 1);
                    setStatus(payload.message || '✅ تم توليد الرمز بنجاح');
                } catch (error) {
                    setStatus(error.message || '❌ فشل توليد رمز التحقق.', true);
                } finally {
                    if (button.dataset.exhausted !== '1') {
                        button.disabled = false;
                    }
                }
            });
        })();
    </script>
</body>
</html>
