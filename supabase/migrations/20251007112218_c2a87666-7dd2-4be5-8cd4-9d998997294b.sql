-- 1. Javítjuk a "Tartasz attól, hogy a főnököd" kérdés 4-es válaszát mindkét ágban (not_used és used)
UPDATE questionnaires
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks,1,questions,1,labels,4}',
    '"Eléggé tartok tőle"'
  ),
  '{branches,used,blocks,1,questions,1,labels,4}',
  '"Eléggé tartok tőle"'
)
WHERE is_active = true;

-- 2. Frissítjük a "Mi az, ami leginkább visszatartana" kérdést és válaszokat a not_used ágban
UPDATE questionnaires
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks,2,questions,0,question}',
    '"Mi az, ami leginkább visszatartana attól, hogy kipróbáld vagy használd a programot?"'
  ),
  '{branches,not_used,blocks,2,questions,0,options}',
  '["Nem bízom benne teljesen", "Úgy érzem, ez nem nekem való", "Nincs rá időm", "Túl bonyolultnak tűnik", "Inkább más módon kérnék segítséget", "Valami más okból"]'::jsonb
)
WHERE is_active = true;

-- 4. Módosítjuk a konzisztencia kérdést skáláról normál opciókra a used ágban
UPDATE questionnaires
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      questions,
      '{branches,used,blocks,3,questions,5,question}',
      '"Ha több alkalommal is használtad, mennyire érezted egyformán jónak a segítséget minden alkalommal?"'
    ),
    '{branches,used,blocks,3,questions,5,type}',
    '"single_choice"'
  ),
  '{branches,used,blocks,3,questions,5,options}',
  '["Nagyon változó volt, volt nagyon jó és nagyon rossz is", "Inkább változó volt", "Nagyjából hasonló volt", "Elég egyenletes volt", "Teljesen egyforma, mindegyik alkalom jó volt"]'::jsonb
)
WHERE is_active = true;

-- Eltávolítjuk a scale és labels mezőket, mert már nem kell
UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,5,scale}' #- '{branches,used,blocks,3,questions,5,labels}'
WHERE is_active = true;