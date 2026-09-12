<?php
/*
 * SOULSILVER — a kártyával fizethető csomagok EGYETLEN hiteles listája.
 *
 * Az arak.html táblázatával kell egyeznie. Ha ott változik az ár, ITT is
 * változtasd — a fizetes.php és a stripe-checkout.php innen olvas, a
 * böngészőből érkező összeget SOHA nem fogadjuk el (árhamisítás ellen).
 *
 * netto  = nettó ár forintban, ÁFA nélkül (ahogy az arak.html mutatja)
 * tipus  = 'egyszeri' (egyszeri projektdíj) | 'havi' (előfizetés)
 *
 * Az „Egyedi ár" csomagok szándékosan NINCSENEK itt: azokra ajánlat készül,
 * nem lehet őket kattintásra megvenni.
 */
return [

    // ───── Projektalapú, egyszeri díj ─────
    'termekfotozas-belepo' => [
        'nev' => 'Termékfotózás — Belépő', 'netto' => 50000, 'tipus' => 'egyszeri',
        'mit' => 'Packshot · lifestyle · retus', 'oldal' => 'termekfotozas.html',
    ],
    'termekfotozas-kozepso' => [
        'nev' => 'Termékfotózás — Középső', 'netto' => 120000, 'tipus' => 'egyszeri',
        'mit' => 'Packshot · lifestyle · retus', 'oldal' => 'termekfotozas.html',
    ],
    'dronfelvetel-belepo' => [
        'nev' => 'Drónfelvétel — Belépő', 'netto' => 80000, 'tipus' => 'egyszeri',
        'mit' => 'Légi felvétel · cinematic vágás · engedélyeztetés', 'oldal' => 'dronfelvetel.html',
    ],
    'dronfelvetel-kozepso' => [
        'nev' => 'Drónfelvétel — Középső', 'netto' => 180000, 'tipus' => 'egyszeri',
        'mit' => 'Légi felvétel · cinematic vágás · engedélyeztetés', 'oldal' => 'dronfelvetel.html',
    ],
    'workflow-belepo' => [
        'nev' => 'Workflow automatizálás — Belépő', 'netto' => 90000, 'tipus' => 'egyszeri',
        'mit' => 'Folyamatfelmérés · bekötés · betanítás', 'oldal' => 'workflow-automatizalas.html',
    ],
    'workflow-kozepso' => [
        'nev' => 'Workflow automatizálás — Középső', 'netto' => 180000, 'tipus' => 'egyszeri',
        'mit' => 'Folyamatfelmérés · bekötés · betanítás', 'oldal' => 'workflow-automatizalas.html',
    ],
    'workflow-felso' => [
        'nev' => 'Workflow automatizálás — Felső', 'netto' => 490000, 'tipus' => 'egyszeri',
        'mit' => 'Folyamatfelmérés · bekötés · betanítás', 'oldal' => 'workflow-automatizalas.html',
    ],
    'markaidentitas-belepo' => [
        'nev' => 'Márkaidentitás — Belépő', 'netto' => 120000, 'tipus' => 'egyszeri',
        'mit' => 'Logó · színpaletta · arculati kézikönyv', 'oldal' => 'markaidentitas.html',
    ],
    'markaidentitas-kozepso' => [
        'nev' => 'Márkaidentitás — Középső', 'netto' => 350000, 'tipus' => 'egyszeri',
        'mit' => 'Logó · színpaletta · arculati kézikönyv', 'oldal' => 'markaidentitas.html',
    ],
    'aftermovie-belepo' => [
        'nev' => 'Aftermovie — Belépő', 'netto' => 150000, 'tipus' => 'egyszeri',
        'mit' => 'Több kamera · drón · teaser és full változat', 'oldal' => 'aftermovie.html',
    ],
    'aftermovie-kozepso' => [
        'nev' => 'Aftermovie — Középső', 'netto' => 300000, 'tipus' => 'egyszeri',
        'mit' => 'Több kamera · drón · teaser és full változat', 'oldal' => 'aftermovie.html',
    ],
    'weboldal-belepo' => [
        'nev' => 'Weboldalkészítés — Belépő', 'netto' => 150000, 'tipus' => 'egyszeri',
        'mit' => 'Landing · többoldalas site · webshop', 'oldal' => 'weboldalkeszites.html',
    ],
    'weboldal-kozepso' => [
        'nev' => 'Weboldalkészítés — Középső', 'netto' => 350000, 'tipus' => 'egyszeri',
        'mit' => 'Landing · többoldalas site · webshop', 'oldal' => 'weboldalkeszites.html',
    ],

    // ───── Havidíjas, előfizetés (havonta automatikusan terhel) ─────
    'crm-belepo' => [
        'nev' => 'SOULSILVER CRM — Belépő', 'netto' => 9900, 'tipus' => 'havi',
        'mit' => 'Platformok összekötése · dashboard · riasztások', 'oldal' => 'crm.html',
    ],
    'crm-kozepso' => [
        'nev' => 'SOULSILVER CRM — Középső', 'netto' => 24900, 'tipus' => 'havi',
        'mit' => 'Platformok összekötése · dashboard · riasztások', 'oldal' => 'crm.html',
    ],
    'ppc-belepo' => [
        'nev' => 'PPC hirdetéskezelés — Belépő', 'netto' => 90000, 'tipus' => 'havi',
        'mit' => 'Meta · Google · TikTok kampánykezelés', 'oldal' => 'ppc-hirdeteskezeles.html',
    ],
    'ppc-kozepso' => [
        'nev' => 'PPC hirdetéskezelés — Középső', 'netto' => 180000, 'tipus' => 'havi',
        'mit' => 'Meta · Google · TikTok kampánykezelés', 'oldal' => 'ppc-hirdeteskezeles.html',
    ],
    'kozossegi-belepo' => [
        'nev' => 'Közösségi média — Belépő', 'netto' => 120000, 'tipus' => 'havi',
        'mit' => 'Tartalomgyártás · közösségkezelés · hirdetés', 'oldal' => 'kozossegi-media.html',
    ],
    'kozossegi-kozepso' => [
        'nev' => 'Közösségi média — Középső', 'netto' => 250000, 'tipus' => 'havi',
        'mit' => 'Tartalomgyártás · közösségkezelés · hirdetés', 'oldal' => 'kozossegi-media.html',
    ],
];
