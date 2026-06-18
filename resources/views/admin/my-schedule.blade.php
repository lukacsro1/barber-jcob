@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <header class="mb-12">
        <h1 class="text-4xl font-serif text-white mb-2">{{ $pageTitle }}</h1>
        <p class="text-gray-500 text-sm">Manage your weekly working hours and planned days off.</p>
    </header>

    <div id="my-schedule"
         data-user="{{ json_encode($user) }}">
    </div>
</div>
@endsection
