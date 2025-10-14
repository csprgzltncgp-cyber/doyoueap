-- Update email templates to remove {programName} placeholder
UPDATE communication_templates
SET 
  subject = 'Véleményed számít - Töltsd ki a felmérést!',
  content = 'Kedves Kolléga!

Szeretnénk megismerni a véleményedet a munkahelyi támogatási programunkról. Kérünk, szánj néhány percet a felmérés kitöltésére:

{link}

A válaszaid anonim módon kerülnek feldolgozásra, és segítenek nekünk, hogy még jobb szolgáltatást nyújtsunk.

Köszönjük a részvételedet!

Üdvözlettel,
HR Csapat'
WHERE template_type = 'email' AND has_gift = false;

UPDATE communication_templates
SET 
  subject = 'Véleményed számít - Töltsd ki a felmérést és nyerj!',
  content = 'Kedves Kolléga!

Szeretnénk megismerni a véleményedet a munkahelyi támogatási programunkról. Kérünk, szánj néhány percet a felmérés kitöltésére:

{link}

A felmérés kitöltői között értékes nyereményeket sorsolunk ki!

A válaszaid anonim módon kerülnek feldolgozásra, és segítenek nekünk, hogy még jobb szolgáltatást nyújtsunk.

Köszönjük a részvételedet!

Üdvözlettel,
HR Csapat'
WHERE template_type = 'email' AND has_gift = true;

-- Update public_link templates to remove {programName} placeholder
UPDATE communication_templates
SET content = 'Kedves Kollégák!

Szeretnénk megismerni a véleményeteket a munkahelyi támogatási programunkról. 

Kérünk benneteket, szánjatok néhány percet a felmérés kitöltésére az alábbi linken:

{link}

A válaszaitok anonim módon kerülnek feldolgozásra, és segítenek nekünk, hogy még jobb szolgáltatást nyújtsunk.

Köszönjük a részvételeteket!

HR Csapat'
WHERE template_type = 'public_link' AND has_gift = false;

UPDATE communication_templates
SET content = 'Kedves Kollégák!

Szeretnénk megismerni a véleményeteket a munkahelyi támogatási programunkról. 

Kérünk benneteket, szánjatok néhány percet a felmérés kitöltésére az alábbi linken:

{link}

A felmérés kitöltői között értékes nyereményeket sorsolunk ki!

A válaszaitok anonim módon kerülnek feldolgozásra, és segítenek nekünk, hogy még jobb szolgáltatást nyújtsunk.

Köszönjük a részvételeteket!

HR Csapat'
WHERE template_type = 'public_link' AND has_gift = true;

-- Update qr_code templates to remove {programName} placeholder
UPDATE communication_templates
SET content = 'OSZD MEG A VÉLEMÉNYED!

Szeretnénk megismerni a véleményedet a munkahelyi támogatási programunkról.

Szkenneld be a QR kódot, vagy látogass el az alábbi linkre:

{link}

A válaszaid anonim módon kerülnek feldolgozásra.

Köszönjük a részvételedet!'
WHERE template_type = 'qr_code' AND has_gift = false;

UPDATE communication_templates
SET content = 'OSZD MEG A VÉLEMÉNYED ÉS NYERJ!

Szeretnénk megismerni a véleményedet a munkahelyi támogatási programunkról.

Szkenneld be a QR kódot, vagy látogass el az alábbi linkre:

{link}

A felmérés kitöltői között értékes nyereményeket sorsolunk ki!

A válaszaid anonim módon kerülnek feldolgozásra.

Köszönjük a részvételedet!'
WHERE template_type = 'qr_code' AND has_gift = true;