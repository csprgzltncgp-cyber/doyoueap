import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

const Index = () => {
  const { user, role, loading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!loading && user && role) {
      // Redirect authenticated users to their dashboard
      switch (role) {
        case 'admin':
          navigate('/admin');
          break;
        case 'hr':
          navigate('/hr');
          break;
        case 'user':
          navigate('/user');
          break;
      }
    }
  }, [user, role, loading, navigate]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Hero Section */}
      <section className="py-20 px-4">
        <div className="max-w-7xl mx-auto text-center">
          <h1 className="text-5xl font-bold mb-6">DoYouEAP</h1>
          <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
            Employee Assistance Program (EAP) platform - Dolgozói mentális egészség támogató rendszer
          </p>
          <Button onClick={() => navigate('/auth')} size="lg">
            Bejelentkezés / Regisztráció
          </Button>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-16 px-4 bg-muted/30">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-3xl font-bold text-center mb-12">Platformunk szolgáltatásai</h2>
          
          <div className="grid md:grid-cols-3 gap-8">
            <Card>
              <CardHeader>
                <CardTitle>Admin Felület</CardTitle>
                <CardDescription>Teljes körű rendszer kezelés</CardDescription>
              </CardHeader>
              <CardContent>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li>• Felhasználók és szerepkörök kezelése</li>
                  <li>• CMS és cikkek szerkesztése</li>
                  <li>• Rendszer beállítások</li>
                </ul>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>HR Felület</CardTitle>
                <CardDescription>Dolgozói statisztikák</CardDescription>
              </CardHeader>
              <CardContent>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li>• Dashboard grafikonokkal</li>
                  <li>• Dolgozók mentális egészség adatai</li>
                  <li>• Cég branding testreszabása</li>
                </ul>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Dolgozói Felület</CardTitle>
                <CardDescription>Személyes támogatás</CardDescription>
              </CardHeader>
              <CardContent>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li>• Mentális egészség kérdőívek</li>
                  <li>• Egyéni eredmények követése</li>
                  <li>• Szakember kapcsolatfelvétel</li>
                </ul>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Magazin Section */}
      <section className="py-16 px-4">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-3xl font-bold text-center mb-12">EAP Magazin</h2>
          <p className="text-center text-muted-foreground mb-8">
            Cikkek és információk a mentális egészségről, munkahelyi jóllétről és EAP programokról
          </p>
          <div className="text-center">
            <Button variant="outline">Cikkek böngészése (hamarosan)</Button>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Index;
