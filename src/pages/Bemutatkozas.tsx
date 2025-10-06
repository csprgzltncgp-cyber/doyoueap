import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { CheckCircle2, ArrowRight, Eye, Heart, Activity, BarChart3, Users, Zap, Shield, FileText } from 'lucide-react';
import logo from '@/assets/logo.png';
import heroImage from '@/assets/eap-pulse-hero.jpg';
import { useAuth } from '@/hooks/useAuth';

const Bemutatkozas = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

  const pulseBenefits = [
    {
      icon: CheckCircle2,
      title: "Hiteles visszajelzés",
      description: "Anonim, összesített, torzítatlan nézőpont a dolgozóktól – rendszeresen, összehasonlíthatóan."
    },
    {
      icon: BarChart3,
      title: "Jobb döntések",
      description: "4Score riportokkal gyorsan azonosíthatók a fejlesztési pontok és a működő gyakorlatok."
    },
    {
      icon: FileText,
      title: "Menedzsment-riport egy kattintásra",
      description: "Kész vizualizációk és exportok a vezetői prezentációkhoz."
    },
    {
      icon: Users,
      title: "Szolgáltató-barát megoldás",
      description: "Az EAP-partnerrel együttműködve működik; API-val és white-label opcióval is bővíthető."
    }
  ];

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
              className="text-sm bg-white border border-black font-semibold transition-colors px-3 py-2 rounded-sm"
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
                onClick={() => navigate('/?section=eap-pulse&sub=create-audit')}
                className="text-sm transition-colors px-3 py-2 rounded hover:bg-muted"
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

      {/* Hero Section - Full Width Image with Overlay */}
      <section className="relative h-[70vh] min-h-[500px] flex items-center justify-center overflow-hidden">
        <div className="absolute inset-0">
          <img 
            src={heroImage} 
            alt="EAP Pulse - munkavállalói felmérés" 
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-[#3572ef]/90 via-[#3572ef]/70 to-transparent" />
        </div>
        <div className="relative max-w-7xl mx-auto px-4 text-white z-10">
          <div className="max-w-3xl">
            <h1 className="text-4xl md:text-6xl font-bold mb-6 leading-tight">
              Tedd láthatóvá az EAP-programod valódi értékét – <span className="text-[#3abef9]">valós idejű pulzusméréssel</span>
            </h1>
            <p className="text-lg md:text-xl mb-8 text-white/90">
              Az EAP Pulse rövid, rendszeres felmérésekkel méri a munkavállalói élményt, és a 4Score módszertan szerint mutatja meg, hol erős a programod és hol érdemes fejleszteni. Extra riportadatok, tiszta trendek, jobb döntések – szolgáltatóval együttműködve, nem helyette.
            </p>
            <div className="flex gap-4 flex-wrap">
              <Button 
                size="lg" 
                onClick={() => navigate('/auth')}
                className="bg-white text-[#3572ef] hover:bg-white/90"
              >
                Kezdje el most <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
              <Button 
                size="lg" 
                variant="outline"
                className="border-white text-white hover:bg-white/10"
              >
                Tudjon meg többet
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Main Content */}
      <main>
        
        {/* What is EAP Pulse - Two Column Layout */}
        <section className="py-20">
          <div className="max-w-7xl mx-auto px-4">
            <div className="grid md:grid-cols-5 gap-12 items-start">
              <div className="md:col-span-2">
                <div className="sticky top-24">
                  <div className="inline-block px-3 py-1 bg-gradient-to-r from-[#3abef9] to-[#ff66ff] text-white rounded-full text-xs font-medium mb-4">
                    Bemutatkozás
                  </div>
                  <h2 className="text-4xl font-bold mb-4">Mi az EAP Pulse?</h2>
                  <p className="text-muted-foreground">
                    Gyors és kíméletes „pulzusmérő" a futó EAP-programokhoz
                  </p>
                </div>
              </div>
              <div className="md:col-span-3 space-y-6">
                <p className="text-lg leading-relaxed">
                  Az EAP Pulse egy gyors és kíméletes „pulzusmérő" a futó EAP-programokhoz. Rövid, mobilbarát kérdőívekkel rendszeresen visszajelzést kérünk a munkatársaktól, majd az eredményeket a 4Score keretben jelenítjük meg – így egy pillantással látszik, mennyire ismert a program, mennyire bíznak benne és használják, valamint milyen kézzelfogható hatást ér el.
                </p>
                <p className="text-lg leading-relaxed">
                  A Pulse nem váltja ki az EAP-szolgáltatódat, hanem kiegészíti: több adatot ad a kezébe (és a HR kezébe) a jobb kommunikációhoz, fejlesztési döntésekhez és a vezetői riportokhoz. A cél egy win–win: hiteles, átlátható kép a menedzsment felé, és egyre használhatóbb program a dolgozóknak.
                </p>
                <p className="text-lg leading-relaxed">
                  A megvalósítás egyszerű: előre összeállított vagy testre szabható kérdések, automatikus kiküldés, real-time dashboardok és kész menedzsment-riportok. A válaszadás anonim, az adatok összesítve jelennek meg.
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* 4Score - Horizontal Cards */}
        <section className="py-20 bg-gradient-to-b from-muted/30 to-background">
          <div className="max-w-7xl mx-auto px-4">
            <div className="text-center mb-16">
              <div className="inline-block px-3 py-1 bg-gradient-to-r from-[#3abef9] to-[#ff66ff] text-white rounded-full text-xs font-medium mb-4">
                4Score Módszertan
              </div>
              <h2 className="text-4xl font-bold mb-4">A 4Score négy dimenzióban ad teljes képet az EAP-ról</h2>
              <p className="text-muted-foreground max-w-2xl mx-auto">
                Mérhető eredmények négy kulcsfontosságú pillér mentén
              </p>
            </div>
            
            <div className="space-y-4">
              <Card className="border-l-4 border-l-[#3572ef] hover:shadow-md transition-shadow">
                <CardHeader>
                  <div className="flex items-center gap-4">
                    <div className="h-14 w-14 rounded-lg bg-[#3572ef]/10 flex items-center justify-center flex-shrink-0">
                      <Eye className="h-7 w-7 text-[#3572ef]" />
                    </div>
                    <div>
                      <CardTitle className="text-2xl">Tudatosság</CardTitle>
                      <CardDescription className="text-base mt-1">
                        Méri, hogy a munkavállalók mennyire ismerik az EAP-t, tudják-e, hogyan és mire használható.
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>
              </Card>

              <Card className="border-l-4 border-l-[#3abef9] hover:shadow-md transition-shadow">
                <CardHeader>
                  <div className="flex items-center gap-4">
                    <div className="h-14 w-14 rounded-lg bg-[#3abef9]/10 flex items-center justify-center flex-shrink-0">
                      <Activity className="h-7 w-7 text-[#3abef9]" />
                    </div>
                    <div>
                      <CardTitle className="text-2xl">Használat</CardTitle>
                      <CardDescription className="text-base mt-1">
                        Értékeli a tényleges igénybevételt és a használat mintázatait – hol és miért aktivizálódnak az emberek.
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>
              </Card>

              <Card className="border-l-4 border-l-[#ff66ff] hover:shadow-md transition-shadow">
                <CardHeader>
                  <div className="flex items-center gap-4">
                    <div className="h-14 w-14 rounded-lg bg-[#ff66ff]/10 flex items-center justify-center flex-shrink-0">
                      <Heart className="h-7 w-7 text-[#ff66ff]" />
                    </div>
                    <div>
                      <CardTitle className="text-2xl">Bizalom & Hajlandóság</CardTitle>
                      <CardDescription className="text-base mt-1">
                        Vizsgálja a programba vetett bizalmat és a segítségkérésre való hajlandóságot – az anonimitás érzésétől a vezetői példáig.
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>
              </Card>

              <Card className="border-l-4 border-l-[#3572ef] hover:shadow-md transition-shadow">
                <CardHeader>
                  <div className="flex items-center gap-4">
                    <div className="h-14 w-14 rounded-lg bg-[#3572ef]/10 flex items-center justify-center flex-shrink-0">
                      <BarChart3 className="h-7 w-7 text-[#3572ef]" />
                    </div>
                    <div>
                      <CardTitle className="text-2xl">Hatás</CardTitle>
                      <CardDescription className="text-base mt-1">
                        Méri a program valós hatását a jóllétre és a szervezeti működésre: hiányzás, fluktuáció, elégedettség, ajánlási szándék.
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>
              </Card>
            </div>
          </div>
        </section>

        {/* Benefits - Grid with Icons */}
        <section className="py-20">
          <div className="max-w-7xl mx-auto px-4">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold mb-3">Miért válassza az EAP Pulse-t?</h2>
              <p className="text-muted-foreground">
                Konkrét előnyök a szervezet számára
              </p>
            </div>
            <div className="grid md:grid-cols-2 gap-8">
              {pulseBenefits.map((benefit, index) => (
                <div key={index} className="flex gap-4 p-6 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                  <div className="flex-shrink-0">
                    <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                      <benefit.icon className="h-6 w-6 text-primary" />
                    </div>
                  </div>
                  <div>
                    <h3 className="text-xl font-semibold mb-2">{benefit.title}</h3>
                    <p className="text-muted-foreground">{benefit.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* FAQ Section */}
        <section className="py-20 bg-muted/30">
          <div className="max-w-7xl mx-auto px-4">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold mb-3">Gyakran Ismételt Kérdések</h2>
              <p className="text-muted-foreground">
                Minden, amit tudnia kell az EAP Pulse-ról
              </p>
            </div>
            
            <Accordion type="single" collapsible className="max-w-3xl mx-auto space-y-4">
              <AccordionItem value="item-1" className="border rounded-lg px-6 bg-background">
                <AccordionTrigger className="text-left hover:no-underline">
                  <span className="font-semibold">Az EAP Pulse helyettesíti az EAP-szolgáltatót?</span>
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground">
                  Nem. A Pulse a meglévő EAP-ot egészíti ki extra adatokkal és riportokkal.
                </AccordionContent>
              </AccordionItem>

              <AccordionItem value="item-2" className="border rounded-lg px-6 bg-background">
                <AccordionTrigger className="text-left hover:no-underline">
                  <span className="font-semibold">Mennyi idő a bevezetés?</span>
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground">
                  Általában 15–30 perc a kezdeti beállítás, utána a kiküldések és riportok automatizálhatók.
                </AccordionContent>
              </AccordionItem>

              <AccordionItem value="item-3" className="border rounded-lg px-6 bg-background">
                <AccordionTrigger className="text-left hover:no-underline">
                  <span className="font-semibold">Anonim a válaszadás?</span>
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground">
                  Igen. A válaszok anonimek, az eredmények összesítve jelennek meg.
                </AccordionContent>
              </AccordionItem>

              <AccordionItem value="item-4" className="border rounded-lg px-6 bg-background">
                <AccordionTrigger className="text-left hover:no-underline">
                  <span className="font-semibold">Testre szabható a kérdőív?</span>
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground">
                  Igen. Az előre definiált kérdések mellett saját kérdéseket is hozzáadhatsz.
                </AccordionContent>
              </AccordionItem>

              <AccordionItem value="item-5" className="border rounded-lg px-6 bg-background">
                <AccordionTrigger className="text-left hover:no-underline">
                  <span className="font-semibold">Elérhető white-label verzió?</span>
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground">
                  Igen, EAP-szolgáltatók számára egyedi arculati beállításokat és API-integrációt is kínálunk.
                </AccordionContent>
              </AccordionItem>
            </Accordion>
          </div>
        </section>
        
        {/* Final CTA */}
        <section className="py-20 bg-gradient-to-r from-[#3572ef] to-[#3abef9] text-white">
          <div className="max-w-4xl mx-auto px-4 text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">
              Készen áll az EAP program fejlesztésére?
            </h2>
            <p className="text-lg mb-8 text-white/90">
              Kezdje el az EAP Pulse használatát még ma, és tapasztalja meg az adatalapú döntéshozatal előnyeit.
            </p>
            <div className="flex gap-4 justify-center flex-wrap">
              <Button 
                size="lg" 
                onClick={() => navigate('/auth')}
                className="bg-white text-[#3572ef] hover:bg-white/90"
              >
                Regisztráció <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
              <Button 
                size="lg" 
                variant="outline"
                className="border-white text-white hover:bg-white/10"
                onClick={() => navigate('/arak')}
              >
                Árak megtekintése
              </Button>
            </div>
          </div>
        </section>

      </main>
    </div>
  );
};

export default Bemutatkozas;
