<x-mail::message>
# Fodrász Fiók Létrehozva

Üdvözlünk a csapatban! A fiókod sikeresen elkészült. Az alábbi adatokkal tudsz bejelentkezni a rendszerbe:

**E-mail cím:** {{ $email }}  
**Jelszó:** {{ $password }}

Kérjük, biztonsági okokból bejelentkezés után változtasd meg a jelszavadat a profilodban!

<x-mail::button :url="config('app.url') . '/login'">
Bejelentkezés
</x-mail::button>

Üdvözlettel,<br>
{{ config('app.name') }}
</x-mail::message>
