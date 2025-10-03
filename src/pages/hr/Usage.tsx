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
        <Card className="p-12">
          <CardContent className="text-center text-muted-foreground">
            <p className="text-lg mb-2">Nincs megjeleníthető adat</p>
            <p className="text-sm">Ehhez az audithoz még nincsenek olyan válaszok, ahol a kitöltők használták a programot. Csak "Nem tudtam róla" vagy "Nem használtam" válaszok érkeztek eddig.</p>
          </CardContent>
        </Card>
      ) : (
        <>
          {/* Gauge Charts - Simplified frequency and family usage metrics */}
          <div className="grid md:grid-cols-2 gap-6 mb-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Családtagok használata</CardTitle>
                <CardDescription>Hányan használták családtagokkal együtt</CardDescription>
              </CardHeader>
              <CardContent className="flex justify-center">
                <GaugeChart
                  value={familyUsage.yes + familyUsage.no > 0 ? (familyUsage.yes / (familyUsage.yes + familyUsage.no)) * 100 : 0}
                  maxValue={100}
                  size={180}
                  label={`${familyUsage.yes + familyUsage.no > 0 ? ((familyUsage.yes / (familyUsage.yes + familyUsage.no)) * 100).toFixed(0) : 0}%`}
                  sublabel={`${familyUsage.yes} / ${familyUsage.yes + familyUsage.no}`}
                />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Válasz sebesség</CardTitle>
                <CardDescription>Mennyire gyorsan kaptak segítséget</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={200}>
                  <BarChart data={timeToCareData} layout="vertical">
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis type="number" />
                    <YAxis dataKey="name" type="category" width={150} />
                    <Tooltip />
                    <Bar dataKey="value" fill="#3b82f6" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>
          
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
