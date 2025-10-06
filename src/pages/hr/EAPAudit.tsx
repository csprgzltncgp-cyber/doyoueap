import { useSearchParams } from 'react-router-dom';
import CreateAudit from './CreateAudit';
import RunningAudits from './RunningAudits';
import AuditQuestionnaire from './AuditQuestionnaire';

const EAPAudit = () => {
  const [searchParams] = useSearchParams();
  const subSection = searchParams.get('sub') || 'create-audit';

  const renderContent = () => {
    switch (subSection) {
      case 'running-audits':
        return <RunningAudits />;
      case 'audit-questionnaire':
        return <AuditQuestionnaire />;
      case 'create-audit':
      default:
        return <CreateAudit />;
    }
  };

  return (
    <div className="space-y-6">
      {renderContent()}
    </div>
  );
};

export default EAPAudit;
