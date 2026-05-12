@extends('layouts.merchant')

@section('title', 'العملاء')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">العملاء</p>
        <div class="page-title-bar">
            <div>
                <h1>قائمة العملاء</h1>
                <p class="text-muted">إدارة العملاء، التحكم بالرصيد، وإرسال الروابط الخاصة.</p>
            </div>
            <div class="title-actions">
                <a class="btn btn-ghost" href="{{ route('merchant.customers.bulk') }}">📋 إنشاء بالجملة</a>
                <a class="btn btn-primary" href="{{ route('merchant.customers.create') }}">➕ إضافة عميل</a>
            </div>
        </div>
        <div class="hero-meta">
            <span class="chip chip-brand">👥 {{ $customers->count() }} عميل</span>
            <span class="chip">✅ {{ $customers->where('is_active', true)->count() }} نشط</span>
            <span class="chip">📊 {{ $customers->sum('usage_limit') - $customers->sum('usage_count') }} رصيد متاح</span>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>الحساب</th>
                        <th>الرابط</th>
                        <th>الاستخدام</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        @php
                            $usagePct = $customer->usage_limit > 0 ? ($customer->usage_count / $customer->usage_limit) * 100 : 0;
                            $barClass = $usagePct < 50 ? 'low' : ($usagePct < 80 ? 'medium' : 'high');
                        @endphp
                        <tr>
                            <td><span class="badge badge-neutral">#{{ $loop->iteration }}</span></td>
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td class="text-sm">{{ $customer->account?->label ?? '-' }}</td>
                            <td>
                                <div class="link-actions">
                                    <a class="btn btn-ghost btn-xs" href="{{ route('public.info', $customer->token) }}" target="_blank" rel="noopener noreferrer">🔗 فتح</a>
                                    <button type="button" class="btn btn-secondary btn-xs copy-link-btn" data-link="{{ route('public.info', $customer->token) }}">📋 نسخ</button>
                                </div>
                                <small class="copy-feedback" data-copy-feedback></small>
                            </td>
                            <td>
                                <div class="usage-bar">
                                    <span class="usage-track">
                                        <span class="usage-fill {{ $barClass }}" style="width: {{ min(100, $usagePct) }}%"></span>
                                    </span>
                                    <span class="usage-text">{{ $customer->usage_count }}/{{ $customer->usage_limit }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $customer->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a class="btn btn-secondary btn-xs" href="{{ route('merchant.customers.edit', $customer->id) }}">✏️</a>
                                    <span class="text-xs text-muted" style="line-height:1.8;">تعديل</span>

                                    <form method="post" action="{{ route('merchant.customers.renew', $customer->id) }}" class="action-inline" onsubmit="return confirm('هل تريد تجديد رصيد هذا العميل؟');">
                                        @csrf
                                        <button class="btn btn-ghost btn-xs" type="submit">🔄</button>
                                    </form>
                                    <span class="text-xs text-muted" style="line-height:1.8;">تجديد</span>

                                    <form method="post" action="{{ route('merchant.customers.delete', $customer->id) }}" class="delete-inline" onsubmit="return confirm('هل تريد حذف هذا العميل؟');">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-xs" type="submit">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">👥</div>
                                    <p>لا يوجد عملاء حتى الآن.</p>
                                    <div class="empty-actions">
                                        <a class="btn btn-primary" href="{{ route('merchant.customers.create') }}">➕ إضافة عميل</a>
                                        <a class="btn btn-ghost" href="{{ route('merchant.customers.bulk') }}">📋 إنشاء بالجملة</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .link-actions { display: flex; gap: 0.3rem; flex-wrap: wrap; }
        .copy-feedback { font-size: 0.75rem; margin-top: 0.2rem; display: block; }

        .usage-bar {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .usage-track {
            width: 55px;
            height: 5px;
            border-radius: 999px;
            background: var(--line);
            overflow: hidden;
        }

        .usage-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        .usage-fill.low { background: var(--success); }
        .usage-fill.medium { background: var(--warning); }
        .usage-fill.high { background: var(--danger); }

        .usage-text { font-size: 0.82rem; color: var(--ink-soft); font-weight: 600; }

        .actions-cell { display: flex; align-items: center; gap: 0.2rem; flex-wrap: wrap; }
        .action-inline, .delete-inline { display: inline; }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            document.querySelectorAll('.copy-link-btn').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const link = btn.dataset.link;
                    const fb = btn.closest('td')?.querySelector('[data-copy-feedback]');
                    if (!link) return;
                    try {
                        await navigator.clipboard.writeText(link);
                        if (fb) { fb.textContent = '✅ تم النسخ'; fb.style.color = 'var(--success)'; }
                    } catch (e) {
                        if (fb) { fb.textContent = '❌ فشل'; fb.style.color = 'var(--danger)'; }
                    }
                });
            });
        })();
    </script>
@endpush
