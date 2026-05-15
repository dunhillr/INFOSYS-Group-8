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
            --bobong-accent: #22d3ee;
            --bobong-dark: #020617;
            --bobong-dark-soft: #0f172a;
            --bobong-panel: rgba(15, 23, 42, 0.78);
            --bobong-panel-soft: rgba(15, 23, 42, 0.55);
            --bobong-border: rgba(255, 255, 255, 0.10);
            --bobong-text: #e2e8f0;
            --bobong-muted: #94a3b8;
            --bobong-danger: #ef4444;
            --bobong-success: #10b981;
            --bobong-shadow: 0 30px 80px rgba(2, 8, 23, 0.40);
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
                radial-gradient(circle at top left, rgba(34, 211, 238, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.18), transparent 24%),
                linear-gradient(135deg, #020617 0%, #0f172a 48%, #111827 100%);
            overflow-x: hidden;
        }

        .auth-shell {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
        }

        .orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(90px);
            opacity: .35;
            pointer-events: none;
        }

        .orb-1 {
            width: 320px;
            height: 320px;
            top: -90px;
            left: -70px;
            background: #22d3ee;
        }

        .orb-2 {
            width: 340px;
            height: 340px;
            right: -90px;
            top: 120px;
            background: #2563eb;
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
            max-width: 1240px;
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
            background: var(--bobong-panel);
            backdrop-filter: blur(18px);
            box-shadow: var(--bobong-shadow);
            overflow: hidden;
        }

        .auth-card {
            padding: 38px;
        }

        .auth-showcase {
            position: relative;
            background:
                linear-gradient(160deg, rgba(14, 165, 233, 0.18), rgba(15, 23, 42, 0.86)),
                rgba(15, 23, 42, 0.85);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 38px;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .brand-mark {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--bobong-primary), var(--bobong-accent));
            color: #06233a;
            font-size: 1.1rem;
            font-weight: 900;
            box-shadow: 0 18px 36px rgba(14, 165, 233, 0.28);
        }

        .brand-title {
            margin: 0;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .brand-subtitle {
            margin: 4px 0 0;
            color: var(--bobong-muted);
            font-size: .9rem;
        }

        .auth-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(34, 211, 238, 0.25);
            background: rgba(34, 211, 238, 0.10);
            color: #bdf6ff;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .auth-heading {
            margin: 18px 0 0;
            color: #fff;
            font-size: clamp(2rem, 4vw, 3.1rem);
            line-height: 1.05;
            font-weight: 900;
        }

        .auth-heading span {
            color: #67e8f9;
        }

        .auth-description {
            margin: 18px 0 0;
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.8;
            max-width: 560px;
        }

        .role-cards {
            display: grid;
            gap: 14px;
            margin-top: 28px;
        }

        .role-card {
            padding: 18px 20px;
            border-radius: 18px;
            background: rgba(2, 6, 23, 0.38);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .role-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-size: 1.2rem;
        }

        .role-icon.owner {
            background: rgba(14, 165, 233, 0.18);
            color: #67e8f9;
        }

        .role-icon.employee {
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7;
        }

        .role-icon.secure {
            background: rgba(168, 85, 247, 0.18);
            color: #c4b5fd;
        }

        .role-card strong {
            display: block;
            color: #fff;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .role-card p {
            margin: 0;
            color: #cbd5e1;
            font-size: .9rem;
            line-height: 1.6;
        }

        .mini-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .mini-stat {
            padding: 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            text-align: center;
        }

        .mini-stat strong {
            display: block;
            color: #67e8f9;
            font-size: 1.05rem;
            margin-bottom: 6px;
        }

        .mini-stat span {
            color: #cbd5e1;
            font-size: .88rem;
            line-height: 1.6;
        }

        .bottom-note {
            margin-top: 26px;
            color: var(--bobong-muted);
            font-size: .92rem;
        }

        .login-title {
            margin: 0;
            color: #fff;
            font-size: 2rem;
            font-weight: 900;
        }

        .login-subtitle {
            margin: 10px 0 0;
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.7;
        }

        .error-box {
            margin-top: 18px;
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(239, 68, 68, 0.10);
            border: 1px solid rgba(239, 68, 68, 0.20);
            color: #fecaca;
        }

        .error-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .auth-form {
            margin-top: 28px;
            display: grid;
            gap: 18px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-label-modern {
            color: #e2e8f0;
            font-weight: 700;
            font-size: .92rem;
        }

        .form-label-modern .optional {
            color: var(--bobong-muted);
            font-weight: 500;
            font-size: .82rem;
        }

        .input-wrap {
            position: relative;
        }

        .form-control-modern {
            width: 100%;
            min-height: 56px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.10);
            background: rgba(2, 6, 23, 0.42);
            color: #fff;
            padding: 0 16px;
            outline: none;
            transition: .25s ease;
            font-size: .95rem;
        }

        .form-control-modern::placeholder {
            color: #94a3b8;
        }

        .form-control-modern:focus {
            border-color: rgba(34, 211, 238, 0.40);
            box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.08);
        }

        .form-control-modern.is-invalid {
            border-color: rgba(239, 68, 68, 0.50);
        }

        .form-control-modern.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.10);
        }

        .field-error {
            color: #fca5a5;
            font-size: .82rem;
            margin-top: 2px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 700;
            padding: 4px 6px;
        }

        .password-toggle:hover {
            color: #e2e8f0;
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
            background: rgba(255,255,255,0.08);
            transition: .3s ease;
        }

        .strength-bar.active.weak { background: #ef4444; }
        .strength-bar.active.fair { background: #f59e0b; }
        .strength-bar.active.good { background: #22d3ee; }
        .strength-bar.active.strong { background: #10b981; }

        .strength-text {
            font-size: .78rem;
            color: var(--bobong-muted);
            margin-top: 4px;
        }

        .submit-btn {
            min-height: 56px;
            border: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--bobong-primary), var(--bobong-accent));
            color: #06233a;
            font-weight: 900;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 18px 34px rgba(14, 165, 233, 0.28);
            transition: .25s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 42px rgba(14, 165, 233, 0.34);
        }

        .register-row {
            text-align: center;
            color: var(--bobong-muted);
            font-size: .94rem;
        }

        .register-row a {
            color: #67e8f9;
            font-weight: 700;
            text-decoration: none;
        }

        .register-row a:hover {
            color: #a5f3fc;
        }

        @media (max-width: 1024px) {
            .auth-container {
                grid-template-columns: 1fr;
            }

            .auth-showcase {
                order: -1;
            }
        }

        @media (max-width: 640px) {
            .auth-shell {
                padding: 18px 12px;
            }

            .auth-card,
            .auth-showcase {
                border-radius: 22px;
                padding: 22px;
            }

            .login-title {
                font-size: 1.7rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .mini-stats {
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
                    <div class="auth-badge">Join the Platform</div>

                    <h3 class="auth-heading">
                        Start managing your <span>ice plant operations</span> today
                    </h3>

                    <p class="auth-description">
                        Create your account to access production tracking, inventory management,
                        sales recording, delivery scheduling, and comprehensive reporting —
                        all from one unified dashboard.
                    </p>

                    <div class="role-cards">
                        <div class="role-card">
                            <div class="role-icon owner">👑</div>
                            <div>
                                <strong>Owner Access</strong>
                                <p>Full control over all modules — users, reports, settings, inventory, and analytics.</p>
                            </div>
                        </div>

                        <div class="role-card">
                            <div class="role-icon employee">👤</div>
                            <div>
                                <strong>Employee Access</strong>
                                <p>Day-to-day operations — sales, production logs, deliveries, and customer records.</p>
                            </div>
                        </div>

                        <div class="role-card">
                            <div class="role-icon secure">🔒</div>
                            <div>
                                <strong>Secure & Private</strong>
                                <p>Encrypted passwords and role-based access control to protect your business data.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mini-stats">
                        <div class="mini-stat">
                            <strong>Simple</strong>
                            <span>Quick registration</span>
                        </div>
                        <div class="mini-stat">
                            <strong>Secure</strong>
                            <span>Encrypted credentials</span>
                        </div>
                        <div class="mini-stat">
                            <strong>Ready</strong>
                            <span>Instant dashboard access</span>
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