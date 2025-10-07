import { Label } from '@/components/ui/label';
import { Check, X } from 'lucide-react';

interface YesNoQuestionProps {
  question: {
    id: string;
    question: string;
    required: boolean;
  };
  value: string | undefined;
  onChange: (value: string) => void;
}

export const YesNoQuestion = ({ question, value, onChange }: YesNoQuestionProps) => {
  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold block">
        {question.question}
      </Label>
      <div className="grid grid-cols-2 gap-4">
        <button
          type="button"
          onClick={() => onChange('yes')}
          className={`
            p-6 rounded-lg border transition-all
            ${value === 'yes' 
              ? 'border-green-500 bg-green-50 dark:bg-green-950/20 shadow-md' 
              : 'border-border hover:border-green-500/50 hover:bg-muted/50'
            }
            min-h-[80px] flex flex-col items-center justify-center gap-2
          `}
        >
          <Check className={`w-8 h-8 ${value === 'yes' ? 'text-green-600' : 'text-muted-foreground'}`} />
          <span className="text-base font-semibold">Igen</span>
        </button>
        <button
          type="button"
          onClick={() => onChange('no')}
          className={`
            p-6 rounded-lg border transition-all
            ${value === 'no' 
              ? 'border-red-500 bg-red-50 dark:bg-red-950/20 shadow-md' 
              : 'border-border hover:border-red-500/50 hover:bg-muted/50'
            }
            min-h-[80px] flex flex-col items-center justify-center gap-2
          `}
        >
          <X className={`w-8 h-8 ${value === 'no' ? 'text-red-600' : 'text-muted-foreground'}`} />
          <span className="text-base font-semibold">Nem</span>
        </button>
      </div>
    </div>
  );
};
