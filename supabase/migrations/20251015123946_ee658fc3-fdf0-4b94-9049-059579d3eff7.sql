UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,4,questions,3,question}',
  '"Szerinted milyen gyakran kellene kommunikálni a programról?"'
)
WHERE is_active = true;