import { useParams, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Calendar, Clock, User, Share2 } from 'lucide-react';
import logo from '@/assets/doyoueap-logo.png';
import journalistLogo from '@/assets/thejournalist_logo.png';
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
        <p>Az elmúlt években a vállalati jól-lét (wellbeing) fogalma világszerte központi témává vált. A cégek mindennapi működésében egyre hangsúlyosabb, hogy a munkavállalók fizikai és mentális egészségét támogassák – hiszen ennek hiánya nemcsak egyéni szinten okozhat problémát, hanem a teljesítményt, a fluktuációt és a szervezeti kultúrát is jelentősen befolyásolja. Ezzel párhuzamosan megjelentek és rohamosan terjednek a digitális wellbeing platformok, appok és tréningcsomagok. Ezek sok értékes funkciót kínálnak, de a gyakorlati tapasztalat azt mutatja, hogy sok helyen összemosódik a wellbeing és az Employee Assistance Program (EAP) fogalma. Ez a zavar félreértésekhez vezet, és végső soron gátolhatja a munkavállalókhoz valódi támogatás eljutását.</p>

        <p>A célunk ebben a cikkben nem az, hogy éles határvonalat húzzunk és „kizárjunk" más megközelítéseket, hanem az, hogy diplomatikusan, szakmai érvekkel tisztázzuk: mi az EAP, mi az, ami hasznos kiegészítés lehet, és mikor kell különbséget tenni az eltérő megoldások között.</p>

        <p>Az EAP gyökerei az 1940-es évek Amerikájába nyúlnak vissza, amikor vállalatok a dolgozóik alkoholproblémáira kerestek professzionális megoldást. A kezdeti fókusz tehát nagyon szűk volt, de a koncepció gyorsan fejlődött. Ma már az EAP magában foglalja a pszichológiai támogatást, a kríziskezelést, a jogi és pénzügyi tanácsadást, a családi és életvezetési kérdésekben való segítséget.</p>

        <p>Az EAP három alapvető pillére a professzionalizmus, a bizalmasság és anonimitás, valamint a komplexitás. Ezek együtt biztosítják, hogy a program ne csak hasznos kiegészítő legyen, hanem valódi, strukturált és hiteles támogatási forma. A wellbeing platformok ezzel szemben másra valók: a mindennapi egészségmegőrzést és prevenciót szolgálják, de nem helyettesítik a pszichológiai vagy jogi tanácsadást, és nem tudják kezelni a krízishelyzeteket.</p>

        <p>Fontos kiemelni, hogy az EAP és a wellbeing nem versenytársak, hanem egymást kiegészítő eszközök. A tiszta kommunikáció kulcskérdés: ha a munkavállaló nem tudja, pontosan mihez fér hozzá, könnyen előfordulhat, hogy nem a megfelelő segítséget veszi igénybe. Ez elmaradt támogatáshoz, bizalomvesztéshez és piaci zavarhoz vezethet. A HR feladata, hogy világosan elválassza a két fogalmat: mit ad a wellbeing, és mit nyújt az EAP. Így a dolgozó mindig tudja, hová fordulhat.</p>

        <p>Az Employee Assistance Program tehát nem csupán egy wellbeing eszköz. Ez egy professzionális, bizalmas, széles körű támogatást nyújtó szolgáltatás, amely különösen értékes akkor, amikor a munkavállaló valódi krízissel, nehéz döntésekkel vagy komplex problémákkal néz szembe. A cél nem a versengés, hanem az együttműködés: az EAP és a wellbeing eszközök közösen erősítik a vállalati kultúrát, a dolgozói biztonságot és elégedettséget.</p>
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
        <p>Egy Employee Assistance Program (EAP) sikerességét ma már nem elég érzésre vagy eseti visszajelzésekre alapozni. A szervezetek elvárják, hogy a támogatási programok hatása mérhető, összehasonlítható és kimutatható legyen — emberi és üzleti szinten egyaránt. Ennek a szemléletnek a megtestesítője a 4Score modell, amely négy kulcsmutatón keresztül ad átfogó képet arról, mennyire hatékonyan működik egy EAP program: Tudatosság, Használat, Bizalom & Hajlandóság, valamint Hatás.</p>

        <h2>1. Tudatosság – az első lépés a siker felé</h2>
        <p>A legjobb EAP is láthatatlan marad, ha a munkavállalók nem tudnak róla. A tudatosság azt méri, hányan ismerik a program létezését, tudják, hogyan érhetik el, és milyen szolgáltatásokat kínál. Gyakori tapasztalat, hogy még jól működő programok esetében is a dolgozók fele nincs tisztában a részletekkel. A tudatosság növelése nem egyszeri kampány, hanem folyamatos kommunikációs feladat: hírlevelek, vezetői megszólalások, belső események és vizuális emlékeztetők együtt alakítják ki azt a szervezeti kultúrát, ahol az EAP természetes része a mindennapoknak.</p>

        <h2>2. Használat – a valós aktivitás mutatója</h2>
        <p>A használat azt vizsgálja, milyen arányban veszik igénybe a munkavállalók a programot, és milyen gyakorisággal. Ez nem csupán számokról szól, hanem arról is, hogy a szolgáltatások ténylegesen elérik-e azokat, akiknek szükségük van rájuk. Fontos hangsúlyozni: a magas használati arány nem mindig önmagában jó vagy rossz. Egy szervezetben a növekvő igénybevétel jelezheti a növekvő bizalmat és tudatosságot — ugyanakkor rávilágíthat bizonyos belső problémákra is, amelyeket érdemes kezelni. A cél tehát nem a statisztikai verseny, hanem az egyensúly: a program legyen könnyen elérhető, de ne legyen tabu a használata sem.</p>

        <h2>3. Bizalom & Hajlandóság – a rejtett kulcstényező</h2>
        <p>A bizalom az EAP szíve. A munkavállalók csak akkor fordulnak segítségért, ha hisznek a program hitelességében, bizalmasságában és értékében. A 4Score modell harmadik eleme éppen ezt a bizalmi viszonyt méri, valamint a hajlandóságot, hogy valaki igénybe vegye a szolgáltatást, ha szüksége lenne rá. Sokszor a tudás már megvan, de a félelem a megítéléstől vagy az anonimitás hiányától visszatartja a dolgozókat. A bizalom építése hosszú távú munka, amelyben a vezetői kommunikáció és a szervezeti kultúra egyaránt kulcsszerepet játszik.</p>

        <h2>4. Hatás – az EAP valódi értékmérője</h2>
        <p>A modell utolsó eleme azt vizsgálja, milyen tényleges hatást gyakorol a program a munkavállalók jólétére, mentális egészségére és a szervezet működésére. Mérhető például a hiányzások csökkenése, a fluktuáció mérséklődése, vagy a dolgozói elégedettség és elköteleződés növekedése. De ide tartoznak a kvalitatív eredmények is: a jobb csapatdinamika, a pszichológiai biztonság erősödése vagy a vezetők megnövekedett érzékenysége a mentális egészség iránt.</p>

        <h2>A 4Score: új standard az EAP értékelésében</h2>
        <p>A 4Score modell nemcsak egy mérési eszköz, hanem egy szemlélet. Arra ösztönzi a szervezeteket, hogy ne csupán az igénybevételt, hanem a teljes folyamatot és annak hatását is figyeljék. Minden mutató egy tükör: a Tudatosság kommunikációról, a Használat hozzáférhetőségről, a Bizalom kultúráról, a Hatás pedig eredményességről szól.</p>

        <p>Ha mind a négy dimenziót mérjük, nemcsak adatokat kapunk — hanem egy pontos, átfogó képet arról, mennyire élő, hatékony és hiteles az EAP a szervezeten belül.</p>
      `
    },
    'eap-jovoje': {
      title: "Az EAP jövője és a digitalizáció szerepe",
      excerpt: "Az Employee Assistance Programok hagyományosan személyes találkozásokon, telefonos tanácsadáson alapultak. A jövő EAP-ja egy új, hibrid rendszer lesz, amely ötvözi a technológia előnyeit a személyes kapcsolattartás értékeivel.",
      author: "doyoueap",
      date: "2025. január 10.",
      readTime: "6 perc",
      category: "Jövő",
      image: futureArticleImg,
      content: `
        <p>Az Employee Assistance Programok (EAP) hagyományosan személyes találkozásokon, telefonos tanácsadáson és klasszikus szakértői hálózatokon alapultak. Az utóbbi években azonban a digitalizáció alapvetően formálta át a működésüket. A jövő EAP-ja nem egyszerűen a múlt modelljeinek digitális másolata lesz, hanem egy új, hibrid rendszer, amely ötvözi a technológia előnyeit a személyes kapcsolattartás értékeivel.</p>

        <h2>Az elérhetőség forradalma</h2>
        <p>A digitalizáció elsődleges hozadéka az elérhetőség. Az online platformok és applikációk révén a munkavállalók bárhonnan és bármikor kapcsolatba léphetnek szakértőkkel. Ez különösen fontos a nemzetközi cégek esetében, ahol a különböző időzónák és nyelvek miatt a klasszikus modellek nehezen működtethetők. A 24/7 elérhetőség digitális csatornákon keresztül alapelvárássá válik.</p>

        <h2>Mesterséges intelligencia és adatvezérelt megoldások</h2>
        <p>A mesterséges intelligencia és az adatvezérelt megoldások új dimenziókat nyitnak. Az AI képes előszűrni a problémákat, javaslatokat adni a megfelelő szakemberhez való irányításra, sőt, bizonyos alapszintű támogatást (például stresszkezelési tippeket vagy pénzügyi tanácsokat) azonnal nyújtani. Ez tehermentesíti a szakembereket, és gyors segítséget ad a munkavállalóknak. Ugyanakkor fontos hangsúlyozni, hogy az AI nem helyettesíti, hanem kiegészíti az emberi támogatást.</p>

        <h2>Mérhetőség és adatbiztonság</h2>
        <p>A digitalizáció lehetőséget ad a mérhetőség javítására is. Az online rendszerek könnyebben gyűjtenek adatokat a használatról, a gyakori problémákról és a felhasználói elégedettségről. Ezek az információk értékesek a HR és a menedzsment számára, mert pontosabb képet adnak a dolgozók szükségleteiről, és segítik a program folyamatos fejlesztését.</p>

        <p>Az adatbiztonság és a bizalmasság azonban kulcskérdés marad. Ahhoz, hogy a digitalizáció valódi előnyt hozzon, garantálni kell a legmagasabb szintű adatvédelmet. Ha a munkavállalók nem bíznak abban, hogy a személyes információik biztonságban vannak, a digitális megoldások nem fogják elérni céljukat.</p>

        <h2>A hibrid modell</h2>
        <p>A jövő EAP-ja várhatóan hibrid modell lesz. A munkavállalók választhatnak majd a digitális és a személyes csatornák között, attól függően, hogy milyen jellegű problémával szembesülnek. A mindennapi kisebb kérdésekre gyors, digitális támogatást kapnak, míg a komplexebb, mélyebb problémáknál továbbra is a személyes tanácsadás és a szakemberrel való kapcsolat lesz a kulcs.</p>

        <p>Összességében a digitalizáció nem gyengíti, hanem erősíti az EAP értékét. A technológia révén a program rugalmasabb, elérhetőbb és mérhetőbb lesz, miközben megőrzi alapvető értékeit: a professzionális szakértelmet, a bizalmasságot és az emberközpontú támogatást. Azok a szervezetek, amelyek képesek integrálni a digitális megoldásokat az EAP rendszerébe, hosszú távon versenyelőnyre tesznek szert.</p>
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
        <p>Egy EAP soha nem a vákuumban működik. A program hatását mindig befolyásolja a szervezet kultúrája, a vezetők attitűdje és a munkahelyi környezet. Hiába elérhető egy kiváló szolgáltatás, ha a dolgozók azt érzik, hogy a cégnél valójában nem támogatják a mentális egészségüket, vagy félnek attól, hogy a segítségkérés következményekkel járhat. Ilyen közegben az EAP kihasználatlan marad.</p>

        <p>A kutatások szerint azoknál a cégeknél, ahol a vezetők nyíltan beszélnek a jóllétről, ahol biztonságos a pszichológiai légkör és ahol a dolgozók érzik, hogy valóban fontos a jól-létük, sokkal magasabb az EAP-használat. A pozitív szervezeti klíma katalizátorként működik: erősíti a tudatosságot, lebontja a stigmát, és növeli a dolgozók bizalmát. Ezzel szemben egy toxikus, túlterhelt környezetben a program csak papíron létezik, de valójában nem tölti be a funkcióját.</p>

        <p>A szervezeti klíma tehát közvetlenül befolyásolja a 4Score mutatókat is. Ha a vezetők példát mutatnak, az növeli a tudatosságot. Ha támogatják a hozzáférést, az javítja a használatot. Ha nyíltan beszélnek arról, hogy ők maguk is használnák a szolgáltatást, az aktiváció is megugrik. És ha a dolgozók érzik, hogy nem lesz negatív következménye a részvételnek, a bizalom is erősödik.</p>

        <p>Az EAP tehát nem csodaszer, hanem egy erős eszköz, amely a megfelelő környezetben bontakozik ki igazán. A HR és a menedzsment felelőssége, hogy ezt a környezetet megteremtsék: a pszichológiai biztonság, a nyílt kommunikáció és az őszinte elköteleződés együtt adják azt a talajt, amelyben az EAP valódi értéket tud teremteni.</p>
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
        <p>Az elmúlt években gombamód szaporodtak a digitális wellbeing platformok. Alvásfigyelő alkalmazások, meditációs és mindfulness tréningek, online fitneszprogramok, táplálkozási tanácsadást kínáló appok váltak mindennapossá a vállalatok körében. Ezek a megoldások sok szempontból hasznosak: könnyen hozzáférhetők, széles körben skálázhatók, és a prevencióban, valamint az egészségmegőrzésben fontos szerepet játszanak. Ugyanakkor egyre gyakrabban találkozunk azzal a jelenséggel, hogy ezeket a szolgáltatásokat azonosítják vagy egyenértékűnek tekintik az Employee Assistance Programmal.</p>

        <p>Ez az összemosás több okból is problémás. Az EAP ugyanis professzionális, több szakterületet lefedő, bizalmas támogatást nyújt, amely pszichológiai tanácsadást, kríziskezelést, jogi és pénzügyi tanácsadást is tartalmazhat. A digitális wellbeing platformok ezzel szemben általában önsegítő eszközöket kínálnak, amelyek előnyösek lehetnek a mindennapokban, de krízishelyzetben nem jelentenek megoldást.</p>

        <p>Nem arról van szó, hogy az egyik jobb, a másik rosszabb. Inkább arról, hogy más funkciót töltenek be. A wellbeing platformok a dolgozók általános jóllétét segítik, megelőző és egészségfenntartó eszközök. Az EAP akkor válik nélkülözhetetlenné, amikor mélyebb, személyre szabott, szakértői támogatásra van szükség. Ez a két megközelítés tehát kiegészítheti egymást, és egy tudatos HR-stratégiában mindkettőnek helye van.</p>

        <p>A vállalatok számára a kihívás abban áll, hogyan kommunikálják ezt a különbséget a munkavállalók felé. Ha ugyanis a dolgozó azt gondolja, hogy a wellbeing app az EAP, akkor lehet, hogy soha nem fordul valódi szakemberhez, amikor szüksége lenne rá. Ez hosszú távon nemcsak egyéni, hanem szervezeti szinten is károkat okozhat.</p>

        <p>A legjobb gyakorlat az, ha a cégek világosan kijelölik a határokat: elmagyarázzák, hogy a wellbeing platform mire jó, és mikor kell inkább az EAP-hoz fordulni. Ha a kettőt összehangoltan, egymást kiegészítve kínálják, az win-win helyzetet teremt. A dolgozó napi szinten használhat wellbeing eszközöket a stressz csökkentésére vagy a fizikai állóképesség javítására, és közben tudja, hogy bármikor bizalmasan fordulhat szakértőkhöz komolyabb problémákkal.</p>

        <p>A digitális wellbeing platformok és az EAP tehát nem egymás versenytársai, hanem egy tágabb ökoszisztéma részei. A HR feladata, hogy megtalálja az egyensúlyt, és biztosítsa, hogy a munkavállalók pontosan tudják, melyik eszközt mikor és hogyan használhatják a legnagyobb haszonnal.</p>
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

      {/* The Journalist Header */}
      <section className="relative py-8 px-4 overflow-hidden bg-white border-t border-border">
        <div className="max-w-7xl mx-auto relative z-10">
          <div className="flex items-center justify-between gap-8">
            <div className="flex flex-col items-start text-sm text-black">
              <span className="font-semibold">2025. JANUÁR</span>
              <span className="text-xs">16. Szám</span>
            </div>
            <div className="flex-shrink-0">
              <img 
                src={journalistLogo} 
                alt="The Journalist!" 
                className="h-24 object-contain"
              />
            </div>
            <div className="flex items-end text-right">
              <span className="text-sm font-medium text-black hidden md:inline uppercase tracking-wide">
                Az EAP világ<br />szakfolyóirata
              </span>
            </div>
          </div>
        </div>
        <div className="absolute bottom-0 left-0 right-0">
          <div className="border-b-4 border-black"></div>
          <div className="border-b border-black mt-1"></div>
        </div>
      </section>

      {/* Article Content */}
      <article className="w-full">
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

          {/* Article Header */}
          <div className="mb-8">
            <Badge className="mb-4 text-base px-4 py-1">{article.category}</Badge>
            <h1 className="text-4xl md:text-6xl font-bold mb-6 leading-tight">
              {article.title}
            </h1>
            
            {/* Meta Info */}
            <div className="flex flex-wrap items-center gap-4 md:gap-6 text-sm text-muted-foreground pb-8 border-b">
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
          </div>

          {/* Featured Image - Full Width */}
          {article.image && (
            <div className="mb-12">
              <img 
                src={article.image} 
                alt={article.title}
                className="w-full rounded-lg shadow-lg max-h-[600px] object-cover"
              />
            </div>
          )}

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
