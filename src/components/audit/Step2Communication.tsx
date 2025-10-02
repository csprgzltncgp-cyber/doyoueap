import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

interface Step2Props {
  communicationText: string;
  onCommunicationTextChange: (text: string) => void;
  accessMode: string;
  onNext: () => void;
  onBack: () => void;
}

export const Step2Communication = ({
  communicationText,
  onCommunicationTextChange,
  accessMode,
  onNext,
  onBack,
}: Step2Props) => {
  const getTemplate = () => {
    if (accessMode === 'tokenes') {
      return `Kedves {{név}}!

Céged biztosítja számodra a {{program_név}} programot – egy olyan szolgáltatást, amely támogatást nyújt a mindennapok kihívásaiban. Legyen szó stresszről, jogi vagy pénzügyi kérdésekről, esetleg pszichológiai támogatásról, szakembereink mindig rendelkezésedre állnak.

Most szeretnénk, ha Te is segítenél abban, hogy a program még jobb legyen!

Ehhez csak annyit kell tenned, hogy kitöltöd rövid kérdőívünket:
{{audit_link}}

A kitöltés mindössze néhány percet vesz igénybe, teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célra használjuk fel.

Előre is köszönjük, hogy segítesz a {{program_név}} fejlesztésében!

Üdvözlettel,
{{cég_név}}`;
    } else if (accessMode === 'qr_code') {
      return `Kedves Kollégák!

Segíts még jobbá tenni a {{program_név}} programot!

Biztosan tudjátok, hogy cégünk biztosítja számotokra a {{program_név}} programot – egy olyan támogatási szolgáltatást, amely segítséget nyújt különféle munkahelyi és magánéleti helyzetekben. Legyen szó stresszről, jogi, pénzügyi kérdésekről vagy pszichológiai támogatásról, szakembereink rendelkezésetekre állnak.

Most itt a lehetőség, hogy pár kérdés megválaszolásával segítsetek nekünk még jobbá tenni ezt a programot!

Szkenneld be a QR kódot és töltsd ki a kérdőívet – pár perc az egész, és tényleg sokat jelent nekünk, ha segítetek ebben. A kitöltés és a részvétel teljesen anonim, semmilyen regisztráció nem szükséges. A válaszokat csak statisztikai adatként kezeljük, összesítve.

Köszönjük!`;
    } else {
      return `Kedves Kollégák!

Segítsetek még jobbá tenni a {{program_név}} programot!

Biztosan tudjátok, hogy cégünk biztosítja számotokra a {{program_név}} programot – egy olyan támogatási szolgáltatást, amely segítséget nyújt különféle munkahelyi és magánéleti helyzetekben. Legyen szó stresszről, jogi, pénzügyi kérdésekről vagy pszichológiai támogatásról, szakembereink rendelkezésetekre állnak.

Most itt a lehetőség, hogy pár kérdés megválaszolásával segítsetek nekünk még jobbá tenni ezt a programot!

Kattintsatok a linkre és töltsétek ki a kérdőívet:
{{audit_link}}

Pár perc az egész, és tényleg sokat jelent nekünk, ha segítetek ebben. A kitöltés és a részvétel teljesen anonim, semmilyen regisztráció nem szükséges. A válaszokat csak statisztikai adatként kezeljük, összesítve.

Köszönjük a részvételt!`;
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Kommunikációs támogatás</h2>
        <p className="text-muted-foreground">
          Testreszabhatja a munkavállalóknak küldött kommunikációt
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>
            {accessMode === 'tokenes' ? 'Email sablon' : 
             accessMode === 'qr_code' ? 'Plakát szöveg' : 
             'Belső kommunikációs szöveg'}
          </CardTitle>
          <CardDescription>
            {accessMode === 'tokenes' 
              ? 'Használhat változókat: {{név}}, {{cég_név}}, {{program_név}}, {{audit_link}}'
              : 'Használhat változókat: {{cég_név}}, {{program_név}}, {{audit_link}}'}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            <Label htmlFor="communication">Üzenet szövege</Label>
            <Textarea
              id="communication"
              value={communicationText || getTemplate()}
              onChange={(e) => onCommunicationTextChange(e.target.value)}
              rows={10}
              className="font-mono text-sm"
            />
          </div>
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