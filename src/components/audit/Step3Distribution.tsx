import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { Download, Link as LinkIcon, QrCode as QrCodeIcon, Mail, Info } from "lucide-react";
import { QRCodeSVG } from 'qrcode.react';
import { useState } from "react";

interface Step3Props {
  accessMode: string;
  accessToken: string;
  communicationText: string;
  emailSubject?: string;
  emailFrom?: string;
  onEmailSubjectChange?: (subject: string) => void;
  onEmailFromChange?: (from: string) => void;
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
  onEmailSubjectChange,
  onEmailFromChange,
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
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Email beállítások és lista feltöltése</h2>
        <p className="text-muted-foreground">
          Állítsd be az email paramétereit, majd töltsd fel a munkavállalók adatait
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Email feladó és tárgy</CardTitle>
          <CardDescription>
            Ezek az adatok fognak megjelenni az emailben
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label htmlFor="email-from">Feladó email címe</Label>
            <Input
              id="email-from"
              type="email"
              value={emailFrom || 'noreply@doyoueap.com'}
              onChange={(e) => onEmailFromChange?.(e.target.value)}
              placeholder="noreply@doyoueap.com"
              className="mt-2"
            />
            <p className="text-xs text-muted-foreground mt-1">
              Alapértelmezett: noreply@doyoueap.com
            </p>
          </div>

          <div>
            <Label htmlFor="email-subject">Email tárgya</Label>
            <Input
              id="email-subject"
              type="text"
              value={emailSubject || 'Fontos információ a munkavállalói támogatási programról'}
              onChange={(e) => onEmailSubjectChange?.(e.target.value)}
              placeholder="Email tárgya"
              className="mt-2"
            />
            <p className="text-xs text-muted-foreground mt-1">
              Rövid, informatív tárgy, amely felkelti a figyelmet
            </p>
          </div>
        </CardContent>
      </Card>

      <Alert className="border-info/50 bg-info/10">
        <Info className="h-4 w-4" />
        <AlertDescription>
          <strong>Fontos:</strong> A táblázatnak tartalmaznia kell egy "email" oszlopot. 
          Támogatott formátumok: CSV, Excel (.xlsx)
        </AlertDescription>
      </Alert>

      <Card>
        <CardHeader>
          <CardTitle>Mintafájl letöltése</CardTitle>
          <CardDescription>
            Töltsd le a mintafájlt, majd töltsd ki a munkavállalók adataival
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Button variant="outline" className="w-full" onClick={() => {
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
            <Download className="mr-2 h-4 w-4" />
            Mintafájl letöltése (CSV)
          </Button>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Email lista feltöltése</CardTitle>
          <CardDescription>
            Válaszd ki a kitöltött táblázatot
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label htmlFor="email-file">Fájl kiválasztása</Label>
            <Input
              id="email-file"
              type="file"
              accept=".csv,.xlsx,.xls"
              onChange={handleFileChange}
              className="mt-2"
            />
            {emailFile && (
              <p className="text-sm text-muted-foreground mt-2">
                Kiválasztva: {emailFile.name}
              </p>
            )}
          </div>

          <Alert>
            <Mail className="h-4 w-4" />
            <AlertDescription>
              Az emaileket a felmérés véglegesítése után küldjük ki automatikusan.
            </AlertDescription>
          </Alert>
        </CardContent>
      </Card>
    </div>
  );

  const renderPublicLinkMode = () => (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Nyilvános link</h2>
        <p className="text-muted-foreground">
          Ez a link bárkinek elérhető, aki megkapja
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Felmérés link</CardTitle>
          <CardDescription>
            Oszd meg ezt a linket a munkavállalókkal
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="p-4 bg-muted rounded-lg font-mono text-sm break-all">
            {surveyUrl}
          </div>
          <Button onClick={handleCopyLink} variant="outline" className="w-full">
            <LinkIcon className="mr-2 h-4 w-4" />
            Link másolása
          </Button>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Kommunikációs szöveg</CardTitle>
          <CardDescription>
            Töltsd le a 2. lépésben megadott szöveget
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Button onClick={handleDownloadText} variant="outline" className="w-full">
            <Download className="mr-2 h-4 w-4" />
            Szöveg letöltése
          </Button>
        </CardContent>
      </Card>
    </div>
  );

  const renderQRCodeMode = () => (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">QR kód generálása</h2>
        <p className="text-muted-foreground">
          Nyomtasd ki vagy digitálisan oszd meg a QR kódot
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>QR kód</CardTitle>
          <CardDescription>
            A munkavállalók beolvashatják mobiltelefonjukkal
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="flex justify-center p-8 bg-white rounded-lg">
            <QRCodeSVG
              id="qr-code-svg"
              value={surveyUrl}
              size={256}
              level="H"
              includeMargin={true}
            />
          </div>
          <Button onClick={handleDownloadQR} variant="outline" className="w-full">
            <Download className="mr-2 h-4 w-4" />
            QR kód letöltése (PNG)
          </Button>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Plakát szöveg</CardTitle>
          <CardDescription>
            Töltsd le a 2. lépésben megadott szöveget
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Button onClick={handleDownloadText} variant="outline" className="w-full">
            <Download className="mr-2 h-4 w-4" />
            Szöveg letöltése
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

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button 
          onClick={onNext}
          style={{
            backgroundColor: '#3572ef',
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
