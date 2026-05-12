@extends('layouts.merchant')

@section('title', 'لوحة تحكم التاجر')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">مركز التحكم</p>
        <div class="page-title-bar">
            <div>
                <h1>مرحبًا، {{ $merchant->name }}</h1>
                <p class="text-muted">نظرة سريعة على أداء حساباتك وعملائك اليوم.</p>
            </div>
        </div>
        <div class="hero-meta">
            <span class="chip chip-brand">🚀 تاجر</span>
            <span class="chip">📅 حتى {{ optional($merchant->subscription_end)->format('Y-m-d') ?? 'غير محدد' }}</span>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon teal">📦</div>
            <div class="kpi-label">إجمالي الحسابات</div>
            <div class="kpi-value">{{ $accountCount }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon teal">👥</div>
            <div class="kpi-label">إجمالي العملاء</div>
            <div class="kpi-value">{{ $customerCount }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon {{ $exhaustedCustomers > 0 ? 'rose' : 'teal' }}">✅</div>
            <div class="kpi-label">العملاء النشطون</div>
            <div class="kpi-value">{{ $activeCustomerCount }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon blue">🔄</div>
            <div class="kpi-label">OTP اليوم</div>
            <div class="kpi-value">{{ $otpToday }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon blue">📆</div>
            <div class="kpi-label">OTP هذا الشهر</div>
            <div class="kpi-value">{{ $otpThisMonth }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon {{ $exhaustedCustomers > 0 ? 'amber' : 'teal' }}">⚠️</div>
            <div class="kpi-label">عملاء نفد رصيدهم</div>
            <div class="kpi-value" style="color: {{ $exhaustedCustomers > 0 ? 'var(--warning)' : 'var(--ink)' }}">{{ $exhaustedCustomers }}</div>
        </div>
    </div>

    @if($topAccounts->isNotEmpty())
        <div class="card">
            <div class="section-head">
                <div>
                    <h2>أكثر الحسابات ارتباطًا بالعملاء</h2>
                    <p class="text-muted text-sm">الحسابات التي لديها أكبر عدد من العملاء.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الحساب</th>
                            <th>عدد العملاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topAccounts as $index => $account)
                            <tr>
                                <td><span class="badge badge-neutral">{{ $index + 1 }}</span></td>
                                <td><strong>{{ $account->label }}</strong></td>
                                <td>{{ $account->customers_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="section-head">
            <div>
                <h2>تنقل سريع</h2>
                <p class="text-muted text-sm">اختر المسار الذي تريد العمل عليه الآن.</p>
            </div>
        </div>

        <div class="quick-grid">
            <a class="quick-card" href="{{ route('merchant.accounts.index') }}">
                <h3>📦 الحسابات</h3>
                <p>إدارة الحسابات وربط 2FA Seed.</p>
                <span class="quick-link">عرض الحسابات ←</span>
            </a>

            <a class="quick-card" href="{{ route('merchant.accounts.create') }}">
                <h3>➕ إضافة حساب</h3>
                <p>إضافة حساب اشتراك جديد مع مفتاح 2FA.</p>
                <span class="quick-link">إضافة حساب ←</span>
            </a>

            <a class="quick-card" href="{{ route('merchant.customers.index') }}">
                <h3>👥 العملاء</h3>
                <p>إدارة العملاء والتحكم بالرصيد.</p>
                <span class="quick-link">عرض العملاء ←</span>
            </a>

            <a class="quick-card" href="{{ route('merchant.customers.bulk') }}">
                <h3>📋 إنشاء بالجملة</h3>
                <p>إنشاء عدة عملاء دفعة واحدة بسرعة.</p>
                <span class="quick-link">إنشاء عملاء ←</span>
            </a>
        </div>
    </div>
@endsection
