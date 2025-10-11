import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Code } from 'lucide-react';

const API = () => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-3xl font-bold mb-2">API</h2>
        <p className="text-muted-foreground">
          Enterprise API dokumentáció és integrációs lehetőségek
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Code className="h-5 w-5" />
            API Hozzáférés
          </CardTitle>
          <CardDescription>
            Ez a funkció hamarosan elérhető lesz az Enterprise csomag tagjai számára
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground">
            Az API dokumentáció és integrációs eszközök fejlesztés alatt állnak.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default API;
