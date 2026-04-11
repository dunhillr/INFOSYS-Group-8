@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Add User</h3></div></div>
<div class="box"><div class="box-body"><form action="{{ route('users.store') }}" method="POST">@csrf @include('users._form')<div class="mt-4"><button class="ti-btn ti-btn-primary-full">Save</button><a href="{{ route('users.index') }}" class="ti-btn ti-btn-light">Cancel</a></div></form></div></div>
@endsection