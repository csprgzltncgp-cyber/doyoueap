import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { GaugeChart } from '@/components/ui/gauge-chart';

interface Audit {
  id: string;
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config: any;
  is_active: boolean;
  expires_at: string | null;
}

interface TrustData {
  metric: string;
  used: number;
  notUsed: number;
  overall: number;
}

interface BarrierData {
  name: string;
  value: number;
  percentage: string;
}

const BARRIER_COLORS = ['#ef4444', '#f59e0b', '#eab308', '#84cc16', '#10b981', '#14b8a6'];

const TrustWillingness = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [trustData, setTrustData] = useState<TrustData[]>([]);
  const [barrierData, setBarrierData] = useState<BarrierData[]>([]);
  const [loading, setLoading] = useState(true);
  const [responseCount, setResponseCount] = useState({ used: 0, notUsed: 0 });

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchTrustData(selectedAuditId);
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

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchTrustData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        setTrustData([]);
        setBarrierData([]);
        setResponseCount({ used: 0, notUsed: 0 });
        return;
      }

      const usedResponses = data.filter(r => r.employee_metadata?.branch === 'used');
      const notUsedResponses = data.filter(r => r.employee_metadata?.branch === 'not_used');

      setResponseCount({
        used: usedResponses.length,
        notUsed: notUsedResponses.length
      });

      // Trust metrics for "used" branch
      const usedAnonymity = usedResponses
        .map(r => r.responses?.u_trust_anonymity)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedEmployer = usedResponses
        .map(r => r.responses?.u_trust_employer)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedColleagues = usedResponses
        .map(r => r.responses?.u_trust_colleagues)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedLikelihood = usedResponses
        .map(r => r.responses?.u_trust_likelihood)
        .filter(v => v !== undefined && v !== null) as number[];

      // Trust metrics for "not used" branch
      const notUsedAnonymity = notUsedResponses
        .map(r => r.responses?.nu_trust_anonymity)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const notUsedEmployer = notUsedResponses
        .map(r => r.responses?.nu_trust_employer)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const notUsedColleagues = notUsedResponses
        .map(r => r.responses?.nu_trust_colleagues)
        .filter(v => v !== undefined && v !== null) as number[];

      const chartData: TrustData[] = [
        {
          metric: 'Anonimitásba vetett bizalom',
          used: Number(calculateAverage(usedAnonymity).toFixed(2)),
          notUsed: Number(calculateAverage(notUsedAnonymity).toFixed(2)),
          overall: Number(calculateAverage([...usedAnonymity, ...notUsedAnonymity]).toFixed(2)),
        },
        {
          metric: 'Munkaadó reakciójától félelem',
          used: Number(calculateAverage(usedEmployer).toFixed(2)),
          notUsed: Number(calculateAverage(notUsedEmployer).toFixed(2)),
          overall: Number(calculateAverage([...usedEmployer, ...notUsedEmployer]).toFixed(2)),
        },
        {
          metric: 'Kollégák megítélésétől félelem',
          used: Number(calculateAverage(usedColleagues).toFixed(2)),
          notUsed: Number(calculateAverage(notUsedColleagues).toFixed(2)),
          overall: Number(calculateAverage([...usedColleagues, ...notUsedColleagues]).toFixed(2)),
        },
        {
          metric: 'Használati hajlandóság',
          used: Number(calculateAverage(usedLikelihood).toFixed(2)),
          notUsed: 0, // Not asked in not_used branch
          overall: Number(calculateAverage(usedLikelihood).toFixed(2)),
        },
      ];

      setTrustData(chartData);

      // Calculate barriers (only from "used" branch)
      const barriers: { [key: string]: number } = {};
      usedResponses.forEach(r => {
        const barrier = r.responses?.u_trust_barriers;
        if (barrier) {
          barriers[barrier] = (barriers[barrier] || 0) + 1;
        }
      });

      const total = Object.values(barriers).reduce((sum, val) => sum + val, 0);
      const barrierChartData: BarrierData[] = Object.entries(barriers).map(([name, value]) => ({
        name,
        value,
        percentage: total > 0 ? ((value / total) * 100).toFixed(1) : '0',
      }));

      setBarrierData(barrierChartData);
    } catch (error) {
      console.error('Error fetching trust data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
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
        <h1 className="text-3xl font-bold">Trust & Willingness Riport</h1>
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
      </div>

      <div className="grid grid-cols-3 gap-4">
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Használók</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-3xl font-bold">{responseCount.used}</p>
            <p className="text-sm text-muted-foreground">válaszadó</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Nem használók</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-3xl font-bold">{responseCount.notUsed}</p>
            <p className="text-sm text-muted-foreground">válaszadó</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Összes</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-3xl font-bold">{responseCount.used + responseCount.notUsed}</p>
            <p className="text-sm text-muted-foreground">válaszadó</p>
          </CardContent>
        </Card>
      </div>

      {/* Gauge Charts for Key Metrics */}
      {trustData.length > 0 && (
        <div className="grid md:grid-cols-3 gap-6 mb-6">
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Anonimitásba vetett bizalom</CardTitle>
              <CardDescription>Átlagos bizalom szint (1-5 skála)</CardDescription>
            </CardHeader>
            <CardContent className="flex justify-center">
              <GaugeChart
                value={parseFloat((trustData.find(d => d.metric === 'Anonimitásba vetett bizalom')?.overall || 0).toFixed(1))}
                maxValue={5}
                size={150}
                label={(trustData.find(d => d.metric === 'Anonimitásba vetett bizalom')?.overall || 0).toFixed(1)}
                sublabel="/ 5"
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Munkaadó reakciójától félelem</CardTitle>
              <CardDescription>Átlagos félelem szint (1-5 skála)</CardDescription>
            </CardHeader>
            <CardContent className="flex justify-center">
              <GaugeChart
                value={parseFloat((trustData.find(d => d.metric === 'Munkaadó reakciójától félelem')?.overall || 0).toFixed(1))}
                maxValue={5}
                size={150}
                label={(trustData.find(d => d.metric === 'Munkaadó reakciójától félelem')?.overall || 0).toFixed(1)}
                sublabel="/ 5"
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Kollégák megítélésétől félelem</CardTitle>
              <CardDescription>Átlagos félelem szint (1-5 skála)</CardDescription>
            </CardHeader>
            <CardContent className="flex justify-center">
              <GaugeChart
                value={parseFloat((trustData.find(d => d.metric === 'Kollégák megítélésétől félelem')?.overall || 0).toFixed(1))}
                maxValue={5}
                size={150}
                label={(trustData.find(d => d.metric === 'Kollégák megítélésétől félelem')?.overall || 0).toFixed(1)}
                sublabel="/ 5"
              />
            </CardContent>
          </Card>
        </div>
      )}

      <Card>
        <CardHeader>
          <CardTitle>Bizalom Mutatók</CardTitle>
          <CardDescription>
            Likert skála átlagok (1-5), ahol 5 = teljes mértékben
          </CardDescription>
        </CardHeader>
        <CardContent>
          {trustData.length === 0 ? (
            <div className="text-center py-12 text-muted-foreground">
              Még nincs adat ehhez az audithoz
            </div>
          ) : (
            <ResponsiveContainer width="100%" height={400}>
              <BarChart data={trustData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="metric" angle={-15} textAnchor="end" height={100} />
                <YAxis domain={[0, 5]} />
                <Tooltip />
                <Legend />
                <Bar dataKey="used" name="Használók" fill="#10b981" />
                <Bar dataKey="notUsed" name="Nem használók" fill="#f59e0b" />
                <Bar dataKey="overall" name="Összesített" fill="#3b82f6" />
              </BarChart>
            </ResponsiveContainer>
          )}
        </CardContent>
      </Card>

      {barrierData.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle>Visszatartó Okok Megoszlása</CardTitle>
            <CardDescription>
              Csak a használók válaszai alapján
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 gap-8">
              <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                  <Pie
                    data={barrierData}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={(entry) => `${entry.name}: ${entry.percentage}%`}
                    outerRadius={100}
                    fill="#8884d8"
                    dataKey="value"
                  >
                    {barrierData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={BARRIER_COLORS[index % BARRIER_COLORS.length]} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>

              <div className="space-y-2">
                {barrierData.map((barrier, index) => (
                  <div 
                    key={barrier.name}
                    className="flex items-center justify-between p-3 rounded border"
                    style={{ borderLeft: `4px solid ${BARRIER_COLORS[index % BARRIER_COLORS.length]}` }}
                  >
                    <span className="font-medium">{barrier.name}</span>
                    <div className="text-right">
                      <p className="text-lg font-bold">{barrier.value}</p>
                      <p className="text-sm text-muted-foreground">{barrier.percentage}%</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
};

export default TrustWillingness;
