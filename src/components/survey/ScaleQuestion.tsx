import { Label } from '@/components/ui/label';

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

  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold block">
        {text}
      </Label>
      
      {labels && (
        <p className="text-sm text-muted-foreground italic">
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
    </div>
  );
};
