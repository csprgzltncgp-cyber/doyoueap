-- Restore the original longer branch selector question
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branch_selector,question}',
  '"Tudtad, hogy a munkahelyeden elérhető egy támogatási program? Ez a szolgáltatás segítséget nyújt neked és családodnak különböző munkahelyi vagy magánéleti kihívások kezeléséhez, például stresszhelyzetekben, konfliktusok megoldásában vagy akár pénzügyi tanácsadásban is."'
)
WHERE is_active = true;