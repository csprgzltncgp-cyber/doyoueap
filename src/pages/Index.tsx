import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ArrowRight, CheckCircle, TrendingUp, Users, FileText, Award } from 'lucide-react';

const Index = () => {
  const { user, role, loading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!loading && user && role) {
      // Redirect authenticated users to their dashboard
      if (role === 'admin') {
        navigate('/admin');
      } else if (role === 'hr') {
        navigate('/hr');
      }
    }
  }, [user, role, loading, navigate]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p>Betöltés...</p>
      </div>
    );
  }

  const featuredArticles = [
    {
      id: 1,
      title: "EAP trendek 2025-ben",
      category: "Hírek",
      excerpt: "Milyen változások várhatók az EAP szolgáltatások piacán?",
      image: "/placeholder.svg"
    },
    {
      id: 2,
      title: "Interjú vezető HR szakértővel",
      category: "Interjú",
      excerpt: "Hogyan növelhető a dolgozói elkötelezettség az EAP programok iránt?",
      image: "/placeholder.svg"
    },
    {
      id: 3,
      title: "Statisztikák: EAP használat Magyarországon",
      category: "Elemzés",
      excerpt: "Friss adatok az EAP programok hazai használatáról és hatékonyságáról.",
      image: "/placeholder.svg"
    }
  ];

  const auditBenefits = [
    {
      icon: CheckCircle,
      title: "Hiteles visszajelzés",
      description: "Független forrásból származó, megbízható adatok"
    },
    {
      icon: TrendingUp,
      title: "Költséghatékonyság",
      description: "A befektetés valódi értéket teremt"
    },
    {
      icon: Users,
      title: "Fejlesztési irányok",
      description: "Konkrét fejlesztési pontok azonosítása"
    },
    {
      icon: FileText,
      title: "Stratégiai HR-eszköz",
      description: "Adatalapú döntéshozatal támogatása"
    }
  ];

  return (
    <div className="min-h-screen bg-background">
      {/* Navigation */}
      <header className="border-b sticky top-0 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 z-50">
        <div className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
          <div className="flex items-center gap-2">
            <img src="/src/assets/doyoueap-logo.png" alt="DoYouEAP" className="h-8" />
            <span className="font-bold text-xl">DoYouEAP</span>
          </div>
          <nav className="hidden md:flex gap-6">
            <a href="#home" className="text-sm hover:text-primary transition-colors">Főoldal</a>
            <a href="#magazin" className="text-sm hover:text-primary transition-colors">Magazin</a>
            <a href="#audit" className="text-sm hover:text-primary transition-colors">Mi az EAP Audit?</a>
            <a href="#elonyok" className="text-sm hover:text-primary transition-colors">Előnyök</a>
            <a href="#arak" className="text-sm hover:text-primary transition-colors">Árak</a>
          </nav>
          <Button onClick={() => navigate('/auth')}>
            Bejelentkezés
          </Button>
        </div>
      </header>

      {/* Hero Section */}
      <section id="home" className="py-20 px-4 bg-gradient-to-b from-muted/50 to-background">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium mb-4">
                Új: EAP Audit
              </div>
              <h1 className="text-5xl font-bold mb-6 leading-tight">
                Független visszajelzés az Ön EAP programjáról
              </h1>
              <p className="text-xl text-muted-foreground mb-8">
                Az EAP audit megmutatja, hogy a cégnél futó program valóban működik-e: mennyire ismert, 
                mennyire használt, mit gondolnak róla a dolgozók. Független, anonim, azonnal érthető riportokkal.
              </p>
              <div className="flex gap-4">
                <Button size="lg" onClick={() => navigate('/auth')}>
                  Indítsa el az auditot <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
                <Button size="lg" variant="outline">
                  Tudjon meg többet
                </Button>
              </div>
            </div>
            <div className="bg-muted rounded-lg p-8 aspect-video flex items-center justify-center">
              <p className="text-muted-foreground">Dashboard előnézet</p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Content - Magazine Style */}
      <section className="py-16 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-2 gap-8">
            {/* Left: What is EAP Audit */}
            <Card className="md:row-span-2">
              <CardHeader>
                <div className="text-sm font-medium text-primary mb-2">Kiemelt</div>
                <CardTitle className="text-3xl">Mi az EAP Audit?</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <p className="text-muted-foreground">
                  Az EAP Audit egy átfogó értékelési eszköz, amely négy fő pillér mentén méri az EAP program hatékonyságát:
                </p>
                <div className="space-y-3">
                  <div className="flex items-start gap-3">
                    <Award className="h-5 w-5 text-primary mt-1" />
                    <div>
                      <h4 className="font-medium">Awareness - Ismertség</h4>
                      <p className="text-sm text-muted-foreground">Mennyire ismerik a dolgozók a programot?</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3">
                    <CheckCircle className="h-5 w-5 text-primary mt-1" />
                    <div>
                      <h4 className="font-medium">Trust - Bizalom</h4>
                      <p className="text-sm text-muted-foreground">Megbíznak-e a dolgozók a szolgáltatásban?</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3">
                    <Users className="h-5 w-5 text-primary mt-1" />
                    <div>
                      <h4 className="font-medium">Usage - Használat</h4>
                      <p className="text-sm text-muted-foreground">Hányan veszik igénybe a szolgáltatást?</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3">
                    <TrendingUp className="h-5 w-5 text-primary mt-1" />
                    <div>
                      <h4 className="font-medium">Impact - Hatás</h4>
                      <p className="text-sm text-muted-foreground">Milyen eredményeket hoz a program?</p>
                    </div>
                  </div>
                </div>
                <Button variant="outline" className="w-full mt-4">
                  Részletek <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </CardContent>
            </Card>

            {/* Right: Recent Articles */}
            <div className="space-y-4">
              {featuredArticles.map((article) => (
                <Card key={article.id} className="hover:shadow-md transition-shadow cursor-pointer">
                  <CardHeader>
                    <div className="text-xs font-medium text-primary mb-1">{article.category}</div>
                    <CardTitle className="text-lg">{article.title}</CardTitle>
                    <CardDescription>{article.excerpt}</CardDescription>
                  </CardHeader>
                </Card>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Audit Benefits */}
      <section id="elonyok" className="py-16 px-4 bg-muted/30">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold mb-4">Miért jó egy cégnek az audit?</h2>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              Az EAP audit konkrét, mérhető visszajelzést ad a program hatékonyságáról
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {auditBenefits.map((benefit, index) => (
              <Card key={index} className="text-center">
                <CardHeader>
                  <div className="mx-auto mb-4 h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <benefit.icon className="h-6 w-6 text-primary" />
                  </div>
                  <CardTitle className="text-lg">{benefit.title}</CardTitle>
                  <CardDescription>{benefit.description}</CardDescription>
                </CardHeader>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Magazine Feed */}
      <section id="magazin" className="py-16 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-3xl font-bold">EAP Magazin</h2>
            <Button variant="outline">Összes cikk</Button>
          </div>
          
          <div className="flex gap-4 mb-8 overflow-x-auto pb-2">
            <Button variant="outline" size="sm">Összes</Button>
            <Button variant="ghost" size="sm">Hírek</Button>
            <Button variant="ghost" size="sm">Elemzések</Button>
            <Button variant="ghost" size="sm">Interjúk</Button>
            <Button variant="ghost" size="sm">Statisztikák</Button>
          </div>

          <div className="grid md:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map((item) => (
              <Card key={item} className="overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <div className="aspect-video bg-muted"></div>
                <CardHeader>
                  <div className="text-xs font-medium text-primary mb-2">Kategória</div>
                  <CardTitle className="text-lg">Cikk címe {item}</CardTitle>
                  <CardDescription>
                    Rövid leírás a cikkről, ami felkelti az olvasó érdeklődését...
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="flex justify-between items-center text-xs text-muted-foreground">
                    <span>2025. jan. {item}</span>
                    <span>5 perc olvasás</span>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 px-4 bg-primary text-primary-foreground">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-4xl font-bold mb-4">
            Indítsa el most az első EAP auditot
          </h2>
          <p className="text-xl mb-8 opacity-90">
            15 perc alatt beállítható, azonnali riportokkal
          </p>
          <Button size="lg" variant="secondary" onClick={() => navigate('/auth')}>
            Regisztráció
          </Button>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 px-4 border-t">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-4 gap-8">
            <div>
              <h3 className="font-bold mb-4">DoYouEAP</h3>
              <p className="text-sm text-muted-foreground">
                EAP audit platform a dolgozói jóllét méréséhez
              </p>
            </div>
            <div>
              <h4 className="font-medium mb-4">Platform</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><a href="#audit" className="hover:text-foreground">Mi az EAP Audit?</a></li>
                <li><a href="#elonyok" className="hover:text-foreground">Előnyök</a></li>
                <li><a href="#arak" className="hover:text-foreground">Árak</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">Magazin</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><a href="#magazin" className="hover:text-foreground">Hírek</a></li>
                <li><a href="#magazin" className="hover:text-foreground">Elemzések</a></li>
                <li><a href="#magazin" className="hover:text-foreground">Interjúk</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">Kapcsolat</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li>info@doyoueap.hu</li>
                <li>+36 1 234 5678</li>
              </ul>
            </div>
          </div>
          <div className="mt-8 pt-8 border-t text-center text-sm text-muted-foreground">
            © 2025 DoYouEAP. Minden jog fenntartva.
          </div>
        </div>
      </footer>
    </div>
  );
};

export default Index;
