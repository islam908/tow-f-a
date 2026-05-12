@extends('layouts.merchant')

@section('title', 'إضافة حساب جديد')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">الحسابات</p>
        <div class="page-title-bar">
            <h1>➕ إضافة حساب جديد</h1>
            <a href="{{ route('merchant.accounts.index') }}" class="btn btn-ghost">← عودة للقائمة</a>
        </div>
        <p class="text-muted">أدخل بيانات الحساب واربط مفتاح 2FA Seed يدويًا أو عبر QR.</p>
    </div>

    <div class="card">
        <form method="post" action="{{ route('merchant.accounts.store') }}" class="form-layout" data-qr-secret-tool>
            @csrf

            <div class="field">
                <label for="label">اسم الحساب</label>
                <input id="label" type="text" name="label" value="{{ old('label') }}" placeholder="مثال: ChatGPT Team A" required>
            </div>

            <div class="field">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="merchant@example.com">
                <span class="hint">اختياري إذا توفر اسم مستخدم</span>
            </div>

            <div class="field">
                <label for="username">اسم المستخدم</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="username">
                <span class="hint">اختياري إذا توفر بريد إلكتروني</span>
            </div>

            <div class="field">
                <label for="password">كلمة المرور</label>
                <input id="password" type="text" name="password" value="{{ old('password') }}" required>
            </div>

            <div class="field field-full">
                <div class="secret-section">
                    <label for="secret_key">المفتاح السري (2FA Seed)</label>
                    <input id="secret_key" type="text" name="secret_key" value="{{ old('secret_key') }}" data-secret-input required placeholder="JBSWY3DPEHPK3PXP">
                    <span class="hint">يمكنك إدخال المفتاح مباشرة، أو لصق رابط otpauth، أو استخراجه من QR.</span>

                    <div class="qr-tools">
                        <div class="field">
                            <label for="create_qr_payload">نص QR أو رابط otpauth</label>
                            <input id="create_qr_payload" type="text" data-qr-payload-input placeholder="otpauth://totp/...?...&secret=XXXXX">
                        </div>

                        <div class="qr-row">
                            <div class="field">
                                <label for="create_qr_image">رفع صورة QR</label>
                                <input id="create_qr_image" type="file" data-qr-file-input accept="image/*">
                            </div>

                            <div class="field">
                                <label>المسح بالكاميرا</label>
                                <button type="button" class="btn btn-ghost" data-qr-camera-btn style="width:100%">📷 مسح QR</button>
                            </div>
                        </div>
                    </div>

                    <small class="qr-status" data-qr-status></small>
                </div>
            </div>

            <div class="field field-full form-actions">
                <button type="submit" class="btn btn-primary">💾 حفظ الحساب</button>
                <a href="{{ route('merchant.accounts.index') }}" class="btn btn-ghost">إلغاء</a>
            </div>
        </form>
    </div>

    <!-- Camera Overlay -->
    <div id="qr-camera-overlay" class="camera-overlay" hidden>
        <div class="camera-panel">
            <div class="camera-head">
                <h3>مسح رمز QR بالكاميرا</h3>
                <button type="button" id="qr-camera-close" class="btn btn-danger">إغلاق</button>
            </div>
            <p class="text-muted text-sm">وجه الكاميرا إلى رمز QR وسيتم تعبئة 2FA Seed تلقائيًا.</p>
            <video id="qr-camera-video" autoplay playsinline class="camera-video"></video>
            <canvas id="qr-camera-canvas" hidden></canvas>
            <div id="qr-camera-status" class="camera-status">جارِ البحث عن رمز QR...</div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .secret-section {
            border: 1.5px dashed var(--line);
            border-radius: var(--radius);
            padding: 0.9rem;
            background: #fafdfc;
        }

        .qr-tools {
            display: grid;
            gap: 0.6rem;
            margin-top: 0.5rem;
        }

        .qr-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
        }

        .qr-status {
            display: inline-block;
            margin-top: 0.35rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--primary);
        }

        .qr-status.error { color: var(--danger); }

        .camera-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.75);
            z-index: 9999;
            display: grid;
            place-items: center;
            padding: 1rem;
            animation: fadeIn 0.2s ease;
        }

        .camera-overlay[hidden] { display: none; }

        .camera-panel {
            width: min(640px, 96vw);
            background: var(--panel);
            border-radius: var(--radius-xl);
            border: 1px solid var(--line);
            padding: 1.2rem;
            box-shadow: var(--shadow-lg);
        }

        .camera-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            margin-bottom: 0.5rem;
        }

        .camera-video {
            width: 100%;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            background: #0f172a;
            margin-top: 0.5rem;
        }

        .camera-status {
            margin-top: 0.6rem;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--primary);
        }

        .camera-status.error { color: var(--danger); }

        @media (max-width: 768px) {
            .qr-row { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        (() => {
            const seedPattern = /^[A-Z2-7]+$/;
            const cameraOverlay = document.getElementById('qr-camera-overlay');
            const cameraVideo = document.getElementById('qr-camera-video');
            const cameraCanvas = document.getElementById('qr-camera-canvas');
            const cameraStatus = document.getElementById('qr-camera-status');
            const cameraClose = document.getElementById('qr-camera-close');

            const cameraState = { stream: null, rafId: null, applySecret: null };

            const normalizeSeed = (value) => {
                const cleaned = (value || '').toUpperCase().replace(/[\s-]/g, '').replace(/=+$/g, '');
                return cleaned ? (seedPattern.test(cleaned) ? cleaned : null) : null;
            };

            const extractSecret = (value) => {
                const raw = (value || '').trim();
                if (!raw) return null;
                const q = raw.match(/(?:[?&]|^)secret=([^&]+)/i);
                if (q && q[1]) { try { return normalizeSeed(decodeURIComponent(q[1])); } catch (e) { return normalizeSeed(q[1]); } }
                return normalizeSeed(raw);
            };

            const setCameraStatus = (text, error = false) => {
                if (!cameraStatus) return;
                cameraStatus.textContent = text;
                cameraStatus.classList.toggle('error', error);
            };

            const decodeQrFromFile = (file) => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onerror = () => reject(new Error('تعذر قراءة ملف الصورة.'));
                    reader.onload = () => {
                        const image = new Image();
                        image.onerror = () => reject(new Error('تعذر فتح صورة QR.'));
                        image.onload = () => {
                            if (!window.jsQR) { reject(new Error('تعذر تحميل مكتبة jsQR.')); return; }
                            const c = document.createElement('canvas');
                            c.width = image.naturalWidth || image.width;
                            c.height = image.naturalHeight || image.height;
                            const ctx = c.getContext('2d');
                            if (!ctx) { reject(new Error('تعذر تحليل الصورة.')); return; }
                            ctx.drawImage(image, 0, 0, c.width, c.height);
                            const d = ctx.getImageData(0, 0, c.width, c.height);
                            const r = window.jsQR(d.data, d.width, d.height, { inversionAttempts: 'attemptBoth' });
                            if (!r || !r.data) { reject(new Error('لم يتم العثور على رمز QR.')); return; }
                            resolve(r.data);
                        };
                        image.src = reader.result;
                    };
                    reader.readAsDataURL(file);
                });
            };

            const stopCamera = () => {
                if (cameraState.rafId) { cancelAnimationFrame(cameraState.rafId); cameraState.rafId = null; }
                if (cameraState.stream) { cameraState.stream.getTracks().forEach(t => t.stop()); cameraState.stream = null; }
                if (cameraVideo) cameraVideo.srcObject = null;
                if (cameraOverlay) cameraOverlay.hidden = true;
                setCameraStatus('');
            };

            const scanFromCameraFrame = () => {
                if (!cameraState.stream || !cameraVideo || !cameraCanvas) return;
                if (cameraVideo.readyState >= 2) {
                    const ctx = cameraCanvas.getContext('2d');
                    if (ctx) {
                        cameraCanvas.width = cameraVideo.videoWidth;
                        cameraCanvas.height = cameraVideo.videoHeight;
                        ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);
                        const d = ctx.getImageData(0, 0, cameraCanvas.width, cameraCanvas.height);
                        const r = window.jsQR(d.data, d.width, d.height, { inversionAttempts: 'attemptBoth' });
                        if (r && r.data) {
                            const s = extractSecret(r.data);
                            if (!s) setCameraStatus('تم اكتشاف QR لكنه لا يحتوي على 2FA Seed صالح.', true);
                            else { if (typeof cameraState.applySecret === 'function') cameraState.applySecret(s, 'الكاميرا'); stopCamera(); return; }
                        }
                    }
                }
                cameraState.rafId = requestAnimationFrame(scanFromCameraFrame);
            };

            const startCamera = async (applySecret, setStatus) => {
                if (!navigator.mediaDevices?.getUserMedia) { setStatus('متصفحك لا يدعم الكاميرا.', true); return; }
                stopCamera();
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } });
                    cameraState.stream = stream; cameraState.applySecret = applySecret;
                    if (cameraVideo) cameraVideo.srcObject = stream;
                    if (cameraOverlay) cameraOverlay.hidden = false;
                    setCameraStatus('جارِ البحث عن رمز QR...');
                    scanFromCameraFrame();
                } catch (e) { stopCamera(); setStatus('تعذر الوصول إلى الكاميرا.', true); }
            };

            const wireForm = (form) => {
                const secretInput = form.querySelector('[data-secret-input]');
                const payloadInput = form.querySelector('[data-qr-payload-input]');
                const fileInput = form.querySelector('[data-qr-file-input]');
                const cameraButton = form.querySelector('[data-qr-camera-btn]');
                const statusBox = form.querySelector('[data-qr-status]');
                if (!secretInput || !statusBox) return;

                const setStatus = (text, isError = false) => {
                    statusBox.textContent = text;
                    statusBox.classList.toggle('error', isError);
                };

                const applySecret = (candidate, sourceName) => {
                    if (!candidate) { setStatus('تعذر استخراج 2FA Seed صالح من ' + sourceName + '.', true); return; }
                    secretInput.value = candidate;
                    setStatus('✅ تم استخراج المفتاح السري بنجاح من ' + sourceName + '.');
                };

                if (payloadInput) payloadInput.addEventListener('change', () => applySecret(extractSecret(payloadInput.value), 'النص المدخل'));
                if (fileInput) {
                    fileInput.addEventListener('change', async () => {
                        const file = fileInput.files?.[0];
                        if (!file) return;
                        setStatus('جارِ قراءة رمز QR من الصورة...');
                        try { applySecret(extractSecret(await decodeQrFromFile(file)), 'الصورة'); }
                        catch (e) { setStatus(e.message || 'تعذر قراءة رمز QR.', true); }
                    });
                }
                if (cameraButton) cameraButton.addEventListener('click', async () => { setStatus('جارِ تجهيز الكاميرا...'); await startCamera(applySecret, setStatus); });
            };

            if (cameraClose) cameraClose.addEventListener('click', stopCamera);
            if (cameraOverlay) cameraOverlay.addEventListener('click', (e) => { if (e.target === cameraOverlay) stopCamera(); });
            window.addEventListener('beforeunload', stopCamera);
            window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && cameraOverlay && !cameraOverlay.hidden) stopCamera(); });

            document.querySelectorAll('[data-qr-secret-tool]').forEach(wireForm);
        })();
    </script>
@endpush
