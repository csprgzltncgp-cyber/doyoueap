import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { toast } from "sonner";
import { Eye, Shield, Activity, Target, Heart, Users, TrendingUp, GitCompare } from "lucide-react";
import { formatAuditName } from "@/lib/auditUtils";

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
  const activeTab = searchParams.get("tab") || "awareness";

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchResponses();
    }
  }, [selectedAuditId]);

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

  // Calculate statistics
  const totalResponses = responses.length;
  const usedBranch = responses.filter(r => r.employee_metadata?.branch === 'used').length;
  const notUsedBranch = responses.filter(r => r.employee_metadata?.branch === 'not_used').length;
  const redirectBranch = responses.filter(r => r.employee_metadata?.branch === 'redirect').length;

  // Helper function to calculate average
  const calculateAverage = (values: number[]) => {
    if (values.length === 0) return 0;
    return (values.reduce((a, b) => a + b, 0) / values.length).toFixed(1);
  };

  // Helper function to count occurrences
  const countOccurrences = (items: string[]) => {
    return items.reduce((acc, item) => {
      acc[item] = (acc[item] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);
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
          {/* Awareness – mennyien tudnak a program létezéséről */}
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
                        <p className="text-2xl font-bold">
                          {calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_anonymity)
                              .filter(v => v !== undefined)
                          )}
                        </p>
                        <p className="text-xs text-muted-foreground">átlag (1-5 skála)</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Munkaadói félelem</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <p className="text-2xl font-bold">
                          {calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_employer)
                              .filter(v => v !== undefined)
                          )}
                        </p>
                        <p className="text-xs text-muted-foreground">átlag (1-5, magasabb = nagyobb félelem)</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Használati hajlandóság</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <p className="text-2xl font-bold">
                          {calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_trust_likelihood)
                              .filter(v => v !== undefined)
                          )}
                        </p>
                        <p className="text-xs text-muted-foreground">átlag (1-5 skála)</p>
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
                        <p className="text-2xl font-bold">
                          {calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_usage_satisfaction)
                              .filter(v => v !== undefined)
                          )}
                        </p>
                        <p className="text-xs text-muted-foreground">átlag (1-5)</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Célok elérése</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <p className="text-2xl font-bold">
                          {calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_usage_achievement)
                              .filter(v => v !== undefined)
                          )}
                        </p>
                        <p className="text-xs text-muted-foreground">átlag (1-5)</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">NPS átlag</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <p className="text-2xl font-bold">
                          {calculateAverage(
                            responses
                              .filter(r => r.employee_metadata?.branch === 'used')
                              .map(r => r.responses?.u_usage_nps)
                              .filter(v => v !== undefined)
                          )}
                        </p>
                        <p className="text-xs text-muted-foreground">0-10 skála</p>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-sm">Ajánlanák</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <p className="text-2xl font-bold">
                          {Math.round(
                            (responses
                              .filter(r => r.employee_metadata?.branch === 'used' && r.responses?.u_usage_recommend === 'yes')
                              .length / usedBranch) * 100
                          )}%
                        </p>
                        <p className="text-xs text-muted-foreground">igen válaszok</p>
                      </CardContent>
                    </Card>
                  </div>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Használati gyakoriság eloszlása</CardTitle>
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
                                  style={{ width: `${(count / usedBranch) * 100}%` }}
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
                      <CardTitle className="text-sm">Munkavégzésre gyakorolt hatás</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-3xl font-bold">
                        {calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_work)
                            .filter(v => v !== undefined)
                        )}
                      </p>
                      <p className="text-xs text-muted-foreground">átlag (1-5 skála)</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Stresszcsökkentés</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-3xl font-bold">
                        {calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_stress)
                            .filter(v => v !== undefined)
                        )}
                      </p>
                      <p className="text-xs text-muted-foreground">átlag (1-5 skála)</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Általános jóllét</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-3xl font-bold">
                        {calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_wellbeing)
                            .filter(v => v !== undefined)
                        )}
                      </p>
                      <p className="text-xs text-muted-foreground">átlag (1-5 skála)</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardHeader>
                      <CardTitle className="text-sm">Produktivitás</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-3xl font-bold">
                        {calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_productivity)
                            .filter(v => v !== undefined)
                        )}
                      </p>
                      <p className="text-xs text-muted-foreground">átlag (1-5 skála)</p>
                    </CardContent>
                  </Card>
                  <Card className="col-span-2">
                    <CardHeader>
                      <CardTitle className="text-sm">Munkahely megítélése</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-3xl font-bold">
                        {calculateAverage(
                          responses
                            .filter(r => r.employee_metadata?.branch === 'used')
                            .map(r => r.responses?.u_impact_workplace)
                            .filter(v => v !== undefined)
                        )}
                      </p>
                      <p className="text-xs text-muted-foreground">átlag (1-5 skála) - mennyire javult a munkahely megítélése</p>
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
                                    style={{ width: `${((count as number) / notUsedBranch) * 100}%` }}
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
                              .map(r => r.employee_metadata?.gender)
                              .filter(Boolean)
                          )
                        ).map(([gender, count]) => (
                          <div key={gender} className="flex justify-between items-center">
                            <span className="text-sm">{gender}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${((count as number) / totalResponses) * 100}%` }}
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
                              .map(r => r.employee_metadata?.age_group)
                              .filter(Boolean)
                          )
                        ).map(([age, count]) => (
                          <div key={age} className="flex justify-between items-center">
                            <span className="text-sm">{age}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${((count as number) / totalResponses) * 100}%` }}
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
                      <CardTitle className="text-sm">Részleg megoszlás</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        {Object.entries(
                          countOccurrences(
                            responses
                              .map(r => r.employee_metadata?.department)
                              .filter(Boolean)
                          )
                        ).map(([dept, count]) => (
                          <div key={dept} className="flex justify-between items-center">
                            <span className="text-sm">{dept}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${((count as number) / totalResponses) * 100}%` }}
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
                      <CardTitle className="text-sm">Pozíció megoszlás</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        {Object.entries(
                          countOccurrences(
                            responses
                              .map(r => r.employee_metadata?.position)
                              .filter(Boolean)
                          )
                        ).map(([pos, count]) => (
                          <div key={pos} className="flex justify-between items-center">
                            <span className="text-sm">{pos}</span>
                            <div className="flex items-center gap-2">
                              <div className="w-32 bg-muted rounded-full h-2">
                                <div
                                  className="bg-primary h-2 rounded-full"
                                  style={{ width: `${((count as number) / totalResponses) * 100}%` }}
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