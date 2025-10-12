import { useSearchParams } from 'react-router-dom';
import CreateAudit from './CreateAudit';
import RunningAudits from './RunningAudits';
import AuditQuestionnaire from './AuditQuestionnaire';
import Focus from './Focus';
import Gifts from './Gifts';
import StepByStep from './StepByStep';

const EAPAudit = () => {
  const [searchParams] = useSearchParams();
  const subSection = searchParams.get('sub') || 'create-audit';

  const renderContent = () => {
    switch (subSection) {
      case 'step-by-step':
        return <StepByStep />;
      case 'focus':
        return <Focus />;
      case 'running-audits':
        return <RunningAudits />;
      case 'audit-questionnaire':
        return <AuditQuestionnaire />;
      case 'gifts':
        return <Gifts />;
      case 'create-audit':
      default:
        return <CreateAudit />;
    }
  };

  return (
    <div className="space-y-6 pt-20 md:pt-0">
      {renderContent()}
    </div>
  );
};

export default EAPAudit;
