import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { Download, Eye, Shield, Activity, Target, Users, TrendingUp, Presentation, RefreshCw } from "lucide-react";
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

const Reports = () => {
  const [searchParams] = useSearchParams();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [responses, setResponses] = useState<any[]>([]);
  const [loadingResponses, setLoadingResponses] = useState(false);
  const [employeeCount, setEmployeeCount] = useState<number>(0);
  const activeTab = searchParams.get("sub") || "overview";
  const autoExport = searchParams.get("autoExport");
  const fileName = searchParams.get("fileName");
  const inIframe = searchParams.get("inIframe") === "true";

  useEffect(() => {
    fetchAudits();
    fetchEmployeeCount();
  }, []);

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
      const { data } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at')
        .eq('is_active', true)
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
  
  const awarenessScore = calculateAverage(
    awarenessResponses
      .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
      .filter(v => typeof v === 'number' && !isNaN(v))
  );

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

  // Usage Score - Future Usage Intent (from non-users)
  const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const wouldUseYes = notUsedResponses.filter(r => r.responses?.nu_usage_would_use === 'yes' || r.responses?.nu_usage_would_use === 'Igen').length;
  const wouldUseNo = notUsedResponses.filter(r => r.responses?.nu_usage_would_use === 'no' || r.responses?.nu_usage_would_use === 'Nem').length;
  const wouldUseTotal = wouldUseYes + wouldUseNo;
  const futureUsageIntent = wouldUseTotal > 0 ? ((wouldUseYes / wouldUseTotal) * 100) : 0;
  const usageScore = futureUsageIntent.toFixed(1);

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
    { name: 'Használók', value: usedBranch, color: 'hsl(var(--chart-2))' },
    { name: 'Nem használók', value: notUsedBranch, color: 'hsl(var(--chart-3))' },
    { name: 'Nem tudtak róla', value: redirectBranch, color: 'hsl(var(--chart-4))' },
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

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

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
            {audits.length > 0 && (
              <div className="w-full md:max-w-[300px] md:ml-auto">
                <label className="text-xs text-muted-foreground mb-1.5 block">
                  Felmérés kiválasztása
                </label>
                <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
                  <SelectTrigger className="w-full">
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
              </div>
            )}
          </div>
          {/* First Row: Awareness, Trust, Usage, and Impact (4Score Cards) */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {/* Card 1: Awareness */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="awareness-card">
              <div 
                className="absolute inset-0 transition-all duration-500"
                style={{
                  background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(parseFloat(awarenessScore) / 5) * 100}%, transparent ${(parseFloat(awarenessScore) / 5) * 100}%, transparent 100%)`,
                  opacity: 0.1
                }}
              />
              <CardHeader className="relative z-10">
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
                  Ismertség
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4 relative z-10">
                <div className="text-center">
                  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>{awarenessScore}</div>
                  <p className="text-sm text-muted-foreground mt-2">Mennyire értik a munkavállalók a szolgáltatást</p>
                </div>
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
              <CardHeader className="relative z-10">
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
              <CardContent className="space-y-4 relative z-10">
                <div className="text-center">
                  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>{trustScore}</div>
                  <p className="text-sm text-muted-foreground mt-2">Átfogó bizalmi mutató: anonimitás, félelmek és jövőbeli használati hajlandóság együttesen</p>
                </div>
              </CardContent>
            </Card>

            {/* Card 3: Usage */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="usage-card">
              <div 
                className="absolute inset-0 transition-all duration-500"
                style={{
                  background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${futureUsageIntent}%, transparent ${futureUsageIntent}%, transparent 100%)`,
                  opacity: 0.1
                }}
              />
              <CardHeader className="relative z-10">
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
                  Jövőbeni Használati Szándék
                </CardTitle>
                <CardDescription>Nem használók körében</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4 relative z-10">
                <div className="text-center">
                  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>{usageScore}%</div>
                  <p className="text-sm text-muted-foreground mt-2">A nem használók hány százaléka tervezi igénybe venni a programot</p>
                </div>
              </CardContent>
            </Card>

            {/* Card 4: Impact */}
            <Card className="relative overflow-hidden border-2 border-[#3366ff]" id="impact-card">
              <div 
                className="absolute inset-0 transition-all duration-500"
                style={{
                  background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(avgImpact / 5) * 100}%, transparent ${(avgImpact / 5) * 100}%, transparent 100%)`,
                  opacity: 0.1
                }}
              />
              <CardHeader className="relative z-10">
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
                  Átlagos Hatás
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4 relative z-10">
                <div className="text-center">
                  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>{impactScore}</div>
                  <p className="text-sm text-muted-foreground mt-2">Átlagos hatékonyság az elégedettség, problémamegoldás, jóllét, teljesítmény és konzisztencia alapján</p>
                </div>
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
                <GaugeChart 
                  value={utilization} 
                  maxValue={100}
                  size={280}
                  label={`${utilization.toFixed(1)}%`}
                  sublabel={`~${estimatedUsers} / ${employeeCount} fő (becsült)`}
                  cornerRadius={30}
                />
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
                <GaugeChart 
                  value={satisfactionIndex} 
                  maxValue={100}
                  size={280}
                  label={`${satisfactionIndex.toFixed(0)}%`}
                  sublabel={`${satisfactionScore}/5`}
                  cornerRadius={30}
                />
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
                  <strong>Használók:</strong> Azok, akik ismerik ÉS használták az EAP programot<br/>
                  <strong>Nem használók:</strong> Azok, akik tudnak a programról, de még nem használták<br/>
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
                  <Progress value={parseFloat(npsScore) * 10} style={{ '--progress-background': 'hsl(var(--chart-1))' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Mennyire valószínű, hogy ajánlaná a szolgáltatást</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Problémamegoldás</span>
                    <span className="font-semibold">{problemSolvingScore}/5</span>
                  </div>
                  <Progress value={parseFloat(problemSolvingScore) * 20} style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Mennyire hatékony volt a program a problémák kezelésében</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Teljesítmény</span>
                    <span className="font-semibold">{performanceScore}/5</span>
                  </div>
                  <Progress value={parseFloat(performanceScore) * 20} style={{ '--progress-background': 'hsl(var(--chart-3))' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Teljesítmény javulása a program használata után</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Jóllét</span>
                    <span className="font-semibold">{wellbeingScore}/5</span>
                  </div>
                  <Progress value={parseFloat(wellbeingScore) * 20} style={{ '--progress-background': 'hsl(var(--chart-4))' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Jóllét javulása a program használata után</p>
                </div>
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Konzisztencia</span>
                    <span className="font-semibold">{consistencyScore}/5</span>
                  </div>
                  <Progress value={parseFloat(consistencyScore) * 20} style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties} className="h-3" />
                  <p className="text-xs text-muted-foreground">Mennyire volt konzisztens a szolgáltatás minősége minden alkalommal</p>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      );
    }

    // Render other tabs
    switch (activeTab) {
      case "awareness":
        return <Awareness selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} />;
      case "trust":
        return <TrustWillingness selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} />;
      case "usage":
        return <Usage selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} />;
      case "impact":
        return <Impact selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} />;
      case "motivation":
        return <Motivation selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} />;
      case "categories":
        return <UserCategories />;
      case "demographics":
        return <Demographics selectedAuditId={selectedAuditId} audits={audits} onAuditChange={setSelectedAuditId} />;
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
      {audits.length === 0 && (
        <Card>
          <CardContent className="py-8 text-center text-muted-foreground">
            <p className="text-sm">Nincs elérhető felmérés. Hozz létre egyet az adatok megjelenítéséhez.</p>
          </CardContent>
        </Card>
      )}

      {renderContent()}
    </div>
  );
};

export default Reports;
