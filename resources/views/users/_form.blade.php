<div class="grid grid-cols-12 gap-x-6 gap-y-5">

    {{-- Section: Basic Information --}}
    <div class="col-span-12">
        <p class="text-xs font-semibold uppercase tracking-wider text-textmuted mb-1">
            <i class="ri-user-settings-line me-1"></i> Basic Information
        </p>
        <hr class="border-defaultborder dark:border-defaultborder/10">
    </div>

    {{-- Full Name --}}
    <div class="xl:col-span-6 col-span-12">
        <label for="user_name" class="form-label">
            Full Name <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-e-0">
                <i class="ri-user-3-line text-textmuted"></i>
            </span>
            <input
                type="text"
                id="user_name"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="e.g. Juan Dela Cruz"
                value="{{ old('name', $user->name ?? '') }}"
                required
            >
        </div>
        @error('name')
            <div class="text-danger text-xs mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    {{-- Username --}}
    <div class="xl:col-span-6 col-span-12">
        <label for="user_username" class="form-label">
            Username <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-e-0">
                <i class="ri-at-line text-textmuted"></i>
            </span>
            <input
                type="text"
                id="user_username"
                name="username"
                class="form-control @error('username') is-invalid @enderror"
                placeholder="e.g. juandc"
                value="{{ old('username', $user->username ?? '') }}"
                required
            >
        </div>
        @error('username')
            <div class="text-danger text-xs mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="xl:col-span-6 col-span-12">
        <label for="user_email" class="form-label">
            Email Address
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-e-0">
                <i class="ri-mail-line text-textmuted"></i>
            </span>
            <input
                type="email"
                id="user_email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="e.g. juan@example.com"
                value="{{ old('email', $user->email ?? '') }}"
            >
        </div>
        @error('email')
            <div class="text-danger text-xs mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    {{-- Role --}}
    <div class="xl:col-span-6 col-span-12">
        <label for="user_type" class="form-label">
            Role <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-e-0">
                <i class="ri-shield-user-line text-textmuted"></i>
            </span>
            <select
                id="user_type"
                name="user_type"
                class="form-control @error('user_type') is-invalid @enderror"
                required
            >
                <option value="" disabled {{ old('user_type', $user->user_type ?? '') === '' ? 'selected' : '' }}>— Select Role —</option>
                <option value="owner"    @selected(old('user_type', $user->user_type ?? 'employee') === 'owner')>Owner / Admin</option>
                <option value="employee" @selected(old('user_type', $user->user_type ?? 'employee') === 'employee')>Staff / Employee</option>
                <option value="driver"   @selected(old('user_type', $user->user_type ?? 'employee') === 'driver')>Driver</option>
            </select>
        </div>
        @error('user_type')
            <div class="text-danger text-xs mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    {{-- Section: Security --}}
    <div class="col-span-12 mt-2">
        <p class="text-xs font-semibold uppercase tracking-wider text-textmuted mb-1">
            <i class="ri-lock-line me-1"></i> Security
        </p>
        <hr class="border-defaultborder dark:border-defaultborder/10">
    </div>

    {{-- Password --}}
    <div class="xl:col-span-6 col-span-12">
        <label for="user_password" class="form-label">
            Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}
        </label>
        @if(isset($user))
            <p class="text-xs text-textmuted mb-1">Leave blank to keep the current password.</p>
        @endif
        <div class="input-group">
            <span class="input-group-text bg-light border-e-0">
                <i class="ri-lock-password-line text-textmuted"></i>
            </span>
            <input
                type="password"
                id="user_password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Min. 8 characters"
                {{ isset($user) ? '' : 'required' }}
            >
            <button type="button" class="input-group-text bg-light cursor-pointer" onclick="
                var inp = document.getElementById('user_password');
                var icon = this.querySelector('i');
                if (inp.type === 'password') { inp.type = 'text'; icon.className = 'ri-eye-off-line text-textmuted'; }
                else { inp.type = 'password'; icon.className = 'ri-eye-line text-textmuted'; }
            ">
                <i class="ri-eye-line text-textmuted"></i>
            </button>
        </div>
        @error('password')
            <div class="text-danger text-xs mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    {{-- Section: Status --}}
    <div class="col-span-12 mt-2">
        <p class="text-xs font-semibold uppercase tracking-wider text-textmuted mb-1">
            <i class="ri-toggle-line me-1"></i> Account Status
        </p>
        <hr class="border-defaultborder dark:border-defaultborder/10">
    </div>

    {{-- Active Toggle --}}
    <div class="col-span-12">
        <div class="flex items-center justify-between p-4 rounded-lg bg-light dark:bg-bodybg2/50 border border-defaultborder dark:border-defaultborder/10">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-success/10">
                    <i class="ri-checkbox-circle-line text-success text-lg"></i>
                </span>
                <div>
                    <p class="font-semibold text-defaulttextcolor text-sm mb-0">Active Account</p>
                    <p class="text-textmuted text-xs mb-0">An active user can log in and access the system.</p>
                </div>
            </div>
            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input
                    class="ti-switch"
                    type="checkbox"
                    id="user_is_active"
                    name="is_active"
                    value="1"
                    role="switch"
                    @checked(old('is_active', $user->is_active ?? true))
                >
                <label class="ms-2 text-sm font-medium text-defaulttextcolor cursor-pointer" for="user_is_active">
                    Active
                </label>
            </div>
        </div>
    </div>

</div>