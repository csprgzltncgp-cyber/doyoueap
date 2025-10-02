import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

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
    <div className="p-8 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">User Kategóriák Megoszlása</h1>
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

      <Card>
        <CardHeader>
          <CardTitle>EAP Program Ismeret és Használat</CardTitle>
          <CardDescription>
            Összes válasz: {totalResponses}
          </CardDescription>
        </CardHeader>
        <CardContent>
          {categoryData.length === 0 ? (
            <div className="text-center py-12 text-muted-foreground">
              Még nincs adat ehhez az audithoz
            </div>
          ) : (
            <div className="space-y-8">
              <ResponsiveContainer width="100%" height={400}>
                <PieChart>
                  <Pie
                    data={categoryData}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={renderCustomLabel}
                    outerRadius={150}
                    fill="#8884d8"
                    dataKey="value"
                  >
                    {categoryData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip 
                    formatter={(value: number) => [`${value} fő`, 'Válaszok']}
                  />
                  <Legend />
                </PieChart>
              </ResponsiveContainer>

              <div className="grid grid-cols-3 gap-4">
                {categoryData.map((category) => (
                  <Card 
                    key={category.name}
                    className="cursor-pointer hover:shadow-lg transition-shadow"
                    style={{ borderLeft: `4px solid ${category.color}` }}
                  >
                    <CardHeader>
                      <CardTitle className="text-lg">{category.name}</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        <p className="text-3xl font-bold">{category.value}</p>
                        <p className="text-muted-foreground">
                          {category.percentage}% a válaszadókból
                        </p>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default UserCategories;
