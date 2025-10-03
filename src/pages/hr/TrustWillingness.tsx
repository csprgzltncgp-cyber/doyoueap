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

interface TrustWillingnessProps {
  selectedAuditId: string;
}

const TrustWillingness = ({ selectedAuditId }: TrustWillingnessProps) => {
  const [trustData, setTrustData] = useState<TrustData[]>([]);
  const [barrierData, setBarrierData] = useState<BarrierData[]>([]);
  const [loading, setLoading] = useState(true);
  const [responseCount, setResponseCount] = useState({ used: 0, notUsed: 0 });

  useEffect(() => {
    if (selectedAuditId) {
      fetchTrustData(selectedAuditId);
    }
  }, [selectedAuditId]);


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
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold">Bizalom & Készség Riport</h2>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default TrustWillingness;
