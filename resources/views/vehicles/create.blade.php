@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Add Vehicle</h3></div></div>
<div class="box"><div class="box-body"><form action="{{ route('vehicles.store') }}" method="POST">@csrf @include('vehicles._form')<div class="mt-4"><button class="ti-btn ti-btn-primary-full">Save</button><a href="{{ route('vehicles.index') }}" class="ti-btn ti-btn-light">Cancel</a></div></form></div></div>
@endsection