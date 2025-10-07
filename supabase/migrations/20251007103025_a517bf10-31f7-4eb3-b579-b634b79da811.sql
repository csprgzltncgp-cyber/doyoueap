
-- Frissítjük a kérdőív kérdéseit érthetőbb megfogalmazásra

-- A "used" ág kérdései
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        jsonb_set(
          jsonb_set(
            jsonb_set(
              jsonb_set(
                questions,
                '{branches,used,blocks,1,questions,0,question}',
                '"Mennyire bízol abban, hogy ha használod a programot, az teljesen névtelen marad, és senki sem tudja meg?"'
              ),
              '{branches,used,blocks,1,questions,1,question}',
              '"Tartasz attól, hogy a főnököd vagy valamelyik közvetlen felettesed megtudja, ha igénybe veszed a programot?"'
            ),
            '{branches,used,blocks,1,questions,2,question}',
            '"Félnél attól, hogy mit gondolnak rólad a kollégáid, ha megtudnák, hogy használtad a programot?"'
          ),
          '{branches,used,blocks,1,questions,3,question}',
          '"Ha egyszer tényleg szükséged lenne rá, szerinted mennyire valószínű, hogy igénybe vennéd a programot?"'
        ),
        '{branches,used,blocks,0,questions,3,question}',
        '"Szerinted mennyire egyszerű számodra elérni vagy használni a programot, ha szeretnéd?"'
      ),
      '{branches,used,blocks,0,questions,1,question}',
      '"Mennyire világos számodra, hogy miben tud segíteni ez a program?"'
    ),
    '{branches,used,blocks,0,questions,4,question}',
    '"Úgy érzed, kaptál elég tájékoztatást arról, hogy miről szól ez a program és hogyan működik?"'
  ),
  '{branches,used,blocks,0,questions,0,question}',
  '"Honnan hallottál először erről a programról?"'
)
WHERE is_active = true;

-- A "not_used" ág kérdései
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        jsonb_set(
          questions,
          '{branches,not_used,blocks,1,questions,0,question}',
          '"Mennyire bízol abban, hogy ha használod a programot, az teljesen névtelen marad, és senki sem tudja meg?"'
        ),
        '{branches,not_used,blocks,1,questions,1,question}',
        '"Tartasz attól, hogy a főnököd vagy valamelyik közvetlen felettesed megtudja, ha igénybe veszed a programot?"'
      ),
      '{branches,not_used,blocks,1,questions,2,question}',
      '"Félnél attól, hogy mit gondolnak rólad a kollégáid, ha megtudnák, hogy használtad a programot?"'
    ),
    '{branches,not_used,blocks,2,questions,0,question}',
    '"Mi az, ami leginkább visszatartana attól, hogy kipróbáld vagy használd a programot?"'
  ),
  '{branches,not_used,blocks,0,questions,0,question}',
  '"Honnan hallottál először erről a programról?"'
)
WHERE is_active = true;

-- Még egy frissítés a "not_used" ág awareness kérdésére
UPDATE questionnaires 
SET questions = jsonb_set(
  questions,
  '{branches,not_used,blocks,0,questions,1,question}',
  '"Mennyire világos számodra, hogy miben tud segíteni ez a program?"'
)
WHERE is_active = true;
