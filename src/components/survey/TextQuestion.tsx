import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface TextQuestionProps {
  question: {
    id: string;
    question: string;
    required: boolean;
  };
  value: string | undefined;
  onChange: (value: string) => void;
}

export const TextQuestion = ({ question, value, onChange }: TextQuestionProps) => {
  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold block">
        {question.question}
        {question.required && <span className="text-destructive ml-1">*</span>}
      </Label>
      <Textarea
        value={value || ''}
        onChange={(e) => onChange(e.target.value)}
        placeholder="Írd ide a válaszod..."
        className="min-h-[120px] text-base p-4 resize-none"
      />
    </div>
  );
};
