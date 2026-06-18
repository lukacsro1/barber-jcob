<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@if(app()->getLocale() === 'hu') Adatvédelmi Irányelvek @else Politică de Confidențialitate @endif | Jcob</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#0a0a0a] text-white selection:bg-gold selection:text-dark">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 md:p-12">
            <!-- Language Switcher -->
            <div class="absolute top-8 right-8 flex gap-4 text-xs font-bold uppercase tracking-widest z-50">
                <a href="/lang/ro" class="transition-colors @if(app()->getLocale() === 'ro') text-gold @else text-gray-600 hover:text-white @endif">RO</a>
                <span class="text-white/20">|</span>
                <a href="/lang/hu" class="transition-colors @if(app()->getLocale() === 'hu') text-gold @else text-gray-600 hover:text-white @endif">HU</a>
            </div>

            <div class="w-full max-w-3xl my-12">
                <!-- Brand -->
                <div class="text-center mb-12 relative">
                    <div class="text-3xl font-serif font-bold tracking-widest text-gold uppercase mb-2">Jcob</div>
                    <div class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold italic">The Art of Grooming</div>
                </div>

                <!-- Policy Card -->
                <div class="bg-[#111] border border-white/5 p-8 md:p-12 shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-gold to-transparent opacity-50"></div>

                    @if(app()->getLocale() === 'hu')
                        <!-- Hungarian Content -->
                        <h1 class="text-3xl font-serif text-gold italic mb-8 border-b border-white/5 pb-4">Adatkezelési Tájékoztató</h1>
                        
                        <div class="space-y-6 text-sm text-gray-300 leading-relaxed font-sans">
                            <p>Ez az adatkezelési tájékoztató részletezi, hogyan gyűjtjük, használjuk és védjük az Ön személyes adatait, amikor a <strong>Jcob</strong> időpontfoglaló rendszerét használja.</p>
                            
                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">1. Az adatkezelő adatai</h2>
                                <p>Jcob Grooming Salon. Amennyiben kérdése van az adatkezeléssel kapcsolatban, kérjük lépjen velünk kapcsolatba a szalon elérhetőségein.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">2. A gyűjtött adatok köre</h2>
                                <ul class="list-disc list-inside space-y-1 pl-2">
                                    <li>Név (azonosításhoz)</li>
                                    <li>Telefonszám (kapcsolattartáshoz és megerősítéshez)</li>
                                    <li>Foglalási részletek (választott szolgáltatás, dátum, időpont, borbély)</li>
                                </ul>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">3. Az adatkezelés célja</h2>
                                <p>Az Ön által megadott adatokat kizárólag a kért szolgáltatásra (időpontfoglalás) vonatkozó foglalás kezelésére, visszaigazolására és az esetleges változásokról való értesítésre használjuk.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">4. Az adatkezelés jogalapja</h2>
                                <p>Az adatkezelés jogalapja az Ön kifejezett hozzájárulása, amelyet az időpontfoglalás során a jelölőnégyzet bepipálásával ad meg.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">5. Az adattárolás időtartama</h2>
                                <p>A személyes adatokat a foglalás teljesítését követő 1 évig, vagy a törlési kérelem benyújtásáig tároljuk rendszerünkben, kivéve, ha jogszabályi kötelezettség (pl. számlázás) hosszabb tárolást ír elő.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">6. Az Ön jogai</h2>
                                <p>Önnek joga van hozzáférést kérni személyes adataihoz, kérheti azok helyesbítését, törlését vagy kezelésének korlátozását. Bármikor visszavonhatja hozzájárulását, amely nem érinti a visszavonás előtt végzett adatkezelés jogszerűségét.</p>
                            </div>
                        </div>
                    @else
                        <!-- Romanian Content -->
                        <h1 class="text-3xl font-serif text-gold italic mb-8 border-b border-white/5 pb-4">Politică de Confidențialitate</h1>
                        
                        <div class="space-y-6 text-sm text-gray-300 leading-relaxed font-sans">
                            <p>Această politică de confidențialitate explică modul în care colectăm, utilizăm și protejăm datele dumneavoastră cu caracter personal atunci când folosiți sistemul de rezervări <strong>Jcob</strong>.</p>
                            
                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">1. Operatorul de date</h2>
                                <p>Salonul Jcob Grooming. Dacă aveți întrebări despre prelucrarea datelor dumneavoastră, ne puteți contacta direct prin canalele oficiale ale salonului.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">2. Datele colectate</h2>
                                <ul class="list-disc list-inside space-y-1 pl-2">
                                    <li>Numele (pentru identificarea rezervării)</li>
                                    <li>Numărul de telefon (pentru contact și confirmare)</li>
                                    <li>Detalii rezervare (serviciul selectat, data, ora, frizerul)</li>
                                </ul>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">3. Scopul prelucrării</h2>
                                <p>Datele furnizate sunt utilizate exclusiv pentru gestionarea programărilor dumneavoastră, transmiterea notificărilor prin email/SMS legate de programare și confirmarea detaliilor acesteia.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">4. Temeiul juridic</h2>
                                <p>Temeiul legal al prelucrării este consimțământul dumneavoastră liber exprimat prin bifarea căsuței de acceptare înainte de plasarea programării.</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">5. Durata stocării</h2>
                                <p>Datele sunt păstrate în sistemul nostru timp de 1 an de la data ultimei programări sau până când solicitați ștergerea acestora, cu excepția situațiilor în care legea ne obligă să le păstrăm o perioadă mai lungă (ex. date de facturare).</p>
                            </div>

                            <div>
                                <h2 class="text-base font-serif text-white font-bold uppercase tracking-wider mb-2">6. Drepturile dumneavoastră</h2>
                                <p>Conform GDPR, aveți dreptul de acces la date, rectificare, ștergere ("dreptul de a fi uitat"), restricționarea prelucrării, portabilitatea datelor și dreptul de a vă retrage consimțământul în orice moment, fără a afecta legalitatea prelucrării efectuate înainte de retragere.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-12">
                    <a href="javascript:window.close();" onclick="if(window.opener) { window.close(); } else { window.location.href='/book'; }" class="inline-block px-8 py-3 border border-gold text-gold text-[10px] uppercase tracking-widest font-bold hover:bg-gold/10 transition-all">
                        @if(app()->getLocale() === 'hu') Bezárás / Vissza @else Închide / Înapoi @endif
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
