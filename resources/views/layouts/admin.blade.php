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
        <div class="flex h-screen overflow-hidden relative">
            <!-- Sidebar -->
            <aside id="sidebar" class="w-72 bg-[#111] border-r border-white/5 flex flex-col h-full z-40 fixed inset-y-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0">
                <div class="p-8 flex justify-between items-center lg:block">
                    <div>
                        <div class="text-2xl font-serif font-bold tracking-widest text-gold uppercase mb-1">Jcob</div>
                        <div class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold">Admin Portal</div>
                    </div>
                    <!-- Close button on mobile -->
                    <button id="sidebar-close" class="lg:hidden text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
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

            <!-- Overlay -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black/70 z-30 hidden transition-opacity duration-300 opacity-0"></div>

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col h-full overflow-hidden w-full">
                <!-- Mobile Header -->
                <header class="lg:hidden bg-[#111] border-b border-white/5 p-4 flex items-center justify-between z-20">
                    <button id="sidebar-toggle" class="text-white hover:text-gold transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <div class="text-xl font-serif font-bold text-gold">Jcob</div>
                    <div class="w-6"></div> <!-- Spacer for centering -->
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto bg-[#0a0a0a] p-6 md:p-8 lg:p-12">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- JS for Toggle -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggleBtn = document.getElementById('sidebar-toggle');
                const closeBtn = document.getElementById('sidebar-close');
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');

                function openSidebar() {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    setTimeout(() => {
                        overlay.classList.add('opacity-100');
                    }, 10);
                }

                function closeSidebar() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100');
                    setTimeout(() => {
                        overlay.classList.add('hidden');
                    }, 300);
                }

                if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
                if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
                if (overlay) overlay.addEventListener('click', closeSidebar);
            });
        </script>
    </body>
</html>
