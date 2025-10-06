import { useParams, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Calendar, Clock, User, Share2 } from 'lucide-react';
import logo from '@/assets/doyoueap-logo.png';
import { useAuth } from '@/hooks/useAuth';

// Import all article images
import mythsArticleImg from '@/assets/myths-article.jpg';
import scoreArticleImg from '@/assets/4score-article.jpg';
import futureArticleImg from '@/assets/future-article.jpg';
import climateArticleImg from '@/assets/climate-article.jpg';
import digitalWellbeingArticleImg from '@/assets/digital-wellbeing-article.jpg';
import globalArticleImg from '@/assets/global-article.jpg';
import leadershipArticleImg from '@/assets/leadership-article.jpg';
import stigmaArticleImg from '@/assets/stigma-article.jpg';
import engagementArticleImg from '@/assets/engagement-article.jpg';
import roiArticleImg from '@/assets/roi-article.jpg';

const MagazinArticle = () => {
  const { slug } = useParams();
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

  // Article database with full content
  const articles: Record<string, any> = {
    'mitoszok-es-tevhitek': {
      title: "Mítoszok és tévhitek: mi nem EAP – és mi az valójában",
      excerpt: "Az elmúlt években a vállalati jól-lét (wellbeing) fogalma világszerte központi témává vált. Tisztázzuk szakmai érvekkel: mi az EAP, mi az, ami hasznos kiegészítés lehet, és mikor kell különbséget tenni az eltérő megoldások között.",
      author: "doyoueap",
      date: "2025. január 15.",
      readTime: "8 perc",
      category: "Alapok",
      image: mythsArticleImg,
      content: `
        <p>Az elmúlt években a vállalati jól-lét (wellbeing) fogalma világszerte központi témává vált. A munkahelyi stressz, a mentális egészség támogatása és a work-life balance mind gyakori témák, és számos megoldás jelent meg a piacon, amelyek különböző megközelítéseket kínálnak.</p>
        
        <p>Ez önmagában pozitív fejlemény – ám egyre gyakoribb, hogy az Employee Assistance Program (EAP) kifejezést olyan szolgáltatásokra is használják, amelyek valójában nem EAP-ok. Ez a fogalmi zűrzavar félrevezető lehet a HR-vezetők, döntéshozók és munkavállalók számára egyaránt.</p>

        <h2>Mi az EAP valójában?</h2>
        
        <p>Az EAP egy strukturált, szakmailag irányított támogatási program, amely:</p>
        
        <ul>
          <li><strong>Bizalmas, rövid távú tanácsadást</strong> nyújt a munkavállalóknak és családtagjaiknak személyes vagy munkahelyi problémákkal kapcsolatban.</li>
          <li><strong>Szakképzett, engedéllyel rendelkező tanácsadók</strong> (pszichológusok, szociális munkások, terapeuták) végzik, nem pedig AI chatbotok, wellness coachok vagy önfejlesztő applikációk.</li>
          <li><strong>Célzott, rövid intervenciót</strong> kínál (általában 3-8 ülés), amely krízishelyzetekben, stresszel, szorongással, addikciókkal, családi konfliktusokkal vagy más komplex problémákkal foglalkozik.</li>
          <li><strong>Teljesen ingyenes a munkavállaló számára</strong>, a munkáltató fizeti.</li>
          <li><strong>Teljes körű titoktartás mellett</strong> működik – a munkáltató nem kap egyéni felhasználói adatokat, csak aggregált statisztikákat.</li>
        </ul>

        <h2>Mi NEM EAP?</h2>

        <h3>1. Wellness appok és platformok</h3>
        <p>Bár a meditációs appok, alvásjavító programok vagy fitnesz-trackerek hasznosak lehetnek, ezek nem EAP-ok. Az EAP valós, emberek közötti interakciót jelent, ahol szakképzett tanácsadók segítenek komoly problémák feldolgozásában.</p>

        <h3>2. AI-alapú chatbotok</h3>
        <p>Egyes platformok mesterséges intelligenciával működő chatbotokat kínálnak, amelyek „támogató beszélgetéseket" folytatnak. Ez hasznos lehet önismereti célokra, de nem helyettesíti a licenccel rendelkező szakember által nyújtott EAP-szolgáltatást.</p>

        <h3>3. Online önsegítő tartalmak</h3>
        <p>A videók, podcastek, cikkek és önfejlesztő kurzusok értékes kiegészítők lehetnek, de önmagukban nem jelentenek EAP-ot. Az EAP személyre szabott, interaktív támogatást jelent.</p>

        <h2>Miért fontos a különbségtétel?</h2>

        <p>Amikor egy szervezet bevezet egy „wellbeing programot", és EAP-ként kommunikálja, miközben valójában csak egy applikációs csomag vagy önfejlesztő platform, akkor:</p>

        <ul>
          <li>A munkavállalók nem kapják meg azt a szintű támogatást, amire szükségük lenne egy valódi krízishelyzetben.</li>
          <li>A HR-vezetők félreértik a programjuk hatékonyságát és ROI-ját.</li>
          <li>A valódi EAP-szolgáltatók hírneve sérül, mert összekeverik őket alacsonyabb színvonalú megoldásokkal.</li>
        </ul>

        <h2>Összegzés</h2>

        <p>Az EAP egy bizonyított hatékonyságú, szakmailag irányított támogatási program, amely valódi szakembereket és bizalmas tanácsadást foglal magában. A wellbeing appok, AI chatbotok és önfejlesztő platformok hasznos kiegészítők lehetnek, de nem helyettesítik az EAP-ot.</p>

        <p>Ha egy szervezet valóban törődik a munkavállalói mentális egészségével, akkor fontos, hogy különbséget tegyen ezek között – és ne használja az EAP kifejezést olyan szolgáltatásokra, amelyek nem felelnek meg a szakmai standardoknak.</p>
      `
    },
    '4score-mutatok': {
      title: "A 4Score mutatók ereje: hogyan mérhető az EAP valódi értéke",
      excerpt: "Egy Employee Assistance Program (EAP) sikerességét ma már nem elég érzésre vagy eseti visszajelzésekre alapozni. A szervezetek elvárják, hogy a támogatási programok hatása mérhető, összehasonlítható és kimutatható legyen — emberi és üzleti szinten egyaránt.",
      author: "doyoueap",
      date: "2025. január 12.",
      readTime: "7 perc",
      category: "Mérés",
      image: scoreArticleImg,
      content: `
        <p>Egy Employee Assistance Program (EAP) sikerességét ma már nem elég érzésre vagy eseti visszajelzésekre alapozni. A szervezetek elvárják, hogy a támogatási programok hatása mérhető, összehasonlítható és kimutatható legyen — emberi és üzleti szinten egyaránt.</p>

        <p>A 4Score modell éppen ezt a célt szolgálja: átlátható, strukturált keretrendszert nyújt az EAP valódi értékének mérésére.</p>

        <h2>Mi a 4Score modell?</h2>

        <p>A 4Score négy kulcsmutatón keresztül ad átfogó képet az EAP teljesítményéről:</p>

        <ol>
          <li><strong>Tudatosság (Awareness)</strong> – Mennyire ismerik a munkavállalók az EAP-ot?</li>
          <li><strong>Használat (Usage)</strong> – Hányan veszik ténylegesen igénybe a szolgáltatást?</li>
          <li><strong>Bizalom & Hajlandóság (Trust & Willingness)</strong> – Mennyire bíznak a programban, és mennyire hajlandóak élni vele?</li>
          <li><strong>Hatás (Impact)</strong> – Milyen konkrét eredményeket hoz az EAP egyéni és szervezeti szinten?</li>
        </ol>

        <h2>1. Tudatosság (Awareness Score)</h2>

        <p>A tudatosság az első lépés. Ha a munkavállalók nem tudnak az EAP létezéséről, vagy nem értik, mire használhatják, akkor a program hatása nulla lesz – függetlenül attól, hogy milyen jó minőségű a szolgáltatás.</p>

        <h3>Mit mérünk?</h3>
        <ul>
          <li>Hány százalék tudja, hogy létezik EAP a cégnél?</li>
          <li>Tisztában vannak-e azzal, hogyan lehet igénybe venni?</li>
          <li>Értik-e, hogy milyen problémákkal fordulhatnak az EAP-hoz?</li>
        </ul>

        <h2>2. Használat (Usage Score)</h2>

        <p>A használati arány azt mutatja, hogy mennyien élnek ténylegesen a program lehetőségeivel. Az átlagos EAP-használat a legtöbb országban 3-8% között mozog, de a jól kommunikált, támogatott programoknál ez akár 15-20% is lehet.</p>

        <h3>Mit mérünk?</h3>
        <ul>
          <li>Hány munkavállaló vett igénybe EAP-szolgáltatást az elmúlt évben?</li>
          <li>Mennyien térnek vissza további ülésekre?</li>
          <li>Melyek a leggyakoribb igénybevételi okok?</li>
        </ul>

        <h2>3. Bizalom & Hajlandóság (Trust & Willingness Score)</h2>

        <p>A program sikerességének kulcsa, hogy a munkavállalók bíznak-e a titoktartásban, és hajlandóak-e segítséget kérni, amikor szükségük van rá.</p>

        <h3>Mit mérünk?</h3>
        <ul>
          <li>Mennyire bíznak a munkavállalók abban, hogy az EAP valóban bizalmas?</li>
          <li>Mennyire érzik úgy, hogy nem lesz negatív következménye, ha igénybe veszik?</li>
          <li>Ajánlanák-e az EAP-ot egy kollégának?</li>
        </ul>

        <h2>4. Hatás (Impact Score)</h2>

        <p>Végül az a legfontosabb kérdés: milyen konkrét eredményeket hoz az EAP? Ez mérhető egyéni szinten (javuló mentális egészség, csökkenő stressz) és szervezeti szinten (produktivitás, munkahelyi jelenléti arány, fluktuáció csökkenése).</p>

        <h3>Mit mérünk?</h3>
        <ul>
          <li>Az igénybevevők elégedettségi szintje</li>
          <li>Változás a mentális egészség mutatókban</li>
          <li>Produktivitás és munkavégzés javulása</li>
          <li>ROI: megtérülés pénzügyi szempontból</li>
        </ul>

        <h2>Miért hatékony a 4Score modell?</h2>

        <p>A 4Score modell azért működik, mert:</p>

        <ul>
          <li><strong>Holisztikus</strong> – Nem csak egy mutatót néz (pl. használat), hanem az egész folyamatot.</li>
          <li><strong>Strukturált</strong> – Egyértelmű keretrendszert ad a méréshez.</li>
          <li><strong>Összehasonlítható</strong> – Lehetővé teszi a benchmarkingot más szervezetekkel vagy időszakokkal.</li>
          <li><strong>Cselekvésorientált</strong> – Megmutatja, hol van szükség fejlesztésre.</li>
        </ul>

        <h2>Összegzés</h2>

        <p>A 4Score modell segít abban, hogy az EAP ne csak egy költség legyen a HR-költségvetésben, hanem bizonyítható értéket teremtsen a szervezet és a munkavállalók számára egyaránt.</p>
      `
    },
    'eap-jovoje': {
      title: "Az EAP jövője és a digitalizáció szerepe",
      excerpt: "A jövő EAP-ja nem egyszerűen a múlt modelljeinek digitális másolata, hanem egy új, hibrid rendszer, amely ötvözi a technológia előnyeit a személyes kapcsolattartás értékeivel.",
      author: "doyoueap",
      date: "2025. január 10.",
      readTime: "6 perc",
      category: "Jövő",
      image: futureArticleImg,
      content: `
        <p>Az Employee Assistance Programok (EAP) világában a digitalizáció nem egy opcionális kiegészítés többé – ez az iparág jövője. De vajon mit jelent ez a gyakorlatban? És hogyan változtatja meg a digitalizáció az EAP szolgáltatások természetét?</p>

        <h2>A hagyományos EAP korlátai</h2>

        <p>A klasszikus EAP-modellek általában telefonos tanácsadásra és személyes találkozókra épültek. Bár ezek a módszerek továbbra is fontosak, számos kihívással szembesülnek:</p>

        <ul>
          <li>Időbeli korlátok – A munkavállalóknak nehéz időt találni személyes találkozókra.</li>
          <li>Földrajzi akadályok – Nem mindenki él közel a tanácsadói irodákhoz.</li>
          <li>Stigma és anonimitás – Sokan nem mernek telefonálni vagy személyesen megjelenni.</li>
          <li>Generációs elvárások – A fiatalabb generációk digitális megoldásokat várnak.</li>
        </ul>

        <h2>A digitális EAP előnyei</h2>

        <h3>1. Azonnali hozzáférhetőség</h3>
        <p>A digitális platformok 24/7 elérhetőséget biztosítanak, így a munkavállalók akkor kaphatnak segítséget, amikor szükségük van rá – akár éjszaka, akár hétvégén.</p>

        <h3>2. Nagyobb anonimitás</h3>
        <p>A chat-alapú vagy video-tanácsadás lehetőséget ad arra, hogy az emberek saját otthonukból, biztonságos környezetből kérjenek segítséget, ami csökkenti a stigmát.</p>

        <h3>3. Adatvezérelt betekintések</h3>
        <p>A digitális platformok lehetővé teszik az aggregált adatok gyűjtését, ami segít a HR-nek jobban megérteni a munkavállalói igényeket és trendeket.</p>

        <h3>4. Költséghatékonyság</h3>
        <p>A digitális EAP skálázhatóbb, és gyakran költséghatékonyabb megoldást kínál, különösen nagyvállalatok esetében.</p>

        <h2>A hibrid modell: a jövő útja</h2>

        <p>A jövő EAP-ja nem egyszerűen a múlt modelljeinek digitális másolata, hanem egy új, hibrid rendszer, amely ötvözi a technológia előnyeit a személyes kapcsolattartás értékeivel:</p>

        <ul>
          <li><strong>Digitális első lépés</strong> – Chat, app, vagy önértékelő eszközök az első segítségkéréshez.</li>
          <li><strong>Emberi támogatás</strong> – Személyes (online vagy offline) tanácsadás, amikor szükséges.</li>
          <li><strong>AI-támogatás</strong> – Intelligens triázs, amely meghatározza, melyik problémához milyen szintű támogatás kell.</li>
          <li><strong>Folyamatos követés</strong> – Digitális eszközök segítségével nyomon követhető a haladás és a hosszú távú eredmények.</li>
        </ul>

        <h2>Az AI szerepe az EAP-ban</h2>

        <p>Az AI nem helyettesíti az emberi tanácsadókat, de jelentősen támogathatja őket:</p>

        <ul>
          <li><strong>Triázs és útválasztás</strong> – Az AI segíthet meghatározni, hogy egy munkavállaló milyen típusú támogatásra van szüksége.</li>
          <li><strong>Chatbotok</strong> – Azonnali válaszokat adhatnak gyakori kérdésekre.</li>
          <li><strong>Prediktív elemzés</strong> – Segíthet azonosítani a veszélyeztetett csoportokat.</li>
          <li><strong>Personalizáció</strong> – Testre szabott tartalmakat és javaslatokat nyújthat.</li>
        </ul>

        <h2>Kihívások és etikai kérdések</h2>

        <p>A digitalizáció nem problémamentes:</p>

        <ul>
          <li><strong>Adatvédelem</strong> – Az érzékeny egészségügyi adatok védelme kulcsfontosságú.</li>
          <li><strong>Digitális szakadék</strong> – Nem mindenki rendelkezik megfelelő eszközökkel vagy digitális tudással.</li>
          <li><strong>Emberi kapcsolat hiánya</strong> – Az AI és a digitális eszközök nem helyettesíthetik a valódi emberi empatát.</li>
        </ul>

        <h2>Összegzés</h2>

        <p>A digitalizáció nem fenyegetés az EAP-ra, hanem lehetőség a szolgáltatások javítására, bővítésére és hozzáférhetőbbé tételére. A jövő EAP-ja hibrid lesz: ötvözi a technológia hatékonyságát az emberi kapcsolat erejével, hogy minden munkavállaló megkapja a szükséges támogatást, amikor és ahogyan szüksége van rá.</p>
      `
    },
    'szervezeti-klima-szerepe': {
      title: "A szervezeti klíma szerepe az EAP hatékonyságában",
      excerpt: "Egy EAP soha nem a vákuumban működik. A program hatását mindig befolyásolja a szervezet kultúrája, a vezetők attitűdje és a munkahelyi környezet.",
      author: "doyoueap",
      date: "2025. január 8.",
      readTime: "6 perc",
      category: "Kultúra",
      image: climateArticleImg,
      content: `
        <p>Egy Employee Assistance Program (EAP) soha nem vákuumban működik. Bármennyire is kiváló minőségű szolgáltatást kínál, a program hatását mindig befolyásolja a szervezet kultúrája, a vezetők attitűdje, és a munkahelyi környezet általános hangulata.</p>

        <h2>Mi a szervezeti klíma?</h2>
        
        <p>A szervezeti klíma az a „légkör", amely egy munkahely mindennapjait meghatározza:</p>
        <ul>
          <li>Hogyan kommunikálnak egymással a vezetők és a munkavállalók?</li>
          <li>Milyen értékek irányítják a döntéshozatalt?</li>
          <li>Mennyire nyitottak a mentális egészségről szóló beszélgetésekre?</li>
          <li>Van-e bizalom a szervezetben?</li>
        </ul>

        <h2>A nyílt kultúra fontossága</h2>
        
        <p>Azokban a szervezetekben, ahol a mentális egészség és a jól-lét nem tabutéma, hanem természetes része a mindennapoknak, az EAP-használat jelentősen magasabb. A munkavállalók nem félnek attól, hogy megbélyegzik őket, ha segítséget kérnek.</p>

        <h2>A vezetők szerepe</h2>
        
        <p>A szervezeti klíma formálásában a vezetőknek kulcsszerepük van:</p>
        <ul>
          <li>Ha a vezetők nyíltan beszélnek a mentális egészségről, az legitimizálja a témát</li>
          <li>Ha maguk is igénybe veszik az EAP-ot (vagy nyíltan támogatják azt), az példát mutat</li>
          <li>Ha empátiával fordulnak a munkavállalók felé, az bizalmat épít</li>
        </ul>

        <h2>Mérési lehetőségek</h2>
        
        <p>A szervezeti klíma hatását az EAP-ra különböző módokon lehet mérni:</p>
        <ul>
          <li>Munkavállalói elégedettség felmérések</li>
          <li>EAP tudatossági és használati adatok</li>
          <li>Bizalmi index (Trust & Willingness Score)</li>
          <li>Kilépési interjúk elemzése</li>
        </ul>

        <h2>Összegzés</h2>
        
        <p>Az EAP sikeressége nem csak a szolgáltatás minőségén múlik. A szervezeti klíma az, ami meghatározza, hogy a munkavállalók mernek-e élni a lehetőséggel, és érzik-e úgy, hogy a munkahelyük támogatja őket.</p>
      `
    },
    'digitalis-wellbeing-platformok': {
      title: "Digitális wellbeing platformok és az EAP – kiegészítés vagy verseny?",
      excerpt: "Az elmúlt években gombamód szaporodtak a digitális wellbeing platformok. Nem arról van szó, hogy az egyik jobb, a másik rosszabb – inkább arról, hogy más funkciót töltenek be.",
      author: "doyoueap",
      date: "2025. január 6.",
      readTime: "7 perc",
      category: "Technológia",
      image: digitalWellbeingArticleImg,
      content: `
        <p>Az elmúlt években gombamód szaporodtak a digitális wellbeing platformok: meditációs appok, fitness trackerek, alvásjavító programok, stresszkezelő alkalmazások és még sok más. Ezekkel párhuzamosan az Employee Assistance Programok (EAP) is jelen vannak a piacon.</p>

        <p>Felmerül a kérdés: versenyeznek egymással, vagy kiegészítik egymást?</p>

        <h2>Mi a különbség?</h2>
        
        <h3>Digitális wellbeing platformok</h3>
        <ul>
          <li>Önkiszolgáló eszközök</li>
          <li>Általános jól-lét témák (meditáció, alvás, fitnesz)</li>
          <li>Nincs személyre szabott szakmai támogatás</li>
          <li>Megelőzés és fenntartás</li>
        </ul>

        <h3>EAP</h3>
        <ul>
          <li>Szakemberek által nyújtott támogatás</li>
          <li>Komplex problémák kezelése (szorongás, addikció, kapcsolati problémák)</li>
          <li>Személyre szabott, konfidenciális tanácsadás</li>
          <li>Intervenció és kezelés</li>
        </ul>

        <h2>Kiegészítés, nem verseny</h2>
        
        <p>A két megoldás nem zárja ki egymást – épp ellenkezőleg, együtt a legerősebbek:</p>

        <ul>
          <li><strong>Prevenció + Intervenció</strong> – A wellbeing appok megelőzésre, az EAP kezelésre fókuszál</li>
          <li><strong>Önfejlesztés + Szakmai támogatás</strong> – Az appok önfejlesztésre, az EAP valódi problémák megoldására</li>
          <li><strong>Tömegeknek + Egyéneknek</strong> – Az appok széles körben használhatók, az EAP személyre szabott</li>
        </ul>

        <h2>A hibrid modell előnyei</h2>
        
        <p>Azok a szervezetek, amelyek kombinálják a két megközelítést, a legjobb eredményeket érik el:</p>
        
        <ul>
          <li>A munkavállalók különböző igényeit szolgálják ki</li>
          <li>Megelőzés és kezelés is jelen van</li>
          <li>Szélesebb körben elérhető támogatás</li>
          <li>Jobb ROI és munkavállalói elégedettség</li>
        </ul>

        <h2>Összegzés</h2>
        
        <p>A digitális wellbeing platformok és az EAP nem vetélytársak, hanem együtt alkotnak egy átfogó munkavállalói támogatási ökoszisztémát. A sikeres HR-stratégia mindkettőt integrálja.</p>
      `
    },
    'globalis-kitekintes': {
      title: "Globális kitekintés: EAP-használat régiók szerint",
      excerpt: "Az Employee Assistance Programok nemzetközi elterjedtsége és használata nagyon különböző képet mutat a világ régióiban. Nincs egyetlen univerzális modell – az EAP sikeressége mindig kontextusfüggő.",
      author: "doyoueap",
      date: "2025. január 5.",
      readTime: "8 perc",
      category: "Globális",
      image: globalArticleImg,
      content: `
        <p>Az Employee Assistance Programok (EAP) világszerte jelen vannak, de elterjedtségük, használatuk és a velük kapcsolatos attitűd régiónként jelentősen eltér.</p>

        <h2>Észak-Amerika: Az EAP otthona</h2>
        
        <p>Az EAP az Egyesült Államokban született meg az 1970-es években, és ma is itt a leginkább elterjedt:</p>
        <ul>
          <li>A nagyvállalatok 75%-a kínál EAP-ot</li>
          <li>Átlagos használati arány: 5-8%</li>
          <li>Erős jogi és kulturális támogatás</li>
        </ul>

        <h2>Európa: Vegyes kép</h2>
        
        <h3>Nyugat-Európa</h3>
        <p>Nagy-Britannia, Írország és Hollandia vezető szerepet tölt be. Az EAP itt már mainstream.</p>

        <h3>Közép- és Kelet-Európa</h3>
        <p>Magyarország, Lengyelország és Csehország lassan kezd felzárkózni, de még mindig alacsony a tudatosság és a használat.</p>

        <h2>Ázsia-Csendes-óceáni régió</h2>
        
        <p>Japánban és Dél-Koreában hagyományosan tabu a mentális egészség, de a fiatal generációk nyitottabbak. Ausztráliában és Új-Zélandon az EAP elterjedt.</p>

        <h2>Latin-Amerika és Afrika</h2>
        
        <p>Ezekben a régiókban az EAP még gyerekcipőben jár. A kulturális stigma és az erőforrások hiánya jelentős akadályok.</p>

        <h2>Összegzés</h2>
        
        <p>Az EAP globális elterjedése nem egyenletes. A kulturális különbségek, jogi környezet és gazdasági fejlettség mind befolyásolják a programok sikerességét.</p>
      `
    },
    'vezetok-szerepe': {
      title: "A vezetők szerepe és felelőssége az EAP sikerében",
      excerpt: "Egy Employee Assistance Program sikerének kulcsa nemcsak a szolgáltatások minőségén múlik, hanem azon is, hogy a vezetők hogyan viszonyulnak hozzá.",
      author: "doyoueap",
      date: "2025. január 3.",
      readTime: "6 perc",
      category: "Vezetés",
      image: leadershipArticleImg,
      content: `
        <p>Egy Employee Assistance Program (EAP) sikerének kulcsa nemcsak a szolgáltatások minőségén múlik, hanem azon is, hogy a vezetők hogyan viszonyulnak hozzá. A vezetői attitűd és a vezetői példamutatás döntő jelentőségű.</p>

        <h2>Miért fontos a vezetők szerepe?</h2>
        
        <p>A vezetők az alábbi okokból kritikus szereplők az EAP sikerében:</p>
        <ul>
          <li>Ők formálják a szervezeti kultúrát</li>
          <li>Ők mutatnak példát a munkavállalóknak</li>
          <li>Ők hozzák a stratégiai döntéseket az EAP támogatásáról</li>
          <li>Ők kommunikálják az EAP értékét</li>
        </ul>

        <h2>Mit tehetnek a vezetők?</h2>
        
        <h3>1. Nyílt kommunikáció</h3>
        <p>Beszéljenek nyíltan a mentális egészség fontosságáról, és támogassák az EAP használatát.</p>

        <h3>2. Példamutatás</h3>
        <p>Ha egy vezető maga is igénybe veszi az EAP-ot (vagy legalább támogatja azt), az legitimizálja a programot.</p>

        <h3>3. Erőforrások biztosítása</h3>
        <p>Az EAP csak akkor működhet hatékonyan, ha megfelelő költségvetést és figyelmet kap.</p>

        <h3>4. Aktív promóció</h3>
        <p>A vezetőknek rendszeresen emlékeztetniük kell a munkavállalókat az EAP létezésére és előnyeire.</p>

        <h2>Mit NE tegyenek a vezetők?</h2>
        
        <ul>
          <li><strong>Ne stigmatizálják</strong> – Soha ne tekintsék gyengeségnek a segítségkérést</li>
          <li><strong>Ne hagyjá figyelmen kívül</strong> – Az EAP nem lehet csak egy "kipipálandó" tétel</li>
          <li><strong>Ne kérdezzenek túl sokat</strong> – A bizalmasság kulcsfontosságú</li>
        </ul>

        <h2>Összegzés</h2>
        
        <p>A vezetők felelőssége nem ér véget az EAP bevezetésével. A folyamatos támogatás, kommunikáció és példamutatás nélkül még a legjobb program is kudarcra van ítélve.</p>
      `
    },
    'stigma-lebontasa': {
      title: "A stigma lebontása és a kommunikáció szerepe",
      excerpt: "Az EAP egyik legnagyobb akadálya világszerte a stigma. A stigma lebontásának első lépése a kommunikáció – folyamatosan, változatos formában kell találkozniuk a munkavállalóknak az üzenettel.",
      author: "doyoueap",
      date: "2025. január 2.",
      readTime: "7 perc",
      category: "Kommunikáció",
      image: stigmaArticleImg,
      content: `
        <p>Az Employee Assistance Programok (EAP) egyik legnagyobb akadálya világszerte a stigma. Hiába elérhető egy kiváló minőségű szolgáltatás, ha a munkavállalók félnek vagy szégyellik igénybe venni.</p>

        <h2>Mi a stigma?</h2>
        
        <p>A stigma a mentális egészséggel kapcsolatos negatív attitűdök és előítéletek összessége:</p>
        <ul>
          <li>"Ha EAP-ra van szükségem, az gyengeség"</li>
          <li>"Ha igénybe veszem, azt fogják gondolni, nem bírok a munkával"</li>
          <li>"A főnököm megítél, ha segítséget kérek"</li>
        </ul>

        <h2>A kommunikáció szerepe</h2>
        
        <p>A stigma lebontásának első és legfontosabb lépése a kommunikáció:</p>

        <h3>1. Folyamatos jelenlét</h3>
        <p>Nem elég egyszer éves eleje bejelenteni az EAP-ot. Folyamatosan, változatos formában kell találkozniuk a munkavállalóknak az üzenettel.</p>

        <h3>2. Változatos csatornák</h3>
        <ul>
          <li>Email kampányok</li>
          <li>Poszterek az irodában</li>
          <li>Belső hírlevelek</li>
          <li>Webinárok és workshopok</li>
          <li>Személyes vezetői kommunikáció</li>
        </ul>

        <h3>3. Pozitív üzenetek</h3>
        <p>A kommunikációnak pozitívnak és támogatónak kell lennie, nem pedig fenyegetőnek vagy megfélemlítőnek.</p>

        <h2>Sikeres példák</h2>
        
        <p>Azok a szervezetek, ahol sikeresen csökkentették a stigmát:</p>
        <ul>
          <li>Rendszeres "Mental Health Awareness" kampányokat szerveztek</li>
          <li>Vezetők személyes történeteket osztottak meg</li>
          <li>Az EAP-ot "természetesnek" kezelték, nem "válságkezelésnek"</li>
        </ul>

        <h2>Összegzés</h2>
        
        <p>A stigma lebontása hosszú folyamat, de elengedhetetlen az EAP sikeréhez. A kommunikáció az eszköz, amely megváltoztathatja a munkavállalók hozzáállását.</p>
      `
    },
    'munkavallaoi-elkotelezoedes': {
      title: "Az EAP és a munkavállalói elköteleződés kapcsolata",
      excerpt: "Az elkötelezett dolgozók produktívabbak, lojálisabbak, kevésbé hajlamosak a fluktuációra. Az EAP ugyan elsősorban támogató szolgáltatásként ismert, mégis közvetlenül és közvetve is erősíti az elköteleződést.",
      author: "doyoueap",
      date: "2024. december 30.",
      readTime: "6 perc",
      category: "HR",
      image: engagementArticleImg,
      content: `
        <p>A munkavállalói elköteleződés (employee engagement) az egyik legfontosabb HR-mutató. Az elkötelezett dolgozók produktívabbak, lojálisabbak, kevésbé hajlamosak a fluktuációra, és pozitívan hatnak a csapat hangulatára.</p>

        <p>De mi köze ehhez az EAP-nak?</p>

        <h2>Az EAP hatása az elköteleződésre</h2>
        
        <p>Az EAP többféleképpen is erősíti a munkavállalói elköteleződést:</p>

        <h3>1. "A cég törődik velem"</h3>
        <p>Amikor egy munkáltató EAP-ot kínál, az üzenet egyértelmű: "Fontos vagy nekünk, nemcsak munkavállalóként, hanem emberként is."</p>

        <h3>2. Stressz és kiégés csökkentése</h3>
        <p>Az EAP segít a munkavállalóknak kezelni a stresszt és megelőzni a kiégést, ami közvetlenül javítja a munkahelyi teljesítményt és elégedettséget.</p>

        <h3>3. Munka-magánélet egyensúly</h3>
        <p>Az EAP nem csak munkahelyi problémákban segít, hanem magánéleti kérdésekben is (családi konfliktusok, pénzügyi nehézségek stb.), ami javítja az általános jóllétet.</p>

        <h2>Mérési lehetőségek</h2>
        
        <p>Az EAP és az elköteleződés kapcsolatát különböző módokon lehet mérni:</p>
        <ul>
          <li>Munkavállalói elégedettségi felmérések</li>
          <li>Fluktuációs arány változása</li>
          <li>Produktivitási mutatók</li>
          <li>Betegszabadságok száma</li>
        </ul>

        <h2>Sikeres példák</h2>
        
        <p>Azok a vállalatok, amelyek komolyan veszik az EAP-ot és a munkavállalói jóllétet:</p>
        <ul>
          <li>Alacsonyabb fluktuációval rendelkeznek</li>
          <li>Magasabb munkavállalói elégedettséget érnek el</li>
          <li>Jobb employer branding-gel rendelkeznek</li>
        </ul>

        <h2>Összegzés</h2>
        
        <p>Az EAP nem csak egy "nice-to-have" benefit. Ez egy stratégiai eszköz, amely közvetlenül hozzájárul a munkavállalói elköteleződéshez és a szervezet hosszú távú sikeréhez.</p>
      `
    },
    'eap-merhetosege-roi': {
      title: "Az EAP mérhetősége és a megtérülés (ROI) kérdése",
      excerpt: "Számos kutatás bizonyítja, hogy az EAP nemcsak humánus és etikus megoldás, hanem gazdaságilag is kifizetődő. A programok átlagosan 3-10-szeres megtérülést hoznak.",
      author: "doyoueap",
      date: "2024. december 28.",
      readTime: "8 perc",
      category: "ROI",
      image: roiArticleImg,
      content: `
        <p>Az egyik leggyakoribb kérdés, amit a HR-vezetők feltehetnek az EAP-val kapcsolatban: "Mennyibe kerül, és megtérül-e?"</p>

        <p>A jó hír: számos kutatás bizonyítja, hogy az EAP nemcsak humánus és etikus megoldás, hanem gazdaságilag is kifizetődő.</p>

        <h2>Hogyan számoljuk az EAP ROI-ját?</h2>
        
        <p>Az EAP megtérülését (Return on Investment) a következő képlettel számíthatjuk:</p>
        
        <p><strong>ROI = (Haszon - Költség) / Költség × 100</strong></p>

        <h3>Költségek</h3>
        <ul>
          <li>Az EAP szolgáltatás díja (általában munkavállalónként éves szinten)</li>
          <li>Belső kommunikáció és promóció költségei</li>
          <li>Adminisztráció</li>
        </ul>

        <h3>Hasznok</h3>
        <ul>
          <li>Csökkent hiányzások</li>
          <li>Javult produktivitás</li>
          <li>Alacsonyabb fluktuáció</li>
          <li>Kevesebb baleset a munkahelyen</li>
          <li>Jobb munkavállalói elégedettség</li>
        </ul>

        <h2>Konkrét adatok</h2>
        
        <p>Nemzetközi tanulmányok szerint az EAP átlagosan:</p>
        <ul>
          <li><strong>3-10-szeres ROI</strong>-t hoz (azaz minden befektetett 1 dollár 3-10 dollárt térít meg)</li>
          <li><strong>25-30%-kal csökkenti</strong> a munkahelyi hiányzásokat</li>
          <li><strong>15-20%-kal javítja</strong> a produktivitást</li>
          <li><strong>50%-kal csökkenti</strong> a munkával kapcsolatos stresszt</li>
        </ul>

        <h2>Nehezen mérhető előnyök</h2>
        
        <p>Az EAP néhány előnye nehezen számszerűsíthető, de mégis jelentős:</p>
        <ul>
          <li>Jobb employer branding</li>
          <li>Pozitívabb szervezeti kultúra</li>
          <li>Magasabb munkavállalói lojalitás</li>
          <li>Etikus és humánus munkáltató imázs</li>
        </ul>

        <h2>Hogyan növeljük az ROI-t?</h2>
        
        <p>Az EAP ROI-ja jelentősen növelhető, ha:</p>
        <ul>
          <li>Folyamatosan kommunikálják a programot</li>
          <li>A vezetők aktívan támogatják</li>
          <li>Rendszeresen mérik a használatot és a hatást</li>
          <li>Személyre szabják a szolgáltatásokat</li>
        </ul>

        <h2>Összegzés</h2>
        
        <p>Az EAP nem költség, hanem befektetés. A jól működő programok nemcsak emberközpontúak, hanem gazdaságilag is ésszerűek.</p>
      `
    }
  };

  const article = slug ? articles[slug] : null;

  if (!article) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-4xl font-bold mb-4">Cikk nem található</h1>
          <Button onClick={() => navigate('/magazin')}>
            Vissza a magazinhoz
          </Button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center">
          <div className="flex items-center gap-8">
            <img 
              src={logo} 
              alt="EAP Pulse" 
              className="h-8 cursor-pointer" 
              onClick={() => navigate('/')}
            />
            <nav className="hidden md:flex gap-6 items-center">
              <button
                onClick={() => navigate('/magazin')}
                className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-sm"
              >
                The Journalist!
              </button>
              <button
                onClick={() => navigate('/bemutatkozas')}
                className="text-sm border border-transparent transition-colors px-3 py-2 rounded-sm hover:bg-muted"
              >
                Bemutatkozás
              </button>
              <button
                onClick={() => navigate('/arak')}
                className="text-sm border border-transparent transition-colors px-3 py-2 rounded-sm hover:bg-muted"
              >
                Árak és Csomagok
              </button>
              {user && role === 'hr' && (
                <button
                  onClick={() => navigate('/?section=eap-pulse&sub=create-audit')}
                  className="text-sm border border-transparent transition-colors px-3 py-2 rounded-md hover:bg-muted"
                >
                  Dashboard
                </button>
              )}
            </nav>
          </div>
          <div className="flex items-center gap-4">
            {!loading && (
              user ? (
                <Button onClick={signOut} variant="outline">
                  Kilépés
                </Button>
              ) : (
                <Button onClick={() => navigate('/auth')}>
                  Bejelentkezés
                </Button>
              )
            )}
          </div>
        </div>
      </header>

      {/* Article Content */}
      <article className="w-full">
        {/* Featured Image - Full Width */}
        {article.image && (
          <div className="w-full h-[60vh] overflow-hidden relative">
            <img 
              src={article.image} 
              alt={article.title}
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
            
            {/* Title Overlay on Image */}
            <div className="absolute bottom-0 left-0 right-0 p-8 md:p-16">
              <div className="max-w-5xl mx-auto">
                <Badge className="mb-4 bg-white text-black hover:bg-white">{article.category}</Badge>
                <h1 className="text-4xl md:text-6xl font-bold text-white mb-4 leading-tight drop-shadow-lg">
                  {article.title}
                </h1>
              </div>
            </div>
          </div>
        )}

        {/* Article Meta & Content */}
        <div className="max-w-5xl mx-auto px-4 md:px-8 py-12">
          {/* Back Button */}
          <Button 
            variant="ghost" 
            className="mb-8"
            onClick={() => navigate('/magazin')}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            Vissza a magazinhoz
          </Button>
          
          {/* Meta Info */}
          <div className="flex flex-wrap items-center gap-4 md:gap-6 text-sm text-muted-foreground pb-8 border-b mb-12">
            <div className="flex items-center gap-2">
              <User className="h-4 w-4" />
              <span className="font-medium">{article.author}</span>
            </div>
            <div className="flex items-center gap-2">
              <Calendar className="h-4 w-4" />
              <span>{article.date}</span>
            </div>
            <div className="flex items-center gap-2">
              <Clock className="h-4 w-4" />
              <span>{article.readTime}</span>
            </div>
            <Button variant="ghost" size="sm" className="ml-auto">
              <Share2 className="mr-2 h-4 w-4" />
              Megosztás
            </Button>
          </div>

          {/* Excerpt */}
          <p className="text-xl md:text-2xl leading-relaxed text-muted-foreground mb-12 font-light">
            {article.excerpt}
          </p>

          {/* Article Body with wider layout */}
          <div 
            className="prose prose-lg md:prose-xl max-w-none 
                       prose-headings:font-bold 
                       prose-h2:text-3xl md:prose-h2:text-4xl prose-h2:mt-16 prose-h2:mb-6 
                       prose-h3:text-2xl md:prose-h3:text-3xl prose-h3:mt-12 prose-h3:mb-4 
                       prose-p:mb-6 prose-p:leading-relaxed prose-p:text-lg
                       prose-ul:my-8 prose-ul:space-y-3 prose-li:text-lg
                       prose-strong:font-semibold prose-strong:text-foreground"
            dangerouslySetInnerHTML={{ __html: article.content }}
          />

          {/* Back to Magazine */}
          <div className="mt-20 pt-12 border-t text-center">
            <h3 className="text-2xl font-bold mb-6">Fedezz fel még több cikket</h3>
            <Button 
              variant="default" 
              size="lg"
              onClick={() => navigate('/magazin')}
              className="bg-[hsl(var(--magazine-red))] hover:bg-[hsl(var(--magazine-red))]/90"
            >
              Vissza a magazinhoz
            </Button>
          </div>
        </div>
      </article>
    </div>
  );
};

export default MagazinArticle;
