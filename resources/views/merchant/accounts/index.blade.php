@extends('layouts.merchant')

@section('title', 'الحسابات')

@section('content')
    <div class="card card-hero">
        <p class="eyebrow">الحسابات</p>
        <div class="page-title-bar">
            <div>
                <h1>قائمة الحسابات</h1>
                <p class="text-muted">إدارة حسابات الاشتراكات ومفاتيح 2FA.</p>
            </div>
            <div class="title-actions">
                <a class="btn btn-primary" href="{{ route('merchant.accounts.create') }}">➕ إضافة حساب</a>
            </div>
        </div>
        <div class="hero-meta">
            <span class="chip chip-brand">📦 {{ $accounts->count() }} حساب</span>
            <span class="chip">📧 {{ $accounts->whereNotNull('email')->count() }} بريد</span>
            <span class="chip">👥 {{ $accounts->sum('customers_count') }} عميل مرتبط</span>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>البريد / اسم المستخدم</th>
                        <th>كلمة المرور</th>
                        <th>المفتاح السري</th>
                        <th>العملاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td><span class="badge badge-neutral">#{{ $loop->iteration }}</span></td>
                            <td><strong>{{ $account->label }}</strong></td>
                            <td>
                                <div class="text-sm">{{ $account->email ?? '-' }}</div>
                                @if($account->username)
                                    <div class="text-xs text-muted">{{ $account->username }}</div>
                                @endif
                            </td>
                            <td><span class="mono">{{ $account->password_encrypted }}</span></td>
                            <td><span class="mono">{{ $account->secret_key_encrypted }}</span></td>
                            <td><span class="badge badge-neutral">{{ $account->customers_count }}</span></td>
                            <td>
                                <div class="actions-cell">
                                    <a class="btn btn-secondary btn-sm" href="{{ route('merchant.accounts.edit', $account->id) }}">✏️ تعديل</a>

                                    <form method="post" action="{{ route('merchant.accounts.delete', $account->id) }}" class="delete-inline" onsubmit="return confirm('هل تريد حذف هذا الحساب؟');">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm" type="submit">🗑️ حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">📦</div>
                                    <p>لا توجد حسابات حتى الآن.</p>
                                    <div class="empty-actions">
                                        <a class="btn btn-primary" href="{{ route('merchant.accounts.create') }}">➕ إضافة أول حساب</a>
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
