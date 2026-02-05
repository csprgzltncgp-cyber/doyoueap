import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Clock, User, TrendingUp, Calendar, ChevronRight } from 'lucide-react';
import journalistLogo from '@/assets/thejournalist_logo_2.png';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
import { 
  Carousel, 
  CarouselContent, 
  CarouselItem, 
  CarouselNext, 
  CarouselPrevious 
} from '@/components/ui/carousel';

export function MagazinContent() {
  const navigate = useNavigate();
  const [activeCategory, setActiveCategory] = useState('all');
  const [featuredArticles, setFeaturedArticles] = useState<any[]>([]);
  const [articles, setArticles] = useState<any[]>([]);
  const [popularArticles, setPopularArticles] = useState<any[]>([]);
  const [loadingArticles, setLoadingArticles] = useState(true);
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [subscribing, setSubscribing] = useState(false);

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
        
        const popular = [...data]
          .sort((a, b) => (b.view_count || 0) - (a.view_count || 0))
          .slice(0, 4);
        setPopularArticles(popular);
      }
    } catch (error) {
      console.error('Error fetching articles:', error);
      toast.error('Hiba történt a cikkek betöltése közben');
    } finally {
      setLoadingArticles(false);
    }
  };

  const handleNewsletterSubscribe = async (e: React.FormEvent) => {
    e.preventDefault();

    setSubscribing(true);
    try {
      const { error } = await supabase
        .from('newsletter_subscribers')
        .insert([{
          email: newsletterEmail,
          source: 'magazine',
          is_active: true
        }]);

      if (error) {
        if (error.code === '23505') {
          toast.error('Ez az email cím már feliratkozott a hírlevélre');
        } else {
          throw error;
        }
      } else {
        toast.success('Sikeresen feliratkozott a hírlevélre!');
        setNewsletterEmail('');
      }
    } catch (error: any) {
      console.error('Newsletter subscription error:', error);
      toast.error('Hiba történt a feliratkozás során');
    } finally {
      setSubscribing(false);
    }
  };

  const categories = [
    { id: 'all', label: 'Összes' },
    { id: 'Alapok', label: 'Alapok' },
    { id: 'Mérés', label: 'Mérés' },
    { id: 'Kultúra', label: 'Kultúra' },
    { id: 'Jövő', label: 'Jövő' }
  ];

  const filteredArticles = activeCategory === 'all' 
    ? articles 
    : articles.filter(article => article.category === activeCategory);

  return (
    <>
      {/* Hero Section - Newspaper Style Header */}
      <section className="relative py-4 md:py-8 overflow-hidden bg-gradient-to-b from-muted/50 to-background border-t border-border pt-20 md:pt-4">
        <div className="max-w-7xl mx-auto px-4 relative z-10">
          {/* Mobile: centered logo only */}
          <div className="flex md:hidden flex-col items-center gap-2">
            <div className="flex-shrink-0">
              <img 
                src={journalistLogo} 
                alt="The EAP Journalist!"
                className="h-16 object-contain"
              />
            </div>
            <button 
              onClick={() => window.open('/superadmin', '_blank')}
              className="text-xs text-black text-center hover:text-foreground/70 transition-colors cursor-pointer"
            >
              <span className="font-semibold">2025. OKTÓBER</span>
              <span className="mx-2">•</span>
              <span>1. Szám</span>
            </button>
          </div>

          {/* Desktop: Three column layout */}
          <div className="hidden md:flex items-center justify-between gap-8">
            {/* Left: Date and Issue */}
            <button 
              onClick={() => window.open('/superadmin', '_blank')}
              className="flex flex-col items-start text-sm text-black hover:text-foreground/70 transition-colors cursor-pointer"
            >
              <span className="font-semibold">2025. OKTÓBER</span>
              <span className="text-xs">1. Szám</span>
            </button>

            {/* Center: Logo */}
            <div className="flex-shrink-0">
              <img 
                src={journalistLogo} 
                alt="The EAP Journalist!" 
                className="h-24 object-contain"
              />
            </div>

            {/* Right: Magazine description */}
            <div className="flex items-end text-right">
              <button 
                onClick={() => navigate('/unsubscribe-success')}
                className="text-sm font-medium text-black uppercase tracking-wide hover:text-foreground/70 transition-colors cursor-pointer text-right"
              >
                A CGP Europe<br />EAP-Magazinja
              </button>
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
      <section className="py-12 px-4 bg-gradient-to-br from-foreground/5 via-background to-foreground/10">
        <div className="max-w-6xl mx-auto">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold">Kiemelt Cikkek</h2>
            <TrendingUp className="h-6 w-6 text-foreground" />
          </div>
          {loadingArticles ? (
            <div className="text-center py-12">Betöltés...</div>
          ) : featuredArticles.length === 0 ? (
            <div className="text-center py-12 text-muted-foreground">Nincsenek kiemelt cikkek</div>
          ) : (
            <Carousel className="w-full">
              <CarouselContent className="-ml-2 md:-ml-4">
                {featuredArticles.map((article) => (
                  <CarouselItem key={article.id} className="pl-2 md:pl-4">
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
                          <div className="w-full h-full bg-gradient-to-br from-foreground/20 to-foreground/5 flex items-center justify-center">
                            <span className="text-4xl font-bold text-foreground/30">?</span>
                          </div>
                        )}
                        <Badge variant="outline" className="absolute top-4 left-4 z-10 bg-foreground text-background border-foreground">{article.category}</Badge>
                      </div>
                      
                      {/* Content */}
                      <div className="space-y-4">
                        <h3 
                          className="text-4xl font-bold hover:text-foreground/70 transition-colors cursor-pointer leading-tight"
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
              <CarouselPrevious className="hidden md:flex -left-12" />
              <CarouselNext className="hidden md:flex -right-12" />
            </Carousel>
          )}
        </div>
      </section>

      <section className="py-12 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="grid lg:grid-cols-3 gap-8">
            {/* Main Articles */}
            <div className="lg:col-span-2 min-w-0">
              {/* Category Filter */}
              <div className="flex gap-3 mb-8 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
                {categories.map((cat) => (
                  <Button
                    key={cat.id}
                    variant={activeCategory === cat.id ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => setActiveCategory(cat.id)}
                    className={`whitespace-nowrap flex-shrink-0 ${activeCategory === cat.id ? 'bg-foreground text-background hover:bg-foreground/90' : 'bg-foreground/10 hover:bg-foreground/15 border-foreground/10'}`}
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
                      <Card className="hover:shadow-lg transition-shadow overflow-hidden aspect-video md:aspect-auto">
                        {article.image_url ? (
                          <img 
                            src={article.image_url} 
                            alt={article.title}
                            className="w-full h-full object-cover"
                          />
                        ) : (
                          <div className="h-full bg-gradient-to-br from-foreground/20 to-foreground/5 flex items-center justify-center">
                            <div className="text-4xl font-bold text-foreground/30">?</div>
                          </div>
                        )}
                      </Card>
                      <Card className="hover:shadow-lg transition-shadow">
                        <CardHeader>
                          <Badge variant="outline" className="w-fit mb-2 bg-foreground text-background border-foreground">{article.category}</Badge>
                          <CardTitle className="text-xl group-hover:text-foreground/70 transition-colors">
                            {article.title}
                          </CardTitle>
                          <CardDescription className="mt-2">{article.excerpt}</CardDescription>
                        </CardHeader>
                        <CardContent>
                          <div className="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                            <div className="flex items-center gap-1">
                              <User className="h-4 w-4" />
                              <span>{article.author}</span>
                            </div>
                            <span className="text-xs">{new Date(article.published_date).toLocaleDateString('hu-HU', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
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
            <div className="space-y-6 min-w-0">
              {/* Newsletter */}
              <Card className="bg-gradient-to-br from-[hsl(var(--magazine-red))] to-[hsl(var(--magazine-yellow))] text-white border-0">
                <CardHeader>
                  <CardTitle>Hírlevél Feliratkozás</CardTitle>
                  <CardDescription className="text-white/90">
                    Havi összefoglaló a CGP Europe legújabb újdonságairól, híreiről
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <form onSubmit={handleNewsletterSubscribe}>
                    <Input 
                      type="email" 
                      placeholder="Email cím"
                      value={newsletterEmail}
                      onChange={(e) => setNewsletterEmail(e.target.value)}
                      required
                      className="mb-3 bg-white/20 border-white/30 placeholder:text-white/70 text-white"
                    />
                    <Button 
                      type="submit" 
                      variant="secondary" 
                      className="w-full bg-white text-foreground hover:bg-white/90"
                      disabled={subscribing}
                    >
                      {subscribing ? 'Feliratkozás...' : 'Feliratkozom'}
                    </Button>
                  </form>
                </CardContent>
              </Card>

              {/* Popular Articles */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <TrendingUp className="h-5 w-5" />
                    Népszerű Cikkek
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {loadingArticles ? (
                    <div className="text-center py-4">Betöltés...</div>
                  ) : popularArticles.length === 0 ? (
                    <div className="text-center py-4 text-muted-foreground">Nincsenek cikkek</div>
                  ) : (
                    popularArticles.map((article, index) => (
                      <div 
                        key={article.id} 
                        className="flex gap-3 cursor-pointer group"
                        onClick={() => navigate(`/magazin/${article.slug}`)}
                      >
                        <span className="text-2xl font-bold text-muted-foreground/50">{index + 1}</span>
                        <div>
                          <h4 className="font-medium text-sm group-hover:text-foreground/70 transition-colors line-clamp-2">{article.title}</h4>
                          <span className="text-xs text-muted-foreground">{article.view_count || 0} olvasás</span>
                        </div>
                      </div>
                    ))
                  )}
                </CardContent>
              </Card>

              {/* Categories */}
              <Card>
                <CardHeader>
                  <CardTitle>Kategóriák</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="flex flex-wrap gap-2">
                    {categories.filter(c => c.id !== 'all').map((category) => (
                      <Badge 
                        key={category.id} 
                        variant="outline" 
                        className="cursor-pointer hover:bg-foreground hover:text-background transition-colors"
                        onClick={() => setActiveCategory(category.id)}
                      >
                        {category.label}
                      </Badge>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 border-t bg-muted/30">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-4 gap-8">
            <div>
              <h3 className="font-bold mb-4 cursor-pointer hover:text-primary transition-colors" onClick={() => {
                navigate('/');
                window.scrollTo({ top: 0, behavior: 'smooth' });
              }}>doyoueap</h3>
              <p className="text-sm text-muted-foreground">
                EAP Pulse platform a dolgozói jóllét méréséhez
              </p>
            </div>
            <div>
              <h4 className="font-medium mb-4">Platform</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                {/* HIDDEN: Bemutatkozás link */}
                {/* HIDDEN: Árak és Csomagok link */}
                <li>
                  <button onClick={() => navigate('/auth')} className="hover:text-foreground transition-colors">
                    Bejelentkezés
                  </button>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">Kategóriák</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                {categories.filter(c => c.id !== 'all').map((category) => (
                  <li key={category.id}>
                    <button 
                      onClick={() => setActiveCategory(category.id)} 
                      className="hover:text-foreground transition-colors"
                    >
                      {category.label}
                    </button>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">Kapcsolat</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li>
                  <a href="mailto:eap@cgpeu.com" className="hover:text-foreground transition-colors">
                    eap@cgpeu.com
                  </a>
                </li>
                <li>
                  <button onClick={() => window.location.href = 'mailto:eap@cgpeu.com'} className="hover:text-foreground transition-colors">
                    Kapcsolatfelvétel
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <div className="mt-8 pt-8 border-t text-center text-sm text-muted-foreground">
            © 2025 CGP Europe. Minden jog fenntartva.
          </div>
        </div>
      </footer>
    </>
  );
}
