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
        <p className="text-muted-foreground">Változások és trendek követése</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Auditok Kiválasztása</CardTitle>
          <CardDescription>Válaszd ki, mely auditokat szeretnéd összehasonlítani</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 gap-4">
            {audits.map((audit) => (
              <div key={audit.id} className="flex items-center space-x-2">
                <Checkbox
                  id={audit.id}
                  checked={selectedAuditIds.includes(audit.id)}
                  onCheckedChange={() => toggleAudit(audit.id)}
                />
                <Label htmlFor={audit.id} className="cursor-pointer">
                  {formatAuditName(audit)}
                </Label>
              </div>
            ))}
          </div>
          <p className="text-sm text-muted-foreground mt-4">
            {selectedAuditIds.length} audit kiválasztva
          </p>
        </CardContent>
      </Card>

      {trendData.length === 0 ? (
        <Card>
          <CardContent className="py-12 text-center text-muted-foreground">
            Válassz ki legalább egy auditot
          </CardContent>
        </Card>
      ) : (
        <>
          <Card>
            <CardHeader>
              <CardTitle>Kategória Eloszlás Trend</CardTitle>
              <CardDescription>EAP ismeret és használat változása (%)</CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={400}>
                <LineChart data={categoryTrend}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="date" />
                  <YAxis domain={[0, 100]} />
                  <Tooltip />
                  <Legend />
                  <Line 
                    type="monotone" 
                    dataKey="notKnew" 
                    name="Nem tudott róla %" 
                    stroke="#ef4444" 
                    strokeWidth={2}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="notUsed" 
                    name="Nem használta %" 
                    stroke="#f59e0b" 
                    strokeWidth={2}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="used" 
                    name="Használta %" 
                    stroke="#10b981" 
                    strokeWidth={2}
                  />
                </LineChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Kulcs Mutatók Trendje</CardTitle>
              <CardDescription>Awareness, Trust, Usage, Impact változása</CardDescription>
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
                    name="Awareness (1-5)" 
                    stroke="#3b82f6" 
                    strokeWidth={2}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="trust" 
                    name="Trust (1-5)" 
                    stroke="#8b5cf6" 
                    strokeWidth={2}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="usage" 
                    name="Usage (%)" 
                    stroke="#10b981" 
                    strokeWidth={2}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="impact" 
                    name="Impact (1-5)" 
                    stroke="#f59e0b" 
                    strokeWidth={2}
                  />
                </LineChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          {trendData.length >= 2 && (
            <Card>
              <CardHeader>
                <CardTitle>Változások Elemzése</CardTitle>
                <CardDescription>Legutóbbi vs. előző periódus</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-4 gap-4">
                  <div className="p-4 border rounded">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium">Awareness</p>
                      {getTrendIcon(
                        trendData[trendData.length - 1].awareness,
                        trendData[trendData.length - 2].awareness
                      )}
                    </div>
                    <p className="text-2xl font-bold">
                      {trendData[trendData.length - 1].awareness}
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].awareness,
                        trendData[trendData.length - 2].awareness
                      )}
                    </p>
                  </div>

                  <div className="p-4 border rounded">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium">Trust</p>
                      {getTrendIcon(
                        trendData[trendData.length - 1].trust,
                        trendData[trendData.length - 2].trust
                      )}
                    </div>
                    <p className="text-2xl font-bold">
                      {trendData[trendData.length - 1].trust}
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].trust,
                        trendData[trendData.length - 2].trust
                      )}
                    </p>
                  </div>

                  <div className="p-4 border rounded">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium">Usage %</p>
                      {getTrendIcon(
                        trendData[trendData.length - 1].usage,
                        trendData[trendData.length - 2].usage
                      )}
                    </div>
                    <p className="text-2xl font-bold">
                      {trendData[trendData.length - 1].usage}%
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].usage,
                        trendData[trendData.length - 2].usage
                      )}pp
                    </p>
                  </div>

                  <div className="p-4 border rounded">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium">Impact</p>
                      {getTrendIcon(
                        trendData[trendData.length - 1].impact,
                        trendData[trendData.length - 2].impact
                      )}
                    </div>
                    <p className="text-2xl font-bold">
                      {trendData[trendData.length - 1].impact}
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].impact,
                        trendData[trendData.length - 2].impact
                      )}
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}
        </>
      )}
    </div>
  );
};

export default Trends;
