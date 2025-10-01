import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

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
    <div className="space-y-3">
      <Label className="text-base font-medium">
        {text}
      </Label>
      <RadioGroup
        value={value?.toString()}
        onValueChange={(val) => onChange(parseInt(val))}
        className="grid grid-cols-11 gap-2"
      >
        {Array.from({ length: scale + 1 }, (_, i) => i).map((val) => (
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
          <span>{labels['0']}</span>
          <span>{labels[scale.toString()]}</span>
        </div>
      )}
    </div>
  );
};
