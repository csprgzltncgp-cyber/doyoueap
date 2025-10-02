import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { toast } from 'sonner';

interface AwarenessData {
  metric: string;
  used: number;
  notUsed: number;
  overall: number;
}

interface AwarenessTabProps {
  auditId: string;
}

export const AwarenessTab = ({ auditId }: AwarenessTabProps) => {
  const [awarenessData, setAwarenessData] = useState<AwarenessData[]>([]);
  const [responseCount, setResponseCount] = useState({ used: 0, notUsed: 0 });

  useEffect(() => {
    if (auditId) {
      fetchAwarenessData(auditId);
    }
  }, [auditId]);

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchAwarenessData = async (selectedAuditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        setAwarenessData([]);
        setResponseCount({ used: 0, notUsed: 0 });
        return;
      }

      const usedResponses = data.filter(r => r.employee_metadata?.branch === 'used');
      const notUsedResponses = data.filter(r => r.employee_metadata?.branch === 'not_used');

      setResponseCount({
        used: usedResponses.length,
        notUsed: notUsedResponses.length
      });

      const usedUnderstanding = usedResponses
        .map(r => r.responses?.u_awareness_understanding)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedHowToUse = usedResponses
        .map(r => r.responses?.u_awareness_how_to_use)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const usedAccessibility = usedResponses
        .map(r => r.responses?.u_awareness_accessibility)
        .filter(v => v !== undefined && v !== null) as number[];

      const notUsedUnderstanding = notUsedResponses
        .map(r => r.responses?.nu_awareness_understanding)
        .filter(v => v !== undefined && v !== null) as number[];

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
          notUsed: 0,
          overall: Number(calculateAverage(usedHowToUse).toFixed(2)),
        },
        {
          metric: 'Elérhetőség érzete',
          used: Number(calculateAverage(usedAccessibility).toFixed(2)),
          notUsed: 0,
          overall: Number(calculateAverage(usedAccessibility).toFixed(2)),
        },
      ];

      setAwarenessData(chartData);
    } catch (error) {
      console.error('Error fetching awareness data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    }
  };

  return (
    <div className="space-y-6">
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

      <Card>
        <CardHeader>
          <CardTitle>Awareness Mutatók</CardTitle>
          <CardDescription>
            Likert skála átlagok (1-5), ahol 5 = teljes mértékben
          </CardDescription>
        </CardHeader>
        <CardContent>
          {awarenessData.length === 0 ? (
            <div className="text-center py-12 text-muted-foreground">
              Még nincs adat ehhez az audithoz
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
    </div>
  );
};