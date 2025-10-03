import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
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

interface DemographicStats {
  total: number;
  notKnew: number;
  notUsed: number;
  used: number;
}

interface ComparisonData {
  category: string;
  overall: number;
  filtered: number;
}

interface DemographicsProps {
  selectedAuditId: string;
}

const Demographics = ({ selectedAuditId }: DemographicsProps) => {
  const [selectedGender, setSelectedGender] = useState<string>('all');
  const [selectedAge, setSelectedAge] = useState<string>('all');
  const [stats, setStats] = useState<DemographicStats>({ total: 0, notKnew: 0, notUsed: 0, used: 0 });
  const [categoryData, setCategoryData] = useState<any[]>([]);
  const [awarenessComparison, setAwarenessComparison] = useState<ComparisonData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (selectedAuditId) {
      fetchDemographicData(selectedAuditId, selectedGender, selectedAge);
    }
  }, [selectedAuditId, selectedGender, selectedAge]);


  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchDemographicData = async (auditId: string, gender: string, age: string) => {
    try {
      const { data: allData, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId);

      if (error) throw error;

      if (!allData || allData.length === 0) {
        setStats({ total: 0, notKnew: 0, notUsed: 0, used: 0 });
        setCategoryData([]);
        setAwarenessComparison([]);
        return;
      }

      // Filter by demographics
      const filteredData = allData.filter(r => {
        const genderMatch = gender === 'all' || r.responses?.gender === gender;
        const ageMatch = age === 'all' || r.responses?.age === age;
        return genderMatch && ageMatch;
      });

      // Calculate category distribution
      let notKnew = 0, notUsed = 0, used = 0;
      filteredData.forEach(r => {
        const branch = r.employee_metadata?.branch;
        if (branch === 'redirect') notKnew++;
        else if (branch === 'not_used') notUsed++;
        else if (branch === 'used') used++;
      });

      const total = filteredData.length;
      setStats({ total, notKnew, notUsed, used });

      // Category distribution chart
      setCategoryData([
        { 
          name: 'Nem tudott róla', 
          value: notKnew, 
          percentage: total > 0 ? ((notKnew / total) * 100).toFixed(1) : 0 
        },
        { 
          name: 'Nem használta', 
          value: notUsed, 
          percentage: total > 0 ? ((notUsed / total) * 100).toFixed(1) : 0 
        },
        { 
          name: 'Használta', 
          value: used, 
          percentage: total > 0 ? ((used / total) * 100).toFixed(1) : 0 
        },
      ]);

      // Awareness comparison (overall vs filtered)
      const allUsed = allData.filter(r => r.employee_metadata?.branch === 'used');
      const filteredUsed = filteredData.filter(r => r.employee_metadata?.branch === 'used');

      const allNotUsed = allData.filter(r => r.employee_metadata?.branch === 'not_used');
      const filteredNotUsed = filteredData.filter(r => r.employee_metadata?.branch === 'not_used');

      // Calculate awareness metrics
      const allUnderstanding = [
        ...allUsed.map(r => r.responses?.u_awareness_understanding).filter(v => v),
        ...allNotUsed.map(r => r.responses?.nu_awareness_understanding).filter(v => v)
      ] as number[];

      const filteredUnderstanding = [
        ...filteredUsed.map(r => r.responses?.u_awareness_understanding).filter(v => v),
        ...filteredNotUsed.map(r => r.responses?.nu_awareness_understanding).filter(v => v)
      ] as number[];

      const allTrust = [
        ...allUsed.map(r => r.responses?.u_trust_anonymity).filter(v => v),
        ...allNotUsed.map(r => r.responses?.nu_trust_anonymity).filter(v => v)
      ] as number[];

      const filteredTrust = [
        ...filteredUsed.map(r => r.responses?.u_trust_anonymity).filter(v => v),
        ...filteredNotUsed.map(r => r.responses?.nu_trust_anonymity).filter(v => v)
      ] as number[];

      const allSatisfaction = allUsed
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v) as number[];

      const filteredSatisfaction = filteredUsed
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v) as number[];

      setAwarenessComparison([
        {
          category: 'Szolgáltatás megértése',
          overall: Number(calculateAverage(allUnderstanding).toFixed(2)),
          filtered: Number(calculateAverage(filteredUnderstanding).toFixed(2)),
        },
        {
          category: 'Anonimitásba bizalom',
          overall: Number(calculateAverage(allTrust).toFixed(2)),
          filtered: Number(calculateAverage(filteredTrust).toFixed(2)),
        },
        {
          category: 'Elégedettség',
          overall: Number(calculateAverage(allSatisfaction).toFixed(2)),
          filtered: Number(calculateAverage(filteredSatisfaction).toFixed(2)),
        },
      ]);

    } catch (error) {
      console.error('Error fetching demographic data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold">Demográfiai Bontás</h2>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Demographics;
