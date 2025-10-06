import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Link, Mail, QrCode } from "lucide-react";

interface Step1Props {
  accessMode: string;
  onAccessModeChange: (mode: string) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step1AccessMode = ({ accessMode, onAccessModeChange, onNext, onBack }: Step1Props) => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Hozzáférés módja</h2>
        <p className="text-muted-foreground">
          Válassza ki, hogyan férhetnek hozzá a munkavállalók az EAP Pulse felmérés kérdőívhez
        </p>
      </div>

      <RadioGroup value={accessMode} onValueChange={onAccessModeChange}>
        <Card className="cursor-pointer hover:border-primary">
          <CardHeader>
            <div className="flex items-center space-x-4">
              <RadioGroupItem value="tokenes" id="tokenes" />
              <div className="flex items-center gap-2">
                <Mail className="h-5 w-5" />
                <Label htmlFor="tokenes" className="cursor-pointer">
                  <CardTitle className="text-lg">Egyedi tokenes link minden munkatársnak</CardTitle>
                </Label>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <CardDescription>
              Email-cím lista feltöltése (XLSX/CSV formátumban). Minden munkatárs egyedi linket kap.
              Pontosan követhető a válaszarány.
            </CardDescription>
          </CardContent>
        </Card>

        <Card className="cursor-pointer hover:border-primary">
          <CardHeader>
            <div className="flex items-center space-x-4">
              <RadioGroupItem value="public_link" id="public_link" />
              <div className="flex items-center gap-2">
                <Link className="h-5 w-5" />
                <Label htmlFor="public_link" className="cursor-pointer">
                  <CardTitle className="text-lg">Egységes nyilvános link</CardTitle>
                </Label>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <CardDescription>
              Egyetlen link, amit belső csatornákon kommunikálhat. Teljesen anonim kitöltés.
            </CardDescription>
          </CardContent>
        </Card>

        <Card className="cursor-pointer hover:border-primary">
          <CardHeader>
            <div className="flex items-center space-x-4">
              <RadioGroupItem value="qr_code" id="qr_code" />
              <div className="flex items-center gap-2">
                <QrCode className="h-5 w-5" />
                <Label htmlFor="qr_code" className="cursor-pointer">
                  <CardTitle className="text-lg">QR kód / plakát</CardTitle>
                </Label>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <CardDescription>
              Automatikusan generált QR-kód és link. Letölthető PNG/SVG formátumban, plakátra helyezhető.
            </CardDescription>
          </CardContent>
        </Card>
      </RadioGroup>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button 
          onClick={onNext} 
          disabled={!accessMode}
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
