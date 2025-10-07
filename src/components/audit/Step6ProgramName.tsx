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
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Program adatai</CardTitle>
          <div className="space-y-3 mt-4 text-foreground">
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
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="program-name">Program neve</Label>
            <Input
              id="program-name"
              value={programName}
              onChange={(e) => onProgramNameChange(e.target.value)}
              maxLength={60}
              placeholder="pl. Munkavállalói Támogatási Program"
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="program-url">Program weboldala</Label>
            <Input
              id="program-url"
              type="url"
              value={eapProgramUrl}
              onChange={(e) => onEapProgramUrlChange(e.target.value)}
              placeholder="https://"
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
        <Button 
          onClick={onNext}
          style={{
            backgroundColor: '#000000',
            color: 'white'
          }}
          className="hover:opacity-90"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
