-- Update the question text in the questionnaire
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,2,questions,0,question}',
  '"Mi az, ami leginkább visszatart attól, hogy kipróbáld vagy használd a programot?"'
)
WHERE is_active = true
AND questions->'branches'->'not_used'->'blocks'->2->'questions'->0->>'id' = 'nu_motivation_what';