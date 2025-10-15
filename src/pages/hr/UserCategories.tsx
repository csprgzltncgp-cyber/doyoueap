import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';
import { ReportNavigation } from '@/components/navigation/ReportNavigation';

type Audit = StandardAudit;

interface CategoryData {
  name: string;
  value: number;
  percentage: string;
  color: string;
}

const COLORS = {
  'Nem tudott róla': '#ef4444',
  'Nem használta': '#f59e0b',
  'Használta': '#10b981',
};

const UserCategories = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [categoryData, setCategoryData] = useState<CategoryData[]>([]);
  const [loading, setLoading] = useState(true);
  const [totalResponses, setTotalResponses] = useState(0);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchCategoryData(selectedAuditId);
    }
  }, [selectedAuditId]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select(AUDIT_SELECT_FIELDS)
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

  const fetchCategoryData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses')
        .eq('audit_id', auditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        setCategoryData([]);
        setTotalResponses(0);
        return;
      }

      // Count categories
      const counts = {
        'Nem tudott róla': 0,
        'Nem használta': 0,
        'Használta': 0,
      };

      data.forEach((response) => {
        const eapKnowledge = response.responses?.eap_knowledge;
        
        if (eapKnowledge === 'Nem tudtam róla') {
          counts['Nem tudott róla']++;
        } else if (eapKnowledge === 'Igen, tudok róla, de nem használtam') {
          counts['Nem használta']++;
        } else if (eapKnowledge === 'Igen, tudok róla és használtam') {
          counts['Használta']++;
        }
      });

      const total = data.length;
      setTotalResponses(total);

      const chartData: CategoryData[] = Object.entries(counts).map(([name, value]) => ({
        name,
        value,
        percentage: total > 0 ? ((value / total) * 100).toFixed(1) : '0',
        color: COLORS[name as keyof typeof COLORS],
      }));

      setCategoryData(chartData);
    } catch (error) {
      console.error('Error fetching category data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    }
  };

  const renderCustomLabel = (entry: any) => {
    return `${entry.name}: ${entry.percentage}%`;
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6 pt-20 md:pt-0">
      <div className="flex items-center gap-4 mb-4">
        <h2 className="text-2xl font-bold">Felhasználói Kategóriák</h2>
        <ReportNavigation currentTab="categories" />
      </div>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default UserCategories;
