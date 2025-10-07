import { Label } from '@/components/ui/label';
import { Check } from 'lucide-react';

interface ScaleQuestionProps {
  question: {
    id: string;
    question: string;
    scale: number;
    labels?: { [key: string]: string };
    required: boolean;
  };
  value: number | undefined;
  onChange: (value: number) => void;
}

export const ScaleQuestion = ({ question, value, onChange }: ScaleQuestionProps) => {
  const { question: text, scale, labels } = question;

  // Generáljuk a szöveges válaszokat a skálához
  const getScaleOptions = () => {
    if (!labels) return [];
    
    const options = [];
    for (let i = 1; i <= scale; i++) {
      if (labels[i.toString()]) {
        options.push({
          value: i,
          label: labels[i.toString()]
        });
      }
    }
    return options;
  };

  const scaleOptions = getScaleOptions();
  const hasTextOptions = scaleOptions.length === scale;

  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold block">
        {text}
      </Label>
      
      <div className="flex flex-col gap-2">
        {hasTextOptions ? (
          // Szöveges válaszopciók pipával
          <div className="grid gap-3">
            {scaleOptions.map((option) => (
              <button
                key={option.value}
                type="button"
                onClick={() => onChange(option.value)}
                className={`
                  relative w-full p-4 rounded-lg border transition-all text-left
                  ${value === option.value
                    ? 'border-primary bg-primary/5 shadow-md' 
                    : 'border-border hover:border-primary/50 hover:bg-muted/50'
                  }
                  min-h-[60px] flex items-center justify-between
                `}
              >
                <span className="text-base pr-2">{option.label}</span>
                {value === option.value && (
                  <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                    <Check className="w-4 h-4 text-primary-foreground" />
                  </div>
                )}
              </button>
            ))}
          </div>
        ) : (
          // Visszaesés számokra, ha nincsenek szöveges címkék (1-10 skála)
          <>
            {labels && (
              <p className="text-sm text-muted-foreground italic mb-2">
                A skálán az 1-es érték azt jelenti, hogy {labels['1']}, míg a {scale}-ös azt, hogy {labels[scale.toString()]}.
              </p>
            )}
            
            <div className="flex justify-between gap-2 md:gap-3">
              {Array.from({ length: scale }, (_, i) => i + 1).map((val) => (
                <button
                  key={val}
                  type="button"
                  onClick={() => onChange(val)}
                  className={`
                    flex-1 aspect-square rounded-lg border transition-all
                    flex items-center justify-center font-semibold text-lg
                    ${value === val
                      ? 'border-primary bg-primary/5 shadow-md' 
                      : 'border-border hover:border-primary/50 hover:bg-muted/50 hover:scale-105'
                    }
                    min-h-[56px] max-w-[80px]
                  `}
                >
                  {val}
                </button>
              ))}
            </div>
          </>
        )}
      </div>
    </div>
  );
};
