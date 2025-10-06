import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { CheckCircle2, TrendingUp, Users, Award, Eye, Heart, Target, BarChart3 } from 'lucide-react';
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
      icon: Target,
      title: "Jobb döntések",
      description: "4Score riportokkal gyorsan azonosíthatók a fejlesztési pontok és a működő gyakorlatok."
    },
    {
      icon: BarChart3,
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

      {/* Gradient bar below header */}
      <div className="bg-gradient-to-r from-[#3572ef] to-[#3abef9] h-1"></div>

      {/* Hero Section */}
      <section className="relative h-[600px] flex items-center justify-center overflow-hidden">
        <div className="absolute inset-0">
          <img 
            src={heroImage} 
            alt="EAP Pulse" 
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent" />
        </div>
        <div className="relative max-w-7xl mx-auto px-4 text-white">
          <h1 className="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
            Tedd láthatóvá az EAP-programod valódi értékét – valós idejű pulzusméréssel
          </h1>
          <p className="text-xl md:text-2xl mb-8 max-w-3xl animate-fade-in">
            Az EAP Pulse rövid, rendszeres felmérésekkel méri a munkavállalói élményt, és a 4Score módszertan szerint mutatja meg, hol erős a programod és hol érdemes fejleszteni. Extra riportadatok, tiszta trendek, jobb döntések – szolgáltatóval együttműködve, nem helyette.
          </p>
          <div className="flex gap-4 flex-wrap">
            <Button 
              size="lg" 
              onClick={() => navigate('/auth')}
              className="bg-primary hover:bg-primary/90 text-primary-foreground animate-scale-in"
            >
              Kezdje el most
            </Button>
            <Button 
              size="lg" 
              variant="outline"
              className="bg-white/10 border-white/20 text-white hover:bg-white/20"
            >
              Tudjon meg többet
            </Button>
          </div>
        </div>
      </section>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-16">
        
        {/* What is EAP Pulse */}
        <section className="mb-20">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Mi az EAP Pulse?</h2>
          </div>
          
          <Card className="max-w-4xl mx-auto">
            <CardContent className="pt-6">
              <p className="text-lg text-muted-foreground mb-4">
                Az EAP Pulse egy gyors és kíméletes „pulzusmérő" a futó EAP-programokhoz. Rövid, mobilbarát kérdőívekkel rendszeresen visszajelzést kérünk a munkatársaktól, majd az eredményeket a 4Score keretben jelenítjük meg – így egy pillantással látszik, mennyire ismert a program, mennyire bíznak benne és használják, valamint milyen kézzelfogható hatást ér el.
              </p>
              <p className="text-lg text-muted-foreground mb-4">
                A Pulse nem váltja ki az EAP-szolgáltatódat, hanem kiegészíti: több adatot ad a kezébe (és a HR kezébe) a jobb kommunikációhoz, fejlesztési döntésekhez és a vezetői riportokhoz. A cél egy win–win: hiteles, átlátható kép a menedzsment felé, és egyre használhatóbb program a dolgozóknak.
              </p>
              <p className="text-lg text-muted-foreground">
                A megvalósítás egyszerű: előre összeállított vagy testre szabható kérdések, automatikus kiküldés, real-time dashboardok és kész menedzsment-riportok. A válaszadás anonim, az adatok összesítve jelennek meg.
              </p>
            </CardContent>
          </Card>
        </section>

        {/* 4Score Methodology */}
        <section className="mb-20">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">A 4Score négy dimenzióban ad teljes képet az EAP-ról:</h2>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <Eye className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">01</CardTitle>
                </div>
                <CardTitle className="text-xl">Tudatosság</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Méri, hogy a munkavállalók mennyire ismerik az EAP-t, tudják-e, hogyan és mire használható.
                </p>
              </CardContent>
            </Card>

            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <Users className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">02</CardTitle>
                </div>
                <CardTitle className="text-xl">Használat</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Értékeli a tényleges igénybevételt és a használat mintázatait – hol és miért aktivizálódnak az emberek.
                </p>
              </CardContent>
            </Card>

            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <Heart className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">03</CardTitle>
                </div>
                <CardTitle className="text-xl">Bizalom & Hajlandóság</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Vizsgálja a programba vetett bizalmat és a segítségkérésre való hajlandóságot – az anonimitás érzésétől a vezetői példáig.
                </p>
              </CardContent>
            </Card>

            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <TrendingUp className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">04</CardTitle>
                </div>
                <CardTitle className="text-xl">Hatás</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Méri a program valós hatását a jóllétre és a szervezeti működésre: hiányzás, fluktuáció, elégedettség, ajánlási szándék.
                </p>
              </CardContent>
            </Card>
          </div>
        </section>

        {/* Benefits */}
        <section className="mb-20">
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {pulseBenefits.map((benefit, index) => (
              <Card key={index} className="hover-scale">
                <CardHeader>
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                    <benefit.icon className="h-6 w-6 text-primary" />
                  </div>
                  <CardTitle className="text-lg">{benefit.title}</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-sm text-muted-foreground">
                    {benefit.description}
                  </p>
                </CardContent>
              </Card>
            ))}
          </div>
        </section>

        {/* Final CTA Section */}
        <section className="mb-20 text-center py-16 bg-gradient-to-r from-primary/10 via-primary/5 to-primary/10 rounded-2xl">
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Készen áll az EAP program fejlesztésére?
          </h2>
          <p className="text-lg text-muted-foreground mb-8 max-w-2xl mx-auto">
            Kezdje el az EAP Pulse használatát még ma, és tapasztalja meg az adatalapú döntéshozatal előnyeit.
          </p>
          <div className="flex gap-4 justify-center flex-wrap">
            <Button 
              size="lg" 
              onClick={() => navigate('/auth')}
              className="bg-primary hover:bg-primary/90"
            >
              Regisztráció
            </Button>
            <Button 
              size="lg" 
              variant="outline"
              onClick={() => navigate('/arak')}
            >
              Árak megtekintése
            </Button>
          </div>
        </section>

        {/* FAQ Section */}
        <section className="mb-20">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Gyakran Ismételt Kérdések</h2>
          </div>
          
          <Accordion type="single" collapsible className="max-w-3xl mx-auto">
            <AccordionItem value="item-1">
              <AccordionTrigger className="text-left">
                Az EAP Pulse helyettesíti az EAP-szolgáltatót?
              </AccordionTrigger>
              <AccordionContent>
                Nem. A Pulse a meglévő EAP-ot egészíti ki extra adatokkal és riportokkal.
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="item-2">
              <AccordionTrigger className="text-left">
                Mennyi idő a bevezetés?
              </AccordionTrigger>
              <AccordionContent>
                Általában 15–30 perc a kezdeti beállítás, utána a kiküldések és riportok automatizálhatók.
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="item-3">
              <AccordionTrigger className="text-left">
                Anonim a válaszadás?
              </AccordionTrigger>
              <AccordionContent>
                Igen. A válaszok anonimek, az eredmények összesítve jelennek meg.
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="item-4">
              <AccordionTrigger className="text-left">
                Testre szabható a kérdőív?
              </AccordionTrigger>
              <AccordionContent>
                Igen. Az előre definiált kérdések mellett saját kérdéseket is hozzáadhatsz.
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="item-5">
              <AccordionTrigger className="text-left">
                Elérhető white-label verzió?
              </AccordionTrigger>
              <AccordionContent>
                Igen, EAP-szolgáltatók számára egyedi arculati beállításokat és API-integrációt is kínálunk.
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </section>

      </main>
    </div>
  );
};

export default Bemutatkozas;
