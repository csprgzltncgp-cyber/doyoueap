import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Clock, User, TrendingUp, Calendar, ChevronRight } from 'lucide-react';
import logo from '@/assets/doyoueap-logo.png';
import journalistLogo from '@/assets/thejournalist_logo.png';
import featuredArticleImg from '@/assets/featured-article.jpg';
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
      title: "A mentális egészség munkahelyi jelentősége 2025-ben",
      excerpt: "Az elmúlt évek tapasztalatai alapján a vállalatok egyre nagyobb figyelmet fordítanak munkavállalóik mentális jólétére. Nézzük meg, milyen trendek várhatóak idén.",
      author: "Dr. Kovács Anna",
      date: "2025. január 15.",
      readTime: "8 perc",
      category: "Trendek",
      image: "gradient-1"
    },
    {
      title: "Az EAP program ROI-ja: számok, amelyek beszélnek",
      excerpt: "Mennyire térül meg egy jól működő EAP program? Esettanulmányok és statisztikák a befektetés megtérüléséről.",
      author: "Szabó Katalin",
      date: "2025. január 10.",
      readTime: "6 perc",
      category: "Elemzések",
      image: "gradient-2"
    },
    {
      title: "Stresszkezelési technikák a mindennapokban",
      excerpt: "Praktikus módszerek, amelyeket azonnal alkalmazhatunk a munkahelyi stressz csökkentésére.",
      author: "Tóth László",
      date: "2025. január 8.",
      readTime: "4 perc",
      category: "Gyakorlatok",
      image: "gradient-3"
    }
  ];

  const articles = [
    {
      title: "Hogyan ismerjük fel a kiégés jeleit?",
      excerpt: "A munkahelyi kiégés egyre gyakoribb probléma. Ebben a cikkben bemutatjuk a legfontosabb figyelmeztető jeleket.",
      author: "Nagy Péter",
      date: "2025. január 12.",
      readTime: "5 perc",
      category: "Tanácsok"
    },
    {
      title: "A hibrid munkavégzés kihívásai",
      excerpt: "Hogyan őrizhetjük meg a mentális egyensúlyt az otthoni és irodai munka között?",
      author: "Kiss Mónika",
      date: "2025. január 5.",
      readTime: "7 perc",
      category: "Munkakörnyezet"
    },
    {
      title: "Vezetői támogatás és empátia",
      excerpt: "A jó vezetők kulcsszerepet játszanak a munkavállalók mentális jólétében.",
      author: "Farkas Balázs",
      date: "2025. január 3.",
      readTime: "6 perc",
      category: "Vezetés"
    },
    {
      title: "Mindfulness a munkahelyen",
      excerpt: "Egyszerű tudatossági gyakorlatok, amelyek segítenek megőrizni a fókuszt és a nyugalmat.",
      author: "Molnár Eszter",
      date: "2024. december 28.",
      readTime: "5 perc",
      category: "Gyakorlatok"
    },
    {
      title: "Az EAP szolgáltatások fejlődése",
      excerpt: "Hogyan változtak az EAP programok az elmúlt évtizedben?",
      author: "Horváth Gábor",
      date: "2024. december 25.",
      readTime: "9 perc",
      category: "Történet"
    },
    {
      title: "Generációs különbségek a munkahelyen",
      excerpt: "Különböző generációk, különböző igények - hogyan lehet mindenkit támogatni?",
      author: "Varga Júlia",
      date: "2024. december 22.",
      readTime: "6 perc",
      category: "Elemzések"
    }
  ];

  const popularArticles = [
    { title: "Top 10 mentális egészség tipp", views: "12.5k" },
    { title: "EAP program sikersztori", views: "9.8k" },
    { title: "Vezetői interjú", views: "8.2k" },
    { title: "Statisztikák 2024", views: "7.5k" }
  ];

  const categories = [
    { id: 'all', label: 'Összes' },
    { id: 'trends', label: 'Trendek' },
    { id: 'analysis', label: 'Elemzések' },
    { id: 'interviews', label: 'Interjúk' },
    { id: 'tips', label: 'Tanácsok' }
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
                className="text-sm bg-primary text-primary-foreground font-semibold transition-colors px-3 py-2 rounded"
              >
                The Journalis
              </button>
              <button
                onClick={() => navigate('/bemutatkozas')}
                className="text-sm transition-colors px-3 py-2 rounded hover:bg-muted"
              >
                Bemutatkozás
              </button>
              <button
                onClick={() => navigate('/arak')}
                className="text-sm transition-colors px-3 py-2 rounded hover:bg-muted"
              >
                Árak és Csomagok
              </button>
              {user && role === 'hr' && (
                <button
                  onClick={() => navigate('/?section=eap-pulse&sub=create-audit')}
                  className="text-sm transition-colors px-3 py-2 rounded hover:bg-muted"
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

      {/* Hero Section */}
      <section className="bg-gradient-to-br from-primary/10 via-primary/5 to-background py-16 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-4 flex justify-center">
            <img 
              src={journalistLogo} 
              alt="The Journalis" 
              className="h-48 object-contain"
            />
          </div>
        </div>
      </section>

      {/* Featured Carousel */}
      <section className="py-12 px-4">
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
                    <div className="relative aspect-[4/3] overflow-hidden rounded-lg">
                      <img 
                        src={featuredArticleImg} 
                        alt={article.title}
                        className="w-full h-full object-cover"
                      />
                      <Badge className="absolute top-4 left-4 z-10">{article.category}</Badge>
                    </div>
                    
                    {/* Content */}
                    <div className="space-y-4">
                      <h3 className="text-4xl font-bold hover:text-primary transition-colors cursor-pointer leading-tight">
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
                      <Button className="group mt-4">
                        Tovább olvasom 
                        <ChevronRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                      </Button>
                    </div>
                  </div>
                </CarouselItem>
              ))}
            </CarouselContent>
            <CarouselPrevious className="left-2" />
            <CarouselNext className="right-2" />
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
                    className="whitespace-nowrap"
                  >
                    {cat.label}
                  </Button>
                ))}
              </div>

              <h2 className="text-2xl font-bold mb-6">Legújabb Cikkek</h2>
              
              <div className="space-y-6">
                {articles.map((article, index) => (
                  <Card key={index} className="hover:shadow-lg transition-all hover:scale-[1.02] cursor-pointer group">
                    <div className="md:flex">
                      <div className="md:w-48 h-48 bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                        <div className="text-4xl font-bold text-primary/30">{index + 1}</div>
                      </div>
                      <div className="flex-1">
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
                      </div>
                    </div>
                  </Card>
                ))}
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
              <Card className="bg-primary text-primary-foreground">
                <CardHeader>
                  <CardTitle>Hírlevél Feliratkozás</CardTitle>
                  <CardDescription className="text-primary-foreground/80">
                    Heti összefoglaló a legújabb cikkekről
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <input 
                    type="email" 
                    placeholder="Email cím"
                    className="w-full px-4 py-2 rounded mb-3 bg-primary-foreground/10 border border-primary-foreground/20 text-primary-foreground placeholder:text-primary-foreground/60"
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
                    {['Trendek', 'Elemzések', 'Interjúk', 'Tanácsok', 'Statisztikák', 'Történet'].map((cat) => (
                      <button
                        key={cat}
                        className="w-full text-left px-3 py-2 rounded hover:bg-muted transition-colors text-sm flex items-center justify-between group"
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
