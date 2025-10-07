-- Eltávolítjuk a zárójeles részt a "Milyen módon vetted fel..." kérdésből

UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,2,questions,0,question}',
  '"Milyen módon vetted fel a kapcsolatot a programmal?"'
)
WHERE is_active = true;