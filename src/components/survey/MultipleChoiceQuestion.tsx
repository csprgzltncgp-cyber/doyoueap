import { Label } from '@/components/ui/label';
import { Check } from 'lucide-react';

interface MultipleChoiceQuestionProps {
  question: {
    id: string;
    question: string;
    options: string[];
    required: boolean;
  };
  value: string[] | undefined;
  onChange: (value: string[]) => void;
}

export const MultipleChoiceQuestion = ({ question, value = [], onChange }: MultipleChoiceQuestionProps) => {
  const handleToggle = (option: string) => {
    const newValue = value.includes(option)
      ? value.filter((v) => v !== option)
      : [...value, option];
    onChange(newValue);
  };

  return (
    <div className="space-y-4">
      <div className="space-y-1">
        <Label className="text-lg font-semibold block">
          {question.question}
        </Label>
        <p className="text-sm text-muted-foreground italic">Több válasz lehetséges</p>
      </div>
      <div className="grid gap-3">
        {question.options.map((option) => (
          <button
            key={option}
            type="button"
            onClick={() => handleToggle(option)}
            className={`
              relative w-full p-4 rounded-lg border transition-all text-left
              ${value.includes(option)
                ? 'border-foreground bg-muted shadow-md' 
                : 'border-border hover:border-muted-foreground hover:bg-muted/50'
              }
              min-h-[60px] flex items-center justify-between
            `}
          >
            <span className="text-base pr-2">{option}</span>
            {value.includes(option) && (
              <div className="flex-shrink-0 w-6 h-6 rounded-full bg-foreground flex items-center justify-center">
                <Check className="w-4 h-4 text-background" />
              </div>
            )}
          </button>
        ))}
      </div>
    </div>
  );
};
