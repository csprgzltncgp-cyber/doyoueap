import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckCircle2, Mail, QrCode, Link2, Calendar, Gift, TrendingUp, BarChart } from 'lucide-react';

const StepByStep = () => {
  return (
    <div className="space-y-6 pt-20 md:pt-0">
      <div>
        <h2 className="text-2xl font-bold mb-2">A felmérés menete – lépésről lépésre</h2>
        <p className="text-muted-foreground">
          Az EAP Pulse felmérés célja, hogy pontosabb képet adjon a vállalat által biztosított EAP program működéséről, 
          ismertségéről és hatásáról. A folyamat egyszerű, jól átlátható, és mindössze néhány beállítást igényel az indulás előtt.
        </p>
      </div>

      <div className="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5 text-primary" />
              1. A felmérés előkészítése
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <p>
              Első lépésként ki kell választani, hogyan érik el a munkavállalók a kérdőívet. 
              A rendszer három különböző megoldást kínál, attól függően, hogy milyen kommunikációs csatornát használ a vállalat leggyakrabban:
            </p>
            
            <div className="space-y-4 pl-4">
              <div className="border-l-2 border-primary pl-4">
                <h4 className="font-semibold flex items-center gap-2 mb-2">
                  <Link2 className="h-4 w-4" />
                  Nyilvános link
                </h4>
                <p className="text-sm text-muted-foreground">
                  Ez a legegyszerűbb megoldás. Egyetlen linket kell megosztani, amelyet bárki elérhet, akinek továbbítják. 
                  Ideális választás, ha a vállalat intraneten, belső hírlevélben, Teams-csatornán vagy akár e-mailben 
                  szeretné elérni a munkavállalókat. A nyilvános link gyorsan terjeszthető, nincs szükség előzetes adatlistára, 
                  és bármilyen belső kommunikációs felületre beilleszthető.
                </p>
              </div>

              <div className="border-l-2 border-primary pl-4">
                <h4 className="font-semibold flex items-center gap-2 mb-2">
                  <Mail className="h-4 w-4" />
                  Egyedi e-mailes link
                </h4>
                <p className="text-sm text-muted-foreground">
                  Itt minden munkavállaló egy személyre szóló hivatkozást kap e-mailben. Ez lehetőséget ad arra, hogy a vállalat 
                  pontosan lássa, ki kapott meghívót, és ki nyitotta meg a kérdőívet. Ehhez szükség van egy e-mail-cím listára, 
                  amelyet Excel formátumban kell feltölteni a rendszerbe. A lista csak a kiküldéshez szükséges adatokat tartalmazza, 
                  a válaszadás során semmilyen személyes információ nem kerül mentésre. Fontos, hogy a válaszadás így is teljesen 
                  anonim – a rendszer kizárólag a kitöltés tényét rögzíti, nem magát a választ. Ez a megoldás különösen hasznos, 
                  ha a vállalat nagyobb visszajelzési arányt szeretne elérni, vagy ha fontos, hogy minden munkavállaló biztosan 
                  megkapja a felkérést.
                </p>
              </div>

              <div className="border-l-2 border-primary pl-4">
                <h4 className="font-semibold flex items-center gap-2 mb-2">
                  <QrCode className="h-4 w-4" />
                  QR-kód
                </h4>
                <p className="text-sm text-muted-foreground">
                  Ez a leglátványosabb és leginkább „offline" megoldás, amely papíralapú kommunikációs eszközökre – például 
                  plakátokra vagy szórólapokra – épül. A QR-kódot érdemes jól látható helyekre kihelyezni a közösségi terekben, 
                  mint például az irodai konyha, pihenő, öltöző vagy folyosó. A munkavállalók a kód beolvasásával azonnal eljutnak 
                  a kérdőívhez, és egyetlen mozdulattal, akár a telefonjukról is részt vehetnek a felmérésben. A QR-megoldás 
                  különösen hatékony azokban a munkakörnyezetekben, ahol a dolgozók nem ülnek napi szinten számítógép előtt. 
                  A vállalat ehhez támogatást kap a Kommunikáció menüben, ahol plakát- és szórólapminták, valamint szövegjavaslatok 
                  segítik a felmérés népszerűsítését és a vizuális anyagok gyors elkészítését.
                </p>
              </div>
            </div>

            <p className="mt-4">
              Ezzel egyidőben beállítható a felmérés kezdő és záró dátuma is. Amint elindul a megadott időszak, 
              a kérdőív automatikusan aktiválódik, és a beérkező válaszok valós időben megjelennek a riportok menüben.
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Mail className="h-5 w-5 text-primary" />
              2. A felmérés kommunikálása
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              A rendszer többféle sablont kínál, amelyek segítségével gyorsan elkészíthető a felhívás szövege – 
              akár e-mailhez, akár intranetes hírhez vagy QR-plakáthoz. A felhívásban érdemes röviden jelezni, 
              hogy a felmérés anonim, a kitöltés néhány percet vesz igénybe, és – ha van sorsolás – a kitöltők 
              között ajándék kerül kisorsolásra. A megfelelő kommunikációval biztosítható, hogy a munkavállalók 
              megértsék, miért fontos a részvétel, és bátran megosszák a véleményüket.
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CheckCircle2 className="h-5 w-5 text-primary" />
              3. A kitöltés folyamata
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              A munkavállalók a megosztott linkre kattintva vagy a QR-kód beolvasásával érik el a felmérést. 
              A kérdések egyszerűek és gyakorlatiasak, a kitöltés jellemzően 2–5 percet vesz igénybe. 
              A válaszok teljes mértékben anonimok, semmilyen személyes adat nem kerül rögzítésre. 
              A kérdőív végén a munkavállaló önkéntesen megadhat egy e-mail-címet, kizárólag azért, 
              hogy értesítést kaphasson a sorsolás eredményéről, ha nyer.
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BarChart className="h-5 w-5 text-primary" />
              4. A felmérés lezárása és az eredmények
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-muted-foreground">
              A beállított zárási időpont után a felmérés automatikusan lezárul, új kitöltések már nem érkeznek be. 
              A rendszer frissíti a 4Score riportokat, amelyek négy fő mutatót jelenítenek meg:
            </p>
            <ul className="space-y-2 pl-4">
              <li className="flex items-start gap-2">
                <CheckCircle2 className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                <span className="text-muted-foreground">
                  <strong>Tudatosság</strong> – mennyire ismert a program a munkavállalók körében
                </span>
              </li>
              <li className="flex items-start gap-2">
                <CheckCircle2 className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                <span className="text-muted-foreground">
                  <strong>Használat</strong> – milyen arányban és gyakorisággal veszik igénybe
                </span>
              </li>
              <li className="flex items-start gap-2">
                <CheckCircle2 className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                <span className="text-muted-foreground">
                  <strong>Bizalom és hajlandóság</strong> – mennyire érzik biztonságosnak és hasznosnak
                </span>
              </li>
              <li className="flex items-start gap-2">
                <CheckCircle2 className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                <span className="text-muted-foreground">
                  <strong>Hatás</strong> – milyen tényleges változást tapasztalnak a résztvevők
                </span>
              </li>
            </ul>
            <p className="text-muted-foreground">
              Az eredmények valós időben megtekinthetők, és exportálhatók PDF- vagy Excel-formátumban.
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Gift className="h-5 w-5 text-primary" />
              5. A nyereményjáték
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-muted-foreground">
              A nyereményjáték a felmérés opcionális része – nem kötelező, de segíthet abban, hogy többen vegyenek részt a kitöltésben.
            </p>
            <p className="text-muted-foreground">
              A munkavállalók a kitöltés végén egyedi nyereménykódot kapnak. Ha e-mail-címet is megadnak, a rendszer ezen keresztül 
              értesíti a nyertest. Mivel a nyereményjáték – akárcsak a felmérés – anonim, a nyertes sorszámát minden esetben belső 
              csatornán is közzé kell tenni, így mindenki értesülhet az eredményről, függetlenül attól, hogy megadott-e e-mail címet.
            </p>
            <p className="text-muted-foreground">
              A csomag részeként biztosított ajándékok mellett lehetőség van saját ajándék hozzáadására is – például utalvány, 
              céges termék, vagy a vállalati kultúrához illő élmény formájában.
            </p>
            <div className="bg-muted/50 p-4 rounded-lg">
              <p className="text-sm font-medium mb-2">A sorsolás kétféleképpen működhet:</p>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li className="flex items-start gap-2">
                  <CheckCircle2 className="h-4 w-4 text-primary mt-0.5 flex-shrink-0" />
                  <span><strong>Automatikusan:</strong> a rendszer a lezárás után véletlenszerűen kiválaszt egy nyereménykódot, 
                  és elküldi az értesítést a nyertesnek.</span>
                </li>
                <li className="flex items-start gap-2">
                  <CheckCircle2 className="h-4 w-4 text-primary mt-0.5 flex-shrink-0" />
                  <span><strong>Manuálisan:</strong> a vállalat saját döntése alapján indíthatja el a sorsolást és 
                  teheti közzé az eredményt.</span>
                </li>
              </ul>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <TrendingUp className="h-5 w-5 text-primary" />
              6. Ismétlés és trendek követése
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              A felmérés futtatható egyszeri alkalommal, vagy – csomagszinttől függően – automatikus ismétléssel is, 
              negyedéves, féléves vagy éves gyakorisággal. Ilyenkor a rendszer a korábbi beállítások alapján újraindítja 
              a felmérést, és a riportokban összehasonlíthatók a különböző körök eredményei, így pontosan láthatóvá válik 
              a program fejlődése.
            </p>
          </CardContent>
        </Card>

        <Card className="bg-primary/5 border-primary/20">
          <CardHeader>
            <CardTitle>Összegzés</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              Az EAP Pulse felmérés egyszerűen kezelhető, biztonságos és megbízható eszköz arra, hogy a vállalat 
              átfogó képet kapjon az EAP program működéséről. A munkavállalók anonim módon mondhatják el tapasztalataikat, 
              a rendszer pedig valós idejű visszajelzésekkel, részletes statisztikákkal és – igény esetén – ajándéksorsolással 
              támogatja a program folyamatos fejlesztését.
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default StepByStep;