import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, RadarChart, PolarGrid, PolarAngleAxis, PolarRadiusAxis, Radar } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { GaugeChart } from '@/components/ui/gauge-chart';
import html2canvas from 'html2canvas';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-react';

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

interface ImpactProps {
  selectedAuditId: string;
}

const Impact = ({ selectedAuditId }: ImpactProps) => {
  const [impactData, setImpactData] = useState<ImpactMetric[]>([]);
  const [npsData, setNpsData] = useState<NPSData>({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
  const [loading, setLoading] = useState(true);
  const [usedCount, setUsedCount] = useState(0);

  useEffect(() => {
    if (selectedAuditId) {
      fetchImpactData(selectedAuditId);
    }
  }, [selectedAuditId]);


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
    } finally {
      setLoading(false);
    }
  };

  const getNPSColor = (score: number) => {
    if (score >= 50) return 'text-primary';
    if (score > 0) return 'text-primary/70';
    return 'text-destructive';
  };

  const getNPSLabel = (score: number) => {
    if (score >= 50) return 'Kiváló';
    if (score >= 30) return 'Jó';
    if (score >= 0) return 'Fejleszthető';
    return 'Kritikus';
  };

  const exportCardToPNG = async (cardId: string, fileName: string) => {
    try {
      const element = document.getElementById(cardId);
      if (!element) {
        toast.error('Az elem nem található');
        return;
      }

      const canvas = await html2canvas(element, {
        backgroundColor: '#ffffff',
        scale: 2,
        logging: false,
      });

      const link = document.createElement('a');
      link.download = `${fileName}.png`;
      link.href = canvas.toDataURL('image/png');
      link.click();

      toast.success('Diagram sikeresen exportálva');
    } catch (error) {
      console.error('Error exporting chart:', error);
      toast.error('Hiba történt az exportálás során');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="text-lg">Betöltés...</div>
      </div>
    );
  }

  if (usedCount === 0) {
    return (
      <div className="space-y-6">
        <h2 className="text-2xl font-bold">Hatás Riport</h2>
        <div className="text-center py-12 text-muted-foreground">
          Még nincs kiértékelt adat a programot használók körében
        </div>
      </div>
    );
  }

  const avgImpact = impactData.length > 0 
    ? Number((impactData.reduce((sum, m) => sum + m.average, 0) / impactData.length).toFixed(2))
    : 0;

  return (
    <div className="space-y-6">

      {/* Main Metrics */}
      <div className="grid gap-6 md:grid-cols-2">
        {/* NPS Score */}
        <Card id="impact-nps-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('impact-nps-card', 'hatás-nps')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <div>
              <CardTitle className="text-lg">Net Promoter Score (NPS)</CardTitle>
              <CardDescription>Hányan ajánlanák a programot másoknak?</CardDescription>
            </div>
          </CardHeader>
          <CardContent>
            <div className="mb-4 p-3 bg-muted/50 rounded-lg text-sm space-y-2">
              <p className="text-muted-foreground">
                <strong>Mit mutat az érték?</strong> Az NPS -100-tól +100-ig terjedhet.
              </p>
              <ul className="text-muted-foreground space-y-1 pl-4">
                <li>• <strong>+50 felett:</strong> Kiváló - Sokan ajánlanák</li>
                <li>• <strong>0 körül:</strong> Semleges - Ajánlók és kritikusok egyensúlyban</li>
                <li>• <strong>0 alatt:</strong> Fejlesztendő - Több a kritikus</li>
              </ul>
              <p className="text-muted-foreground text-xs pt-1">
                Kérdés: "0-10 skálán mennyire ajánlaná kollégáinak a programot?"
              </p>
            </div>

            {/* NPS Score Visual */}
            <div className="my-8">
              {/* Score Display */}
              <div className="text-center mb-6">
                <div className={`text-5xl font-bold mb-2 ${getNPSColor(npsData.npsScore)}`}>{npsData.npsScore}</div>
                <div className={`text-lg ${getNPSColor(npsData.npsScore)}`}>{getNPSLabel(npsData.npsScore)}</div>
              </div>

              {/* Scale */}
              <div className="relative h-12 rounded-full mb-8" style={{ background: 'linear-gradient(to right, rgba(51, 102, 255, 0.3), rgba(51, 102, 255, 0.6), rgba(51, 102, 255, 1))' }}>
                {/* Indicator */}
                <div 
                  className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 transition-all duration-500"
                  style={{ left: `${((npsData.npsScore + 100) / 200) * 100}%` }}
                >
                  <div className="w-6 h-6 rounded-full shadow-lg" style={{ backgroundColor: '#050c9c', border: '3px solid #ffffff' }}></div>
                </div>
                
                {/* Scale markers */}
                <div className="absolute -bottom-6 left-0 text-xs text-muted-foreground">-100</div>
                <div className="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs text-muted-foreground">0</div>
                <div className="absolute -bottom-6 right-0 text-xs text-muted-foreground">+100</div>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Average Impact */}
        <Card id="impact-avg-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('impact-avg-card', 'hatás-átlag')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <div>
              <CardTitle className="text-lg">Átlagos Hatás Érték</CardTitle>
              <CardDescription>Az 5 terület átlagos értékelése (1-5 skála)</CardDescription>
            </div>
          </CardHeader>
          <CardContent>
            <div className="mb-4 p-3 bg-muted/50 rounded-lg text-sm space-y-2">
              <p className="text-muted-foreground">
                <strong>Értékelt területek:</strong> Elégedettség, Problémamegoldás, Wellbeing, Teljesítmény, Konzisztencia
              </p>
              <p className="text-muted-foreground">
                Az 5 terület összesített átlaga 1-5 skálán.
              </p>
              <ul className="text-muted-foreground space-y-1 pl-4">
                <li>• <strong>4.5 felett:</strong> Kiváló hatás</li>
                <li>• <strong>3.5-4.5:</strong> Jó hatás</li>
                <li>• <strong>2.5-3.5:</strong> Közepes hatás</li>
                <li>• <strong>2.5 alatt:</strong> Fejlesztendő</li>
              </ul>
            </div>
            <GaugeChart
              value={avgImpact}
              maxValue={5}
              minValue={1}
              size={200}
              label={avgImpact.toFixed(2)}
              sublabel="Átlag"
            />
          </CardContent>
        </Card>
      </div>

      {/* Impact Metrics Gauge Charts */}
      <Card id="impact-metrics-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('impact-metrics-card', 'hatás-területek')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <div>
            <CardTitle>Hatás Területek</CardTitle>
            <CardDescription>Értékelések területenként (1-5 skála)</CardDescription>
          </div>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-6">
            {impactData.map((item) => (
              <div key={item.metric} className="flex flex-col items-center">
                <GaugeChart
                  value={item.average}
                  maxValue={5}
                  minValue={1}
                  size={180}
                  label={item.average.toFixed(2)}
                  sublabel={item.metric}
                />
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Radar Chart */}
      <Card id="impact-radar-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('impact-radar-card', 'hatás-profil')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <div>
            <CardTitle>Hatás Profil</CardTitle>
            <CardDescription>Radar nézet</CardDescription>
          </div>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={400}>
            <RadarChart data={impactData}>
              <PolarGrid />
              <PolarAngleAxis dataKey="metric" />
              <PolarRadiusAxis domain={[0, 5]} />
              <Radar
                name="Impact"
                dataKey="average"
                stroke="#3366ff"
                fill="#3366ff"
                fillOpacity={0.6}
              />
              <Tooltip />
            </RadarChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>

      {/* Statistical Summary */}
      <Card id="impact-summary-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('impact-summary-card', 'hatás-összefoglaló')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <div>
            <CardTitle>Statisztikai Összefoglaló</CardTitle>
            <CardDescription>Hatás riport összesítő</CardDescription>
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <div className="text-sm text-muted-foreground">Válaszadók (használók)</div>
                <div className="text-2xl font-bold">{usedCount}</div>
              </div>
              <div className="space-y-1">
                <div className="text-sm text-muted-foreground">NPS Score</div>
                <div className={`text-2xl font-bold ${getNPSColor(npsData.npsScore)}`}>
                  {npsData.npsScore}
                </div>
              </div>
            </div>
            
            <div className="pt-4 border-t">
              <h4 className="font-semibold mb-3">Impact Metricsek részletesen:</h4>
              <div className="space-y-2">
                {impactData.map((metric) => (
                  <div key={metric.metric} className="flex justify-between items-center">
                    <span className="text-sm">{metric.metric}</span>
                    <span className="font-semibold">{metric.average.toFixed(2)} / 5.00</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="pt-4 border-t">
              <div className="space-y-1">
                <div className="text-sm text-muted-foreground">Átlagos Impact Érték</div>
                <div className="text-2xl font-bold">{avgImpact.toFixed(2)} / 5.00</div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default Impact;
