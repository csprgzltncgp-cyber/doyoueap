import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Clock, User, TrendingUp, Calendar, ChevronRight } from 'lucide-react';
import logo from '@/assets/logo.png';
import journalistLogo from '@/assets/thejournalist_logo.png';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
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
  const [featuredArticles, setFeaturedArticles] = useState<any[]>([]);
  const [articles, setArticles] = useState<any[]>([]);
  const [loadingArticles, setLoadingArticles] = useState(true);

  useEffect(() => {
    fetchArticles();
  }, []);

  const fetchArticles = async () => {
    try {
      setLoadingArticles(true);
      const { data, error } = await supabase
        .from('magazine_articles')
        .select('*')
        .eq('is_published', true)
        .order('published_date', { ascending: false });

      if (error) throw error;

      if (data) {
        const featured = data.filter(article => article.is_featured);
        const regular = data.filter(article => !article.is_featured);
        setFeaturedArticles(featured);
        setArticles(regular);
      }
    } catch (error) {
      console.error('Error fetching articles:', error);
      toast.error('Hiba történt a cikkek betöltése közben');
    } finally {
      setLoadingArticles(false);
    }
  };

  const popularArticles = [
    { title: "Mítoszok és tévhitek az EAP-ról", views: "15.2k" },
    { title: "4A mutatók a gyakorlatban", views: "12.8k" },
    { title: "EAP ROI számokban", views: "11.5k" },
    { title: "Digitalizáció az EAP-ban", views: "9.3k" }
  ];

  const categories = [
    { id: 'all', label: 'Összes' },
    { id: 'Alapok', label: 'Alapok' },
    { id: 'Mérés', label: 'Mérés' },
    { id: 'Kultúra', label: 'Kultúra' },
    { id: 'Jövő', label: 'Jövő' },
    { id: 'EAP', label: 'EAP' }
  ];

  const filteredArticles = activeCategory === 'all' 
    ? articles 
    : articles.filter(article => article.category === activeCategory);

  const gradients = {
    'gradient-1': 'from-blue-500/20 to-purple-500/20',
    'gradient-2': 'from-green-500/20 to-teal-500/20',
    'gradient-3': 'from-orange-500/20 to-red-500/20'
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header with Navigation */}
      <header className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center relative">
          <div className="flex items-center">
            <img 
              src={logo} 
              alt="EAP Pulse" 
              className="h-8 cursor-pointer" 
              onClick={() => navigate('/')}
            />
          </div>
          <nav className="hidden md:flex gap-6 items-center absolute left-1/2 -translate-x-1/2">
            <button
              onClick={() => navigate('/bemutatkozas')}
              className="text-sm border border-transparent transition-colors px-3 py-2 rounded-md hover:bg-muted"
            >
              EAP Pulse
            </button>
            <button
              onClick={() => navigate('/arak')}
              className="text-sm border border-transparent transition-colors px-3 py-2 rounded-md hover:bg-muted"
            >
              Árak és Csomagok
            </button>
            <button
              onClick={() => navigate('/magazin')}
              className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-md"
            >
              The Journalist!
            </button>
            {user && role === 'hr' && (
              <button
                onClick={() => navigate('/?section=focus')}
                className="text-sm border border-transparent transition-colors px-3 py-2 rounded-md hover:bg-muted"
              >
                Dashboard
              </button>
            )}
          </nav>
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
      <section className="relative py-8 overflow-hidden bg-gradient-to-b from-muted/50 to-background border-t border-border">
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
          {loadingArticles ? (
            <div className="text-center py-12">Betöltés...</div>
          ) : featuredArticles.length === 0 ? (
            <div className="text-center py-12 text-muted-foreground">Nincsenek kiemelt cikkek</div>
          ) : (
            <Carousel className="w-full">
              <CarouselContent>
                {featuredArticles.map((article) => (
                  <CarouselItem key={article.id}>
                    <div className="grid md:grid-cols-2 gap-8 items-center">
                      {/* Large Featured Image */}
                      <div 
                        className="relative aspect-[4/3] overflow-hidden rounded-lg cursor-pointer"
                        onClick={() => navigate(`/magazin/${article.slug}`)}
                      >
                        {article.image_url ? (
                          <img 
                            src={article.image_url} 
                            alt={article.title}
                            className="w-full h-full object-cover"
                          />
                        ) : (
                          <div className="w-full h-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                            <span className="text-4xl font-bold text-primary/30">?</span>
                          </div>
                        )}
                        <Badge className="absolute top-4 left-4 z-10">{article.category}</Badge>
                      </div>
                      
                      {/* Content */}
                      <div className="space-y-4">
                        <h3 
                          className="text-4xl font-bold hover:text-primary transition-colors cursor-pointer leading-tight"
                          onClick={() => navigate(`/magazin/${article.slug}`)}
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
                            <span>{new Date(article.published_date).toLocaleDateString('hu-HU', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                          </div>
                        </div>
                        <Button 
                          className="group mt-4 bg-[hsl(var(--magazine-red))] hover:bg-[hsl(var(--magazine-red))]/90 text-white"
                          onClick={() => navigate(`/magazin/${article.slug}`)}
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
          )}
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
                    className={`whitespace-nowrap ${activeCategory !== cat.id ? 'bg-primary/10 hover:bg-primary/15 border-primary/10' : ''}`}
                  >
                    {cat.label}
                  </Button>
                ))}
              </div>

              <h2 className="text-2xl font-bold mb-6">Legújabb Cikkek</h2>
              
              {loadingArticles ? (
                <div className="text-center py-12">Betöltés...</div>
              ) : filteredArticles.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground">Nincsenek cikkek ebben a kategóriában</div>
              ) : (
                <div className="space-y-6">
                  {filteredArticles.map((article) => (
                    <div 
                      key={article.id} 
                      className="grid md:grid-cols-[200px_1fr] gap-4 cursor-pointer group"
                      onClick={() => navigate(`/magazin/${article.slug}`)}
                    >
                      <Card className="hover:shadow-lg transition-all group-hover:scale-[1.02] overflow-hidden">
                        {article.image_url ? (
                          <img 
                            src={article.image_url} 
                            alt={article.title}
                            className="w-full h-full object-cover"
                          />
                        ) : (
                          <div className="h-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                            <div className="text-4xl font-bold text-primary/30">?</div>
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
                            <span>{new Date(article.published_date).toLocaleDateString('hu-HU', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                          </div>
                        </CardContent>
                      </Card>
                    </div>
                  ))}
                </div>
              )}

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
                    {['Alapok', 'Mérés', 'Kultúra', 'Jövő'].map((cat) => (
                      <button
                        key={cat}
                        className="w-full text-left px-3 py-2 rounded-sm bg-primary/10 hover:bg-primary/15 transition-colors text-sm flex items-center justify-between group"
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
