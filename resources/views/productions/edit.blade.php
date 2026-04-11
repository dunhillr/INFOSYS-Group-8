@extends('layouts.app')
@section('title', 'Edit Production')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Edit Production</h3></div></div>
<div class="box"><div class="box-body"><form action="{{ route('productions.update', $production) }}" method="POST">@csrf @method('PUT') @include('productions._form')<div class="mt-4"><button class="ti-btn ti-btn-primary-full">Update</button><a href="{{ route('productions.index') }}" class="ti-btn ti-btn-light">Cancel</a></div></form></div></div>
@endsection