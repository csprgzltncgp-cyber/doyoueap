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
        <p>Az Employee Assistance Programok nemzetközi elterjedtsége és használata nagyon különböző képet mutat a világ régióiban. Míg Észak-Amerikában és Ausztráliában az EAP évtizedek óta bevett gyakorlat, Európában, Ázsiában vagy Afrikában sok helyen még mindig új, kevéssé ismert eszköznek számít. Ez a különbség nemcsak történelmi okokra vezethető vissza, hanem kulturális tényezőkre és a mentális egészséghez való társadalmi hozzáállásra is.</p>

        <p>Észak-Amerikában az EAP az 1950-es évektől kezdve folyamatosan fejlődött, és ma a legtöbb nagyvállalat kínálja dolgozóinak. Az ismertség és az elfogadottság magas, a stigma alacsonyabb, és a szolgáltatásokat széles körben igénybe is veszik. Európában a helyzet vegyes: Nyugat-Európában sok cég alkalmaz EAP-t, de Közép- és Kelet-Európában még viszonylag kevésbé ismert. Az eltérések oka részben az, hogy a régiókban eltérő mértékű a munkahelyi mentális egészség társadalmi elfogadottsága, és nem mindenhol alakult ki olyan erős munkajogi vagy vállalati kultúra, amely az EAP-kat természetesnek tekintené.</p>

        <p>Ázsiában a fejlődés még inkább kezdeti stádiumban van. Bár egyre több multinacionális cég igyekszik bevezetni a programot, a helyi kulturális normák sokszor akadályozzák az igénybevételt: a mentális egészséggel kapcsolatos stigma erős, a dolgozók gyakran félnek segítséget kérni, nehogy az gyengeségnek tűnjön. Afrikában és Latin-Amerikában hasonló a helyzet: az EAP elindult, de egyelőre korlátozottan ismert, és sokszor hiányzik az infrastruktúra vagy a szakemberhálózat, amely lehetővé tenné a széles körű működést.</p>

        <p>A globális kitekintés fontos tanulsága, hogy nincs egyetlen univerzális modell. Az EAP sikeressége mindig kontextusfüggő: figyelembe kell venni a kulturális attitűdöket, a jogi környezetet és a vállalati gyakorlatokat. Ugyanakkor a nemzetközi tapasztalatokból sokat lehet tanulni. Észak-Amerika példája mutatja, hogy a program hosszú távon fenntartható és eredményes lehet, ha a szervezetek és a munkavállalók bizalmat építenek ki iránta. Európa és Ázsia pedig arra hívja fel a figyelmet, hogy a bevezetéshez elengedhetetlen a folyamatos edukáció és a stigma lebontása.</p>

        <p>A HR és a menedzsment számára a globális benchmarkok azt az üzenetet hordozzák, hogy az EAP nem statikus program, hanem egy olyan rendszer, amelynek alkalmazkodnia kell a helyi igényekhez és kultúrához. Ezért a nemzetközi szervezeteknél célszerű régiónként eltérő kommunikációs és bevezetési stratégiát kialakítani. Ami működik az Egyesült Államokban, nem biztos, hogy ugyanúgy működik Kínában vagy Magyarországon. A kulcs a rugalmasság és a kulturális érzékenység, mert csak így válhat az EAP valóban globális értékké.</p>
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
        <p>Egy Employee Assistance Program sikerének kulcsa nemcsak a szolgáltatások minőségén vagy a rendszer felépítésén múlik, hanem azon is, hogy a vezetők hogyan viszonyulnak hozzá. A menedzsment és a középvezetők hozzáállása közvetlenül meghatározza, hogy a dolgozók mennyire bíznak a programban, és mennyire érzik azt legitimnek, használhatónak.</p>

        <p>A vezetők szerepe többrétegű. Először is ők azok, akik formálják a szervezeti kultúrát: ha egy vezető nyíltan és hitelesen beszél a mentális egészség fontosságáról, az üzenetet küld arról, hogy az EAP nem papíron létező eszköz, hanem valós lehetőség. Másodszor, a vezetők a mindennapi működésben is példát mutatnak. Amikor támogatják a munkavállalókat abban, hogy biztonságosan fordulhassanak szakemberhez, vagy amikor elismerik, hogy a segítségkérés a felelősségteljes döntés része, az lebontja a stigmát. Kutatások is alátámasztják: ahol a vezetők proaktívan kommunikálnak az EAP-ról, ott magasabb az igénybevételi arány, és a munkavállalók elégedettsége is nő. Ha azonban a vezetés hallgat, esetleg cinikusan áll a programhoz, a dolgozók azt érzik, hogy valójában nincs felhatalmazásuk élni a lehetőséggel.</p>

        <p>A vezetői felelősség kiterjed a kommunikáció módjára is. Nem elég évente egyszer megemlíteni a programot egy belső hírlevélben. Szükség van rendszeres, hiteles üzenetekre: vezetői fórumokon, csapatmegbeszéléseken, személyes példákon keresztül. Amikor egy vezető arról beszél, hogy a segítség igénybevétele természetes, az normalizálja a folyamatot. Fontos azonban, hogy a vezetők ne csak „beszéljenek róla", hanem a gyakorlatban is támogassák az EAP-t. Ez jelentheti például, hogy rugalmas időt biztosítanak a konzultációra, hogy garantálják a bizalmasságot, vagy hogy támogatják a HR-t a programhoz kapcsolódó kampányokban. Így a vezető nemcsak üzenetet közvetít, hanem aktívan hozzájárul a program sikeréhez.</p>

        <p>Összességében a vezetők jelenléte és támogatása teszi élővé az EAP-t. Egy program akkor válik valódi eszközzé, ha a dolgozók érzik: a menedzsment nemcsak finanszírozza, hanem szívből hisz is benne. A vezetők felelőssége tehát kulcsfontosságú – ők azok, akik legitimálják az EAP-t, lebontják a stigmát, és példájukkal bátorítják a munkavállalókat a segítség igénybevételére.</p>
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
        <p>Az Employee Assistance Program (EAP) egyik legnagyobb akadálya világszerte a stigma. A mentális egészséghez kapcsolódó előítéletek sokszor visszatartják a dolgozókat attól, hogy igénybe vegyék a számukra biztosított támogatást, még akkor is, ha tudják, hogy a program elérhető és bizalmas. A stigma nemcsak egyéni szinten jelent problémát, hanem szervezeti és társadalmi szinten is: ha az emberek szégyellik a segítségkérést, az a program kihasználatlanságához, rejtett problémákhoz és hosszú távon teljesítménycsökkenéshez vezethet.</p>

        <p>A stigma lebontásának első lépése a kommunikáció. Nem elég, ha a HR évente egyszer elküld egy e-mailt az EAP-ról. A munkavállalóknak folyamatosan, változatos formában kell találkozniuk az üzenettel: plakátokon, intranetes hírekben, vezetői megszólalásokban, csapatértekezleteken. A cél az, hogy az EAP jelenléte természetessé váljon, ne egy különleges, „vészhelyzetre tartogatott" eszközként éljen a fejekben.</p>

        <p>Különösen fontos, hogy a kommunikáció nyelve empatikus és normalizáló legyen. Nem szabad úgy tálalni az EAP-t, mintha az csak „problémás" embereknek szólna. Éppen ellenkezőleg: az üzenetnek azt kell közvetítenie, hogy mindenki kerülhet nehéz helyzetbe, és a támogatás igénybevétele éppúgy természetes, mint egy orvosi vizsgálat vagy egy szakmai tréning. Ha a dolgozók azt érzik, hogy a segítségkérés része a felelős, tudatos életnek, sokkal nagyobb arányban fordulnak majd az EAP-hoz.</p>

        <p>A stigma csökkentésében óriási szerepe van a történeteknek és a példaképeknek. Amikor vezetők, vagy akár hétköznapi munkatársak név nélkül megosztják tapasztalataikat az EAP-val kapcsolatban, az hitelessé és emberközelivé teszi a programot. A dolgozók látják, hogy nem egy távoli, elvont szolgáltatásról van szó, hanem valós emberek valós élethelyzeteiről.</p>

        <p>A kommunikáció akkor igazán hatékony, ha kétirányú. Nemcsak információt közvetít, hanem visszajelzést is gyűjt. A HR-nek érdemes rendszeresen felmérnie, hogyan gondolkodnak a dolgozók az EAP-ról, milyen előítéleteik vannak, és ezekre célzottan reagálni. Így a stigma elleni küzdelem nem kampányszerű, hanem folyamatos, tudatos folyamat lesz.</p>

        <p>Összességében a stigma lebontása nem gyors feladat, de hosszú távon az egyik legfontosabb befektetés. Ha a munkavállalók nem szégyellik igénybe venni a támogatást, az EAP kihasználtsága nő, a dolgozók mentális egészsége javul, a szervezet pedig erősebb, ellenállóbb és fenntarthatóbb lesz.</p>
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
        <p>A munkavállalói elköteleződés az egyik legfontosabb tényező a szervezeti siker szempontjából. Az elkötelezett dolgozók produktívabbak, lojálisabbak, kevésbé hajlamosak a fluktuációra, és pozitívan járulnak hozzá a vállalati kultúrához. Az Employee Assistance Program (EAP) ugyan elsősorban támogató szolgáltatásként ismert, mégis közvetlenül és közvetve is erősíti az elköteleződést.</p>

        <p>Az EAP elsődleges hozzájárulása az, hogy biztonságos hátteret ad a munkavállalóknak. Amikor egy dolgozó tudja, hogy a nehézségeiben nincs egyedül, hanem elérhető számára professzionális és bizalmas támogatás, az erősíti a cég iránti bizalmat. Ez a biztonságérzet kulcsfontosságú: ha a munkavállaló úgy érzi, hogy a szervezet gondoskodik róla, ő maga is szívesebben köteleződik el hosszú távon a vállalat mellett.</p>

        <p>Az elköteleződéshez hozzátartozik az is, hogy a dolgozó értelmesnek találja a munkáját, és úgy érzi, van lehetősége fejlődni. Az EAP ebben közvetett módon segít: ha valaki személyes vagy családi problémákkal küzd, sokszor nem tud teljes figyelmével a munkára koncentrálni. A program által kínált tanácsadás, kríziskezelés vagy jogi-pénzügyi támogatás tehermentesíti a dolgozót, és így nagyobb energiát fordíthat a szakmai feladataira. Ez közvetve javítja a teljesítményt és az elégedettséget is.</p>

        <p>Az elköteleződés egyik mércéje a fluktuáció. A kutatások azt mutatják, hogy azokban a szervezetekben, ahol az EAP aktívan működik és jól kommunikált, alacsonyabb a kilépési arány. Ennek oka, hogy a dolgozók nemcsak a munkakörnyezet, hanem a munkahely által nyújtott támogatás miatt is értékesnek érzik a helyüket. Ez különösen fontos a tehetségmegőrzés szempontjából: a képzett, tapasztalt munkavállalók könnyebben maradnak egy olyan cégnél, ahol érzik a törődést.</p>

        <p>Az EAP tehát nem csupán a problémamegoldásról szól. Egy jól működő, átláthatóan kommunikált program erősíti a munkavállalók és a szervezet közötti érzelmi kötődést. A dolgozók azt érzik, hogy a cég nemcsak munkavégzőként, hanem emberként is értékeli őket. Ez az érzés teremti meg az elköteleződés alapját, amely hosszú távon a cég sikerének egyik legerősebb motorja.</p>

        <p>Összességében elmondható, hogy az EAP nemcsak egy HR-eszköz, hanem stratégiai befektetés a munkavállalói elköteleződésbe. Aki valóban törődik dolgozói jóllétével, nemcsak jobb teljesítményre számíthat, hanem lojálisabb, motiváltabb munkaerőt is nyer.</p>
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
        <p>Az Employee Assistance Program (EAP) egyik gyakori kritikája, hogy nehéz pontosan számszerűsíteni a hatását. A HR vezetők és a menedzsment sokszor kérdezik: „Milyen megtérülést hoz a program? Megéri a ráfordítást?" Ez a kérdés teljesen jogos, hiszen a szervezeti döntéshozatalban egyre nagyobb hangsúlyt kapnak a mérhető adatok és a befektetések megtérülésének igazolása. Az EAP esetében azonban a hatás sokszor közvetett és nehezebben mérhető, ami félreértésekhez vezethet.</p>

        <p>Mégis, számos kutatás bizonyítja, hogy az EAP nemcsak humánus és etikus megoldás, hanem gazdaságilag is kifizetődő. Az egyik leggyakrabban idézett adat, hogy a programok átlagosan 3-10-szeres megtérülést hoznak. Ez azt jelenti, hogy minden befektetett egységnyi összeg többszörösen térül meg a csökkentett hiányzások, a jobb teljesítmény, a kisebb fluktuáció és az egészségügyi költségek mérséklése révén.</p>

        <p>A megtérülés egyik legkézzelfoghatóbb eleme a hiányzás csökkenése. A dolgozók, akik támogatást kapnak a stressz, szorongás vagy családi problémák kezelésében, hamarabb és hatékonyabban térnek vissza a munkába. Ezzel nemcsak a betegszabadságok ideje rövidül, hanem a termelékenység is javul. Emellett az EAP segít a „presenteeism" problémáján is, amikor a munkavállaló ugyan jelen van, de személyes gondjai miatt nem tud teljes erőbedobással dolgozni. Az EAP által nyújtott támogatás révén ez a rejtett teljesítményveszteség is mérséklődik.</p>

        <p>A fluktuáció szintén fontos mérőszám. Az EAP-hoz való hozzáférés növeli a dolgozói lojalitást, hiszen a munkavállalók értékelik, ha a cég valóban törődik velük. Egy megtartott munkatárs pótlásának költsége sokszor a több havi fizetését is meghaladja, így a programok közvetve jelentős megtakarítást eredményeznek.</p>

        <p>A ROI másik dimenziója a vállalati kultúrában rejlik. Bár ez nehezebben számszerűsíthető, a pozitív hatás vitathatatlan: egy támogató, biztonságos légkörben a dolgozók nyitottabbak, együttműködőbbek, és kevesebb konfliktus alakul ki. Ez közvetve javítja a teljes szervezet hatékonyságát.</p>

        <p>A mérhetőséghez szükséges a megfelelő adatgyűjtés. Nem elég csupán a felhasználási arányokat rögzíteni – érdemes a 4Score mutatókat (Tudatosság, Használat, Bizalom & Hajlandóság, Hatás) is figyelni, valamint felmérni a dolgozói elégedettséget és a program közvetett hatásait. Az összesített adatok jól szemléltethetők riportokban és prezentációkban, amelyek segítenek a menedzsment számára is világossá tenni a program értékét.</p>

        <p>Összességében az EAP megtérülése valós és igazolható, ha a szervezet tudatosan gyűjti és elemzi az adatokat. Bár a hatások egy része nehezen számszerűsíthető, a tapasztalat és a kutatások egyaránt azt mutatják: az EAP nem költség, hanem stratégiai befektetés, amely hosszú távon mind emberi, mind gazdasági szempontból megtérül.</p>
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
                       prose-h2:text-3xl md:prose-h2:text-4xl prose-h2:mt-16 prose-h2:mb-8 
                       prose-h3:text-2xl md:prose-h3:text-3xl prose-h3:mt-12 prose-h3:mb-6 
                       prose-p:mb-8 prose-p:leading-relaxed prose-p:text-lg
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
