<?php

return [
    'close-case' => 'Az esetlap lezárását követően az esetlap nem lesz többé elérhető! Kérjük, jegyezze fel a esethez kapcsolódó tanácsadások számát, hogy azt felhasználhassa később számlázáskor. Biztosan folytatni szeretné az eset lezárását?',
    'closeable-case' => 'Az eset lezárható, kérjük véglegesítse az eset lezárását az \':BUTTON\' gomb megnyomásával!',
    'first_consultation_wos_warning' => 'Az első ülés végén ne felejts el a WOS gombra kattintani, hogy feltehesd a WOS felmérés kérdéseit a kliensnek. A kérdőív kitöltése nélkül nem lehetséges második ülést hozzáadni az esetlaphoz!',
    'last_consultation_wos_warning' => 'Az utolsó ülés végén ne felejts el a WOS gombra kattintani, hogy feltehesd a WOS felmérés kérdéseit a kliensnek. A kérdőív kitöltése nélkül nem lehetséges az esetlapot lezárni!',

    'wos_instruction' => 'Kérjük, tedd fel a kliensnek az alábbi 6 kérdést és rögzítsd a válaszokat!',
    'wos_survey_warning' => 'Kérem töltse ki a kérdőívet!',
    'wos_first_consultation_warning' => 'Az első ülés után nem töltötte ki a WOS kérdőívet!',
    'wos_case_close_warning' => 'A utolsó ülés után nem töltötte ki a WOS kérdőívet!',
    'wos_max_survey_warning' => 'Az esethethez nem lehet több WOS kérdőívet kitölteni!',
    'invalid_wos_save' => 'Az esetben csak az első és utolsó ülést követően lehet WOS kérdőívet kitölteni!',
    'wos_questions' => [
        1 => 'Hiányzott-e a munkából az elmúlt 30 napban?',
        2 => 'Milyen gyakran történt meg az elmúlt 30 napban, hogy személyes problémái miatt nem tudott dolgozni?   Számolja bele a teljes munkanapokat és azokat a részben teljesített munkanapokat, amikor később érkezett vagy hamarabb távozott. Válassza ki azt a kategóriát, amely a legjobban leírja távolmaradásának teljes idejét (ha volt):',
        3 => 'Eddig úgy tűnik, hogy az életem rendben halad előre.',
        4 => 'Gyakran alig várom, hogy beérjek a munkahelyre, és elkezdjem a napot.',
        5 => 'A személyes problémáim megakadályozták, hogy a munkára tudjak koncentrálni.',
        6 => 'Rettegek a munkába járástól.',
    ],
    'wos_answers' => [
        1 => [
            1 => 'Egyszer sem',
            2 => 'Néhány napig',
        ],
        2 => [
            1 => 'Nem volt semennyi távolmaradás',
            2 => 'Kevesebb, mint fél napnyi távolmaradás',
            3 => 'Fél és egész nap közötti távolmaradás',
            4 => '1-3 nap közötti távolmaradás',
            5 => 'Több, mint 3 napnyi távolmaradás',
        ],
        3 => [
            1 => 'Egyáltalán nem értek egyet',
            2 => 'Kis mértékben nem értek egyet',
            3 => 'Semleges',
            4 => 'Kis mértékben egyetértek',
            5 => 'Határozottan egyetértek',
        ],
    ],

    'cant_assign_case' => [
        'title' => 'Miért nem vállalja ek az esetet? Kérjük válasszon!',
        'not_available_1' => 'A következő',
        'not_available_2' => 'naptári napban nem tudok eseteket vállalni.',
        'professional' => 'Szakmai okok miatt.',
        'ethical' => 'Etikai okok miatt.',
    ],

    'case_save_phone_number' => 'Telefonszám mentése',

    'case_no_phone_number' => 'Nincs telefonszám',

    'case_field_required' => 'Mezők kitöltése kötelező!',

    'case_phone_number_only_number_error' => 'A telefonszám csak számokat tartalmazhat!',

    'case_phone_number_length_error' => 'A telefonszám nem lehet hosszabb mint 15 karakter!',
];
