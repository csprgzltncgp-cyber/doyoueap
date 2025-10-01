import { ScaleQuestion } from './ScaleQuestion';
import { YesNoQuestion } from './YesNoQuestion';
import { TextQuestion } from './TextQuestion';
import { SingleChoiceQuestion } from './SingleChoiceQuestion';
import { MultipleChoiceQuestion } from './MultipleChoiceQuestion';
import { NPSQuestion } from './NPSQuestion';

interface QuestionRendererProps {
  question: any;
  value: any;
  onChange: (value: any) => void;
}

export const QuestionRenderer = ({ question, value, onChange }: QuestionRendererProps) => {
  switch (question.type) {
    case 'scale':
      return <ScaleQuestion question={question} value={value} onChange={onChange} />;
    
    case 'yesno':
      return <YesNoQuestion question={question} value={value} onChange={onChange} />;
    
    case 'text':
      return <TextQuestion question={question} value={value} onChange={onChange} />;
    
    case 'single_choice':
      return <SingleChoiceQuestion question={question} value={value} onChange={onChange} />;
    
    case 'multiple_choice':
      return <MultipleChoiceQuestion question={question} value={value} onChange={onChange} />;
    
    case 'nps':
      return <NPSQuestion question={question} value={value} onChange={onChange} />;
    
    default:
      return null;
  }
};
