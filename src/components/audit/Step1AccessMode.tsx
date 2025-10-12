import { Button } from "@/components/ui/button";
import { Link, Mail, QrCode, Check } from "lucide-react";

interface Step1Props {
  accessMode: string;
  onAccessModeChange: (mode: string) => void;
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

export const Step1AccessMode = ({ accessMode, onAccessModeChange, onNext, onBack }: Step1Props) => {
  return (
    <div className="space-y-8 max-w-3xl mx-auto">
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

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext} 
          disabled={!accessMode}
          size="lg"
          className="bg-primary hover:bg-primary/90"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
