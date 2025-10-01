import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

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
    <div className="space-y-3">
      <Label className="text-base font-medium">
        {text}
        {question.required && <span className="text-destructive ml-1">*</span>}
      </Label>
      <RadioGroup
        value={value?.toString()}
        onValueChange={(val) => onChange(parseInt(val))}
        className="flex justify-between"
      >
        {Array.from({ length: scale }, (_, i) => i + 1).map((val) => (
          <div key={val} className="flex flex-col items-center space-y-2">
            <RadioGroupItem value={val.toString()} id={`${question.id}-${val}`} />
            <Label htmlFor={`${question.id}-${val}`} className="text-sm font-normal">
              {val}
            </Label>
          </div>
        ))}
      </RadioGroup>
      {labels && (
        <div className="flex justify-between text-xs text-muted-foreground">
          <span>{labels['1']}</span>
          <span>{labels[scale.toString()]}</span>
        </div>
      )}
    </div>
  );
};
