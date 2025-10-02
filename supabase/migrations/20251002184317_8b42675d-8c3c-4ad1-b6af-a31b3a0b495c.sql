-- Update questionnaire to replace all "EAP" references with "program"
UPDATE questionnaires
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branch_selector,question}',
    '"Tudsz arról, hogy a céged programot működtet munkavállalói támogatásra?"'
  ),
  '{branches,not_used,blocks,0,questions,0,question}',
  '"Honnan hallottál a programról?"'
)
WHERE is_active = true;

-- Update awareness understanding question
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,0,questions,1,question}',
  '"Mennyire érted, mit kínál a program?"'
)
WHERE is_active = true;

-- Update motivation question
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,2,questions,0,question}',
  '"Mi kellene ahhoz, hogy kipróbáld a programot?"'
)
WHERE is_active = true;

-- Update used branch frequency question
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,0,questions,5,question}',
  '"Milyen gyakran találkozol kommunikációval a programról?"'
)
WHERE is_active = true;