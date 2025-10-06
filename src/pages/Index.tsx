import { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowRight, CheckCircle, TrendingUp, Users, FileText, Award, BarChart3, Settings as SettingsIcon, Download, FileEdit, PlayCircle, ClipboardList, Eye, Shield, Activity, Target, Heart, UsersRound, LineChart, GitCompare } from 'lucide-react';
import { RegistrationWizard } from '@/components/registration/RegistrationWizard';
import logo from '@/assets/doyoueap-logo.png';
import HRDashboard from './HRDashboard';
import EAPAudit from './hr/EAPAudit';
import Reports from './hr/Reports';
import Export from './hr/Export';
import CustomSurvey from './hr/CustomSurvey';
import Settings from './hr/Settings';

const Index = () => {
  const { user, role, loading, signIn, signOut } = useAuth();
  const navigate = useNavigate();
  const [searchParams, setSearchParams] = useSearchParams();
  const [showLoginDialog, setShowLoginDialog] = useState(false);
  const [showRegistration, setShowRegistration] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoggingIn, setIsLoggingIn] = useState(false);

  // Get current section and subsection from URL params
  const section = searchParams.get('section') || '';
  const subSection = searchParams.get('sub') || '';

  useEffect(() => {
    // Redirect admin users to their dedicated dashboard
    if (!loading && user && role === 'admin') {
      navigate('/admin');
    }
    
    // Set default subsection for EAP Pulse
    if (user && role === 'hr' && section === 'eap-pulse' && !subSection) {
      setSearchParams({ section: 'eap-pulse', sub: 'create-audit' });
    }
  }, [user, role, loading, navigate, section, subSection, setSearchParams]);

  const handleSignIn = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoggingIn(true);
    await signIn(email, password);
    setIsLoggingIn(false);
    setShowLoginDialog(false);
  };

  const handleLogout = async () => {
    await signOut();
    setSearchParams({});
  };

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

  // Render dashboard content if user is logged in and has selected a dashboard section
  const renderDashboardContent = () => {
    if (!user || role !== 'hr') return null;

    switch (section) {
      case 'dashboard':
        return <HRDashboard />;
      case 'eap-pulse':
        switch (subSection) {
          case 'create-audit':
          case 'running-audits':
          case 'audit-questionnaire':
            return <EAPAudit />;
          default:
            return <HRDashboard />;
        }
      case 'reports':
        return <Reports />;
      case 'export':
        return <Export />;
      case 'custom-survey':
        return <CustomSurvey />;
      case 'settings':
        return <Settings />;
      default:
        return null;
    }
  };

  if (showRegistration) {
    return <RegistrationWizard />;
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Navigation */}
      <header className="border-b sticky top-0 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 z-50">
        {/* Main Navigation */}
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center">
          <div className="flex items-center gap-8">
            <img 
              src={logo} 
              alt="doyoueap" 
              className="h-8 cursor-pointer" 
              onClick={() => {
                setSearchParams({});
                window.scrollTo({ top: 0, behavior: 'smooth' });
              }} 
            />
            <nav className="hidden md:flex gap-6 items-center">
            <button
              onClick={() => navigate('/magazin')}
              className="text-sm border border-transparent transition-colors px-3 py-2 rounded-sm hover:bg-muted"
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
                onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'create-audit' })}
                className={`text-sm transition-colors px-3 py-2 rounded ${
                  section 
                    ? 'bg-primary text-primary-foreground font-semibold' 
                    : 'hover:bg-muted'
                }`}
              >
                Dashboard
              </button>
            )}
            </nav>
          </div>
          <div className="flex items-center gap-4">
            {user ? (
              <Button onClick={handleLogout} variant="outline">
                Kilépés
              </Button>
            ) : (
              <Button onClick={() => navigate('/auth')}>
                Bejelentkezés
              </Button>
            )}
          </div>
        </div>

        {/* Dashboard Sub-Navigation */}
        {user && role === 'hr' && section && (
          <div className="border-t bg-muted/10">
            <div className="max-w-7xl mx-auto px-4 py-3">
              <nav className="flex gap-6">
                <button
                  onClick={() => {
                    console.log('Clicked EAP Pulse navigation');
                    setSearchParams({ section: 'eap-pulse', sub: 'create-audit' });
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'eap-pulse' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <BarChart3 className="h-4 w-4" />
                  EAP Pulse
                </button>
                <button
                  onClick={() => {
                    console.log('Clicked Riportok navigation');
                    setSearchParams({ section: 'reports' });
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'reports' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <TrendingUp className="h-4 w-4" />
                  Riportok
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'export' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'export' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Download className="h-4 w-4" />
                  Export
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'custom-survey' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'custom-survey' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <FileEdit className="h-4 w-4" />
                  Egyedi Felmérés
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'settings' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'settings' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <SettingsIcon className="h-4 w-4" />
                  Beállítások
                </button>
              </nav>
            </div>
          </div>
        )}

        {/* EAP Pulse Sub-Sub-Navigation */}
        {user && role === 'hr' && section === 'eap-pulse' && (
          <div className="border-t bg-muted/20">
            <div className="max-w-7xl mx-auto px-4 py-2">
              <nav className="flex gap-6">
                <button
                  onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'create-audit' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'create-audit' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <PlayCircle className="h-4 w-4" />
                  Új Felmérés
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'running-audits' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'running-audits' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <ClipboardList className="h-4 w-4" />
                  Futó Felmérések
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'audit-questionnaire' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'audit-questionnaire' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Eye className="h-4 w-4" />
                  EAP Pulse demo
                </button>
              </nav>
            </div>
          </div>
        )}

        {/* Reports Sub-Sub-Navigation */}
        {user && role === 'hr' && section === 'reports' && (
          <div className="border-t bg-muted/20">
            <div className="max-w-7xl mx-auto px-4 py-2">
              <nav className="flex gap-6">
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'overview' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    (!subSection || subSection === 'overview') 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <BarChart3 className="h-4 w-4" />
                  Összefoglaló
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'awareness' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'awareness' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Eye className="h-4 w-4" />
                  Ismertség
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'trust' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'trust' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Shield className="h-4 w-4" />
                  Bizalom
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'usage' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'usage' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Activity className="h-4 w-4" />
                  Használat
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'impact' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'impact' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Target className="h-4 w-4" />
                  Hatás
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'motivation' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'motivation' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <Heart className="h-4 w-4" />
                  Motiváció
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'demographics' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'demographics' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <UsersRound className="h-4 w-4" />
                  Demográfia
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'trends' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'trends' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <LineChart className="h-4 w-4" />
                  Trendek
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'compare' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    subSection === 'compare' 
                      ? 'text-primary font-semibold border-primary' 
                      : 'text-muted-foreground border-transparent hover:text-foreground'
                  }`}
                >
                  <GitCompare className="h-4 w-4" />
                  Összehasonlítás
                </button>
              </nav>
            </div>
          </div>
        )}
      </header>

      {/* Login Dialog */}
      <Dialog open={showLoginDialog} onOpenChange={setShowLoginDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Bejelentkezés</DialogTitle>
            <DialogDescription>
              Jelentkezzen be fiókjába
            </DialogDescription>
          </DialogHeader>
          <form onSubmit={handleSignIn} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Jelszó</Label>
              <Input
                id="password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
            <Button type="submit" className="w-full" disabled={isLoggingIn}>
              {isLoggingIn ? 'Folyamatban...' : 'Bejelentkezés'}
            </Button>
          </form>
          
          <div className="mt-4 text-center">
            <p className="text-sm text-muted-foreground mb-2">
              Még nincs fiókja?
            </p>
            <Button 
              variant="outline" 
              className="w-full"
              onClick={() => {
                setShowLoginDialog(false);
                setShowRegistration(true);
              }}
            >
              Céges regisztráció
            </Button>
          </div>
        </DialogContent>
      </Dialog>

      {/* Dashboard Content or Landing Page */}
      {renderDashboardContent() ? (
        <div className="max-w-7xl mx-auto px-4 py-6">
          {renderDashboardContent()}
        </div>
      ) : (
        <>
          {/* Hero Section */}

      {/* Hero Section */}
      <section id="home" className="py-20 bg-gradient-to-b from-muted/50 to-background">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium mb-4">
                Új: EAP Pulse
              </div>
              <h1 className="text-5xl font-bold mb-6 leading-tight">
                Legyen teljesebb képe cége EAP programjáról - bővítse riportját akár további 48 extra statisztikai adattal!
              </h1>
              <p className="text-xl text-muted-foreground mb-8">
                Egészítse ki az EAP szolgáltatója riportjait a 4Score módszertannal. Szerezzen egyedi 
                visszajelzéseket dolgozóitól, gazdagítsa az adatokat, mutassa ki a program értékét 
                – évről évre. Mindez támogatja az EAP szolgáltatójával való együttműködést.
              </p>
              <div className="flex gap-4">
                <Button size="lg" onClick={() => navigate('/auth')}>
                  Kezdje el most <ArrowRight className="ml-2 h-4 w-4" />
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
      <section id="magazin" className="py-16">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-8">
            {/* Left: What is EAP Pulse */}
            <Card id="pulse" className="md:row-span-2">
              <CardHeader>
                <div className="text-sm font-medium text-primary mb-2">Kiemelt</div>
                <CardTitle className="text-3xl">Mi az EAP Pulse?</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <p className="text-muted-foreground">
                  Az EAP Pulse a 4Score módszertant használja, amely négy fő pillér mentén méri az EAP program hatékonyságát:
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

      {/* 4Score Section */}
      <section id="4score" className="py-20 bg-gradient-to-br from-primary/5 via-background to-primary/10">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-16">
            <div className="inline-block px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium mb-4">
              4Score Módszertan
            </div>
            <h2 className="text-4xl font-bold mb-4">Négy dimenzió, egy átfogó kép</h2>
            <p className="text-muted-foreground max-w-3xl mx-auto text-lg">
              Az EAP program hatékonyságát négy kulcsfontosságú dimenzió mentén mérjük, 
              hogy teljes körű képet kapjon a befektetés megtérüléséről
            </p>
          </div>
          <div className="grid md:grid-cols-2 gap-8">
            <div className="group relative p-8 bg-card border rounded-lg hover:shadow-lg transition-all">
              <div className="flex items-start gap-6">
                <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <Eye className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-2xl font-bold mb-3">Awareness</h3>
                  <p className="text-muted-foreground leading-relaxed">
                    Felmérjük, hogy a munkatársak mennyire ismerik az EAP programot, 
                    milyen csatornákon értesültek róla, és milyen szolgáltatásokról tudnak.
                  </p>
                </div>
              </div>
            </div>
            <div className="group relative p-8 bg-card border rounded-lg hover:shadow-lg transition-all">
              <div className="flex items-start gap-6">
                <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <Shield className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-2xl font-bold mb-3">Trust</h3>
                  <p className="text-muted-foreground leading-relaxed">
                    Mérjük a bizalmat és hajlandóságot: mennyire érzik biztonságosnak a dolgozók 
                    a szolgáltatást, és milyen tényezők befolyásolják a használatot.
                  </p>
                </div>
              </div>
            </div>
            <div className="group relative p-8 bg-card border rounded-lg hover:shadow-lg transition-all">
              <div className="flex items-start gap-6">
                <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <Activity className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-2xl font-bold mb-3">Usage</h3>
                  <p className="text-muted-foreground leading-relaxed">
                    Megismerjük a tényleges használatot: ki, mikor és miért veszi igénybe a programot, 
                    milyen motivációk húzódnak meg a háttérben.
                  </p>
                </div>
              </div>
            </div>
            <div className="group relative p-8 bg-card border rounded-lg hover:shadow-lg transition-all">
              <div className="flex items-start gap-6">
                <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <Target className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="text-2xl font-bold mb-3">Impact</h3>
                  <p className="text-muted-foreground leading-relaxed">
                    Kimutatjuk a hatást: hogyan értékelik a dolgozók a kapott segítséget, 
                    milyen változásokat tapasztaltak, és ajánlanák-e másoknak.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Assessment Benefits */}
      <section id="elonyok" className="py-16 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold mb-4">Vállalati előnyök</h2>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              A 4Score módszertan konkrét üzleti értéket teremt a vállalat számára
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

      {/* Pricing Section */}
      <section id="arak" className="py-16 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold mb-4">Árak és csomagok</h2>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              Válassza ki a cégének megfelelő csomagot
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 items-stretch">
            {/* Starter */}
            <Card className="flex flex-col">
              <CardHeader>
                <CardTitle className="text-2xl">Starter</CardTitle>
                <CardDescription>Kis cégeknek, 50-250 fő</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">149 €<span className="text-lg font-normal text-muted-foreground">/hó</span></div>
                  <div className="text-sm text-muted-foreground mt-1">vagy 1 490 €/év</div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">Évente max. 1 felmérés</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">4Score alapriportok</p>
                      <p className="text-sm text-muted-foreground">Awareness, Trust, Usage, Impact</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">PDF export</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Alap kinézet</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Email support</p>
                  </div>
                </div>
                <Button className="w-full" onClick={() => navigate('/auth')}>
                  Válassza ezt a csomagot
                </Button>
              </CardContent>
            </Card>

            {/* Pro */}
            <Card className="border-primary shadow-lg flex flex-col">
              <CardHeader>
                <div className="text-xs font-medium text-primary mb-2">NÉPSZERŰ</div>
                <CardTitle className="text-2xl">Pro</CardTitle>
                <CardDescription>Közepes cégeknek, 250-1000 fő</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">399 €<span className="text-lg font-normal text-muted-foreground">/hó</span></div>
                  <div className="text-sm text-muted-foreground mt-1">vagy 3 990 €/év</div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">Negyedévente felmérés</p>
                      <p className="text-sm text-muted-foreground">Ismétlés funkcióval</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">4Score bővített riportok</p>
                      <p className="text-sm text-muted-foreground">Demográfia, trendek, összehasonlítás</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">PDF + Excel export</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">Brandelhető User App</p>
                      <p className="text-sm text-muted-foreground">Logó + 4 szín módosítható</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Kommunikációs támogatás</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Email + chat support</p>
                  </div>
                </div>
                <Button className="w-full" onClick={() => navigate('/auth')}>
                  Válassza ezt a csomagot
                </Button>
              </CardContent>
            </Card>

            {/* Enterprise */}
            <Card className="flex flex-col">
              <CardHeader>
                <CardTitle className="text-2xl">Enterprise</CardTitle>
                <CardDescription>Nagyvállalatoknak, &gt;1000 fő</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">Egyedi ajánlat</div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Korlátlan felmérés</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">Teljes 4Score funkcionalitás</p>
                      <p className="text-sm text-muted-foreground">KPI-k, trendek, benchmark, motivációk</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">PDF, Excel, PowerPoint export</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">Teljes brandelés</p>
                      <p className="text-sm text-muted-foreground">Saját domain opció</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">SSO integráció</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Dedikált account manager</p>
                  </div>
                </div>
                <Button className="w-full" variant="outline">
                  Vegye fel velünk a kapcsolatot
                </Button>
              </CardContent>
            </Card>

            {/* EAP Provider */}
            <Card className="flex flex-col bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20">
              <CardHeader>
                <div className="text-xs font-medium text-primary mb-2">SZOLGÁLTATÓKNAK</div>
                <CardTitle className="text-2xl">Partner Program</CardTitle>
                <CardDescription>Emeld új szintre az ügyfélriportjaidat</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">Egyedi ajánlat</div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-3 mb-4">
                  <p className="text-sm font-medium mb-3">
                    Bővítsd szolgáltatásportfóliód olyan riportadatokkal, amelyek egyedivé teszik ajánlatodat.
                  </p>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">REST API integráció</p>
                      <p className="text-sm text-muted-foreground">Zökkenőmentes adatkapcsolat rendszereiddel</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">White-label megoldás</p>
                      <p className="text-sm text-muted-foreground">Saját arculatoddal, a te nevedben</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <div>
                      <p className="font-medium">Partner központ</p>
                      <p className="text-sm text-muted-foreground">Összes ügyfeled átlátható menedzselése</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Dedikált technikai támogatás</p>
                  </div>
                </div>
                <Button className="w-full" variant="default">
                  Kérj egyedi ajánlatot
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>


      {/* CTA Section */}
      <section className="py-20 bg-primary text-primary-foreground">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-4">
            Indítsa el most az első EAP Pulse felmérést
          </h2>
          <p className="text-xl mb-8 opacity-90">
            15 perc alatt beállítható, azonnali 4Score riportokkal
          </p>
          <Button size="lg" variant="secondary" onClick={() => navigate('/auth')}>
            Regisztráció
          </Button>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 border-t">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-4 gap-8">
            <div>
              <h3 className="font-bold mb-4">doyoueap</h3>
              <p className="text-sm text-muted-foreground">
                EAP Pulse platform a dolgozói jóllét méréséhez
              </p>
            </div>
            <div>
              <h4 className="font-medium mb-4">Platform</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><a href="#pulse" className="hover:text-foreground">Mi az EAP Pulse?</a></li>
                <li><a href="#elonyok" className="hover:text-foreground">Előnyök</a></li>
                <li><a href="#arak" className="hover:text-foreground">Árak</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">The Journalis</h4>
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
            © 2025 doyoueap. Minden jog fenntartva.
          </div>
        </div>
      </footer>
        </>
      )}
    </div>
  );
};

export default Index;
