-- Update question text in used branch, step 5
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks,4,questions,0,question}',
  '"Szerinted milyen gyakran kellene kommunikálni a programról?"'
)
WHERE is_active = true
AND questions->'branches'->'used'->'blocks'->4->'questions'->0->>'id' = 'u_pref_comm_frequency';