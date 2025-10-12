import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

interface Step6Props {
  programName: string;
  eapProgramUrl: string;
  onProgramNameChange: (name: string) => void;
  onEapProgramUrlChange: (url: string) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step6ProgramName = ({
  programName,
  eapProgramUrl,
  onProgramNameChange,
  onEapProgramUrlChange,
  onNext,
  onBack,
}: Step6Props) => {
  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Program adatai</h2>
        <p className="text-muted-foreground text-lg">
          Add meg az EAP program alapvető információit
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="text-xl">Program elnevezése</CardTitle>
          <CardDescription className="text-base space-y-3 mt-4 text-foreground">
            <p className="leading-relaxed">
              Add meg az EAP program cégeden belüli elnevezését (maximum 60 karakter).
            </p>
            <p className="leading-relaxed">
              Ez az elnevezés csak a kérdőív legelső kérdésében fog megjelenni:
            </p>
            <p className="italic leading-relaxed pl-4 border-l-2 border-primary/30">
              „Tudtad, hogy a munkahelyeden elérhető egy támogatási program, amit &lt;program neve&gt; néven ismerhetsz? 
              Ez a szolgáltatás segítséget nyújt neked és családodnak különböző munkahelyi vagy magánéleti kihívások kezeléséhez, 
              például stresszhelyzetekben, konfliktusok megoldásában vagy akár pénzügyi tanácsadásban is."
            </p>
            <p className="leading-relaxed">
              A kérdőív további részében mindig általános „program" megnevezéssel hivatkozunk rá, 
              így biztosítva az egységes és könnyen érthető kommunikációt.
            </p>
            <p className="leading-relaxed text-sm">
              Ha üresen hagyod, akkor „EAP" lesz az alapértelmezett elnevezés.
            </p>
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6 pt-6">
          <div className="space-y-2">
            <Label htmlFor="program-name" className="text-base font-medium">Program neve</Label>
            <Input
              id="program-name"
              value={programName}
              onChange={(e) => onProgramNameChange(e.target.value)}
              maxLength={60}
              placeholder="pl. Munkavállalói Támogatási Program"
              className="h-12 text-base"
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="program-url" className="text-base font-medium">Program weboldala</Label>
            <Input
              id="program-url"
              type="url"
              value={eapProgramUrl}
              onChange={(e) => onEapProgramUrlChange(e.target.value)}
              placeholder="https://"
              className="h-12 text-base"
            />
            <p className="text-sm text-muted-foreground">
              Erre a weboldalra irányítjuk azokat a munkavállalókat, akik még nem ismerik a programot.
            </p>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext}
          disabled={!programName}
          size="lg"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
