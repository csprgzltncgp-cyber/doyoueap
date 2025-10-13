import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { Download, Link as LinkIcon, QrCode as QrCodeIcon, Mail, Info, Check } from "lucide-react";
import { QRCodeSVG } from 'qrcode.react';
import { useState } from "react";

interface Step3Props {
  accessMode: string;
  accessToken: string;
  communicationText: string;
  emailSubject?: string;
  emailFrom?: string;
  emailBody?: string;
  onEmailSubjectChange?: (subject: string) => void;
  onEmailFromChange?: (from: string) => void;
  onEmailBodyChange?: (body: string) => void;
  onEmailListUpload?: (file: File) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step3Distribution = ({
  accessMode,
  accessToken,
  communicationText,
  emailSubject,
  emailFrom,
  emailBody,
  onEmailSubjectChange,
  onEmailFromChange,
  onEmailBodyChange,
  onEmailListUpload,
  onNext,
  onBack,
}: Step3Props) => {
  const [emailFile, setEmailFile] = useState<File | null>(null);
  const surveyUrl = `${window.location.origin}/survey/${accessToken}`;

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setEmailFile(file);
      onEmailListUpload?.(file);
    }
  };

  const handleDownloadText = () => {
    const blob = new Blob([communicationText], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `kommunikacio-${accessMode}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  const handleCopyLink = () => {
    navigator.clipboard.writeText(surveyUrl);
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

  const renderTokenesMode = () => (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Email beállítások és lista feltöltése</h2>
        <p className="text-muted-foreground text-lg">
          Állítsd be az email paramétereit, majd töltsd fel a munkavállalók adatait
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Mail className="h-6 w-6" />
            Email feladó, tárgy és szöveg
          </CardTitle>
          <CardDescription className="text-base">
            Ezek az adatok fognak megjelenni az emailben
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6 pt-6">
          <div>
            <Label htmlFor="email-from" className="text-base font-medium">Feladó email címe</Label>
            <Input
              id="email-from"
              type="email"
              value={emailFrom || 'noreply@doyoueap.com'}
              onChange={(e) => onEmailFromChange?.(e.target.value)}
              placeholder="noreply@doyoueap.com"
              className="mt-2 h-12 text-base"
            />
            <p className="text-sm text-muted-foreground mt-2">
              Alapértelmezett: noreply@doyoueap.com
            </p>
          </div>

          <div>
            <Label htmlFor="email-subject" className="text-base font-medium">Email tárgya</Label>
            <Input
              id="email-subject"
              type="text"
              value={emailSubject || 'Fontos információ a munkavállalói támogatási programról'}
              onChange={(e) => onEmailSubjectChange?.(e.target.value)}
              placeholder="Email tárgya"
              className="mt-2 h-12 text-base"
            />
            <p className="text-sm text-muted-foreground mt-2">
              Rövid, informatív tárgy, amely felkelti a figyelmet
            </p>
          </div>

          <div>
            <Label htmlFor="email-body" className="text-base font-medium">Email szövege</Label>
            <Textarea
              id="email-body"
              value={emailBody || `Tárgy: Segítsd jobbá tenni a EAP programot!

Helló!

Az EAP program azért jött létre, hogy támogatást nyújtson a mindennapokban – legyen szó stresszről, jogi vagy pénzügyi kérdésekről, vagy éppen pszichológiai segítségről. Szeretnénk, ha a program minél inkább a Te igényeidhez igazodna, ezért nagy segítség lenne számunkra a visszajelzésed.

Kérjük, töltsd ki rövid kérdőívünket, amelyet az alábbi linken érsz el:

[Link]

A kitöltés csak néhány percet vesz igénybe. A válaszadás teljesen anonim, és az eredményeket kizárólag összesítve, statisztikai célokra használjuk.

Előre is köszönjük, hogy segítesz jobbá tenni a EAP programot!

Üdvözlettel,
HR osztály`}
              onChange={(e) => onEmailBodyChange?.(e.target.value)}
              placeholder="Email szövege"
              rows={15}
              className="mt-2 font-mono text-sm resize-y"
            />
            <p className="text-sm text-muted-foreground mt-2">
              Az email szövege, amelyet a munkavállalók megkapnak. A [Link] helyére automatikusan bekerül a személyre szóló link.
            </p>
          </div>
        </CardContent>
      </Card>

      <Alert className="border-primary/50 bg-primary/5">
        <Info className="h-5 w-5 text-primary" />
        <AlertDescription className="text-base">
          <strong>Fontos:</strong> A táblázatnak tartalmaznia kell egy "email" oszlopot. 
          Támogatott formátumok: CSV, Excel (.xlsx)
        </AlertDescription>
      </Alert>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Download className="h-6 w-6" />
            Mintafájl letöltése
          </CardTitle>
          <CardDescription className="text-base">
            Töltsd le a mintafájlt, majd töltsd ki a munkavállalók adataival
          </CardDescription>
        </CardHeader>
        <CardContent className="pt-6">
          <Button variant="outline" size="lg" className="w-full h-14 text-base" onClick={() => {
            const csvContent = "email\npelda@ceg.hu\npelda2@ceg.hu\npelda3@ceg.hu";
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'email-lista-minta.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
          }}>
            <Download className="mr-2 h-5 w-5" />
            Mintafájl letöltése (CSV)
          </Button>
        </CardContent>
      </Card>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Mail className="h-6 w-6" />
            Email lista feltöltése
          </CardTitle>
          <CardDescription className="text-base">
            Válaszd ki a kitöltött táblázatot
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div>
            <Label htmlFor="email-file" className="text-base font-medium">Fájl kiválasztása</Label>
            <Input
              id="email-file"
              type="file"
              accept=".csv,.xlsx,.xls"
              onChange={handleFileChange}
              className="mt-2 h-12 text-base cursor-pointer"
            />
            {emailFile && (
              <p className="text-base text-muted-foreground mt-3 flex items-center gap-2">
                <Check className="h-5 w-5 text-primary" />
                Kiválasztva: <strong>{emailFile.name}</strong>
              </p>
            )}
          </div>

          <Alert className="border-primary/50 bg-primary/5">
            <Mail className="h-5 w-5 text-primary" />
            <AlertDescription className="text-base">
              Az emaileket a felmérés véglegesítése után küldjük ki automatikusan.
            </AlertDescription>
          </Alert>
        </CardContent>
      </Card>
    </div>
  );

  const renderPublicLinkMode = () => (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Nyilvános link</h2>
        <p className="text-muted-foreground text-lg">
          Ez a link bárkinek elérhető, aki megkapja
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <LinkIcon className="h-6 w-6" />
            Felmérés link
          </CardTitle>
          <CardDescription className="text-base">
            Oszd meg ezt a linket a munkavállalókkal
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div className="p-4 bg-primary/5 border border-primary/20 rounded-lg font-mono text-sm break-all">
            {surveyUrl}
          </div>
          <Button onClick={handleCopyLink} variant="outline" size="lg" className="w-full h-14 text-base">
            <LinkIcon className="mr-2 h-5 w-5" />
            Link másolása
          </Button>
        </CardContent>
      </Card>
    </div>
  );

  const renderQRCodeMode = () => (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">QR kód generálása</h2>
        <p className="text-muted-foreground text-lg">
          Nyomtasd ki vagy digitálisan oszd meg a QR kódot
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <QrCodeIcon className="h-6 w-6" />
            QR kód
          </CardTitle>
          <CardDescription className="text-base">
            A munkavállalók beolvashatják mobiltelefonjukkal
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div className="flex justify-center p-10 bg-white rounded-xl border-2 border-dashed border-primary/30">
            <QRCodeSVG
              id="qr-code-svg"
              value={surveyUrl}
              size={280}
              level="H"
              includeMargin={true}
            />
          </div>
          <Button onClick={handleDownloadQR} variant="outline" size="lg" className="w-full h-14 text-base">
            <Download className="mr-2 h-5 w-5" />
            QR kód letöltése (PNG)
          </Button>
        </CardContent>
      </Card>
    </div>
  );

  return (
    <div className="space-y-6">
      {accessMode === 'tokenes' && renderTokenesMode()}
      {accessMode === 'public_link' && renderPublicLinkMode()}
      {accessMode === 'qr_code' && renderQRCodeMode()}

      <div className="flex justify-between pt-6 max-w-4xl mx-auto">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext}
          size="lg"
          variant="dark"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
