@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Users</h3></div><div><a href="{{ route('users.create') }}" class="ti-btn ti-btn-primary-full">Add User</a></div></div>
<div class="box"><div class="box-body"><div class="overflow-auto"><table class="table min-w-full whitespace-nowrap table-bordered"><thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th width="150">Actions</th></tr></thead><tbody>
@forelse ($users as $user)
<tr><td>{{ $user->name }}</td><td>{{ $user->username }}</td><td>{{ $user->email ?? '-' }}</td><td>{{ ucfirst($user->user_type) }}</td><td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td><td><a href="{{ route('users.edit', $user) }}" class="ti-btn ti-btn-info-full ti-btn-sm">Edit</a>@if (auth()->id() !== $user->id)<form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button class="ti-btn ti-btn-danger-full ti-btn-sm">Delete</button></form>@endif</td></tr>
@empty <tr><td colspan="6" class="text-center">No users found.</td></tr>
@endforelse
</tbody></table></div>{{ $users->links() }}</div></div>
@endsection
