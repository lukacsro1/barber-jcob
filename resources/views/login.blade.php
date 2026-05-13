<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>The Gentry | Premium Barber Shop</title>
        <meta name="description" content="Experience the finest grooming services at The Gentry. Premium haircuts, beard trims, and hot towel shaves.">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#0a0a0a] text-white">
        <div id="app" 
            data-csrf="{{ csrf_token() }}"
            data-errors="{{ json_encode($errors->all()) }}"
            data-old-email="{{ old('email') }}"
        ></div>
    </body>

</html>