import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useLocation, useNavigate } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { PlayCircle, Activity, Eye } from 'lucide-react';
import CreateAudit from './CreateAudit';
import RunningAudits from './RunningAudits';
import AuditQuestionnaire from './AuditQuestionnaire';

const EAPAudit = () => {
  const location = useLocation();
  const navigate = useNavigate();
  
  // Determine active tab from URL
  const getActiveTab = () => {
    if (location.pathname.includes('/hr/create-audit')) return 'create';
    if (location.pathname.includes('/hr/running-audits')) return 'running';
    if (location.pathname.includes('/hr/audit-questionnaire')) return 'demo';
    return 'create'; // default
  };

  const [activeTab, setActiveTab] = useState(getActiveTab());

  useEffect(() => {
    setActiveTab(getActiveTab());
  }, [location.pathname]);

  const handleTabChange = (value: string) => {
    setActiveTab(value);
    switch (value) {
      case 'create':
        navigate('/hr/create-audit');
        break;
      case 'running':
        navigate('/hr/running-audits');
        break;
      case 'demo':
        navigate('/hr/audit-questionnaire');
        break;
    }
  };

  return (
    <div className="p-8">
      <Tabs value={activeTab} onValueChange={handleTabChange} className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="create" className="flex items-center gap-2">
            <PlayCircle className="h-4 w-4" />
            <span className="hidden sm:inline">Audit Indítása</span>
          </TabsTrigger>
          <TabsTrigger value="running" className="flex items-center gap-2">
            <Activity className="h-4 w-4" />
            <span className="hidden sm:inline">Futó Auditok</span>
          </TabsTrigger>
          <TabsTrigger value="demo" className="flex items-center gap-2">
            <Eye className="h-4 w-4" />
            <span className="hidden sm:inline">Demo Audit</span>
          </TabsTrigger>
        </TabsList>

        <TabsContent value="create" className="mt-6">
          <CreateAudit />
        </TabsContent>

        <TabsContent value="running" className="mt-6">
          <RunningAudits />
        </TabsContent>

        <TabsContent value="demo" className="mt-6">
          <AuditQuestionnaire />
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default EAPAudit;
