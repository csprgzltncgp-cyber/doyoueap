-- Kérdések átírása feltételes módba a nem használók ágában
UPDATE questionnaires
SET questions = jsonb_set(
  jsonb_set(
    questions,
    '{branches,not_used,blocks}',
    (
      SELECT jsonb_agg(
        CASE 
          WHEN block->>'id' = 'trust_light' THEN
            jsonb_set(
              block,
              '{questions}',
              (
                SELECT jsonb_agg(
                  CASE 
                    WHEN q->>'id' = 'nu_trust_anonymity' THEN
                      jsonb_set(q, '{question}', '"Mennyire bízol abban, hogy ha használnád a programot, az teljesen névtelen marad, és senki sem tudja meg?"')
                    WHEN q->>'id' = 'nu_trust_employer' THEN
                      jsonb_set(q, '{question}', '"Tartasz attól, hogy a főnököd vagy valamelyik közvetlen felettesed megtudja, ha igénybe vennéd a programot?"')
                    ELSE q
                  END
                )
                FROM jsonb_array_elements(block->'questions') AS q
              )
            )
          ELSE block
        END
      )
      FROM jsonb_array_elements(questions->'branches'->'not_used'->'blocks') AS block
    )
  ),
  '{branches,not_used,blocks}',
  (
    SELECT jsonb_agg(
      CASE 
        WHEN block->>'id' = 'trust_light' THEN
          jsonb_set(
            block,
            '{questions}',
            (
              SELECT jsonb_agg(
                CASE 
                  WHEN q->>'id' = 'nu_trust_anonymity' THEN
                    jsonb_set(q, '{question}', '"Mennyire bízol abban, hogy ha használnád a programot, az teljesen névtelen marad, és senki sem tudja meg?"')
                  WHEN q->>'id' = 'nu_trust_employer' THEN
                    jsonb_set(q, '{question}', '"Tartasz attól, hogy a főnököd vagy valamelyik közvetlen felettesed megtudja, ha igénybe vennéd a programot?"')
                  ELSE q
                END
              )
              FROM jsonb_array_elements(block->'questions') AS q
            )
          )
        ELSE block
      END
    )
    FROM jsonb_array_elements(questions->'branches'->'not_used'->'blocks') AS block
  )
)
WHERE id = '1ed222be-a62f-4db5-bd42-a4bd392602fb';