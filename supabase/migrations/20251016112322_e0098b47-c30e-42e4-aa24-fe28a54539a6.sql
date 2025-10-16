-- 1. Kérdőív frissítése: új pozitív motivátor kérdés hozzáadása a nem használta ághoz
UPDATE questionnaires
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks}',
  (
    SELECT jsonb_agg(
      CASE 
        WHEN block->>'id' = 'motivation' THEN
          jsonb_set(
            block,
            '{questions}',
            (block->'questions') || jsonb_build_array(
              jsonb_build_object(
                'id', 'nu_motivation_positive',
                'type', 'multiple_choice',
                'question', 'Mi lenne az, ami megkönnyítené számodra a program kipróbálását?',
                'required', true,
                'options', jsonb_build_array(
                  'Egyszerűbb elérhetőség',
                  'Pozitív kollégai tapasztalatok',
                  'Biztos anonimitás garanciája',
                  'Több információ a szolgáltatásról',
                  'Vezetői támogatás és ösztönzés',
                  'Kipróbálási lehetőség mentorral'
                )
              )
            )
          )
        ELSE block
      END
    )
    FROM jsonb_array_elements(questions->'branches'->'not_used'->'blocks') AS block
  )
)
WHERE is_active = true;

-- 2. Adatmigráció: pozitív motivátorok szétválasztása
-- Pozitív motivátorok listája
DO $$
DECLARE
  positive_motivators text[] := ARRAY[
    'Egyszerűbb elérhetőség',
    'Pozitív kollégai tapasztalatok',
    'Biztos anonimitás garanciája',
    'Több információ a szolgáltatásról',
    'Vezetői támogatás és ösztönzés',
    'Kipróbálási lehetőség mentorral'
  ];
  rec record;
  current_values jsonb;
  positive_values jsonb := '[]'::jsonb;
  negative_values jsonb := '[]'::jsonb;
  val text;
BEGIN
  -- Végigmegyünk minden válaszon
  FOR rec IN 
    SELECT id, responses 
    FROM audit_responses 
    WHERE responses ? 'nu_motivation_what'
    AND jsonb_typeof(responses->'nu_motivation_what') = 'array'
  LOOP
    current_values := rec.responses->'nu_motivation_what';
    positive_values := '[]'::jsonb;
    negative_values := '[]'::jsonb;
    
    -- Szétválogatjuk a válaszokat
    FOR val IN SELECT jsonb_array_elements_text(current_values)
    LOOP
      IF val = ANY(positive_motivators) THEN
        positive_values := positive_values || jsonb_build_array(val);
      ELSE
        negative_values := negative_values || jsonb_build_array(val);
      END IF;
    END LOOP;
    
    -- Frissítjük a rekordot
    UPDATE audit_responses
    SET responses = jsonb_set(
      jsonb_set(
        responses,
        '{nu_motivation_what}',
        negative_values
      ),
      '{nu_motivation_positive}',
      positive_values
    )
    WHERE id = rec.id;
  END LOOP;
END $$;