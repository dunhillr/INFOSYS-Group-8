<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bobong Ice Plant</title>
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/brand-logos/favicon.ico') }}">
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
</head>
<body class="bg-bodybg">
<div class="grid grid-cols-12 authentication mx-0">
    <div class="xxl:col-span-7 xl:col-span-7 lg:col-span-12 col-span-12">
        <div class="row justify-center items-center h-full">
            <div class="xxl:col-span-6 xl:col-span-7 lg:col-span-7 md:col-span-8 sm:col-span-8 col-span-12">
                <div class="box my-4 !border-0 shadow-none">
                    <div class="box-body !p-[3rem]">
                        <p class="h5 font-semibold mb-2 text-center">Create Account</p>
                        <p class="mb-4 text-textmuted opacity-[0.7] font-normal text-center">
                            Bobong Ice Plant Management System
                        </p>

                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="grid grid-cols-12 gap-y-4">
                                <div class="xl:col-span-12 col-span-12">
                                    <label class="form-label text-default">Full Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg w-full !rounded-md" value="{{ old('name') }}" required>
                                </div>

                                <div class="xl:col-span-12 col-span-12">
                                    <label class="form-label text-default">Username</label>
                                    <input type="text" name="username" class="form-control form-control-lg w-full !rounded-md" value="{{ old('username') }}" required>
                                </div>

                                <div class="xl:col-span-12 col-span-12">
                                    <label class="form-label text-default">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg w-full !rounded-md" value="{{ old('email') }}">
                                </div>

                                <div class="xl:col-span-12 col-span-12">
                                    <label class="form-label text-default">Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg w-full !rounded-md" required>
                                </div>

                                <div class="xl:col-span-12 col-span-12">
                                    <label class="form-label text-default">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg w-full !rounded-md" required>
                                </div>

                                <div class="xl:col-span-12 col-span-12 grid mt-2">
                                    <button type="submit" class="ti-btn ti-btn-primary !bg-primary text-white !font-medium min-h-[45px]">
                                        Create Account
                                    </button>
                                </div>

                                <div class="xl:col-span-12 col-span-12 text-center mt-2">
                                    <a href="{{ route('login') }}" class="text-primary">Already have an account?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('backend/assets/libs/preline/preline.js') }}"></script>
<script src="{{ asset('backend/assets/js/custom.js') }}"></script>
</body>
</html>