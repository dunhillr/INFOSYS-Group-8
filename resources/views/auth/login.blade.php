<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bobong Ice Plant</title>

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/brand-logos/favicon.ico') }}">
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">

    <style>
        :root {
            --bobong-primary: #0ea5e9;
            --bobong-primary-dark: #0284c7;
            --bobong-accent: #38bdf8;
            --bobong-dark: #0f172a;
            --bobong-dark-soft: #1e293b;
            --bobong-panel: #ffffff;
            --bobong-border: #e2e8f0;
            --bobong-text: #334155;
            --bobong-muted: #64748b;
            --bobong-danger: #ef4444;
            --bobong-success: #10b981;
            --bobong-shadow: 0 20px 40px -15px rgba(148, 163, 184, 0.15), 0 0 0 1px rgba(148, 163, 184, 0.05);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--bobong-text);
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.12), transparent 35%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.10), transparent 30%),
                #f8fafc;
            overflow: hidden;
        }

        .auth-shell {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
            overflow: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(90px);
            opacity: .25;
            pointer-events: none;
            z-index: 1;
        }

        .orb-1 {
            width: 320px;
            height: 320px;
            top: -90px;
            left: -70px;
            background: #38bdf8;
        }

        .orb-2 {
            width: 340px;
            height: 340px;
            right: -90px;
            top: 120px;
            background: #0ea5e9;
        }

        .orb-3 {
            width: 260px;
            height: 260px;
            bottom: -80px;
            left: 20%;
            background: #06b6d4;
        }

        .auth-container {
            width: 100%;
            max-width: 1140px;
            height: 100%;
            max-height: 650px;
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: stretch;
        }

        .auth-card,
        .auth-showcase {
            border: 1px solid var(--bobong-border);
            border-radius: 28px;
            box-shadow: var(--bobong-shadow);
            overflow: hidden;
            height: 100%;
        }

        .auth-card {
            padding: 32px 38px;
            background: var(--bobong-panel);
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .auth-showcase {
            position: relative;
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
            border: 1px solid #bae6fd;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 32px 38px;
            overflow-y: auto;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .brand-mark {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--bobong-primary), var(--bobong-accent));
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.15);
        }

        .brand-title {
            margin: 0;
            color: var(--bobong-dark);
            font-size: 1.1rem;
            font-weight: 800;
        }

        .brand-subtitle {
            margin: 2px 0 0;
            color: var(--bobong-muted);
            font-size: .88rem;
        }

        .auth-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(14, 165, 233, 0.25);
            background: rgba(14, 165, 233, 0.08);
            color: #0369a1;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .auth-heading {
            margin: 18px 0 0;
            color: #0369a1;
            font-size: clamp(1.8rem, 3.5vw, 2.6rem);
            line-height: 1.1;
            font-weight: 900;
        }

        .auth-heading span {
            color: #0ea5e9;
        }

        .feature-list {
            display: grid;
            gap: 12px;
            margin-top: 24px;
        }

        .feature-item {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .feature-item strong {
            display: block;
            color: #0369a1;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .feature-item span {
            color: #475569;
            font-size: .90rem;
            line-height: 1.6;
        }

        .bottom-note {
            margin-top: 24px;
            color: #0369a1;
            opacity: 0.8;
            font-size: .88rem;
        }

        .login-title {
            margin: 0;
            color: var(--bobong-dark);
            font-size: 1.8rem;
            font-weight: 900;
        }

        .login-subtitle {
            margin: 6px 0 0;
            color: var(--bobong-text);
            font-size: 0.94rem;
            line-height: 1.6;
        }

        .status-box {
            margin-top: 14px;
        }

        .error-box {
            margin-top: 14px;
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.15);
            color: #c53030;
            font-size: 0.9rem;
        }

        .error-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .auth-form {
            margin-top: 20px;
            display: grid;
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 6px;
        }

        .form-label-modern {
            color: var(--bobong-dark-soft);
            font-weight: 700;
            font-size: .88rem;
        }

        .input-wrap {
            position: relative;
        }

        .form-control-modern {
            width: 100%;
            min-height: 52px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: var(--bobong-dark);
            padding: 0 16px;
            outline: none;
            transition: .25s ease;
            font-size: 0.92rem;
        }

        .form-control-modern::placeholder {
            color: #94a3b8;
        }

        .form-control-modern:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            font-size: .85rem;
            font-weight: 700;
            padding: 4px 6px;
        }

        .password-toggle:hover {
            color: var(--bobong-dark);
        }

        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .remember-wrap {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--bobong-text);
            font-size: .88rem;
        }

        .remember-wrap input {
            accent-color: var(--bobong-primary);
        }

        .inline-link {
            color: var(--bobong-primary);
            text-decoration: none;
            font-weight: 700;
            font-size: .88rem;
        }

        .inline-link:hover {
            color: var(--bobong-primary-dark);
        }

        .submit-btn {
            min-height: 52px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--bobong-primary), #06b6d4);
            color: #ffffff;
            font-weight: 900;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(14, 165, 233, 0.15);
            transition: .25s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.20);
        }

        .register-row {
            text-align: center;
            color: var(--bobong-muted);
            font-size: .90rem;
        }

        .register-row a {
            color: var(--bobong-primary);
            font-weight: 700;
            text-decoration: none;
        }

        .register-row a:hover {
            color: var(--bobong-primary-dark);
        }

        @media (max-width: 1024px) {
            body {
                overflow: auto;
            }

            .auth-shell {
                height: auto;
                min-height: 100vh;
                padding: 24px 16px;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
            }

            .auth-container {
                grid-template-columns: 1fr;
                gap: 24px;
                max-width: 520px;
                height: auto;
            }

            .auth-card,
            .auth-showcase {
                height: auto;
                border-radius: 28px;
                border: 1px solid var(--bobong-border);
            }

            .auth-card {
                padding: 38px 28px;
                border-right: none;
            }

            .auth-showcase {
                padding: 38px 28px;
                border: 1px solid #bae6fd;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="auth-container">
            <div class="auth-card">
                <div class="brand-row">
                    <div class="brand-mark">BIP</div>
                    <div>
                        <h1 class="brand-title">Bobong Ice Plant</h1>
                        <p class="brand-subtitle">Automated Management System</p>
                    </div>
                </div>

                <h2 class="login-title">Welcome back</h2>
                <p class="login-subtitle">
                    Sign in to manage products, customers, production, inventory, sales,
                    vehicles, deliveries, notifications, and reports.
                </p>

                <div class="status-box">
                    <x-auth-session-status :status="session('status')" />
                </div>

                @if ($errors->any())
                    <div class="error-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="username" class="form-label-modern">Username</label>
                        <div class="input-wrap">
                            <input
                                id="username"
                                type="text"
                                name="username"
                                class="form-control-modern"
                                value="{{ old('username') }}"
                                placeholder="Enter your username"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label-modern">Password</label>
                        <div class="input-wrap">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-control-modern"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                Show
                            </button>
                        </div>
                    </div>

                    <div class="form-meta">
                        <label class="remember-wrap" for="remember">
                            <input id="remember" type="checkbox" name="remember" value="1">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="inline-link">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="submit-btn">
                        Sign In
                    </button>

                    @if (Route::has('register'))
                        <div class="register-row">
                            Don’t have an account?
                            <a href="{{ route('register') }}">Create Account</a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="auth-showcase">
                <div>
                    <div class="auth-badge">Professional Operations Platform</div>

                    <h3 class="auth-heading">
                        Manage your <span>ice plant workflow</span> in one secure platform
                    </h3>

                    <div class="feature-list">
                        <div class="feature-item">
                            <strong>🔒 Secured Access Control</strong>
                            <span>Separate workspace for Owner, Staff, and Drivers. Locked functions prevent unauthorized data editing.</span>
                        </div>

                        <div class="feature-item">
                            <strong>🚚 Delivery & History Tracking</strong>
                            <span>Live status updates from drivers. Auto-locks transactions to Sales History once fully delivered and paid.</span>
                        </div>

                        <div class="feature-item">
                            <strong>💳 Payment Management</strong>
                            <span>Easily collect partial balances or process walk-in transactions with real-time stock adjustments.</span>
                        </div>
                    </div>
                </div>

                <div class="bottom-note">
                    Sign in with your assigned Bobong Ice Plant account to continue.
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/assets/libs/preline/preline.js') }}"></script>
    <script src="{{ asset('backend/assets/js/custom.js') }}"></script>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const button = document.querySelector('.password-toggle');

            if (password.type === 'password') {
                password.type = 'text';
                button.textContent = 'Hide';
            } else {
                password.type = 'password';
                button.textContent = 'Show';
            }
        }
    </script>
</body>
</html>