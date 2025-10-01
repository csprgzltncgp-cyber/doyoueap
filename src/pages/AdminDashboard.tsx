import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const AdminDashboard = () => {
  const { user, signOut } = useAuth();

  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">Admin Felület</h1>
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
                Ez az admin felület. Itt kezelheti a rendszer beállításait, felhasználókat,
                cikkeket és a teljes CMS-t.
              </p>
            </CardContent>
          </Card>

          <div className="grid md:grid-cols-3 gap-4">
            <Card>
              <CardHeader>
                <CardTitle>Felhasználók</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Felhasználók kezelése és szerepkörök hozzárendelése
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>CMS / Cikkek</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Magazin cikkek létrehozása és szerkesztése
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Beállítások</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Rendszer beállítások és konfiguráció
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;
