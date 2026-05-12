@extends('layouts.merchant')

@section('title', 'إنشاء عملاء بالجملة')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">العملاء</p>
        <div class="page-title-bar">
            <h1>📋 إنشاء عملاء بالجملة</h1>
            <a href="{{ route('merchant.customers.index') }}" class="btn btn-ghost">← عودة للقائمة</a>
        </div>
        <p class="text-muted">أنشئ عدة عملاء دفعة واحدة لنفس الحساب وبحد استخدام موحد.</p>
    </div>

    <div class="card">
        @if($accounts->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <p>يجب إضافة حساب واحد على الأقل قبل إنشاء العملاء.</p>
                <div class="empty-actions">
                    <a class="btn btn-primary" href="{{ route('merchant.accounts.create') }}">➕ إضافة حساب جديد</a>
                </div>
            </div>
        @else
            <form method="post" action="{{ route('merchant.customers.store-bulk') }}" class="form-layout">
                @csrf

                <div class="field">
                    <label for="account_id">الحساب</label>
                    <select id="account_id" name="account_id" required>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="name_prefix">بادئة الاسم</label>
                    <input id="name_prefix" type="text" name="name_prefix" value="{{ old('name_prefix', 'عميل') }}" placeholder="مثال: عميل باقة" required>
                    <span class="hint">سيتم إضافة رقم تسلسلي (مثال: عميل #1، عميل #2)</span>
                </div>

                <div class="field">
                    <label for="count">العدد</label>
                    <input id="count" type="number" min="1" max="100" name="count" value="{{ old('count', 5) }}" required>
                </div>

                <div class="field">
                    <label for="usage_limit">حد الاستخدام لكل عميل</label>
                    <input id="usage_limit" type="number" min="1" name="usage_limit" value="{{ old('usage_limit', 5) }}" required>
                </div>

                <div class="field">
                    <label for="is_active">الحالة</label>
                    <select id="is_active" name="is_active">
                        <option value="1" selected>✅ نشط</option>
                        <option value="0">⛔ غير نشط</option>
                    </select>
                </div>

                <div class="field field-full form-actions">
                    <button type="submit" class="btn btn-secondary">📋 إنشاء {{ old('count', 5) }} عميل</button>
                    <a href="{{ route('merchant.customers.index') }}" class="btn btn-ghost">إلغاء</a>
                </div>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="section-head">
            <h2>💡 معلومات</h2>
        </div>
        <ul style="margin-right:1.2rem;color:var(--ink-soft);font-size:0.88rem;line-height:1.8;">
            <li>الحد الأقصى لإنشاء العملاء في الدفعة الواحدة هو <strong>100 عميل</strong>.</li>
            <li>جميع العملاء يتم إنشاؤهم بنفس حد الاستخدام والحالة.</li>
            <li>كل عميل يحصل على <strong>رابط فريد</strong> للوصول إلى بيانات الحساب.</li>
            <li>يمكنك تعديل كل عميل لاحقًا من <a href="{{ route('merchant.customers.index') }}" style="color:var(--primary);font-weight:700;">قائمة العملاء</a>.</li>
        </ul>
    </div>
@endsection
