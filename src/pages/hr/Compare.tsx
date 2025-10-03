import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { ArrowRight, TrendingUp, TrendingDown, Minus } from 'lucide-react';
import { formatAuditName } from '@/lib/auditUtils';

// NOTE: "Audit" in code represents "Felmérés" (EAP Pulse Survey) in the UI
interface Audit {
  id: string;
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config: any;
  is_active: boolean;
  expires_at: string | null;
}

interface ComparisonMetrics {
  awareness: { old: number; new: number; delta: number };
  trust: { old: number; new: number; delta: number };
  usage: { old: number; new: number; delta: number };
  impact: { old: number; new: number; delta: number };
}

const Compare = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [audit1Id, setAudit1Id] = useState<string>('');
  const [audit2Id, setAudit2Id] = useState<string>('');
  const [metrics, setMetrics] = useState<ComparisonMetrics | null>(null);
  const [loading, setLoading] = useState(true);
  const [comparing, setComparing] = useState(false);

  useEffect(() => {
    fetchAudits();
  }, []);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at')
        .order('start_date', { ascending: true });

      if (data && data.length > 0) {
        setAudits(data);
        if (data.length >= 2) {
          setAudit1Id(data[0].id);
          setAudit2Id(data[data.length - 1].id);
        }
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const handleCompare = async () => {
    if (!audit1Id || !audit2Id || audit1Id === audit2Id) {
      toast.error('Válassz két különböző felmérést az összehasonlításhoz');
      return;
    }

    setComparing(true);
    try {
      // Fetch data for both audits
      const [data1, data2] = await Promise.all([
        supabase.from('audit_responses').select('responses, employee_metadata').eq('audit_id', audit1Id),
        supabase.from('audit_responses').select('responses, employee_metadata').eq('audit_id', audit2Id)
      ]);

      if (data1.error) throw data1.error;
      if (data2.error) throw data2.error;

      const responses1 = data1.data || [];
      const responses2 = data2.data || [];

      // Calculate metrics for audit 1
      const used1 = responses1.filter(r => r.employee_metadata?.branch === 'used');
      const notUsed1 = responses1.filter(r => r.employee_metadata?.branch === 'not_used');
      
      const awareness1 = [
        ...used1.map(r => r.responses?.u_awareness_understanding),
        ...notUsed1.map(r => r.responses?.nu_awareness_understanding)
      ].filter(v => v !== undefined && v !== null) as number[];

      const trust1 = [
        ...used1.map(r => r.responses?.u_trust_anonymity),
        ...notUsed1.map(r => r.responses?.nu_trust_anonymity)
      ].filter(v => v !== undefined && v !== null) as number[];

      const usage1 = responses1.length > 0 ? (used1.length / responses1.length) * 100 : 0;

      const impact1 = used1
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v !== undefined && v !== null) as number[];

      // Calculate metrics for audit 2
      const used2 = responses2.filter(r => r.employee_metadata?.branch === 'used');
      const notUsed2 = responses2.filter(r => r.employee_metadata?.branch === 'not_used');
      
      const awareness2 = [
        ...used2.map(r => r.responses?.u_awareness_understanding),
        ...notUsed2.map(r => r.responses?.nu_awareness_understanding)
      ].filter(v => v !== undefined && v !== null) as number[];

      const trust2 = [
        ...used2.map(r => r.responses?.u_trust_anonymity),
        ...notUsed2.map(r => r.responses?.nu_trust_anonymity)
      ].filter(v => v !== undefined && v !== null) as number[];

      const usage2 = responses2.length > 0 ? (used2.length / responses2.length) * 100 : 0;

      const impact2 = used2
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v !== undefined && v !== null) as number[];

      // Calculate deltas
      const awarenessAvg1 = calculateAverage(awareness1);
      const awarenessAvg2 = calculateAverage(awareness2);
      const trustAvg1 = calculateAverage(trust1);
      const trustAvg2 = calculateAverage(trust2);
      const impactAvg1 = calculateAverage(impact1);
      const impactAvg2 = calculateAverage(impact2);

      setMetrics({
        awareness: {
          old: Number(awarenessAvg1.toFixed(2)),
          new: Number(awarenessAvg2.toFixed(2)),
          delta: Number((awarenessAvg2 - awarenessAvg1).toFixed(2))
        },
        trust: {
          old: Number(trustAvg1.toFixed(2)),
          new: Number(trustAvg2.toFixed(2)),
          delta: Number((trustAvg2 - trustAvg1).toFixed(2))
        },
        usage: {
          old: Number(usage1.toFixed(1)),
          new: Number(usage2.toFixed(1)),
          delta: Number((usage2 - usage1).toFixed(1))
        },
        impact: {
          old: Number(impactAvg1.toFixed(2)),
          new: Number(impactAvg2.toFixed(2)),
          delta: Number((impactAvg2 - impactAvg1).toFixed(2))
        }
      });

    } catch (error) {
      console.error('Error comparing audits:', error);
      toast.error('Hiba történt az összehasonlítás során');
    } finally {
      setComparing(false);
    }
  };

  const getDeltaIcon = (delta: number) => {
    if (delta > 0.5) return <TrendingUp className="h-5 w-5 text-green-600" />;
    if (delta < -0.5) return <TrendingDown className="h-5 w-5 text-red-600" />;
    return <Minus className="h-5 w-5 text-gray-400" />;
  };

  const getDeltaColor = (delta: number) => {
    if (delta > 0.5) return 'text-green-600';
    if (delta < -0.5) return 'text-red-600';
    return 'text-gray-600';
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
        <h2 className="text-2xl font-bold mb-2">Összehasonlító Nézet</h2>
        <p className="text-muted-foreground">Két felmérés összehasonlítása</p>
      </div>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Compare;
