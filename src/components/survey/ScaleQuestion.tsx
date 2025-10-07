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
        <div className="bg-muted/30 rounded-lg p-3 space-y-1.5">
          <p className="text-sm font-medium text-center">
            A válaszadás során az{' '}
            <span className="font-bold text-foreground">1-es szám</span>
            {' '}jelentése: <span className="italic">{labels['1']}</span>,
            {' '}míg a{' '}
            <span className="font-bold text-foreground">{scale}-es szám</span>
            {' '}jelentése: <span className="italic">{labels[scale.toString()]}</span>
          </p>
        </div>
      )}
      
      <div className="flex justify-between gap-2 md:gap-3">
        {Array.from({ length: scale }, (_, i) => i + 1).map((val) => (
          <button
            key={val}
            type="button"
            onClick={() => onChange(val)}
            className={`
              flex-1 aspect-square rounded-lg border-2 transition-all
              flex items-center justify-center font-semibold text-lg
              ${value === val
                ? 'border-primary bg-primary text-primary-foreground shadow-lg scale-110' 
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
