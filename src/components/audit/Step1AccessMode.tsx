import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Link, Mail, QrCode, Check, Target, AlertTriangle } from "lucide-react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

interface Step1Props {
  accessMode: string;
  targetResponses: number | null;
  onAccessModeChange: (mode: string) => void;
  onTargetResponsesChange: (target: number | null) => void;
  onNext: () => void;
  onBack: () => void;
}

const options = [
  {
    value: 'tokenes',
    icon: Mail,
    title: 'Egyedi tokenes link minden munkatársnak',
    description: 'Email-cím lista feltöltése (XLSX/CSV formátumban). Minden munkatárs egyedi linket kap. Pontosan követhető a válaszarány.'
  },
  {
    value: 'public_link',
    icon: Link,
    title: 'Egységes nyilvános link',
    description: 'Egyetlen link, amit belső csatornákon kommunikálhat. Teljesen anonim kitöltés.'
  },
  {
    value: 'qr_code',
    icon: QrCode,
    title: 'QR kód / plakát',
    description: 'Automatikusan generált QR-kód és link. Letölthető PNG/SVG formátumban, plakátra helyezhető.'
  }
];

export const Step1AccessMode = ({ accessMode, targetResponses, onAccessModeChange, onTargetResponsesChange, onNext, onBack }: Step1Props) => {
  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Hozzáférés módja</h2>
        <p className="text-muted-foreground text-lg">
          Válaszd ki, hogyan férhetnek hozzá a munkavállalók a kérdőívhez
        </p>
      </div>

      <div className="grid gap-4">
        {options.map((option) => {
          const Icon = option.icon;
          const isSelected = accessMode === option.value;
          
          return (
            <button
              key={option.value}
              type="button"
              onClick={() => onAccessModeChange(option.value)}
              className={`
                relative w-full p-6 rounded-xl border-2 transition-all text-left
                ${isSelected 
                  ? 'border-primary bg-primary/5 shadow-lg scale-[1.02]' 
                  : 'border-border hover:border-primary/50 hover:bg-muted/30 hover:scale-[1.01]'
                }
                min-h-[120px] flex gap-4
              `}
            >
              <div className={`
                flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center
                ${isSelected ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'}
              `}>
                <Icon className="w-6 h-6" />
              </div>
              
              <div className="flex-1 space-y-2">
                <h3 className="text-lg font-semibold">{option.title}</h3>
                <p className="text-sm text-muted-foreground leading-relaxed">
                  {option.description}
                </p>
              </div>

              {isSelected && (
                <div className="flex-shrink-0 w-8 h-8 rounded-full bg-primary flex items-center justify-center self-start">
                  <Check className="w-5 h-5 text-primary-foreground" />
                </div>
              )}
            </button>
          );
        })}
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Target className="h-6 w-6" />
            Célszám megadása (opcionális)
          </CardTitle>
          <CardDescription className="text-base">
            Ha szeretnéd követni a kitöltöttséget százalékosan, add meg a várt válaszok számát
          </CardDescription>
        </CardHeader>
        <CardContent className="pt-6">
          <div className="space-y-2">
            <Label htmlFor="target-responses" className="text-base font-medium">
              Várt válaszok száma
            </Label>
            <Input
              id="target-responses"
              type="number"
              min="1"
              placeholder="pl. 150"
              value={targetResponses || ''}
              onChange={(e) => onTargetResponsesChange(e.target.value ? parseInt(e.target.value) : null)}
              className="h-12 text-base"
            />
            {accessMode === 'tokenes' ? (
              <div className="flex items-start gap-2 text-sm text-destructive font-medium">
                <AlertTriangle className="h-4 w-4 flex-shrink-0 mt-0.5" />
                <p>
                  Ha megadsz célszámot, az felülírja az automatikus email-számolást!
                </p>
              </div>
            ) : (
              <p className="text-sm text-muted-foreground">
                Ha nem adsz meg célszámot, a regisztráció során megadott munkavállalói létszámból fogjuk számolni, vagy ha az sincs megadva, csak a kitöltések számát fogjuk mutatni.
              </p>
            )}
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext} 
          disabled={!accessMode}
          size="lg"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
