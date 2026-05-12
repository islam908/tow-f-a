<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'بوابة التاجر')</title>
    <meta name="description" content="@yield('meta_description', 'منصة OTP Hub لإدارة الحسابات وتأمين التحقق بخطوتين')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'بوابة التاجر')">
    <meta property="og:description" content="@yield('meta_description', 'منصة OTP Hub لإدارة الحسابات وتأمين التحقق بخطوتين')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('meta_image', asset('og-image.svg'))">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'بوابة التاجر')">
    <meta name="twitter:description" content="@yield('meta_description', 'منصة OTP Hub لإدارة الحسابات وتأمين التحقق بخطوتين')">
    <meta name="twitter:image" content="@yield('meta_image', asset('og-image.svg'))">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Changa:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #0f766e;
            --primary-dark: #0d5e57;
            --primary-light: #14b8a6;
            --primary-bg: #ecfffb;
            --primary-glow: rgba(15, 118, 110, 0.12);
            --danger: #e11d48;
            --danger-bg: #fff1f2;
            --success: #16a34a;
            --success-bg: #f0fdf4;
            --warning: #d97706;
            --warning-bg: #fffbeb;
            --ink: #0f172a;
            --ink-2: #1e293b;
            --ink-3: #334155;
            --ink-soft: #64748b;
            --ink-muted: #94a3b8;
            --line: #e2e8f0;
            --line-2: #f1f5f9;
            --panel: #ffffff;
            --paper: #f8fafc;
            --sidebar-w: 270px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
            --shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 12px 40px rgba(15, 23, 42, 0.12);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html {
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Cairo', sans-serif;
            color: var(--ink);
            line-height: 1.6;
            background: var(--paper);
            min-height: 100vh;
            display: flex;
        }

        a { color: inherit; text-decoration: none; }
        h1, h2, h3, h4 { font-family: 'Changa', sans-serif; line-height: 1.3; }
        h1 { font-size: clamp(1.35rem, 2.2vw, 1.85rem); }
        h2 { font-size: clamp(1.1rem, 1.6vw, 1.4rem); }
        h3 { font-size: 1.1rem; }
        p { margin: 0; }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--ink-muted); border-radius: 9px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ink-soft); }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: var(--panel);
            border-left: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            z-index: 200;
            transition: transform var(--transition);
        }

        .sidebar-brand {
            padding: 1.2rem 1.2rem 0.8rem;
            border-bottom: 1px solid var(--line);
        }

        .sidebar-brand .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sidebar-brand .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .sidebar-brand .brand-text {
            display: grid;
        }

        .sidebar-brand .brand-kicker {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--ink-muted);
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .sidebar-brand .brand-title {
            font-family: 'Changa', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 0.7rem 0.7rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .nav-section {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--ink-muted);
            padding: 0.8rem 0.8rem 0.35rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.8rem;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--ink-3);
            transition: all var(--transition);
            position: relative;
        }

        .nav-item:hover {
            background: var(--line-2);
            color: var(--ink);
        }

        .nav-item.is-active {
            background: var(--primary-bg);
            color: var(--primary-dark);
            font-weight: 700;
        }

        .nav-item.is-active::before {
            content: '';
            position: absolute;
            right: -7px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }

        .nav-item .nav-icon {
            font-size: 1.05rem;
            width: 1.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-footer {
            border-top: 1px solid var(--line);
            padding: 0.8rem 1rem;
            display: grid;
            gap: 0.6rem;
        }

        .sidebar-footer .user-card {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sidebar-footer .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-footer .user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-footer .user-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer .user-sub {
            font-size: 0.72rem;
            color: var(--ink-muted);
        }

        .sidebar-footer .logout-btn {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 0.5rem;
            background: var(--panel);
            color: var(--ink-soft);
            font-family: inherit;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .sidebar-footer .logout-btn:hover {
            background: var(--danger-bg);
            border-color: var(--danger);
            color: var(--danger);
        }

        /* ===== MAIN ===== */
        .main {
            flex: 1;
            margin-right: var(--sidebar-w);
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-header {
            display: none;
            position: sticky;
            top: 0;
            z-index: 150;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--line);
            padding: 0.7rem 1rem;
            align-items: center;
            justify-content: space-between;
        }

        .main-header .mobile-brand {
            font-family: 'Changa', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--ink);
        }

        .sidebar-toggle {
            border: none;
            background: none;
            font-size: 1.4rem;
            cursor: pointer;
            padding: 0.25rem;
            color: var(--ink);
            border-radius: var(--radius-sm);
            transition: background var(--transition);
        }

        .sidebar-toggle:hover {
            background: var(--line-2);
        }

        .container {
            width: min(1200px, 100%);
            margin-inline: auto;
            padding: 1.5rem 1.5rem 2.5rem;
        }

        .page-content {
            display: grid;
            gap: 1.2rem;
        }

        /* ===== CARDS & PANELS ===== */
        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-hero {
            background: linear-gradient(135deg, var(--panel) 0%, #fafffd 100%);
            border-color: #ccddd7;
            position: relative;
            overflow: hidden;
        }

        .card-hero::before {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(15, 118, 110, 0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-hero::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .page-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .page-title-bar h1 {
            margin: 0;
        }

        .eyebrow {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.15rem;
        }

        .text-muted {
            color: var(--ink-soft);
            font-size: 0.9rem;
        }

        .text-sm { font-size: 0.84rem; }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.7rem;
            position: relative;
            z-index: 1;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--panel);
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--ink-3);
        }

        .chip-brand {
            border-color: #a7d4cb;
            background: var(--primary-bg);
            color: var(--primary-dark);
        }

        .title-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            border: none;
            border-radius: var(--radius-sm);
            padding: 0.55rem 1rem;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: all var(--transition);
            white-space: nowrap;
            line-height: 1.3;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: var(--ink-2);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: transparent;
            color: var(--ink-3);
            border: 1px solid var(--line);
        }

        .btn-ghost:hover {
            background: var(--line-2);
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background: #be123c;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(225, 29, 72, 0.2);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-success:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.38rem 0.7rem;
            font-size: 0.82rem;
        }

        .btn-xs {
            padding: 0.28rem 0.5rem;
            font-size: 0.76rem;
            border-radius: 6px;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* ===== FORMS ===== */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-layout .field-full {
            grid-column: 1 / -1;
        }

        .field {
            display: grid;
            gap: 0.35rem;
        }

        .field label {
            font-size: 0.83rem;
            font-weight: 700;
            color: var(--ink-2);
        }

        .field .hint {
            font-size: 0.78rem;
            color: var(--ink-muted);
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 0.6rem 0.75rem;
            background: var(--panel);
            color: var(--ink);
            font-family: inherit;
            font-size: 0.9rem;
            transition: all var(--transition);
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .field input::placeholder {
            color: var(--ink-muted);
        }

        .field select {
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        /* ===== TABLES ===== */
        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--panel);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        thead th {
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--ink-soft);
            background: var(--line-2);
            padding: 0.7rem 0.8rem;
            text-align: right;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tbody td {
            padding: 0.65rem 0.8rem;
            border-bottom: 1px solid var(--line-2);
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #fafdfc;
        }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .badge-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid #fecdd3;
        }

        .badge-warning {
            background: var(--warning-bg);
            color: var(--warning);
            border: 1px solid #fde68a;
        }

        .badge-neutral {
            background: var(--line-2);
            color: var(--ink-soft);
            border: 1px solid var(--line);
        }

        /* ===== KPI GRID ===== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.9rem;
        }

        .kpi-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.1rem;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
        }

        .kpi-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .kpi-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-bottom: 0.6rem;
        }

        .kpi-icon.teal {
            background: var(--primary-bg);
            color: var(--primary);
        }

        .kpi-icon.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .kpi-icon.amber {
            background: #fffbeb;
            color: #d97706;
        }

        .kpi-icon.rose {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .kpi-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--ink-soft);
            margin-bottom: 0.2rem;
        }

        .kpi-value {
            font-family: 'Changa', sans-serif;
            font-size: clamp(1.4rem, 2vw, 1.8rem);
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
        }

        /* ===== MONO ===== */
        .mono {
            direction: ltr;
            text-align: left;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 0.8rem;
            background: var(--line-2);
            border: 1px dashed var(--line);
            padding: 0.15rem 0.35rem;
            border-radius: 6px;
            display: inline-block;
            max-width: 100%;
            overflow: auto;
            word-break: break-all;
        }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: var(--radius);
            padding: 0.75rem 0.9rem;
            border: 1px solid;
            font-size: 0.88rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-success {
            color: var(--success);
            border-color: #bbf7d0;
            background: var(--success-bg);
        }

        .alert-error {
            color: var(--danger);
            border-color: #fecdd3;
            background: var(--danger-bg);
        }

        .alert ul {
            margin: 0.4rem 1.2rem 0 0;
            padding: 0;
        }

        .alert li {
            margin-bottom: 0.15rem;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--ink-soft);
        }

        .empty-state .empty-icon {
            font-size: 2.5rem;
            margin-bottom: 0.6rem;
            opacity: 0.5;
        }

        .empty-state p {
            margin-bottom: 0.8rem;
        }

        .empty-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ===== USAGE BAR ===== */
        .usage-bar {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .usage-track {
            width: 60px;
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

        .usage-text {
            font-size: 0.84rem;
            color: var(--ink-soft);
            font-weight: 600;
        }

        /* ===== LINK ACTIONS ===== */
        .link-actions {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .copy-feedback {
            font-size: 0.78rem;
            margin-top: 0.25rem;
            display: block;
        }

        /* ===== ACTIONS CELL ===== */
        .actions-cell {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .action-inline { display: inline; }
        .delete-inline { display: inline; }

        /* ===== SECTION HEAD ===== */
        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .section-head h2 {
            margin: 0;
        }

        /* ===== QUICK GRID ===== */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.8rem;
        }

        .quick-card {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1rem;
            background: var(--panel);
            transition: all var(--transition);
            display: grid;
            gap: 0.3rem;
        }

        .quick-card:hover {
            border-color: var(--primary-light);
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .quick-card h3 {
            font-size: 0.95rem;
            color: var(--ink);
        }

        .quick-card p {
            font-size: 0.84rem;
            color: var(--ink-soft);
        }

        .quick-card .quick-link {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 0.3rem;
        }

        /* ===== TOKEN DISPLAY ===== */
        .token-display {
            display: flex;
            gap: 0.6rem;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 0.6rem;
        }

        .token-display .mono {
            flex: 1;
            padding: 0.5rem 0.7rem;
            font-size: 0.8rem;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .page-content>* {
            animation: fadeUp 0.4s ease both;
        }

        .page-content>*:nth-child(2) { animation-delay: 0.05s; }
        .page-content>*:nth-child(3) { animation-delay: 0.1s; }
        .page-content>*:nth-child(4) { animation-delay: 0.15s; }
        .page-content>*:nth-child(5) { animation-delay: 0.2s; }

        /* ===== OVERLAY ===== */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 250;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .overlay.is-visible {
            display: block;
            opacity: 1;
        }

        /* ===== RESPONSIVE ===== */

        @media (max-width: 1024px) {
            .form-layout { grid-template-columns: 1fr; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 820px) {
            body { 
                flex-direction: column;
                min-height: 100vh;
                overflow-x: hidden;
            }

            .sidebar {
                position: fixed;
                top: 0;
                right: -100%;
                bottom: 0;
                width: 280px;
                max-width: 80%;
                transform: translateX(0);
                box-shadow: none;
                z-index: 300;
                transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.is-open {
                right: 0;
                box-shadow: -4px 0 24px rgba(15, 23, 42, 0.15);
            }

            .main {
                margin-right: 0 !important;
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .main-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.5rem 1rem;
                height: 60px;
                background: var(--panel);
                border-bottom: 1px solid var(--line);
                position: sticky;
                top: 0;
                z-index: 150;
            }

            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px; height: 40px;
                font-size: 1.4rem;
                background: var(--line-2);
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                color: var(--ink);
            }

            .container {
                width: 100%;
                max-width: 100%;
                padding: 1rem 0.8rem 4rem;
                overflow-x: hidden;
            }

            .page-content { gap: 1rem; width: 100%; }

            .page-title-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }

            .title-actions {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            .title-actions .btn { width: 100%; justify-content: center; }

            .card { padding: 1.2rem 1rem; border-radius: var(--radius); width: 100%; overflow: hidden; }
            .card-hero { padding: 1.5rem 1rem; }

            .kpi-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            
            .table-wrap {
                margin: 0 -1rem;
                border-radius: 0;
                border-left: none;
                border-right: none;
                width: calc(100% + 2rem);
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            table { min-width: 700px; }
            thead th { padding: 0.6rem 0.8rem; font-size: 0.75rem; }
            tbody td { padding: 0.6rem 0.8rem; font-size: 0.85rem; }
            
            .form-layout { grid-template-columns: 1fr; gap: 1rem; }
            .form-actions { display: flex; flex-direction: column; gap: 0.5rem; }
            .form-actions .btn { width: 100%; justify-content: center; }
            
            .quick-grid { grid-template-columns: 1fr; }
            
            /* Typography optimizations */
            h1 { font-size: 1.4rem; }
            h2 { font-size: 1.15rem; }
            
            /* Actions inside tables */
            .actions-cell { display: flex; flex-direction: row; flex-wrap: nowrap; gap: 0.4rem; align-items: center; }
            .actions-cell .btn { flex: 0 0 auto; }
            .actions-cell .text-muted { display: none; } /* Hide labels next to buttons to save space */

            .login-card { padding: 1.5rem 1rem; border-radius: var(--radius-lg); margin: 0; width: 100%; border: none; box-shadow: none; background: transparent; }
            .login-page { min-height: 100vh; background: var(--panel); align-items: flex-start; padding-top: 2rem; }
        }

        @media (max-width: 480px) {
            h1 { font-size: 1.25rem; }
            .container { padding: 0.8rem 0.5rem 4rem; }
            .card { padding: 1rem 0.8rem; }
            .table-wrap { margin: 0 -0.8rem; width: calc(100% + 1.6rem); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- OVERLAY -->
    <div class="overlay" id="sidebar-overlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('merchant.dashboard') }}" class="brand-logo">
                <div class="brand-icon">🔐</div>
                <div class="brand-text">
                    <span class="brand-kicker">OTP Hub</span>
                    <span class="brand-title">بوابة التاجر</span>
                </div>
            </a>
        </div>

        @auth
            @if(auth()->user()->isMerchant())
                <nav class="sidebar-nav">
                    <div class="nav-section">القائمة الرئيسية</div>

                    <a class="nav-item {{ request()->routeIs('merchant.dashboard') ? 'is-active' : '' }}" href="{{ route('merchant.dashboard') }}">
                        <span class="nav-icon">📊</span> الرئيسية
                    </a>

                    <div class="nav-section">الحسابات</div>
                    <a class="nav-item {{ request()->routeIs('merchant.accounts.index') ? 'is-active' : '' }}" href="{{ route('merchant.accounts.index') }}">
                        <span class="nav-icon">📦</span> قائمة الحسابات
                    </a>
                    <a class="nav-item {{ request()->routeIs('merchant.accounts.create') ? 'is-active' : '' }}" href="{{ route('merchant.accounts.create') }}">
                        <span class="nav-icon">➕</span> إضافة حساب جديد
                    </a>

                    <div class="nav-section">العملاء</div>
                    <a class="nav-item {{ request()->routeIs('merchant.customers.index') ? 'is-active' : '' }}" href="{{ route('merchant.customers.index') }}">
                        <span class="nav-icon">👥</span> قائمة العملاء
                    </a>
                    <a class="nav-item {{ request()->routeIs('merchant.customers.create') ? 'is-active' : '' }}" href="{{ route('merchant.customers.create') }}">
                        <span class="nav-icon">➕</span> إضافة عميل جديد
                    </a>
                    <a class="nav-item {{ request()->routeIs('merchant.customers.bulk') ? 'is-active' : '' }}" href="{{ route('merchant.customers.bulk') }}">
                        <span class="nav-icon">📋</span> إنشاء بالجملة
                    </a>

                    <div class="nav-section">الإعدادات</div>
                    <a class="nav-item {{ request()->routeIs('merchant.profile.*') ? 'is-active' : '' }}" href="{{ route('merchant.profile.edit') }}">
                        <span class="nav-icon">⚙️</span> إعدادات الحساب
                    </a>
                </nav>

                <div class="sidebar-footer">
                    <div class="user-card">
                        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 2) }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-sub">اشتراك حتى {{ optional(auth()->user()->subscription_end)->format('Y-m-d') ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                    <form method="post" action="{{ route('merchant.logout') }}">
                        @csrf
                        <button class="logout-btn" type="submit">🚪 تسجيل الخروج</button>
                    </form>
                </div>
            @endif
        @endauth
    </aside>

    <!-- MAIN -->
    <div class="main">
        <header class="main-header">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="فتح القائمة">☰</button>
            <span class="mobile-brand">🔐 OTP Hub</span>
            <span></span>
        </header>

        <div class="container">
            <div class="page-content">
                @if(session('status'))
                    <div class="alert alert-success">
                        <span>✅</span>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <span>❌</span>
                        <div>
                            <strong>يوجد أخطاء في البيانات المدخلة.</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        (() => {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            if (sidebar && toggle && overlay) {
                const openSidebar = (e) => {
                    if(e) e.stopPropagation();
                    sidebar.classList.add('is-open');
                    overlay.classList.add('is-visible');
                    document.body.style.overflow = 'hidden';
                };

                const closeSidebar = () => {
                    sidebar.classList.remove('is-open');
                    overlay.classList.remove('is-visible');
                    document.body.style.overflow = '';
                };

                toggle.addEventListener('click', openSidebar);
                overlay.addEventListener('click', closeSidebar);

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
                        closeSidebar();
                    }
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth > 820 && sidebar.classList.contains('is-open')) {
                        closeSidebar();
                    }
                });

                /* Swipe to open/close sidebar on mobile */
                let touchStartX = null;
                document.addEventListener('touchstart', (e) => {
                    if (window.innerWidth > 820) return;
                    touchStartX = e.touches[0].clientX;
                }, { passive: true });

                document.addEventListener('touchend', (e) => {
                    if (window.innerWidth > 820 || touchStartX === null) return;
                    const touchEndX = e.changedTouches[0].clientX;
                    const dx = touchEndX - touchStartX;
                    
                    if (Math.abs(dx) > 60) {
                        // RTL context: swiping right (dx > 0) usually means closing side bar, left means open
                        if (dx < -40) openSidebar(); // swipe towards left <-
                        else if (dx > 40) closeSidebar(); // swipe towards right ->
                    }
                    touchStartX = null;
                }, { passive: true });
            }

            /* Add title to truncated elements for long-press tooltip */
            document.querySelectorAll('.mono').forEach((el) => {
                if (el.scrollWidth > el.clientWidth && !el.hasAttribute('title')) {
                    el.title = el.textContent.trim();
                }
            });

            /* Enable touch scrolling on table wraps with momentum */
            document.querySelectorAll('.table-wrap').forEach((wrap) => {
                wrap.style.webkitOverflowScrolling = 'touch';
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
