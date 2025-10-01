import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

interface Step6Props {
  programName: string;
  companyName: string;
  onProgramNameChange: (name: string) => void;
  onCompanyNameChange: (name: string) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step6ProgramName = ({
  programName,
  companyName,
  onProgramNameChange,
  onCompanyNameChange,
  onNext,
  onBack,
}: Step6Props) => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Program adatok</h2>
        <p className="text-muted-foreground">
          Adja meg a cég nevét és a program elnevezését
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Cég neve</CardTitle>
          <CardDescription>
            A cég neve, amely számára az audittot végzi
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            <Label htmlFor="company-name">Cég neve *</Label>
            <Input
              id="company-name"
              value={companyName}
              onChange={(e) => onCompanyNameChange(e.target.value)}
              placeholder="pl. Példa Kft."
              maxLength={100}
              required
            />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Program elnevezése</CardTitle>
          <CardDescription>
            A program saját elnevezése a cégnél (max. 60 karakter)
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            <Label htmlFor="program-name">Program neve</Label>
            <Input
              id="program-name"
              value={programName}
              onChange={(e) => onProgramNameChange(e.target.value)}
              placeholder="pl. Számíthatsz Ránk! vagy DoYouEAP"
              maxLength={60}
            />
            <p className="text-sm text-muted-foreground">
              Ez az elnevezés jelenik meg az első kérdésben: „Tudta, hogy a cégénél elérhető a <strong>{programName || 'DoYouEAP'}</strong> program?"
            </p>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button onClick={onNext} disabled={!companyName}>
          Következő lépés
        </Button>
      </div>
    </div>
  );
};