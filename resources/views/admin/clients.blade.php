@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <header class="mb-12 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-serif text-white mb-2">{{ $pageTitle }}</h1>
            <p class="text-gray-500 text-sm">Manage your registered customers and their contact details.</p>
        </div>
        <div id="customer-actions">
            <!-- This will be handled by Vue -->
             <button onclick="window.dispatchEvent(new CustomEvent('open-customer-modal'))" class="bg-gold text-dark font-bold px-6 py-3 uppercase tracking-widest text-xs hover:bg-gold-light transition-all">
                Add New Customer
             </button>
        </div>
    </header>

    <!-- Vue Customer Management -->
    <div id="customer-management"
         data-customers="{{ json_encode($clients) }}">
    </div>
</div>
@endsection
