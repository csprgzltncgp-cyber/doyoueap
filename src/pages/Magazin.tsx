import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Clock, User } from 'lucide-react';
import logo from '@/assets/doyoueap-logo.png';
import { useAuth } from '@/hooks/useAuth';

const Magazin = () => {
  const navigate = useNavigate();
  const { user } = useAuth();

  const featuredArticle = {
    title: "A mentális egészség munkahelyi jelentősége 2025-ben",
    excerpt: "Az elmúlt évek tapasztalatai alapján a vállalatok egyre nagyobb figyelmet fordítanak munkavállalóik mentális jólétére. Nézzük meg, milyen trendek várhatóak idén.",
    author: "Dr. Kovács Anna",
    date: "2025. január 15.",
    readTime: "8 perc",
    category: "Trendek"
  };

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
      title: "Az EAP program ROI-ja: számok, amelyek beszélnek",
      excerpt: "Mennyire térül meg egy jól működő EAP program? Esettanulmányok és statisztikák a befektetés megtérüléséről.",
      author: "Szabó Katalin",
      date: "2025. január 10.",
      readTime: "6 perc",
      category: "Elemzések"
    },
    {
      title: "Stresszkezelési technikák a mindennapokban",
      excerpt: "Praktikus módszerek, amelyeket azonnal alkalmazhatunk a munkahelyi stressz csökkentésére.",
      author: "Tóth László",
      date: "2025. január 8.",
      readTime: "4 perc",
      category: "Gyakorlatok"
    },
    {
      title: "A hibrid munkavégzés kihívásai",
      excerpt: "Hogyan őrizhetjük meg a mentális egyensúlyt az otthoni és irodai munka között?",
      author: "Kiss Mónika",
      date: "2025. január 5.",
      readTime: "7 perc",
      category: "Munkakörnyezet"
    }
  ];

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
                className="text-sm text-primary font-medium transition-all duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-primary"
              >
                Magazin
              </button>
              <button
                onClick={() => navigate('/bemutatkozas')}
                className="text-sm transition-all duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-primary after:transition-all after:duration-300"
              >
                Bemutatkozás
              </button>
              <button
                onClick={() => navigate('/arak')}
                className="text-sm transition-all duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-primary after:transition-all after:duration-300"
              >
                Árak és Csomagok
              </button>
              {user && (
                <button
                  onClick={() => navigate('/')}
                  className="text-sm transition-all duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-primary after:transition-all after:duration-300"
                >
                  Főoldal
                </button>
              )}
            </nav>
          </div>
          <div className="flex items-center gap-4">
            {user ? (
              <Button onClick={() => navigate('/?section=eap-pulse')}>
                Dashboard
              </Button>
            ) : (
              <Button onClick={() => navigate('/auth')}>
                Bejelentkezés
              </Button>
            )}
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-12">
        <Button
          variant="ghost"
          onClick={() => navigate('/')}
          className="mb-6"
        >
          <ArrowLeft className="mr-2 h-4 w-4" />
          Vissza a főoldalra
        </Button>

        <h1 className="text-4xl font-bold mb-2">EAP Pulse Magazin</h1>
        <p className="text-muted-foreground mb-12">
          Cikkek, tanácsok és szakértői vélemények a munkahelyi mentális egészségről
        </p>

        {/* Featured Article */}
        <Card className="mb-12 overflow-hidden">
          <div className="md:flex">
            <div className="md:w-2/3 p-8">
              <Badge className="mb-4">{featuredArticle.category}</Badge>
              <CardHeader className="p-0 mb-4">
                <CardTitle className="text-3xl mb-2">{featuredArticle.title}</CardTitle>
                <CardDescription className="text-base">{featuredArticle.excerpt}</CardDescription>
              </CardHeader>
              <CardContent className="p-0">
                <div className="flex items-center gap-4 text-sm text-muted-foreground mb-4">
                  <div className="flex items-center gap-1">
                    <User className="h-4 w-4" />
                    <span>{featuredArticle.author}</span>
                  </div>
                  <div className="flex items-center gap-1">
                    <Clock className="h-4 w-4" />
                    <span>{featuredArticle.readTime}</span>
                  </div>
                  <span>{featuredArticle.date}</span>
                </div>
                <Button>Tovább olvasom</Button>
              </CardContent>
            </div>
            <div className="md:w-1/3 bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center p-8">
              <div className="text-center">
                <div className="text-6xl font-bold text-primary mb-2">★</div>
                <p className="text-sm text-muted-foreground">Kiemelt cikk</p>
              </div>
            </div>
          </div>
        </Card>

        {/* Articles Grid */}
        <h2 className="text-2xl font-bold mb-6">Ajánlott cikkek</h2>
        <div className="grid md:grid-cols-2 gap-6">
          {articles.map((article, index) => (
            <Card key={index} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <Badge className="w-fit mb-2">{article.category}</Badge>
                <CardTitle className="text-xl mb-2">{article.title}</CardTitle>
                <CardDescription>{article.excerpt}</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="flex items-center gap-4 text-sm text-muted-foreground mb-4">
                  <div className="flex items-center gap-1">
                    <User className="h-4 w-4" />
                    <span>{article.author}</span>
                  </div>
                  <div className="flex items-center gap-1">
                    <Clock className="h-4 w-4" />
                    <span>{article.readTime}</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-muted-foreground">{article.date}</span>
                  <Button variant="outline" size="sm">Tovább olvasom</Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </main>
    </div>
  );
};

export default Magazin;
