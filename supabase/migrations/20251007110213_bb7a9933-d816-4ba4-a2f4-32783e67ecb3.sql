-- Visszaállítjuk a preferencia kérdések típusát és opcióit (egyszerűbb megközelítéssel)

-- 1. Csatorna preferencia (used ág, block 3, question 0)
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,used,blocks,3,questions,0,type}',
    '"single_choice"'
  ),
  '{branches,used,blocks,3,questions,0,options}',
  '["Telefonon", "Online platformon (chat, videó)", "Email", "Személyesen", "Mobilalkalmazás"]'::jsonb
)
WHERE is_active = true;

-- Eltávolítjuk a scale és labels mezőket
UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,0,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,0,labels}'
WHERE is_active = true;

-- 2. Időpont preferencia (used ág, block 3, question 1)
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,used,blocks,3,questions,1,type}',
    '"single_choice"'
  ),
  '{branches,used,blocks,3,questions,1,options}',
  '["Munkaidőben", "Munkaidőn kívül", "Hétvégén is", "Bármikor (0-24)", "Rugalmasan"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,1,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,1,labels}'
WHERE is_active = true;

-- 3. Kommunikáció gyakorisága (used ág, block 3, question 2)
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,used,blocks,3,questions,2,type}',
    '"single_choice"'
  ),
  '{branches,used,blocks,3,questions,2,options}',
  '["Hetente", "Havonta", "Negyedévente", "Félévente", "Évente egyszer", "Csak új belépőknek"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,2,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,2,labels}'
WHERE is_active = true;

-- 4. Tartalom típus (used ág, block 3, question 3)
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,used,blocks,3,questions,3,type}',
    '"single_choice"'
  ),
  '{branches,used,blocks,3,questions,3,options}',
  '["Email hírlevél", "Rövid videók", "Poszterek/plakátok", "Webinar/online előadás", "Személyes workshopok", "Infografika"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,3,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,used,blocks,3,questions,3,labels}'
WHERE is_active = true;

-- not_used ág preferencia kérdései
-- 1. Csatorna preferencia
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks,3,questions,0,type}',
    '"single_choice"'
  ),
  '{branches,not_used,blocks,3,questions,0,options}',
  '["Telefonon", "Online platformon (chat, videó)", "Email", "Személyesen", "Mobilalkalmazás"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,0,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,0,labels}'
WHERE is_active = true;

-- 2. Időpont preferencia
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks,3,questions,1,type}',
    '"single_choice"'
  ),
  '{branches,not_used,blocks,3,questions,1,options}',
  '["Munkaidőben", "Munkaidőn kívül", "Hétvégén is", "Bármikor (0-24)", "Rugalmasan"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,1,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,1,labels}'
WHERE is_active = true;

-- 3. Kommunikáció gyakorisága
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks,3,questions,2,type}',
    '"single_choice"'
  ),
  '{branches,not_used,blocks,3,questions,2,options}',
  '["Hetente", "Havonta", "Negyedévente", "Félévente", "Évente egyszer", "Csak új belépőknek"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,2,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,2,labels}'
WHERE is_active = true;

-- 4. Tartalom típus
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks,3,questions,3,type}',
    '"single_choice"'
  ),
  '{branches,not_used,blocks,3,questions,3,options}',
  '["Email hírlevél", "Rövid videók", "Poszterek/plakátok", "Webinar/online előadás", "Személyes workshopok", "Infografika"]'::jsonb
)
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,3,scale}'
WHERE is_active = true;

UPDATE questionnaires
SET questions = questions #- '{branches,not_used,blocks,3,questions,3,labels}'
WHERE is_active = true;