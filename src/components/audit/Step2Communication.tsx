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

Szeretnénk felhívni a figyelmedet egy fontos lehetőségre: cégednek köszönhetően most hozzáférhetsz a {{program_név}} programhoz.

Ez egy bizalmas támogatási szolgáltatás, amely különféle élethelyzetekben nyújt segítséget – legyen szó munkával kapcsolatos kihívásokról, magánéleti nehézségekről, stresszről vagy bármilyen más témáról, ahol támogatásra van szükséged.

A szolgáltatás teljesen ingyenes, anonim és bizalmas. Pszichológusok, jogászok, pénzügyi tanácsadók és más szakemberek állnak rendelkezésedre.

Kérjük, töltsd ki az alábbi rövid, anonim kérdőívet (kb. 5-8 perc):
{{audit_link}}

A válaszaid rendkívül fontosak számunkra. Segítenek megérteni, hogyan tudnánk még jobban támogatni téged és kollégáidat. A visszajelzéseid alapján folyamatosan fejlesztjük a programot, hogy még hasznosabb és elérhetőbb legyen mindenki számára.

Köszönjük a részvételed!
{{cég_név}}`;
    } else if (accessMode === 'qr_code') {
      return `Kedves Kollégák!

Szkenneld be a QR kódot és ismerd meg a {{program_név}} programot!

Ez egy bizalmas támogatási szolgáltatás, amely különféle élethelyzetekben nyújt segítséget – legyen szó munkával kapcsolatos kihívásokról, magánéleti nehézségekről, stresszről vagy bármilyen más témáról.

A szolgáltatás teljesen ingyenes, anonim és bizalmas. Pszichológusok, jogászok, pénzügyi tanácsadók és más szakemberek állnak rendelkezésetekre.

A kérdőív kitöltése mindössze 5-8 percet vesz igénybe, és teljesen anonim.

A válaszaitok rendkívül fontosak számunkra. Segítenek megérteni, hogyan tudnánk még jobban támogatni benneteket. A visszajelzéseitek alapján folyamatosan fejlesztjük a programot, hogy még hasznosabb és elérhetőbb legyen mindenki számára.

Köszönjük!`;
    } else {
      return `Kedves Kollégák!

Szeretnénk felhívni a figyelmeteket egy fontos lehetőségre: cégünknek köszönhetően most hozzáférhettek a {{program_név}} programhoz.

Ez egy bizalmas támogatási szolgáltatás, amely különféle élethelyzetekben nyújt segítséget – legyen szó munkával kapcsolatos kihívásokról, magánéleti nehézségekről, stresszről vagy bármilyen más témáról, ahol támogatásra van szükség.

A szolgáltatás teljesen ingyenes, anonim és bizalmas. Pszichológusok, jogászok, pénzügyi tanácsadók és más szakemberek állnak rendelkezésetekre.

Kérjük, töltsé tek ki az alábbi rövid, anonim kérdőívet (kb. 5-8 perc):
{{audit_link}}

A válaszaitok rendkívül fontosak számunkra. Segítenek megérteni, hogyan tudnánk még jobban támogatni benneteket. A visszajelzéseitek alapján folyamatosan fejlesztjük a programot, hogy még hasznosabb és elérhetőbb legyen mindenki számára.

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