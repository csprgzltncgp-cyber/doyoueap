import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { Download, Eye, Shield, Activity, Target, Users, TrendingUp } from "lucide-react";
import { Progress } from "@/components/ui/progress";
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip } from "recharts";
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

const Reports = () => {
  const [searchParams] = useSearchParams();
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
      .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
      .filter(v => v !== undefined)
  );

  const trustResponses = responses.filter(r => 
    r.employee_metadata?.branch === 'used' || r.employee_metadata?.branch === 'not_used'
  );
  
  const trustScore = calculateAverage(
    trustResponses
      .map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity)
      .filter(v => v !== undefined)
  );

  const usageScore = employeeCount > 0 ? ((usedBranch / employeeCount) * 100).toFixed(1) : '0.0';

  const impactScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_satisfaction)
      .filter(v => v !== undefined)
  );

  // Additional metrics for Overview tab
  const npsScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_nps)
      .filter(v => v !== undefined)
  );

  const problemSolvingScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_problem_solving)
      .filter(v => v !== undefined)
  );

  const performanceScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_performance)
      .filter(v => v !== undefined)
  );

  const wellbeingScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_wellbeing)
      .filter(v => v !== undefined)
  );

  const consistencyScore = calculateAverage(
    responses
      .filter(r => r.employee_metadata?.branch === 'used')
      .map(r => r.responses?.u_impact_consistency)
      .filter(v => v !== undefined)
  );

  // Pie chart data for participation breakdown
  const pieData = [
    { name: 'Használók', value: usedBranch, color: 'hsl(var(--chart-1))' },
    { name: 'Nem használók', value: notUsedBranch, color: 'hsl(var(--chart-2))' },
    { name: 'Nem tudtak róla', value: redirectBranch, color: 'hsl(var(--chart-3))' },
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
          {/* Top Row: Utilization and Satisfaction Gauges */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Igénybevétel (Utilization)</CardTitle>
                <CardDescription>Hány munkavállaló használja a programot</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <GaugeChart 
                  value={utilization} 
                  maxValue={100}
                  size={200}
                  label={`${utilization.toFixed(1)}%`}
                  sublabel={`${usedBranch} / ${employeeCount}`}
                />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
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
                  size={200}
                  label={`${satisfactionIndex.toFixed(0)}%`}
                  sublabel={`${satisfactionScore}/5`}
                />
              </CardContent>
            </Card>
          </div>

          {/* Second Row: Participation and Satisfaction Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Participation Chart */}
            <Card>
              <CardHeader>
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
                        innerRadius={60}
                        outerRadius={100}
                        paddingAngle={2}
                        dataKey="value"
                        label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
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
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Elégedettségi mutatók</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Általános elégedettség</span>
                    <span className="font-semibold">{satisfactionScore}/5</span>
                  </div>
                  <Progress value={parseFloat(satisfactionScore) * 20} />
                </div>
                
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">NPS átlag</span>
                    <span className="font-semibold">{npsScore}/10</span>
                  </div>
                  <Progress value={parseFloat(npsScore) * 10} />
                  <p className="text-xs text-muted-foreground">Ajánlási hajlandóság: mennyire ajánlaná másoknak a programot</p>
                </div>

                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Problémamegoldás</span>
                    <span className="font-semibold">{problemSolvingScore}/5</span>
                  </div>
                  <Progress value={parseFloat(problemSolvingScore) * 20} />
                  <p className="text-xs text-muted-foreground">Mennyire segített a program a probléma megoldásában</p>
                </div>

                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Teljesítmény hatás</span>
                    <span className="font-semibold">{performanceScore}/5</span>
                  </div>
                  <Progress value={parseFloat(performanceScore) * 20} />
                  <p className="text-xs text-muted-foreground">A program hatása a munkahelyi teljesítményre</p>
                </div>

                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Wellbeing hatás</span>
                    <span className="font-semibold">{wellbeingScore}/5</span>
                  </div>
                  <Progress value={parseFloat(wellbeingScore) * 20} />
                  <p className="text-xs text-muted-foreground">A program hatása az általános jóllétre és mentális egészségre</p>
                </div>

                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <span className="text-sm">Konzisztencia</span>
                    <span className="font-semibold">{consistencyScore}/5</span>
                  </div>
                  <Progress value={parseFloat(consistencyScore) * 20} />
                  <p className="text-xs text-muted-foreground">Mennyire volt konzisztens a szolgáltatás minősége minden alkalommal</p>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Third Row: Awareness, Trust, Usage, and Impact */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg flex items-center gap-2">
                  <Eye className="w-5 h-5" />
                  Ismertség
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="text-center">
                  <div className="text-5xl font-bold text-primary mb-2">{awarenessScore}</div>
                  <p className="text-sm text-muted-foreground mb-4">Mennyire értik a munkavállalók a szolgáltatást</p>
                </div>
                <Progress value={parseFloat(awarenessScore) * 20} />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-lg flex items-center gap-2">
                  <Shield className="w-5 h-5" />
                  Bizalom
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="text-center">
                  <div className="text-5xl font-bold text-primary mb-2">{trustScore}</div>
                  <p className="text-sm text-muted-foreground mb-4">Mennyire bíznak az anonimitás védelmében</p>
                </div>
                <Progress value={parseFloat(trustScore) * 20} />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-lg flex items-center gap-2">
                  <Users className="w-5 h-5" />
                  Használat
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="text-center">
                  <div className="text-5xl font-bold text-primary mb-2">{usageScore}</div>
                  <p className="text-sm text-muted-foreground mb-4">Felhasználók aránya</p>
                </div>
                <Progress value={parseFloat(usageScore) * 20} />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-lg flex items-center gap-2">
                  <TrendingUp className="w-5 h-5" />
                  Hatás
                </CardTitle>
                <CardDescription>1-5 skála</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="text-center">
                  <div className="text-5xl font-bold text-primary mb-2">{wellbeingScore}</div>
                  <p className="text-sm text-muted-foreground mb-4">Jóllét javulása a program használata után</p>
                </div>
                <Progress value={parseFloat(wellbeingScore) * 20} />
              </CardContent>
            </Card>
          </div>
        </div>
      );
    }

    return (
      <div className="text-center py-12 text-muted-foreground">
        Hamarosan elérhető: {activeTab}
      </div>
    );
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-start gap-4">
        <div>
          <h1 className="text-3xl font-bold mb-2">Riportok</h1>
          <p className="text-muted-foreground text-sm">Felmérések eredményeinek elemzése és értékelése</p>
        </div>

        {audits.length > 0 && (
          <div className="min-w-[300px]">
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
