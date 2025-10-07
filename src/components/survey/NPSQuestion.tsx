import { Label } from '@/components/ui/label';

interface NPSQuestionProps {
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

export const NPSQuestion = ({ question, value, onChange }: NPSQuestionProps) => {
  const { question: text, scale, labels } = question;

  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold block">
        {text}
      </Label>
      
      {labels && (
        <p className="text-sm text-muted-foreground italic">
          A skálán a 0 érték azt jelenti, hogy {labels['0']}, míg a {scale} azt, hogy {labels[scale.toString()]}.
        </p>
      )}
      
      <div className="grid grid-cols-11 gap-1.5 md:gap-2">
        {Array.from({ length: scale + 1 }, (_, i) => i).map((val) => (
          <button
            key={val}
            type="button"
            onClick={() => onChange(val)}
            className={`
              aspect-square rounded-lg border-2 transition-all
              flex items-center justify-center font-semibold text-sm md:text-base
              ${value === val
                ? 'border-primary bg-primary text-primary-foreground shadow-lg scale-110' 
                : 'border-border hover:border-primary/50 hover:bg-muted/50 hover:scale-105'
              }
              min-h-[44px]
            `}
          >
            {val}
          </button>
        ))}
      </div>
    </div>
  );
};
