import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CheckCircle2, TrendingUp, Users, Award, Shield, Zap, Target, BarChart3 } from 'lucide-react';
import logo from '@/assets/logo.png';
import eapPulseTeam from '@/assets/eap-pulse-team.jpg';
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

      {/* Hero Section */}
      <section className="relative h-[600px] flex items-center justify-center overflow-hidden">
        <div className="absolute inset-0">
          <img 
            src={eapPulseTeam} 
            alt="EAP Pulse" 
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent" />
        </div>
        <div className="relative max-w-7xl mx-auto px-4 text-white">
          <h1 className="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
            Miért az EAP Pulse?
          </h1>
          <p className="text-xl md:text-2xl mb-8 max-w-2xl animate-fade-in">
            A következő generációs EAP értékelési platform, amely forradalmasítja a munkavállalói támogató programok hatékonyságmérését.
          </p>
          <Button 
            size="lg" 
            onClick={() => navigate('/arak')}
            className="bg-primary hover:bg-primary/90 text-primary-foreground animate-scale-in"
          >
            Fedezze fel a lehetőségeket
          </Button>
        </div>
      </section>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-16">
        
        {/* EAP Pulse Introduction */}
        <section className="mb-20">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Bemutatkozik az EAP Pulse</h2>
            <p className="text-xl text-muted-foreground max-w-3xl mx-auto">
              Innovatív platform adatalapú döntésekhez és mérhető eredményekhez
            </p>
          </div>
          
          <div className="grid md:grid-cols-3 gap-8">
            <Card className="hover-scale">
              <CardHeader>
                <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                  <BarChart3 className="h-6 w-6 text-primary" />
                </div>
                <CardTitle>Adatalapú döntések</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Valós idejű adatok és elemzések, amelyek segítenek megérteni az EAP program hatékonyságát és ROI-ját.
                </p>
              </CardContent>
            </Card>

            <Card className="hover-scale">
              <CardHeader>
                <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                  <Zap className="h-6 w-6 text-primary" />
                </div>
                <CardTitle>Egyszerű használat</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Felhasználóbarát felület, amely megkönnyíti az adatok gyűjtését és értelmezését minimális IT erőforrással.
                </p>
              </CardContent>
            </Card>

            <Card className="hover-scale">
              <CardHeader>
                <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                  <Target className="h-6 w-6 text-primary" />
                </div>
                <CardTitle>Rugalmas konfiguráció</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Testre szabható kérdőívek és értékelési módszerek a szervezet egyedi igényei szerint.
                </p>
              </CardContent>
            </Card>
          </div>
        </section>

        {/* 4Score Methodology */}
        <section className="mb-20">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">4Score módszertan</h2>
            <p className="text-xl text-muted-foreground max-w-3xl mx-auto">
              Komplex értékelési rendszer négy kulcsfontosságú dimenzióban
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <TrendingUp className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">01</CardTitle>
                </div>
                <CardTitle className="text-xl">Tudatosság</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Méri, hogy a munkavállalók mennyire ismerik és értik az EAP program létezését és lehetőségeit.
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
                  Értékeli a program tényleges igénybevételét és a szolgáltatások kihasználtságát.
                </p>
              </CardContent>
            </Card>

            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <CheckCircle2 className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">03</CardTitle>
                </div>
                <CardTitle className="text-xl">Bizalom</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Vizsgálja a munkavállalók bizalmát a programban és a szolgáltató diszkréciójában.
                </p>
              </CardContent>
            </Card>

            <Card className="bg-gradient-to-br from-primary/10 to-primary/5 border-primary/20 hover-scale">
              <CardHeader>
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <Award className="h-5 w-5 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl">04</CardTitle>
                </div>
                <CardTitle className="text-xl">Hatás</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">
                  Méri a program valós hatását a munkavállalók jólétére és produktivitására.
                </p>
              </CardContent>
            </Card>
          </div>
        </section>

        {/* Corporate Benefits */}
        <section className="mb-20">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Vállalati előnyök</h2>
            <p className="text-xl text-muted-foreground max-w-3xl mx-auto">
              Miért válassza az EAP Pulse-t a szervezete?
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-8">
            <div className="space-y-6">
              <div className="flex gap-4">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <BarChart3 className="h-6 w-6 text-primary" />
                  </div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold mb-2">ROI növelés</h3>
                  <p className="text-muted-foreground">
                    Mérhető eredmények, amelyek segítenek igazolni az EAP programba való befektetés megtérülését és értékét.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <Target className="h-6 w-6 text-primary" />
                  </div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold mb-2">Adatvezérelt stratégia</h3>
                  <p className="text-muted-foreground">
                    Valós adatok alapján optimalizálhatja az EAP programot és a kommunikációs stratégiát.
                  </p>
                </div>
              </div>
            </div>

            <div className="space-y-6">
              <div className="flex gap-4">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <Zap className="h-6 w-6 text-primary" />
                  </div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold mb-2">Gyors implementáció</h3>
                  <p className="text-muted-foreground">
                    Azonnal elindítható platform minimális IT erőforrás igénnyel és technikai támogatással.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <Shield className="h-6 w-6 text-primary" />
                  </div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold mb-2">Megfelelés és biztonság</h3>
                  <p className="text-muted-foreground">
                    GDPR-kompatibilis adatkezelés és teljes anonimitás a munkavállalók számára garantálva.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* EAP Providers Section */}
        <section className="mb-20">
          <Card className="bg-gradient-to-br from-primary/5 to-background border-primary/20">
            <CardHeader className="text-center pb-8">
              <CardTitle className="text-4xl mb-4">Ajánlat EAP szolgáltatók számára</CardTitle>
              <CardDescription className="text-lg">
                Partner program szolgáltatóknak - White-label megoldás a versenyelőnyért
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-8">
              <div className="grid md:grid-cols-3 gap-6">
                <div className="text-center">
                  <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <CheckCircle2 className="h-8 w-8 text-primary" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">White-label megoldás</h3>
                  <p className="text-sm text-muted-foreground">
                    Saját márkával és arculattal használhatja a platformot, erősítve brand-jét.
                  </p>
                </div>

                <div className="text-center">
                  <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Award className="h-8 w-8 text-primary" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">Versenyelőny</h3>
                  <p className="text-sm text-muted-foreground">
                    Adatalapú értékelés, ami megkülönbözteti Önt a konkurenciától.
                  </p>
                </div>

                <div className="text-center">
                  <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Users className="h-8 w-8 text-primary" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">Ügyfélmegtartás</h3>
                  <p className="text-sm text-muted-foreground">
                    Rendszeres értékelések növelik az ügyfélelégedettséget.
                  </p>
                </div>
              </div>

              <div className="text-center pt-4">
                <Button 
                  size="lg" 
                  onClick={() => navigate('/arak')}
                  className="bg-primary hover:bg-primary/90"
                >
                  Ismerje meg a partner programot
                </Button>
              </div>
            </CardContent>
          </Card>
        </section>

        {/* Final CTA Section */}
        <section className="text-center py-16 bg-gradient-to-r from-primary/10 via-primary/5 to-primary/10 rounded-2xl">
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

      </main>
    </div>
  );
};

export default Bemutatkozas;
