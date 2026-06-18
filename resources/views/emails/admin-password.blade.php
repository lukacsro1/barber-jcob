<x-mail::message>
# Admin Hozzáférés

Üdvözlünk! Az adminisztrációs felülethez a következő adatokkal tudsz bejelentkezni:

**E-mail cím:** {{ $email }}  
**Jelszó:** {{ $password }}

Kérjük, biztonsági okokból bejelentkezés után változtasd meg a jelszavadat!

<x-mail::button :url="config('app.url') . '/login'">
Bejelentkezés
</x-mail::button>

Üdvözlettel,<br>
{{ config('app.name') }}
</x-mail::message>
