@extends('layouts.admin')

@section('title', 'إدارة التجار')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">إدارة التجار</p>
        <div class="page-title-bar">
            <div>
                <h1>جميع التجار المسجلين</h1>
                <p class="text-muted">إضافة وتعديل وحذف التجار، والتحكم في اشتراكاتهم.</p>
            </div>
        </div>
        <div class="hero-meta">
            <span class="chip chip-brand">🏪 {{ $merchants->count() }} تاجر</span>
            <span class="chip">✅ {{ $merchants->where('is_active', true)->count() }} نشط</span>
            <span class="chip">🚫 {{ $merchants->where('is_active', false)->count() }} غير نشط</span>
        </div>
    </div>

    <div class="card" id="add-merchant">
        <div class="section-head">
            <h2>➕ إضافة تاجر جديد</h2>
        </div>

        <form method="post" action="{{ route('admin.merchants.store') }}" class="form-layout">
            @csrf

            <div class="field">
                <label for="name">الاسم</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="اسم التاجر" required>
            </div>

            <div class="field">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="merchant@example.com" required>
            </div>

            <div class="field">
                <label for="password">كلمة المرور</label>
                <input id="password" type="text" name="password" value="{{ old('password') }}" placeholder="أدخل كلمة المرور" required>
            </div>

            <div class="field">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="text" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
            </div>

            <div class="field">
                <label for="subscription_start">بداية الاشتراك</label>
                <input id="subscription_start" type="date" name="subscription_start" value="{{ old('subscription_start', now()->toDateString()) }}">
            </div>

            <div class="field">
                <label for="subscription_end">نهاية الاشتراك</label>
                <input id="subscription_end" type="date" name="subscription_end" value="{{ old('subscription_end', now()->addYear()->toDateString()) }}">
            </div>

            <div class="field field-full">
                <label class="inline-label">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>التاجر نشط</span>
                </label>
            </div>

            <div class="field field-full form-actions">
                <button type="submit" class="btn btn-primary">➕ إضافة التاجر</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="section-head">
            <h2>قائمة التجار</h2>
            <div class="title-actions">
                <form method="get" action="{{ route('admin.merchants.index') }}" class="search-form">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="🔍 بحث بالاسم أو البريد..." style="padding:0.45rem 0.75rem;border:1px solid var(--border);border-radius:6px;font-size:0.85rem;width:240px;">
                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                    @if(!empty($search))
                        <a href="{{ route('admin.merchants.index') }}" class="btn btn-ghost btn-sm">إلغاء</a>
                    @endif
                </form>
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
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchants as $merchant)
                        <tr>
                            <td><span class="badge badge-neutral">#{{ $merchant->id }}</span></td>
                            <td><strong>{{ $merchant->name }}</strong></td>
                            <td class="text-sm">{{ $merchant->email }}</td>
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
                                    <div class="text-xs text-muted" style="margin-top:0.15rem;">
                                        {{ $merchant->subscription_start?->format('Y-m-d') ?? '?' }} → {{ $merchant->subscription_end->format('Y-m-d') }}
                                    </div>
                                @else
                                    <span class="badge badge-neutral">غير محدد</span>
                                @endif
                            </td>
                            <td><span class="badge badge-neutral">{{ $merchant->merchantAccounts->count() }}</span></td>
                            <td><span class="badge badge-neutral">{{ $merchant->merchantCustomers->count() }}</span></td>
                            <td>
                                <div class="actions-cell">
                                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.merchants.edit', $merchant->id) }}">✏️ تعديل</a>

                                    <form method="post" action="{{ route('admin.merchants.delete', $merchant->id) }}" class="delete-inline" onsubmit="return confirm('هل تريد حذف هذا التاجر؟');">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm" type="submit">🗑️ حذف</button>
                                    </form>
                                </div>
                            </td>
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
@endsection
