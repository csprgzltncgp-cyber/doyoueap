import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const HRDashboard = () => {
  const { user, signOut } = useAuth();

  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">HR Felület</h1>
          <Button onClick={signOut} variant="outline">
            Kijelentkezés
          </Button>
        </div>

        <div className="grid gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Üdvözöljük, {user?.email}</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-muted-foreground">
                Ez a HR felület. Itt láthatja a cég dolgozóinak adatait, statisztikákat,
                és kezelheti az EAP programot.
              </p>
            </CardContent>
          </Card>

          <div className="grid md:grid-cols-3 gap-4">
            <Card>
              <CardHeader>
                <CardTitle>Dashboard</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Statisztikák és grafikonok a dolgozók mentális egészségéről
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Dolgozók</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Dolgozók listája és egyéni adataik
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Beállítások</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Cég branding és egyedi színek beállítása
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HRDashboard;
