import { useSearchParams } from 'react-router-dom';
import CreateAudit from './CreateAudit';
import RunningAudits from './RunningAudits';
import AuditQuestionnaire from './AuditQuestionnaire';

const EAPAudit = () => {
  const [searchParams] = useSearchParams();
  const subSection = searchParams.get('sub');

  const renderContent = () => {
    if (!subSection) return null;
    
    switch (subSection) {
      case 'running-audits':
        return <RunningAudits />;
      case 'audit-questionnaire':
        return <AuditQuestionnaire />;
      case 'create-audit':
        return <CreateAudit />;
      default:
        return null;
    }
  };

  return (
    <div className="space-y-6">
      {renderContent()}
    </div>
  );
};

export default EAPAudit;
