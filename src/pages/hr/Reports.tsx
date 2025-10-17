import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { usePackage } from "@/hooks/usePackage";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { Download, Eye, Shield, Activity, Target, Users, TrendingUp, Presentation, RefreshCw, Building2 } from "lucide-react";
import fourScoreLogo from "@/assets/4score_logo.svg";
import { Progress } from "@/components/ui/progress";
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis, BarChart, Bar, XAxis, YAxis, RadialBarChart, RadialBar, Legend } from "recharts";
import { formatAuditName } from "@/lib/auditUtils";
import { GaugeChart } from "@/components/ui/gauge-chart";
import { exportAllChartsToPPT } from "@/lib/pptExportUtils";
import { ReportNavigation } from "@/components/navigation/ReportNavigation";
import Awareness from "./Awareness";
import TrustWillingness from "./TrustWillingness";
import Usage from "./Usage";
import Impact from "./Impact";
import CombinedPreferences from "./CombinedPreferences";
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
  company_name?: string;
  partner_company_id?: string | null;
}

const Reports = () => {
  const [searchParams] = useSearchParams();
  const { packageType } = usePackage();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [selectedCompanyId, setSelectedCompanyId] = useState<string>('all');
  const [companies, setCompanies] = useState<Array<{ id: string; company_name: string }>>([]);
  const [responses, setResponses] = useState<any[]>([]);
  const [loadingResponses, setLoadingResponses] = useState(false);
  const [employeeCount, setEmployeeCount] = useState<number>(0);
  const activeTab = searchParams.get("sub") || "overview";
  const autoExport = searchParams.get("autoExport");
  const fileName = searchParams.get("fileName");
  const inIframe = searchParams.get("inIframe") === "true";

  useEffect(() => {
    if (packageType === 'partner') {
      fetchCompanies();
    }
    fetchAudits();
    fetchEmployeeCount();
  }, [packageType]);

  const fetchCompanies = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { data, error } = await supabase
        .from('companies')
        .select('id, company_name')
        .eq('partner_user_id', user.id)
        .order('company_name');

      if (error) throw error;
      setCompanies(data || []);
    } catch (error) {
      console.error('Error fetching companies:', error);
    }
  };

  useEffect(() => {
    if (selectedAuditId) {
      fetchResponses();
      
      // Set up real-time subscription for new responses
      const channel = supabase
        .channel('audit_responses_changes')
        .on(
          'postgres_changes',
          {
            event: '*',
            schema: 'public',
            table: 'audit_responses',
            filter: `audit_id=eq.${selectedAuditId}`
          },
          (payload) => {
            console.log('Response updated:', payload);
            fetchResponses(); // Refresh responses when there's a change
          }
        )
        .subscribe();

      return () => {
        supabase.removeChannel(channel);
      };
    }
  }, [selectedAuditId]);

  // Auto-export functionality for iframe mode
  useEffect(() => {
    if (autoExport && fileName && responses.length > 0) {
      // Wait a bit for rendering to complete
      const timer = setTimeout(async () => {
        try {
          const html2canvas = (await import('html2canvas')).default;
          const element = document.getElementById(autoExport);
          
          if (!element) {
            if (inIframe) {
              window.parent.postMessage({ 
                type: 'EXPORT_ERROR', 
                error: 'Element not found' 
              }, '*');
            }
            return;
          }

          const canvas = await html2canvas(element, {
            backgroundColor: '#ffffff',
            scale: 2,
          });

          const imageData = canvas.toDataURL('image/png');
          
          if (inIframe) {
            // Send data back to parent window
            window.parent.postMessage({ 
              type: 'EXPORT_COMPLETE', 
              imageData, 
              fileName 
            }, '*');
          }
        } catch (error) {
          console.error('Auto-export error:', error);
          if (inIframe) {
            window.parent.postMessage({ 
              type: 'EXPORT_ERROR', 
              error: String(error) 
            }, '*');
          }
        }
      }, 1500); // Wait for charts to render

      return () => clearTimeout(timer);
    }
  }, [autoExport, fileName, responses, inIframe]);

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
      let query = supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at, company_name, partner_company_id')
        .eq('is_active', true);

      // Filter by company for partners
      if (packageType === 'partner' && selectedCompanyId && selectedCompanyId !== 'all') {
        query = query.eq('partner_company_id', selectedCompanyId);
      }

      const { data } = await query.order('start_date', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      } else {
        setAudits([]);
        setSelectedAuditId('');
      }
    } catch (error) {
      console.error('Error fetching assessments:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    }
  };

  // Refetch audits when selected company changes
  useEffect(() => {
    if (packageType === 'partner') {
      fetchAudits();
    }
  }, [selectedCompanyId, packageType]);

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

  const utilization = totalResponses > 0 ? (usedBranch / totalResponses) * 100 : 0;
  const participationRate = employeeCount > 0 ? (totalResponses / employeeCount) * 100 : 0;
  const usageRateFromRespondents = totalResponses > 0 ? (usedBranch / totalResponses) * 100 : 0;
  const estimatedUsers = Math.round((utilization / 100) * employeeCount);
  
  const satisfactionScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_satisfaction)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );
  const satisfactionIndex = (parseFloat(satisfactionScore) / 5) * 100;

  // Calculate 4Score metrics
  const awarenessResponses = responses.filter(r => 
    r.employee_metadata?.branch === 'used' || r.employee_metadata?.branch === 'not_used'
  );
  
  // Awareness Score - General Awareness Rate (percentage who know about the program)
  const awarenessRate = totalResponses > 0 ? ((awarenessResponses.length / totalResponses) * 100) : 0;
  const awarenessScore = awarenessRate.toFixed(1);

  const trustResponses = responses.filter(r => 
    r.employee_metadata?.branch === 'used' || r.employee_metadata?.branch === 'not_used'
  );
  
  // Calculate Trust Index (matching TrustWillingness page calculation)
  const usedTrustResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  
  const usedAnonymityScore = parseFloat(calculateAverage(
    usedTrustResponses
      .map(r => r.responses?.u_trust_anonymity)
      .filter(v => typeof v === 'number' && !isNaN(v))
  ));
  
  const usedEmployerFearScore = parseFloat(calculateAverage(
    usedTrustResponses
      .map(r => r.responses?.u_trust_employer)
      .filter(v => typeof v === 'number' && !isNaN(v))
  ));
  
  const usedColleaguesFearScore = parseFloat(calculateAverage(
    usedTrustResponses
      .map(r => r.responses?.u_trust_colleagues)
      .filter(v => typeof v === 'number' && !isNaN(v))
  ));
  
  const likelihoodScore = parseFloat(calculateAverage(
    usedTrustResponses
      .map(r => r.responses?.u_trust_likelihood)
      .filter(v => typeof v === 'number' && !isNaN(v))
  ));
  
  const trustProfileData = [
    { score: usedAnonymityScore },
    { score: 5 - usedEmployerFearScore }, // Inverted scale
    { score: 5 - usedColleaguesFearScore }, // Inverted scale
    { score: likelihoodScore }
  ];
  
  const trustIndex = trustProfileData.reduce((sum, item) => sum + (isNaN(item.score) ? 0 : item.score), 0) / trustProfileData.length;
  const trustScore = trustIndex.toFixed(1);

  // Usage Score - Combined future usage intent (from both users and non-users)
  const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const wouldUseYes = notUsedResponses.filter(r => r.responses?.nu_usage_would_use === 'yes' || r.responses?.nu_usage_would_use === 'Igen').length;
  const wouldUseNo = notUsedResponses.filter(r => r.responses?.nu_usage_would_use === 'no' || r.responses?.nu_usage_would_use === 'Nem').length;
  const wouldUseTotal = wouldUseYes + wouldUseNo;
  const futureUsageIntentNonUsers = wouldUseTotal > 0 ? ((wouldUseYes / wouldUseTotal) * 100) : 0;
  
  // Users' likelihood to use again (1-5 scale, convert to percentage)
  const usedLikelihoodValues = usedTrustResponses
    .map(r => r.responses?.u_trust_likelihood)
    .filter(v => typeof v === 'number' && !isNaN(v));
  const futureUsageIntentUsers = usedLikelihoodValues.length > 0 
    ? ((parseFloat(calculateAverage(usedLikelihoodValues)) / 5) * 100)
    : 0;
  
  // Combined usage score (average of both groups if both exist)
  let usageScore: string;
  if (futureUsageIntentNonUsers > 0 && futureUsageIntentUsers > 0) {
    usageScore = ((futureUsageIntentNonUsers + futureUsageIntentUsers) / 2).toFixed(1);
  } else if (futureUsageIntentNonUsers > 0) {
    usageScore = futureUsageIntentNonUsers.toFixed(1);
  } else if (futureUsageIntentUsers > 0) {
    usageScore = futureUsageIntentUsers.toFixed(1);
  } else {
    usageScore = '0.0';
  }

  // Impact Score - Average of all impact metrics
  const usedImpactResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  
  const satisfactionValues = usedImpactResponses
    .map(r => r.responses?.u_impact_satisfaction)
    .filter(v => typeof v === 'number' && !isNaN(v));
  
  const problemSolvingValues = usedImpactResponses
    .map(r => r.responses?.u_impact_problem_solving)
    .filter(v => typeof v === 'number' && !isNaN(v));
  
  const wellbeingValues = usedImpactResponses
    .map(r => r.responses?.u_impact_wellbeing)
    .filter(v => typeof v === 'number' && !isNaN(v));
  
  const performanceValues = usedImpactResponses
    .map(r => r.responses?.u_impact_performance)
    .filter(v => typeof v === 'number' && !isNaN(v));
  
  const consistencyValues = usedImpactResponses
    .map(r => r.responses?.u_impact_consistency)
    .filter(v => typeof v === 'number' && !isNaN(v));
  
  const impactMetrics = [
    satisfactionValues.length > 0 ? parseFloat(calculateAverage(satisfactionValues)) : 0,
    problemSolvingValues.length > 0 ? parseFloat(calculateAverage(problemSolvingValues)) : 0,
    wellbeingValues.length > 0 ? parseFloat(calculateAverage(wellbeingValues)) : 0,
    performanceValues.length > 0 ? parseFloat(calculateAverage(performanceValues)) : 0,
    consistencyValues.length > 0 ? parseFloat(calculateAverage(consistencyValues)) : 0,
  ].filter(v => v > 0);
  
  const avgImpact = impactMetrics.length > 0 
    ? (impactMetrics.reduce((sum, v) => sum + v, 0) / impactMetrics.length)
    : 0;
  
  const impactScore = avgImpact.toFixed(1);

  // Additional metrics for Overview tab
  const npsScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_nps)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );

  const problemSolvingScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_problem_solving)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );

  const performanceScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_performance)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );

  const wellbeingScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_wellbeing)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );

  const consistencyScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_consistency)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );

  // Pie chart data for participation breakdown
  const pieData = [
    { name: 'A program aktív felhasználói', value: usedBranch, color: 'hsl(var(--chart-2))' },
    { name: 'Akik eddig nem vették igénybe a programot', value: notUsedBranch, color: 'hsl(var(--chart-3))' },
    { name: 'Nem tudtak róla', value: redirectBranch, color: '#000099' },
  ].filter(item => item.value > 0);

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


  // Render content based on activeTab
  const renderContent = () => {
    if (activeTab === "overview") {
      return (
        <div className="space-y-6">
          <div className="space-y-4 mb-6">
            <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
              <div className="flex-1">
                <div className="flex items-center gap-4 mb-2">
                  <h2 className="text-2xl font-bold">Összefoglaló Riport</h2>
                  <ReportNavigation currentTab={activeTab} />
                </div>
                <p className="text-muted-foreground">
                  A program átfogó mutatói: igénybevétel, elégedettség és részvétel
                </p>
              </div>
              <Button 
                variant="outline" 
                size="sm"
                onClick={() => fetchResponses()}
                disabled={loadingResponses}
                className="gap-2"
              >
                <RefreshCw className={`h-4 w-4 ${loadingResponses ? 'animate-spin' : ''}`} />
                Frissítés
              </Button>
            </div>
            <div className="flex flex-col md:flex-row md:items-end gap-4">
              <img src={fourScoreLogo} alt="4Score" className="h-6 mb-2" />
              
              <div className="flex flex-col md:flex-row gap-4 md:ml-auto">
                {/* Company selector for partner users - always visible */}
                {packageType === 'partner' && companies.length > 0 && (
                  <div className="flex-1 md:max-w-[300px]">
                    <label className="text-xs text-muted-foreground mb-1.5 block">
                      Ügyfélcég szűrése
                    </label>
                    <Select value={selectedCompanyId} onValueChange={setSelectedCompanyId}>
                      <SelectTrigger className="w-full">
                        <SelectValue placeholder="Válassz ügyfélcéget" />
                      </SelectTrigger>
                      <SelectContent align="start">
                        <SelectItem value="all">Összes ügyfélcég</SelectItem>
                        {companies.map((company) => (
                          <SelectItem key={company.id} value={company.id}>
                            {company.company_name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                )}

                {/* Audit selector - only show if there are audits and conditions are met */}
                {audits.length > 0 && (packageType !== 'partner' || (packageType === 'partner' && selectedCompanyId && selectedCompanyId !== 'all')) && (
                  <div className="flex-1 md:max-w-[300px]">
                    <label className="text-xs text-muted-foreground mb-1.5 block">
                      Felmérés kiválasztása
                    </label>
                    <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
                      <SelectTrigger className="w-full">
                        <SelectValue placeholder="Felmérés kiválasztása" />
                      </SelectTrigger>
                      <SelectContent align="start">
                        {audits.map((audit) => (
                          <SelectItem key={audit.id} value={audit.id}>
                            {formatAuditName(audit)}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                )}
              </div>
            </div>
          </div>
          
          {/* Show message if no companies for partner */}
          {packageType === 'partner' && companies.length === 0 && (
            <Card className="border-dashed border-2">
              <CardContent className="py-12 text-center">
                <Building2 className="h-12 w-12 text-muted-foreground/40 mx-auto mb-3" />
                <p className="text-muted-foreground font-medium mb-2">Nincs még ügyfélcég</p>
                <p className="text-sm text-muted-foreground">Regisztrálj egy ügyfélcéget a Partner Központ menüben a kezdéshez.</p>
              </CardContent>
            </Card>
          )}

          {/* Show message if no audits for selected company */}
          {packageType === 'partner' && selectedCompanyId && selectedCompanyId !== 'all' && audits.length === 0 && (
            <Card className="border-dashed border-2">
              <CardContent className="py-12 text-center">
                <Building2 className="h-12 w-12 text-muted-foreground/40 mx-auto mb-3" />
                <p className="text-muted-foreground font-medium mb-2">Nincs elérhető felmérés ehhez a céghez</p>
                <p className="text-sm text-muted-foreground">Hozz létre egy új felmérést az adatok megjelenítéséhez.</p>
              </CardContent>
            </Card>
          )}

          {/* Show message if no audits at all for non-partner or when 'all companies' is selected */}
          {audits.length === 0 && (packageType !== 'partner' || (packageType === 'partner' && selectedCompanyId === 'all')) && (
            <Card className="border-dashed border-2">
              <CardContent className="py-12 text-center">
                <Building2 className="h-12 w-12 text-muted-foreground/40 mx-auto mb-3" />
                <p className="text-muted-foreground font-medium mb-2">Nincs még felmérés</p>
                <p className="text-sm text-muted-foreground">Hozz létre egy új felmérést az adatok megjelenítéséhez.</p>
              </CardContent>
            </Card>
          )}

          {/* Only show cards if audit is selected AND (not partner OR partner has selected a specific company) */}
          {selectedAuditId && (packageType !== 'partner' || (packageType === 'partner' && selectedCompanyId && selectedCompanyId !== 'all')) ? (
            <>
          {/* First Row: Awareness, Trust, Usage, and Impact (4Score Cards) */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {/* Card 1: Awareness */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="awareness-card">
              <CardHeader className="relative z-10" style={{ minHeight: '120px' }}>
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('awareness-card', 'ismertseg')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg flex items-center gap-2">
                  <Eye className="w-5 h-5" />
                  Ismertség Index
                </CardTitle>
                <CardDescription>A program ismeretének aránya</CardDescription>
              </CardHeader>
              <CardContent className="relative z-10 flex flex-col items-center justify-center" style={{ minHeight: '280px' }}>
                <div className="flex items-center justify-center flex-1 w-full">
                  <GaugeChart
                    value={awarenessRate} 
                    maxValue={100}
                    size={200}
                    label={`${awarenessScore}%`}
                    sublabel={`${awarenessResponses.length} / ${totalResponses} fő`}
                    cornerRadius={30}
                    gaugeColor="hsl(var(--chart-2))"
                  />
                </div>
                <p className="text-xs text-muted-foreground text-center px-2">
                  A válaszolók közül ennyien tudtak a programról
                </p>
              </CardContent>
            </Card>

            {/* Card 2: Trust */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="trust-card">
              <div 
                className="absolute inset-0 transition-all duration-500"
                style={{
                  background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(parseFloat(trustScore) / 5) * 100}%, transparent ${(parseFloat(trustScore) / 5) * 100}%, transparent 100%)`,
                  opacity: 0.1
                }}
              />
              <CardHeader className="relative z-10" style={{ minHeight: '120px' }}>
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('trust-card', 'bizalom')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg flex items-center gap-2">
                  <Shield className="w-5 h-5" />
                  Bizalmi Index
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="relative z-10 flex flex-col justify-between" style={{ minHeight: '280px' }}>
                <div className="text-center w-full px-4 flex-1 flex flex-col justify-center">
                  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                    {trustScore}
                  </div>
                  
                  {/* Számegyenes vizualizáció */}
                  <div className="mt-6 px-8">
                    <div className="relative h-2 bg-gray-400 rounded-full">
                      <div 
                        className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                        style={{ 
                          left: `calc(${((parseFloat(trustScore) - 1) / 4) * 100}% - 8px)`,
                          backgroundColor: 'hsl(var(--chart-2))'
                        }}
                      />
                    </div>
                    <div className="flex justify-between text-xs text-muted-foreground mt-1">
                      <span>1</span>
                      <span>2</span>
                      <span>3</span>
                      <span>4</span>
                      <span>5</span>
                    </div>
                  </div>
                </div>
                <p className="text-xs text-muted-foreground text-center px-2 pb-2">
                  Átfogó bizalmi mutató (anonimitás, félelmek, jövőbeli használat)
                </p>
              </CardContent>
            </Card>

            {/* Card 3: Usage */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="usage-card">
              <CardHeader className="relative z-10" style={{ minHeight: '120px' }}>
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('usage-card', 'hasznalat')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg flex items-center gap-2">
                  <Users className="w-5 h-5" />
                  Használat Index
                </CardTitle>
                <CardDescription>Jövőbeli használati szándék</CardDescription>
              </CardHeader>
              <CardContent className="relative z-10 flex flex-col items-center justify-center" style={{ minHeight: '280px' }}>
                <div className="flex items-center justify-center flex-1 w-full">
                  <GaugeChart 
                    value={parseFloat(usageScore)} 
                    maxValue={100}
                    size={200}
                    label={`${usageScore}%`}
                    sublabel={`${usedLikelihoodValues.length + wouldUseTotal} válasz`}
                    cornerRadius={30}
                    gaugeColor="hsl(var(--chart-2))"
                  />
                </div>
                <p className="text-xs text-muted-foreground text-center px-2">
                  {parseFloat(usageScore) >= 70 
                    ? '✓ Magas a jövőbeli használati hajlandóság.'
                    : parseFloat(usageScore) >= 40
                    ? '→ Közepes a nyitottság a program jövőbeni használatára.'
                    : 'ℹ Alacsony a jövőbeni használati szándék - érdemes a bizalomépítésre és kommunikációra fókuszálni.'}
                </p>
              </CardContent>
            </Card>

            {/* Card 4: Impact */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="impact-card">
              <div 
                className="absolute inset-0 transition-all duration-500"
                style={{
                  background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(parseFloat(impactScore) / 5) * 100}%, transparent ${(parseFloat(impactScore) / 5) * 100}%, transparent 100%)`,
                  opacity: 0.1
                }}
              />
              <CardHeader className="relative z-10" style={{ minHeight: '120px' }}>
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('impact-card', 'hatas')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg flex items-center gap-2">
                  <TrendingUp className="w-5 h-5" />
                  Hatás Index
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="relative z-10 flex flex-col justify-between" style={{ minHeight: '280px' }}>
                <div className="text-center w-full px-4 flex-1 flex flex-col justify-center">
                  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                    {impactScore}
                  </div>
                  
                  {/* Számegyenes vizualizáció */}
                  <div className="mt-6 px-8">
                    <div className="relative h-2 bg-gray-400 rounded-full">
                      <div 
                        className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                        style={{ 
                          left: `calc(${((parseFloat(impactScore) - 1) / 4) * 100}% - 8px)`,
                          backgroundColor: 'hsl(var(--chart-2))'
                        }}
                      />
                    </div>
                    <div className="flex justify-between text-xs text-muted-foreground mt-1">
                      <span>1</span>
                      <span>2</span>
                      <span>3</span>
                      <span>4</span>
                      <span>5</span>
                    </div>
                  </div>
                </div>
                <p className="text-xs text-muted-foreground text-center px-2 pb-2">
                  Ennyire hasznos segítség a program összeségében.
                </p>
              </CardContent>
            </Card>
          </div>

          {/* Second Row: Utilization and Satisfaction Gauges */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card id="utilization-card">
              <CardHeader className="relative">
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('utilization-card', 'igenybeveltel')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg">Igénybevétel (Utilization)</CardTitle>
                <CardDescription>Hány munkavállaló használja a programot</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <p className="text-sm text-muted-foreground">
                  <strong>Számítási módszer:</strong> Mivel a tényleges használói számot csak a szolgáltató ismeri, 
                  mi a felmérésből kapott arányokat vetítjük ki. A válaszolók {usageRateFromRespondents.toFixed(1)}%-a 
                  használta a programot, ezt az arányt alkalmazzuk a teljes {employeeCount} fős létszámra.
                </p>
                <div className="flex flex-col items-center justify-center gap-4">
                  <div className="flex flex-col md:flex-row items-center gap-4 w-full justify-center">
                    <div className="relative flex items-center justify-center" style={{ width: '200px', height: '200px' }}>
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#3572ef"
                        strokeWidth="1.5"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className="w-full h-full absolute"
                        style={{ maxWidth: '160px', maxHeight: '160px' }}
                      >
                        <defs>
                          <clipPath id="smile-fill-clip">
                            <circle cx="12" cy="12" r="10"/>
                          </clipPath>
                        </defs>
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                        <line x1="9" x2="9.01" y1="9" y2="9"/>
                        <line x1="15" x2="15.01" y1="9" y2="9"/>
                      </svg>
                      
                      {/* Fill overlay */}
                      <svg
                        viewBox="0 0 24 24"
                        className="w-full h-full absolute"
                        style={{ maxWidth: '160px', maxHeight: '160px' }}
                      >
                        <defs>
                          <linearGradient id="smile-gradient" x1="0%" y1="100%" x2="0%" y2="0%">
                            <stop offset="0%" stopColor="hsl(var(--chart-2))" stopOpacity="0.15"/>
                            <stop offset={`${utilization}%`} stopColor="hsl(var(--chart-2))" stopOpacity="0.15"/>
                            <stop offset={`${utilization}%`} stopColor="transparent" stopOpacity="0"/>
                            <stop offset="100%" stopColor="transparent" stopOpacity="0"/>
                          </linearGradient>
                          <clipPath id="smile-clip-path">
                            <circle cx="12" cy="12" r="10"/>
                          </clipPath>
                        </defs>
                        <circle cx="12" cy="12" r="10" fill="url(#smile-gradient)" clipPath="url(#smile-clip-path)"/>
                      </svg>
                    </div>
                    
                    <div className="text-center">
                      <div className="text-4xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                        {utilization.toFixed(1)}%
                      </div>
                    </div>
                  </div>
                  
                  <div className="text-sm text-muted-foreground text-center w-full md:hidden">
                    ~{estimatedUsers} / {employeeCount} fő (becsült)
                  </div>
                  <div className="text-sm text-muted-foreground hidden md:block">
                    ~{estimatedUsers} / {employeeCount} fő (becsült)
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card id="satisfaction-card">
              <CardHeader className="relative">
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('satisfaction-card', 'elegedettsegi-index')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg">Elégedettségi Index</CardTitle>
                <CardDescription>Általános elégedettség a használók körében</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <p className="text-sm text-muted-foreground">
                  Ez a mutató azt méri, hogy az EAP programot használók mennyire elégedettek a szolgáltatással. 
                  Az érték az általános elégedettséget tükrözi 1-5 skálán, százalékos formában megjelenítve.
                </p>
                <div className="flex flex-col items-center justify-center gap-4">
                  <div className="flex flex-col md:flex-row items-center gap-4 w-full justify-center">
                    <div className="relative flex items-center justify-center" style={{ width: '200px', height: '200px' }}>
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#3572ef"
                        strokeWidth="1.5"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className="w-full h-full absolute"
                        style={{ maxWidth: '160px', maxHeight: '160px' }}
                      >
                        <defs>
                          <clipPath id="thumbs-fill-clip">
                            <path d="M7 10v12"/>
                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                          </clipPath>
                        </defs>
                        <path d="M7 10v12"/>
                        <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                      </svg>
                      
                      {/* Fill overlay */}
                      <svg
                        viewBox="0 0 24 24"
                        className="w-full h-full absolute"
                        style={{ maxWidth: '160px', maxHeight: '160px' }}
                      >
                        <defs>
                          <linearGradient id="thumbs-gradient" x1="0%" y1="100%" x2="0%" y2="0%">
                            <stop offset="0%" stopColor="hsl(var(--chart-2))" stopOpacity="0.15"/>
                            <stop offset={`${satisfactionIndex}%`} stopColor="hsl(var(--chart-2))" stopOpacity="0.15"/>
                            <stop offset={`${satisfactionIndex}%`} stopColor="transparent" stopOpacity="0"/>
                            <stop offset="100%" stopColor="transparent" stopOpacity="0"/>
                          </linearGradient>
                          <clipPath id="thumbs-clip-path">
                            <path d="M7 10v12"/>
                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                          </clipPath>
                        </defs>
                        <path d="M7 10v12" fill="url(#thumbs-gradient)" clipPath="url(#thumbs-clip-path)"/>
                        <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z" fill="url(#thumbs-gradient)" clipPath="url(#thumbs-clip-path)"/>
                      </svg>
                    </div>
                    
                    <div className="text-center">
                      <div className="text-4xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                        {satisfactionIndex.toFixed(0)}%
                      </div>
                    </div>
                  </div>
                  
                  <div className="text-sm text-muted-foreground text-center w-full md:hidden">
                    {satisfactionScore}/5
                  </div>
                  <div className="text-sm text-muted-foreground hidden md:block">
                    {satisfactionScore}/5
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Third Row: Participation and Satisfaction Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Participation Chart */}
            <Card id="participation-card">
              <CardHeader className="relative">
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('participation-card', 'reszveteli-arany')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg">Részvételi arány</CardTitle>
                <CardDescription>{participationRate.toFixed(1)}%</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  A részvételi arány azt mutatja, hogy a munkavállalók hány százaléka töltötte ki a felmérést. 
                  Jelenleg {totalResponses} fő válaszolt {employeeCount} alkalmazottból.
                </p>
                <p className="text-sm text-muted-foreground mb-4">
                  <strong>A kitöltők három kategóriába sorolhatók:</strong><br/>
                  <strong>Használók:</strong> A program aktív felhasználói<br/>
                  <strong>Nem használók:</strong> Akik eddig nem vették igénybe a programot<br/>
                  <strong>Nem tudtak róla:</strong> Azok, akik nem hallottak a programról
                </p>
                <div className="h-[250px] flex items-center justify-center">
                  <ResponsiveContainer width="100%" height="100%">
                    <PieChart>
                      <Pie
                        data={pieData}
                        cx="50%"
                        cy="50%"
                        labelLine={false}
                        label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                        outerRadius={80}
                        fill="#8884d8"
                        dataKey="value"
                      >
                        {pieData.map((entry, index) => (
                          <Cell key={`cell-${index}`} fill={entry.color} />
                        ))}
                      </Pie>
                      <Tooltip />
                    </PieChart>
                  </ResponsiveContainer>
                </div>
              </CardContent>
            </Card>

            {/* Satisfaction Metrics */}
            <Card id="satisfaction-metrics-card">
              <CardHeader className="relative">
                <Button
                  variant="ghost"
                  size="icon"
                  className="absolute right-2 top-2 h-8 w-8"
                  onClick={() => exportCardToPNG('satisfaction-metrics-card', 'elegedettsegi-metrikak')}
                >
                  <Download className="h-4 w-4" />
                </Button>
                <CardTitle className="text-lg">Elégedettségi mutatók</CardTitle>
                <CardDescription>1-5 skálán értékelve, a programot használók körében</CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">NPS pontszám</span>
                    <span className="font-semibold">{npsScore}/10</span>
                  </div>
                  <Progress value={parseFloat(npsScore) * 10} style={{ '--progress-background': '#3366ff' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Mennyire valószínű, hogy ajánlaná a szolgáltatást</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Problémamegoldás</span>
                    <span className="font-semibold">{problemSolvingScore}/5</span>
                  </div>
                  <Progress value={parseFloat(problemSolvingScore) * 20} style={{ '--progress-background': '#3366ff' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Mennyire hatékony volt a program a problémák kezelésében</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Teljesítmény</span>
                    <span className="font-semibold">{performanceScore}/5</span>
                  </div>
                  <Progress value={parseFloat(performanceScore) * 20} style={{ '--progress-background': '#3366ff' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Teljesítmény javulása a program használata után</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Jóllét</span>
                    <span className="font-semibold">{wellbeingScore}/5</span>
                  </div>
                  <Progress value={parseFloat(wellbeingScore) * 20} style={{ '--progress-background': '#3366ff' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Jóllét javulása a program használata után</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Konzisztencia</span>
                    <span className="font-semibold">{consistencyScore}/5</span>
                  </div>
                  <Progress value={parseFloat(consistencyScore) * 20} style={{ '--progress-background': '#3366ff' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Mennyire volt konzisztens a szolgáltatás minősége minden alkalommal</p>
                </div>
              </CardContent>
            </Card>
          </div>
            </>
          ) : null}
        </div>
      );
    }

    // Render other tabs
    switch (activeTab) {
      case "awareness":
        return <Awareness selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} packageType={packageType} companies={companies} selectedCompanyId={selectedCompanyId} onCompanyChange={setSelectedCompanyId} />;
      case "trust":
        return <TrustWillingness selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} packageType={packageType} companies={companies} selectedCompanyId={selectedCompanyId} onCompanyChange={setSelectedCompanyId} />;
      case "usage":
        return <Usage selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} packageType={packageType} companies={companies} selectedCompanyId={selectedCompanyId} onCompanyChange={setSelectedCompanyId} />;
      case "impact":
        return <Impact selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} packageType={packageType} companies={companies} selectedCompanyId={selectedCompanyId} onCompanyChange={setSelectedCompanyId} />;
      case "motivation":
      case "preferences":
        return <CombinedPreferences selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} packageType={packageType} companies={companies} selectedCompanyId={selectedCompanyId} onCompanyChange={setSelectedCompanyId} />;
      case "categories":
        return <UserCategories />;
      case "demographics":
        return <Demographics selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} packageType={packageType} companies={companies} selectedCompanyId={selectedCompanyId} onCompanyChange={setSelectedCompanyId} />;
      case "trends":
        return <Trends />;
      case "compare":
        return <Compare />;
      case "methodology":
        return <Methodology />;
      default:
        return (
          <div className="text-center py-12 text-muted-foreground">
            Hamarosan elérhető: {activeTab}
          </div>
        );
    }
  };

  return (
    <div className="space-y-6 pt-20 md:pt-0">
      {renderContent()}
    </div>
  );
};

export default Reports;
