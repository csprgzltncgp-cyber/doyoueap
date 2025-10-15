-- Eltávolítjuk a duplikált usage_intent blokkokat és beszúrjuk egyszer, a usefulness elé
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks}',
  (
    SELECT jsonb_agg(elem ORDER BY 
      CASE 
        WHEN elem->>'id' = 'awareness_light' THEN 1
        WHEN elem->>'id' = 'trust_light' THEN 2
        WHEN elem->>'id' = 'motivation' THEN 3
        WHEN elem->>'id' = 'usage_intent' THEN 4
        WHEN elem->>'id' = 'usefulness' THEN 5
        ELSE 99
      END
    )
    FROM (
      SELECT DISTINCT ON (elem->>'id') elem
      FROM jsonb_array_elements(questions->'branches'->'not_used'->'blocks') AS elem
      WHERE elem->>'id' IN ('awareness_light', 'trust_light', 'motivation', 'usage_intent', 'usefulness')
    ) subq
  )
)
WHERE is_active = true;