<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-black text-white min-h-screen font-sans antialiased">
    
    <!-- Navbar -->
    <nav class="border-b border-white/10 bg-[#0a0a0a]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="/" class="text-gold font-bold tracking-widest uppercase hover:text-white transition-colors">
                        JCOB BARBER
                    </a>
                    <span class="text-gray-500 hidden sm:inline">|</span>
                    <span class="text-gray-400 text-sm hidden sm:inline">Client Portal</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-300">Salut, {{ $user->name }}</span>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="text-sm text-gray-400 hover:text-white transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8 border-b border-white/10 pb-6">
            <h1 class="text-3xl font-bold text-white tracking-wide">Programările mele</h1>
            <p class="text-gray-400 mt-2 text-sm">Aici poți vizualiza toate programările tale viitoare și trecute.</p>
        </div>

        @if($appointments->isEmpty())
            <div class="bg-[#111] border border-white/10 p-10 text-center rounded-sm">
                <div class="text-gray-500 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">Nu ai nicio programare</h3>
                <p class="text-gray-400 text-sm mb-6">Momentan nu există programări asociate acestui cont.</p>
                <a href="/" class="inline-block bg-gold text-black px-6 py-3 text-xs uppercase tracking-widest font-bold hover:bg-white transition-colors">Fă o programare nouă</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($appointments as $appointment)
                    @php
                        $isPast = \Carbon\Carbon::parse($appointment->start_at)->isPast();
                        $date = \Carbon\Carbon::parse($appointment->start_at)->translatedFormat('d F Y');
                        $time = \Carbon\Carbon::parse($appointment->start_at)->format('H:i');
                    @endphp
                    
                    <div class="bg-[#111] border border-white/10 p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 {{ $isPast ? 'opacity-50' : '' }}">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-xl font-semibold text-gold">{{ $date }}</span>
                                <span class="bg-white/5 px-2 py-1 text-xs text-gray-300 rounded">{{ $time }}</span>
                                
                                @if($appointment->status === 'cancelled')
                                    <span class="bg-red-500/20 text-red-400 px-2 py-1 text-xs rounded uppercase tracking-wider">Anulat</span>
                                @elseif($isPast)
                                    <span class="bg-white/10 text-gray-400 px-2 py-1 text-xs rounded uppercase tracking-wider">Trecut</span>
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-medium text-white mb-1">{{ $appointment->service }}</h3>
                            <div class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Frizer / Hairstylist: {{ $appointment->barber->name ?? 'N/A' }}
                            </div>
                        </div>
                        
                        <div class="text-right w-full sm:w-auto">
                            @if(!$isPast && $appointment->status !== 'cancelled')
                                <a href="tel:+40700000000" class="text-xs text-gray-400 hover:text-white transition-colors underline decoration-white/30 underline-offset-4">Anulare / Modificare telefonică</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center sm:text-left">
                <a href="/" class="inline-block bg-white/5 border border-white/10 text-white px-6 py-3 text-xs uppercase tracking-widest font-bold hover:bg-white/10 transition-colors">O nouă programare</a>
            </div>
        @endif
    </main>

</body>
</html>
