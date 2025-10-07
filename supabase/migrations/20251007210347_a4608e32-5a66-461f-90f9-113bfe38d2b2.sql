
-- Update the impact block with correct scale questions
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,used,blocks}',
  (
    SELECT jsonb_agg(
      CASE 
        WHEN block->>'id' = 'impact' THEN 
          jsonb_set(
            block,
            '{questions}',
            '[
              {
                "id": "u_impact_satisfaction",
                "type": "scale",
                "scale": 5,
                "question": "Mennyire voltál elégedett a kapott segítséggel?",
                "required": true,
                "labels": {
                  "1": "Egyáltalán nem voltam elégedett",
                  "2": "Inkább nem voltam elégedett",
                  "3": "Közepesen elégedett voltam",
                  "4": "Többnyire elégedett voltam",
                  "5": "Teljesen elégedett voltam, nagyon sokat segített"
                }
              },
              {
                "id": "u_impact_problem_solving",
                "type": "scale",
                "scale": 5,
                "question": "Mennyire érezted úgy, hogy a program valóban segített megoldani a problémát?",
                "required": true,
                "labels": {
                  "1": "Egyáltalán nem segített",
                  "2": "Csak egy kicsit segített",
                  "3": "Részben segített, de maradtak kérdések",
                  "4": "Nagyrészt segített megoldani a problémát",
                  "5": "Teljesen megoldotta, amiben segítséget kértem"
                }
              },
              {
                "id": "u_impact_wellbeing",
                "type": "scale",
                "scale": 5,
                "question": "Érezted, hogy a közérzeted javult a program hatására?",
                "required": true,
                "labels": {
                  "1": "Nem, semmit sem változott",
                  "2": "Kicsit jobban éreztem magam",
                  "3": "Volt némi javulás",
                  "4": "Jelentősen jobb lett a közérzetem",
                  "5": "Sokkal jobban érzem magam, mint korábban"
                }
              },
              {
                "id": "u_impact_performance",
                "type": "scale",
                "scale": 5,
                "question": "Volt érezhető változás a munkádban, miután részt vettél a programban?",
                "required": true,
                "labels": {
                  "1": "Egyáltalán nem változott semmi",
                  "2": "Alig volt érezhető hatás",
                  "3": "Kicsit jobban tudtam teljesíteni",
                  "4": "Érezhetően javult a teljesítményem",
                  "5": "Sokkal hatékonyabb, motiváltabb lettem"
                }
              },
              {
                "id": "u_impact_nps",
                "type": "nps",
                "scale": 10,
                "question": "Ajánlanád kollégáknak?",
                "required": true,
                "labels": {
                  "0": "Egyáltalán nem valószínű",
                  "10": "Nagyon valószínű"
                }
              },
              {
                "id": "u_impact_consistency",
                "type": "scale",
                "scale": 5,
                "question": "Ha több alkalommal is használtad, mennyire érezted egyformán jónak a segítséget minden alkalommal?",
                "required": false,
                "labels": {
                  "1": "Nagyon változó volt, volt nagyon jó és nagyon rossz is",
                  "2": "Inkább változó volt",
                  "3": "Nagyjából hasonló volt",
                  "4": "Elég egyenletes volt",
                  "5": "Teljesen egyforma, mindegyik alkalom jó volt"
                }
              }
            ]'::jsonb
          )
        ELSE block
      END
    )
    FROM jsonb_array_elements(questions->'branches'->'used'->'blocks') AS block
  )
)
WHERE is_active = true;
