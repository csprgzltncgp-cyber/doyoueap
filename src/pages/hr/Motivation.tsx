import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

// NOTE: "Audit" in code represents "Felmérés" (EAP Pulse Survey) in the UI
type Audit = StandardAudit;

interface ChartData {
  name: string;
  value: number;
  percentage: string;
}

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

interface MotivationProps {
  selectedAuditId: string;
}

const Motivation = ({ selectedAuditId }: MotivationProps) => {
  const [motivationData, setMotivationData] = useState<ChartData[]>([]);
  const [expertData, setExpertData] = useState<ChartData[]>([]);
  const [channelData, setChannelData] = useState<ChartData[]>([]);
  const [availabilityData, setAvailabilityData] = useState<ChartData[]>([]);
  const [communicationData, setCommunicationData] = useState<ChartData[]>([]);
  const [loading, setLoading] = useState(true);
  const [notUsedCount, setNotUsedCount] = useState(0);

  useEffect(() => {
    if (selectedAuditId) {
      fetchMotivationData(selectedAuditId);
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
    return Object.entries(counts)
      .map(([name, value]) => ({
        name,
        value,
        percentage: total > 0 ? ((value / total) * 100).toFixed(1) : '0',
      }))
      .sort((a, b) => b.value - a.value);
  };

  const fetchMotivationData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'not_used');

      if (error) throw error;

      if (!data || data.length === 0) {
        setMotivationData([]);
        setExpertData([]);
        setChannelData([]);
        setAvailabilityData([]);
        setCommunicationData([]);
        setNotUsedCount(0);
        return;
      }

      setNotUsedCount(data.length);

      // What would motivate them (multiple choice)
      const motivation = countMultipleChoice(data, 'nu_motivation_what');
      setMotivationData(toChartData(motivation));

      // Expert preference (single choice)
      const expert = countSingleChoice(data, 'nu_motivation_expert');
      setExpertData(toChartData(expert));

      // Channel preference (single choice)
      const channel = countSingleChoice(data, 'nu_motivation_channel');
      setChannelData(toChartData(channel));

      // Availability preference (single choice)
      const availability = countSingleChoice(data, 'nu_motivation_availability');
      setAvailabilityData(toChartData(availability));

      // Communication preference (single choice)
      const communication = countSingleChoice(data, 'nu_motivation_communication');
      setCommunicationData(toChartData(communication));

    } catch (error) {
      console.error('Error fetching motivation data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold">Motiváció Riport</h2>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Motivation;
