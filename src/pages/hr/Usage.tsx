import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';

interface Audit {
  id: string;
  company_name: string;
  created_at: string;
}

interface ChartData {
  name: string;
  value: number;
  percentage: string;
}

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

const Usage = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [channelData, setChannelData] = useState<ChartData[]>([]);
  const [topicData, setTopicData] = useState<ChartData[]>([]);
  const [frequencyData, setFrequencyData] = useState<ChartData[]>([]);
  const [timeToCareData, setTimeToCareData] = useState<ChartData[]>([]);
  const [familyUsage, setFamilyUsage] = useState({ yes: 0, no: 0 });
  const [loading, setLoading] = useState(true);
  const [usedCount, setUsedCount] = useState(0);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchUsageData(selectedAuditId);
    }
  }, [selectedAuditId]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select('id, company_name, created_at')
        .eq('is_active', true)
        .order('created_at', { ascending: false });

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
        if (family === 'Igen') yesCount++;
        else if (family === 'Nem') noCount++;
      });
      setFamilyUsage({ yes: yesCount, no: noCount });

    } catch (error) {
      console.error('Error fetching usage data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    }
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
        <h1 className="text-3xl font-bold">Usage Riport</h1>
        <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
          <SelectTrigger className="w-80">
            <SelectValue placeholder="Válassz auditot" />
          </SelectTrigger>
          <SelectContent>
            {audits.map((audit) => (
              <SelectItem key={audit.id} value={audit.id}>
                {audit.company_name} - {new Date(audit.created_at).toLocaleDateString('hu-HU')}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Használók száma</CardTitle>
          <CardDescription>
            Csak azok, akik használták az EAP programot
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-4xl font-bold">{usedCount}</p>
        </CardContent>
      </Card>

      {usedCount === 0 ? (
        <Card>
          <CardContent className="py-12 text-center text-muted-foreground">
            Még nincs használó ehhez az audithoz
          </CardContent>
        </Card>
      ) : (
        <>
          <div className="grid grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Csatornák Megoszlása</CardTitle>
                <CardDescription>Több válasz lehetséges</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={channelData}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="value" fill="#3b82f6" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Témák Megoszlása</CardTitle>
                <CardDescription>Több válasz lehetséges</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <PieChart>
                    <Pie
                      data={topicData}
                      cx="50%"
                      cy="50%"
                      labelLine={false}
                      label={(entry) => `${entry.name}: ${entry.percentage}%`}
                      outerRadius={100}
                      fill="#8884d8"
                      dataKey="value"
                    >
                      {topicData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>

          <div className="grid grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Használati Gyakoriság</CardTitle>
                <CardDescription>Elmúlt 12 hónapban</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={frequencyData}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="value" fill="#10b981" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Time-to-Care</CardTitle>
                <CardDescription>Kapcsolatfelvétel és konzultáció közötti idő</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <PieChart>
                    <Pie
                      data={timeToCareData}
                      cx="50%"
                      cy="50%"
                      labelLine={false}
                      label={(entry) => `${entry.name}: ${entry.value}`}
                      outerRadius={100}
                      fill="#8884d8"
                      dataKey="value"
                    >
                      {timeToCareData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Családtag Használat</CardTitle>
              <CardDescription>Családtagjaid is használták a programot?</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-2 gap-4">
                <div className="p-6 bg-green-50 dark:bg-green-950 rounded-lg border-2 border-green-500">
                  <p className="text-sm text-muted-foreground mb-2">Igen</p>
                  <p className="text-4xl font-bold text-green-600">{familyUsage.yes}</p>
                  <p className="text-sm text-muted-foreground mt-2">
                    {usedCount > 0 ? ((familyUsage.yes / usedCount) * 100).toFixed(1) : 0}%
                  </p>
                </div>
                <div className="p-6 bg-red-50 dark:bg-red-950 rounded-lg border-2 border-red-500">
                  <p className="text-sm text-muted-foreground mb-2">Nem</p>
                  <p className="text-4xl font-bold text-red-600">{familyUsage.no}</p>
                  <p className="text-sm text-muted-foreground mt-2">
                    {usedCount > 0 ? ((familyUsage.no / usedCount) * 100).toFixed(1) : 0}%
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </>
      )}
    </div>
  );
};

export default Usage;
