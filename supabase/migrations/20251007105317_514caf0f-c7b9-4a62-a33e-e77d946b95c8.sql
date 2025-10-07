-- Frissítjük a nyelvhelyességi hibákat a trust blokk harmadik kérdésében

-- A "used" ág
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,1,questions,2,labels}',
  '{"1": "Egyáltalán nem félnék", "2": "Alig zavarna", "3": "Kissé kényelmetlennek találnám", "4": "Eléggé zavarna", "5": "Nagyon félnék tőle"}'::jsonb
)
WHERE is_active = true;

-- A "not_used" ág
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,1,questions,2,labels}',
  '{"1": "Egyáltalán nem félnék", "2": "Alig zavarna", "3": "Kissé kényelmetlennek találnám", "4": "Eléggé zavarna", "5": "Nagyon félnék tőle"}'::jsonb
)
WHERE is_active = true;