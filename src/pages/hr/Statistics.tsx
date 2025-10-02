import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { toast } from "sonner";
import { BarChart3, Eye, Shield, Activity, Target, Heart, Users, TrendingUp, GitCompare } from "lucide-react";
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
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt az auditek betöltésekor');
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
              <SelectValue placeholder="Válassz auditot" />
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
            Nincs aktív audit - hozz létre egyet az adatok megjelenítéséhez
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
            Bizalom & Hajlandóság
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
              {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
            </div>
          ) : (
            <div className="space-y-6">
              {/* Main KPIs */}
              <div className="grid md:grid-cols-2 gap-6">
                {/* UTILIZATION */}
                <Card>
                  <CardHeader>
                    <CardTitle className="text-2xl">Igénybevétel (Utilization)</CardTitle>
                    <CardDescription>
                      Hány munkavállaló használja a programot
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="flex flex-col items-center">
                      <ResponsiveContainer width="100%" height={200}>
                        <RadialBarChart 
                          innerRadius="70%" 
                          outerRadius="100%" 
                          data={[{ name: 'Utilization', value: utilization, fill: utilization >= 70 ? '#22c55e' : utilization >= 30 ? '#eab308' : '#ef4444' }]}
                          startAngle={180}
                          endAngle={0}
                        >
                          <RadialBar
                            background
                            dataKey="value"
                            cornerRadius={10}
                          />
                          <text x="50%" y="50%" textAnchor="middle" dominantBaseline="middle" className="fill-foreground">
                            <tspan x="50%" dy="-0.5em" className="text-4xl font-bold">{utilization.toFixed(1)}%</tspan>
                            <tspan x="50%" dy="1.5em" className="text-sm text-muted-foreground">{usedBranch} / {employeeCount}</tspan>
                          </text>
                        </RadialBarChart>
                      </ResponsiveContainer>
                      
                      {/* Detailed breakdown */}
                      <div className="w-full mt-6 space-y-3">
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground">📊 Részvételi arány</span>
                          <span className="font-semibold">{participationRate.toFixed(1)}%</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground">✅ Használók (a kitöltőkből)</span>
                          <span className="font-semibold">{usageRateFromRespondents.toFixed(1)}%</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground">❌ Nem használók</span>
                          <span className="font-semibold">{((notUsedBranch / totalResponses) * 100).toFixed(1)}%</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground">🔔 Nem tudtak róla</span>
                          <span className="font-semibold">{((redirectBranch / totalResponses) * 100).toFixed(1)}%</span>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                {/* SATISFACTION INDEX */}
                <Card>
                  <CardHeader>
                    <CardTitle className="text-2xl">Elégedettségi Index</CardTitle>
                    <CardDescription>
                      Általános elégedettség a használók körében
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="flex flex-col items-center">
                      <ResponsiveContainer width="100%" height={200}>
                        <RadialBarChart 
                          innerRadius="70%" 
                          outerRadius="100%" 
                          data={[{ name: 'Satisfaction', value: satisfactionIndex, fill: satisfactionIndex >= 70 ? '#22c55e' : satisfactionIndex >= 50 ? '#eab308' : '#ef4444' }]}
                          startAngle={180}
                          endAngle={0}
                        >
                          <RadialBar
                            background
                            dataKey="value"
                            cornerRadius={10}
                          />
                          <text x="50%" y="50%" textAnchor="middle" dominantBaseline="middle" className="fill-foreground">
                            <tspan x="50%" dy="-0.5em" className="text-4xl font-bold">{satisfactionIndex.toFixed(0)}%</tspan>
                            <tspan x="50%" dy="1.5em" className="text-sm text-muted-foreground">{satisfactionScore}/5</tspan>
                          </text>
                        </RadialBarChart>
                      </ResponsiveContainer>
                      
                      {/* Detailed breakdown */}
                      <div className="w-full mt-6 space-y-3">
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-muted-foreground">⭐ Általános elégedettség</span>
                          <span className="font-semibold">{satisfactionScore}/5</span>
                        </div>
                        <div>
                          <div className="flex justify-between items-center">
                            <span className="text-sm text-muted-foreground">📊 NPS átlag</span>
                            <span className="font-semibold">
                              {calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'used')
                                  .map(r => r.responses?.u_impact_nps)
                                  .filter(v => v !== undefined)
                              )}/10
                            </span>
                          </div>
                          <p className="text-xs text-muted-foreground mt-1">Ajánlási hajlandóság: mennyire ajánlaná másoknak a programot</p>
                        </div>
                        <div>
                          <div className="flex justify-between items-center">
                            <span className="text-sm text-muted-foreground">🔧 Problémamegoldás</span>
                            <span className="font-semibold">
                              {calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'used')
                                  .map(r => r.responses?.u_impact_problem_solving)
                                  .filter(v => v !== undefined)
                              )}/5
                            </span>
                          </div>
                          <p className="text-xs text-muted-foreground mt-1">Mennyire segített a program a probléma megoldásában</p>
                        </div>
                        <div>
                          <div className="flex justify-between items-center">
                            <span className="text-sm text-muted-foreground">💼 Teljesítmény hatás</span>
                            <span className="font-semibold">
                              {calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'used')
                                  .map(r => r.responses?.u_impact_performance)
                                  .filter(v => v !== undefined)
                              )}/5
                            </span>
                          </div>
                          <p className="text-xs text-muted-foreground mt-1">A program hatása a munkahelyi teljesítményre</p>
                        </div>
                        <div>
                          <div className="flex justify-between items-center">
                            <span className="text-sm text-muted-foreground">🧘 Wellbeing hatás</span>
                            <span className="font-semibold">
                              {calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'used')
                                  .map(r => r.responses?.u_impact_wellbeing)
                                  .filter(v => v !== undefined)
                              )}/5
                            </span>
                          </div>
                          <p className="text-xs text-muted-foreground mt-1">A program hatása az általános jóllétre és mentális egészségre</p>
                        </div>
                        <div>
                          <div className="flex justify-between items-center">
                            <span className="text-sm text-muted-foreground">🔄 Konzisztencia</span>
                            <span className="font-semibold">
                              {calculateAverage(
                                responses
                                  .filter(r => r.employee_metadata?.branch === 'used')
                                  .map(r => r.responses?.u_impact_consistency)
                                  .filter(v => v !== undefined)
                              )}/5
                            </span>
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
                  <CardTitle>Gyors áttekintés - 4 témakör</CardTitle>
                  <CardDescription>Kulcsmutatók az audit fő témáiból</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid md:grid-cols-4 gap-4">
                    {/* Awareness */}
                    <Card>
                      <CardHeader className="pb-3">
                        <CardTitle className="text-sm flex items-center gap-2">
                          <Eye className="h-4 w-4" />
                          Ismertség
                        </CardTitle>
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
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire értik a munkavállalók a szolgáltatást</p>
                      </CardContent>
                    </Card>

                    {/* Trust */}
                    <Card>
                      <CardHeader className="pb-3">
                        <CardTitle className="text-sm flex items-center gap-2">
                          <Shield className="h-4 w-4" />
                          Bizalom
                        </CardTitle>
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
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire bíznak az anonimitás védelmében</p>
                      </CardContent>
                    </Card>

                    {/* Usage */}
                    <Card>
                      <CardHeader className="pb-3">
                        <CardTitle className="text-sm flex items-center gap-2">
                          <Activity className="h-4 w-4" />
                          Használat
                        </CardTitle>
                      </CardHeader>
                      <CardContent>
                        <div className="space-y-2">
                          <div>
                            <p className="text-2xl font-bold">{usedBranch}</p>
                            <p className="text-xs text-muted-foreground">Felhasználó</p>
                          </div>
                        </div>
                      </CardContent>
                    </Card>

                    {/* Impact */}
                    <Card>
                      <CardHeader className="pb-3">
                        <CardTitle className="text-sm flex items-center gap-2">
                          <Target className="h-4 w-4" />
                          Hatás
                        </CardTitle>
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
                          sublabel="/ 5"
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
          <Card>
            <CardHeader>
              {/* Awareness – mennyien tudnak a program létezéséről */}
              <CardTitle>Ismertség Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-3 gap-4 mb-6">
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
                    <p className="text-3xl font-bold">{notUsedBranch + redirectBranch}</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Összes</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">{totalResponses}</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
              </div>
              {loadingResponses ? (
                <div className="text-center py-12 text-muted-foreground">
                  Adatok betöltése...
                </div>
              ) : totalResponses === 0 ? (
                <div className="text-center py-12 text-muted-foreground">
                  {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
                </div>
              ) : (
                <div className="space-y-4">
                  <p className="text-sm text-muted-foreground">
                    Részletes statisztikák hamarosan...
                  </p>
                </div>
              )}
            </CardContent>
          </Card>
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
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Bizalom az anonimitásban (Használók)</CardTitle>
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
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire bíznak a használók abban, hogy névtelenségük megmarad</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Munkaadói félelem</CardTitle>
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
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire félnek attól, hogy a munkaadó megtudja a használatot</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Használati hajlandóság</CardTitle>
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
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire valószínű, hogy újra használnák a szolgáltatást</p>
                      </CardContent>
                    </Card>
                  </div>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Nem használók bizalmi indexei</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-3 gap-4">
                        <div>
                          <p className="text-lg font-bold">
                            {calculateAverage(
                              responses
                                .filter(r => r.employee_metadata?.branch === 'not_used')
                                .map(r => r.responses?.nu_trust_anonymity)
                                .filter(v => v !== undefined)
                            )}
                          </p>
                          <p className="text-xs text-muted-foreground">Anonimitás</p>
                          <p className="text-xs text-muted-foreground mt-1">Bizalom az anonimitás védelmében</p>
                        </div>
                        <div>
                          <p className="text-lg font-bold">
                            {calculateAverage(
                              responses
                                .filter(r => r.employee_metadata?.branch === 'not_used')
                                .map(r => r.responses?.nu_trust_employer)
                                .filter(v => v !== undefined)
                            )}
                          </p>
                          <p className="text-xs text-muted-foreground">Munkaadói félelem</p>
                          <p className="text-xs text-muted-foreground mt-1">Félelem a munkaadó tudomásától</p>
                        </div>
                        <div>
                          <p className="text-lg font-bold">
                            {calculateAverage(
                              responses
                                .filter(r => r.employee_metadata?.branch === 'not_used')
                                .map(r => r.responses?.nu_trust_colleagues)
                                .filter(v => v !== undefined)
                            )}
                          </p>
                          <p className="text-xs text-muted-foreground">Kollégai megítéléstől félelem</p>
                          <p className="text-xs text-muted-foreground mt-1">Félelem a kollégák negatív véleményétől</p>
                        </div>
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
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Elégedettség</CardTitle>
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
                          size={120}
                          sublabel="/ 5"
                        />
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Problémamegoldás</CardTitle>
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
                          size={120}
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire segített a program a problémák kezelésében</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">NPS átlag</CardTitle>
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
                          size={120}
                          sublabel="/ 10"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Net Promoter Score: ajánlási hajlandóság másoknak</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Konzisztencia</CardTitle>
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
                          size={120}
                          sublabel="/ 5"
                        />
                        <p className="text-xs text-muted-foreground mt-2 text-center">Szolgáltatás minőségének egyenletessége alkalmanként</p>
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
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${formatPercentage(count, usedBranch)}%` }}
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
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Teljesítményre gyakorolt hatás</CardTitle>
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
                        sublabel="/ 5"
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">A program hatása a munkahelyi teljesítményre és produktivitásra</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Problémamegoldás</CardTitle>
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
                        sublabel="/ 5"
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">Mennyire hatékonyan segített a program a problémák megoldásában</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Általános jóllét</CardTitle>
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
                        sublabel="/ 5"
                      />
                      <p className="text-xs text-muted-foreground mt-2 text-center">A program hatása az általános jóllétre és mentális egészségre</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Elégedettség</CardTitle>
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
                        sublabel="/ 5"
                      />
                    </CardContent>
                  </Card>
                  <Card className="col-span-2">
                    <CardHeader>
                      <CardTitle className="text-sm">Szolgáltatás konzisztencia</CardTitle>
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
                        sublabel="/ 5"
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
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Mi kellene a használathoz? (Top motivátorok)</CardTitle>
                      <CardDescription>Mi motiválná a nem használókat a program igénybevételére</CardDescription>
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
                                    className="bg-primary h-2 rounded-full"
                                    style={{ width: `${formatPercentage(count as number, notUsedBranch)}%` }}
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
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Preferált szakértő típus</CardTitle>
                        <CardDescription>Milyen típusú szakértőt preferálnának a nem használók</CardDescription>
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
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Preferált kommunikációs csatorna</CardTitle>
                        <CardDescription>Milyen elérhetőségi módot preferálnának (telefon, online chat, stb.)</CardDescription>
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
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Nem szerinti megoszlás</CardTitle>
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
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${formatPercentage(count as number, totalResponses)}%` }}
                                />
                              </div>
                              <span className="text-sm font-medium w-12 text-right">{count as number}</span>
                            </div>
                          </div>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Korcsoport megoszlás</CardTitle>
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
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${formatPercentage(count as number, totalResponses)}%` }}
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
              <CardDescription>Időbeli változások elemzése (több audit szükséges)</CardDescription>
            </CardHeader>
            <CardContent>
              {audits.length < 2 ? (
                <div className="text-center py-12 text-muted-foreground">
                  A trendek megjelenítéséhez legalább 2 audit szükséges
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
              <CardDescription>Auditek összehasonlítása (több audit szükséges)</CardDescription>
            </CardHeader>
            <CardContent>
              {audits.length < 2 ? (
                <div className="text-center py-12 text-muted-foreground">
                  Az összehasonlításhoz legalább 2 audit szükséges
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