import { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { usePackage } from '@/hooks/usePackage';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowRight, CheckCircle, TrendingUp, Users, FileText, Award, BarChart3, Settings as SettingsIcon, Download, FileEdit, PlayCircle, ClipboardList, Eye, Shield, Activity, Target, Heart, UsersRound, LineChart, GitCompare, Code, Building2, Map, Calendar, AlertTriangle, Gift, ThumbsUp, PieChart, Upload } from 'lucide-react';
import { RegistrationWizard } from '@/components/registration/RegistrationWizard';
import { MobileNav } from '@/components/navigation/MobileNav';
import { MobileDashboardNav } from '@/components/navigation/MobileDashboardNav';
import logo from '@/assets/doyoueap-logo-header.png';
import { MagazinContent } from '@/components/MagazinContent';
import dashboardPreview from '@/assets/dashboard-preview.jpg';
import Focus from './hr/Focus';
import EAPAudit from './hr/EAPAudit';
import Reports from './hr/Reports';
import ProgramReports from './hr/ProgramReports';
import Export from './hr/Export';
import HealthMap from './hr/HealthMap';
import Workshops from './hr/Workshops';
import CrisisInterventions from './hr/CrisisInterventions';
import Lottery from './hr/Lottery';
import SatisfactionIndex from './hr/SatisfactionIndex';
import ProgramUsage from './hr/ProgramUsage';
import DataSubmission from './hr/DataSubmission';

import Settings from './hr/Settings';
import Raffles from './hr/Raffles';
import API from './hr/API';
import PartnerCenter from './hr/PartnerCenter';
import { supabase } from '@/integrations/supabase/client';

const Index = () => {
  const { user, role, loading, signIn, signOut } = useAuth();
  const { packageType, loading: packageLoading } = usePackage();
  const navigate = useNavigate();
  const [searchParams, setSearchParams] = useSearchParams();
  const [showLoginDialog, setShowLoginDialog] = useState(false);
  const [showRegistration, setShowRegistration] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoggingIn, setIsLoggingIn] = useState(false);
  const [selectedPackage, setSelectedPackage] = useState<string | null>(null);
  const [openSubmenu, setOpenSubmenu] = useState<string | null>(null);
  const [hasAudits, setHasAudits] = useState(true);

  const handleGetStarted = () => {
    if (user) {
      navigate("/dashboard?section=focus");
    } else {
      navigate("/auth");
    }
  };

  // Get current section and subsection from URL params
  const section = searchParams.get('section') || '';
  const subSection = searchParams.get('sub') || '';

  useEffect(() => {
    // If on /dashboard route without section, redirect to focus
    if (!loading && user && role === 'hr' && window.location.pathname === '/dashboard' && !section) {
      navigate('/dashboard?section=focus');
    }
  }, [user, role, loading, navigate, section, setSearchParams]);

  // Check if user has any audits
  useEffect(() => {
    const checkAudits = async () => {
      if (!user || role !== 'hr') return;
      
      try {
        const { data, error } = await supabase
          .from('audits')
          .select('id')
          .eq('is_active', true)
          .limit(1);
        
        if (error) throw error;
        setHasAudits(data && data.length > 0);
      } catch (error) {
        console.error('Error checking audits:', error);
        setHasAudits(true); // Default to true on error to avoid blocking
      }
    };

    checkAudits();
  }, [user, role]);

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
          case 'step-by-step':
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
        // Check if it's EAP Pulse reports (existing) or Program reports (new)
        if (subSection === 'program') {
          return <ProgramReports />;
        }
        return <Reports />;
      case 'export':
        return <Export />;
      case 'health-map':
        return <HealthMap />;
      case 'workshops':
        return <Workshops />;
      case 'crisis':
        return <CrisisInterventions />;
      case 'lottery':
        return <Lottery />;
      case 'satisfaction':
        return <SatisfactionIndex />;
      case 'program-usage':
        return <ProgramUsage />;
      case 'data-submission':
        return <DataSubmission />;
      case 'api':
        // Only Enterprise and Partner can access API
        if (packageType === 'enterprise' || packageType === 'partner') {
          return <API />;
        }
        return null;
      case 'partner-center':
        // Only Partner can access Partner Center
        if (packageType === 'partner') {
          return <PartnerCenter />;
        }
        return null;
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
      <header
        className={`${user && role === 'hr' && section ? '' : 'border-b'} fixed md:sticky top-0 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 z-50 w-full`}
      >
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
            {/* HIDDEN: Bemutatkozás menu item */}
            {/* HIDDEN: Árak és Csomagok menu item */}
            {/* HIDDEN: Magazin menu item */}
          </nav>
          <div className="flex items-center gap-4">
            {user && role === 'hr' && (
              <button
                onClick={() => navigate('/dashboard?section=focus')}
                className={`text-sm border border-transparent transition-colors px-3 py-2 rounded-md hidden md:flex ${
                  section 
                    ? 'bg-[#04565f] text-white font-semibold' 
                    : 'bg-[#04565f] text-white hover:bg-[#04565f]/90'
                }`}
              >
                Dashboard
              </button>
            )}
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
          <div className="border-t-2 border-border bg-gradient-to-r from-muted/50 to-background">
            <div className="max-w-7xl mx-auto px-4 py-2">
              <div className="flex items-center md:hidden">
                <MobileDashboardNav 
                  section={section}
                  subSection={subSection}
                  hasAudits={hasAudits}
                  onNavigate={(newSection, newSub) => {
                    if (newSub) {
                      setSearchParams({ section: newSection, sub: newSub });
                    } else {
                      setSearchParams({ section: newSection });
                    }
                  }}
                />
              </div>
              
              {/* First row - Main navigation */}
              <nav className="hidden md:flex gap-6 justify-center pb-2">
                <button
                  onClick={() => {
                    setSearchParams({ section: 'focus' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 ${
                    section === 'focus' 
                      ? 'text-foreground font-semibold' 
                      : 'text-muted-foreground hover:text-foreground'
                  }`}
                >
                  <Target className="h-4 w-4" />
                  Focus
                </button>
                <div className="relative group">
                  <button
                    onClick={() => {
                      if (openSubmenu === 'eap-pulse') {
                        setOpenSubmenu(null);
                      } else {
                        setOpenSubmenu('eap-pulse');
                      }
                    }}
                    className={`text-sm transition-colors flex items-center gap-2 pb-2 ${
                      section === 'eap-pulse' 
                        ? 'text-foreground font-semibold' 
                        : 'text-muted-foreground hover:text-foreground'
                    }`}
                  >
                    <Activity className="h-4 w-4" />
                    EAP Pulse
                  </button>
                  {openSubmenu === 'eap-pulse' && (
                    <div className="absolute top-full left-0 mt-2 bg-white rounded-md shadow-lg py-2 min-w-[200px] z-50">
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'step-by-step' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'step-by-step' ? 'font-medium' : ''
                        }`}
                      >
                        Lépésről lépésre
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'communication-support' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'communication-support' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Kommunikációs támogatás
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
                        Futó/Lezárt felmérések
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
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'eap-pulse', sub: 'gifts' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'gifts' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Ajándékok
                      </button>
                    </div>
                  )}
                </div>
                <div className="relative group">
                  <button
                    onClick={() => {
                      if (openSubmenu === 'reports') {
                        setOpenSubmenu(null);
                      } else {
                        setOpenSubmenu('reports');
                      }
                    }}
                    className={`text-sm transition-colors flex items-center gap-2 pb-2 ${
                      section === 'reports' 
                        ? 'text-foreground font-semibold' 
                        : 'text-muted-foreground hover:text-foreground'
                    }`}
                  >
                    <BarChart3 className="h-4 w-4" />
                    Riportok
                  </button>
                  {openSubmenu === 'reports' && (
                    <div className="absolute top-full left-0 mt-2 bg-white rounded-md shadow-lg py-2 min-w-[220px] z-50">
                      <div className="px-4 py-1.5 text-xs font-semibold text-muted-foreground uppercase tracking-wider border-b border-gray-100 mb-1">
                        Program riportok
                      </div>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'program' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'program' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Negyedéves riport
                      </button>
                      <div className="px-4 py-1.5 text-xs font-semibold text-muted-foreground uppercase tracking-wider border-b border-t border-gray-100 mt-2 mb-1">
                        EAP Pulse riportok
                      </div>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'overview' });
                          setOpenSubmenu(null);
                        }}
                        className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                          subSection === 'overview' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Összefoglaló
                      </button>
                      <button
                        onClick={() => {
                          setSearchParams({ section: 'reports', sub: 'awareness' });
                          setOpenSubmenu(null);
                        }}
                        disabled={!hasAudits}
                        className={`w-full text-left px-4 py-2 text-sm ${
                          hasAudits 
                            ? 'hover:bg-gray-100 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
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
                        disabled={!hasAudits}
                        className={`w-full text-left px-4 py-2 text-sm ${
                          hasAudits 
                            ? 'hover:bg-gray-100 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
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
                        disabled={!hasAudits}
                        className={`w-full text-left px-4 py-2 text-sm ${
                          hasAudits 
                            ? 'hover:bg-gray-100 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
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
                        disabled={!hasAudits}
                        className={`w-full text-left px-4 py-2 text-sm ${
                          hasAudits 
                            ? 'hover:bg-gray-100 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
                          subSection === 'impact' ? 'bg-gray-100 font-medium' : ''
                        }`}
                      >
                        Hatás
                      </button>
                      {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                        <>
                          <button
                            onClick={() => {
                              setSearchParams({ section: 'reports', sub: 'preferences' });
                              setOpenSubmenu(null);
                            }}
                            disabled={!hasAudits}
                            className={`w-full text-left px-4 py-2 text-sm ${
                              hasAudits 
                                ? 'hover:bg-gray-100 cursor-pointer' 
                                : 'opacity-40 cursor-not-allowed'
                            } ${
                              subSection === 'preferences' ? 'bg-gray-100 font-medium' : ''
                            }`}
                          >
                            Preferenciák
                          </button>
                          <button
                            onClick={() => {
                              setSearchParams({ section: 'reports', sub: 'demographics' });
                              setOpenSubmenu(null);
                            }}
                            disabled={!hasAudits}
                            className={`w-full text-left px-4 py-2 text-sm ${
                              hasAudits 
                                ? 'hover:bg-gray-100 cursor-pointer' 
                                : 'opacity-40 cursor-not-allowed'
                            } ${
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
                            disabled={!hasAudits}
                            className={`w-full text-left px-4 py-2 text-sm ${
                              hasAudits 
                                ? 'hover:bg-gray-100 cursor-pointer' 
                                : 'opacity-40 cursor-not-allowed'
                            } ${
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
                            disabled={!hasAudits}
                            className={`w-full text-left px-4 py-2 text-sm ${
                              hasAudits 
                                ? 'hover:bg-gray-100 cursor-pointer' 
                                : 'opacity-40 cursor-not-allowed'
                            } ${
                              subSection === 'compare' ? 'bg-gray-100 font-medium' : ''
                            }`}
                          >
                            Összehasonlítás
                          </button>
                        </>
                      )}
                    </div>
                  )}
                </div>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'export' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 ${
                    section === 'export' 
                      ? 'text-foreground font-semibold' 
                      : 'text-muted-foreground hover:text-foreground'
                  }`}
                >
                  <Download className="h-4 w-4" />
                  Export
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'settings' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-sm transition-colors flex items-center gap-2 pb-2 ${
                    section === 'settings' 
                      ? 'text-foreground font-semibold' 
                      : 'text-muted-foreground hover:text-foreground'
                  }`}
                >
                  <SettingsIcon className="h-4 w-4" />
                  Beállítások
                </button>
              </nav>
              
              {/* Second row - Program Data navigation */}
              <nav className="hidden md:flex gap-4 justify-center border-t border-border/50 pt-2">
                <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider self-center mr-2">
                  Program adatok:
                </span>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'health-map' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'health-map' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <Map className="h-3.5 w-3.5" />
                  Egészség Térkép
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'workshops' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'workshops' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <Calendar className="h-3.5 w-3.5" />
                  Workshopok
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'crisis' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'crisis' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <AlertTriangle className="h-3.5 w-3.5" />
                  Krízis Intervenciók
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'lottery' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'lottery' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <Gift className="h-3.5 w-3.5" />
                  Nyereményjáték
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'satisfaction' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'satisfaction' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <ThumbsUp className="h-3.5 w-3.5" />
                  Elégedettség
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'program-usage' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'program-usage' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <PieChart className="h-3.5 w-3.5" />
                  Használat
                </button>
                <button
                  onClick={() => {
                    setSearchParams({ section: 'data-submission' });
                    setOpenSubmenu(null);
                  }}
                  className={`text-xs transition-colors flex items-center gap-1.5 px-2 py-1 rounded-md ${
                    section === 'data-submission' 
                      ? 'text-foreground font-semibold bg-muted' 
                      : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
                  }`}
                >
                  <Upload className="h-3.5 w-3.5" />
                  Adatok Küldése
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

      {/* Dashboard Content or Magazin Page */}
      {renderDashboardContent() ? (
        <div className="max-w-7xl mx-auto px-4 py-6 pt-24 md:pt-6">
          {renderDashboardContent()}
        </div>
      ) : (
        <MagazinContent />
      )}
    </div>
  );
};

export default Index;
