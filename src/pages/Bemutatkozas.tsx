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

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-12">
        <h1 className="text-4xl font-bold mb-12">Miért az EAP Pulse?</h1>

        {/* Section 1: EAP Pulse Introduction */}
        <div className="grid md:grid-cols-2 gap-8 mb-16">
          <Card>
            <CardHeader>
              <CardTitle className="text-3xl text-foreground animate-pulse-glow">Bemutatkozik az EAP Pulse!</CardTitle>
              <CardDescription className="text-base">
                A következő generációs EAP értékelési platform
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <p className="text-lg">
                Az EAP Pulse egy innovatív platform, amely forradalmasítja a munkavállalói támogató programok (EAP) értékelését és hatékonyságmérését.
              </p>
              <div className="space-y-4">
                <div className="flex gap-3">
                  <CheckCircle2 className="h-6 w-6 text-primary flex-shrink-0 mt-1" />
                  <div>
                    <h3 className="font-semibold mb-1">Adatalapú döntések</h3>
                    <p className="text-muted-foreground">Valós idejű adatok és elemzések, amelyek segítenek megérteni az EAP program hatékonyságát.</p>
                  </div>
                </div>
                <div className="flex gap-3">
                  <CheckCircle2 className="h-6 w-6 text-primary flex-shrink-0 mt-1" />
                  <div>
                    <h3 className="font-semibold mb-1">Egyszerű használat</h3>
                    <p className="text-muted-foreground">Felhasználóbarát felület, amely megkönnyíti az adatok gyűjtését és értelmezését.</p>
                  </div>
                </div>
                <div className="flex gap-3">
                  <CheckCircle2 className="h-6 w-6 text-primary flex-shrink-0 mt-1" />
                  <div>
                    <h3 className="font-semibold mb-1">Rugalmas konfiguráció</h3>
                    <p className="text-muted-foreground">Testre szabható kérdőívek és értékelési módszerek a szervezet egyedi igényei szerint.</p>
                  </div>
                </div>
              </div>
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
        <div className="grid md:grid-cols-2 gap-8 mb-16">
          <Card>
            <CardHeader>
              <CardTitle className="text-3xl">4Score módszertan</CardTitle>
              <CardDescription className="text-base">
                Komplex értékelési rendszer négy kulcsfontosságú dimenzióban
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <p className="text-lg">
                A 4Score módszertan egy átfogó értékelési keretrendszer, amely négy alapvető területen méri az EAP program hatékonyságát.
              </p>
              <div className="grid grid-cols-2 gap-4 bg-gradient-to-br from-[#3abef9]/30 to-[#ff66ff]/30 p-4 rounded-lg">
                <Card className="bg-background/80 backdrop-blur-sm">
                  <CardHeader className="p-4">
                    <div className="flex items-center gap-2">
                      <div className="p-1.5 bg-primary/10 rounded-lg">
                        <TrendingUp className="h-5 w-5 text-primary" />
                      </div>
                      <CardTitle className="text-base">1. Tudatosság</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent className="p-4 pt-0">
                    <p className="text-xs text-muted-foreground">
                      Méri, hogy a munkavállalók mennyire ismerik az EAP programot.
                    </p>
                  </CardContent>
                </Card>
                <Card className="bg-background/80 backdrop-blur-sm">
                  <CardHeader className="p-4">
                    <div className="flex items-center gap-2">
                      <div className="p-1.5 bg-primary/10 rounded-lg">
                        <Users className="h-5 w-5 text-primary" />
                      </div>
                      <CardTitle className="text-base">2. Használat</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent className="p-4 pt-0">
                    <p className="text-xs text-muted-foreground">
                      Értékeli a program tényleges igénybevételét.
                    </p>
                  </CardContent>
                </Card>
                <Card className="bg-background/80 backdrop-blur-sm">
                  <CardHeader className="p-4">
                    <div className="flex items-center gap-2">
                      <div className="p-1.5 bg-primary/10 rounded-lg">
                        <CheckCircle2 className="h-5 w-5 text-primary" />
                      </div>
                      <CardTitle className="text-base">3. Bizalom</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent className="p-4 pt-0">
                    <p className="text-xs text-muted-foreground">
                      Vizsgálja a munkavállalók bizalmát a programban.
                    </p>
                  </CardContent>
                </Card>
                <Card className="bg-background/80 backdrop-blur-sm">
                  <CardHeader className="p-4">
                    <div className="flex items-center gap-2">
                      <div className="p-1.5 bg-primary/10 rounded-lg">
                        <Award className="h-5 w-5 text-primary" />
                      </div>
                      <CardTitle className="text-base">4. Hatás</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent className="p-4 pt-0">
                    <p className="text-xs text-muted-foreground">
                      Méri a program valós hatását a jólétre.
                    </p>
                  </CardContent>
                </Card>
              </div>
            </CardContent>
          </Card>
          <div className="rounded-lg overflow-hidden h-full">
            <img 
              src={fourScoreTeam} 
              alt="4Score módszertan csapatmunka" 
              className="w-full h-full object-cover"
            />
          </div>
        </div>

        {/* Section 3: Corporate Benefits */}
        <div className="grid md:grid-cols-2 gap-8 mb-16">
          <Card>
            <CardHeader>
              <CardTitle className="text-3xl">Vállalati előnyök</CardTitle>
              <CardDescription className="text-base">
                Miért válassza az EAP Pulse-t a szervezete?
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid gap-6">
                <div className="space-y-4">
                  <h3 className="text-xl font-semibold">ROI növelés</h3>
                  <p className="text-muted-foreground">
                    Mérhető eredmények, amelyek segítenek igazolni az EAP programba való befektetés megtérülését.
                  </p>
                </div>
                <div className="space-y-4">
                  <h3 className="text-xl font-semibold">Adatvezérelt stratégia</h3>
                  <p className="text-muted-foreground">
                    Valós adatok alapján optimalizálhatja az EAP programot és a kommunikációs stratégiát.
                  </p>
                </div>
                <div className="space-y-4">
                  <h3 className="text-xl font-semibold">Gyors implementáció</h3>
                  <p className="text-muted-foreground">
                    Azonnal elindítható platform minimális IT erőforrás igénnyel.
                  </p>
                </div>
                <div className="space-y-4">
                  <h3 className="text-xl font-semibold">Megfelelés és biztonság</h3>
                  <p className="text-muted-foreground">
                    GDPR-kompatibilis adatkezelés és teljes anonimitás a munkavállalók számára.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
          <div className="rounded-lg overflow-hidden h-full">
            <img 
              src={corporateBenefits} 
              alt="Vállalati előnyök megbeszélése" 
              className="w-full h-full object-cover"
            />
          </div>
        </div>

        {/* Section 4: EAP Providers */}
        <div className="grid md:grid-cols-2 gap-8">
          <Card>
            <CardHeader>
              <CardTitle className="text-3xl">Ajánlat EAP szolgáltatók számára</CardTitle>
              <CardDescription className="text-base">
                Partner program szolgáltatóknak
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <p className="text-lg">
                Az EAP Pulse kiváló lehetőség az EAP szolgáltatók számára, hogy értéket adjanak ügyfeleiknek és megkülönböztessék magukat a piacon.
              </p>
              <div className="space-y-4">
                <Card className="bg-primary/5 border-primary/20">
                  <CardHeader>
                    <CardTitle>White-label megoldás</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-muted-foreground">
                      Saját márkával és arculattal használhatja a platformot, így erősítve saját brand-jét.
                    </p>
                  </CardContent>
                </Card>
                <Card className="bg-primary/5 border-primary/20">
                  <CardHeader>
                    <CardTitle>Versenyelőny</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-muted-foreground">
                      Kínáljon adatalapú értékelést ügyfeleinek, ami megkülönbözteti Önt a konkurenciától.
                    </p>
                  </CardContent>
                </Card>
                <Card className="bg-primary/5 border-primary/20">
                  <CardHeader>
                    <CardTitle>Ügyfélmegtartás</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-muted-foreground">
                      A rendszeres értékelések és átlátható eredmények növelik az ügyfélelégedettséget és -megtartást.
                    </p>
                  </CardContent>
                </Card>
              </div>
              <div className="pt-6">
                <Button size="lg" onClick={() => navigate('/arak')} className="bg-[#050c9c] hover:bg-[#050c9c]/90">
                  Ismerje meg a partner programot
                </Button>
              </div>
            </CardContent>
          </Card>
          <div className="rounded-lg overflow-hidden h-full">
            <img 
              src={eapProviders} 
              alt="EAP szolgáltatók együttműködése" 
              className="w-full h-full object-cover"
            />
          </div>
        </div>
      </main>
    </div>
  );
};

export default Bemutatkozas;
