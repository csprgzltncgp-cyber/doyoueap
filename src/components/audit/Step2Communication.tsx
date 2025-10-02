import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { InfoIcon } from "lucide-react";

interface Step2Props {
  communicationText: string;
  onCommunicationTextChange: (text: string) => void;
  accessMode: string;
  programName: string;
  companyName: string;
  onNext: () => void;
  onBack: () => void;
}

export const Step2Communication = ({
  communicationText,
  onCommunicationTextChange,
  accessMode,
  programName,
  companyName,
  onNext,
  onBack,
}: Step2Props) => {
  const getDefaultCommunicationText = () => {
    const program = programName || 'DoYouEAP';
    const company = companyName || '[Cég neve]';
    
    if (accessMode === 'tokenes') {
      return `Tárgy: Segítsd jobbá tenni a ${program} programot!
Helló!

A ${program} azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:

Link

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a ${program} programot!

Üdvözlettel,
HR osztály`;
    } else if (accessMode === 'qr_code') {
      return `Segítsd jobbá tenni a ${program} programot!

A ${program} azért van, hogy támogasson – akár stresszről, jogi vagy pénzügyi kérdésekről, akár pszichológiai segítségről van szó.

Most rajtad a sor: töltsd ki rövid kérdőívünket, és mondd el a véleményed!

QR kód

Néhány perc az egész
Teljesen anonim
Csak statisztikai céllal használjuk

Köszönjük, hogy segítesz fejleszteni a ${program} programot!`;
    } else {
      return `Helló!

A ${program} azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:

Link

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a ${program} programot!

Üdvözlettel,
HR osztály`;
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

      <Alert>
        <InfoIcon className="h-4 w-4" />
        <AlertDescription>
          A szövegben automatikusan behelyettesítettük a program nevét ({programName || 'DoYouEAP'}).
        </AlertDescription>
      </Alert>

      <Card>
        <CardHeader>
          <CardTitle>
            {accessMode === 'tokenes' ? 'Email sablon' : 
             accessMode === 'qr_code' ? 'Plakát szöveg' : 
             'Belső kommunikációs szöveg'}
          </CardTitle>
          <CardDescription>
            A szöveg szabadon szerkeszthető.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            <Label htmlFor="communication">Üzenet szövege</Label>
            <Textarea
              id="communication"
              value={communicationText || getDefaultCommunicationText()}
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
