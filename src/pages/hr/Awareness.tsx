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

      {responseCount.used === 0 && responseCount.notUsed === 0 ? (
        <Card className="p-12">
          <CardContent className="text-center text-muted-foreground">
            <p className="text-lg mb-2">Nincs megjeleníthető adat</p>
            <p className="text-sm">Ehhez a felméréshez még nincsenek olyan válaszok, ahol a kitöltők tudtak a programról vagy használták azt. Csak "Nem tudtam róla" válaszok érkeztek eddig.</p>
          </CardContent>
        </Card>
      ) : (
        <>
          {/* Gauge Charts for Key Metrics */}
          <div className="grid md:grid-cols-3 gap-6 mb-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Szolgáltatás megértése</CardTitle>
                <CardDescription>Átlagos értékelés (1-5 skála)</CardDescription>
              </CardHeader>
              <CardContent className="flex justify-center">
                <GaugeChart
                  value={parseFloat(awarenessData.find(d => d.metric === 'Szolgáltatás megértése')?.overall.toFixed(1) || '0')}
                  maxValue={5}
                  size={150}
                  label={awarenessData.find(d => d.metric === 'Szolgáltatás megértése')?.overall.toFixed(1) || '0'}
                  sublabel="/ 5"
                />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Igénybevételi tudás</CardTitle>
                <CardDescription>Használók átlaga (1-5 skála)</CardDescription>
              </CardHeader>
              <CardContent className="flex justify-center">
                <GaugeChart
                  value={parseFloat(awarenessData.find(d => d.metric === 'Igénybevételi tudás')?.used.toFixed(1) || '0')}
                  maxValue={5}
                  size={150}
                  label={awarenessData.find(d => d.metric === 'Igénybevételi tudás')?.used.toFixed(1) || '0'}
                  sublabel="/ 5"
                />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Elérhetőség érzete</CardTitle>
                <CardDescription>Használók átlaga (1-5 skála)</CardDescription>
              </CardHeader>
              <CardContent className="flex justify-center">
                <GaugeChart
                  value={parseFloat(awarenessData.find(d => d.metric === 'Elérhetőség érzete')?.used.toFixed(1) || '0')}
                  maxValue={5}
                  size={150}
                  label={awarenessData.find(d => d.metric === 'Elérhetőség érzete')?.used.toFixed(1) || '0'}
                  sublabel="/ 5"
                />
              </CardContent>
            </Card>
          </div>

          <div className="grid grid-cols-3 gap-4">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Használók</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-3xl font-bold">{responseCount.used}</p>
                <p className="text-sm text-muted-foreground">válaszadó</p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Nem használók</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-3xl font-bold">{responseCount.notUsed}</p>
                <p className="text-sm text-muted-foreground">válaszadó</p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Összes</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-3xl font-bold">{responseCount.used + responseCount.notUsed}</p>
                <p className="text-sm text-muted-foreground">válaszadó</p>
              </CardContent>
            </Card>
          </div>

          <Card id="awareness-pie-card">
            <CardHeader>
              <div className="flex justify-between items-center">
                <div>
                  <CardTitle>Awareness Mutatók - Összesített</CardTitle>
                  <CardDescription>
                    Válaszadók megoszlása
                  </CardDescription>
                </div>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => exportCardToPNG('awareness-pie-card', 'awareness-megoszlas')}
                >
                  <Download className="h-4 w-4 mr-2" />
                  PNG letöltés
                </Button>
              </div>
            </CardHeader>
            <CardContent>
              {awarenessData.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground">
                  Még nincs adat ehhez a felméréshez
                </div>
              ) : (
                <ResponsiveContainer width="100%" height={400}>
                  <PieChart>
                    <Pie
                      data={[
                        { name: 'Használók', value: responseCount.used, color: '#10b981' },
                        { name: 'Nem használók', value: responseCount.notUsed, color: '#f59e0b' },
                      ]}
                      cx="50%"
                      cy="50%"
                      labelLine={false}
                      label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                      outerRadius={120}
                      fill="#8884d8"
                      dataKey="value"
                    >
                      <Cell fill="#10b981" />
                      <Cell fill="#f59e0b" />
                    </Pie>
                    <Tooltip />
                    <Legend />
                  </PieChart>
                </ResponsiveContainer>
              )}
            </CardContent>
          </Card>

          <Card id="awareness-bar-card">
            <CardHeader>
              <div className="flex justify-between items-center">
                <div>
                  <CardTitle>Awareness Mutatók - Részletes</CardTitle>
                  <CardDescription>
                    Likert skála átlagok (1-5), ahol 5 = teljes mértékben
                  </CardDescription>
                </div>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => exportCardToPNG('awareness-bar-card', 'awareness-reszletes')}
                >
                  <Download className="h-4 w-4 mr-2" />
                  PNG letöltés
                </Button>
              </div>
            </CardHeader>
            <CardContent>
              {awarenessData.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground">
                  Még nincs adat ehhez a felméréshez
                </div>
              ) : (
            <div className="space-y-8">
              <ResponsiveContainer width="100%" height={400}>
                <BarChart data={awarenessData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="metric" />
                  <YAxis domain={[0, 5]} />
                  <Tooltip />
                  <Legend />
                  <Bar dataKey="used" name="Használók" fill="#10b981" />
                  <Bar dataKey="notUsed" name="Nem használók" fill="#f59e0b" />
                  <Bar dataKey="overall" name="Összesített" fill="#3b82f6" />
                </BarChart>
              </ResponsiveContainer>

              <div className="grid grid-cols-1 gap-4">
                {awarenessData.map((item) => (
                  <Card key={item.metric}>
                    <CardHeader>
                      <CardTitle className="text-lg">{item.metric}</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-3 gap-4">
                        <div>
                          <p className="text-sm text-muted-foreground">Használók</p>
                          <p className="text-2xl font-bold text-green-600">
                            {item.used > 0 ? item.used : 'N/A'}
                          </p>
                        </div>
                        <div>
                          <p className="text-sm text-muted-foreground">Nem használók</p>
                          <p className="text-2xl font-bold text-orange-600">
                            {item.notUsed > 0 ? item.notUsed : 'N/A'}
                          </p>
                        </div>
                        <div>
                          <p className="text-sm text-muted-foreground">Összesített</p>
                          <p className="text-2xl font-bold text-blue-600">
                            {item.overall > 0 ? item.overall : 'N/A'}
                          </p>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </div>
              )}
            </CardContent>
          </Card>
        </>
      )}
    </div>
  );
};

export default Awareness;
