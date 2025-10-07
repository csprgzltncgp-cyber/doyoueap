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
        {question.required && <span className="text-destructive ml-1">*</span>}
      </Label>
      
      {labels && (
        <div className="flex justify-between text-sm text-muted-foreground px-1">
          <span className="max-w-[45%] text-left">{labels['0']}</span>
          <span className="max-w-[45%] text-right">{labels[scale.toString()]}</span>
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
