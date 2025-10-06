import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Check, CheckCircle } from 'lucide-react';
import logo from '@/assets/logo.png';
import { useAuth } from '@/hooks/useAuth';

const Arak = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

  const packages = [
    {
      name: "Starter",
      price: "290.000 Ft",
      period: "/év",
      description: "Kezdő csomagunk kisebb szervezeteknek",
      features: [
        "Legfeljebb 200 munkavállaló",
        "Éves EAP audit",
        "4Score módszertan",
        "Alapvető riportok",
        "Email támogatás"
      ],
      recommended: false
    },
    {
      name: "Professional",
      price: "490.000 Ft",
      period: "/év",
      description: "A legnépszerűbb választás",
      features: [
        "Legfeljebb 500 munkavállaló",
        "Féléves EAP auditok",
        "4Score módszertan",
        "Részletes riportok és elemzések",
        "Trend-elemzés",
        "Prioritásos támogatás",
        "Testre szabható kérdőívek"
      ],
      recommended: true
    },
    {
      name: "Enterprise",
      price: "Egyedi",
      period: "árazás",
      description: "Nagy szervezetek számára",
      features: [
        "Korlátlan munkavállaló",
        "Negyedéves EAP auditok",
        "4Score módszertan",
        "Teljes körű elemzések",
        "Prediktív modellek",
        "Dedikált ügyfélmenedzser",
        "API integráció",
        "White-label lehetőség",
        "Oktatás és tanácsadás"
      ],
      recommended: false
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
                className="text-sm border border-transparent transition-colors px-3 py-2 rounded-sm hover:bg-muted"
              >
                The Journalist!
              </button>
              <button
                onClick={() => navigate('/bemutatkozas')}
                className="text-sm border border-transparent transition-colors px-3 py-2 rounded-sm hover:bg-muted"
              >
                EAP Pulse
              </button>
              <button
                onClick={() => navigate('/arak')}
                className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-sm"
              >
                Árak és Csomagok
              </button>
              {user && role === 'hr' && (
                <button
                  onClick={() => navigate('/?section=eap-pulse&sub=create-audit')}
                  className="text-sm transition-colors px-3 py-2 rounded hover:bg-muted"
                >
                  Dashboard
                </button>
              )}
            </nav>
          </div>
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

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-12">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold mb-4">Árak és Csomagok</h1>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Válassza ki a szervezete méretének és igényeinek legmegfelelőbb csomagot
          </p>
        </div>

        {/* Pricing Cards */}
        <div className="grid md:grid-cols-4 gap-8 mb-12">
          {packages.map((pkg, index) => (
            <Card 
              key={index} 
              className={`relative ${pkg.recommended ? 'border-primary shadow-lg scale-105' : ''}`}
            >
              {pkg.recommended && (
                <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                  <span className="bg-primary text-primary-foreground px-4 py-1 rounded-full text-sm font-medium">
                    Ajánlott
                  </span>
                </div>
              )}
              <CardHeader className="text-center pb-8 pt-8">
                <CardTitle className="text-2xl mb-2">{pkg.name}</CardTitle>
                <CardDescription>{pkg.description}</CardDescription>
                <div className="mt-4">
                  <span className="text-4xl font-bold">{pkg.price}</span>
                  <span className="text-muted-foreground ml-1">{pkg.period}</span>
                </div>
              </CardHeader>
              <CardContent>
                <ul className="space-y-3 mb-6">
                  {pkg.features.map((feature, featureIndex) => (
                    <li key={featureIndex} className="flex items-start gap-2">
                      <Check className="h-5 w-5 text-primary flex-shrink-0 mt-0.5" />
                      <span className="text-sm">{feature}</span>
                    </li>
                  ))}
                </ul>
                <Button 
                  className="w-full" 
                  variant={pkg.recommended ? 'default' : 'outline'}
                  onClick={() => navigate('/auth')}
                >
                  {pkg.name === 'Enterprise' ? 'Kapcsolatfelvétel' : 'Kezdjük el'}
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
              <Button className="w-full" variant="default">
                Kérj egyedi ajánlatot
              </Button>
            </CardContent>
          </Card>
        </div>

        {/* Additional Info */}
        <Card className="bg-muted/50">
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
              <h3 className="font-semibold mb-2">Hogyan működik a munkavállalói limit?</h3>
              <p className="text-muted-foreground">
                A limit a szervezet teljes munkavállalói létszámára vonatkozik. Ha túllépi a csomagban meghatározott limitet, automatikusan felajánljuk a következő csomagra való váltást.
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
      </main>
    </div>
  );
};

export default Arak;
