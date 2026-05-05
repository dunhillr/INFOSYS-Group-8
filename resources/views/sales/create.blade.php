@extends('layouts.app')
@section('title', 'Add Sale')
@section('content')
<div class="block justify-between page-header md:flex mt-4"><div><h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 font-semibold">Add Sale</h3></div></div>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Whoops!</strong>
        <span class="block sm:inline">There were some problems with your input.</span>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="box"><div class="box-body"><form action="{{ route('sales.store') }}" method="POST">@csrf @include('sales._form')<div class="mt-4"><button class="ti-btn ti-btn-primary-full">Save</button><a href="{{ route('sales.index') }}" class="ti-btn ti-btn-light">Cancel</a></div></form></div></div>
@endsection
