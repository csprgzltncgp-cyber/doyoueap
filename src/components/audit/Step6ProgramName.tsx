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
      <Card>
        <CardHeader>
          <CardTitle>Program neve</CardTitle>
          <CardDescription>
            Adja meg az EAP program cégén belüli elnevezését (maximum 60 karakter). 
            Ez az elnevezés csak a kérdőív legelső kérdésében fog megjelenni: „Tudta, hogy a cégénál elérhető a &lt;program neve&gt; program?" 
            A kérdőív további részében mindig általános „program" megnevezéssel hivatkozunk rá, így biztosítva az egységes és könnyen érthető kommunikációt.
            Ha üresen hagyja, akkor „EAP" lesz az alapértelmezett elnevezés.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Input
            id="program-name"
            value={programName}
            onChange={(e) => onProgramNameChange(e.target.value)}
            maxLength={60}
          />
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button onClick={onNext}>
          Következő lépés
        </Button>
      </div>
    </div>
  );
};