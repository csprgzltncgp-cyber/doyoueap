import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { BookOpen, Calculator, TrendingUp, Users, Shield, Activity, Target, Eye } from 'lucide-react';

const Methodology = () => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Módszertan és Értékelési Rendszer</h2>
        <p className="text-muted-foreground">
          Az EAP Pulse felmérés értékelési módszertana és a mutatók értelmezése
        </p>
      </div>

      {/* Overview Cards */}
      <div className="grid md:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <BookOpen className="h-5 w-5 text-blue-600" />
              <CardTitle>Felmérési Módszertan</CardTitle>
            </div>
          </CardHeader>
          <CardContent className="space-y-2 text-sm">
            <p><strong>Kérdőív típusa:</strong> Strukturált, ágrajz alapú (branching logic)</p>
            <p><strong>Skálák:</strong> 5-fokozatú Likert skálák (1 = egyáltalán nem, 5 = teljes mértékben)</p>
            <p><strong>Anonimitás:</strong> 100% névtelen válaszadás, csak demográfiai metadata</p>
            <p><strong>Átlagos kitöltési idő:</strong> 3-5 perc</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <Calculator className="h-5 w-5 text-purple-600" />
              <CardTitle>Számítási Módszerek</CardTitle>
            </div>
          </CardHeader>
          <CardContent className="space-y-2 text-sm">
            <p><strong>Százalékos értékek:</strong> (Érték / Maximum) × 100</p>
            <p><strong>Átlagok:</strong> Aritmetikai átlag 2 tizedesjegy pontossággal</p>
            <p><strong>Indexek:</strong> Normalizált értékek 0-100% skálán</p>
            <p><strong>Trendek:</strong> Időbeli delta számítás (új - régi)</p>
          </CardContent>
        </Card>
      </div>

      {/* Main Metrics Methodology */}
      <Card>
        <CardHeader>
          <CardTitle className="text-xl">Fő Mutatók Részletes Leírása</CardTitle>
          <CardDescription>Hogyan számítjuk és mit jelentenek a kulcs metrikák</CardDescription>
        </CardHeader>
        <CardContent>
          <Accordion type="single" collapsible className="w-full">
            {/* Utilization */}
            <AccordionItem value="utilization">
              <AccordionTrigger className="text-lg font-semibold">
                <div className="flex items-center gap-2">
                  <Users className="h-5 w-5 text-blue-600" />
                  Igénybevétel (Utilization)
                </div>
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Definíció:</h4>
                  <p className="text-sm text-muted-foreground">
                    Az elmúlt 12 hónapban az EAP szolgáltatást ténylegesen igénybe vevő munkavállalók aránya a teljes munkaerő létszámához képest.
                  </p>
                </div>
                
                <div>
                  <h4 className="font-semibold mb-2">Számítási formula:</h4>
                  <div className="bg-muted p-4 rounded-lg font-mono text-sm">
                    Igénybevétel % = (Szolgáltatást használók száma / Összes munkavállaló) × 100
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Al-mutatók:</h4>
                  <ul className="space-y-2 text-sm">
                    <li><strong>Részvételi arány:</strong> Hány munkavállaló töltötte ki a felmérést (válaszadók / összes munkavállaló)</li>
                    <li><strong>Használók aránya a kitöltőkből:</strong> A válaszadók közül hányan használták a szolgáltatást</li>
                  </ul>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Értelmezés:</h4>
                  <ul className="space-y-1 text-sm text-muted-foreground">
                    <li>• <strong>0-3%:</strong> Alacsony, fejlesztendő</li>
                    <li>• <strong>3-7%:</strong> Átlagos, iparági szint</li>
                    <li>• <strong>7-12%:</strong> Jó, a benchmark felett</li>
                    <li>• <strong>12%+:</strong> Kiváló, best practice</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>

            {/* Satisfaction Index */}
            <AccordionItem value="satisfaction">
              <AccordionTrigger className="text-lg font-semibold">
                <div className="flex items-center gap-2">
                  <Target className="h-5 w-5 text-green-600" />
                  Elégedettségi Index
                </div>
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Definíció:</h4>
                  <p className="text-sm text-muted-foreground">
                    A szolgáltatást használók általános elégedettsége 0-100% skálán, az 1-5 Likert skála alapján számítva.
                  </p>
                </div>
                
                <div>
                  <h4 className="font-semibold mb-2">Számítási formula:</h4>
                  <div className="bg-muted p-4 rounded-lg font-mono text-sm">
                    Index % = (Átlagos elégedettség / 5) × 100
                  </div>
                  <p className="text-xs text-muted-foreground mt-2">
                    ahol: Átlagos elégedettség = ΣÉrtékek / n (1-5 skálán)
                  </p>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Értelmezés:</h4>
                  <ul className="space-y-1 text-sm text-muted-foreground">
                    <li>• <strong>0-40%:</strong> Kritikus, azonnali beavatkozás szükséges</li>
                    <li>• <strong>40-60%:</strong> Alacsony, jelentős fejlesztés javasolt</li>
                    <li>• <strong>60-75%:</strong> Átlagos, van tér a fejlődésre</li>
                    <li>• <strong>75-90%:</strong> Jó, pozitív visszajelzés</li>
                    <li>• <strong>90%+:</strong> Kiváló, magas szintű elégedettség</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>

            {/* 4Score System */}
            <AccordionItem value="4score">
              <AccordionTrigger className="text-lg font-semibold">
                <div className="flex items-center gap-2">
                  <TrendingUp className="h-5 w-5 text-purple-600" />
                  4Score Rendszer
                </div>
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Rendszer áttekintés:</h4>
                  <p className="text-sm text-muted-foreground mb-4">
                    A 4Score egy holisztikus értékelési keretrendszer, amely négy kulcsfontosságú területen méri az EAP program sikerét.
                  </p>
                </div>

                <div className="grid md:grid-cols-2 gap-4">
                  <div className="border rounded-lg p-4">
                    <div className="flex items-center gap-2 mb-2">
                      <Eye className="h-4 w-4 text-blue-600" />
                      <h5 className="font-semibold">1. Ismertség (Awareness)</h5>
                    </div>
                    <p className="text-xs text-muted-foreground mb-2">
                      <strong>Mit mér:</strong> A munkavállalók tisztában vannak-e a program létezésével és részleteivel
                    </p>
                    <p className="text-xs text-muted-foreground">
                      <strong>Számítás:</strong> Szolgáltatás megértése, igénybevételi tudás, elérhetőség érzete átlaga (1-5 skála)
                    </p>
                  </div>

                  <div className="border rounded-lg p-4">
                    <div className="flex items-center gap-2 mb-2">
                      <Shield className="h-4 w-4 text-green-600" />
                      <h5 className="font-semibold">2. Bizalom (Trust)</h5>
                    </div>
                    <p className="text-xs text-muted-foreground mb-2">
                      <strong>Mit mér:</strong> Megbíznak-e a munkavállalók az anonimitásban és biztonsági garaniákban
                    </p>
                    <p className="text-xs text-muted-foreground">
                      <strong>Számítás:</strong> Anonimitásba vetett bizalom, munkaadói és kollegiális félelmek átlaga (1-5 skála)
                    </p>
                  </div>

                  <div className="border rounded-lg p-4">
                    <div className="flex items-center gap-2 mb-2">
                      <Activity className="h-4 w-4 text-purple-600" />
                      <h5 className="font-semibold">3. Használat (Usage)</h5>
                    </div>
                    <p className="text-xs text-muted-foreground mb-2">
                      <strong>Mit mér:</strong> Hányan veszik ténylegesen igénybe a szolgáltatást
                    </p>
                    <p className="text-xs text-muted-foreground">
                      <strong>Számítás:</strong> (Használók / Összes munkavállaló) × 100%
                    </p>
                  </div>

                  <div className="border rounded-lg p-4">
                    <div className="flex items-center gap-2 mb-2">
                      <Target className="h-4 w-4 text-orange-600" />
                      <h5 className="font-semibold">4. Hatás (Impact)</h5>
                    </div>
                    <p className="text-xs text-muted-foreground mb-2">
                      <strong>Mit mér:</strong> Milyen mértékben segített a program a problémák megoldásában
                    </p>
                    <p className="text-xs text-muted-foreground">
                      <strong>Számítás:</strong> Elégedettség, problémamegoldás, jóllét, teljesítmény átlaga (1-5 skála)
                    </p>
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>

            {/* NPS */}
            <AccordionItem value="nps">
              <AccordionTrigger className="text-lg font-semibold">
                Net Promoter Score (NPS)
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Definíció:</h4>
                  <p className="text-sm text-muted-foreground">
                    Az NPS azt méri, hogy a munkavállalók mennyire valószínű, hogy ajánlanák a szolgáltatást kollégáiknak (0-10 skála).
                  </p>
                </div>
                
                <div>
                  <h4 className="font-semibold mb-2">Kategorizálás:</h4>
                  <ul className="space-y-2 text-sm">
                    <li><strong>Kritikusok (0-6):</strong> Nem ajánlanák, negatív élmény</li>
                    <li><strong>Passzívak (7-8):</strong> Semlegesek, elégedettek, de nem lelkesek</li>
                    <li><strong>Promóterek (9-10):</strong> Lelkesen ajánlanák, pozitív élmény</li>
                  </ul>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Számítási formula:</h4>
                  <div className="bg-muted p-4 rounded-lg font-mono text-sm">
                    NPS = (Promóterek % - Kritikusok %) = -100 és +100 között
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Értelmezés:</h4>
                  <ul className="space-y-1 text-sm text-muted-foreground">
                    <li>• <strong>-100 - 0:</strong> Kritikus, sürgős javítás szükséges</li>
                    <li>• <strong>0 - 30:</strong> Gyenge, jelentős fejlesztés javasolt</li>
                    <li>• <strong>30 - 50:</strong> Jó, pozitív visszajelzés</li>
                    <li>• <strong>50 - 70:</strong> Kiváló, magas lojalitás</li>
                    <li>• <strong>70+:</strong> Világklasszis, kivételes teljesítmény</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>

            {/* Statistical Methods */}
            <AccordionItem value="stats">
              <AccordionTrigger className="text-lg font-semibold">
                Statisztikai Módszerek
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Leíró statisztika:</h4>
                  <ul className="space-y-2 text-sm text-muted-foreground">
                    <li>• <strong>Átlag (Mean):</strong> Központi tendencia mérése</li>
                    <li>• <strong>Százalékos eloszlás:</strong> Kategóriák relatív gyakorisága</li>
                    <li>• <strong>Csoportosítás:</strong> Demográfiai szegmentálás (nem, kor, stb.)</li>
                  </ul>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Adatkezelés:</h4>
                  <ul className="space-y-2 text-sm text-muted-foreground">
                    <li>• <strong>Kerekítés:</strong> 1 tizedesjegy százalékokhoz, 2 az átlagokhoz</li>
                    <li>• <strong>Hiányzó adatok:</strong> Kizárjuk a számításból (listwise deletion)</li>
                    <li>• <strong>Minimum mintanagyság:</strong> 30 válasz a megbízható értékeléshez</li>
                  </ul>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Időbeli összehasonlítás:</h4>
                  <ul className="space-y-2 text-sm text-muted-foreground">
                    <li>• <strong>Delta (Δ):</strong> Abszolút változás = Új érték - Régi érték</li>
                    <li>• <strong>Trend értelmezés:</strong> |Δ| &gt; 0.5 = szignifikáns változás</li>
                    <li>• <strong>Színkódolás:</strong> Zöld = javulás, Piros = romlás, Szürke = stabil</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>

            {/* Data Privacy */}
            <AccordionItem value="privacy">
              <AccordionTrigger className="text-lg font-semibold">
                Adatvédelem és Anonimitás
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Anonimitási garanciák:</h4>
                  <ul className="space-y-2 text-sm text-muted-foreground">
                    <li>• Egyéni válaszok nem visszakövethetők</li>
                    <li>• Csak aggregált adatok kerülnek megjelenítésre</li>
                    <li>• IP címek és böngésző fingerprintek nem tárolódnak</li>
                    <li>• Demográfiai csoportok minimális 5 fős létszámmal</li>
                  </ul>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">GDPR megfelelőség:</h4>
                  <ul className="space-y-2 text-sm text-muted-foreground">
                    <li>• Adatkezelési időtartam: Beállítható (alapértelmezett 365 nap)</li>
                    <li>• Törlési jog: Az adatok automatikusan törlődnek a meghatározott időszak után</li>
                    <li>• Jogalap: Munkáltatói jogos érdek + munkavállalói beleegyezés</li>
                  </ul>
                </div>
              </AccordionContent>
            </AccordionItem>

            {/* Visualization */}
            <AccordionItem value="viz">
              <AccordionTrigger className="text-lg font-semibold">
                Vizualizációs Irányelvek
              </AccordionTrigger>
              <AccordionContent className="space-y-4 pt-4">
                <div>
                  <h4 className="font-semibold mb-2">Használt diagram típusok:</h4>
                  <ul className="space-y-2 text-sm text-muted-foreground">
                    <li>• <strong>Gauge (íves) diagram:</strong> Egyedi KPI-ok, indexek megjelenítése (0-100%)</li>
                    <li>• <strong>Oszlopdiagram (Bar Chart):</strong> Kategóriák összehasonlítása</li>
                    <li>• <strong>Kördiagram (Pie Chart):</strong> Részarányok megjelenítése (max. 6 kategória)</li>
                    <li>• <strong>Vonal diagram (Line Chart):</strong> Időbeli trendek követése</li>
                    <li>• <strong>Horizontális oszlopdiagram:</strong> Többváltozós összehasonlítások</li>
                  </ul>
                </div>

                <div>
                  <h4 className="font-semibold mb-2">Színkód rendszer:</h4>
                  <div className="grid grid-cols-5 gap-2 mt-2">
                    <div className="text-center">
                      <div className="h-12 rounded mb-1" style={{ backgroundColor: '#000099' }}></div>
                      <p className="text-xs">#000099<br/>Sötétkék</p>
                    </div>
                    <div className="text-center">
                      <div className="h-12 rounded mb-1" style={{ backgroundColor: '#3366ff' }}></div>
                      <p className="text-xs">#3366ff<br/>Kék</p>
                    </div>
                    <div className="text-center">
                      <div className="h-12 rounded mb-1" style={{ backgroundColor: '#33ccff' }}></div>
                      <p className="text-xs">#33ccff<br/>Világoskék</p>
                    </div>
                    <div className="text-center">
                      <div className="h-12 rounded mb-1" style={{ backgroundColor: '#99ffff' }}></div>
                      <p className="text-xs">#99ffff<br/>Türkiz</p>
                    </div>
                    <div className="text-center">
                      <div className="h-12 rounded mb-1" style={{ backgroundColor: '#cccccc' }}></div>
                      <p className="text-xs">#cccccc<br/>Szürke</p>
                    </div>
                  </div>
                  <p className="text-xs text-muted-foreground mt-3">
                    Ezek a színek biztosítják a konzisztens vizuális megjelenést és az akadálymentességet.
                  </p>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </CardContent>
      </Card>

      {/* Best Practices */}
      <Card className="border-blue-200 bg-blue-50 dark:bg-blue-950">
        <CardHeader>
          <CardTitle>📚 Ajánlott Gyakorlatok</CardTitle>
        </CardHeader>
        <CardContent className="space-y-3 text-sm">
          <div>
            <h4 className="font-semibold mb-1">Minimum felmérési gyakoriság:</h4>
            <p className="text-muted-foreground">Évi 1 alkalom az éves trendek követéséhez, ideális: féléves vagy negyedéves</p>
          </div>
          <div>
            <h4 className="font-semibold mb-1">Optimális részvételi arány:</h4>
            <p className="text-muted-foreground">Minimum 30%, ideális 50%+ a reprezentatív eredményekhez</p>
          </div>
          <div>
            <h4 className="font-semibold mb-1">Benchmark használat:</h4>
            <p className="text-muted-foreground">Hasonlítsd az eredményeket az előző periódusokkal és az iparági átlagokkal</p>
          </div>
          <div>
            <h4 className="font-semibold mb-1">Cselekvési terv:</h4>
            <p className="text-muted-foreground">Minden felmérés után készíts konkrét fejlesztési tervet a gyenge pontokra</p>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default Methodology;
