@extends('layouts.merchant')

@section('title', 'إضافة عميل جديد')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">العملاء</p>
        <div class="page-title-bar">
            <h1>➕ إضافة عميل جديد</h1>
            <a href="{{ route('merchant.customers.index') }}" class="btn btn-ghost">← عودة للقائمة</a>
        </div>
        <p class="text-muted">اربط العميل بأحد الحسابات وحدد حد الاستخدام.</p>
    </div>

    <div class="card">
        @if($accounts->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <p>يجب إضافة حساب واحد على الأقل قبل إضافة العملاء.</p>
                <div class="empty-actions">
                    <a class="btn btn-primary" href="{{ route('merchant.accounts.create') }}">➕ إضافة حساب جديد</a>
                </div>
            </div>
        @else
            <form method="post" action="{{ route('merchant.customers.store') }}" class="form-layout">
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
                    <label for="name">اسم العميل</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="مثال: عميل باقة شهرية" required>
                </div>

                <div class="field">
                    <label for="usage_limit">حد الاستخدام</label>
                    <input id="usage_limit" type="number" min="1" name="usage_limit" value="{{ old('usage_limit', 5) }}" required>
                </div>

                <div class="field">
                    <label for="is_active">الحالة</label>
                    <select id="is_active" name="is_active">
                        <option value="1">✅ نشط</option>
                        <option value="0">⛔ غير نشط</option>
                    </select>
                </div>

                <div class="field field-full form-actions">
                    <button type="submit" class="btn btn-primary">💾 حفظ العميل</button>
                    <a href="{{ route('merchant.customers.index') }}" class="btn btn-ghost">إلغاء</a>
                </div>
            </form>
        @endif
    </div>
@endsection
