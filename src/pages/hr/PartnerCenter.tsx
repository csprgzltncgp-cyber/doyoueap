import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2 } from 'lucide-react';

const PartnerCenter = () => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-3xl font-bold mb-2">Partner Központ</h2>
        <p className="text-muted-foreground">
          Partnercégek kezelése és felmérések menedzselése
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Building2 className="h-5 w-5" />
            Partner Központ
          </CardTitle>
          <CardDescription>
            Ez a funkció hamarosan elérhető lesz az Enterprise csomag tagjai számára
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground">
            A Partner Központ lehetővé teszi majd, hogy saját cégeket vigyen fel,
            felméréseket indítson és kezelje ezeket a felméréseket.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default PartnerCenter;
