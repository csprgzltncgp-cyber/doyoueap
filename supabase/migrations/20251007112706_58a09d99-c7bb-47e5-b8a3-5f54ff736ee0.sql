-- Frissítjük a "Mi tartana vissza leginkább?" kérdést a USED ágban is
UPDATE questionnaires
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,used,blocks,1,questions,4,question}',
    '"Mi az, ami leginkább visszatartana attól, hogy kipróbáld vagy használd a programot?"'
  ),
  '{branches,used,blocks,1,questions,4,options}',
  '["Nem bízom benne teljesen", "Úgy érzem, ez nem nekem való", "Nincs rá időm", "Túl bonyolultnak tűnik", "Inkább más módon kérnék segítséget", "Valami más okból"]'::jsonb
)
WHERE is_active = true;