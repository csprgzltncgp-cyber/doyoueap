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

Szeretnénk felhívni a figyelmedet a {{cég_név}} által biztosított {{program_név}} programra.

Kérjük, töltsd ki az alábbi rövid kérdőívet:
{{audit_link}}

Köszönjük!`;
    } else if (accessMode === 'qr_code') {
      return `Kedves Kollégák!

Szeretnénk felhívni a figyelmeteket a {{program_név}} programra.

Kérjük, töltsétek ki az alábbi QR kód beolvasásával elérhető kérdőívet!

Köszönjük!`;
    } else {
      return `Kedves Kollégák!

Szeretnénk felhívni a figyelmeteket a {{program_név}} programra.

Kérjük, töltsétek ki az alábbi linken elérhető kérdőívet:
{{audit_link}}

Köszönjük!`;
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
            Használhat változókat: {'{'}{'{'} név{'}'}{'}'},  {'{'}{'{'} cég_név{'}'}{'}'},  {'{'}{'{'} program_név{'}'}{'}'},  {'{'}{'{'} audit_link{'}'}{'}'} 
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