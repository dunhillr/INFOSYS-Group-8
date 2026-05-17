<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bobong Ice Plant</title>

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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
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

        .form-label-modern .optional {
            color: var(--bobong-muted);
            font-weight: 500;
            font-size: .80rem;
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

        .form-control-modern.is-invalid {
            border-color: rgba(239, 68, 68, 0.50);
        }

        .form-control-modern.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.10);
        }

        .field-error {
            color: #ef4444;
            font-size: .80rem;
            margin-top: 2px;
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

        .password-strength {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }

        .strength-bar {
            height: 4px;
            flex: 1;
            border-radius: 99px;
            background: #cbd5e1;
            transition: .3s ease;
        }

        .strength-bar.active.weak { background: #ef4444; }
        .strength-bar.active.fair { background: #f59e0b; }
        .strength-bar.active.good { background: #0ea5e9; }
        .strength-bar.active.strong { background: #10b981; }

        .strength-text {
            font-size: .78rem;
            color: var(--bobong-muted);
            margin-top: 4px;
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

            .form-row {
                grid-template-columns: 1fr;
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
                    <div class="brand-mark">BI</div>
                    <div>
                        <h1 class="brand-title">Bobong Ice Plant</h1>
                        <p class="brand-subtitle">Automated Management System</p>
                    </div>
                </div>

                <h2 class="login-title">Create Account</h2>
                <p class="login-subtitle">
                    Register a new account to access the Bobong Ice Plant Management System.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    {{-- Full Name & Username side by side --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label-modern">Full Name</label>
                            <div class="input-wrap">
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    class="form-control-modern @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    placeholder="e.g. Juan Dela Cruz"
                                    required
                                    autofocus
                                >
                            </div>
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="username" class="form-label-modern">Username</label>
                            <div class="input-wrap">
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    class="form-control-modern @error('username') is-invalid @enderror"
                                    value="{{ old('username') }}"
                                    placeholder="e.g. juandc"
                                    required
                                >
                            </div>
                            @error('username')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email" class="form-label-modern">Email Address <span class="optional">(optional)</span></label>
                        <div class="input-wrap">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="form-control-modern @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="e.g. juan@example.com"
                            >
                        </div>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password & Confirm side by side --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label-modern">Password</label>
                            <div class="input-wrap">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control-modern @error('password') is-invalid @enderror"
                                    placeholder="Min. 8 characters"
                                    required
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                    Show
                                </button>
                            </div>
                            <div class="password-strength" id="strengthBars">
                                <div class="strength-bar" id="bar1"></div>
                                <div class="strength-bar" id="bar2"></div>
                                <div class="strength-bar" id="bar3"></div>
                                <div class="strength-bar" id="bar4"></div>
                            </div>
                            <span class="strength-text" id="strengthText"></span>
                            @error('password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label-modern">Confirm Password</label>
                            <div class="input-wrap">
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control-modern"
                                    placeholder="Re-enter password"
                                    required
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                                    Show
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        Create Account
                    </button>

                    <div class="register-row">
                        Already have an account?
                        <a href="{{ route('login') }}">Sign In</a>
                    </div>
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
                    After registration, your account will require admin approval before full access is granted.
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/assets/libs/preline/preline.js') }}"></script>
    <script src="{{ asset('backend/assets/js/custom.js') }}"></script>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                btn.textContent = 'Hide';
            } else {
                field.type = 'password';
                btn.textContent = 'Show';
            }
        }

        // Password strength meter
        const passwordInput = document.getElementById('password');
        const bars = [
            document.getElementById('bar1'),
            document.getElementById('bar2'),
            document.getElementById('bar3'),
            document.getElementById('bar4')
        ];
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const val = this.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            const levels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
            const classes = ['', 'weak', 'fair', 'good', 'strong'];

            bars.forEach((bar, i) => {
                bar.className = 'strength-bar';
                if (i < score) {
                    bar.classList.add('active', classes[score]);
                }
            });

            strengthText.textContent = val.length > 0 ? levels[score] || 'Too short' : '';
        });
    </script>
</body>
</html>