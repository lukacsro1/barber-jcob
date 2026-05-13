@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <header class="mb-12">
        <h1 class="text-4xl font-serif text-white mb-2">{{ $pageTitle }}</h1>
        <p class="text-gray-500 text-sm">Welcome back, {{ $user['name'] }}. Here's what's happening today.</p>
    </header>

    <!-- Vue Stats/Dashboard -->
    <div id="admin-dashboard" 
         data-user="{{ json_encode($user) }}"
         data-stats="{{ json_encode($stats) }}"
         data-services="{{ json_encode($services) }}">
    </div>
</div>
@endsection
