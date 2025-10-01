-- Remove the option "Nincs szükségem semmire, most is használnám" from the question
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,2,questions,0}',
  '{
    "id": "nu_motivation_what",
    "question": "Mi kellene ahhoz, hogy kipróbáld az EAP-ot?",
    "required": true,
    "type": "multiple_choice",
    "options": [
      "Több információ a szolgáltatásról",
      "Egyszerűbb elérhetőség",
      "Pozitív kollégai tapasztalatok",
      "Vezetői támogatás és ösztönzés",
      "Biztos anonimitás garanciája",
      "Kipróbálási lehetőség mentorral",
      "Célzott marketing anyagok"
    ]
  }'::jsonb
)
WHERE title LIKE '%EAP%';