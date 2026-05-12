@extends('layouts.admin')

@section('title', 'تعديل التاجر')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">إدارة التجار</p>
        <div class="page-title-bar">
            <h1>✏️ تعديل: {{ $merchant->name }}</h1>
            <a href="{{ route('admin.merchants.index') }}" class="btn btn-ghost">← عودة للقائمة</a>
        </div>
    </div>

    <div class="card">
        <form method="post" action="{{ route('admin.merchants.update', $merchant->id) }}" class="form-layout">
            @csrf
            @method('put')

            <div class="field">
                <label for="name">الاسم</label>
                <input id="name" type="text" name="name" value="{{ old('name', $merchant->name) }}" required>
            </div>

            <div class="field">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email', $merchant->email) }}" required>
            </div>

            <div class="field">
                <label for="password">كلمة المرور الجديدة</label>
                <input id="password" type="text" name="password" value="" placeholder="اتركه فارغًا إن لم ترد التغيير">
                <span class="hint">اتركه فارغًا للاحتفاظ بكلمة المرور الحالية</span>
            </div>

            <div class="field">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="text" name="password_confirmation" placeholder="تأكيد كلمة المرور الجديدة">
            </div>

            <div class="field">
                <label for="subscription_start">بداية الاشتراك</label>
                <input id="subscription_start" type="date" name="subscription_start" value="{{ old('subscription_start', $merchant->subscription_start?->format('Y-m-d')) }}">
            </div>

            <div class="field">
                <label for="subscription_end">نهاية الاشتراك</label>
                <input id="subscription_end" type="date" name="subscription_end" value="{{ old('subscription_end', $merchant->subscription_end?->format('Y-m-d')) }}">
            </div>

            <div class="field field-full">
                <label class="inline-label">
                    <input type="checkbox" name="is_active" value="1" {{ $merchant->is_active ? 'checked' : '' }}>
                    <span>التاجر نشط</span>
                </label>
            </div>

            <div class="field field-full form-actions">
                <button type="submit" class="btn btn-primary">💾 حفظ التعديلات</button>
                <a href="{{ route('admin.merchants.index') }}" class="btn btn-ghost">إلغاء</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="section-head">
            <h2>⚡ إجراءات سريعة</h2>
        </div>

        <div class="quick-actions">
            <form method="post" action="{{ route('admin.merchants.toggle', $merchant->id) }}" class="action-inline" onsubmit="return confirm('هل تريد {{ $merchant->is_active ? 'إيقاف' : 'تفعيل' }} هذا التاجر؟');">
                @csrf
                <button class="btn btn-{{ $merchant->is_active ? 'danger' : 'success' }}" type="submit">
                    {{ $merchant->is_active ? '⛔ إيقاف التاجر' : '✅ تفعيل التاجر' }}
                </button>
            </form>

            <form method="post" action="{{ route('admin.merchants.delete', $merchant->id) }}" class="action-inline" onsubmit="return confirm('هل تريد حذف هذا التاجر؟ سيتم حذف جميع حساباته وعملائه.');">
                @csrf
                @method('delete')
                <button class="btn btn-danger" type="submit">🗑️ حذف التاجر</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="section-head">
            <h2>📊 إحصائيات التاجر</h2>
        </div>

        <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);">
            <div class="kpi-card">
                <div class="kpi-icon teal">📦</div>
                <div class="kpi-label">الحسابات</div>
                <div class="kpi-value">{{ $accountCount }}</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon blue">👥</div>
                <div class="kpi-label">العملاء</div>
                <div class="kpi-value">{{ $customerCount }}</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon amber">🔄</div>
                <div class="kpi-label">إجمالي OTP</div>
                <div class="kpi-value">{{ $otpCount }}</div>
            </div>
        </div>

        @if($recentCustomers->isNotEmpty())
            <div style="margin-top:1rem;">
                <h3 style="margin-bottom:0.5rem;font-size:0.95rem;">آخر العملاء النشطون</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العميل</th>
                                <th>الحساب</th>
                                <th>الاستخدام</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCustomers as $customer)
                                <tr>
                                    <td><span class="badge badge-neutral">#{{ $loop->iteration }}</span></td>
                                    <td>{{ $customer->name }}</td>
                                    <td class="text-sm">{{ $customer->account?->label ?? '-' }}</td>
                                    <td>{{ $customer->usage_count }}/{{ $customer->usage_limit }}</td>
                                    <td>
                                        <span class="badge {{ $customer->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .quick-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .action-inline { display: inline; }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.9rem;
        }
    </style>
@endpush
