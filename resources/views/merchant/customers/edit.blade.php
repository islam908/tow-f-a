@extends('layouts.merchant')

@section('title', 'تعديل العميل')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">العملاء</p>
        <div class="page-title-bar">
            <h1>✏️ تعديل: {{ $customer->name }}</h1>
            <a href="{{ route('merchant.customers.index') }}" class="btn btn-ghost">← عودة للقائمة</a>
        </div>
    </div>

    <div class="card">
        <form method="post" action="{{ route('merchant.customers.update', $customer->id) }}" class="form-layout">
            @csrf
            @method('put')

            <div class="field">
                <label for="account_id">الحساب</label>
                <select id="account_id" name="account_id" required>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ $account->id === $customer->account_id ? 'selected' : '' }}>
                            {{ $account->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="name">اسم العميل</label>
                <input id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required>
            </div>

            <div class="field">
                <label for="usage_limit">حد الاستخدام</label>
                <input id="usage_limit" type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $customer->usage_limit) }}" required>
            </div>

            <div class="field">
                <label for="usage_count">عدد الاستخدامات الحالية</label>
                <input id="usage_count" type="number" min="0" name="usage_count" value="{{ old('usage_count', $customer->usage_count) }}" required>
            </div>

            <div class="field">
                <label for="is_active">الحالة</label>
                <select id="is_active" name="is_active">
                    <option value="1" {{ $customer->is_active ? 'selected' : '' }}>✅ نشط</option>
                    <option value="0" {{ !$customer->is_active ? 'selected' : '' }}>⛔ غير نشط</option>
                </select>
            </div>

            <div class="field field-full form-actions">
                <button type="submit" class="btn btn-primary">💾 حفظ التعديلات</button>
                <a href="{{ route('merchant.customers.index') }}" class="btn btn-ghost">إلغاء</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="section-head">
            <h2>⚡ إجراءات سريعة</h2>
        </div>

        <div class="quick-actions">
            <form method="post" action="{{ route('merchant.customers.renew', $customer->id) }}" class="action-inline" onsubmit="return confirm('هل تريد تجديد رصيد هذا العميل؟ سيتم تصفير الاستخدامات.');">
                @csrf
                <button class="btn btn-success" type="submit">🔄 تجديد الرصيد</button>
            </form>

            <form method="post" action="{{ route('merchant.customers.regenerate-token', $customer->id) }}" class="action-inline" onsubmit="return confirm('هل تريد إعادة توليد رابط العميل؟ سيفقد القديم صلاحيته.');">
                @csrf
                <button class="btn btn-ghost" type="submit">🔗 إعادة الرابط</button>
            </form>

            <form method="post" action="{{ route('merchant.customers.delete', $customer->id) }}" class="action-inline" onsubmit="return confirm('هل تريد حذف هذا العميل؟ لا يمكن التراجع.');">
                @csrf
                @method('delete')
                <button class="btn btn-danger" type="submit">🗑️ حذف العميل</button>
            </form>
        </div>
    </div>

    @if($customer->token)
        <div class="card">
            <div class="section-head">
                <h2>🔗 رابط العميل</h2>
            </div>
            <div class="token-display">
                <span class="mono">{{ route('public.info', $customer->token) }}</span>
                <button type="button" class="btn btn-secondary copy-link" data-link="{{ route('public.info', $customer->token) }}">📋 نسخ</button>
            </div>
            <small class="copy-feedback" style="display:block;margin-top:0.4rem;font-size:0.82rem;"></small>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .quick-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .action-inline { display: inline; }

        .token-display {
            display: flex;
            gap: 0.6rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .token-display .mono {
            flex: 1;
            padding: 0.5rem 0.7rem;
            font-size: 0.8rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const btn = document.querySelector('.copy-link');
            const fb = document.querySelector('.copy-feedback');
            if (btn && fb) {
                btn.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(btn.dataset.link);
                        fb.textContent = '✅ تم نسخ الرابط بنجاح.';
                        fb.style.color = 'var(--success)';
                    } catch (e) {
                        fb.textContent = '❌ تعذر النسخ التلقائي.';
                        fb.style.color = 'var(--danger)';
                    }
                });
            }
        })();
    </script>
@endpush
