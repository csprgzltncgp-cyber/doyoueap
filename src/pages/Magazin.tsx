import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Clock, User, TrendingUp, Calendar, ChevronRight } from 'lucide-react';
import logo from '@/assets/logo.png';
import journalistLogo from '@/assets/thejournalist_logo.png';
import journalistBg from '@/assets/journalist-bg.jpg';
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
import { useAuth } from '@/hooks/useAuth';
import { 
  Carousel, 
  CarouselContent, 
  CarouselItem, 
  CarouselNext, 
  CarouselPrevious 
} from '@/components/ui/carousel';

const Magazin = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();
  const [activeCategory, setActiveCategory] = useState('all');

  const featuredArticles = [
    {
      title: "Mítoszok és tévhitek: mi nem EAP – és mi az valójában",
      excerpt: "Az elmúlt években a vállalati jól-lét (wellbeing) fogalma világszerte központi témává vált. Tisztázzuk szakmai érvekkel: mi az EAP, mi az, ami hasznos kiegészítés lehet, és mikor kell különbséget tenni az eltérő megoldások között.",
      author: "doyoueap",
      date: "2025. január 15.",
      readTime: "8 perc",
      category: "Alapok",
      image: mythsArticleImg
    },
    {
      title: "A 4Score mutatók ereje: hogyan mérhető az EAP valódi értéke",
      excerpt: "Egy Employee Assistance Program (EAP) sikerességét ma már nem elég érzésre vagy eseti visszajelzésekre alapozni. A szervezetek elvárják, hogy a támogatási programok hatása mérhető, összehasonlítható és kimutatható legyen — emberi és üzleti szinten egyaránt. A 4Score modell négy kulcsmutatón keresztül ad átfogó képet: Tudatosság, Használat, Bizalom & Hajlandóság, valamint Hatás.",
      author: "doyoueap",
      date: "2025. január 12.",
      readTime: "7 perc",
      category: "Mérés",
      image: scoreArticleImg
    },
    {
      title: "Az EAP jövője és a digitalizáció szerepe",
      excerpt: "A jövő EAP-ja nem egyszerűen a múlt modelljeinek digitális másolata, hanem egy új, hibrid rendszer, amely ötvözi a technológia előnyeit a személyes kapcsolattartás értékeivel.",
      author: "doyoueap",
      date: "2025. január 10.",
      readTime: "6 perc",
      category: "Jövő",
      image: futureArticleImg
    }
  ];

  const articles = [
    {
      title: "A szervezeti klíma szerepe az EAP hatékonyságában",
      excerpt: "Egy EAP soha nem a vákuumban működik. A program hatását mindig befolyásolja a szervezet kultúrája, a vezetők attitűdje és a munkahelyi környezet.",
      author: "doyoueap",
      date: "2025. január 8.",
      readTime: "6 perc",
      category: "Kultúra",
      image: climateArticleImg
    },
    {
      title: "Digitális wellbeing platformok és az EAP – kiegészítés vagy verseny?",
      excerpt: "Az elmúlt években gombamód szaporodtak a digitális wellbeing platformok. Nem arról van szó, hogy az egyik jobb, a másik rosszabb – inkább arról, hogy más funkciót töltenek be.",
      author: "doyoueap",
      date: "2025. január 6.",
      readTime: "7 perc",
      category: "Technológia",
      image: digitalWellbeingArticleImg
    },
    {
      title: "Globális kitekintés: EAP-használat régiók szerint",
      excerpt: "Az Employee Assistance Programok nemzetközi elterjedtsége és használata nagyon különböző képet mutat a világ régióiban. Nincs egyetlen univerzális modell – az EAP sikeressége mindig kontextusfüggő.",
      author: "doyoueap",
      date: "2025. január 5.",
      readTime: "8 perc",
      category: "Globális",
      image: globalArticleImg
    },
    {
      title: "A vezetők szerepe és felelőssége az EAP sikerében",
      excerpt: "Egy Employee Assistance Program sikerének kulcsa nemcsak a szolgáltatások minőségén múlik, hanem azon is, hogy a vezetők hogyan viszonyulnak hozzá.",
      author: "doyoueap",
      date: "2025. január 3.",
      readTime: "6 perc",
      category: "Vezetés",
      image: leadershipArticleImg
    },
    {
      title: "A stigma lebontása és a kommunikáció szerepe",
      excerpt: "Az EAP egyik legnagyobb akadálya világszerte a stigma. A stigma lebontásának első lépése a kommunikáció – folyamatosan, változatos formában kell találkozniuk a munkavállalóknak az üzenettel.",
      author: "doyoueap",
      date: "2025. január 2.",
      readTime: "7 perc",
      category: "Kommunikáció",
      image: stigmaArticleImg
    },
    {
      title: "Az EAP és a munkavállalói elköteleződés kapcsolata",
      excerpt: "Az elkötelezett dolgozók produktívabbak, lojálisabbak, kevésbé hajlamosak a fluktuációra. Az EAP ugyan elsősorban támogató szolgáltatásként ismert, mégis közvetlenül és közvetve is erősíti az elköteleződést.",
      author: "doyoueap",
      date: "2024. december 30.",
      readTime: "6 perc",
      category: "HR",
      image: engagementArticleImg
    },
    {
      title: "Az EAP mérhetősége és a megtérülés (ROI) kérdése",
      excerpt: "Számos kutatás bizonyítja, hogy az EAP nemcsak humánus és etikus megoldás, hanem gazdaságilag is kifizetődő. A programok átlagosan 3-10-szeres megtérülést hoznak.",
      author: "doyoueap",
      date: "2024. december 28.",
      readTime: "8 perc",
      category: "ROI",
      image: roiArticleImg
    }
  ];

  const popularArticles = [
    { title: "Mítoszok és tévhitek az EAP-ról", views: "15.2k" },
    { title: "4A mutatók a gyakorlatban", views: "12.8k" },
    { title: "EAP ROI számokban", views: "11.5k" },
    { title: "Digitalizáció az EAP-ban", views: "9.3k" }
  ];

  const categories = [
    { id: 'all', label: 'Összes' },
    { id: 'basics', label: 'Alapok' },
    { id: 'measurement', label: 'Mérés' },
    { id: 'culture', label: 'Kultúra' },
    { id: 'roi', label: 'ROI' }
  ];

  const gradients = {
    'gradient-1': 'from-blue-500/20 to-purple-500/20',
    'gradient-2': 'from-green-500/20 to-teal-500/20',
    'gradient-3': 'from-orange-500/20 to-red-500/20'
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header with Navigation */}
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
                EAP Pulse
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

      {/* Hero Section - Newspaper Style Header */}
      <section className="relative py-8 overflow-hidden bg-white border-t border-border">
        <div className="max-w-7xl mx-auto px-4 relative z-10">
          {/* Three column layout: Date - Logo - Text */}
          <div className="flex items-center justify-between gap-8">
            {/* Left: Date and Issue */}
            <div className="flex flex-col items-start text-sm text-black">
              <span className="font-semibold">2025. JANUÁR</span>
              <span className="text-xs">16. Szám</span>
            </div>

            {/* Center: Logo */}
            <div className="flex-shrink-0">
              <img 
                src={journalistLogo} 
                alt="The Journalist!" 
                className="h-24 object-contain"
              />
            </div>

            {/* Right: Magazine description */}
            <div className="flex items-end text-right">
              <span className="text-sm font-medium text-black hidden md:inline uppercase tracking-wide">
                Az EAP világ<br />szakfolyóirata
              </span>
            </div>
          </div>
        </div>
        {/* Bottom borders - thick then thin */}
        <div className="absolute bottom-0 left-0 right-0">
          <div className="border-b-4 border-black"></div>
          <div className="border-b border-black mt-1"></div>
        </div>
      </section>

      {/* Featured Carousel */}
      <section className="py-12 px-4 bg-gradient-to-br from-primary/5 via-background to-primary/10">
        <div className="max-w-6xl mx-auto">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold">Kiemelt Cikkek</h2>
            <TrendingUp className="h-6 w-6 text-primary" />
          </div>
          <Carousel className="w-full">
            <CarouselContent>
              {featuredArticles.map((article, index) => (
                  <CarouselItem key={index}>
                  <div className="grid md:grid-cols-2 gap-8 items-center">
                    {/* Large Featured Image */}
                    <div 
                      className="relative aspect-[4/3] overflow-hidden rounded-lg cursor-pointer"
                      onClick={() => {
                        const slugs = ['mitoszok-es-tevhitek', '4score-mutatok', 'eap-jovoje'];
                        navigate(`/magazin/${slugs[index]}`);
                      }}
                    >
                      <img 
                        src={article.image} 
                        alt={article.title}
                        className="w-full h-full object-cover"
                      />
                      <Badge className="absolute top-4 left-4 z-10">{article.category}</Badge>
                    </div>
                    
                    {/* Content */}
                    <div className="space-y-4">
                      <h3 
                        className="text-4xl font-bold hover:text-primary transition-colors cursor-pointer leading-tight"
                        onClick={() => {
                          const slugs = ['mitoszok-es-tevhitek', '4score-mutatok', 'eap-jovoje'];
                          navigate(`/magazin/${slugs[index]}`);
                        }}
                      >
                        {article.title}
                      </h3>
                      <p className="text-lg text-muted-foreground leading-relaxed">
                        {article.excerpt}
                      </p>
                      <div className="flex items-center gap-6 text-sm text-muted-foreground pt-2">
                        <div className="flex items-center gap-2">
                          <User className="h-4 w-4" />
                          <span>{article.author}</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <Calendar className="h-4 w-4" />
                          <span>{article.date}</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <Clock className="h-4 w-4" />
                          <span>{article.readTime}</span>
                        </div>
                      </div>
                      <Button 
                        className="group mt-4 bg-[hsl(var(--magazine-red))] hover:bg-[hsl(var(--magazine-red))]/90 text-white"
                        onClick={() => {
                          const slugs = ['mitoszok-es-tevhitek', '4score-mutatok', 'eap-jovoje'];
                          navigate(`/magazin/${slugs[index]}`);
                        }}
                      >
                        Tovább olvasom 
                        <ChevronRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                      </Button>
                    </div>
                  </div>
                </CarouselItem>
              ))}
            </CarouselContent>
            <CarouselPrevious className="-left-12" />
            <CarouselNext className="-right-12" />
          </Carousel>
        </div>
      </section>

      {/* Main Content with Sidebar */}
      <section className="py-12 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="grid lg:grid-cols-3 gap-8">
            {/* Main Articles */}
            <div className="lg:col-span-2">
              {/* Category Filter */}
              <div className="flex gap-3 mb-8 overflow-x-auto pb-2">
                {categories.map((cat) => (
                  <Button
                    key={cat.id}
                    variant={activeCategory === cat.id ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => setActiveCategory(cat.id)}
                    className={`whitespace-nowrap ${activeCategory !== cat.id ? 'bg-[hsl(var(--magazine-category-bg))] hover:bg-[hsl(var(--magazine-category-bg))]/80 border-[hsl(var(--magazine-category-bg))]' : ''}`}
                  >
                    {cat.label}
                  </Button>
                ))}
              </div>

              <h2 className="text-2xl font-bold mb-6">Legújabb Cikkek</h2>
              
              <div className="space-y-6">
                {articles.map((article, index) => {
                  // Map articles to their slugs
                  const slugMap: Record<number, string> = {
                    0: 'szervezeti-klima-szerepe',
                    1: 'digitalis-wellbeing-platformok',
                    2: 'globalis-kitekintes',
                    3: 'vezetok-szerepe',
                    4: 'stigma-lebontasa',
                    5: 'munkavallaoi-elkotelezoedes',
                    6: 'eap-merhetosege-roi'
                  };

                  return (
                    <div 
                      key={index} 
                      className="grid md:grid-cols-[200px_1fr] gap-4 cursor-pointer group"
                      onClick={() => navigate(`/magazin/${slugMap[index]}`)}
                    >
                    <Card className="hover:shadow-lg transition-all group-hover:scale-[1.02] overflow-hidden">
                      {article.image ? (
                        <img 
                          src={article.image} 
                          alt={article.title}
                          className="w-full h-full object-cover"
                        />
                      ) : (
                        <div className="h-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                          <div className="text-4xl font-bold text-primary/30">{index + 1}</div>
                        </div>
                      )}
                    </Card>
                    <Card className="hover:shadow-lg transition-all group-hover:scale-[1.02]">
                      <CardHeader>
                        <Badge className="w-fit mb-2">{article.category}</Badge>
                        <CardTitle className="text-xl group-hover:text-primary transition-colors">
                          {article.title}
                        </CardTitle>
                        <CardDescription className="mt-2">{article.excerpt}</CardDescription>
                      </CardHeader>
                      <CardContent>
                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                          <div className="flex items-center gap-1">
                            <User className="h-4 w-4" />
                            <span>{article.author}</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Clock className="h-4 w-4" />
                            <span>{article.readTime}</span>
                          </div>
                          <span>{article.date}</span>
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                );
              })}
              </div>

              <div className="mt-8 text-center">
                <Button variant="outline" size="lg">
                  További Cikkek Betöltése
                </Button>
              </div>
            </div>

            {/* Sidebar */}
            <div className="space-y-6">
              {/* Newsletter */}
              <Card className="bg-gradient-to-br from-[hsl(var(--magazine-red))] to-[hsl(var(--magazine-yellow))] text-white border-0">
                <CardHeader>
                  <CardTitle>Hírlevél Feliratkozás</CardTitle>
                  <CardDescription className="text-white/90">
                    Heti összefoglaló a legújabb cikkekről
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <input 
                    type="email" 
                    placeholder="Email cím"
                    className="w-full px-4 py-2 rounded-sm mb-3 bg-white/20 border border-white/30 text-white placeholder:text-white/70"
                  />
                  <Button variant="secondary" className="w-full">
                    Feliratkozom
                  </Button>
                </CardContent>
              </Card>

              {/* Popular Articles */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <TrendingUp className="h-5 w-5 text-primary" />
                    Népszerű Cikkek
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-3">
                    {popularArticles.map((item, index) => (
                      <div 
                        key={index}
                        className="flex items-start gap-3 p-3 rounded hover:bg-muted transition-colors cursor-pointer group"
                      >
                        <div className="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary">
                          {index + 1}
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="text-sm font-medium group-hover:text-primary transition-colors line-clamp-2">
                            {item.title}
                          </p>
                          <p className="text-xs text-muted-foreground mt-1 flex items-center gap-1">
                            <TrendingUp className="h-3 w-3" />
                            {item.views} megtekintés
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              {/* Categories */}
              <Card>
                <CardHeader>
                  <CardTitle>Kategóriák</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    {['Alapok', 'Mérés', 'Kultúra', 'Vezetés', 'ROI', 'Kommunikáció', 'Globális', 'Jövő'].map((cat) => (
                      <button
                        key={cat}
                        className="w-full text-left px-3 py-2 rounded-sm bg-[hsl(var(--magazine-category-bg))] hover:bg-[hsl(var(--magazine-category-bg))]/80 transition-colors text-sm flex items-center justify-between group"
                      >
                        <span>{cat}</span>
                        <ChevronRight className="h-4 w-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all" />
                      </button>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Magazin;
