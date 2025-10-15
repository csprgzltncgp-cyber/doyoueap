-- Frissítjük az aktív kérdőívet két új igen/nem kérdéssel a "nem használta" ágban
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks}',
  (
    SELECT 
      -- Meglévő blokkok
      (SELECT jsonb_agg(elem) FROM jsonb_array_elements(questions->'branches'->'not_used'->'blocks') AS elem) ||
      -- Új "usage_intent" blokk a "usefulness" blokk ELŐTT
      jsonb_build_array(
        jsonb_build_object(
          'id', 'usage_intent',
          'title', 'Használati szándék',
          'questions', jsonb_build_array(
            jsonb_build_object(
              'id', 'nu_usage_would_use',
              'question', 'Használnád a programot, ha a jövőben szükséged lenne rá?',
              'type', 'yesno',
              'required', true
            ),
            jsonb_build_object(
              'id', 'nu_usage_plan_to_use',
              'question', 'Tervezed is esetleg a közeljövőben igénybe venni a programot?',
              'type', 'yesno',
              'required', true
            )
          )
        )
      )
    FROM questionnaires WHERE is_active = true
  )
)
WHERE is_active = true;