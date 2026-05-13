<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Portal | Jcob</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#0a0a0a] text-white overflow-hidden">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-72 bg-[#111] border-r border-white/5 flex flex-col h-full z-30 relative">
                <div class="p-8">
                    <div class="text-2xl font-serif font-bold tracking-widest text-gold uppercase mb-1">Jcob</div>
                    <div class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold">Admin Portal</div>
                </div>

                <!-- Custom Vue Sidebar -->
                <div id="vue-admin-sidebar" 
                    data-path="{{ request()->path() }}" 
                    data-user="{{ json_encode(['name' => auth()->user()->name, 'role' => auth()->user()->role]) }}">
                </div>

                <div class="mt-auto p-6 border-t border-white/5">
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 text-xs text-gray-500 hover:text-red-400 transition-colors uppercase tracking-widest font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-[#0a0a0a] p-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </body>
</html>
