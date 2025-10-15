import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar } from 'recharts';
import { toast } from 'sonner';
import { TrendingUp, TrendingDown, Minus } from 'lucide-react';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

type Audit = StandardAudit;

interface TrendData {
  date: string;
  awareness: number;
  trust: number;
  usage: number;
  impact: number;
}

interface CategoryTrend {
  date: string;
  notKnew: number;
  notUsed: number;
  used: number;
}

const Trends = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditIds, setSelectedAuditIds] = useState<string[]>([]);
  const [trendData, setTrendData] = useState<TrendData[]>([]);
  const [categoryTrend, setCategoryTrend] = useState<CategoryTrend[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditIds.length > 0) {
      fetchTrendData(selectedAuditIds);
    }
  }, [selectedAuditIds]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select(AUDIT_SELECT_FIELDS)
        .eq('is_active', true)
        .order('start_date', { ascending: true });

      if (data && data.length > 0) {
        setAudits(data);
        // Select all audits by default
        setSelectedAuditIds(data.map(a => a.id));
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt az auditek betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const toggleAudit = (auditId: string) => {
    setSelectedAuditIds(prev => 
      prev.includes(auditId) 
        ? prev.filter(id => id !== auditId)
        : [...prev, auditId]
    );
  };

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchTrendData = async (auditIds: string[]) => {
    try {
      const trends: TrendData[] = [];
      const catTrends: CategoryTrend[] = [];

      for (const auditId of auditIds) {
        const audit = audits.find(a => a.id === auditId);
        if (!audit) continue;

        const { data, error } = await supabase
          .from('audit_responses')
          .select('responses, employee_metadata')
          .eq('audit_id', auditId);

        if (error) throw error;
        if (!data || data.length === 0) continue;

        const date = new Date(audit.start_date).toLocaleDateString('hu-HU', { 
          year: 'numeric', 
          month: 'short' 
        });

        // Category counts
        let notKnew = 0, notUsed = 0, used = 0;
        data.forEach(r => {
          const branch = r.employee_metadata?.branch;
          if (branch === 'redirect') notKnew++;
          else if (branch === 'not_used') notUsed++;
          else if (branch === 'used') used++;
        });

        const total = data.length;
        catTrends.push({
          date,
          notKnew: total > 0 ? Number(((notKnew / total) * 100).toFixed(1)) : 0,
          notUsed: total > 0 ? Number(((notUsed / total) * 100).toFixed(1)) : 0,
          used: total > 0 ? Number(((used / total) * 100).toFixed(1)) : 0,
        });

        // Awareness (understanding)
        const usedResponses = data.filter(r => r.employee_metadata?.branch === 'used');
        const notUsedResponses = data.filter(r => r.employee_metadata?.branch === 'not_used');

        const awarenessValues = [
          ...usedResponses.map(r => r.responses?.u_awareness_understanding),
          ...notUsedResponses.map(r => r.responses?.nu_awareness_understanding)
        ].filter(v => v !== undefined && v !== null) as number[];

        // Trust (anonymity)
        const trustValues = [
          ...usedResponses.map(r => r.responses?.u_trust_anonymity),
          ...notUsedResponses.map(r => r.responses?.nu_trust_anonymity)
        ].filter(v => v !== undefined && v !== null) as number[];

        // Usage rate
        const usageRate = total > 0 ? (used / total) * 100 : 0;

        // Impact (satisfaction)
        const impactValues = usedResponses
          .map(r => r.responses?.u_impact_satisfaction)
          .filter(v => v !== undefined && v !== null) as number[];

        trends.push({
          date,
          awareness: Number(calculateAverage(awarenessValues).toFixed(2)),
          trust: Number(calculateAverage(trustValues).toFixed(2)),
          usage: Number(usageRate.toFixed(1)),
          impact: Number(calculateAverage(impactValues).toFixed(2)),
        });
      }

      setTrendData(trends);
      setCategoryTrend(catTrends);
    } catch (error) {
      console.error('Error fetching trend data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    }
  };

  const getTrendIcon = (current: number, previous: number) => {
    if (!previous) return <Minus className="h-4 w-4 text-gray-400" />;
    const diff = current - previous;
    if (diff > 0.5) return <TrendingUp className="h-4 w-4 text-green-600" />;
    if (diff < -0.5) return <TrendingDown className="h-4 w-4 text-red-600" />;
    return <Minus className="h-4 w-4 text-gray-400" />;
  };

  const getTrendText = (current: number, previous: number) => {
    if (!previous) return '—';
    const diff = current - previous;
    return diff > 0 ? `+${diff.toFixed(1)}` : diff.toFixed(1);
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Időbeli Trendek</h2>
        <p className="text-muted-foreground">Változások és trendek követése az idő során</p>
      </div>

      {audits.length === 0 ? (
        <Card>
          <CardContent className="pt-6">
            <p className="text-center text-muted-foreground">Még nincs aktív felmérés</p>
          </CardContent>
        </Card>
      ) : (
        <>
          <Card>
            <CardHeader>
              <CardTitle>Felmérések kiválasztása</CardTitle>
              <CardDescription>Válaszd ki, mely felmérések adatait szeretnéd összehasonlítani</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {audits.map(audit => (
                  <div key={audit.id} className="flex items-center space-x-2">
                    <Checkbox
                      id={audit.id}
                      checked={selectedAuditIds.includes(audit.id)}
                      onCheckedChange={() => toggleAudit(audit.id)}
                    />
                    <Label 
                      htmlFor={audit.id}
                      className="text-sm font-normal cursor-pointer"
                    >
                      {formatAuditName(audit)}
                    </Label>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {selectedAuditIds.length === 0 ? (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-muted-foreground">Válassz ki legalább egy felmérést</p>
              </CardContent>
            </Card>
          ) : trendData.length === 0 ? (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-muted-foreground">A kiválasztott felmérésekhez még nincs válasz</p>
              </CardContent>
            </Card>
          ) : (
            <>
              {/* Category Distribution Trend */}
              <Card>
                <CardHeader>
                  <CardTitle>Kategória megoszlás időbeli alakulása</CardTitle>
                  <CardDescription>Az EAP program ismerete és használata (%)</CardDescription>
                </CardHeader>
                <CardContent>
                  <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={categoryTrend}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="date" />
                      <YAxis />
                      <Tooltip />
                      <Legend />
                      <Bar dataKey="notKnew" name="Nem ismerte" fill="#050c9c" />
                      <Bar dataKey="notUsed" name="Ismerte, de nem használta" fill="#3572ef" />
                      <Bar dataKey="used" name="Használta" fill="#3abef9" />
                    </BarChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>

              {/* Main Metrics Trends */}
              <Card>
                <CardHeader>
                  <CardTitle>Fő mutatók alakulása</CardTitle>
                  <CardDescription>Ismertség, bizalom, használat és elégedettség változása</CardDescription>
                </CardHeader>
                <CardContent>
                  <ResponsiveContainer width="100%" height={400}>
                    <LineChart data={trendData}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="date" />
                      <YAxis />
                      <Tooltip />
                      <Legend />
                      <Line 
                        type="monotone" 
                        dataKey="awareness" 
                        name="Ismertség (1-5)" 
                        stroke="hsl(var(--primary))" 
                        strokeWidth={2}
                      />
                      <Line 
                        type="monotone" 
                        dataKey="trust" 
                        name="Bizalom (1-5)" 
                        stroke="hsl(var(--success))" 
                        strokeWidth={2}
                      />
                      <Line 
                        type="monotone" 
                        dataKey="usage" 
                        name="Használati arány (%)" 
                        stroke="hsl(var(--warning))" 
                        strokeWidth={2}
                      />
                      <Line 
                        type="monotone" 
                        dataKey="impact" 
                        name="Elégedettség (1-5)" 
                        stroke="hsl(var(--chart-5))" 
                        strokeWidth={2}
                      />
                    </LineChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>

              {/* Trend Summary Cards */}
              {trendData.length > 1 && (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-sm font-medium">Ismertség</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="flex items-center justify-between">
                        <div className="text-2xl font-bold">
                          {trendData[trendData.length - 1].awareness}
                        </div>
                        <div className="flex items-center gap-1">
                          {getTrendIcon(
                            trendData[trendData.length - 1].awareness,
                            trendData[trendData.length - 2].awareness
                          )}
                          <span className="text-sm text-muted-foreground">
                            {getTrendText(
                              trendData[trendData.length - 1].awareness,
                              trendData[trendData.length - 2].awareness
                            )}
                          </span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>

                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-sm font-medium">Bizalom</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="flex items-center justify-between">
                        <div className="text-2xl font-bold">
                          {trendData[trendData.length - 1].trust}
                        </div>
                        <div className="flex items-center gap-1">
                          {getTrendIcon(
                            trendData[trendData.length - 1].trust,
                            trendData[trendData.length - 2].trust
                          )}
                          <span className="text-sm text-muted-foreground">
                            {getTrendText(
                              trendData[trendData.length - 1].trust,
                              trendData[trendData.length - 2].trust
                            )}
                          </span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>

                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-sm font-medium">Használat</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="flex items-center justify-between">
                        <div className="text-2xl font-bold">
                          {trendData[trendData.length - 1].usage}%
                        </div>
                        <div className="flex items-center gap-1">
                          {getTrendIcon(
                            trendData[trendData.length - 1].usage,
                            trendData[trendData.length - 2].usage
                          )}
                          <span className="text-sm text-muted-foreground">
                            {getTrendText(
                              trendData[trendData.length - 1].usage,
                              trendData[trendData.length - 2].usage
                            )}
                          </span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>

                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-sm font-medium">Elégedettség</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="flex items-center justify-between">
                        <div className="text-2xl font-bold">
                          {trendData[trendData.length - 1].impact}
                        </div>
                        <div className="flex items-center gap-1">
                          {getTrendIcon(
                            trendData[trendData.length - 1].impact,
                            trendData[trendData.length - 2].impact
                          )}
                          <span className="text-sm text-muted-foreground">
                            {getTrendText(
                              trendData[trendData.length - 1].impact,
                              trendData[trendData.length - 2].impact
                            )}
                          </span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                </div>
              )}
            </>
          )}
        </>
      )}
    </div>
  );
};

export default Trends;
