@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <header class="mb-12 flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif text-white mb-2">{{ $pageTitle }}</h1>
            <p class="text-gray-500 text-sm">Review and manage your shop's schedule.</p>
        </div>
        <div class="flex gap-4">
             <!-- Future: Filter by Barber -->
        </div>
    </header>

    <!-- Vue Appointment Calendar -->
    <div id="appointment-calendar" 
         data-appointments="{{ json_encode($appointments) }}"
         data-barbers="{{ json_encode($barbers) }}"
         data-role="{{ $user['role'] }}">
    </div>
</div>
@endsection
