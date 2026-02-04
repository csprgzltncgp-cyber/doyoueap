<?php

return [
    'step1' => [
        'title' => 'Kedves Partnerünk!',
        'text' => '
            <p>Ahogy azt már bizonyára sokszor tapasztalta, folyamatosan keressük azokat az online megoldásokat, amelyek segítségével egyszerűbbé és átláthatóbbá tehetjük a közös munkát. Ennek szellemében egy újabb fejlesztést tervezünk az Expert Dashboardon idén ősszel.</p>
            <span>A fejlesztés fókuszában ezúttal egy új Profil menü létrehozása kerül. A Profil menü tartalmazni fogja a szakértők elérhetőségeit, úgy mint</span>
            <ul>
                <li>e-mail cím,</li>
                <li>postázási cím,</li>
                <li>telefonszám,</li>
            </ul>
            <span>valamint az alábbi dokumentumok szkennelt verzióját</span>
            <ul>
                <li>szakmai képesítésre vonatkozó dokumentumok,</li>
                <li>a CGP Europe és a szakértő között létrejött, aláírt szerződés.</li>
            </ul>
            <p>Kérjük, kattintson a tovább gombra, és töltse ki az üres adatmezőket, illetve ellenőrizze a már kitöltött mezőkben megjelenő adatokat. Amennyiben eltérést talál, kérjük írja be a mezőbe a jó adatot. Ezen felül kérjük, töltse fel a szkennelt szerződését, továbbá a szakképesítését igazoló dokumentumokat.</p>
            <p>Együttműködését előre is köszönjük!</p>
            <p><strong style="font-family: CalibriB;">Üdvözlettel,</strong><br>CGP Europe</p>
        ',
    ],
    'step4' => [
        'text' => '
            <p>Úgy látjuk, hogy a dokumentumok feltöltése még hiányos. Nem szeretnénk feltartani, ezért most így, hiányosan mentjük el az adatokat az új Profil menüben, amit megtekinthet a továbblépést követően.
            <strong><span style="color:#f70000; font-family: CalibriB;">Azonban a következő bejelentkezésig kérjük, készítse elő a hiányzó dokumentumok szkennelt verzióját, mivel a feltöltésük akkor már kötelező lesz, a dashboardra csak azok feltöltését követően fog tudni belépni.</span></strong></p>
            <p>Együttműködését előre is köszönjük!</p>
            <p><strong style="font-family: CalibriB;">Üdvözlettel,</strong><br>CGP Europe</p>
        ',
    ],
    'phone' => 'Telefonszám',
    'phone_prefix' => 'Országhívó',
    'post_code' => 'Irányítószám',
    'city' => 'Város',
    'country' => 'Ország',
    'street' => 'Utca',
    'house_number' => 'Házszám',
    'street_suffix' => [
        'title' => 'Közterület',
        '1' => 'Utca',
        '2' => 'Tér',
        '3' => 'Út',
    ],
    'hourly_rate' => 'Óradíj',
    'currency' => 'Devizanem',
    'scanned-contract' => 'Szerződés szkennelt verziója',
    'scanned-certificate' => 'Szakképesítést igazoló dokumentumok szkennelt verziója',
    'profile-menu-data' => 'Profil menü adatok',
    'contact-informations' => 'Kapcsolati információk',
    'eap-online-informations' => 'EAP Online információk',
    'post-address' => 'Postázási cím',
    'professional-informations' => 'Szakmai információk',
    'invoice-informations' => 'Számlázási információk',
    'thank-you-for-your-cooperation' => 'Köszönjük az adatmezők kitöltését!',
    'contionue-to-the-dashboard' => 'TOVÁBB A DASHBOARDRA',
    'download' => 'Letöltés',
    'next' => 'Tovább',
    'expert-dashboard-informations' => 'Expert Dashbaord információk',
    'is_cgp_employee' => 'CGP munkatárs',
    'is_eap_online_expert' => 'EAP online szakértő',
    'max_inprogress_cases' => 'Maximális folyamatban lévő esetek száma',
    'min_inprogress_cases' => 'Minimális folyamatban lévő esetek száma',
    'description' => 'Leírás... (Max. 180 karakter)',
    'native_language' => 'Anyanyelv',
    'crisis_countries' => 'Krízis országok',

    'warnings' => [
        'currency_required' => 'A devizanem megadása kötelező!',
        'hourly_rate_50_required' => 'Az óradíj megadása kötelező!',
        'hourly_rate_30_required' => 'Az óradíj megadása kötelező!',
        'post_code_required' => 'Az irányítózám megadása kötelező!',
        'city_id_required' => 'A város megadása kötelező!',
        'country_id_required' => 'Az ország megadása kötelező!',
        'street_required' => 'Az utca megadása kötelező!',
        'street_suffix_required' => 'A közterület megadása kötelező!',
        'house_number_required' => 'A házszám megadása kötelező!',
        'name_required' => 'A név megadása kötelező!',
        'email_required' => 'Az email cím megadása kötelező!',
        'username_required' => 'A felhasználó név megadása kötelező!',
        'language_required' => 'A nyelv kiválasztása kötelező!',
        'country_required' => 'Legalább egy célország kiválasztása kötelező!',
        'city_required' => 'A város kiválasztása kötelező!',
        'permissions_required' => 'Legalább egy szakterületet meg kell adni!',
        'max_inprogress_cases_required' => 'Adja meg a maximális folyamatban lévő esetek számát!',
        'max_inprogress_cases_num' => 'A maximális folyamatban lévő esetek száma csak szám adat lehet!',
        'min_inprogress_cases_num' => 'A minimális folyamatban lévő esetek száma csak szám adat lehet!',
        'phone_prefix_required' => 'Az országhívó kiválasztása kötelező!',
        'phone_number_required' => 'A telefonszám megadása kötelező!',
        'contracts_required' => 'A szerződés szkennelt verzióját töltse fel!',
        'certificates_required' => 'A szakképesítést igazoló dokumentumok szkennelt verzióját töltse fel!',
        'is_cgp_employee' => 'Jelölje meg hogy a szakértő CGP munkatárs vagy nem!',
        'is_eap_online_expert' => 'Jelölje meg hogy a szakértő EAP online szakértő vagy nem!',
        'specializations_required' => 'Legalább egy specilaizáció kiválasztása kötelező!',
        'language_skills_required' => 'Legalább egy nyelvtudás kiválasztása kötelező!',
        'native_language' => 'A szakértő anyanyelvének megadása kötelező!',
        'fixed_wage' => 'A nettó fix díj megadása kötelező!',
    ],

    'specialization' => 'Specializáció',
    'language_skills' => 'Nyelvtudás',
    'is_fixed_wage' => 'Fix díjazás',
];
