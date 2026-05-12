@extends('layouts.admin')

@section('title', 'لوحة تحكم المدير')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">مركز التحكم</p>
        <div class="page-title-bar">
            <div>
                <h1>مرحبًا، {{ $admin->name }}</h1>
                <p class="text-muted">نظرة شاملة على المنصة وإحصائيات جميع التجار.</p>
            </div>
        </div>
        <div class="hero-meta">
            <span class="chip chip-brand">⚙️ مدير المنصة</span>
            <span class="chip">📅 {{ now()->format('Y-m-d') }}</span>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue">🏪</div>
            <div class="kpi-label">إجمالي التجار</div>
            <div class="kpi-value">{{ $totalMerchants }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon teal">✅</div>
            <div class="kpi-label">التجار النشطون</div>
            <div class="kpi-value">{{ $activeMerchants }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon rose">🚫</div>
            <div class="kpi-label">التجار غير النشطون</div>
            <div class="kpi-value">{{ $inactiveMerchants }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon teal">📦</div>
            <div class="kpi-label">إجمالي الحسابات</div>
            <div class="kpi-value">{{ $totalAccounts }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon blue">👥</div>
            <div class="kpi-label">إجمالي العملاء</div>
            <div class="kpi-value">{{ $totalCustomers }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon amber">🔄</div>
            <div class="kpi-label">OTP اليوم</div>
            <div class="kpi-value">{{ $otpToday }}</div>
        </div>

        @if($expiringSoon > 0)
            <div class="kpi-card" style="border-color: #fde68a;">
                <div class="kpi-icon amber">⏰</div>
                <div class="kpi-label">اشتراكات تنتهي قريبًا</div>
                <div class="kpi-value" style="color:var(--warning);">{{ $expiringSoon }}</div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="section-head">
            <div>
                <h2>آخر التجار المسجلين</h2>
                <p class="text-muted text-sm">أحدث 5 تجار في المنصة.</p>
            </div>
            <div class="title-actions">
                <a class="btn btn-primary" href="{{ route('admin.merchants.index') }}">عرض الكل</a>
                <a class="btn btn-ghost" href="{{ route('admin.merchants.index') }}">➕ إضافة تاجر</a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>الحالة</th>
                        <th>الاشتراك</th>
                        <th>الحسابات</th>
                        <th>العملاء</th>
                        <th>تاريخ التسجيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMerchants as $merchant)
                        <tr>
                            <td><span class="badge badge-neutral">#{{ $merchant->id }}</span></td>
                            <td><strong><a href="{{ route('admin.merchants.edit', $merchant->id) }}" style="color:var(--primary);">{{ $merchant->name }}</a></strong></td>
                            <td class="text-sm text-muted">{{ $merchant->email }}</td>
                            <td>
                                <span class="badge {{ $merchant->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $merchant->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                @if($merchant->subscription_end)
                                    <span class="badge {{ $merchant->subscription_end->isPast() ? 'badge-danger' : 'badge-success' }}">
                                        {{ $merchant->subscription_end->isPast() ? 'منتهي' : 'ساري' }}
                                    </span>
                                    <span class="text-sm text-muted">{{ $merchant->subscription_end->format('Y-m-d') }}</span>
                                @else
                                    <span class="badge badge-neutral">غير محدد</span>
                                @endif
                            </td>
                            <td><span class="badge badge-neutral">{{ $merchant->merchantAccounts_count ?? 0 }}</span></td>
                            <td><span class="badge badge-neutral">{{ $merchant->merchantCustomers_count ?? 0 }}</span></td>
                            <td class="text-sm text-muted">{{ optional($merchant->created_at)->format('Y-m-d') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon">🏪</div>
                                    <p>لا يوجد تجار حتى الآن.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="section-head">
            <h2>🔗 تنقل سريع</h2>
        </div>

        <div class="quick-grid">
            <a class="quick-card" href="{{ route('admin.merchants.index') }}">
                <h3>🏪 إدارة التجار</h3>
                <p>إضافة وتعديل وحذف التجار والتحكم باشتراكاتهم.</p>
                <span class="quick-link">عرض التجار ←</span>
            </a>

            <a class="quick-card" href="{{ route('admin.merchants.index') }}#add-merchant">
                <h3>➕ إضافة تاجر</h3>
                <p>إنشاء حساب تاجر جديد وتحديد صلاحية الاشتراك.</p>
                <span class="quick-link">إضافة تاجر ←</span>
            </a>
        </div>
    </div>
@endsection
