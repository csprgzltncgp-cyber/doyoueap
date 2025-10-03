import { useState, useEffect } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { toast } from "sonner";
import { Download, Eye, Shield, Activity, Target } from "lucide-react";
import { formatAuditName } from "@/lib/auditUtils";
import { GaugeChart } from "@/components/ui/gauge-chart";
import Awareness from "./Awareness";
import TrustWillingness from "./TrustWillingness";
import Usage from "./Usage";
import Impact from "./Impact";
import Motivation from "./Motivation";
import UserCategories from "./UserCategories";
import Demographics from "./Demographics";
import Trends from "./Trends";
import Compare from "./Compare";
import Methodology from "./Methodology";

interface Audit {
  id: string;
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config: any;
  is_active: boolean;
  expires_at: string | null;
}

const Statistics = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [responses, setResponses] = useState<any[]>([]);
  const [loadingResponses, setLoadingResponses] = useState(false);
  const [employeeCount, setEmployeeCount] = useState<number>(0);
  const activeTab = searchParams.get("sub") || "overview";

  useEffect(() => {
    fetchAudits();
    fetchEmployeeCount();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchResponses();
    }
  }, [selectedAuditId]);

  const fetchEmployeeCount = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { data, error } = await supabase
        .from('profiles')
        .select('employee_count')
        .eq('id', user.id)
        .single();

      if (error) throw error;
      
      const count = parseInt(data?.employee_count || '0');
      setEmployeeCount(count);
    } catch (error) {
      console.error('Error fetching employee count:', error);
    }
  };

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at')
        .order('start_date', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      }
    } catch (error) {
      console.error('Error fetching assessments:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const fetchResponses = async () => {
    setLoadingResponses(true);
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('*')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;
      setResponses(data || []);
    } catch (error) {
      console.error('Error fetching responses:', error);
      toast.error('Hiba történt a válaszok betöltésekor');
    } finally {
      setLoadingResponses(false);
    }
  };

  const calculateAverage = (values: number[]) => {
    if (values.length === 0) return '0.0';
    return ((values.reduce((a, b) => a + b, 0) / values.length)).toFixed(1);
  };

  const totalResponses = responses.length;
  const usedBranch = responses.filter(r => r.employee_metadata?.branch === 'used').length;
  const notUsedBranch = responses.filter(r => r.employee_metadata?.branch === 'not_used').length;
  const redirectBranch = responses.filter(r => r.employee_metadata?.branch === 'redirect').length;

  const utilization = employeeCount > 0 ? (usedBranch / employeeCount) * 100 : 0;
  const participationRate = employeeCount > 0 ? (totalResponses / employeeCount) * 100 : 0;
  const usageRateFromRespondents = totalResponses > 0 ? (usedBranch / totalResponses) * 100 : 0;
  
  const satisfactionScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_satisfaction)
      .filter(v => v !== undefined)
  );
  const satisfactionIndex = (parseFloat(satisfactionScore) / 5) * 100;

  // Calculate 4Score metrics
  const awarenessResponses = responses.filter(r => 
    r.employee_metadata?.branch === 'used' || r.employee_metadata?.branch === 'not_used'
  );
  
  const awarenessScore = calculateAverage(
    awarenessResponses
      .map(r => r.responses?.awareness_level || r.responses?.nu_awareness_level)
      .filter(v => v !== undefined)
  );

  const trustResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  const trustScore = calculateAverage(
    trustResponses
      .map(r => r.responses?.u_trust_willingness)
      .filter(v => v !== undefined)
  );

  const usageScore = employeeCount > 0 ? ((usedBranch / employeeCount) * 100).toFixed(1) : '0.0';

  const impactScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_satisfaction)
      .filter(v => v !== undefined)
  );

  const exportCardToPNG = async (cardId: string, fileName: string) => {
    try {
      const html2canvas = (await import('html2canvas')).default;
      const element = document.getElementById(cardId);
      
      if (!element) {
        toast.error('Panel nem található');
        return;
      }

      const canvas = await html2canvas(element, {
        backgroundColor: '#ffffff',
        scale: 2,
      });

      const dataUrl = canvas.toDataURL('image/png');
      const link = document.createElement('a');
      link.download = `${fileName}.png`;
      link.href = dataUrl;
      link.click();
      toast.success('PNG sikeresen letöltve!');
    } catch (error) {
      console.error('Error exporting PNG:', error);
      toast.error('Hiba a PNG exportálás során');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  // Render content based on activeTab
  const renderContent = () => {
    if (activeTab === 'awareness') return <Awareness selectedAuditId={selectedAuditId} />;
    if (activeTab === 'trust') return <TrustWillingness selectedAuditId={selectedAuditId} />;
    if (activeTab === 'usage') return <Usage selectedAuditId={selectedAuditId} />;
    if (activeTab === 'impact') return <Impact selectedAuditId={selectedAuditId} />;
    if (activeTab === 'motivation') return <Motivation selectedAuditId={selectedAuditId} />;
    if (activeTab === 'demographics') return <Demographics selectedAuditId={selectedAuditId} />;
    if (activeTab === 'user-categories') return <UserCategories />;
    if (activeTab === 'trends') return <Trends />;
    if (activeTab === 'compare') return <Compare />;
    if (activeTab === 'methodology') return <Methodology />;

    // Default: Overview
    return (
      <>
        {loadingResponses ? (
          <div className="text-center py-12 text-muted-foreground">Adatok betöltése...</div>
        ) : totalResponses === 0 ? (
          <div className="text-center py-12 text-muted-foreground">
            {selectedAuditId ? 'Még nincs adat ehhez a felméréshez' : 'Válassz ki egy felmérést az adatok megjelenítéséhez'}
          </div>
        ) : (
          <div className="space-y-6">
            {/* Main KPIs */}
            <div className="grid md:grid-cols-2 gap-6">
              {/* UTILIZATION */}
              <Card id="utilization-card">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle className="text-2xl">Igénybevétel (Utilization)</CardTitle>
                      <CardDescription>
                        Hány munkavállaló használja a programot
                      </CardDescription>
                    </div>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => exportCardToPNG('utilization-card', 'igénybevétel')}
                      className="h-8 w-8 p-0"
                    >
                      <Download className="h-4 w-4" />
                    </Button>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="flex flex-col items-center">
                    <GaugeChart
                      value={utilization}
                      maxValue={100}
                      size={200}
                      label={`${utilization.toFixed(1)}%`}
                      sublabel={`${usedBranch} / ${employeeCount}`}
                      cornerRadius={0}
                    />
                    
                    <div className="w-full mt-6 space-y-3">
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-muted-foreground">Részvételi arány</span>
                        <div className="flex items-center gap-2">
                          <div className="w-32 bg-muted rounded-full h-2">
                           <div
                              className="h-2 rounded-full"
                              style={{ 
                                width: `${participationRate}%`,
                                backgroundColor: '#3366ff'
                              }}
                            />
                          </div>
                          <span className="font-semibold w-16 text-right">{participationRate.toFixed(1)}%</span>
                        </div>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-muted-foreground">Használók (a kitöltőkből)</span>
                        <div className="flex items-center gap-2">
                          <div className="w-32 bg-muted rounded-full h-2">
                           <div
                              className="h-2 rounded-full"
                              style={{ 
                                width: `${usageRateFromRespondents}%`,
                                backgroundColor: '#3366ff'
                              }}
                            />
                          </div>
                          <span className="font-semibold w-16 text-right">{usageRateFromRespondents.toFixed(1)}%</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* SATISFACTION INDEX */}
              <Card id="satisfaction-card">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle className="text-2xl">Elégedettségi Index</CardTitle>
                      <CardDescription>
                        Általános elégedettség a használók körében
                      </CardDescription>
                    </div>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => exportCardToPNG('satisfaction-card', 'elégedettségi-index')}
                      className="h-8 w-8 p-0"
                    >
                      <Download className="h-4 w-4" />
                    </Button>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="flex flex-col items-center">
                    <GaugeChart
                      value={satisfactionIndex}
                      maxValue={100}
                      size={200}
                      label={`${satisfactionIndex.toFixed(0)}%`}
                      sublabel={`${satisfactionScore} / 5`}
                      cornerRadius={0}
                    />
                    <p className="text-xs text-muted-foreground mt-4 text-center px-4">
                      <strong>Skála:</strong> 0-100%, ahol 100% = teljesen elégedett (5/5)
                    </p>
                    <p className="text-xs text-muted-foreground mt-2 text-center px-4">
                      Az elégedettségi index az 1-5 skálás értékelés átlagából számítva: (Átlag / 5) × 100
                    </p>
                  </div>
                </CardContent>
              </Card>
            </div>

            {/* 4Score Overview Cards */}
            <div className="mt-8">
              <h2 className="text-2xl font-bold mb-4">4Score Átfogó Mutatók</h2>
              <div className="grid md:grid-cols-4 gap-4">
                {/* Awareness Card */}
                <Card>
                  <CardHeader className="pb-3">
                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-blue-100 dark:bg-blue-900">
                        <Eye className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                      </div>
                      <CardTitle className="text-lg">Ismertség</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      <div className="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {awarenessScore}/5
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Átlagos tudatossági szint
                      </p>
                      <div className="w-full bg-muted rounded-full h-2">
                        <div
                          className="h-2 rounded-full bg-blue-600 dark:bg-blue-400"
                          style={{ width: `${(parseFloat(awarenessScore) / 5) * 100}%` }}
                        />
                      </div>
                    </div>
                  </CardContent>
                </Card>

                {/* Trust Card */}
                <Card>
                  <CardHeader className="pb-3">
                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-green-100 dark:bg-green-900">
                        <Shield className="h-5 w-5 text-green-600 dark:text-green-400" />
                      </div>
                      <CardTitle className="text-lg">Bizalom</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      <div className="text-3xl font-bold text-green-600 dark:text-green-400">
                        {trustScore}/5
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Átlagos bizalmi szint
                      </p>
                      <div className="w-full bg-muted rounded-full h-2">
                        <div
                          className="h-2 rounded-full bg-green-600 dark:bg-green-400"
                          style={{ width: `${(parseFloat(trustScore) / 5) * 100}%` }}
                        />
                      </div>
                    </div>
                  </CardContent>
                </Card>

                {/* Usage Card */}
                <Card>
                  <CardHeader className="pb-3">
                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-purple-100 dark:bg-purple-900">
                        <Activity className="h-5 w-5 text-purple-600 dark:text-purple-400" />
                      </div>
                      <CardTitle className="text-lg">Használat</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      <div className="text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {usageScore}%
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Igénybevételi arány
                      </p>
                      <div className="w-full bg-muted rounded-full h-2">
                        <div
                          className="h-2 rounded-full bg-purple-600 dark:bg-purple-400"
                          style={{ width: `${usageScore}%` }}
                        />
                      </div>
                    </div>
                  </CardContent>
                </Card>

                {/* Impact Card */}
                <Card>
                  <CardHeader className="pb-3">
                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-orange-100 dark:bg-orange-900">
                        <Target className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                      </div>
                      <CardTitle className="text-lg">Hatás</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      <div className="text-3xl font-bold text-orange-600 dark:text-orange-400">
                        {impactScore}/5
                      </div>
                      <p className="text-sm text-muted-foreground">
                        Átlagos hatékonysági szint
                      </p>
                      <div className="w-full bg-muted rounded-full h-2">
                        <div
                          className="h-2 rounded-full bg-orange-600 dark:bg-orange-400"
                          style={{ width: `${(parseFloat(impactScore) / 5) * 100}%` }}
                        />
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>
          </div>
        )}
      </>
    );
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Statisztikák</h1>
        {audits.length > 0 ? (
          <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
            <SelectTrigger className="w-80">
              <SelectValue placeholder="Válassz felmérést" />
            </SelectTrigger>
            <SelectContent>
              {audits.map((audit) => (
                <SelectItem key={audit.id} value={audit.id}>
                  {formatAuditName(audit)}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        ) : (
          <div className="text-sm text-muted-foreground">
            Nincs aktív felmérés - hozz létre egyet az adatok megjelenítéséhez
          </div>
        )}
      </div>

      {/* Tabs Navigation */}
      <Tabs value={activeTab} onValueChange={(value) => navigate(`/hr/statistics?sub=${value}`)}>
        <TabsList className="grid grid-cols-5 lg:grid-cols-10 w-full">
          <TabsTrigger value="overview">Összegzés</TabsTrigger>
          <TabsTrigger value="awareness">Ismertség</TabsTrigger>
          <TabsTrigger value="trust">Bizalom</TabsTrigger>
          <TabsTrigger value="usage">Használat</TabsTrigger>
          <TabsTrigger value="impact">Hatás</TabsTrigger>
          <TabsTrigger value="motivation">Motiváció</TabsTrigger>
          <TabsTrigger value="demographics">Demográfia</TabsTrigger>
          <TabsTrigger value="trends">Trendek</TabsTrigger>
          <TabsTrigger value="compare">Összehasonlítás</TabsTrigger>
          <TabsTrigger value="methodology">Módszertan</TabsTrigger>
        </TabsList>
      </Tabs>

      {renderContent()}
    </div>
  );
};

export default Statistics;
