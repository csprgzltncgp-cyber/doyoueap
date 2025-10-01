import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';

interface Question {
  id: string;
  type: string;
  question: string;
  options?: string[];
  scale?: number;
  labels?: Record<string, string>;
  required: boolean;
}

interface QuestionRendererProps {
  question: Question;
  value: any;
  onChange: (value: any) => void;
}

export const QuestionRenderer = ({ question, value, onChange }: QuestionRendererProps) => {
  const { id, type, question: text, options, scale, labels } = question;

  switch (type) {
    case 'scale':
      const scaleSize = scale || 5;
      return (
        <div className="space-y-3">
          <Label className="text-base font-medium">{text}</Label>
          <RadioGroup
            value={value?.toString()}
            onValueChange={(val) => onChange(parseInt(val))}
            className="flex justify-between gap-2"
          >
            {Array.from({ length: scaleSize }, (_, i) => i + 1).map((num) => (
              <div key={num} className="flex flex-col items-center space-y-2">
                <RadioGroupItem value={num.toString()} id={`${id}-${num}`} />
                <Label htmlFor={`${id}-${num}`} className="text-sm font-normal">
                  {num}
                </Label>
              </div>
            ))}
          </RadioGroup>
          {labels && (
            <div className="flex justify-between text-xs text-muted-foreground">
              <span>{labels['1'] || 'Egyáltalán nem'}</span>
              <span>{labels[scaleSize.toString()] || 'Teljes mértékben'}</span>
            </div>
          )}
        </div>
      );

    case 'nps':
      return (
        <div className="space-y-3">
          <Label className="text-base font-medium">{text}</Label>
          <RadioGroup
            value={value?.toString()}
            onValueChange={(val) => onChange(parseInt(val))}
            className="grid grid-cols-11 gap-2"
          >
            {Array.from({ length: 11 }, (_, i) => i).map((num) => (
              <div key={num} className="flex flex-col items-center space-y-2">
                <RadioGroupItem value={num.toString()} id={`${id}-${num}`} />
                <Label htmlFor={`${id}-${num}`} className="text-sm font-normal">
                  {num}
                </Label>
              </div>
            ))}
          </RadioGroup>
          {labels && (
            <div className="flex justify-between text-xs text-muted-foreground">
              <span>{labels['0']}</span>
              <span>{labels['10']}</span>
            </div>
          )}
        </div>
      );

    case 'yesno':
      return (
        <div className="space-y-3">
          <Label className="text-base font-medium">{text}</Label>
          <RadioGroup
            value={value}
            onValueChange={onChange}
            className="flex gap-4"
          >
            <div className="flex items-center space-x-2">
              <RadioGroupItem value="yes" id={`${id}-yes`} />
              <Label htmlFor={`${id}-yes`} className="font-normal">Igen</Label>
            </div>
            <div className="flex items-center space-x-2">
              <RadioGroupItem value="no" id={`${id}-no`} />
              <Label htmlFor={`${id}-no`} className="font-normal">Nem</Label>
            </div>
          </RadioGroup>
        </div>
      );

    case 'text':
      return (
        <div className="space-y-3">
          <Label className="text-base font-medium">{text}</Label>
          <Textarea
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            placeholder="Írd ide a válaszod..."
            className="min-h-[100px]"
          />
        </div>
      );

    case 'single_choice':
      return (
        <div className="space-y-3">
          <Label className="text-base font-medium">{text}</Label>
          <RadioGroup
            value={value}
            onValueChange={onChange}
            className="flex flex-col gap-2"
          >
            {options?.map((option) => (
              <div key={option} className="flex items-center space-x-2">
                <RadioGroupItem value={option} id={`${id}-${option}`} />
                <Label htmlFor={`${id}-${option}`} className="font-normal">{option}</Label>
              </div>
            ))}
          </RadioGroup>
        </div>
      );

    case 'multiple_choice':
      const selectedOptions = Array.isArray(value) ? value : [];
      return (
        <div className="space-y-3">
          <Label className="text-base font-medium">{text}</Label>
          <div className="flex flex-col gap-2">
            {options?.map((option) => (
              <div key={option} className="flex items-center space-x-2">
                <Checkbox
                  id={`${id}-${option}`}
                  checked={selectedOptions.includes(option)}
                  onCheckedChange={(checked) => {
                    if (checked) {
                      onChange([...selectedOptions, option]);
                    } else {
                      onChange(selectedOptions.filter((o: string) => o !== option));
                    }
                  }}
                />
                <Label htmlFor={`${id}-${option}`} className="font-normal">{option}</Label>
              </div>
            ))}
          </div>
        </div>
      );

    default:
      return null;
  }
};
