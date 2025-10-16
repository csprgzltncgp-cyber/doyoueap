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
import { Download, AlertTriangle, TrendingUp } from 'lucide-react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ReportNavigation } from '@/components/navigation/ReportNavigation';
import fourScoreLogo from '@/assets/4score_logo.svg';

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
  audits: Array<{
    id: string;
    start_date: string;
    program_name: string;
    access_mode: string;
    recurrence_config: any;
    is_active: boolean;
    expires_at: string | null;
  }>;
  onAuditChange: (id: string) => void;
}

const Impact = ({ selectedAuditId, audits, onAuditChange }: ImpactProps) => {
  const [impactData, setImpactData] = useState<ImpactMetric[]>([]);
  const [npsData, setNpsData] = useState<NPSData>({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
  const [loading, setLoading] = useState(true);
  const [usedCount, setUsedCount] = useState(0);

  useEffect(() => {
    if (selectedAuditId) {
      fetchImpactData(selectedAuditId);
      
      // Set up real-time subscription
      const channel = supabase
        .channel('impact_responses_changes')
        .on(
          'postgres_changes',
          {
            event: '*',
            schema: 'public',
            table: 'audit_responses',
            filter: `audit_id=eq.${selectedAuditId}`
          },
          (payload) => {
            console.log('Impact response updated:', payload);
            fetchImpactData(selectedAuditId);
          }
        )
        .subscribe();

      return () => {
        supabase.removeChannel(channel);
      };
    }
  }, [selectedAuditId]);


  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchImpactData = async (auditId: string) => {
    try {
      console.log('Fetching impact data for audit:', auditId);
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'used');

      if (error) {
        console.error('Error fetching impact data:', error);
        throw error;
      }

      console.log('Impact data received:', data?.length, 'used responses');

      if (!data || data.length === 0) {
        console.log('No used responses found for audit:', auditId);
        setImpactData([]);
        setNpsData({ promoters: 0, passives: 0, detractors: 0, npsScore: 0 });
        setUsedCount(0);
        return;
      }

      setUsedCount(data.length);

      // Impact metrics (Likert 1-5)
      const satisfaction = data
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => typeof v === 'number' && !isNaN(v)) as number[];
      
      console.log('Satisfaction values:', satisfaction);
      
      const problemSolving = data
        .map(r => r.responses?.u_impact_problem_solving)
        .filter(v => typeof v === 'number' && !isNaN(v)) as number[];
      
      const wellbeing = data
        .map(r => r.responses?.u_impact_wellbeing)
        .filter(v => typeof v === 'number' && !isNaN(v)) as number[];
      
      const performance = data
        .map(r => r.responses?.u_impact_performance)
        .filter(v => typeof v === 'number' && !isNaN(v)) as number[];
      
      const consistency = data
        .map(r => r.responses?.u_impact_consistency)
        .filter(v => typeof v === 'number' && !isNaN(v)) as number[];

      const metrics: ImpactMetric[] = [
        { metric: 'Elégedettség', average: Number(calculateAverage(satisfaction).toFixed(1)) },
        { metric: 'Problémamegoldás', average: Number(calculateAverage(problemSolving).toFixed(1)) },
        { metric: 'Wellbeing javulás', average: Number(calculateAverage(wellbeing).toFixed(1)) },
        { metric: 'Teljesítmény javulás', average: Number(calculateAverage(performance).toFixed(1)) },
        { metric: 'Szolgáltatás konzisztencia', average: Number(calculateAverage(consistency).toFixed(1)) },
      ];

      console.log('Impact metrics:', metrics);
      setImpactData(metrics);

      // NPS calculation (0-10 scale)
      const npsScores = data
        .map(r => r.responses?.u_impact_nps)
        .filter(v => typeof v === 'number' && !isNaN(v)) as number[];

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
    if (score === 0) return 'text-muted-foreground';
    return 'text-destructive';
  };

  const getNPSLabel = (score: number) => {
    if (score >= 50) return 'Kiváló';
    if (score >= 30) return 'Jó';
    if (score > 0) return 'Fejleszthető';
    if (score === 0) return 'Semleges';
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
    ? Number((impactData.reduce((sum, m) => sum + m.average, 0) / impactData.length).toFixed(1))
    : 0;

  return (
    <div className="space-y-6">
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <div className="flex items-center gap-4 mb-2">
              <h2 className="text-2xl font-bold">Hatás Riport</h2>
              <ReportNavigation currentTab="impact" />
            </div>
            <p className="text-muted-foreground">
              A program használóinak elégedettsége, hatékonysága és ajánlási hajlandósága
            </p>
          </div>
        </div>
        {audits.length > 0 && (
          <div className="flex flex-col md:flex-row md:items-center gap-4">
            <img src={fourScoreLogo} alt="4Score" className="h-6" />
            <div className="flex-1 md:max-w-[300px] md:ml-auto">
              <label className="text-xs text-muted-foreground mb-1.5 block">
                Felmérés kiválasztása
              </label>
              <Select value={selectedAuditId} onValueChange={onAuditChange}>
                <SelectTrigger className="w-full">
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
          </div>
        )}
      </div>

      {/* Figyelmeztetés az NPS-hez */}
      {npsData.npsScore < 0 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-4 w-4" />
          <AlertTitle className="text-[#ff0033]">Negatív NPS észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            Az NPS érték negatív ({npsData.npsScore}), ami azt jelenti, hogy több kritikus, mint ajánló. 
            Javasolt intézkedések: részletes felhasználói interjúk, gyenge pontok azonosítása, 
            szolgáltatás minőségének fejlesztése.
          </AlertDescription>
        </Alert>
      )}

      {/* Figyelmeztetés az átlagos hatáshoz */}
      {avgImpact < 2.5 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-4 w-4" />
          <AlertTitle className="text-[#ff0033]">Alacsony hatékonyság észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            Az átlagos hatás érték alacsony ({avgImpact.toFixed(1)}), ami 2.5 alatt van. 
            A program használói nem érzik kellőképpen a pozitív hatást. 
            Javasolt: hatékonyabb szolgáltatások, gyorsabb válaszidő, jobb problémamegoldás.
          </AlertDescription>
        </Alert>
      )}

      {/* Egyedi területi figyelmeztetések */}
      {impactData.map((item) => {
        if (item.average >= 2.5) return null;
        
        let warningText = '';
        switch(item.metric) {
          case 'Elégedettség':
            warningText = `Az általános elégedettség alacsony (${item.average.toFixed(2)}). A használók nem elégedettek a programmal. Javasolt: felhasználói fókuszcsoportok szervezése, visszajelzések részletes elemzése, szolgáltatás testreszabása.`;
            break;
          case 'Problémamegoldás':
            warningText = `A problémamegoldási hatékonyság alacsony (${item.average.toFixed(2)}). A program nem segít elég hatékonyan a problémák kezelésében. Javasolt: tanácsadók képzése, több gyakorlati megoldás, gyorsabb válaszidő.`;
            break;
          case 'Wellbeing javulás':
            warningText = `A wellbeing javulás alacsony (${item.average.toFixed(2)}). A használók nem érzik, hogy javult volna a jóllétük. Javasolt: hosszabb távú támogatás, holisztikus megközelítés, mindfulness és stresszkezelési technikák.`;
            break;
          case 'Teljesítmény javulás':
            warningText = `A teljesítmény javulás alacsony (${item.average.toFixed(2)}). A munkavállalók nem érzik, hogy javult volna a teljesítményük. Javasolt: munkahelyi coaching, produktivitási tanácsadás, időmenedzsment támogatás.`;
            break;
          case 'Szolgáltatás konzisztencia':
            warningText = `A szolgáltatás konzisztenciája alacsony (${item.average.toFixed(2)}). A minőség ingadozik. Javasolt: tanácsadók standardizált képzése, minőségbiztosítás, rendszeres felügyelet.`;
            break;
        }
        
        return (
          <Alert key={item.metric} className="border-[#ff0033] bg-transparent">
            <AlertTriangle style={{ color: '#ff0033' }} className="h-4 w-4" />
            <AlertTitle className="text-[#ff0033]">Alacsony {item.metric}</AlertTitle>
            <AlertDescription className="text-[#ff0033]">
              {warningText}
            </AlertDescription>
          </Alert>
        );
      })}

      {/* Main Metrics */}
      <div className="grid gap-6 md:grid-cols-2">
        {/* Average Impact - MOVED TO FIRST POSITION */}
        <Card id="impact-avg-card" className="border-2 border-[#3366ff]">
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
              <CardTitle className="text-lg flex items-center gap-2">
                <TrendingUp className="w-5 h-5" />
                Hatás Index
              </CardTitle>
              <CardDescription>1-5 skála</CardDescription>
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
              label={avgImpact.toFixed(1)}
              sublabel="Mennyire segített a program összeségében."
            />
            {avgImpact < 2.5 && (
              <div className="flex items-center gap-2 mt-4 text-[#ff0033] text-sm justify-center">
                <AlertTriangle className="w-4 h-4" />
                <span>Alacsony hatékonyság</span>
              </div>
            )}
          </CardContent>
        </Card>

        {/* NPS Score - MOVED TO SECOND POSITION */}
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
                {npsData.npsScore < 0 && (
                  <div className="flex items-center gap-2 mt-2 text-[#ff0033] text-sm justify-center">
                    <AlertTriangle className="w-4 h-4" />
                    <span>Negatív NPS - kritikus helyzet</span>
                  </div>
                )}
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
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {impactData.map((item) => {
              let description = '';
              switch(item.metric) {
                case 'Elégedettség':
                  description = 'Általános elégedettség a programmal';
                  break;
                case 'Problémamegoldás':
                  description = 'Mennyire segített a program a problémák kezelésében';
                  break;
                case 'Wellbeing javulás':
                  description = 'Jóllét és mentális egészség javulása';
                  break;
                case 'Teljesítmény javulás':
                  description = 'Munkahelyi teljesítmény pozitív változása';
                  break;
                case 'Szolgáltatás konzisztencia':
                  description = 'A szolgáltatás minőségének következetessége';
                  break;
              }
              
              const isLow = item.average < 2.5;
              
              return (
                <div key={item.metric} className="flex flex-col items-center p-4 bg-muted/30 rounded-lg">
                  <GaugeChart
                    value={item.average}
                    maxValue={5}
                    minValue={1}
                    size={180}
                    label={item.average.toFixed(2)}
                    sublabel={item.metric}
                    gaugeColor={isLow ? '#ff0033' : undefined}
                  />
                  {isLow && (
                    <div className="flex items-center gap-2 mt-2 text-[#ff0033] text-xs">
                      <AlertTriangle className="w-3 h-3" />
                      <span>Alacsony érték</span>
                    </div>
                  )}
                  <p className="text-xs text-muted-foreground text-center mt-3">
                    {description}
                  </p>
                </div>
              );
            })}
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
            <CardDescription>Összesített hatás nézet</CardDescription>
          </div>
        </CardHeader>
        <CardContent>
          <div className="mb-4 p-3 bg-muted/50 rounded-lg text-sm">
            <p className="text-muted-foreground">
              <strong>Mit mutat a diagram?</strong> A radar diagram az 5 hatás terület egyidejű áttekintését teszi lehetővé. 
              Minél nagyobb a kitöltött terület, annál jobb az összhatás. Az egyenletes ötszög kiegyensúlyozott hatást jelez.
            </p>
          </div>
          <ResponsiveContainer width="100%" height={400}>
            <RadarChart data={impactData}>
              <PolarGrid />
              <PolarAngleAxis dataKey="metric" />
              <PolarRadiusAxis domain={[0, 5]} />
              <Radar
                name="Impact"
                dataKey="average"
                stroke={avgImpact < 2.5 ? '#ff0033' : '#3366ff'}
                fill={avgImpact < 2.5 ? '#ff0033' : '#3366ff'}
                fillOpacity={0.6}
              />
              <Tooltip />
            </RadarChart>
          </ResponsiveContainer>
          {avgImpact < 2.5 && (
            <div className="flex items-center gap-2 mt-4 text-[#ff0033] text-sm justify-center">
              <AlertTriangle className="w-4 h-4" />
              <span>Alacsony hatékonyság</span>
            </div>
          )}
        </CardContent>
      </Card>

    </div>
  );
};

export default Impact;
