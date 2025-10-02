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
      return `Helló!

A {{program_név}} azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:
{{audit_link}}

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a {{program_név}} programot!

Üdvözlettel,
{{cég_név}}`;
    } else if (accessMode === 'qr_code') {
      return `Segítsd jobbá tenni a {{program_név}} programot!

A {{program_név}} azért van, hogy támogasson – akár stresszről, jogi vagy pénzügyi kérdésekről, akár pszichológiai segítségről van szó.

Most rajtad a sor: töltsd ki rövid kérdőívünket, és mondd el a véleményed!

QR kód

Néhány perc az egész
Teljesen anonim
Csak statisztikai céllal használjuk

Köszönjük, hogy segítesz fejleszteni a {{program_név}} programot!`;
    } else {
      return `Helló!

A {{program_név}} azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:
{{audit_link}}

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a {{program_név}} programot!

Üdvözlettel,
{{cég_név}}`;
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
            Használhat változókat: {'{'}{'{'} cég_név {'}'}{'}'},  {'{'}{'{'} program_név {'}'}{'}'},  {'{'}{'{'} audit_link {'}'}{'}'} 
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