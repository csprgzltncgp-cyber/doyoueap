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
  hasGift: boolean;
  onNext: () => void;
  onBack: () => void;
}

export const Step2Communication = ({
  communicationText,
  onCommunicationTextChange,
  accessMode,
  programName,
  hasGift,
  onNext,
  onBack,
}: Step2Props) => {
  const getArticle = (word: string) => {
    const firstChar = word.charAt(0).toLowerCase();
    const vowels = ['a', 'á', 'e', 'é', 'i', 'í', 'o', 'ó', 'ö', 'ő', 'u', 'ú', 'ü', 'ű'];
    return vowels.includes(firstChar) ? 'az' : 'a';
  };

  const getDefaultCommunicationText = () => {
    const program = programName || 'EAP';
    const article = getArticle(program);
    
    if (accessMode === 'tokenes') {
      if (hasGift) {
        return `Tárgy: Töltsd ki a kérdőívet és nyerj! – Segítsd jobbá tenni az EAP programot

Helló!

Most nemcsak segíthetsz jobbá tenni a vállalatod EAP programját, de még nyerhetsz is!

A kérdőív kitöltői között ajándékot sorsolunk ki – mindössze néhány percet kell rászánnod, és máris részt veszel a sorsoláson.

Az EAP program azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Most szeretnénk megtudni, hogyan tudnánk még jobban a Te igényeidre szabni a programot.

Kérjük, töltsd ki rövid kérdőívünket az alábbi linken:

[Kérdőív megnyitása]

A kitöltés teljesen anonim, a válaszokat kizárólag statisztikai célokra használjuk. A felmérés végén megadhatsz egy e-mail-címet, ha szeretnéd, hogy értesítsünk, ha Te leszel a nyertes. A HR osztály a sorsolás után a nyertes sorszámot is közzéteszi, így e-mail megadása nélkül is megtudhatod, ha szerencséd volt.

Köszönjük, hogy segítesz továbbfejleszteni az EAP programot – és sok sikert a sorsoláshoz! 

Üdvözlettel,
HR osztály`;
      } else {
        return `Tárgy: Segítsd jobbá tenni a ${program} programot!
Helló!

${article.charAt(0).toUpperCase() + article.slice(1)} ${program} program azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:

Link

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a ${program} programot!

Üdvözlettel,
HR osztály`;
      }
    } else if (accessMode === 'qr_code') {
      if (hasGift) {
        return `Segítsd jobbá tenni az EAP programot! Töltsd ki egy rövid kérdőívet és nyerj!

Az EAP program azért van, hogy támogasson – akár jogi vagy pénzügyi kérdésekről, akár pszichológiai segítségről van szó. Töltsd ki rövid kérdőívünket, mondd el a véleményed a programról, és nyerj értékes ajándékot!

Olvasd be a QR-kódot és kezdheted is!

QR kód

- Néhány perc az egész
- Teljesen anonim
- Csak statisztikai célra használjuk
- A kitöltők között ajándékot sorsolunk ki!

Köszönjük, hogy segítesz fejleszteni az EAP programot – és sok szerencsét a sorsoláshoz!`;
      } else {
        return `Segítsd jobbá tenni a ${program} programot!

${article.charAt(0).toUpperCase() + article.slice(1)} ${program} program azért van, hogy támogasson – akár stresszről, jogi vagy pénzügyi kérdésekről, akár pszichológiai segítségről van szó.

Most rajtad a sor: töltsd ki rövid kérdőívünket, és mondd el a véleményed!

QR kód

Néhány perc az egész
Teljesen anonim
Csak statisztikai céllal használjuk

Köszönjük, hogy segítesz fejleszteni a ${program} programot!`;
      }
    } else {
      if (hasGift) {
        return `Helló!

Most nemcsak segíthetsz jobbá tenni a vállalatod EAP programját, de ajándékot is nyerhetsz! 

A kérdőív kitöltői között értékes nyereményt sorsolunk ki – a részvétel mindössze néhány percet vesz igénybe. Az EAP program azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről.

Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Töltsd ki a rövid kérdőívet itt:

[Link]

A válaszadás teljesen anonim, az eredményeket kizárólag összesítve, statisztikai célokra használjuk. A felmérés végén megadhatsz egy e-mail-címet, ha szeretnéd, hogy értesítsünk, ha Te leszel a nyertes. A HR osztály a sorsolás után a nyertes sorszámot is közzéteszi, így e-mail megadása nélkül is megtudhatod, ha szerencséd volt.

Köszönjük, hogy segítesz továbbfejleszteni az EAP programot – és sok sikert a sorsoláshoz! 

Üdvözlettel,
HR osztály`;
      } else {
        return `Helló!

${article.charAt(0).toUpperCase() + article.slice(1)} ${program} program azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:

Link

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a ${program} programot!

Üdvözlettel,
HR osztály`;
      }
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
          A szövegben automatikusan behelyettesítettük a program nevét ({programName || 'EAP'}).
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