import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Download, Mail, LinkIcon, QrCode as QrCodeIcon } from 'lucide-react';
import { QRCodeSVG } from 'qrcode.react';

const CommunicationSupport = () => {
  const [programName, setProgramName] = useState('EAP');
  const [hasGift, setHasGift] = useState(false);
  
  const getArticle = (word: string) => {
    const firstChar = word.charAt(0).toLowerCase();
    const vowels = ['a', 'á', 'e', 'é', 'i', 'í', 'o', 'ó', 'ö', 'ő', 'u', 'ú', 'ü', 'ű'];
    return vowels.includes(firstChar) ? 'az' : 'a';
  };

  const getTokenesEmailText = () => {
    const program = programName || 'EAP';
    const article = getArticle(program);
    
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

[Link]

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a ${program} programot!

Üdvözlettel,
HR osztály`;
    }
  };

  const getPublicLinkText = () => {
    const program = programName || 'EAP';
    const article = getArticle(program);
    
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

[Link]

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a ${program} programot!

Üdvözlettel,
HR osztály`;
    }
  };

  const getQRCodeText = () => {
    const program = programName || 'EAP';
    const article = getArticle(program);
    
    if (hasGift) {
      return `Segítsd jobbá tenni az EAP programot! Töltsd ki egy rövid kérdőívet és nyerj!

Az EAP program azért van, hogy támogasson – akár jogi vagy pénzügyi kérdésekről, akár pszichológiai segítségről van szó. Töltsd ki rövid kérdőívünket, mondd el a véleményed a programról, és nyerj értékes ajándékot!

Olvasd be a QR-kódot és kezdheted is!

[QR kód]

- Néhány perc az egész
- Teljesen anonim
- Csak statisztikai célra használjuk
- A kitöltők között ajándékot sorsolunk ki!

Köszönjük, hogy segítesz fejleszteni az EAP programot – és sok szerencsét a sorsoláshoz!`;
    } else {
      return `Segítsd jobbá tenni a ${program} programot!

${article.charAt(0).toUpperCase() + article.slice(1)} ${program} program azért van, hogy támogasson – akár stresszről, jogi vagy pénzügyi kérdésekről, akár pszichológiai segítségről van szó.

Most rajtad a sor: töltsd ki rövid kérdőívünket, és mondd el a véleményed!

[QR kód]

Néhány perc az egész
Teljesen anonim
Csak statisztikai céllal használjuk

Köszönjük, hogy segítesz fejleszteni a ${program} programot!`;
    }
  };

  const handleDownloadText = (text: string, filename: string) => {
    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  const handleDownloadQR = () => {
    const svg = document.getElementById('qr-code-svg') as unknown as SVGElement;
    if (svg) {
      const svgData = new XMLSerializer().serializeToString(svg);
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const img = new Image();
      
      img.onload = () => {
        canvas.width = img.width;
        canvas.height = img.height;
        ctx?.drawImage(img, 0, 0);
        const url = canvas.toDataURL('image/png');
        const a = document.createElement('a');
        a.href = url;
        a.download = 'eap-pulse-qr-kod.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      };
      
      img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold mb-2">Kommunikációs támogatás</h1>
        <p className="text-muted-foreground">
          Készen álló szövegek és sablonok mindhárom hozzáférési módhoz
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Testreszabás</CardTitle>
          <CardDescription>
            Állítsd be a program nevét és az ajándéksorsolás meglétét
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label htmlFor="program-name">Program neve</Label>
            <Input
              id="program-name"
              value={programName}
              onChange={(e) => setProgramName(e.target.value)}
              placeholder="EAP"
              className="mt-2"
            />
          </div>
          <div className="flex items-center space-x-2">
            <input
              type="checkbox"
              id="has-gift"
              checked={hasGift}
              onChange={(e) => setHasGift(e.target.checked)}
              className="h-4 w-4"
            />
            <Label htmlFor="has-gift" className="cursor-pointer">
              Van ajándéksorsolás
            </Label>
          </div>
        </CardContent>
      </Card>

      <Tabs defaultValue="tokenes" className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="tokenes">
            <Mail className="mr-2 h-4 w-4" />
            Egyedi tokenes link
          </TabsTrigger>
          <TabsTrigger value="public">
            <LinkIcon className="mr-2 h-4 w-4" />
            Nyilvános link
          </TabsTrigger>
          <TabsTrigger value="qr">
            <QrCodeIcon className="mr-2 h-4 w-4" />
            QR kód
          </TabsTrigger>
        </TabsList>

        <TabsContent value="tokenes" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Email sablon - Egyedi tokenes link</CardTitle>
              <CardDescription>
                Ez az email sablon minden munkavállalónak személyre szóló linket küld
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="tokenes-text">Email szöveg</Label>
                <Textarea
                  id="tokenes-text"
                  value={getTokenesEmailText()}
                  readOnly
                  rows={20}
                  className="font-mono text-sm mt-2"
                />
              </div>
              <Button 
                onClick={() => handleDownloadText(getTokenesEmailText(), 'email-sablon-tokenes.txt')}
                className="w-full"
              >
                <Download className="mr-2 h-4 w-4" />
                Szöveg letöltése
              </Button>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="public" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Belső kommunikációs szöveg - Nyilvános link</CardTitle>
              <CardDescription>
                Ez a szöveg intraneten, hírlevélben vagy Teams-en keresztül osztható meg
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="public-text">Kommunikációs szöveg</Label>
                <Textarea
                  id="public-text"
                  value={getPublicLinkText()}
                  readOnly
                  rows={20}
                  className="font-mono text-sm mt-2"
                />
              </div>
              <Button 
                onClick={() => handleDownloadText(getPublicLinkText(), 'kommunikacio-nyilvanos-link.txt')}
                className="w-full"
              >
                <Download className="mr-2 h-4 w-4" />
                Szöveg letöltése
              </Button>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="qr" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Plakát szöveg - QR kód</CardTitle>
              <CardDescription>
                Ez a szöveg plakáton vagy szórólapon használható QR kóddal együtt
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="qr-text">Plakát szöveg</Label>
                <Textarea
                  id="qr-text"
                  value={getQRCodeText()}
                  readOnly
                  rows={15}
                  className="font-mono text-sm mt-2"
                />
              </div>
              <Button 
                onClick={() => handleDownloadText(getQRCodeText(), 'plakat-szoveg-qr.txt')}
                className="w-full"
              >
                <Download className="mr-2 h-4 w-4" />
                Szöveg letöltése
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Minta QR kód</CardTitle>
              <CardDescription>
                Ez csak egy példa QR kód. Az éles QR kódot a felmérés létrehozása után kapod meg.
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex justify-center p-8 bg-white rounded-lg">
                <QRCodeSVG
                  id="qr-code-svg"
                  value="https://doyoueap.com/survey/example"
                  size={256}
                  level="H"
                  includeMargin={true}
                />
              </div>
              <Button 
                onClick={handleDownloadQR}
                variant="outline"
                className="w-full"
              >
                <Download className="mr-2 h-4 w-4" />
                Minta QR kód letöltése (PNG)
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default CommunicationSupport;