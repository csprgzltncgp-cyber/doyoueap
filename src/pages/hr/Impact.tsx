import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, RadarChart, PolarGrid, PolarAngleAxis, PolarRadiusAxis, Radar } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';

interface Audit {
  id: string;
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config: any;
  is_active: boolean;
  expires_at: string | null;
}

interface ImpactMetric {
  metric: string;
  average: number;
}

interface NPSData {
  promoters: number;
  passives: number;
  detractors: number;
  npsScore: number;
}

const Impact = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [impactData, setImpactData] = useState<ImpactMetric[]>([]);
  const [npsData, setNpsData] = useState<NPSData>({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
  const [loading, setLoading] = useState(true);
  const [usedCount, setUsedCount] = useState(0);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchImpactData(selectedAuditId);
    }
  }, [selectedAuditId]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at')
        .order('start_date', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchImpactData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'used');

      if (error) throw error;

      if (!data || data.length === 0) {
        setImpactData([]);
        setNpsData({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
        setUsedCount(0);
        return;
      }

      setUsedCount(data.length);

      // Impact metrics (Likert 1-5)
      const satisfaction = data
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const problemSolving = data
        .map(r => r.responses?.u_impact_problem_solving)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const wellbeing = data
        .map(r => r.responses?.u_impact_wellbeing)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const performance = data
        .map(r => r.responses?.u_impact_performance)
        .filter(v => v !== undefined && v !== null) as number[];
      
      const consistency = data
        .map(r => r.responses?.u_impact_consistency)
        .filter(v => v !== undefined && v !== null) as number[];

      const metrics: ImpactMetric[] = [
        { metric: 'Elégedettség', average: Number(calculateAverage(satisfaction).toFixed(2)) },
        { metric: 'Problémamegoldás', average: Number(calculateAverage(problemSolving).toFixed(2)) },
        { metric: 'Wellbeing javulás', average: Number(calculateAverage(wellbeing).toFixed(2)) },
        { metric: 'Teljesítmény javulás', average: Number(calculateAverage(performance).toFixed(2)) },
        { metric: 'Szolgáltatás konzisztencia', average: Number(calculateAverage(consistency).toFixed(2)) },
      ];

      setImpactData(metrics);

      // NPS calculation (0-10 scale)
      const npsScores = data
        .map(r => r.responses?.u_impact_nps)
        .filter(v => v !== undefined && v !== null) as number[];

      if (npsScores.length > 0) {
        const promoters = npsScores.filter(score => score >= 9).length;
        const passives = npsScores.filter(score => score >= 7 && score <= 8).length;
        const detractors = npsScores.filter(score => score <= 6).length;
        
        const total = npsScores.length;
        const npsScore = Math.round(((promoters - detractors) / total) * 100);

        setNpsData({
          promoters,
          passives,
          detractors,
          npsScore,
        });
      }

    } catch (error) {
      console.error('Error fetching impact data:', error);
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

  const getNPSColor = (score: number) => {
    if (score >= 50) return 'text-green-600';
    if (score >= 0) return 'text-orange-600';
    return 'text-red-600';
  };

  const getNPSLabel = (score: number) => {
    if (score >= 50) return 'Kiváló';
    if (score >= 30) return 'Jó';
    if (score >= 0) return 'Fejleszthető';
    return 'Kritikus';
  };

  return (
    <div className="p-8 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">4Score: Hatás Riport</h1>
        <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
          <SelectTrigger className="w-80">
            <SelectValue placeholder="Válassz felmérést" />
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

      {usedCount === 0 ? (
        <Card className="p-12">
          <CardContent className="text-center text-muted-foreground">
            <p className="text-lg mb-2">Nincs megjeleníthető adat</p>
            <p className="text-sm">Ehhez a felméréshez még nincsenek olyan válaszok, ahol a kitöltők használták a programot. Csak "Nem tudtam róla" vagy "Nem használtam" válaszok érkeztek eddig.</p>
          </CardContent>
        </Card>
      ) : (
        <>
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
          
          <div className="grid grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Impact Mutatók</CardTitle>
                <CardDescription>Likert skála átlagok (1-5)</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={400}>
                  <BarChart data={impactData} layout="vertical">
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis type="number" domain={[0, 5]} />
                    <YAxis type="category" dataKey="metric" width={150} />
                    <Tooltip />
                    <Bar dataKey="average" fill="#3b82f6" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Radar Chart</CardTitle>
                <CardDescription>Áttekintő impact vizualizáció</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={400}>
                  <RadarChart data={impactData}>
                    <PolarGrid />
                    <PolarAngleAxis dataKey="metric" />
                    <PolarRadiusAxis domain={[0, 5]} />
                    <Radar name="Átlag" dataKey="average" stroke="#3b82f6" fill="#3b82f6" fillOpacity={0.6} />
                    <Tooltip />
                  </RadarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Net Promoter Score (NPS)</CardTitle>
              <CardDescription>
                Ajánlás valószínűsége (0-10 skálán)
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-6">
                <div className="text-center">
                  <p className="text-6xl font-bold mb-2 {getNPSColor(npsData.npsScore)}">
                    {npsData.npsScore}
                  </p>
                  <p className="text-xl text-muted-foreground">
                    {getNPSLabel(npsData.npsScore)}
                  </p>
                </div>

                <div className="grid grid-cols-3 gap-4">
                  <div className="p-6 bg-green-50 dark:bg-green-950 rounded-lg border-2 border-green-500">
                    <p className="text-sm text-muted-foreground mb-2">Promoters (9-10)</p>
                    <p className="text-4xl font-bold text-green-600">{npsData.promoters}</p>
                    <p className="text-sm text-muted-foreground mt-2">
                      {usedCount > 0 ? ((npsData.promoters / usedCount) * 100).toFixed(1) : 0}%
                    </p>
                  </div>
                  
                  <div className="p-6 bg-yellow-50 dark:bg-yellow-950 rounded-lg border-2 border-yellow-500">
                    <p className="text-sm text-muted-foreground mb-2">Passives (7-8)</p>
                    <p className="text-4xl font-bold text-yellow-600">{npsData.passives}</p>
                    <p className="text-sm text-muted-foreground mt-2">
                      {usedCount > 0 ? ((npsData.passives / usedCount) * 100).toFixed(1) : 0}%
                    </p>
                  </div>
                  
                  <div className="p-6 bg-red-50 dark:bg-red-950 rounded-lg border-2 border-red-500">
                    <p className="text-sm text-muted-foreground mb-2">Detractors (0-6)</p>
                    <p className="text-4xl font-bold text-red-600">{npsData.detractors}</p>
                    <p className="text-sm text-muted-foreground mt-2">
                      {usedCount > 0 ? ((npsData.detractors / usedCount) * 100).toFixed(1) : 0}%
                    </p>
                  </div>
                </div>

                <div className="p-4 bg-muted rounded-lg">
                  <p className="text-sm text-muted-foreground">
                    <strong>NPS Számítás:</strong> (Promoters % - Detractors %) = {npsData.npsScore}
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

export default Impact;
