import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CheckCircle2, TrendingUp, Users, Award } from 'lucide-react';
import logo from '@/assets/logo.png';
import eapPulseTeam from '@/assets/eap-pulse-team.jpg';
import fourScoreTeam from '@/assets/4score-team.jpg';
import corporateBenefits from '@/assets/corporate-benefits.jpg';
import eapProviders from '@/assets/eap-providers.jpg';
import { useAuth } from '@/hooks/useAuth';

const Bemutatkozas = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

  return (
    <div className="min-h-screen bg-background">
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
              className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-md"
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
              The Journalist!
            </button>
            {user && role === 'hr' && (
              <button
                onClick={() => navigate('/?section=eap-pulse&sub=create-audit')}
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

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-12">
        {/* Hero Section */}
        <div className="text-center mb-16">
          <h1 className="text-4xl md:text-5xl font-bold mb-6">
            Tedd láthatóvá az EAP-programod valódi értékét – valós idejű pulzusméréssel
          </h1>
          <p className="text-xl text-muted-foreground mb-8 max-w-4xl mx-auto">
            Az EAP Pulse rövid, rendszeres felmérésekkel méri a munkavállalói élményt, és a 4Score módszertan szerint mutatja meg, hol erős a programod és hol érdemes fejleszteni. Extra riportadatok, tiszta trendek, jobb döntések – szolgáltatóval együttműködve, nem helyette.
          </p>
          <div className="flex gap-4 justify-center">
            <Button size="lg" onClick={() => navigate('/auth')} className="bg-[#3572ef] hover:bg-[#3572ef]/90">
              Kezdje el most
            </Button>
            <Button size="lg" variant="outline" onClick={() => {
              const element = document.getElementById('learn-more');
              element?.scrollIntoView({ behavior: 'smooth' });
            }}>
              Tudjon meg többet
            </Button>
          </div>
        </div>

        {/* Section 1: EAP Pulse Introduction */}
        <div className="grid md:grid-cols-2 gap-8 mb-16" id="learn-more">
          <Card>
            <CardHeader>
              <CardTitle className="text-3xl">Mi az EAP Pulse?</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              <p className="text-lg">
                Az EAP Pulse egy gyors és kíméletes „pulzusmérő" a futó EAP-programokhoz. Rövid, mobilbarát kérdőívekkel rendszeresen visszajelzést kérünk a munkatársaktól, majd az eredményeket a 4Score keretben jelenítjük meg – így egy pillantással látszik, mennyire ismert a program, mennyire bíznak benne és használják, valamint milyen kézzelfogható hatást ér el.
              </p>
              <p className="text-muted-foreground">
                A Pulse nem váltja ki az EAP-szolgáltatódat, hanem kiegészíti: több adatot ad a kezébe (és a HR kezébe) a jobb kommunikációhoz, fejlesztési döntésekhez és a vezetői riportokhoz. A cél egy win–win: hiteles, átlátható kép a menedzsment felé, és egyre használhatóbb program a dolgozóknak.
              </p>
              <p className="text-muted-foreground">
                A megvalósítás egyszerű: előre összeállított vagy testre szabható kérdések, automatikus kiküldés, real-time dashboardok és kész menedzsment-riportok. A válaszadás anonim, az adatok összesítve jelennek meg.
              </p>
            </CardContent>
          </Card>
          <div className="rounded-lg overflow-hidden h-full">
            <img 
              src={eapPulseTeam} 
              alt="EAP Pulse csapat értekezlet" 
              className="w-full h-full object-cover"
            />
          </div>
        </div>

        {/* Section 2: 4Score Methodology */}
        <Card className="mb-16">
          <CardHeader>
            <CardTitle className="text-3xl text-center">A 4Score négy dimenzióban ad teljes képet az EAP-ról:</CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="grid md:grid-cols-2 gap-6">
              <Card className="border-l-4 border-l-[#3572ef]">
                <CardHeader>
                  <div className="flex items-center gap-3">
                    <div className="p-2 bg-[#3572ef]/10 rounded-lg">
                      <TrendingUp className="h-6 w-6 text-[#3572ef]" />
                    </div>
                    <CardTitle className="text-xl">1. Tudatosság (Awareness)</CardTitle>
                  </div>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground">
                    Méri, hogy a munkavállalók mennyire ismerik az EAP-t, tudják-e, hogyan és mire használható.
                  </p>
                </CardContent>
              </Card>

              <Card className="border-l-4 border-l-[#3abef9]">
                <CardHeader>
                  <div className="flex items-center gap-3">
                    <div className="p-2 bg-[#3abef9]/10 rounded-lg">
                      <Users className="h-6 w-6 text-[#3abef9]" />
                    </div>
                    <CardTitle className="text-xl">2. Használat (Usage)</CardTitle>
                  </div>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground">
                    Értékeli a tényleges igénybevételt és a használat mintázatait – hol és miért aktivizálódnak az emberek.
                  </p>
                </CardContent>
              </Card>

              <Card className="border-l-4 border-l-[#ff66ff]">
                <CardHeader>
                  <div className="flex items-center gap-3">
                    <div className="p-2 bg-[#ff66ff]/10 rounded-lg">
                      <CheckCircle2 className="h-6 w-6 text-[#ff66ff]" />
                    </div>
                    <CardTitle className="text-xl">3. Bizalom & Hajlandóság (Trust & Willingness)</CardTitle>
                  </div>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground">
                    Vizsgálja a programba vetett bizalmat és a segítségkérésre való hajlandóságot – az anonimitás érzésétől a vezetői példáig.
                  </p>
                </CardContent>
              </Card>

              <Card className="border-l-4 border-l-[#050c9c]">
                <CardHeader>
                  <div className="flex items-center gap-3">
                    <div className="p-2 bg-[#050c9c]/10 rounded-lg">
                      <Award className="h-6 w-6 text-[#050c9c]" />
                    </div>
                    <CardTitle className="text-xl">4. Hatás (Impact)</CardTitle>
                  </div>
                </CardHeader>
                <CardContent>
                  <p className="text-muted-foreground">
                    Méri a program valós hatását a jóllétre és a szervezeti működésre: hiányzás, fluktuáció, elégedettség, ajánlási szándék.
                  </p>
                </CardContent>
              </Card>
            </div>
          </CardContent>
        </Card>

        {/* Benefits Section */}
        <div className="mb-16">
          <h2 className="text-3xl font-bold text-center mb-8">Vállalati előnyök</h2>
          <div className="grid md:grid-cols-4 gap-6">
          <Card className="text-center">
            <CardHeader>
              <CheckCircle2 className="h-12 w-12 mx-auto text-primary mb-2" />
              <CardTitle className="text-lg">Hiteles visszajelzés</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground">
                Anonim, összesített, torzítatlan nézőpont a dolgozóktól – rendszeresen, összehasonlíthatóan.
              </p>
            </CardContent>
          </Card>

          <Card className="text-center">
            <CardHeader>
              <TrendingUp className="h-12 w-12 mx-auto text-primary mb-2" />
              <CardTitle className="text-lg">Jobb döntések</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground">
                4Score riportokkal gyorsan azonosíthatók a fejlesztési pontok és a működő gyakorlatok.
              </p>
            </CardContent>
          </Card>

          <Card className="text-center">
            <CardHeader>
              <Award className="h-12 w-12 mx-auto text-primary mb-2" />
              <CardTitle className="text-lg">Menedzsment-riport egy kattintásra</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground">
                Kész vizualizációk és exportok a vezetői prezentációkhoz.
              </p>
            </CardContent>
          </Card>

          <Card className="text-center">
            <CardHeader>
              <Users className="h-12 w-12 mx-auto text-primary mb-2" />
              <CardTitle className="text-lg">Szolgáltató-barát megoldás</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground">
                Az EAP-partnerrel együttműködve működik; API-val és white-label opcióval is bővíthető.
              </p>
            </CardContent>
          </Card>
          </div>
        </div>

        {/* CTA Section */}
        <Card className="mb-16 bg-gradient-to-r from-[#3572ef] to-[#3abef9] text-white border-none">
          <CardHeader className="text-center">
            <CardTitle className="text-3xl md:text-4xl text-white">Indítsa el az első EAP Pulse felmérését</CardTitle>
            <CardDescription className="text-xl text-white/90">
              Beállítás 15 perc alatt. Azonnali 4Score-riportok. 100% anonim válaszadás.
            </CardDescription>
          </CardHeader>
          <CardContent className="text-center">
            <Button size="lg" variant="secondary" onClick={() => navigate('/auth')} className="bg-white text-[#3572ef] hover:bg-white/90">
              Regisztráció
            </Button>
          </CardContent>
        </Card>

        {/* Detailed Description */}
        <Card className="mb-16">
          <CardContent className="py-8">
            <p className="text-lg text-muted-foreground">
              Az EAP Pulse a napi működés ritmusára hangolt mérőeszköz: nem évente egyszer vizsgál, hanem folyamatosan, így a kampányok, vezetői üzenetek és szervezeti változások hatása hetek alatt láthatóvá válik. A 4Score dimenziók világos fejlesztési térképet adnak, a trendek pedig segítenek abban, hogy ott avatkozz be, ahol a legnagyobb értéket teremtheti a fejlesztés.
            </p>
          </CardContent>
        </Card>

        {/* FAQ Section */}
        <Card>
          <CardHeader>
            <CardTitle className="text-3xl">Gyakran Ismételt Kérdések (GYIK)</CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="space-y-4">
              <div>
                <h3 className="text-lg font-semibold mb-2">Az EAP Pulse helyettesíti az EAP-szolgáltatót?</h3>
                <p className="text-muted-foreground">
                  Nem. A Pulse a meglévő EAP-ot egészíti ki extra adatokkal és riportokkal.
                </p>
              </div>
              
              <div>
                <h3 className="text-lg font-semibold mb-2">Mennyi idő a bevezetés?</h3>
                <p className="text-muted-foreground">
                  Általában 15–30 perc a kezdeti beállítás, utána a kiküldések és riportok automatizálhatók.
                </p>
              </div>
              
              <div>
                <h3 className="text-lg font-semibold mb-2">Anonim a válaszadás?</h3>
                <p className="text-muted-foreground">
                  Igen. A válaszok anonimek, az eredmények összesítve jelennek meg.
                </p>
              </div>
              
              <div>
                <h3 className="text-lg font-semibold mb-2">Testre szabható a kérdőív?</h3>
                <p className="text-muted-foreground">
                  Igen. Előre összeállított blokkokból dolgozunk, de saját kérdéseket is hozzáadhatsz.
                </p>
              </div>
              
              <div>
                <h3 className="text-lg font-semibold mb-2">Hogyan jelenik meg a 4Score?</h3>
                <p className="text-muted-foreground">
                  Valós idejű dashboardon, letölthető jelentésekben és időbeli trendekben.
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </main>
    </div>
  );
};

export default Bemutatkozas;
