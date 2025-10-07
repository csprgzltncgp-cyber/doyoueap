import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Calendar, Clock, User, Share2, ArrowLeft, TrendingUp, Heart } from 'lucide-react';
import logo from '@/assets/logo.png';
import journalistLogo from '@/assets/thejournalist_logo.png';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';

interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  author: string;
  published_date: string;
  image_url: string | null;
  category: string;
  is_featured: boolean;
  view_count: number;
}

const MagazinArticle = () => {
  const { slug } = useParams();
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();
  const [article, setArticle] = useState<Article | null>(null);
  const [loadingArticle, setLoadingArticle] = useState(true);
  const [relatedArticles, setRelatedArticles] = useState<Article[]>([]);

  useEffect(() => {
    if (slug) {
      fetchArticle();
    }
  }, [slug]);

  const fetchArticle = async () => {
    try {
      setLoadingArticle(true);
      
      // Fetch the article
      const { data: articleData, error: articleError } = await supabase
        .from('magazine_articles')
        .select('*')
        .eq('slug', slug)
        .eq('is_published', true)
        .single();

      if (articleError) throw articleError;

      if (articleData) {
        setArticle(articleData);
        
        // Increment view count using database function
        const { error: updateError } = await supabase.rpc('increment_article_view_count', {
          _article_id: articleData.id
        });

        if (updateError) {
          console.error('Error updating view count:', updateError);
        }

        // Fetch related articles from same category
        const { data: relatedData, error: relatedError } = await supabase
          .from('magazine_articles')
          .select('*')
          .eq('category', articleData.category)
          .eq('is_published', true)
          .neq('id', articleData.id)
          .order('published_date', { ascending: false })
          .limit(3);

        if (relatedError) throw relatedError;
        setRelatedArticles(relatedData || []);
      }
    } catch (error) {
      console.error('Error fetching article:', error);
      toast.error('Hiba történt a cikk betöltése közben');
      navigate('/magazin');
    } finally {
      setLoadingArticle(false);
    }
  };

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [slug]);

  const handleShare = () => {
    if (navigator.share && article) {
      navigator.share({
        title: article.title,
        text: article.excerpt,
        url: window.location.href,
      }).catch((error) => console.log('Error sharing:', error));
    } else {
      navigator.clipboard.writeText(window.location.href);
      toast.success('Link vágólapra másolva!');
    }
  };

  if (loading || loadingArticle) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <p>Betöltés...</p>
      </div>
    );
  }

  if (!article) {
    return (
      <div className="flex flex-col items-center justify-center min-h-screen gap-4">
        <p>Cikk nem található</p>
        <Button onClick={() => navigate('/magazin')}>Vissza a magazinhoz</Button>
      </div>
    );
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('hu-HU', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const estimatedReadTime = Math.max(1, Math.ceil(article.content.length / 1000));

  return (
    <div className="min-h-screen bg-background overflow-x-hidden">
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

      {/* The Journalist Header */}
      <section className="relative py-4 md:py-8 overflow-hidden bg-gradient-to-b from-muted/50 to-background border-t border-border">
        <div className="max-w-7xl mx-auto px-4 relative z-10">
          {/* Mobile: centered logo only */}
          <div className="flex md:hidden flex-col items-center gap-2">
            <div className="flex-shrink-0">
              <img 
                src={journalistLogo} 
                alt="The Journalist!" 
                className="h-16 object-contain"
              />
            </div>
            <div className="text-xs text-black text-center">
              <span className="font-semibold">2025. JANUÁR</span>
              <span className="mx-2">•</span>
              <span>16. Szám</span>
            </div>
          </div>

          {/* Desktop: Three column layout */}
          <div className="hidden md:flex items-center justify-between gap-8">
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
              <span className="text-sm font-medium text-black uppercase tracking-wide">
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

      {/* Article Content */}
      <article className="max-w-4xl mx-auto px-4 py-12">
        {/* Back Button */}
        <div className="mb-4">
          <Button
            variant="ghost"
            onClick={() => navigate('/magazin')}
            className="group -ml-4"
          >
            <ArrowLeft className="h-4 w-4 mr-2 transition-transform group-hover:-translate-x-1" />
            Vissza a cikkekhez
          </Button>
        </div>

        {/* Category Badge */}
        <div className="mb-4">
          <Badge variant="outline" className="text-sm">
            {article.category}
          </Badge>
        </div>

        {/* Article Title */}
        <h1 className="text-4xl md:text-5xl font-bold mb-6 leading-tight">
          {article.title}
        </h1>

        {/* Article Meta */}
        <div className="flex flex-wrap items-center gap-4 text-sm text-muted-foreground mb-8 pb-8 border-b">
          <div className="flex items-center gap-2">
            <User className="h-4 w-4" />
            <span>{article.author}</span>
          </div>
          <div className="flex items-center gap-2">
            <Calendar className="h-4 w-4" />
            <span>{formatDate(article.published_date)}</span>
          </div>
          <div className="flex items-center gap-2">
            <Clock className="h-4 w-4" />
            <span>{estimatedReadTime} perc olvasás</span>
          </div>
          <div className="flex items-center gap-2">
            <TrendingUp className="h-4 w-4" />
            <span>{article.view_count || 0} megtekintés</span>
          </div>
          <Button
            variant="ghost"
            size="sm"
            onClick={handleShare}
            className="ml-auto"
          >
            <Share2 className="h-4 w-4 mr-2" />
            Megosztás
          </Button>
        </div>

        {/* Featured Image */}
        {article.image_url && (
          <div className="mb-8 rounded-lg overflow-hidden max-h-[600px]">
            <img
              src={article.image_url}
              alt={article.title}
              className="w-full h-full object-cover max-h-[600px]"
            />
          </div>
        )}

        {/* Article Excerpt */}
        <div className="text-xl text-muted-foreground mb-8 leading-relaxed border-l-4 border-primary pl-6 italic">
          {article.excerpt}
        </div>

        {/* Article Content */}
        <div 
          className="prose prose-lg max-w-none"
          dangerouslySetInnerHTML={{ __html: article.content.replace(/\n/g, '<br />') }}
        />

      </article>

      {/* Related Articles */}
      {relatedArticles.length > 0 && (
        <section className="max-w-7xl mx-auto px-4 py-12 border-t">
          <h2 className="text-2xl font-bold mb-6">Kapcsolódó cikkek</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {relatedArticles.map((related) => (
              <Card
                key={related.id}
                className="cursor-pointer hover:shadow-lg transition-shadow"
                onClick={() => navigate(`/magazin/${related.slug}`)}
              >
                {related.image_url && (
                  <div className="aspect-video overflow-hidden">
                    <img
                      src={related.image_url}
                      alt={related.title}
                      className="w-full h-full object-cover hover:scale-105 transition-transform"
                    />
                  </div>
                )}
                <CardContent className="p-4">
                  <Badge variant="outline" className="mb-2">
                    {related.category}
                  </Badge>
                  <h3 className="font-semibold mb-2 line-clamp-2">
                    {related.title}
                  </h3>
                  <p className="text-sm text-muted-foreground line-clamp-2">
                    {related.excerpt}
                  </p>
                </CardContent>
              </Card>
            ))}
          </div>
        </section>
      )}
    </div>
  );
};

export default MagazinArticle;
