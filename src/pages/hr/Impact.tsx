import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, RadarChart, PolarGrid, PolarAngleAxis, PolarRadiusAxis, Radar } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { GaugeChart } from '@/components/ui/gauge-chart';

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

interface ImpactMetric {
  metric: string;
  average: number;
}

interface NPSData {
  promoters: number;
  passives: number;
  detractors: number;
  npsScore: number;
}

interface ImpactProps {
  selectedAuditId: string;
}

const Impact = ({ selectedAuditId }: ImpactProps) => {
  const [impactData, setImpactData] = useState<ImpactMetric[]>([]);
  const [npsData, setNpsData] = useState<NPSData>({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
  const [loading, setLoading] = useState(true);
  const [usedCount, setUsedCount] = useState(0);

  useEffect(() => {
    if (selectedAuditId) {
      fetchImpactData(selectedAuditId);
    }
  }, [selectedAuditId]);


  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchImpactData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'used');

      if (error) throw error;

      if (!data || data.length === 0) {
        setImpactData([]);
        setNpsData({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
        setUsedCount(0);
        return;
      }

      setUsedCount(data.length);

      // Impact metrics (Likert 1-5)
      const satisfaction = data
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const problemSolving = data
        .map(r => r.responses?.u_impact_problem_solving)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const wellbeing = data
        .map(r => r.responses?.u_impact_wellbeing)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const performance = data
        .map(r => r.responses?.u_impact_performance)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const consistency = data
        .map(r => r.responses?.u_impact_consistency)
        .filter(v => v !== undefined && v !== null) as number[];

      const metrics: ImpactMetric[] = [
        { metric: 'Elégedettség', average: Number(calculateAverage(satisfaction).toFixed(2)) },
        { metric: 'Problémamegoldás', average: Number(calculateAverage(problemSolving).toFixed(2)) },
        { metric: 'Wellbeing javulás', average: Number(calculateAverage(wellbeing).toFixed(2)) },
        { metric: 'Teljesítmény javulás', average: Number(calculateAverage(performance).toFixed(2)) },
        { metric: 'Szolgáltatás konzisztencia', average: Number(calculateAverage(consistency).toFixed(2)) },
      ];

      setImpactData(metrics);

      // NPS calculation (0-10 scale)
      const npsScores = data
        .map(r => r.responses?.u_impact_nps)
        .filter(v => v !== undefined && v !== null) as number[];

      if (npsScores.length > 0) {
        const promoters = npsScores.filter(score => score >= 9).length;
        const passives = npsScores.filter(score => score >= 7 && score <= 8).length;
        const detractors = npsScores.filter(score => score <= 6).length;
        
        const total = npsScores.length;
        const npsScore = Math.round(((promoters - detractors) / total) * 100);

        setNpsData({
          promoters,
          passives,
          detractors,
          npsScore,
        });
      }

    } catch (error) {
      console.error('Error fetching impact data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const getNPSColor = (score: number) => {
    if (score >= 50) return 'text-green-600';
    if (score >= 0) return 'text-orange-600';
    return 'text-red-600';
  };

  const getNPSLabel = (score: number) => {
    if (score >= 50) return 'Kiváló';
    if (score >= 30) return 'Jó';
    if (score >= 0) return 'Fejleszthető';
    return 'Kritikus';
  };

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold">Hatás Riport</h2>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Impact;
