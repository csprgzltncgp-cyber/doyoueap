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
  packageType?: string;
  companies?: Array<{ id: string; company_name: string }>;
  selectedCompanyId?: string;
  onCompanyChange?: (id: string) => void;
}

const Impact = ({ selectedAuditId, audits, onAuditChange, packageType, companies = [], selectedCompanyId, onCompanyChange }: ImpactProps) => {
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

  if (audits.length === 0) {
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <div className="flex items-center gap-4">
            <h2 className="text-2xl font-bold">Hatás Riport</h2>
            <ReportNavigation currentTab="impact" />
          </div>
          <p className="text-muted-foreground">
            A program használóinak elégedettsége, hatékonysága és ajánlási hajlandósága
          </p>
          <div className="flex flex-col md:flex-row md:items-center gap-4 mb-4">
            <img src={fourScoreLogo} alt="4Score" className="h-6" />
            {packageType === 'partner' && companies.length > 0 && onCompanyChange && (
              <div className="flex-1 md:max-w-[300px] md:ml-auto">
                <label className="text-xs text-muted-foreground mb-1.5 block">
                  Ügyfélcég szűrése
                </label>
                <Select value={selectedCompanyId} onValueChange={onCompanyChange}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Válassz ügyfélcéget" />
                  </SelectTrigger>
                  <SelectContent align="start">
                    <SelectItem value="all">Összes ügyfélcég</SelectItem>
                    {companies.map((company) => (
                      <SelectItem key={company.id} value={company.id}>
                        {company.company_name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            )}
          </div>
          <div className="text-center py-12 text-muted-foreground">
            Még nincs felmérés ehhez a céghez. Hozz létre egy új felmérést az első riport elkészítéséhez.
          </div>
        </div>
      </div>
    );
  }

  if (usedCount === 0) {
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <div className="flex items-center gap-4">
            <h2 className="text-2xl font-bold">Hatás Riport</h2>
            <ReportNavigation currentTab="impact" />
          </div>
          <p className="text-muted-foreground">
            A program használóinak elégedettsége, hatékonysága és ajánlási hajlandósága
          </p>
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
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
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
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
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
            <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
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
        <Card id="impact-avg-card" className={`border-2 ${avgImpact < 2.5 ? 'border-[#ff0033]' : 'border-[#3366ff]'} relative overflow-hidden`}>
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, ${
                avgImpact < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
              } 0%, ${
                avgImpact < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
              } ${(avgImpact / 5) * 100}%, transparent ${(avgImpact / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
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
          <CardContent className="relative z-10 flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center min-h-[200px]">
              <div className="text-center w-full">
                <div 
                  className="text-6xl font-bold" 
                  style={{ 
                    color: avgImpact < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
                  }}
                >
                  {avgImpact.toFixed(1)}
                </div>
                
                {/* Számegyenes vizualizáció */}
                <div className="mt-4 px-8">
                  <div className="relative h-2 bg-gray-400 rounded-full">
                    <div 
                      className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                      style={{ 
                        left: `calc(${((avgImpact - 1) / 4) * 100}% - 8px)`,
                        backgroundColor: avgImpact < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
                      }}
                    />
                  </div>
                  <div className="flex justify-between text-xs text-muted-foreground mt-1">
                    <span>1</span>
                    <span>2</span>
                    <span>3</span>
                    <span>4</span>
                    <span>5</span>
                  </div>
                </div>
              </div>
            </div>
            
            {/* Magyarázó szöveg mindig lent, balra zárva */}
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              {avgImpact < 2.5 ? (
                <div className="flex items-start gap-2">
                  <AlertTriangle className="h-5 w-5 flex-shrink-0 mt-0.5" style={{ color: '#ff0033' }} />
                  <p className="text-xs" style={{ color: '#ff0033' }}>
                    Alacsony hatékonyság - A program nem segít kellőképpen az öt fő területen.
                  </p>
                </div>
              ) : (
                <p className="text-xs text-muted-foreground">
                  A Hatás Index azt mutatja, hogy mennyire segített a program összeségében az öt fő területen (elégedettség, problémamegoldás, wellbeing, teljesítmény, konzisztencia).
                </p>
              )}
            </div>
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
              <CardTitle className="text-lg flex items-center gap-2">
                <TrendingUp className="w-5 h-5" />
                Net Promoter Score (NPS)
              </CardTitle>
              <CardDescription>Hányan ajánlanák a programot másoknak?</CardDescription>
            </div>
          </CardHeader>
          <CardContent>
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
            
            <div className="bg-muted/30 p-3 rounded-md mt-4">
              <p className="text-xs text-muted-foreground">
                {npsData.npsScore >= 50
                  ? '✓ Kiváló NPS - Sokan ajánlanák a programot kollégáiknak.'
                  : npsData.npsScore >= 30
                  ? '✓ Jó NPS - A programot általában ajánlanák.'
                  : npsData.npsScore > 0
                  ? '→ Fejleszthető NPS - Van tér a javításra.'
                  : npsData.npsScore === 0
                  ? '→ Semleges NPS - Ajánlók és kritikusok egyensúlyban vannak.'
                  : 'ℹ Negatív NPS - Több kritikus, mint ajánló. Javasolt: részletes felhasználói interjúk, gyenge pontok azonosítása, szolgáltatás minőségének fejlesztése.'}
              </p>
            </div>
          </CardContent>
        </Card>
      </div>


      {/* Impact Metrics - Individual Cards */}
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
              <Card key={item.metric} id={`impact-${item.metric.toLowerCase().replace(/\s+/g, '-')}-card`} className="relative overflow-hidden">
                <div 
                  className="absolute inset-0 transition-all duration-500"
                  style={{
                    background: `linear-gradient(to top, ${
                      isLow ? '#ff0033' : 'hsl(var(--chart-2))'
                    } 0%, ${
                      isLow ? '#ff0033' : 'hsl(var(--chart-2))'
                    } ${(item.average / 5) * 100}%, transparent ${(item.average / 5) * 100}%, transparent 100%)`,
                    opacity: 0.1
                  }}
                />
                <CardHeader className="relative z-10">
                  <Button
                    variant="ghost"
                    size="icon"
                    className="absolute right-2 top-2 h-8 w-8"
                    onClick={() => exportCardToPNG(`impact-${item.metric.toLowerCase().replace(/\s+/g, '-')}-card`, `hatás-${item.metric.toLowerCase()}`)}
                  >
                    <Download className="h-4 w-4" />
                  </Button>
                  <CardTitle className="text-lg">{item.metric}</CardTitle>
                  <CardDescription>1-5 skála</CardDescription>
                </CardHeader>
                <CardContent className="relative z-10 flex flex-col min-h-[320px]">
                  <div className="flex-1 flex items-center justify-center min-h-[200px]">
                    <div className="text-center w-full">
                      <div 
                        className="text-6xl font-bold" 
                        style={{ 
                          color: isLow ? '#ff0033' : 'hsl(var(--chart-2))'
                        }}
                      >
                        {item.average.toFixed(1)}
                      </div>
                      
                      {/* Számegyenes vizualizáció */}
                      <div className="mt-4 px-8">
                        <div className="relative h-2 bg-gray-400 rounded-full">
                          <div 
                            className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                            style={{ 
                              left: `calc(${((item.average - 1) / 4) * 100}% - 8px)`,
                              backgroundColor: isLow ? '#ff0033' : 'hsl(var(--chart-2))'
                            }}
                          />
                        </div>
                        <div className="flex justify-between text-xs text-muted-foreground mt-1">
                          <span>1</span>
                          <span>2</span>
                          <span>3</span>
                          <span>4</span>
                          <span>5</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  {/* Magyarázó szöveg mindig lent, balra zárva */}
                  <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
                    {isLow ? (
                      <div className="flex items-start gap-2">
                        <AlertTriangle className="h-5 w-5 flex-shrink-0 mt-0.5" style={{ color: '#ff0033' }} />
                        <p className="text-xs" style={{ color: '#ff0033' }}>
                          Alacsony érték - {description}
                        </p>
                      </div>
                    ) : (
                      <p className="text-xs text-muted-foreground">
                        {description}
                      </p>
                    )}
                  </div>
                </CardContent>
              </Card>
            );
          })}
      </div>

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
        <CardContent className="flex flex-col">
          <div className="w-full">
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
          </div>
          <div className="w-full mt-4 p-3 bg-muted/50 rounded-lg text-sm">
            <p className="text-muted-foreground">
              <strong>Mit mutat a diagram?</strong> A radar diagram az 5 hatás terület egyidejű áttekintését teszi lehetővé. 
              Minél nagyobb a kitöltött terület, annál jobb az összhatás. Az egyenletes ötszög kiegyensúlyozott hatást jelez.
            </p>
          </div>
          {avgImpact < 2.5 && (
            <div className="flex items-center gap-2 mt-4 text-[#ff0033] text-sm justify-center">
              <AlertTriangle className="h-5 w-5" />
              <span>Alacsony hatékonyság</span>
            </div>
          )}
        </CardContent>
      </Card>

    </div>
  );
};

export default Impact;
