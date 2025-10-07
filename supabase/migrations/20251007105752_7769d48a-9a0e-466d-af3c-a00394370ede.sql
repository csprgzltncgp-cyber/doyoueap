-- Frissítjük a "Mi az, ami leginkább visszatartana..." kérdés válaszopciót érthetőbbre

UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,2,questions,0,options}',
  '["Nem bízom meg annyira a névtelenségben", "Úgy érzem, nem nekem szól ez a szolgáltatás", "Nincs időm vagy lehetőségem igénybe venni", "Túl bonyolultnak tűnik az elérése vagy használata", "Inkább más módon kérnék segítséget", "Egyéb ok"]'::jsonb
)
WHERE is_active = true;