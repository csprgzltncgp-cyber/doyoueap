-- Frissítjük a skála kérdéseket teljes szöveges válaszopciókkal (1-5 helyett)

-- A "used" ág - Awareness blokk
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        jsonb_set(
          questions,
          '{branches,used,blocks,0,questions,1,labels}',
          '{"1": "Egyáltalán nem értem, mire jó", "2": "Csak nagyon homályosan tudom", "3": "Van róla valamennyi elképzelésem", "4": "Elég jól értem, mire szolgál", "5": "Teljesen világos, miben segíthet"}'::jsonb
        ),
        '{branches,used,blocks,0,questions,2,labels}',
        '{"1": "Egyáltalán nem egyszerű, sok akadályba ütközöm", "2": "Elég nehézkes", "3": "Közepesen könnyű", "4": "Viszonylag egyszerű", "5": "Nagyon könnyű és gyors"}'::jsonb
      ),
      '{branches,used,blocks,0,questions,3,labels}',
      '{"1": "Nagyon bonyolult elérni", "2": "Inkább nehéz", "3": "Átlagos", "4": "Elég egyszerű", "5": "Rendkívül egyszerű"}'::jsonb
    ),
    '{branches,used,blocks,0,questions,4,labels}',
    '{"1": "Szinte semmit sem kaptam", "2": "Kevés információt kaptam", "3": "Valamennyit kaptam", "4": "Elég sokat kaptam", "5": "Minden szükséges információt megkaptam"}'::jsonb
  ),
  '{branches,used,blocks,0,questions,5,labels}',
  '{"1": "Egyáltalán nem segítette a munkámat", "2": "Alig volt hatása", "3": "Valamennyire segített", "4": "Elég sokat segített", "5": "Nagyon nagy segítség volt"}'::jsonb
)
WHERE is_active = true;

-- A "used" ág - Trust blokk
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        questions,
        '{branches,used,blocks,1,questions,0,labels}',
        '{"1": "Egyáltalán nem bízom benne, biztosan kiderül", "2": "Inkább nem bízom meg", "3": "Nem vagyok biztos benne", "4": "Elég nagy a bizalmam", "5": "Teljesen bízom a névtelenségben"}'::jsonb
      ),
      '{branches,used,blocks,1,questions,1,labels}',
      '{"1": "Egyáltalán nem tartok tőle", "2": "Alig tartok tőle", "3": "Valamennyire aggódom miatta", "4": "Elég sokat tartok tőle", "5": "Nagyon félek ettől"}'::jsonb
    ),
    '{branches,used,blocks,1,questions,2,labels}',
    '{"1": "Egyáltalán nem félnék", "2": "Alig zavarná", "3": "Kissé kényelmetlennek találnám", "4": "Elég sokat zavarná", "5": "Nagyon félnék tőle"}'::jsonb
  ),
  '{branches,used,blocks,1,questions,3,labels}',
  '{"1": "Biztosan nem venném igénybe", "2": "Valószínűleg nem", "3": "Talán igen, talán nem", "4": "Valószínűleg igénybe venném", "5": "Biztosan használnám"}'::jsonb
)
WHERE is_active = true;

-- A "used" ág - Usage/Impact blokk
UPDATE questionnaires 
SET questions = jsonb_set(
  jsonb_set(
    jsonb_set(
      jsonb_set(
        jsonb_set(
          jsonb_set(
            questions,
            '{branches,used,blocks,2,questions,5,labels}',
            '{"1": "Nagyon eltérő volt a minőség", "2": "Inkább változó volt", "3": "Általában hasonló", "4": "Elég következetes volt", "5": "Mindig egyformán jó volt"}'::jsonb
          ),
          '{branches,used,blocks,2,questions,6,labels}',
          '{"1": "Egyáltalán nem volt hasznos", "2": "Alig segített", "3": "Valamennyire hasznos volt", "4": "Elég sokat segített", "5": "Rendkívül hasznos volt"}'::jsonb
        ),
        '{branches,used,blocks,2,questions,7,labels}',
        '{"1": "Egyáltalán nem éreztem így", "2": "Alig éreztem ezt", "3": "Valamennyire éreztem", "4": "Elég sokat", "5": "Teljes mértékben így éreztem"}'::jsonb
      ),
      '{branches,used,blocks,2,questions,8,labels}',
      '{"1": "Egyáltalán nem segített", "2": "Alig volt hatása", "3": "Valamennyire segített", "4": "Elég sokat javított", "5": "Nagyon sokat segített"}'::jsonb
    ),
    '{branches,used,blocks,2,questions,9,labels}',
    '{"1": "Egyáltalán nem", "2": "Alig tudom elképzelni", "3": "Talán ajánlanám", "4": "Valószínűleg ajánlanám", "5": "Határozottan ajánlanám"}'::jsonb
  ),
  '{branches,used,blocks,2,questions,10,labels}',
  '{"1": "Egyáltalán nem voltam elégedett", "2": "Inkább elégedetlen voltam", "3": "Semleges", "4": "Elégedett voltam", "5": "Teljes mértékben elégedett voltam"}'::jsonb
)
WHERE is_active = true;