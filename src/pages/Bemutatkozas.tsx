import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CheckCircle2, TrendingUp, Users, Award, Eye, Shield, Activity, Target } from 'lucide-react';
import logo from '@/assets/logo_black_v2.png';
import riportImage from '@/assets/riport_ok.svg';
import iphoneImage from '@/assets/iphone_eap_pulse_ok_2.png';
import { useAuth } from '@/hooks/useAuth';
import { MobileNav } from '@/components/navigation/MobileNav';

const Bemutatkozas = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

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
              className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-md"
            >
              Bemutatkozás
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
              Magazin
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
      <main className="bg-gradient-to-b from-muted/50 to-background pt-16 md:pt-0">
        <div className="max-w-7xl mx-auto px-4 py-12">
          {/* Hero Section */}
          <div className="text-center mb-16">
          <h1 className="text-4xl font-bold mb-4">
            Tedd láthatóvá az EAP-programod valódi értékét – valós idejű pulzusméréssel
          </h1>
          <p className="text-xl text-muted-foreground mb-8 max-w-4xl mx-auto">
            Az EAP Pulse rövid, rendszeres felmérésekkel méri a munkavállalói élményt, és a 4Score módszertan szerint mutatja meg, hol erős a programod és hol érdemes fejleszteni. Extra riportadatok, tiszta trendek, jobb döntések – szolgáltatóval együttműködve, nem helyette.
          </p>
          <div className="flex gap-4 justify-center relative z-10">
            <Button size="lg" onClick={() => navigate('/auth')} className="bg-[#3572ef] hover:bg-[#3572ef]/90">
              Kezdje el most
            </Button>
            <Button size="lg" variant="outline" onClick={() => {
              window.location.href = 'mailto:eap@cgpeu.com';
            }}>
              Tudjon meg többet
            </Button>
          </div>
          </div>

        {/* Section 1: EAP Pulse Introduction */}
        <div className="mb-16" id="learn-more">
          {/* Desktop view - original layout */}
          <div className="hidden lg:grid lg:grid-cols-2 gap-8">
            <Card>
              <CardHeader>
                <CardTitle className="text-3xl animate-pulse-glow">Mi az EAP Pulse?</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <p className="text-lg">
                  Az EAP Pulse egy innovatív eszköz, amely valós időben méri az EAP-programok pulzusát. Rövid, mobilbarát kérdőívekkel rendszeresen visszajelzést kérünk a munkatársaktól, majd az eredményeket a 4Score keretben jelenítjük meg – így egy pillantással látszik, mennyire ismert a program, mennyire bíznak benne és használják, valamint milyen kézzelfogható hatást ér el.
                </p>
                <p className="text-muted-foreground">
                  A Pulse nem helyettesíti az EAP-szolgáltatódat, hanem kiegészíti: több adatot ad a HR és a szolgáltató kezébe, hogy megalapozottabb döntések születhessenek a kommunikáció, a fejlesztés és a vezetői riportok terén. A cél közös: átlátható, hiteles kép a menedzsment felé, és egyre értékesebb, hasznosabb program a munkatársak számára.
                </p>
                <p className="text-muted-foreground">
                  A rendszer működése egyszerű és teljesen automatikus: a kérdőívek rendszeresen, előre beállított időpontokban érkeznek, az eredmények pedig valós időben frissülnek egy átlátható dashboardon. Így a HR és a vezetők mindig naprakész képet kapnak arról, hogyan alakul az EAP-program elfogadottsága és hatása. A válaszadás anonim, az adatok csak összesítve jelennek meg.
                </p>
              </CardContent>
            </Card>
            <div className="relative h-[500px] flex items-center justify-center md:justify-start">
              {/* Phone image with white rounded box background */}
              <div className="relative z-10 h-full flex items-center">
                <div className="absolute inset-2 bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.8)]" />
                <img 
                  src={iphoneImage} 
                  alt="EAP Pulse mobilalkalmazás" 
                  className="relative z-10 h-full w-auto object-contain"
                />
              </div>
              {/* Screen image - right, behind phone, overflowing */}
              <img 
                src={riportImage} 
                alt="Részletes riportok és analitika" 
                className="absolute left-[35%] -top-[7px] w-auto object-contain object-left scale-[1.2] z-0 rounded-lg shadow-lg"
              />
            </div>
          </div>

          {/* Tablet view - new layout */}
          <div className="hidden md:block lg:hidden space-y-8">
            <Card>
              <CardHeader>
                <CardTitle className="text-3xl animate-pulse-glow">Mi az EAP Pulse?</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <p className="text-lg">
                  Az EAP Pulse egy innovatív eszköz, amely valós időben méri az EAP-programok pulzusát. Rövid, mobilbarát kérdőívekkel rendszeresen visszajelzést kérünk a munkatársaktól, majd az eredményeket a 4Score keretben jelenítjük meg – így egy pillantással látszik, mennyire ismert a program, mennyire bíznak benne és használják, valamint milyen kézzelfogható hatást ér el.
                </p>
                <p className="text-muted-foreground">
                  A Pulse nem helyettesíti az EAP-szolgáltatódat, hanem kiegészíti: több adatot ad a HR és a szolgáltató kezébe, hogy megalapozottabb döntések születhessenek a kommunikáció, a fejlesztés és a vezetői riportok terén. A cél közös: átlátható, hiteles kép a menedzsment felé, és egyre értékesebb, hasznosabb program a munkatársak számára.
                </p>
                <p className="text-muted-foreground">
                  A rendszer működése egyszerű és teljesen automatikus: a kérdőívek rendszeresen, előre beállított időpontokban érkeznek, az eredmények pedig valós időben frissülnek egy átlátható dashboardon. Így a HR és a vezetők mindig naprakész képet kapnak arról, hogyan alakul az EAP-program elfogadottsága és hatása. A válaszadás anonim, az adatok csak összesítve jelennek meg.
                </p>
              </CardContent>
            </Card>
            
            <div className="relative">
              <img 
                src={riportImage} 
                alt="Részletes riportok és analitika" 
                className="w-full rounded-lg shadow-lg translate-x-[3cm]"
              />
              {/* Phone image positioned on left, half size, in front */}
              <div className="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-1/2 max-w-[250px]">
                <div className="relative">
                  <div className="absolute inset-2 bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.8)]" />
                  <img 
                    src={iphoneImage} 
                    alt="EAP Pulse mobilalkalmazás" 
                    className="relative z-10 w-full h-auto object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          {/* Mobile view - phone only, centered, full width */}
          <div className="md:hidden space-y-8">
            <Card>
              <CardHeader>
                <CardTitle className="text-3xl animate-pulse-glow">Mi az EAP Pulse?</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <p className="text-lg">
                  Az EAP Pulse egy innovatív eszköz, amely valós időben méri az EAP-programok pulzusát. Rövid, mobilbarát kérdőívekkel rendszeresen visszajelzést kérünk a munkatársaktól, majd az eredményeket a 4Score keretben jelenítjük meg – így egy pillantással látszik, mennyire ismert a program, mennyire bíznak benne és használják, valamint milyen kézzelfogható hatást ér el.
                </p>
                <p className="text-muted-foreground">
                  A Pulse nem helyettesíti az EAP-szolgáltatódat, hanem kiegészíti: több adatot ad a HR és a szolgáltató kezébe, hogy megalapozottabb döntések születhessenek a kommunikáció, a fejlesztés és a vezetői riportok terén. A cél közös: átlátható, hiteles kép a menedzsment felé, és egyre értékesebb, hasznosabb program a munkatársak számára.
                </p>
                <p className="text-muted-foreground">
                  A rendszer működése egyszerű és teljesen automatikus: a kérdőívek rendszeresen, előre beállított időpontokban érkeznek, az eredmények pedig valós időben frissülnek egy átlátható dashboardon. Így a HR és a vezetők mindig naprakész képet kapnak arról, hogyan alakul az EAP-program elfogadottsága és hatása. A válaszadás anonim, az adatok csak összesítve jelennek meg.
                </p>
              </CardContent>
            </Card>
            
            <div className="flex justify-center">
              <div className="relative w-full max-w-[300px]">
                <div className="absolute inset-2 bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.8)]" />
                <img 
                  src={iphoneImage} 
                  alt="EAP Pulse mobilalkalmazás" 
                  className="relative z-10 w-full h-auto object-contain"
                />
              </div>
            </div>
          </div>
        </div>

        {/* 4Score Section */}
        <section className="py-20 -mx-4 px-4 mb-16">
          <div className="max-w-7xl mx-auto">
            <div className="text-center mb-16">
              <div className="inline-block px-4 py-1 bg-gradient-to-r from-[#3abef9] to-[#ff66ff] text-white rounded-full text-sm font-medium mb-4">
                4Score Módszertan
              </div>
              <h2 className="text-4xl font-bold mb-4">A 4Score négy dimenzióban ad teljes képet az EAP-ról</h2>
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
                      Méri, hogy a munkavállalók mennyire ismerik az EAP-t, tudják-e, hogyan és mire használható.
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
                      Vizsgálja a programba vetett bizalmat és a segítségkérésre való hajlandóságot – az anonimitás érzésétől a vezetői példáig.
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
                      Értékeli a tényleges igénybevételt és a használat mintázatait – hol és miért aktivizálódnak az emberek.
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
                      Méri a program valós hatását a jóllétre és a szervezeti működésre: hiányzás, fluktuáció, elégedettség, ajánlási szándék.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Benefits Section */}
        <div className="mb-16">
          <h2 className="text-3xl font-bold text-center mb-8">Vállalati előnyök</h2>
          <div className="grid md:grid-cols-4 gap-6">
          <Card className="text-center">
            <CardHeader>
              <CheckCircle2 className="h-12 w-12 mx-auto mb-2" style={{ color: '#3366ff' }} />
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
              <TrendingUp className="h-12 w-12 mx-auto mb-2" style={{ color: '#3366ff' }} />
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
              <Award className="h-12 w-12 mx-auto mb-2" style={{ color: '#3366ff' }} />
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
              <Users className="h-12 w-12 mx-auto mb-2" style={{ color: '#3366ff' }} />
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

        {/* Detailed Description */}
        <div className="mb-16 text-center max-w-4xl mx-auto">
          <p className="text-lg text-muted-foreground">
            Az EAP Pulse a napi működés ritmusára hangolt mérőeszköz: nem évente egyszer vizsgál, hanem folyamatosan, így a kampányok, vezetői üzenetek és szervezeti változások hatása hetek alatt láthatóvá válik. A 4Score dimenziók világos fejlesztési térképet adnak, a trendek pedig segítenek abban, hogy ott avatkozz be, ahol a legnagyobb értéket teremtheti a fejlesztés.
          </p>
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
                  Általában 15 perc a kezdeti beállítás, utána a kiküldések és riportok automatizálhatók.
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
                  A kérdőív tartalma alapértelmezetten nem módosítható, azonban a választott csomagtól függően lehetőség van egyéni kérdéssor létrehozására és azzal új felmérés indítására. Ez biztosítja, hogy a vállalat saját igényeihez illeszkedő témákat is bevonhasson a vizsgálatba.
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
        </div>
      </main>
    </div>
  );
};

export default Bemutatkozas;
