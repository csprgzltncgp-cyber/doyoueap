import { Label } from '@/components/ui/label';
import { Check } from 'lucide-react';

interface SingleChoiceQuestionProps {
  question: {
    id: string;
    question: string;
    options: string[];
    required: boolean;
  };
  value: string | undefined;
  onChange: (value: string) => void;
}

export const SingleChoiceQuestion = ({ question, value, onChange }: SingleChoiceQuestionProps) => {
  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold block">
        {question.question}
        {question.required && <span className="text-destructive ml-1">*</span>}
      </Label>
      <div className="grid gap-3">
        {question.options.map((option) => (
          <button
            key={option}
            type="button"
            onClick={() => onChange(option)}
            className={`
              relative w-full p-4 rounded-lg border-2 transition-all text-left
              ${value === option 
                ? 'border-primary bg-primary/5 shadow-md' 
                : 'border-border hover:border-primary/50 hover:bg-muted/50'
              }
              min-h-[60px] flex items-center justify-between
            `}
          >
            <span className="text-base pr-2">{option}</span>
            {value === option && (
              <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                <Check className="w-4 h-4 text-primary-foreground" />
              </div>
            )}
          </button>
        ))}
      </div>
    </div>
  );
};
