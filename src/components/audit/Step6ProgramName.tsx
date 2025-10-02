import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

interface Step6Props {
  programName: string;
  companyName: string;
  eapProgramUrl: string;
  onProgramNameChange: (name: string) => void;
  onCompanyNameChange: (name: string) => void;
  onEapProgramUrlChange: (url: string) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step6ProgramName = ({
  programName,
  companyName,
  eapProgramUrl,
  onProgramNameChange,
  onCompanyNameChange,
  onEapProgramUrlChange,
  onNext,
  onBack,
}: Step6Props) => {
  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Alapadatok</CardTitle>
          <CardDescription>Add meg a cég nevét és a program alapinformációit</CardDescription>
          <div className="space-y-3 mt-4 text-foreground">
            <p className="leading-relaxed">
              Add meg az EAP program cégeden belüli elnevezését (maximum 60 karakter).
            </p>
            <p className="leading-relaxed">
              Ez az elnevezés a kérdőív legelső kérdésében és a kommunikációs szövegekben fog megjelenni.
            </p>
            <p className="leading-relaxed text-sm">
              Ha üresen hagyod, akkor „DoYouEAP" lesz az alapértelmezett elnevezés.
            </p>
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="company-name">Cég neve *</Label>
            <Input
              id="company-name"
              value={companyName}
              onChange={(e) => onCompanyNameChange(e.target.value)}
              placeholder="pl. Példa Kft."
              required
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="program-name">Program neve</Label>
            <Input
              id="program-name"
              value={programName}
              onChange={(e) => onProgramNameChange(e.target.value)}
              maxLength={60}
              placeholder="pl. DoYouEAP, Dolgozói Támogatási Program"
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="program-url">Program weboldala</Label>
            <Input
              id="program-url"
              type="url"
              value={eapProgramUrl}
              onChange={(e) => onEapProgramUrlChange(e.target.value)}
              placeholder="https://doyoueap.hu"
            />
            <p className="text-sm text-muted-foreground">
              Erre a weboldalra irányítjuk azokat a munkavállalókat, akik még nem ismerik a programot.
            </p>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button onClick={onNext} disabled={!companyName.trim()}>
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
