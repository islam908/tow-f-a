@extends('layouts.merchant')

@section('title', 'تعديل الحساب')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">الحسابات</p>
        <div class="page-title-bar">
            <h1>✏️ تعديل: {{ $account->label }}</h1>
            <a href="{{ route('merchant.accounts.index') }}" class="btn btn-ghost">← عودة للقائمة</a>
        </div>
    </div>

    <div class="card">
        <form method="post" action="{{ route('merchant.accounts.update', $account->id) }}" class="form-layout" data-qr-secret-tool>
            @csrf
            @method('put')

            <div class="field">
                <label for="label">اسم الحساب</label>
                <input id="label" type="text" name="label" value="{{ old('label', $account->label) }}" required>
            </div>

            <div class="field">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email', $account->email) }}">
                <span class="hint">اختياري إذا توفر اسم مستخدم</span>
            </div>

            <div class="field">
                <label for="username">اسم المستخدم</label>
                <input id="username" type="text" name="username" value="{{ old('username', $account->username) }}">
            </div>

            <div class="field">
                <label for="password">كلمة المرور</label>
                <input id="password" type="text" name="password" value="{{ old('password', $account->password_encrypted) }}" required>
            </div>

            <div class="field field-full">
                <div class="secret-section">
                    <label for="secret_key">المفتاح السري (2FA Seed)</label>
                    <input id="secret_key" type="text" name="secret_key" value="{{ old('secret_key', $account->secret_key_encrypted) }}" data-secret-input required>
                    <span class="hint">يمكنك إدخال المفتاح مباشرة، أو لصق رابط otpauth، أو استخراجه من QR.</span>

                    <div class="qr-tools">
                        <div class="field">
                            <label for="edit_qr_payload">نص QR أو رابط otpauth</label>
                            <input id="edit_qr_payload" type="text" data-qr-payload-input placeholder="otpauth://totp/...?...&secret=XXXXX">
                        </div>

                        <div class="qr-row">
                            <div class="field">
                                <label for="edit_qr_image">رفع صورة QR</label>
                                <input id="edit_qr_image" type="file" data-qr-file-input accept="image/*">
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
                <button type="submit" class="btn btn-primary">💾 حفظ التعديلات</button>
                <a href="{{ route('merchant.accounts.index') }}" class="btn btn-ghost">إلغاء</a>
            </div>
        </form>
    </div>

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

            const normalizeSeed = (v) => { const c = (v||'').toUpperCase().replace(/[\s-]/g,'').replace(/=+$/g,''); return c ? (seedPattern.test(c)?c:null) : null; };
            const extractSecret = (v) => { const r=(v||'').trim(); if(!r)return null; const q=r.match(/(?:[?&]|^)secret=([^&]+)/i); if(q&&q[1]){try{return normalizeSeed(decodeURIComponent(q[1]))}catch(e){return normalizeSeed(q[1])}} return normalizeSeed(r); };
            const setCS = (t,e)=>{ if(!cameraStatus)return; cameraStatus.textContent=t; cameraStatus.classList.toggle('error',e); };

            const decodeQr = (file) => new Promise((rs,rj)=>{
                const rd=new FileReader();
                rd.onerror=()=>rj('تعذر قراءة الملف.');
                rd.onload=()=>{
                    const img=new Image();
                    img.onerror=()=>rj('تعذر فتح الصورة.');
                    img.onload=()=>{
                        if(!window.jsQR){rj('تعذر تحميل jsQR.');return;}
                        const c=document.createElement('canvas'); c.width=img.naturalWidth||img.width; c.height=img.naturalHeight||img.height;
                        const ctx=c.getContext('2d'); if(!ctx){rj('تعذر تحليل الصورة.');return;}
                        ctx.drawImage(img,0,0); const d=ctx.getImageData(0,0,c.width,c.height);
                        const r=window.jsQR(d.data,d.width,d.height,{inversionAttempts:'attemptBoth'});
                        r&&r.data?rs(r.data):rj('لم يتم العثور على رمز QR.');
                    }; img.src=rd.result;
                }; rd.readAsDataURL(file);
            });

            const stopCam = ()=>{
                if(cameraState.rafId){cancelAnimationFrame(cameraState.rafId);cameraState.rafId=null;}
                if(cameraState.stream){cameraState.stream.getTracks().forEach(t=>t.stop());cameraState.stream=null;}
                if(cameraVideo)cameraVideo.srcObject=null;
                if(cameraOverlay)cameraOverlay.hidden=true;
                setCS('');
            };

            const scanFrame = ()=>{
                if(!cameraState.stream||!cameraVideo||!cameraCanvas)return;
                if(cameraVideo.readyState>=2){
                    const ctx=cameraCanvas.getContext('2d');
                    if(ctx){
                        cameraCanvas.width=cameraVideo.videoWidth; cameraCanvas.height=cameraVideo.videoHeight;
                        ctx.drawImage(cameraVideo,0,0); const d=ctx.getImageData(0,0,cameraCanvas.width,cameraCanvas.height);
                        const r=window.jsQR(d.data,d.width,d.height,{inversionAttempts:'attemptBoth'});
                        if(r&&r.data){
                            const s=extractSecret(r.data);
                            if(!s) setCS('تم اكتشاف QR لكنه لا يحتوي على 2FA Seed صالح.', true);
                            else{ if(typeof cameraState.applySecret==='function') cameraState.applySecret(s,'الكاميرا'); stopCam(); return; }
                        }
                    }
                }
                cameraState.rafId=requestAnimationFrame(scanFrame);
            };

            const startCam = async (apply,st)=>{
                if(!navigator.mediaDevices?.getUserMedia){st('متصفحك لا يدعم الكاميرا.',true);return;}
                stopCam();
                try{
                    const s=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}}});
                    cameraState.stream=s; cameraState.applySecret=apply;
                    if(cameraVideo)cameraVideo.srcObject=s;
                    if(cameraOverlay)cameraOverlay.hidden=false;
                    setCS('جارِ البحث عن رمز QR...'); scanFrame();
                }catch(e){stopCam();st('تعذر الوصول إلى الكاميرا.',true);}
            };

            const wire = (form)=>{
                const si=form.querySelector('[data-secret-input]'), pi=form.querySelector('[data-qr-payload-input]'), fi=form.querySelector('[data-qr-file-input]'), cb=form.querySelector('[data-qr-camera-btn]'), sb=form.querySelector('[data-qr-status]');
                if(!si||!sb)return;
                const st=(t,e=0)=>{sb.textContent=t;sb.classList.toggle('error',e);};
                const ap=(c,src)=>{if(!c){st('تعذر استخراج 2FA Seed من '+src+'.',true);return;} si.value=c; st('✅ تم استخراج المفتاح من '+src+'.');};
                if(pi)pi.addEventListener('change',()=>ap(extractSecret(pi.value),'النص'));
                if(fi)fi.addEventListener('change',async()=>{const f=fi.files?.[0];if(!f)return;st('جارِ القراءة...');try{ap(extractSecret(await decodeQr(f)),'الصورة');}catch(e){st(e.message||'فشل.',true);}});
                if(cb)cb.addEventListener('click',async()=>{st('جارِ تجهيز الكاميرا...');await startCam(ap,st);});
            };

            if(cameraClose)cameraClose.addEventListener('click',stopCam);
            if(cameraOverlay)cameraOverlay.addEventListener('click',e=>{if(e.target===cameraOverlay)stopCam();});
            window.addEventListener('beforeunload',stopCam);
            window.addEventListener('keydown',e=>{if(e.key==='Escape'&&cameraOverlay&&!cameraOverlay.hidden)stopCam();});
            document.querySelectorAll('[data-qr-secret-tool]').forEach(wire);
        })();
    </script>
@endpush
