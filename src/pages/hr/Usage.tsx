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

interface ChartData {
  name: string;
  value: number;
  percentage: string;
}

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

interface UsageProps {
  selectedAuditId: string;
}

const Usage = ({ selectedAuditId }: UsageProps) => {
  const [channelData, setChannelData] = useState<ChartData[]>([]);
  const [topicData, setTopicData] = useState<ChartData[]>([]);
  const [frequencyData, setFrequencyData] = useState<ChartData[]>([]);
  const [timeToCareData, setTimeToCareData] = useState<ChartData[]>([]);
  const [familyUsage, setFamilyUsage] = useState({ yes: 0, no: 0 });
  const [loading, setLoading] = useState(true);
  const [usedCount, setUsedCount] = useState(0);

  useEffect(() => {
    if (selectedAuditId) {
      fetchUsageData(selectedAuditId);
    }
  }, [selectedAuditId]);


  const countMultipleChoice = (responses: any[], key: string): { [key: string]: number } => {
    const counts: { [key: string]: number } = {};
    responses.forEach(r => {
      const values = r.responses?.[key];
      if (Array.isArray(values)) {
        values.forEach(val => {
          counts[val] = (counts[val] || 0) + 1;
        });
      }
    });
    return counts;
  };

  const countSingleChoice = (responses: any[], key: string): { [key: string]: number } => {
    const counts: { [key: string]: number } = {};
    responses.forEach(r => {
      const value = r.responses?.[key];
      if (value) {
        counts[value] = (counts[value] || 0) + 1;
      }
    });
    return counts;
  };

  const toChartData = (counts: { [key: string]: number }): ChartData[] => {
    const total = Object.values(counts).reduce((sum, val) => sum + val, 0);
    return Object.entries(counts).map(([name, value]) => ({
      name,
      value,
      percentage: total > 0 ? ((value / total) * 100).toFixed(1) : '0',
    }));
  };

  const fetchUsageData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'used');

      if (error) throw error;

      if (!data || data.length === 0) {
        setChannelData([]);
        setTopicData([]);
        setFrequencyData([]);
        setTimeToCareData([]);
        setFamilyUsage({ yes: 0, no: 0 });
        setUsedCount(0);
        return;
      }

      setUsedCount(data.length);

      // Channels (multiple choice)
      const channels = countMultipleChoice(data, 'u_usage_channel');
      setChannelData(toChartData(channels));

      // Topics (multiple choice)
      const topics = countMultipleChoice(data, 'u_usage_topic');
      setTopicData(toChartData(topics));

      // Frequency (single choice)
      const frequency = countSingleChoice(data, 'u_usage_frequency');
      setFrequencyData(toChartData(frequency));

      // Time to care (single choice)
      const timeToCare = countSingleChoice(data, 'u_usage_time_to_care');
      setTimeToCareData(toChartData(timeToCare));

      // Family usage (yes/no)
      let yesCount = 0;
      let noCount = 0;
      data.forEach(r => {
        const family = r.responses?.u_usage_family;
        if (family === 'yes' || family === 'Igen') yesCount++;
        else if (family === 'no' || family === 'Nem') noCount++;
      });
      setFamilyUsage({ yes: yesCount, no: noCount });

    } catch (error) {
      console.error('Error fetching usage data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold">Használat Riport</h2>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Usage;
