<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
        .container { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0f766e, #14b8a6); padding: 28px 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; color: #1e293b; margin-bottom: 8px; }
        .message { color: #475569; font-size: 14px; line-height: 1.7; margin-bottom: 20px; }
        .otp-box { text-align: center; background: #f0fdfa; border: 2px dashed #14b8a6; border-radius: 12px; padding: 24px; margin: 20px 0; }
        .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 6px; color: #0f766e; direction: ltr; }
        .note { font-size: 12px; color: #94a3b8; margin-top: 16px; }
        .footer { text-align: center; padding: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 OTP Hub</h1>
        </div>
        <div class="body">
            <div class="greeting">مرحبًا {{ $userName }},</div>
            <div class="message">لقد تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بك. استخدم رمز التحقق التالي:</div>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <div class="message">هذا الرمز صالح لمدة 10 دقائق. إذا لم تقم بهذا الطلب، يمكنك تجاهل هذه الرسالة.</div>
            <div class="note">OTP Hub - إدارة حسابات 2FA</div>
        </div>
        <div class="footer">&copy; {{ date('Y') }} OTP Hub. جميع الحقوق محفوظة.</div>
    </div>
</body>
</html>
