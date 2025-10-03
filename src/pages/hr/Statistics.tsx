import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { BarChart3, Eye, Shield, Activity, Target, Heart, Users, TrendingUp, GitCompare, Wrench, Briefcase, Sparkles, RotateCw, CheckCircle, XCircle, Bell, Star, Download } from "lucide-react";
import { formatAuditName } from "@/lib/auditUtils";
import { RadialBarChart, RadialBar, Legend, ResponsiveContainer, Cell, PieChart, Pie, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip as RechartsTooltip } from "recharts";
import { GaugeChart } from "@/components/ui/gauge-chart";

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
  const [searchParams, setSearchParams] = useSearchParams();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [responses, setResponses] = useState<any[]>([]);
  const [loadingResponses, setLoadingResponses] = useState(false);
  const [employeeCount, setEmployeeCount] = useState<number>(0);
  const activeTab = searchParams.get("tab") || "overview";
  const autoExport = searchParams.get("autoExport");
  const exportFileName = searchParams.get("fileName");
  const inIframe = searchParams.get("inIframe") === 'true';

  useEffect(() => {
    fetchAudits();
    fetchEmployeeCount();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchResponses();
    }
  }, [selectedAuditId]);

  // Auto-export effect
  useEffect(() => {
    if (autoExport && exportFileName && !loadingResponses && responses.length > 0) {
      // Wait a bit for the DOM to render
      setTimeout(async () => {
        await exportCardToPNG(autoExport, exportFileName, inIframe);
      }, 1500);
    }
  }, [autoExport, exportFileName, loadingResponses, responses, inIframe]);

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
      
      // Parse employee_count as number
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

  // Helper function to calculate average
  const calculateAverage = (values: number[]) => {
    if (values.length === 0) return '0.0';
    return ((values.reduce((a, b) => a + b, 0) / values.length)).toFixed(1);
  };

  // Calculate statistics
  const totalResponses = responses.length;
  const usedBranch = responses.filter(r => r.employee_metadata?.branch === 'used').length;
  const notUsedBranch = responses.filter(r => r.employee_metadata?.branch === 'not_used').length;
  const redirectBranch = responses.filter(r => r.employee_metadata?.branch === 'redirect').length;

  // Calculate main KPIs
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

  // Helper function to count occurrences
  const countOccurrences = (items: string[]) => {
    return items.reduce((acc, item) => {
      acc[item] = (acc[item] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);
  };

  // Helper function to format percentage
  const formatPercentage = (count: number, total: number) => {
    return Math.round((count / total) * 100);
  };


  // PNG Export function for individual cards
  const exportCardToPNG = async (cardId: string, fileName: string, sendToParent: boolean = false) => {
    try {
      const html2canvas = (await import('html2canvas')).default;
      const element = document.getElementById(cardId);
      
      if (!element) {
        toast.error('Panel nem található');
        if (sendToParent && window.parent) {
          window.parent.postMessage({ type: 'EXPORT_ERROR' }, '*');
        }
        return;
      }

      const canvas = await html2canvas(element, {
        backgroundColor: '#ffffff',
        scale: 2,
      });

      const dataUrl = canvas.toDataURL('image/png');

      if (sendToParent && window.parent) {
        // Send image data to parent window
        window.parent.postMessage({ 
          type: 'EXPORT_COMPLETE', 
          imageData: dataUrl,
          fileName: fileName 
        }, '*');
      } else {
        // Download directly
        const link = document.createElement('a');
        link.download = `${fileName}.png`;
        link.href = dataUrl;
        link.click();
        toast.success('PNG sikeresen letöltve!');
      }
    } catch (error) {
      console.error('Error exporting PNG:', error);
      toast.error('Hiba a PNG exportálás során');
      if (sendToParent && window.parent) {
        window.parent.postMessage({ type: 'EXPORT_ERROR' }, '*');
      }
    }
  };


  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="p-8 space-y-6">
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

      <Tabs value={activeTab} onValueChange={(value) => setSearchParams({ tab: value })} className="w-full">
        <TabsList className="w-full justify-start border-b rounded-none h-auto p-0 bg-transparent">
          <TabsTrigger 
            value="overview" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <BarChart3 className="h-4 w-4" />
            Összefoglaló
          </TabsTrigger>
          <TabsTrigger 
            value="awareness" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Eye className="h-4 w-4" />
            Ismertség
          </TabsTrigger>
          <TabsTrigger 
            value="trust" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Shield className="h-4 w-4" />
            Bizalom
          </TabsTrigger>
          <TabsTrigger 
            value="usage" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Activity className="h-4 w-4" />
            Használat
          </TabsTrigger>
          <TabsTrigger 
            value="impact" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Target className="h-4 w-4" />
            Hatás
          </TabsTrigger>
          <TabsTrigger 
            value="motivation" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Heart className="h-4 w-4" />
            Motiváció
          </TabsTrigger>
          <TabsTrigger 
            value="demographics" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <Users className="h-4 w-4" />
            Demográfia
          </TabsTrigger>
          <TabsTrigger 
            value="trends" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <TrendingUp className="h-4 w-4" />
            Trendek
          </TabsTrigger>
          <TabsTrigger 
            value="compare" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent gap-2"
          >
            <GitCompare className="h-4 w-4" />
            Összehasonlítás
          </TabsTrigger>
        </TabsList>

        {/* OVERVIEW TAB */}
        <TabsContent value="overview" className="mt-6">
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
                      
                      {/* Detailed breakdown */}
                      <div className="w-full mt-6 space-y-3">
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground flex items-center gap-2">
                            <BarChart3 className="h-4 w-4" />
                            Részvételi arány
                          </span>
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
                          <span className="text-sm text-muted-foreground flex items-center gap-2">
                            <CheckCircle className="h-4 w-4" />
                            Használók (a kitöltőkből)
                          </span>
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
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground flex items-center gap-2">
                            <XCircle className="h-4 w-4" />
                            Nem használók
                          </span>
                          <div className="flex items-center gap-2">
                            <div className="w-32 bg-muted rounded-full h-2">
                              <div
                                className="h-2 rounded-full"
                                style={{ 
                                  width: `${((notUsedBranch / totalResponses) * 100)}%`,
                                  backgroundColor: '#3366ff'
                                }}
                              />
                            </div>
                            <span className="font-semibold w-16 text-right">{((notUsedBranch / totalResponses) * 100).toFixed(1)}%</span>
                          </div>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground flex items-center gap-2">
                            <Bell className="h-4 w-4" />
                            Nem tudtak róla
                          </span>
                          <div className="flex items-center gap-2">
                            <div className="w-32 bg-muted rounded-full h-2">
                              <div
                                className="h-2 rounded-full"
                                style={{ 
                                  width: `${((redirectBranch / totalResponses) * 100)}%`,
                                  backgroundColor: '#3366ff'
                                }}
                              />
                            </div>
                            <span className="font-semibold w-16 text-right">{((redirectBranch / totalResponses) * 100).toFixed(1)}%</span>
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
                        onClick={() => exportCardToPNG('satisfaction-card', 'elégedettség')}
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
                        sublabel={`${satisfactionScore}/5`}
                        cornerRadius={0}
                      />
                      
                      {/* Detailed breakdown */}
                      <div className="w-full mt-6 space-y-3">
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground flex items-center gap-2">
                            <Star className="h-4 w-4" />
                            Általános elégedettség
                          </span>
                          <div className="flex items-center gap-2">
                            <div className="w-32 bg-muted rounded-full h-2">
                              <div
                                className="h-2 rounded-full"
                                style={{ 
                                  width: `${(parseFloat(satisfactionScore) / 5) * 100}%`,
                                  backgroundColor: '#3366ff'
                                }}
                              />
                            </div>
                            <span className="font-semibold w-16 text-right">{satisfactionScore}/5</span>
                          </div>
                        </div>
                          <div>
                            <div className="flex justify-between items-center">
                              <span className="text-sm text-muted-foreground flex items-center gap-2">
                                <TrendingUp className="h-4 w-4" />
                                NPS átlag
                              </span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${(parseFloat(calculateAverage(
                                        responses
                                          .filter(r => r.employee_metadata?.branch === 'used')
                                          .map(r => r.responses?.u_impact_nps)
                                          .filter(v => v !== undefined)
                                      )) / 10) * 100}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">
                                  {calculateAverage(
                                    responses
                                      .filter(r => r.employee_metadata?.branch === 'used')
                                      .map(r => r.responses?.u_impact_nps)
                                      .filter(v => v !== undefined)
                                  )}/10
                                </span>
                              </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">Ajánlási hajlandóság: mennyire ajánlaná másoknak a programot</p>
                          </div>
                          <div>
                            <div className="flex justify-between items-center">
                              <span className="text-sm text-muted-foreground flex items-center gap-2">
                                <Wrench className="h-4 w-4" />
                                Problémamegoldás
                              </span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${(parseFloat(calculateAverage(
                                        responses
                                          .filter(r => r.employee_metadata?.branch === 'used')
                                          .map(r => r.responses?.u_impact_problem_solving)
                                          .filter(v => v !== undefined)
                                      )) / 5) * 100}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">
                                  {calculateAverage(
                                    responses
                                      .filter(r => r.employee_metadata?.branch === 'used')
                                      .map(r => r.responses?.u_impact_problem_solving)
                                      .filter(v => v !== undefined)
                                  )}/5
                                </span>
                              </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">Mennyire segített a program a probléma megoldásában</p>
                          </div>
                          <div>
                            <div className="flex justify-between items-center">
                              <span className="text-sm text-muted-foreground flex items-center gap-2">
                                <Briefcase className="h-4 w-4" />
                                Teljesítmény hatás
                              </span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${(parseFloat(calculateAverage(
                                        responses
                                          .filter(r => r.employee_metadata?.branch === 'used')
                                          .map(r => r.responses?.u_impact_performance)
                                          .filter(v => v !== undefined)
                                      )) / 5) * 100}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">
                                  {calculateAverage(
                                    responses
                                      .filter(r => r.employee_metadata?.branch === 'used')
                                      .map(r => r.responses?.u_impact_performance)
                                      .filter(v => v !== undefined)
                                  )}/5
                                </span>
                              </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">A program hatása a munkahelyi teljesítményre</p>
                          </div>
                          <div>
                            <div className="flex justify-between items-center">
                              <span className="text-sm text-muted-foreground flex items-center gap-2">
                                <Sparkles className="h-4 w-4" />
                                Wellbeing hatás
                              </span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${(parseFloat(calculateAverage(
                                        responses
                                          .filter(r => r.employee_metadata?.branch === 'used')
                                          .map(r => r.responses?.u_impact_wellbeing)
                                          .filter(v => v !== undefined)
                                      )) / 5) * 100}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">
                                  {calculateAverage(
                                    responses
                                      .filter(r => r.employee_metadata?.branch === 'used')
                                      .map(r => r.responses?.u_impact_wellbeing)
                                      .filter(v => v !== undefined)
                                  )}/5
                                </span>
                              </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">A program hatása az általános jóllétre és mentális egészségre</p>
                          </div>
                          <div>
                            <div className="flex justify-between items-center">
                              <span className="text-sm text-muted-foreground flex items-center gap-2">
                                <RotateCw className="h-4 w-4" />
                                Konzisztencia
                              </span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${(parseFloat(calculateAverage(
                                        responses
                                          .filter(r => r.employee_metadata?.branch === 'used')
                                          .map(r => r.responses?.u_impact_consistency)
                                          .filter(v => v !== undefined)
                                      )) / 5) * 100}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">
                                  {calculateAverage(
                                    responses
                                      .filter(r => r.employee_metadata?.branch === 'used')
                                      .map(r => r.responses?.u_impact_consistency)
                                      .filter(v => v !== undefined)
                                  )}/5
                                </span>
                              </div>
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">Mennyire volt konzisztens a szolgáltatás minősége minden alkalommal</p>
                          </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>

              {/* Quick Overview of 4 themes */}
              <Card>
                <CardHeader>
                  <CardTitle>4Score Áttekintés</CardTitle>
                  <CardDescription>Kulcsmutatók a négy pillér mentén</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid md:grid-cols-4 gap-4">
                    {/* Awareness */}
                    <Card id="awareness-card">
                      <CardHeader className="pb-3">
                        <div className="flex justify-between items-center">
                          <CardTitle className="text-sm flex items-center gap-2">
                            <Eye className="h-4 w-4" />
                            Ismertség (1-5 skála)
                          </CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('awareness-card', 'ismertség')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_awareness_understanding)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire értik a munkavállalók a szolgáltatást</p>
                      </CardContent>
                    </Card>

                    {/* Trust */}
                    <Card id="trust-card">
                      <CardHeader className="pb-3">
                        <div className="flex justify-between items-center">
                          <CardTitle className="text-sm flex items-center gap-2">
                            <Shield className="h-4 w-4" />
                            Bizalom (1-5 skála)
                          </CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('trust-card', 'bizalom')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_anonymity)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire bíznak az anonimitás védelmében</p>
                      </CardContent>
                    </Card>

                    {/* Usage - Problem Solving */}
                    <Card id="usage-card">
                      <CardHeader className="pb-3">
                        <div className="flex justify-between items-center">
                          <CardTitle className="text-sm flex items-center gap-2">
                            <Activity className="h-4 w-4" />
                            Használat (1-5 skála)
                          </CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('usage-card', 'használat')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_impact_problem_solving)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire segített a program a problémák kezelésében</p>
                      </CardContent>
                    </Card>

                    {/* Impact */}
                    <Card id="impact-card">
                      <CardHeader className="pb-3">
                        <div className="flex justify-between items-center">
                          <CardTitle className="text-sm flex items-center gap-2">
                            <Target className="h-4 w-4" />
                            Hatás (1-5 skála)
                          </CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('impact-card', 'hatás')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_impact_wellbeing)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Jóllét javulása a program használata után</p>
                      </CardContent>
                    </Card>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}
        </TabsContent>

        <TabsContent value="awareness" className="mt-6">
          {loadingResponses ? (
            <div className="text-center py-12 text-muted-foreground">
              Adatok betöltése...
            </div>
          ) : totalResponses === 0 ? (
            <Card className="p-12">
              <CardContent className="text-center text-muted-foreground">
                <p className="text-lg mb-2">Nincs megjeleníthető adat</p>
                <p className="text-sm">Ehhez az audithoz még nincsenek olyan válaszok, ahol a kitöltők tudtak a programról vagy használták azt. Csak "Nem tudtam róla" válaszok érkeztek eddig.</p>
              </CardContent>
            </Card>
          ) : (
            <div className="space-y-6">
              <div className="grid grid-cols-3 gap-4">
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Használók</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">{usedBranch}</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Nem használók</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">{notUsedBranch}</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Összes</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">{usedBranch + notUsedBranch}</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
              </div>

              <Card id="awareness-pie-card">
                <CardHeader>
                  <div className="flex justify-between items-center">
                    <div>
                      <CardTitle>Awareness Mutatók - Összesített</CardTitle>
                      <CardDescription>
                        Válaszadók megoszlása - használók vs. nem használók
                      </CardDescription>
                    </div>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => exportCardToPNG('awareness-pie-card', 'awareness-megoszlas')}
                      className="h-8 w-8 p-0"
                    >
                      <Download className="h-4 w-4" />
                    </Button>
                  </div>
                </CardHeader>
                <CardContent>
                  <ResponsiveContainer width="100%" height={400}>
                    <PieChart>
                      <Pie
                        data={[
                          { name: 'Használók', value: usedBranch, color: '#3366ff' },
                          { name: 'Nem használók', value: notUsedBranch, color: '#3abef9' },
                        ]}
                        cx="50%"
                        cy="50%"
                        labelLine={false}
                        label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                        outerRadius={120}
                        fill="#8884d8"
                        dataKey="value"
                      >
                        <Cell fill="#3366ff" />
                        <Cell fill="#3abef9" />
                      </Pie>
                      <RechartsTooltip />
                      <Legend />
                    </PieChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>

              <Card id="awareness-bar-card">
                <CardHeader>
                  <div className="flex justify-between items-center">
                    <div>
                      <CardTitle>Awareness Mutatók - Részletes</CardTitle>
                      <CardDescription>
                        Likert skála átlagok (1-5), ahol 1 = egyáltalán nem, 5 = teljes mértékben
                      </CardDescription>
                    </div>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => exportCardToPNG('awareness-bar-card', 'awareness-reszletes')}
                      className="h-8 w-8 p-0"
                    >
                      <Download className="h-4 w-4" />
                    </Button>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="space-y-8">
                    <ResponsiveContainer width="100%" height={400}>
                      <BarChart data={[
                        {
                          metric: 'Szolgáltatás megértése',
                          used: parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_awareness_understanding)
                              .filter(v => v !== undefined)
                          )),
                          notUsed: parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'not_used')
                              .map(r => r.responses?.nu_awareness_understanding)
                              .filter(v => v !== undefined)
                          )),
                        },
                        {
                          metric: 'Igénybevételi tudás',
                          used: parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_awareness_how_to_use)
                              .filter(v => v !== undefined)
                          )),
                          notUsed: 0,
                        },
                        {
                          metric: 'Elérhetőség érzete',
                          used: parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_awareness_accessibility)
                              .filter(v => v !== undefined)
                          )),
                          notUsed: 0,
                        },
                      ]}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="metric" />
                        <YAxis domain={[0, 5]} />
                        <RechartsTooltip />
                        <Legend />
                        <Bar dataKey="used" name="Használók" fill="#3366ff" />
                        <Bar dataKey="notUsed" name="Nem használók" fill="#3abef9" />
                      </BarChart>
                    </ResponsiveContainer>

                    <div className="grid grid-cols-1 gap-4">
                      <Card>
                        <CardHeader>
                          <CardTitle className="text-lg">Szolgáltatás megértése</CardTitle>
                          <CardDescription>Mennyire értik a munkavállalók, hogy mit nyújt az EAP program (1-5 skála)</CardDescription>
                        </CardHeader>
                        <CardContent>
                          <div className="grid grid-cols-2 gap-4">
                            <div>
                              <p className="text-sm text-muted-foreground">Használók</p>
                              <p className="text-2xl font-bold" style={{ color: '#3366ff' }}>
                                {calculateAverage(
                                  responses
                                    .filter(r => r.employee_metadata?.branch === 'used')
                                    .map(r => r.responses?.u_awareness_understanding)
                                    .filter(v => v !== undefined)
                                ) || 'N/A'}
                              </p>
                            </div>
                            <div>
                              <p className="text-sm text-muted-foreground">Nem használók</p>
                              <p className="text-2xl font-bold" style={{ color: '#3abef9' }}>
                                {calculateAverage(
                                  responses
                                    .filter(r => r.employee_metadata?.branch === 'not_used')
                                    .map(r => r.responses?.nu_awareness_understanding)
                                    .filter(v => v !== undefined)
                                ) || 'N/A'}
                              </p>
                            </div>
                          </div>
                        </CardContent>
                      </Card>

                      <Card>
                        <CardHeader>
                          <CardTitle className="text-lg">Igénybevételi tudás</CardTitle>
                          <CardDescription>Mennyire tudják a használók, hogyan vehetik igénybe a szolgáltatást (1-5 skála)</CardDescription>
                        </CardHeader>
                        <CardContent>
                          <div className="grid grid-cols-2 gap-4">
                            <div>
                              <p className="text-sm text-muted-foreground">Használók</p>
                              <p className="text-2xl font-bold" style={{ color: '#3366ff' }}>
                                {calculateAverage(
                                  responses
                                    .filter(r => r.employee_metadata?.branch === 'used')
                                    .map(r => r.responses?.u_awareness_how_to_use)
                                    .filter(v => v !== undefined)
                                ) || 'N/A'}
                              </p>
                            </div>
                            <div>
                              <p className="text-sm text-muted-foreground">Nem használók</p>
                              <p className="text-2xl font-bold" style={{ color: '#3abef9' }}>N/A</p>
                            </div>
                          </div>
                        </CardContent>
                      </Card>

                      <Card>
                        <CardHeader>
                          <CardTitle className="text-lg">Elérhetőség érzete</CardTitle>
                          <CardDescription>Mennyire érzik elérhetőnek a használók az EAP szolgáltatást (1-5 skála)</CardDescription>
                        </CardHeader>
                        <CardContent>
                          <div className="grid grid-cols-2 gap-4">
                            <div>
                              <p className="text-sm text-muted-foreground">Használók</p>
                              <p className="text-2xl font-bold" style={{ color: '#3366ff' }}>
                                {calculateAverage(
                                  responses
                                    .filter(r => r.employee_metadata?.branch === 'used')
                                    .map(r => r.responses?.u_awareness_accessibility)
                                    .filter(v => v !== undefined)
                                ) || 'N/A'}
                              </p>
                            </div>
                            <div>
                              <p className="text-sm text-muted-foreground">Nem használók</p>
                              <p className="text-2xl font-bold" style={{ color: '#3abef9' }}>N/A</p>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}
        </TabsContent>

        <TabsContent value="trust" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Bizalom & Hajlandóság Riport</CardTitle>
              <CardDescription>
                Anonimitásba vetett bizalom és használati hajlandóság
              </CardDescription>
            </CardHeader>
            <CardContent>
              {loadingResponses ? (
                <div className="text-center py-12 text-muted-foreground">Adatok betöltése...</div>
              ) : totalResponses === 0 ? (
                <div className="text-center py-12 text-muted-foreground">Még nincs adat</div>
              ) : (
                <div className="space-y-6">
                  <div className="grid grid-cols-3 gap-4">
                    <Card id="trust-users-anonymity-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">Bizalom az anonimitásban - Használók (1-5 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('trust-users-anonymity-card', 'használók-anonimitás')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_anonymity)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire bíznak a használók abban, hogy névtelenségük megmarad</p>
                      </CardContent>
                    </Card>
                    <Card id="trust-users-employer-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">Munkaadói félelem (1-5 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('trust-users-employer-card', 'használók-munkaadói-félelem')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_employer)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire félnek attól, hogy a munkaadó megtudja a használatot</p>
                      </CardContent>
                    </Card>
                    <Card id="trust-users-likelihood-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">Használati hajlandóság (1-5 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('trust-users-likelihood-card', 'használók-hajlandóság')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_likelihood)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire valószínű, hogy újra használnák a szolgáltatást</p>
                      </CardContent>
                    </Card>
                  </div>
                  <Card id="trust-non-users-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Nem használók bizalmi indexei</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('trust-non-users-card', 'nem-használók-bizalom')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-3 gap-4">
                        <Card id="trust-nu-anonymity-card">
                          <CardHeader className="pb-3">
                            <div className="flex justify-between items-start">
                              <CardTitle className="text-sm">Anonimitás (1-5 skála)</CardTitle>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => exportCardToPNG('trust-nu-anonymity-card', 'nem-használók-anonimitás')}
                                className="h-8 w-8 p-0"
                              >
                                <Download className="h-4 w-4" />
                              </Button>
                            </div>
                          </CardHeader>
                          <CardContent>
                            <GaugeChart
                              value={parseFloat(calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'not_used')
                                  .map(r => r.responses?.nu_trust_anonymity)
                                  .filter(v => v !== undefined)
                              ))}
                              maxValue={5}
                              size={150}
                            />
                            <p className="text-xs text-muted-foreground mt-2 text-center">Bizalom az anonimitás védelmében</p>
                          </CardContent>
                        </Card>

                        <Card id="trust-nu-employer-card">
                          <CardHeader className="pb-3">
                            <div className="flex justify-between items-start">
                              <CardTitle className="text-sm">Munkaadói félelem (1-5 skála)</CardTitle>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => exportCardToPNG('trust-nu-employer-card', 'nem-használók-munkaadói-félelem')}
                                className="h-8 w-8 p-0"
                              >
                                <Download className="h-4 w-4" />
                              </Button>
                            </div>
                          </CardHeader>
                          <CardContent>
                            <GaugeChart
                              value={parseFloat(calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'not_used')
                                  .map(r => r.responses?.nu_trust_employer)
                                  .filter(v => v !== undefined)
                              ))}
                              maxValue={5}
                              size={150}
                            />
                            <p className="text-xs text-muted-foreground mt-2 text-center">Félelem a munkaadó tudomásától</p>
                          </CardContent>
                        </Card>

                        <Card id="trust-nu-colleagues-card">
                          <CardHeader className="pb-3">
                            <div className="flex justify-between items-start">
                              <CardTitle className="text-sm">Kollégai megítéléstől félelem (1-5 skála)</CardTitle>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => exportCardToPNG('trust-nu-colleagues-card', 'nem-használók-kollégák')}
                                className="h-8 w-8 p-0"
                              >
                                <Download className="h-4 w-4" />
                              </Button>
                            </div>
                          </CardHeader>
                          <CardContent>
                            <GaugeChart
                              value={parseFloat(calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'not_used')
                                  .map(r => r.responses?.nu_trust_colleagues)
                                  .filter(v => v !== undefined)
                              ))}
                              maxValue={5}
                              size={150}
                            />
                            <p className="text-xs text-muted-foreground mt-2 text-center">Félelem a kollégák negatív véleményétől</p>
                          </CardContent>
                        </Card>
                      </div>
                    </CardContent>
                  </Card>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="usage" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Használat Riport</CardTitle>
              <CardDescription>Használók aktivitása és elégedettsége</CardDescription>
            </CardHeader>
            <CardContent>
              {loadingResponses ? (
                <div className="text-center py-12 text-muted-foreground">Adatok betöltése...</div>
              ) : usedBranch === 0 ? (
                <div className="text-center py-12 text-muted-foreground">Még nincs használó adat</div>
              ) : (
                <div className="space-y-6">
                  <div className="grid grid-cols-4 gap-4">
                    <Card id="usage-satisfaction-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">Elégedettség (1-5 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('usage-satisfaction-card', 'használat-elégedettség')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_impact_satisfaction)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                      </CardContent>
                    </Card>
                    <Card id="usage-problem-solving-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">Problémamegoldás (1-5 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('usage-problem-solving-card', 'problémamegoldás')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_impact_problem_solving)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire segített a program a problémák kezelésében</p>
                      </CardContent>
                    </Card>
                    <Card id="usage-nps-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">NPS átlag (0-10 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('usage-nps-card', 'NPS')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_impact_nps)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={10}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Net Promoter Score: ajánlási hajlandóság másoknak</p>
                      </CardContent>
                    </Card>
                    <Card id="usage-consistency-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <CardTitle className="text-sm">Konzisztencia (1-5 skála)</CardTitle>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('usage-consistency-card', 'konzisztencia')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <GaugeChart
                          value={parseFloat(calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_impact_consistency)
                              .filter(v => v !== undefined)
                          ))}
                          maxValue={5}
                          size={150}
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Szolgáltatás minőségének egyenletessége alkalmanként</p>
                      </CardContent>
                    </Card>
                  </div>

                  {/* Használati részletek Grid */}
                  <div className="grid grid-cols-2 gap-6">
                    <Card>
                      <CardHeader>
                        <CardTitle>Csatornák eloszlása</CardTitle>
                        <CardDescription>Hogyan vették igénybe a programot (több válasz lehetséges)</CardDescription>
                      </CardHeader>
                      <CardContent>
                        <div className="space-y-2">
                          {Object.entries(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .reduce((acc, r) => {
                                const channels = r.responses?.u_usage_channel;
                                if (Array.isArray(channels)) {
                                  channels.forEach(channel => {
                                    acc[channel] = (acc[channel] || 0) + 1;
                                  });
                                }
                                return acc;
                              }, {} as Record<string, number>)
                          )
                          .sort(([, a], [, b]) => (b as number) - (a as number))
                          .map(([channel, count]) => (
                            <div key={channel} className="flex justify-between items-center">
                              <span className="text-sm">{channel}</span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${formatPercentage(count as number, usedBranch)}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">{count as number} ({formatPercentage(count as number, usedBranch)}%)</span>
                              </div>
                            </div>
                          ))}
                        </div>
                      </CardContent>
                    </Card>

                    <Card>
                      <CardHeader>
                        <CardTitle>Témák eloszlása</CardTitle>
                        <CardDescription>Milyen témákban kértek segítséget (több válasz lehetséges)</CardDescription>
                      </CardHeader>
                      <CardContent>
                        <div className="space-y-2">
                          {Object.entries(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .reduce((acc, r) => {
                                const topics = r.responses?.u_usage_topic;
                                if (Array.isArray(topics)) {
                                  topics.forEach(topic => {
                                    acc[topic] = (acc[topic] || 0) + 1;
                                  });
                                }
                                return acc;
                              }, {} as Record<string, number>)
                          )
                          .sort(([, a], [, b]) => (b as number) - (a as number))
                          .map(([topic, count]) => (
                            <div key={topic} className="flex justify-between items-center">
                              <span className="text-sm">{topic}</span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${formatPercentage(count as number, usedBranch)}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="font-semibold w-16 text-right">{count as number} ({formatPercentage(count as number, usedBranch)}%)</span>
                              </div>
                            </div>
                          ))}
                        </div>
                      </CardContent>
                    </Card>
                  </div>

                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Használati gyakoriság eloszlása</CardTitle>
                      <CardDescription>Hányszor vették igénybe a programot az elmúlt évben</CardDescription>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        {Object.entries(
                          countOccurrences(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_usage_frequency)
                              .filter(Boolean)
                          )
                        ).map(([freq, count]) => (
                          <div key={freq} className="flex justify-between items-center">
                            <span className="text-sm">{freq}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="h-2 rounded-full"
                                  style={{ 
                                    width: `${formatPercentage(count, usedBranch)}%`,
                                    backgroundColor: '#3366ff'
                                  }}
                                />
                              </div>
                              <span className="text-sm font-medium w-12 text-right">{count}</span>
                            </div>
                          </div>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="impact" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Hatás Riport</CardTitle>
              <CardDescription>A program hatása a munkavállalókra</CardDescription>
            </CardHeader>
            <CardContent>
              {loadingResponses ? (
                <div className="text-center py-12 text-muted-foreground">Adatok betöltése...</div>
              ) : usedBranch === 0 ? (
                <div className="text-center py-12 text-muted-foreground">Még nincs használó adat</div>
              ) : (
                <div className="grid grid-cols-2 gap-4">
                  <Card id="impact-performance-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Teljesítményre gyakorolt hatás (1-5 skála)</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('impact-performance-card', 'teljesítmény-hatás')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <GaugeChart
                        value={parseFloat(calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_performance)
                            .filter(v => v !== undefined)
                        ))}
                        maxValue={5}
                        size={150}
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">A program hatása a munkahelyi teljesítményre és produktivitásra</p>
                    </CardContent>
                  </Card>
                  <Card id="impact-problem-solving-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Problémamegoldás (1-5 skála)</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('impact-problem-solving-card', 'hatás-problémamegoldás')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <GaugeChart
                        value={parseFloat(calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_problem_solving)
                            .filter(v => v !== undefined)
                        ))}
                        maxValue={5}
                        size={150}
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire hatékonyan segített a program a problémák megoldásában</p>
                    </CardContent>
                  </Card>
                  <Card id="impact-wellbeing-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Általános jóllét (1-5 skála)</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('impact-wellbeing-card', 'jóllét-hatás')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <GaugeChart
                        value={parseFloat(calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_wellbeing)
                            .filter(v => v !== undefined)
                        ))}
                        maxValue={5}
                        size={150}
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">A program hatása az általános jóllétre és mentális egészségre</p>
                    </CardContent>
                  </Card>
                  <Card id="impact-satisfaction-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Elégedettség (1-5 skála)</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('impact-satisfaction-card', 'hatás-elégedettség')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <GaugeChart
                        value={parseFloat(calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_satisfaction)
                            .filter(v => v !== undefined)
                        ))}
                        maxValue={5}
                        size={150}
                      />
                    </CardContent>
                  </Card>
                  <Card id="impact-consistency-card" className="col-span-2">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Szolgáltatás konzisztencia (1-5 skála)</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('impact-consistency-card', 'szolgáltatás-konzisztencia')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <GaugeChart
                        value={parseFloat(calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_consistency)
                            .filter(v => v !== undefined)
                        ))}
                        maxValue={5}
                        size={150}
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">Szolgáltatás minőségének konzisztenciája</p>
                    </CardContent>
                  </Card>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="motivation" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Motiváció Riport</CardTitle>
              <CardDescription>Mi motiválná a nem használókat?</CardDescription>
            </CardHeader>
            <CardContent>
              {loadingResponses ? (
                <div className="text-center py-12 text-muted-foreground">Adatok betöltése...</div>
              ) : notUsedBranch === 0 ? (
                <div className="text-center py-12 text-muted-foreground">Még nincs nem használó adat</div>
              ) : (
                <div className="space-y-6">
                  <Card id="motivation-what-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <div>
                          <CardTitle className="text-sm">Mi kellene a használathoz? (Top motivátorok)</CardTitle>
                          <CardDescription>Mi motiválná a nem használókat a program igénybevételére</CardDescription>
                        </div>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('motivation-what-card', 'motivátorok')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        {Object.entries(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'not_used')
                            .flatMap(r => r.responses?.nu_motivation_what || [])
                            .reduce((acc, item) => {
                              acc[item] = (acc[item] || 0) + 1;
                              return acc;
                            }, {} as Record<string, number>)
                        )
                          .sort(([, a], [, b]) => (b as number) - (a as number))
                          .slice(0, 5)
                          .map(([motivation, count]) => (
                            <div key={motivation} className="flex justify-between items-center">
                              <span className="text-sm">{motivation}</span>
                              <div className="flex items-center gap-2">
                                <div className="w-32 bg-muted rounded-full h-2">
                                  <div
                                    className="h-2 rounded-full"
                                    style={{ 
                                      width: `${formatPercentage(count as number, notUsedBranch)}%`,
                                      backgroundColor: '#3366ff'
                                    }}
                                  />
                                </div>
                                <span className="text-sm font-medium w-12 text-right">{count as number}</span>
                              </div>
                            </div>
                          ))}
                      </div>
                    </CardContent>
                  </Card>
                  <div className="grid grid-cols-2 gap-4">
                    <Card id="motivation-expert-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <div>
                            <CardTitle className="text-sm">Preferált szakértő típus</CardTitle>
                            <CardDescription>Milyen típusú szakértőt preferálnának a nem használók</CardDescription>
                          </div>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('motivation-expert-card', 'szakértő-típus')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <div className="space-y-1">
                          {Object.entries(
                            countOccurrences(
                              responses
                                .filter(r => r.employee_metadata?.branch === 'not_used')
                                .map(r => r.responses?.nu_motivation_expert)
                                .filter(Boolean)
                            )
                          )
                            .sort(([, a], [, b]) => b - a)
                            .map(([expert, count]) => (
                              <div key={expert} className="flex justify-between text-sm">
                                <span>{expert}</span>
                                <span className="font-medium">{count}</span>
                              </div>
                            ))}
                        </div>
                      </CardContent>
                    </Card>
                    <Card id="motivation-channel-card">
                      <CardHeader>
                        <div className="flex justify-between items-start">
                          <div>
                            <CardTitle className="text-sm">Preferált kommunikációs csatorna</CardTitle>
                            <CardDescription>Milyen elérhetőségi módot preferálnának (telefon, online chat, stb.)</CardDescription>
                          </div>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => exportCardToPNG('motivation-channel-card', 'kommunikációs-csatorna')}
                            className="h-8 w-8 p-0"
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </CardHeader>
                      <CardContent>
                        <div className="space-y-1">
                          {Object.entries(
                            countOccurrences(
                              responses
                                .filter(r => r.employee_metadata?.branch === 'not_used')
                                .map(r => r.responses?.nu_motivation_channel)
                                .filter(Boolean)
                            )
                          )
                            .sort(([, a], [, b]) => b - a)
                            .map(([channel, count]) => (
                              <div key={channel} className="flex justify-between text-sm">
                                <span>{channel}</span>
                                <span className="font-medium">{count}</span>
                              </div>
                            ))}
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="demographics" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Demográfia Riport</CardTitle>
              <CardDescription>Válaszadók demográfiai megoszlása</CardDescription>
            </CardHeader>
            <CardContent>
              {loadingResponses ? (
                <div className="text-center py-12 text-muted-foreground">Adatok betöltése...</div>
              ) : totalResponses === 0 ? (
                <div className="text-center py-12 text-muted-foreground">Még nincs adat</div>
              ) : (
                <div className="grid grid-cols-2 gap-4">
                  <Card id="demographics-gender-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Nem szerinti megoszlás</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('demographics-gender-card', 'nem-megoszlás')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        {Object.entries(
                          countOccurrences(
                            responses
                              .map(r => r.responses?.gender)
                              .filter(Boolean)
                          )
                        ).map(([gender, count]) => (
                          <div key={gender} className="flex justify-between items-center">
                            <span className="text-sm">{gender}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="h-2 rounded-full"
                                  style={{ 
                                    width: `${formatPercentage(count as number, totalResponses)}%`,
                                    backgroundColor: '#3366ff'
                                  }}
                                />
                              </div>
                              <span className="text-sm font-medium w-12 text-right">{count as number}</span>
                            </div>
                          </div>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                  <Card id="demographics-age-card">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-sm">Korcsoport megoszlás</CardTitle>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => exportCardToPNG('demographics-age-card', 'korcsoport-megoszlás')}
                          className="h-8 w-8 p-0"
                        >
                          <Download className="h-4 w-4" />
                        </Button>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        {Object.entries(
                          countOccurrences(
                            responses
                              .map(r => r.responses?.age)
                              .filter(Boolean)
                          )
                        ).map(([age, count]) => (
                          <div key={age} className="flex justify-between items-center">
                            <span className="text-sm">{age}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="h-2 rounded-full"
                                  style={{ 
                                    width: `${formatPercentage(count as number, totalResponses)}%`,
                                    backgroundColor: '#3366ff'
                                  }}
                                />
                              </div>
                              <span className="text-sm font-medium w-12 text-right">{count as number}</span>
                            </div>
                          </div>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="trends" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Trendek Riport</CardTitle>
              <CardDescription>Időbeli változások elemzése (több felmérés szükséges)</CardDescription>
            </CardHeader>
            <CardContent>
              {audits.length < 2 ? (
                <div className="text-center py-12 text-muted-foreground">
                  A trendek megjelenítéséhez legalább 2 felmérés szükséges
                </div>
              ) : (
                <div className="text-center py-12 text-muted-foreground">
                  Trend elemzés fejlesztés alatt - hamarosan elérhető!
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="compare" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Összehasonlítás Riport</CardTitle>
              <CardDescription>Felmérések összehasonlítása (több felmérés szükséges)</CardDescription>
            </CardHeader>
            <CardContent>
              {audits.length < 2 ? (
                <div className="text-center py-12 text-muted-foreground">
                  Az összehasonlításhoz legalább 2 felmérés szükséges
                </div>
              ) : (
                <div className="text-center py-12 text-muted-foreground">
                  Összehasonlító elemzés fejlesztés alatt - hamarosan elérhető!
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default Statistics;