import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, CheckCircle } from 'lucide-react';
import logo from '@/assets/logo.png';
import { useAuth } from '@/hooks/useAuth';
import { MobileNav } from '@/components/navigation/MobileNav';

const Arak = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

  const [selectedPackage, setSelectedPackage] = useState<string | null>(null);

  const packages = [
    {
      name: "Starter",
      price: "150 €",
      monthlyPrice: "150 € / hó",
      yearlyPrice: "1 800 € / év",
      period: "/hó",
      description: "Alapvető betekintés az EAP-programba",
      features: [
        "2 felmérés / év (félévente)",
        "Ajándéksorsolás minden felmérés végén",
        "4Score riport (Tudatosság · Használat · Bizalom & Hajlandóság · Hatás)",
        "Alap dashboard + letölthető riportok"
      ],
      recommended: false
    },
    {
      name: "Professional",
      price: "325 €",
      monthlyPrice: "325 € / hó",
      yearlyPrice: "3 900 € / év",
      period: "/hó",
      description: "Rendszeres és mélyreható elemzés",
      features: [
        "3 felmérés / év",
        "Ajándéksorsolás minden felmérés végén",
        "Teljes riportkészlet (minden statisztika + 4Score)",
        "Testreszabható kérdőív-design",
        "Trendösszevetések a felmérések között"
      ],
      recommended: true
    },
    {
      name: "Enterprise",
      price: "480 €",
      monthlyPrice: "480 € / hó",
      yearlyPrice: "5 760 € / év",
      period: "/hó",
      description: "Maximális átláthatóság és integráció",
      features: [
        "4 felmérés / év",
        "Ajándéksorsolás minden felmérés végén",
        "Teljes riportkészlet + trend- és összehasonlító elemzés",
        "White-label lehetőség",
        "API integráció"
      ],
      recommended: false
    }
  ];

  const handlePackageClick = (pkgName: string) => {
    if (pkgName === 'Enterprise') {
      window.location.href = 'mailto:info@doyoueap.com';
    } else {
      if (user) {
        navigate('/?section=focus');
      } else {
        navigate('/auth');
      }
    }
  };

  return (
    <div className="min-h-screen bg-background overflow-x-hidden">
      {/* Header with Navigation */}
      <header className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 fixed md:sticky top-0 z-50 w-full">
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center relative">
          <div className="flex items-center gap-2">
            <MobileNav 
              user={user}
              role={role}
              section=""
              onNavigate={navigate}
              onLogout={signOut}
            />
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
              className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-md"
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
                className="text-sm border border-transparent transition-colors px-3 py-2 rounded-md hover:bg-muted"
              >
                Dashboard
              </button>
            )}
          </nav>
          <div className="flex items-center gap-4">
            {!loading && (
              user ? (
                <Button onClick={signOut} variant="outline" className="hidden md:flex">
                  Kilépés
                </Button>
              ) : (
                <Button onClick={() => navigate('/auth')} className="hidden md:flex">
                  Bejelentkezés
                </Button>
              )
            )}
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="bg-gradient-to-b from-muted/50 to-background py-12 pt-24 md:pt-12">
        <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold mb-4">Árak és Csomagok</h1>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Válassza ki az igényeinek megfelelő csomagot a felmérések gyakorisága és a riportok mélysége szerint
          </p>
        </div>

        {/* Pricing Cards */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 items-stretch">
          {packages.map((pkg, index) => (
            <Card 
              key={index} 
              className={`relative flex flex-col cursor-pointer transition-all ${
                selectedPackage === pkg.name 
                  ? 'border-[#3572ef] shadow-xl scale-105' 
                  : pkg.recommended && selectedPackage === null
                    ? 'border-[#3572ef] shadow-lg' 
                    : 'hover:shadow-md'
              }`}
              onClick={() => setSelectedPackage(pkg.name)}
            >
              <CardHeader className="pb-6">
                {pkg.recommended && selectedPackage === null && (
                  <div className="text-xs font-medium mb-2" style={{ color: '#3572ef' }}>AJÁNLOTT</div>
                )}
                <CardTitle className="text-2xl">{pkg.name}</CardTitle>
                <CardDescription>{pkg.description}</CardDescription>
                <div className="mt-4">
                  <div className="text-3xl font-bold">
                    {pkg.price}<span className="text-lg font-normal text-muted-foreground">{pkg.period}</span>
                  </div>
                  <div className="text-sm text-muted-foreground mt-1">
                    {pkg.yearlyPrice}
                  </div>
                </div>
              </CardHeader>
              <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-2 mb-4">
                  {pkg.features.map((feature, featureIndex) => (
                    <div key={featureIndex} className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                      <p className="font-medium text-sm">{feature}</p>
                    </div>
                  ))}
                </div>
                <Button 
                  className={`w-full ${pkg.recommended && selectedPackage === null ? 'bg-[#3572ef] hover:bg-[#3572ef]/90' : ''}`}
                  variant={pkg.recommended && selectedPackage === null ? 'default' : 'outline'}
                  onClick={(e) => {
                    e.stopPropagation();
                    handlePackageClick(pkg.name);
                  }}
                >
                  {pkg.name === 'Enterprise' ? 'Kapcsolatfelvétel' : 'Kiválasztom'}
                </Button>
              </CardContent>
            </Card>
          ))}

          {/* EAP Provider / Partner Program Card */}
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

        {/* Additional Info */}
        <div className="mt-12">
          <Card className="bg-background border">
            <CardHeader>
              <CardTitle>Gyakran Ismételt Kérdések</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <h3 className="font-semibold mb-2">Milyen fizetési módokat fogadnak el?</h3>
                <p className="text-muted-foreground">
                  Banki átutalást és céges bankkártyás fizetést is elfogadunk. Az éves előfizetést egy összegben kell kiegyenlíteni.
                </p>
              </div>
              <div>
                <h3 className="font-semibold mb-2">Van próbaidőszak?</h3>
                <p className="text-muted-foreground">
                  Igen, minden csomagunk esetében biztosítunk 30 napos pénz-visszafizetési garanciát.
                </p>
              </div>
              <div>
                <h3 className="font-semibold mb-2">Mi a különbség a csomagok között?</h3>
                <p className="text-muted-foreground">
                  A fő különbség a felmérések gyakorisága és a riportok mélysége. Minél többször méri fel munkatársait, annál pontosabb trendeket láthat, és a magasabb csomagok részletesebb elemzéseket, testreszabási lehetőségeket kínálnak.
                </p>
              </div>
              <div>
                <h3 className="font-semibold mb-2">Mikor válthatom a csomagomat?</h3>
                <p className="text-muted-foreground">
                  Bármikor válthat magasabb csomagra az aktuális előfizetési időszakon belül. Az ár különbözetet időarányosan számoljuk el.
                </p>
              </div>
            </CardContent>
          </Card>
        </div>
        </div>
      </main>
    </div>
  );
};

export default Arak;
