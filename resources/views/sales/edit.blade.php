@extends('layouts.app')
@section('title', 'Edit Sale')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Edit Sale</h3></div></div>
<div class="box"><div class="box-body"><form action="{{ route('sales.update', $sale) }}" method="POST">@csrf @method('PUT') @include('sales._form')<div class="mt-4"><button class="ti-btn ti-btn-primary-full">Update</button><a href="{{ route('sales.index') }}" class="ti-btn ti-btn-light">Cancel</a></div></form></div></div>
@endsection