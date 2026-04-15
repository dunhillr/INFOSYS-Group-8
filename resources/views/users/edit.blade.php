@extends('layouts.app')
@section('title', 'Edit User')
@section('content')

{{-- Page Header --}}
<div class="block justify-between page-header md:flex mt-4">
    <div>
        <h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Edit User</h3>
    </div>
    <ol class="flex items-center whitespace-nowrap min-w-0">
        <li class="text-sm font-semibold text-primary">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <i class="ri-home-line me-1"></i> Home
            </a>
        </li>
        <li class="before:content-['/'] before:mx-2 text-sm text-textmuted">
            <a href="{{ route('users.index') }}" class="text-textmuted hover:text-primary">Users</a>
        </li>
        <li class="before:content-['/'] before:mx-2 text-sm text-textmuted truncate">Edit: {{ $user->name }}</li>
    </ol>
</div>

{{-- Form Card --}}
<div class="grid grid-cols-12 gap-6">
    <div class="xl:col-span-8 col-span-12">
        <div class="box">
            <div class="box-header">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-warning/10">
                        <i class="ri-user-settings-line text-warning text-base"></i>
                    </span>
                    <h5 class="box-title mb-0">Edit User: <span class="text-primary">{{ $user->name }}</span></h5>
                </div>
            </div>
            <div class="box-body">
                <form action="{{ route('users.update', $user) }}" method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')
                    @include('users._form')
                    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-defaultborder dark:border-defaultborder/10">
                        <button type="submit" class="ti-btn ti-btn-warning-full !font-medium min-w-[120px]">
                            <i class="ri-save-line me-1"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="ti-btn ti-btn-light !font-medium min-w-[100px]">
                            <i class="ri-arrow-left-line me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Helper Info Card --}}
    <div class="xl:col-span-4 col-span-12">
        <div class="box">
            <div class="box-header">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-info/10">
                        <i class="ri-information-line text-info text-base"></i>
                    </span>
                    <h5 class="box-title mb-0">User Roles</h5>
                </div>
            </div>
            <div class="box-body space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-md bg-primary/5 border border-primary/10">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-primary/15 shrink-0">
                        <i class="ri-shield-star-line text-primary"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-defaulttextcolor text-sm mb-0.5">Owner</p>
                        <p class="text-textmuted text-xs">Full access to all modules including user management, reports, and system settings.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-md bg-success/5 border border-success/10">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-success/15 shrink-0">
                        <i class="ri-user-line text-success"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-defaulttextcolor text-sm mb-0.5">Employee</p>
                        <p class="text-textmuted text-xs">Standard access to day-to-day operations such as sales, deliveries, and production logs.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-md bg-warning/5 border border-warning/10">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-warning/15 shrink-0">
                        <i class="ri-lock-password-line text-warning"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-defaulttextcolor text-sm mb-0.5">Password Policy</p>
                        <p class="text-textmuted text-xs">Leave password blank to keep the current one. Use a strong password with 8+ characters.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection