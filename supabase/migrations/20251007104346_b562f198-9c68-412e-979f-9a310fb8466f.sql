-- Frissítjük a további kérdéseket érthetőbb megfogalmazásra

-- A "used" ág kérdései (használati tapasztalatok)
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        jsonb_set(
          jsonb_set(
            jsonb_set(
              jsonb_set(
                jsonb_set(
                  jsonb_set(
                    questions,
                    '{branches,used,blocks,2,questions,0,question}',
                    '"Milyen módon vetted fel a kapcsolatot a programmal? (például telefonon, online felületen vagy személyesen)"'
                  ),
                  '{branches,used,blocks,2,questions,1,question}',
                  '"Milyen problémával vagy kérdéssel fordultál a programhoz?"'
                ),
                '{branches,used,blocks,2,questions,2,question}',
                '"Az elmúlt egy évben körülbelül hányszor használtad a programot?"'
              ),
              '{branches,used,blocks,2,questions,3,question}',
              '"Mennyi idő telt el attól, hogy először jelentkeztél, addig, hogy ténylegesen beszéltél egy szakemberrel?"'
            ),
            '{branches,used,blocks,2,questions,4,question}',
            '"Tudsz róla, hogy a családodból más is igénybe vette a programot?"'
          ),
          '{branches,used,blocks,2,questions,5,question}',
          '"Ha több alkalommal is használtad, mennyire érezted egyformán jónak a segítséget minden alkalommal?"'
        ),
        '{branches,used,blocks,3,questions,0,question}',
        '"Ha választhatnál, milyen módon szeretnéd leginkább elérni a programot?"'
      ),
      '{branches,used,blocks,3,questions,1,question}',
      '"Mikor lenne számodra a legkényelmesebb elérni a programot?"'
    ),
    '{branches,used,blocks,3,questions,2,question}',
    '"Szerinted milyen gyakran érdemes emlékeztetni vagy tájékoztatni a dolgozókat erről a programról?"'
  ),
  '{branches,used,blocks,3,questions,3,question}',
  '"Milyen formában lenne számodra a leghasznosabb a tájékoztatás a programról?"'
)
WHERE is_active = true;

-- A "not_used" ág preferencia kérdései
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        questions,
        '{branches,not_used,blocks,3,questions,0,question}',
        '"Ha választhatnál, milyen módon szeretnéd leginkább elérni a programot?"'
      ),
      '{branches,not_used,blocks,3,questions,1,question}',
      '"Mikor lenne számodra a legkényelmesebb elérni a programot?"'
    ),
    '{branches,not_used,blocks,3,questions,2,question}',
    '"Szerinted milyen gyakran érdemes emlékeztetni vagy tájékoztatni a dolgozókat erről a programról?"'
  ),
  '{branches,not_used,blocks,3,questions,3,question}',
  '"Milyen formában lenne számodra a leghasznosabb a tájékoztatás a programról?"'
)
WHERE is_active = true;