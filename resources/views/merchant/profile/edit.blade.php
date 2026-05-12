@extends('layouts.merchant')

@section('title', 'إعدادات الحساب')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">الإعدادات</p>
        <div class="page-title-bar">
            <h1>⚙️ إعدادات الحساب</h1>
        </div>
        <div class="hero-meta">
            <span class="chip chip-brand">🔐 {{ $merchant->email }}</span>
            <span class="chip">📅 عضو منذ {{ $merchant->created_at->format('Y-m-d') }}</span>
        </div>
    </div>

    <div class="card">
        <div class="section-head">
            <h2>✏️ تعديل البيانات الشخصية</h2>
        </div>

        <form method="post" action="{{ route('merchant.profile.update') }}" class="form-layout">
            @csrf
            @method('put')

            <div class="field field-full">
                <label for="name">الاسم</label>
                <input id="name" type="text" name="name" value="{{ old('name', $merchant->name) }}" required>
            </div>

            <div class="field field-full">
                <label for="password">كلمة المرور الجديدة</label>
                <input id="password" type="text" name="password" value="" placeholder="اتركه فارغًا إن لم ترد التغيير">
                <span class="hint">اتركه فارغًا للاحتفاظ بكلمة المرور الحالية</span>
            </div>

            <div class="field field-full">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="text" name="password_confirmation" placeholder="تأكيد كلمة المرور الجديدة">
            </div>

            <div class="field field-full form-actions">
                <button type="submit" class="btn btn-primary">💾 حفظ التعديلات</button>
            </div>
        </form>
    </div>
@endsection
