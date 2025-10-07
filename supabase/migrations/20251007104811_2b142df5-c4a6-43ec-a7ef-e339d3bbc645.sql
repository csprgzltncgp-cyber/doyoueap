-- Frissítjük a "not_used" ág skála kérdéseit is teljes szöveges válaszopciókkal

-- A "not_used" ág - Awareness blokk
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        questions,
        '{branches,not_used,blocks,0,questions,1,labels}',
        '{"1": "Egyáltalán nem értem, mire jó", "2": "Csak nagyon homályosan tudom", "3": "Van róla valamennyi elképzelésem", "4": "Elég jól értem, mire szolgál", "5": "Teljesen világos, miben segíthet"}'::jsonb
      ),
      '{branches,not_used,blocks,0,questions,2,labels}',
      '{"1": "Egyáltalán nem egyszerű, sok akadályba ütközöm", "2": "Elég nehézkes", "3": "Közepesen könnyű", "4": "Viszonylag egyszerű", "5": "Nagyon könnyű és gyors"}'::jsonb
    ),
    '{branches,not_used,blocks,0,questions,3,labels}',
    '{"1": "Nagyon bonyolult elérni", "2": "Inkább nehéz", "3": "Átlagos", "4": "Elég egyszerű", "5": "Rendkívül egyszerű"}'::jsonb
  ),
  '{branches,not_used,blocks,0,questions,4,labels}',
  '{"1": "Szinte semmit sem kaptam", "2": "Kevés információt kaptam", "3": "Valamennyit kaptam", "4": "Elég sokat kaptam", "5": "Minden szükséges információt megkaptam"}'::jsonb
)
WHERE is_active = true;

-- A "not_used" ág - Trust blokk
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        questions,
        '{branches,not_used,blocks,1,questions,0,labels}',
        '{"1": "Egyáltalán nem bízom benne, biztosan kiderül", "2": "Inkább nem bízom meg", "3": "Nem vagyok biztos benne", "4": "Elég nagy a bizalmam", "5": "Teljesen bízom a névtelenségben"}'::jsonb
      ),
      '{branches,not_used,blocks,1,questions,1,labels}',
      '{"1": "Egyáltalán nem tartok tőle", "2": "Alig tartok tőle", "3": "Valamennyire aggódom miatta", "4": "Elég sokat tartok tőle", "5": "Nagyon félek ettől"}'::jsonb
    ),
    '{branches,not_used,blocks,1,questions,2,labels}',
    '{"1": "Egyáltalán nem félnék", "2": "Alig zavarná", "3": "Kissé kényelmetlennek találnám", "4": "Elég sokat zavarná", "5": "Nagyon félnék tőle"}'::jsonb
  ),
  '{branches,not_used,blocks,1,questions,3,labels}',
  '{"1": "Biztosan nem venném igénybe", "2": "Valószínűleg nem", "3": "Talán igen, talán nem", "4": "Valószínűleg igénybe venném", "5": "Biztosan használnám"}'::jsonb
)
WHERE is_active = true;