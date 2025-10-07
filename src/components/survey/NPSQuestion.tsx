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
        <div className="bg-muted/30 rounded-lg p-3 space-y-1.5">
          <p className="text-sm font-medium text-center">
            A válaszadás során a{' '}
            <span className="font-bold text-foreground">0</span>
            {' '}jelentése: <span className="italic">{labels['0']}</span>,
            {' '}míg a{' '}
            <span className="font-bold text-foreground">{scale}</span>
            {' '}jelentése: <span className="italic">{labels[scale.toString()]}</span>
          </p>
        </div>
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
