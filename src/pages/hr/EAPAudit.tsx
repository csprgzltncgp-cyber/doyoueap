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
      <div className="mb-6">
        <h1 className="text-3xl font-bold mb-2">EAP Pulse</h1>
        <p className="text-muted-foreground">
          4Score felmérések létrehozása, kezelése és előnézete
        </p>
      </div>

      <Tabs value={activeTab} onValueChange={handleTabChange} className="w-full">
        <TabsList className="w-full justify-start border-b rounded-none h-auto p-0 bg-transparent">
          <TabsTrigger 
            value="create" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <PlayCircle className="h-4 w-4" />
            Új Felmérés Indítása
          </TabsTrigger>
          <TabsTrigger 
            value="running" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Activity className="h-4 w-4" />
            Futó Felmérések
          </TabsTrigger>
          <TabsTrigger 
            value="demo" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Eye className="h-4 w-4" />
            Demo Felmérés
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
