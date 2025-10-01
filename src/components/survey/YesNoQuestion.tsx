import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

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
    <div className="space-y-3">
      <Label className="text-base font-medium">
        {question.question}
      </Label>
      <RadioGroup value={value} onValueChange={onChange} className="flex gap-4">
        <div className="flex items-center space-x-2">
          <RadioGroupItem value="yes" id={`${question.id}-yes`} />
          <Label htmlFor={`${question.id}-yes`} className="font-normal">Igen</Label>
        </div>
        <div className="flex items-center space-x-2">
          <RadioGroupItem value="no" id={`${question.id}-no`} />
          <Label htmlFor={`${question.id}-no`} className="font-normal">Nem</Label>
        </div>
      </RadioGroup>
    </div>
  );
};
