import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, CheckCircle2, TrendingUp, Users, Award } from 'lucide-react';
import logo from '@/assets/doyoueap-logo.png';
import { useAuth } from '@/hooks/useAuth';

const Bemutatkozas = () => {
  const navigate = useNavigate();
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState<'eap-pulse' | '4score' | 'vallalati' | 'eap-szolgaltatok'>('eap-pulse');

  const tabs = [
    { id: 'eap-pulse' as const, label: 'Bemutatkozik az EAP Pulse!' },
    { id: '4score' as const, label: '4Score módszertan' },
    { id: 'vallalati' as const, label: 'Vállalati előnyök' },
    { id: 'eap-szolgaltatok' as const, label: 'Ajánlat EAP szolgáltatók számára' }
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
                className="text-sm hover:text-primary transition-colors"
              >
                Magazin
              </button>
              <button
                onClick={() => navigate('/bemutatkozas')}
                className="text-sm text-primary font-medium transition-colors"
              >
                Bemutatkozás
              </button>
              <button
                onClick={() => navigate('/arak')}
                className="text-sm hover:text-primary transition-colors"
              >
                Árak és Csomagok
              </button>
              {user && (
                <button
                  onClick={() => navigate('/')}
                  className="text-sm hover:text-primary transition-colors"
                >
                  Főoldal
                </button>
              )}
            </nav>
          </div>
          <div className="flex items-center gap-4">
            {user ? (
              <Button onClick={() => navigate('/?section=eap-pulse')}>
                Dashboard
              </Button>
            ) : (
              <Button onClick={() => navigate('/auth')}>
                Bejelentkezés
              </Button>
            )}
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-12">
        <Button
          variant="ghost"
          onClick={() => navigate('/')}
          className="mb-6"
        >
          <ArrowLeft className="mr-2 h-4 w-4" />
          Vissza a főoldalra
        </Button>

        <h1 className="text-4xl font-bold mb-8">Bemutatkozás</h1>

        {/* Tabs */}
        <div className="flex gap-4 mb-8 overflow-x-auto pb-2">
          {tabs.map((tab) => (
            <Button
              key={tab.id}
              variant={activeTab === tab.id ? 'default' : 'outline'}
              onClick={() => setActiveTab(tab.id)}
              className="whitespace-nowrap"
            >
              {tab.label}
            </Button>
          ))}
        </div>

        {/* Content based on active tab */}
        {activeTab === 'eap-pulse' && (
          <div className="space-y-8">
            <Card>
              <CardHeader>
                <CardTitle className="text-3xl">Bemutatkozik az EAP Pulse!</CardTitle>
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
          </div>
        )}

        {activeTab === '4score' && (
          <div className="space-y-8">
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
                <div className="grid md:grid-cols-2 gap-6">
                  <Card className="bg-muted/50">
                    <CardHeader>
                      <div className="flex items-center gap-3">
                        <div className="p-2 bg-primary/10 rounded-lg">
                          <TrendingUp className="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle className="text-xl">1. Tudatosság</CardTitle>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <p className="text-muted-foreground">
                        Méri, hogy a munkavállalók mennyire ismerik az EAP programot és szolgáltatásait.
                      </p>
                    </CardContent>
                  </Card>
                  <Card className="bg-muted/50">
                    <CardHeader>
                      <div className="flex items-center gap-3">
                        <div className="p-2 bg-primary/10 rounded-lg">
                          <Users className="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle className="text-xl">2. Használat</CardTitle>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <p className="text-muted-foreground">
                        Értékeli a program tényleges igénybevételét és a szolgáltatások használatának gyakoriságát.
                      </p>
                    </CardContent>
                  </Card>
                  <Card className="bg-muted/50">
                    <CardHeader>
                      <div className="flex items-center gap-3">
                        <div className="p-2 bg-primary/10 rounded-lg">
                          <CheckCircle2 className="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle className="text-xl">3. Bizalom & Hajlandóság</CardTitle>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <p className="text-muted-foreground">
                        Vizsgálja a munkavállalók bizalmát a programban és hajlandóságukat a szolgáltatások igénybevételére.
                      </p>
                    </CardContent>
                  </Card>
                  <Card className="bg-muted/50">
                    <CardHeader>
                      <div className="flex items-center gap-3">
                        <div className="p-2 bg-primary/10 rounded-lg">
                          <Award className="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle className="text-xl">4. Hatás</CardTitle>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <p className="text-muted-foreground">
                        Méri a program valós hatását a munkavállalók jólétére és a szervezeti teljesítményre.
                      </p>
                    </CardContent>
                  </Card>
                </div>
              </CardContent>
            </Card>
          </div>
        )}

        {activeTab === 'vallalati' && (
          <div className="space-y-8">
            <Card>
              <CardHeader>
                <CardTitle className="text-3xl">Vállalati előnyök</CardTitle>
                <CardDescription className="text-base">
                  Miért válassza az EAP Pulse-t a szervezete?
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="grid md:grid-cols-2 gap-6">
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
          </div>
        )}

        {activeTab === 'eap-szolgaltatok' && (
          <div className="space-y-8">
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
                  <Button size="lg" onClick={() => navigate('/arak')}>
                    Ismerje meg a partner programot
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        )}
      </main>
    </div>
  );
};

export default Bemutatkozas;
