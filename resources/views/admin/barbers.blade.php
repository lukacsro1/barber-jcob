@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <header class="mb-12 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-serif text-white mb-2">{{ $pageTitle }}</h1>
            <p class="text-gray-500 text-sm">Manage your team of professional barbers.</p>
        </div>
        <div id="barber-actions">
            <!-- This will be handled by Vue if needed, or just a simple button for now -->
             <button onclick="window.dispatchEvent(new CustomEvent('open-barber-modal'))" class="bg-gold text-dark font-bold px-6 py-3 uppercase tracking-widest text-xs hover:bg-gold-light transition-all">
                Add New Barber
             </button>
        </div>
    </header>

    <!-- Vue Barber Management -->
    <div id="barber-management" 
         data-barbers="{{ json_encode($barbers) }}">
    </div>
</div>
@endsection
