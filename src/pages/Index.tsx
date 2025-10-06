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
import logo from '@/assets/logo.png';
import dashboardPreview from '@/assets/dashboard-preview.jpg';
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
  }, [user, role, loading, navigate]);

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

    // Show welcome screen when no section is selected
    if (!section) {
      const userName = user.user_metadata?.full_name || user.email?.split('@')[0] || 'Felhasználó';
      return (
        <div className="max-w-7xl mx-auto px-4 py-16">
          <Card className="border-none shadow-lg">
            <CardHeader className="text-center pb-8">
              <CardTitle className="text-4xl font-bold mb-4">
                Szia {userName}!
              </CardTitle>
              <CardDescription className="text-xl">
                Üdv újra az EAP Pulse Dashboard-on!
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-8">
              <p className="text-center text-lg text-muted-foreground">
                Válassz egy menüpontot a fenti menüsorból a kezdéshez.
              </p>
              <div className="grid md:grid-cols-3 gap-6 mt-8">
                <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'create-audit' })}>
                  <CardHeader>
                    <div className="flex items-center gap-3 mb-2">
                      <BarChart3 className="h-6 w-6 text-primary" />
                      <CardTitle>EAP Pulse</CardTitle>
                    </div>
                    <CardDescription>
                      Új felmérések létrehozása és futó auditek kezelése
                    </CardDescription>
                  </CardHeader>
                </Card>
                <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => setSearchParams({ section: 'reports', sub: 'overview' })}>
                  <CardHeader>
                    <div className="flex items-center gap-3 mb-2">
                      <TrendingUp className="h-6 w-6 text-primary" />
                      <CardTitle>Riportok</CardTitle>
                    </div>
                    <CardDescription>
                      Részletes elemzések és statisztikák megtekintése
                    </CardDescription>
                  </CardHeader>
                </Card>
                <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => setSearchParams({ section: 'settings' })}>
                  <CardHeader>
                    <div className="flex items-center gap-3 mb-2">
                      <SettingsIcon className="h-6 w-6 text-primary" />
                      <CardTitle>Beállítások</CardTitle>
                    </div>
                    <CardDescription>
                      Profil és rendszer beállítások kezelése
                    </CardDescription>
                  </CardHeader>
                </Card>
              </div>
            </CardContent>
          </Card>
        </div>
      );
    }

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
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center relative">
          <div className="flex items-center">
            <img 
              src={logo} 
              alt="doyoueap" 
              className="h-8 cursor-pointer" 
              onClick={() => {
                setSearchParams({});
                window.scrollTo({ top: 0, behavior: 'smooth' });
              }} 
            />
          </div>
          <nav className="hidden md:flex gap-6 items-center absolute left-1/2 -translate-x-1/2">
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
            <button
              onClick={() => navigate('/magazin')}
              className="text-sm border border-transparent transition-colors px-3 py-2 rounded-sm hover:bg-muted"
            >
              The Journalist!
            </button>
            {user && role === 'hr' && (
              <button
                onClick={() => setSearchParams({})}
                className={`text-sm transition-colors px-3 py-2 rounded-sm ${
                  section 
                    ? 'bg-[#3572ef] text-white font-semibold' 
                    : 'hover:bg-muted'
                }`}
              >
                Dashboard
              </button>
            )}
          </nav>
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
          <div className="border-t bg-gradient-to-r from-[#3572ef] to-[#3abef9]">
            <div className="max-w-7xl mx-auto px-4 py-3">
              <nav className="flex gap-6 justify-center">
                <button
                  onClick={() => {
                    console.log('Clicked EAP Pulse navigation');
                    setSearchParams({ section: 'eap-pulse', sub: 'create-audit' });
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'eap-pulse' 
                      ? 'text-white font-semibold border-white' 
                      : 'text-white/80 border-transparent hover:text-white'
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
                      ? 'text-white font-semibold border-white' 
                      : 'text-white/80 border-transparent hover:text-white'
                  }`}
                >
                  <TrendingUp className="h-4 w-4" />
                  Riportok
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'export' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'export' 
                      ? 'text-white font-semibold border-white' 
                      : 'text-white/80 border-transparent hover:text-white'
                  }`}
                >
                  <Download className="h-4 w-4" />
                  Export
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'custom-survey' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'custom-survey' 
                      ? 'text-white font-semibold border-white' 
                      : 'text-white/80 border-transparent hover:text-white'
                  }`}
                >
                  <FileEdit className="h-4 w-4" />
                  Egyedi Felmérés
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'settings' })}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'settings' 
                      ? 'text-white font-semibold border-white' 
                      : 'text-white/80 border-transparent hover:text-white'
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
              <nav className="flex gap-6 justify-center">
                <button
                  onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'create-audit' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'create-audit' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Új Felmérés
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'running-audits' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'running-audits' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Futó Felmérések
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'eap-pulse', sub: 'audit-questionnaire' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'audit-questionnaire' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
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
              <nav className="flex gap-6 justify-center">
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'overview' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    (!subSection || subSection === 'overview') 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Összefoglaló
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'awareness' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'awareness' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Ismertség
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'trust' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'trust' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Bizalom
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'usage' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'usage' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Használat
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'impact' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'impact' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Hatás
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'motivation' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'motivation' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Motiváció
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'demographics' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'demographics' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Demográfia
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'trends' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'trends' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
                  Trendek
                </button>
                <button
                  onClick={() => setSearchParams({ section: 'reports', sub: 'compare' })}
                  className={`text-sm transition-colors pb-2 border-b-2 ${
                    subSection === 'compare' 
                      ? 'text-[#3572ef] font-semibold border-[#3572ef]' 
                      : 'text-[#3572ef]/60 border-transparent hover:text-[#3572ef]'
                  }`}
                >
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
              <div className="inline-block px-3 py-1 bg-gradient-to-r from-[#3abef9] to-[#ff66ff] text-white rounded-full text-sm font-medium mb-4">
                Új: EAP Pulse
              </div>
              <h1 className="text-5xl font-bold mb-6 leading-tight">
                Legyen teljesebb képe cége EAP programjáról - bővítse riportját akár <span style={{ color: '#3abef9' }}>további 48 extra statisztikai adattal!</span>
              </h1>
              <p className="text-xl text-muted-foreground mb-8">
                Egészítse ki az EAP szolgáltatója riportjait a 4Score módszertannal. Szerezzen egyedi 
                visszajelzéseket dolgozóitól, gazdagítsa az adatokat, mutassa ki a program értékét 
                – évről évre. Mindez támogatja az EAP szolgáltatójával való együttműködést.
              </p>
              <div className="flex gap-4">
                <Button size="lg" variant="cta" onClick={() => navigate('/auth')}>
                  Kezdje el most <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
                <Button size="lg" variant="outline">
                  Tudjon meg többet
                </Button>
              </div>
            </div>
            <div className="rounded-lg overflow-hidden shadow-lg">
              <img 
                src={dashboardPreview} 
                alt="Dashboard előnézet - analitikai grafikonok és jelentések" 
                className="w-full h-full object-cover"
              />
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
                <div className="text-sm font-medium mb-2" style={{ color: 'rgb(115, 115, 115)' }}>Kiemelt</div>
                <CardTitle className="text-3xl animate-pulse-glow">Mi az EAP Pulse?</CardTitle>
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
                    <div className="text-xs font-medium mb-1" style={{ color: 'rgb(115, 115, 115)' }}>{article.category}</div>
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
      <section id="4score" className="py-20 bg-gradient-to-br from-[#3abef9]/20 to-[#ff66ff]/20">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-16">
            <div className="inline-block px-4 py-1 bg-gradient-to-r from-[#3abef9] to-[#ff66ff] text-white rounded-full text-sm font-medium mb-4">
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
                <CardDescription>Kezdő csomagunk kisebb szervezeteknek</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">290.000 Ft<span className="text-lg font-normal text-muted-foreground">/év</span></div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Legfeljebb 200 munkavállaló</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Éves EAP audit</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">4Score módszertan</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Alapvető riportok</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Email támogatás</p>
                  </div>
                </div>
                <Button className="w-full" variant="outline" onClick={() => navigate('/auth')}>
                  Kezdjük el
                </Button>
              </CardContent>
            </Card>

            {/* Professional */}
            <Card className="shadow-lg flex flex-col" style={{ borderColor: '#3572ef', borderWidth: '2px' }}>
              <CardHeader>
                <div className="text-xs font-medium mb-2" style={{ color: '#3572ef' }}>AJÁNLOTT</div>
                <CardTitle className="text-2xl">Professional</CardTitle>
                <CardDescription>A legnépszerűbb választás</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">490.000 Ft<span className="text-lg font-normal text-muted-foreground">/év</span></div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Legfeljebb 500 munkavállaló</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Féléves EAP auditok</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">4Score módszertan</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Részletes riportok és elemzések</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Trend-elemzés</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Prioritásos támogatás</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Testre szabható kérdőívek</p>
                  </div>
                </div>
                <Button className="w-full" variant="cta" onClick={() => navigate('/auth')}>
                  Kezdjük el
                </Button>
              </CardContent>
            </Card>

            {/* Enterprise */}
            <Card className="flex flex-col">
              <CardHeader>
                <CardTitle className="text-2xl">Enterprise</CardTitle>
                <CardDescription>Nagy szervezetek számára</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">Egyedi árazás</div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Korlátlan munkavállaló</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Negyedéves EAP auditok</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">4Score módszertan</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Teljes körű elemzések</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Prediktív modellek</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Dedikált ügyfélmenedzser</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">API integráció</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">White-label lehetőség</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                    <p className="font-medium">Oktatás és tanácsadás</p>
                  </div>
                </div>
                <Button className="w-full" variant="outline" onClick={() => navigate('/auth')}>
                  Kapcsolatfelvétel
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
                <Button className="w-full" variant="cta" style={{ backgroundColor: '#050c9c' }}>
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
