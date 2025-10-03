import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-react';
import { exportCardToPNG } from '@/lib/exportUtils';
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

interface AwarenessData {
  metric: string;
  used: number;
  notUsed: number;
  overall: number;
}

interface AwarenessProps {
  selectedAuditId: string;
}

const Awareness = ({ selectedAuditId }: AwarenessProps) => {
  const [awarenessData, setAwarenessData] = useState<AwarenessData[]>([]);
  const [loading, setLoading] = useState(true);
  const [responseCount, setResponseCount] = useState({ used: 0, notUsed: 0 });

  useEffect(() => {
    if (selectedAuditId) {
      fetchAwarenessData(selectedAuditId);
    }
  }, [selectedAuditId]);


  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchAwarenessData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        setAwarenessData([]);
        setResponseCount({ used: 0, notUsed: 0 });
        return;
      }

      // Separate responses by branch
      const usedResponses = data.filter(r => r.employee_metadata?.branch === 'used');
      const notUsedResponses = data.filter(r => r.employee_metadata?.branch === 'not_used');

      setResponseCount({
        used: usedResponses.length,
        notUsed: notUsedResponses.length
      });

      // Calculate averages for "used" branch
      const usedUnderstanding = usedResponses
        .map(r => r.responses?.u_awareness_understanding)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedHowToUse = usedResponses
        .map(r => r.responses?.u_awareness_how_to_use)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedAccessibility = usedResponses
        .map(r => r.responses?.u_awareness_accessibility)
        .filter(v => v !== undefined && v !== null) as number[];

      // Calculate averages for "not used" branch
      const notUsedUnderstanding = notUsedResponses
        .map(r => r.responses?.nu_awareness_understanding)
        .filter(v => v !== undefined && v !== null) as number[];

      // Combine all understanding values for overall
      const allUnderstanding = [...usedUnderstanding, ...notUsedUnderstanding];

      const chartData: AwarenessData[] = [
        {
          metric: 'Szolgáltatás megértése',
          used: Number(calculateAverage(usedUnderstanding).toFixed(2)),
          notUsed: Number(calculateAverage(notUsedUnderstanding).toFixed(2)),
          overall: Number(calculateAverage(allUnderstanding).toFixed(2)),
        },
        {
          metric: 'Igénybevételi tudás',
          used: Number(calculateAverage(usedHowToUse).toFixed(2)),
          notUsed: 0, // Not asked in not_used branch
          overall: Number(calculateAverage(usedHowToUse).toFixed(2)),
        },
        {
          metric: 'Elérhetőség érzete',
          used: Number(calculateAverage(usedAccessibility).toFixed(2)),
          notUsed: 0, // Not asked in not_used branch
          overall: Number(calculateAverage(usedAccessibility).toFixed(2)),
        },
      ];

      setAwarenessData(chartData);
    } catch (error) {
      console.error('Error fetching awareness data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold">Ismertség Riport</h2>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Awareness;
