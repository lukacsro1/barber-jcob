<x-mail::message>
# Salut {{ $appointment->customer_name }},

Programarea ta a fost înregistrată cu succes la Jcob Barber!

**Detalii programare:**
- **Frizer / Hairstylist:** {{ $appointment->barber->name }}
- **Serviciu:** {{ $appointment->service }}
- **Data și Ora:** {{ \Carbon\Carbon::parse($appointment->start_at)->format('d.m.Y H:i') }}

Te așteptăm cu drag!

@if($password)
---
**Cont creat automat!**  
Am creat un cont de client pentru tine, cu care te poți autentifica pe viitor pentru a-ți gestiona programările.  
**E-mail:** {{ $appointment->customer_email }}  
**Parolă:** {{ $password }}  
@endif

<x-mail::button :url="config('app.url') . '/login'">
Intră în cont
</x-mail::button>

Cu respect,<br>
{{ config('app.name') }}
</x-mail::message>
