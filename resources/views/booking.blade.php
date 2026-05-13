<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Book Appointment | Jcob</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#0a0a0a] text-white">
        <div id="booking-app" 
            data-csrf="{{ csrf_token() }}"
            data-barbers="{{ json_encode($barbers) }}"
            data-services="{{ json_encode($services) }}"
            data-appointments="{{ json_encode($appointments) }}"
            data-success="{{ session('success') }}"
            data-translations="{{ json_encode($translations) }}"
            data-locale="{{ $locale }}"
        ></div>
    </body>
</html>
