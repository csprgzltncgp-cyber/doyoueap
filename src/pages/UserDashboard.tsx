import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const UserDashboard = () => {
  const { user, signOut } = useAuth();

  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">Felhasználói Felület</h1>
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
                Ez a dolgozói felület. Itt töltheti ki a mentális egészség kérdőíveket,
                és hozzáférhet a támogató szolgáltatásokhoz.
              </p>
            </CardContent>
          </Card>

          <div className="grid md:grid-cols-3 gap-4">
            <Card>
              <CardHeader>
                <CardTitle>Kérdőívek</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Mentális egészség kérdőívek kitöltése
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Eredmények</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Korábbi kérdőívek eredményeinek megtekintése
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Támogatás</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Kapcsolatfelvétel szakemberekkel
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default UserDashboard;
