-- Update the frequency question in the questionnaire
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,4,questions,2,question}',
  '"Szerinted milyen gyakran kellene kommunikálni a programról?"'
)
WHERE is_active = true;

-- Also update for non-users branch if it exists
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,4,questions,2,question}',
  '"Szerinted milyen gyakran kellene kommunikálni a programról?"'
)
WHERE is_active = true AND questions->'branches'->'not_used' IS NOT NULL;