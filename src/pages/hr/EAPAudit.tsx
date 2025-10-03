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
      <div>
        <h1 className="text-3xl font-bold mb-2">EAP Pulse</h1>
        <p className="text-muted-foreground">
          4Score felmérések létrehozása, kezelése és előnézete
        </p>
      </div>
      {renderContent()}
    </div>
  );
};

export default EAPAudit;
