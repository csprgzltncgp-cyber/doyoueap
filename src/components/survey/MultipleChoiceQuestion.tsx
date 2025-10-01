import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

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
    <div className="space-y-3">
      <div className="space-y-1">
        <Label className="text-base font-medium">
          {question.question}
          {question.required && <span className="text-destructive ml-1">*</span>}
        </Label>
        <p className="text-sm text-muted-foreground italic">Több válasz lehetséges</p>
      </div>
      <div className="space-y-2">
        {question.options.map((option) => (
          <div key={option} className="flex items-center space-x-2">
            <Checkbox
              id={`${question.id}-${option}`}
              checked={value.includes(option)}
              onCheckedChange={() => handleToggle(option)}
            />
            <Label htmlFor={`${question.id}-${option}`} className="font-normal">
              {option}
            </Label>
          </div>
        ))}
      </div>
    </div>
  );
};
