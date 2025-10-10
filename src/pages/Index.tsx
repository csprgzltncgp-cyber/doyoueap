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
import { MobileNav } from '@/components/navigation/MobileNav';
import { MobileDashboardNav } from '@/components/navigation/MobileDashboardNav';
import logo from '@/assets/logo.png';
import dashboardPreview from '@/assets/dashboard-preview.jpg';
import Focus from './hr/Focus';
import EAPAudit from './hr/EAPAudit';
import Reports from './hr/Reports';
import Export from './hr/Export';
import CustomSurvey from './hr/CustomSurvey';
import Settings from './hr/Settings';
import Raffles from './hr/Raffles';

const Index = () => {
  const { user, role, loading, signIn, signOut } = useAuth();
  const navigate = useNavigate();
  const [searchParams, setSearchParams] = useSearchParams();
  const [showLoginDialog, setShowLoginDialog] = useState(false);
  const [showRegistration, setShowRegistration] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoggingIn, setIsLoggingIn] = useState(false);
  const [selectedPackage, setSelectedPackage] = useState<string | null>(null);
  const [openSubmenu, setOpenSubmenu] = useState<string | null>(null);

  const handleGetStarted = () => {
    if (user) {
      navigate("/?section=focus");
    } else {
      navigate("/auth");
    }
  };

  // Get current section and subsection from URL params
  const section = searchParams.get('section') || '';
  const subSection = searchParams.get('sub') || '';

  useEffect(() => {
    // Redirect admin users to their dedicated dashboard
    if (!loading && user && role === 'admin') {
      navigate('/admin');
    }
    
    // If on /dashboard route without section, redirect to focus
    if (!loading && user && role === 'hr' && window.location.pathname === '/dashboard' && !section) {
      navigate('/?section=focus');
    }
  }, [user, role, loading, navigate, section, setSearchParams]);

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
        return <Focus />;
      case 'focus':
        return <Focus />;
      case 'eap-pulse':
        switch (subSection) {
          case 'create-audit':
          case 'running-audits':
          case 'audit-questionnaire':
            return <EAPAudit />;
          case 'raffles':
            return <Raffles />;
          default:
            return <EAPAudit />;
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
    <div className="min-h-screen bg-background overflow-x-hidden">
      {/* Navigation */}
      <header className="border-b fixed md:sticky top-0 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 z-50 w-full">
        {/* Main Navigation */}
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center relative">
          <div className="flex items-center gap-2">
            <MobileNav 
              user={user}
              role={role}
              section={section}
              onNavigate={navigate}
              onLogout={handleLogout}
            />
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
              className="text-sm border border-transparent transition-colors px-3 py-2 rounded-md hover:bg-muted"
            >
              The EAP Journalist!
            </button>
            {user && role === 'hr' && (
              <button
                onClick={() => navigate('/?section=focus')}
                className={`text-sm border border-transparent transition-colors px-3 py-2 rounded-md ${
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
              <Button onClick={handleLogout} variant="outline" className="hidden md:flex">
                Kilépés
              </Button>
            ) : (
              <Button onClick={() => navigate('/auth')} className="hidden md:flex">
                Bejelentkezés
              </Button>
            )}
          </div>
        </div>

        {/* Dashboard Sub-Navigation */}
        {user && role === 'hr' && section && (
          <div className="border-t bg-gradient-to-r from-[#3572ef] to-[#3abef9]">
            <div className="max-w-7xl mx-auto px-4 py-3">
              <div className="flex items-center">
                <MobileDashboardNav 
                  section={section}
                  subSection={subSection}
                  onNavigate={(newSection, newSub) => {
                    if (newSub) {
                      setSearchParams({ section: newSection, sub: newSub });
                    } else {
                      setSearchParams({ section: newSection });
                    }
                  }}
                />
              </div>
              <nav className="hidden md:flex gap-6 justify-center">
                <button
                  onClick={() => {
                    setSearchParams({ section: 'focus' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                    section === 'focus' 
                      ? 'text-white font-semibold border-white' 
                      : 'text-white/80 border-transparent hover:text-white'
                  }`}
                >
                  <Target className="h-4 w-4" />
                  Focus
                </button>
                <div className="relative group">
                  <button
                    onClick={() => {
                      console.log('Clicked EAP Pulse navigation');
                      if (openSubmenu === 'eap-pulse') {
                        setOpenSubmenu(null);
                      } else {
                        setOpenSubmenu('eap-pulse');
                      }
                    }}
                    className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                      section === 'eap-pulse' 
                        ? 'text-white font-semibold border-white' 
                        : 'text-white/80 border-transparent hover:text-white'
                    }`}
                  >
                    <Activity className="h-4 w-4" />
                    EAP Pulse
                  </button>
                  {openSubmenu === 'eap-pulse' && (
                    <div className="absolute top-full left-0 mt-2 bg-white rounded-md shadow-lg py-2 min-w-[200px] z-50">
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'create-audit' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'create-audit' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Új Felmérés
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'running-audits' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'running-audits' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Futó Felmérések
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'audit-questionnaire' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'audit-questionnaire' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        EAP Pulse demo
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'raffles' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'raffles' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Sorsolások
                      </button>
                    </div>
                  )}
                </div>
                <div className="relative group">
                  <button
                    onClick={() => {
                      console.log('Clicked Riportok navigation');
                      if (openSubmenu === 'reports') {
                        setOpenSubmenu(null);
                      } else {
                        setOpenSubmenu('reports');
                      }
                    }}
                    className={`text-sm transition-colors flex items-center gap-2 pb-2 border-b-2 ${
                      section === 'reports' 
                        ? 'text-white font-semibold border-white' 
                        : 'text-white/80 border-transparent hover:text-white'
                    }`}
                  >
                    <BarChart3 className="h-4 w-4" />
                    Riportok
                  </button>
                  {openSubmenu === 'reports' && (
                    <div className="absolute top-full left-0 mt-2 bg-white rounded-md shadow-lg py-2 min-w-[200px] z-50">
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'overview' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          (!subSection || subSection === 'overview') ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Összefoglaló
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'awareness' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'awareness' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Ismertség
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'trust' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'trust' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Bizalom
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'usage' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'usage' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Használat
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'impact' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'impact' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Hatás
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'motivation' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'motivation' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Motiváció
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'demographics' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'demographics' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Demográfia
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'trends' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'trends' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Trendek
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'compare' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'compare' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Összehasonlítás
                      </button>
                    </div>
                  )}
                </div>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'export' });
                    setOpenSubmenu(null);
                  }}
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
                  onClick={() => {
                    setSearchParams({ section: 'custom-survey' });
                    setOpenSubmenu(null);
                  }}
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
                  onClick={() => {
                    setSearchParams({ section: 'settings' });
                    setOpenSubmenu(null);
                  }}
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
        <div className="max-w-7xl mx-auto px-4 py-6 pt-24 md:pt-6">
          {renderDashboardContent()}
        </div>
      ) : (
        <>
          {/* Hero Section */}

      {/* Hero Section */}
      <section id="home" className="py-20 bg-gradient-to-b from-muted/50 to-background pt-24 md:pt-20">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-block px-3 py-1 bg-gradient-to-r from-[#3abef9] to-[#ff66ff] text-white rounded-full text-sm font-medium mb-4">
                Új: EAP Pulse
              </div>
              <h1 className="text-5xl font-bold mb-6 leading-tight">
                Legyen teljesebb képe cége EAP programjáról - bővítse riportját akár <span style={{ color: '#3abef9' }}>további 60+ extra statisztikai adattal!</span>
              </h1>
              <p className="text-xl text-muted-foreground mb-8">
                Egészítse ki az EAP szolgáltatója riportjait a 4Score módszertannal. Szerezzen egyedi 
                visszajelzéseket dolgozóitól, gazdagítsa az adatokat, mutassa ki a program értékét 
                – évről évre. Mindez támogatja az EAP szolgáltatójával való együttműködést.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Button size="lg" variant="cta" onClick={() => navigate('/auth')}>
                  Kezdje el most <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
                <Button size="lg" variant="outline" onClick={() => navigate('/bemutatkozas')}>
                  Tudjon meg többet
                </Button>
              </div>
            </div>
            <div className="rounded-lg overflow-hidden shadow-lg max-w-full">
              <img 
                src={dashboardPreview} 
                alt="Dashboard előnézet - analitikai grafikonok és jelentések" 
                className="w-full h-auto object-contain"
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
                  Az EAP Pulse a munkavállalói jóllét barométere.
                </p>
                <p className="text-muted-foreground">
                  Egy digitális mérőeszköz, amely lehetővé teszi, hogy a HR és az EAP-szolgáltató rendszeresen, gyorsan és anonim módon visszajelzést kapjon a dolgozóktól az EAP-program működéséről.
                </p>
                <p className="text-muted-foreground">
                  A Pulse lényege a rövid, ismétlődő felmérés: néhány kérdés, ami segít megérteni, hogyan érzékelik a munkavállalók a programot, mennyire bíznak benne, és mennyire érzik hasznosnak. Az eredmények automatikusan frissülnek a dashboardon, így valós időben követhetők a trendek és a változások.
                </p>
                <p className="text-muted-foreground">
                  A cél nem egyszeri audit, hanem folyamatos párbeszéd a dolgozókkal – adatvezérelt, mégis emberi módon. A Pulse támogatja az EAP-szolgáltató és a HR-csapat közös munkáját: segít kimutatni a program hatását, megerősíteni a bizalmat, és időben észrevenni, ha valahol csökken a bevonódás.
                </p>
                <p className="text-muted-foreground">
                  Röviden: az EAP Pulse az EAP-program pulzusa – élő, dinamikus, mérhető.
                </p>
                <Button variant="outline" className="w-full mt-4" onClick={() => {
                  navigate('/bemutatkozas');
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                }}>
                  Részletek <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </CardContent>
            </Card>

            {/* Right: Recent Articles */}
            <div className="space-y-4">
              <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('/magazin/eap-tudasbazis-es-onsegelyseg')}>
                <CardHeader>
                  <div className="text-xs font-medium mb-1" style={{ color: 'rgb(115, 115, 115)' }}>Alapok</div>
                  <CardTitle className="text-lg">EAP tudásbázis és önsegélyesség</CardTitle>
                  <CardDescription>Hogyan működik az EAP tudásbázis és miért fontos az önsegélyesség támogatása a munkavállalók körében?</CardDescription>
                </CardHeader>
              </Card>
              <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('/magazin/a-jovo-eapje')}>
                <CardHeader>
                  <div className="text-xs font-medium mb-1" style={{ color: 'rgb(115, 115, 115)' }}>Jövő</div>
                  <CardTitle className="text-lg">A jövő EAP-je</CardTitle>
                  <CardDescription>Milyen újítások várhatók az EAP programokban? Mesterséges intelligencia, digitalizáció és a holisztikus megközelítés.</CardDescription>
                </CardHeader>
              </Card>
              <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('/magazin/az-eap-merese-es-a-4score-modszertan')}>
                <CardHeader>
                  <div className="text-xs font-medium mb-1" style={{ color: 'rgb(115, 115, 115)' }}>Mérés</div>
                  <CardTitle className="text-lg">Az EAP mérése és a 4Score módszertan</CardTitle>
                  <CardDescription>Ismertség, bizalom, használat és hatás – a négy dimenzió, amely átfogó képet ad az EAP program hatékonyságáról.</CardDescription>
                </CardHeader>
              </Card>
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
                  <h3 className="text-2xl font-bold mb-3">Ismertség</h3>
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
                  <h3 className="text-2xl font-bold mb-3">Bizalom</h3>
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
                  <h3 className="text-2xl font-bold mb-3">Használat</h3>
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
                  <h3 className="text-2xl font-bold mb-3">Hatás</h3>
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
              Válassza ki az igényeinek megfelelő csomagot a felmérések gyakorisága és a riportok mélysége szerint
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 items-stretch">
            {/* Starter */}
            <Card 
              className={`flex flex-col cursor-pointer transition-all ${
                selectedPackage === 'Starter' 
                  ? 'border-[#3572ef] shadow-xl scale-105' 
                  : 'hover:shadow-md'
              } animate-fade-in`}
              onClick={() => setSelectedPackage(selectedPackage === 'Starter' ? null : 'Starter')}
            >
              <CardHeader className="pb-6">
                <CardTitle className="text-2xl">Starter</CardTitle>
                <CardDescription>Alapvető betekintés az EAP-programba</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">
                    150 €<span className="text-lg font-normal text-muted-foreground">/hó</span>
                  </div>
                  <div className="text-sm text-muted-foreground mt-1">
                    1 800 € / év
                  </div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">2 felmérés / év (félévente)</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Ajándéksorsolás minden felmérés végén</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">4Score riport (Tudatosság · Használat · Bizalom & Hajlandóság · Hatás)</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Alap dashboard + letölthető riportok</p>
                  </div>
                </div>
                <Button 
                  className="w-full" 
                  variant="outline" 
                  onClick={(e) => {
                    e.stopPropagation();
                    handleGetStarted();
                  }}
                >
                  Kiválasztom
                </Button>
              </CardContent>
            </Card>

            {/* Professional */}
            <Card 
              className={`flex flex-col cursor-pointer transition-all ${
                selectedPackage === 'Professional' 
                  ? 'border-[#3572ef] shadow-xl scale-105' 
                  : selectedPackage === null
                    ? 'border-[#3572ef] shadow-lg' 
                    : 'hover:shadow-md'
              } animate-fade-in`}
              onClick={() => setSelectedPackage(selectedPackage === 'Professional' ? null : 'Professional')}
            >
              <CardHeader className="pb-6">
                {selectedPackage === null && (
                  <div className="text-xs font-medium mb-2" style={{ color: '#3572ef' }}>AJÁNLOTT</div>
                )}
                <CardTitle className="text-2xl">Professional</CardTitle>
                <CardDescription>Rendszeres és mélyreható elemzés</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">
                    325 €<span className="text-lg font-normal text-muted-foreground">/hó</span>
                  </div>
                  <div className="text-sm text-muted-foreground mt-1">
                    3 900 € / év
                  </div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">3 felmérés / év</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Ajándéksorsolás minden felmérés végén</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Teljes riportkészlet (minden statisztika + 4Score)</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Testreszabható kérdőív-design</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Trendösszevetések a felmérések között</p>
                  </div>
                </div>
                <Button 
                  className={`w-full ${selectedPackage === null ? 'bg-[#3572ef] hover:bg-[#3572ef]/90' : ''}`}
                  variant={selectedPackage === null ? 'default' : 'outline'}
                  onClick={(e) => {
                    e.stopPropagation();
                    handleGetStarted();
                  }}
                >
                  Kiválasztom
                </Button>
              </CardContent>
            </Card>

            {/* Enterprise */}
            <Card 
              className={`flex flex-col cursor-pointer transition-all ${
                selectedPackage === 'Enterprise' 
                  ? 'border-[#3572ef] shadow-xl scale-105' 
                  : 'hover:shadow-md'
              } animate-fade-in`}
              onClick={() => setSelectedPackage(selectedPackage === 'Enterprise' ? null : 'Enterprise')}
            >
              <CardHeader className="pb-6">
                <CardTitle className="text-2xl">Enterprise</CardTitle>
                <CardDescription>Maximális átláthatóság és integráció</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">
                    480 €<span className="text-lg font-normal text-muted-foreground">/hó</span>
                  </div>
                  <div className="text-sm text-muted-foreground mt-1">
                    5 760 € / év
                  </div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">4 felmérés / év</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Ajándéksorsolás minden felmérés végén</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">Teljes riportkészlet + trend- és összehasonlító elemzés</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">White-label lehetőség</p>
                  </div>
                  <div className="flex items-start gap-2">
                    <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                    <p className="font-medium text-sm">API integráció</p>
                  </div>
                </div>
                <Button 
                  className="w-full" 
                  variant="outline" 
                  onClick={(e) => {
                    e.stopPropagation();
                    window.location.href = 'mailto:info@doyoueap.com';
                  }}
                >
                  Kapcsolatfelvétel
                </Button>
              </CardContent>
            </Card>

            {/* EAP Provider / Partner Program */}
            <Card className="flex flex-col bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20 animate-fade-in">
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
                <Button 
                  className="w-full bg-[#050c9c] hover:bg-[#050c9c]/90" 
                  variant="default"
                  onClick={() => window.location.href = 'mailto:info@doyoueap.com'}
                >
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
                <li>
                  <button onClick={() => navigate('/bemutatkozas')} className="hover:text-foreground transition-colors">
                    Bemutatkozás
                  </button>
                </li>
                <li>
                  <button onClick={() => navigate('/arak')} className="hover:text-foreground transition-colors">
                    Árak és Csomagok
                  </button>
                </li>
                <li>
                  <button onClick={() => {
                    navigate('/');
                    setTimeout(() => {
                      const element = document.getElementById('4score');
                      element?.scrollIntoView({ behavior: 'smooth' });
                    }, 100);
                  }} className="hover:text-foreground transition-colors">
                    4Score Módszertan
                  </button>
                </li>
                <li>
                  <button onClick={() => navigate('/auth')} className="hover:text-foreground transition-colors">
                    Bejelentkezés
                  </button>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">The EAP Journalist!</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li>
                  <button onClick={() => navigate('/magazin')} className="hover:text-foreground transition-colors">
                    Magazin
                  </button>
                </li>
                <li>
                  <button onClick={() => {
                    navigate('/magazin');
                    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
                  }} className="hover:text-foreground transition-colors">
                    Alapok
                  </button>
                </li>
                <li>
                  <button onClick={() => {
                    navigate('/magazin');
                    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
                  }} className="hover:text-foreground transition-colors">
                    Hatékonyság
                  </button>
                </li>
                <li>
                  <button onClick={() => {
                    navigate('/magazin');
                    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
                  }} className="hover:text-foreground transition-colors">
                    Jövő
                  </button>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="font-medium mb-4">Kapcsolat</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li>
                  <a href="mailto:info@doyoueap.com" className="hover:text-foreground transition-colors">
                    info@doyoueap.com
                  </a>
                </li>
                <li>
                  <button onClick={() => window.location.href = 'mailto:info@doyoueap.com'} className="hover:text-foreground transition-colors">
                    Kapcsolatfelvétel
                  </button>
                </li>
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
