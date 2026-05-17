@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Users</h3></div><div><a href="{{ route('users.create') }}" class="ti-btn ti-btn-primary-full">Add User</a></div></div>
<div class="box"><div class="box-body"><div class="overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th width="150">Actions</th></tr></thead><tbody>
@forelse ($users as $user)
<tr><td>{{ $user->name }}</td><td>{{ $user->username }}</td><td>{{ $user->email ?? '-' }}</td>
<td>
    @if($user->user_type === 'owner')
        <span class="badge bg-purple-100 text-purple-700 text-[11px] font-bold px-2 py-0.5 rounded-full">👑 Owner / Admin</span>
    @elseif($user->user_type === 'employee')
        <span class="badge bg-blue-100 text-blue-700 text-[11px] font-bold px-2 py-0.5 rounded-full">🧑‍💼 Staff</span>
    @elseif($user->user_type === 'driver')
        <span class="badge bg-green-100 text-green-700 text-[11px] font-bold px-2 py-0.5 rounded-full">🚚 Driver</span>
    @else
        <span class="badge bg-gray-100 text-gray-600 text-[11px] px-2 py-0.5 rounded-full">{{ ucfirst($user->user_type) }}</span>
    @endif
</td>
<td>{{ $user->is_active ? '<span class="text-green-600 font-semibold text-xs">Active</span>' : '<span class="text-red-500 text-xs">Inactive</span>' }}</td>
<td><a href="{{ route('users.edit', $user) }}" class="ti-btn ti-btn-info-full ti-btn-sm">Edit</a>@if (auth()->id() !== $user->id)<form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" data-confirm-delete data-confirm-item="{{ $user->name }}">@csrf @method('DELETE')<button class="ti-btn ti-btn-danger-full ti-btn-sm">Delete</button></form>@endif</td></tr>
@empty <tr><td colspan="6" class="text-center">No users found.</td></tr>
@endforelse
</tbody></table></div>{{ $users->links() }}</div></div>
@endsection
