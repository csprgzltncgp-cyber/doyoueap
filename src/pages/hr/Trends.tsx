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
    <div className="space-y-6 pt-20">
      <div>
        <h2 className="text-2xl font-bold mb-2">Időbeli Trendek</h2>
        <p className="text-muted-foreground">Változások és trendek követése</p>
      </div>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Trends;
