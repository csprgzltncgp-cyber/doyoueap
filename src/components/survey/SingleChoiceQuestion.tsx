import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

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
    <div className="space-y-3">
      <Label className="text-base font-medium">
        {question.question}
        {question.required && <span className="text-destructive ml-1">*</span>}
      </Label>
      <RadioGroup value={value} onValueChange={onChange} className="space-y-2">
        {question.options.map((option) => (
          <div key={option} className="flex items-center space-x-2">
            <RadioGroupItem value={option} id={`${question.id}-${option}`} />
            <Label htmlFor={`${question.id}-${option}`} className="font-normal">
              {option}
            </Label>
          </div>
        ))}
      </RadioGroup>
    </div>
  );
};
