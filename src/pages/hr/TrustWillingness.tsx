import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis } from 'recharts';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Download, Shield, Users, AlertTriangle, TrendingUp, Lock, Eye } from 'lucide-react';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { Progress } from '@/components/ui/progress';
import { formatAuditName } from '@/lib/auditUtils';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ReportNavigation } from '@/components/navigation/ReportNavigation';
import fourScoreLogo from "@/assets/4score_logo_cgp.png";

interface TrustWillingnessProps {
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

const TrustWillingness = ({ selectedAuditId, audits, onAuditChange, packageType, companies = [], selectedCompanyId, onCompanyChange }: TrustWillingnessProps) => {
  const [responses, setResponses] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (selectedAuditId) {
      fetchResponses(selectedAuditId);
    }
  }, [selectedAuditId]);

  const fetchResponses = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('*')
        .eq('audit_id', auditId);

      if (error) throw error;
      setResponses(data || []);
    } catch (error) {
      console.error('Error fetching responses:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const exportCardToPNG = async (cardId: string, fileName: string) => {
    try {
      const html2canvas = (await import('html2canvas')).default;
      const element = document.getElementById(cardId);
      
      if (!element) {
        toast.error('Panel nem található');
        return;
      }

      const canvas = await html2canvas(element, {
        backgroundColor: '#ffffff',
        scale: 2,
      });

      const dataUrl = canvas.toDataURL('image/png');
      const link = document.createElement('a');
      link.download = `${fileName}.png`;
      link.href = dataUrl;
      link.click();
      toast.success('PNG sikeresen letöltve!');
    } catch (error) {
      console.error('Error exporting PNG:', error);
      toast.error('Hiba a PNG exportálás során');
    }
  };

  const calculateAverage = (values: number[]) => {
    if (values.length === 0) return '0.0';
    return ((values.reduce((a, b) => a + b, 0) / values.length)).toFixed(1);
  };

  // Adatok szűrése branch szerint
  const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const trustResponses = [...usedResponses, ...notUsedResponses];

  // Bizalmi pontszámok - Anonimitás
  const usedAnonymityScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_trust_anonymity)
      .filter(v => v !== undefined)
  );

  const notUsedAnonymityScore = calculateAverage(
    notUsedResponses
      .map(r => r.responses?.nu_trust_anonymity)
      .filter(v => v !== undefined)
  );

  const overallAnonymityScore = calculateAverage(
    trustResponses
      .map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity)
      .filter(v => v !== undefined)
  );

  // Félelem mutatók
  const usedEmployerFearScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_trust_employer)
      .filter(v => v !== undefined)
  );

  const notUsedEmployerFearScore = calculateAverage(
    notUsedResponses
      .map(r => r.responses?.nu_trust_employer)
      .filter(v => v !== undefined)
  );

  const overallEmployerFearScore = calculateAverage(
    trustResponses
      .map(r => r.responses?.u_trust_employer || r.responses?.nu_trust_employer)
      .filter(v => v !== undefined)
  );

  const usedColleaguesFearScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_trust_colleagues)
      .filter(v => v !== undefined)
  );

  const notUsedColleaguesFearScore = calculateAverage(
    notUsedResponses
      .map(r => r.responses?.nu_trust_colleagues)
      .filter(v => v !== undefined)
  );

  const overallColleaguesFearScore = calculateAverage(
    trustResponses
      .map(r => r.responses?.u_trust_colleagues || r.responses?.nu_trust_colleagues)
      .filter(v => v !== undefined)
  );

  // Használati hajlandóság (csak használók)
  const likelihoodScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_trust_likelihood)
      .filter(v => v !== undefined)
  );

  // Akadályok elemzése (nem használók - mi tartja vissza őket)
  const barriersData: { [key: string]: number } = {};
  const negativeMotivators = [
    'Inkább más módon kérnék segítséget',
    'Nem bízom benne teljesen',
    'Nincs rá időm',
    'Túl bonyolultnak tűnik',
    'Túl bonyolultnak tűnik az elérése vagy használata',
    'Úgy érzem, ez nem nekem való',
    'Úgy érzem, nem nekem szól ez a szolgáltatás',
    'Valami más okból'
  ];
  
  notUsedResponses.forEach(r => {
    const barriers = r.responses?.nu_motivation_what;
    if (Array.isArray(barriers)) {
      barriers.forEach(barrier => {
        if (negativeMotivators.includes(barrier)) {
          barriersData[barrier] = (barriersData[barrier] || 0) + 1;
        }
      });
    }
  });

  const barriersChartData = Object.entries(barriersData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Bizalmi törés elemzése - Nehézségek a használat során (használók)
  // Mapping régi válaszokról az új válaszokra
  const barriersMapping: { [key: string]: string } = {
    'Időhiány': 'Nehezen tudtam időpontot egyeztetni',
    'Nincs rá időm': 'Nehezen tudtam időpontot egyeztetni',
    'Bizalomhiány': 'Nem volt szimpatikus a szakember / nem volt jó a kapcsolat',
    'Úgy érzem, ez nem nekem való': 'Nem volt szimpatikus a szakember / nem volt jó a kapcsolat',
    'Inkább más módon kérnék segítséget': 'Nem volt szimpatikus a szakember / nem volt jó a kapcsolat',
    'Bonyolult': 'Nem kaptam elég információt arról, hogyan működik',
    'Túl bonyolultnak tűnik': 'Nem kaptam elég információt arról, hogyan működik',
    'Valami más okból': 'Túl sokáig tartott a folyamat',
  };

  const difficultiesData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    let barrier = r.responses?.u_trust_barriers;
    if (barrier) {
      // Ha régi válasz, akkor átmappeljük az új válaszra
      barrier = barriersMapping[barrier] || barrier;
      
      if (barrier !== 'Nincs' && barrier !== 'nincs' && barrier !== 'Nem volt ilyen') {
        difficultiesData[barrier] = (difficultiesData[barrier] || 0) + 1;
      }
    }
  });

  const difficultiesChartData = Object.entries(difficultiesData)
    .map(([name, count]) => ({ 
      name, 
      count,
      percentage: usedResponses.length > 0 ? (count / usedResponses.length) * 100 : 0
    }))
    .sort((a, b) => b.count - a.count);

  // Összehasonlító adatok - Anonimitás
  const anonymityComparison = [
    {
      group: 'A program aktív felhasználói',
      score: parseFloat(usedAnonymityScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Akik eddig nem vették igénybe a programot',
      score: parseFloat(notUsedAnonymityScore),
      count: notUsedResponses.length,
      color: 'hsl(var(--chart-3))'
    }
  ];

  // Összehasonlító adatok - Munkaadói félelem
  const employerFearComparison = [
    {
      group: 'A program aktív felhasználói',
      score: parseFloat(usedEmployerFearScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Akik eddig nem vették igénybe a programot',
      score: parseFloat(notUsedEmployerFearScore),
      count: notUsedResponses.length,
      color: 'hsl(var(--chart-3))'
    }
  ];

  // Összehasonlító adatok - Kollégák megítélése
  const colleaguesFearComparison = [
    {
      group: 'A program aktív felhasználói',
      score: parseFloat(usedColleaguesFearScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Akik eddig nem vették igénybe a programot',
      score: parseFloat(notUsedColleaguesFearScore),
      count: notUsedResponses.length,
      color: 'hsl(var(--chart-3))'
    }
  ];

  // Bizalmi profil (használók)
  const trustProfileData = [
    { 
      category: 'Anonimitás', 
      score: parseFloat(usedAnonymityScore),
      description: 'Bizalom az anonimitás védelmében'
    },
    { 
      category: 'Munkaadó', 
      score: 5 - parseFloat(usedEmployerFearScore), // Fordított skála
      description: 'Nincs félelem a munkaadó reakciójától'
    },
    { 
      category: 'Kollégák', 
      score: 5 - parseFloat(usedColleaguesFearScore), // Fordított skála
      description: 'Nincs félelem a kollégák megítélésétől'
    },
    { 
      category: 'Jövőbeli használat', 
      score: parseFloat(likelihoodScore),
      description: 'Hajlandóság ismételt használatra'
    }
  ];

  // Átfogó bizalmi index
  const overallTrustIndex = (trustProfileData.reduce((sum, item) => sum + item.score, 0) / trustProfileData.length);

  // Radar chart adatok
  const radarData = [
    {
      subject: 'Anonimitás',
      Használók: parseFloat(usedAnonymityScore),
      'Nem használók': parseFloat(notUsedAnonymityScore),
      fullMark: 5
    },
    {
      subject: 'Munkaadói bizalom',
      Használók: 5 - parseFloat(usedEmployerFearScore),
      'Nem használók': 5 - parseFloat(notUsedEmployerFearScore),
      fullMark: 5
    },
    {
      subject: 'Kollégák bizalom',
      Használók: 5 - parseFloat(usedColleaguesFearScore),
      'Nem használók': 5 - parseFloat(notUsedColleaguesFearScore),
      fullMark: 5
    }
  ];

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  if (audits.length === 0) {
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <div className="flex items-center gap-4">
            <h2 className="text-2xl font-bold">Bizalom & Készség Részletes Elemzése</h2>
            <ReportNavigation currentTab="trust" />
          </div>
          <p className="text-muted-foreground text-sm">
            Az EAP program bizalmi szintjének, anonimitás védelmének és használati hajlandóságának átfogó kiértékelése
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

  const totalCount = responses.length;

  return (
    <div className="space-y-6">
      {/* Fejléc */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <div className="flex items-center gap-4 mb-2">
              <h2 className="text-2xl font-bold">Bizalom & Készség Részletes Elemzése</h2>
              <ReportNavigation currentTab="trust" />
            </div>
            <p className="text-muted-foreground text-sm">
              Az EAP program bizalmi szintjének, anonimitás védelmének és használati hajlandóságának átfogó kiértékelése
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

      {/* Figyelmeztetés az alacsony bizalmi indexhez */}
      {overallTrustIndex < 3 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
          <AlertTitle className="text-[#ff0033]">Alacsony bizalmi szint észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            Az átfogó bizalmi index {overallTrustIndex.toFixed(1)} pont, ami 3.0 alatt van. 
            Ez azt jelzi, hogy a munkatársak nem bíznak kellőképpen a program anonimitásában és használatában.
            Javasolt intézkedések: fokozott transzparencia, anonimitás garantálásának megerősítése, 
            független szolgáltató kommunikálása, sikertörténetek megosztása.
          </AlertDescription>
        </Alert>
      )}

      {/* Figyelmeztetés az alacsony anonimitási bizalomhoz */}
      {parseFloat(overallAnonymityScore) < 2.5 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
          <AlertTitle className="text-[#ff0033]">Alacsony anonimitási bizalom észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            Az anonimitási bizalom {overallAnonymityScore}, ami 2.5 alatt van.
            A munkavállalók nem bíznak abban, hogy adataik biztonságban vannak.
            Javasolt: független adatvédelmi tanúsítványok bemutatása, adatkezelési szabályzat átlátható
            kommunikálása, külső szolgáltató szerepének hangsúlyozása.
          </AlertDescription>
        </Alert>
      )}

      {/* Figyelmeztetés a magas munkaadói félelemhez */}
      {parseFloat(overallEmployerFearScore) > 3.5 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
          <AlertTitle className="text-[#ff0033]">Magas munkaadói félelem észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            A munkaadói félelemszint {overallEmployerFearScore}, ami 3.5 felett van.
            A munkavállalók tartanak attól, hogy a munkaadó megtudja, ha igénybe veszik a programot.
            Javasolt: vezetői támogatás nyilvános kifejezése, sikeres esetpéldák névtelenül,
            hangsúlyozni, hogy a HR nem kap egyéni adatokat, csak összesített statisztikákat.
          </AlertDescription>
        </Alert>
      )}

      {/* Figyelmeztetés az alacsony használati hajlandósághoz */}
      {parseFloat(likelihoodScore) < 3.0 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
          <AlertTitle className="text-[#ff0033]">Alacsony használati hajlandóság észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            A jövőbeli használati hajlandóság {likelihoodScore}, ami 3.0 alatt van.
            A korábbi használók nem valószínű, hogy újra igénybe vennék a programot.
            Javasolt: felhasználói visszajelzések gyűjtése, szolgáltatás minőségének javítása,
            új szolgáltatások bevezetése, gyorsabb válaszidő biztosítása.
          </AlertDescription>
        </Alert>
      )}

      {/* 1. sor: Fő bizalmi mutatók */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {/* Bizalmi Index (from Reports overview page) */}
        <Card className="relative overflow-hidden" id="trust-index-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, ${
                overallTrustIndex < 3 ? '#ff0033' : 'hsl(var(--chart-2))'
              } 0%, ${
                overallTrustIndex < 3 ? '#ff0033' : 'hsl(var(--chart-2))'
              } ${(overallTrustIndex / 5) * 100}%, transparent ${(overallTrustIndex / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('trust-index-card', 'bizalom-index')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Shield className="w-5 h-5" />
              Bizalmi Index
            </CardTitle>
            <CardDescription>1-5 skála</CardDescription>
          </CardHeader>
          <CardContent className="relative z-10 flex flex-col min-h-[320px]">
            <div className="flex-none flex items-center justify-center" style={{ height: '200px' }}>
              <div className="text-center w-full">
                <div 
                  className="text-6xl font-bold" 
                  style={{ 
                    color: overallTrustIndex < 3 ? '#ff0033' : 'hsl(var(--chart-2))'
                  }}
                >
                  {overallTrustIndex.toFixed(1)}
                </div>
                
                {/* Számegyenes vizualizáció */}
                <div className="mt-4 px-8">
                  <div className="relative h-2 bg-gray-400 rounded-full">
                    <div 
                      className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                      style={{ 
                        left: `calc(${((overallTrustIndex - 1) / 4) * 100}% - 8px)`,
                        backgroundColor: overallTrustIndex < 3 ? '#ff0033' : 'hsl(var(--chart-2))'
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
            
            {/* Figyelmeztető és magyarázó szövegek */}
            {overallTrustIndex < 3 && (
              <div className="bg-muted/30 p-3 rounded-md text-left mb-2">
                <p className="text-xs" style={{ color: '#ff0033' }}>
                  ! Alacsony bizalmi szint - A munkavállalók nem bíznak kellőképpen az EAP program anonimitásában és biztonságában.
                </p>
              </div>
            )}
            <div className="bg-muted/30 p-3 rounded-md text-left">
              <p className="text-xs text-muted-foreground">
                A Bizalmi Index azt mutatja, hogy mennyire bíznak a munkavállalók az EAP program anonimitásában, függetlenségében és biztonságában. Az érték 1-5 skálán mozog.
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Jövőbeli Használat */}
        <Card className="relative overflow-hidden" id="likelihood-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, ${
                parseFloat(likelihoodScore) < 3.0 ? '#ff0033' : 'hsl(var(--chart-2))'
              } 0%, ${
                parseFloat(likelihoodScore) < 3.0 ? '#ff0033' : 'hsl(var(--chart-2))'
              } ${(parseFloat(likelihoodScore) / 5) * 100}%, transparent ${(parseFloat(likelihoodScore) / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('likelihood-card', 'hasznalati-hajlandosag')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <TrendingUp className="w-5 h-5" />
              Jövőbeli Használat
            </CardTitle>
            <CardDescription>1-5 skála</CardDescription>
          </CardHeader>
          <CardContent className="relative z-10 flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center min-h-[200px]">
              <div className="text-center w-full">
                <div 
                  className="text-6xl font-bold" 
                  style={{ 
                    color: parseFloat(likelihoodScore) < 3.0 ? '#ff0033' : 'hsl(var(--chart-2))'
                  }}
                >
                  {likelihoodScore}
                </div>
                
                {/* Számegyenes vizualizáció */}
                <div className="mt-4 px-8">
                  <div className="relative h-2 bg-gray-400 rounded-full">
                    <div 
                      className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                      style={{ 
                        left: `calc(${((parseFloat(likelihoodScore) - 1) / 4) * 100}% - 8px)`,
                        backgroundColor: parseFloat(likelihoodScore) < 3.0 ? '#ff0033' : 'hsl(var(--chart-2))'
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
            
            {/* Figyelmeztető és magyarázó szövegek */}
            {parseFloat(likelihoodScore) < 3.0 && (
              <div className="bg-muted/30 p-3 rounded-md text-left mb-2">
                <p className="text-xs" style={{ color: '#ff0033' }}>
                  ! Alacsony hajlandóság - A munkavállalók nem valószínű, hogy újra igénybe vennék a szolgáltatást.
                </p>
              </div>
            )}
            <div className="bg-muted/30 p-3 rounded-md text-left">
              <p className="text-xs text-muted-foreground">
                A Jövőbeli Használat azt mutatja, hogy milyen valószínűséggel venne újra igénybe az EAP szolgáltatást. Az érték 1-5 skálán mozog.
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Anonimitás Bizalom */}
        <Card className="relative overflow-hidden" id="overall-anonymity-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, ${
                parseFloat(overallAnonymityScore) < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
              } 0%, ${
                parseFloat(overallAnonymityScore) < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
              } ${(parseFloat(overallAnonymityScore) / 5) * 100}%, transparent ${(parseFloat(overallAnonymityScore) / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('overall-anonymity-card', 'altalanos-anonimitas-bizalom')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Lock className="w-5 h-5" />
              Anonimitás Bizalom
            </CardTitle>
            <CardDescription>1-5 skála</CardDescription>
          </CardHeader>
          <CardContent className="relative z-10 flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center min-h-[200px]">
              <div className="text-center w-full">
                <div 
                  className="text-6xl font-bold" 
                  style={{ 
                    color: parseFloat(overallAnonymityScore) < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
                  }}
                >
                  {overallAnonymityScore}
                </div>
                
                {/* Számegyenes vizualizáció */}
                <div className="mt-4 px-8">
                  <div className="relative h-2 bg-gray-400 rounded-full">
                    <div 
                      className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                      style={{ 
                        left: `calc(${((parseFloat(overallAnonymityScore) - 1) / 4) * 100}% - 8px)`,
                        backgroundColor: parseFloat(overallAnonymityScore) < 2.5 ? '#ff0033' : 'hsl(var(--chart-2))'
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
            
            {/* Figyelmeztető és magyarázó szövegek */}
            {parseFloat(overallAnonymityScore) < 2.5 && (
              <div className="bg-muted/30 p-3 rounded-md text-left mb-2">
                <p className="text-xs" style={{ color: '#ff0033' }}>
                  ! Alacsony bizalom - A munkavállalók nem bíznak abban, hogy adataik védve vannak az EAP használat során.
                </p>
              </div>
            )}
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              <p className="text-xs text-muted-foreground">
                Az Anonimitás Bizalom azt mutatja, hogy mennyire bíznak a munkavállalók abban, hogy az EAP használat során adataik védve vannak. Az érték 1-5 skálán mozog.
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Munkaadói Félelem */}
        <Card className="relative overflow-hidden" id="employer-fear-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, ${
                parseFloat(overallEmployerFearScore) > 3.5 ? '#ff0033' : 'hsl(var(--chart-2))'
              } 0%, ${
                parseFloat(overallEmployerFearScore) > 3.5 ? '#ff0033' : 'hsl(var(--chart-2))'
              } ${(parseFloat(overallEmployerFearScore) / 5) * 100}%, transparent ${(parseFloat(overallEmployerFearScore) / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('employer-fear-card', 'munkaadoi-felelem')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Eye className="w-5 h-5" />
              Munkaadói Félelem
            </CardTitle>
            <CardDescription>1-5 skála (fordított)</CardDescription>
          </CardHeader>
          <CardContent className="relative z-10 flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center min-h-[200px]">
              <div className="text-center w-full">
                <div 
                  className="text-6xl font-bold" 
                  style={{ 
                    color: parseFloat(overallEmployerFearScore) > 3.5 ? '#ff0033' : 'hsl(var(--chart-2))'
                  }}
                >
                  {overallEmployerFearScore}
                </div>
                
                {/* Számegyenes vizualizáció */}
                <div className="mt-4 px-8">
                  <div className="relative h-2 bg-gray-400 rounded-full">
                    <div 
                      className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                      style={{ 
                        left: `calc(${((parseFloat(overallEmployerFearScore) - 1) / 4) * 100}% - 8px)`,
                        backgroundColor: parseFloat(overallEmployerFearScore) > 3.5 ? '#ff0033' : 'hsl(var(--chart-2))'
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
            
            {/* Figyelmeztető és magyarázó szövegek */}
            {parseFloat(overallEmployerFearScore) > 3.5 && (
              <div className="bg-muted/30 p-3 rounded-md text-left mb-2">
                <p className="text-xs" style={{ color: '#ff0033' }}>
                  ! Magas félelem - A munkavállalók félnek attól, hogy a munkaadó megtudja az EAP használatát.
                </p>
              </div>
            )}
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              <p className="text-xs text-muted-foreground">
                A Munkaadói Félelem azt mutatja, hogy mennyire félnek a munkavállalók attól, hogy a munkaadó megtudja az EAP használatát. Az érték 1-5 skálán mozog, ahol a magasabb érték nagyobb félelmet jelent.
              </p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* 2. sor: Radar chart + Akadályok */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Bizalmi Dimenziók Radar Chart */}
        <Card id="trust-radar-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('trust-radar-card', 'bizalmi-dimenzio-radar')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Bizalmi Dimenziók Összehasonlítása</CardTitle>
            <CardDescription>Használók vs. Nem használók</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px] flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <RadarChart data={radarData}>
                  <PolarGrid />
                  <PolarAngleAxis dataKey="subject" />
                  <PolarRadiusAxis angle={90} domain={[0, 5]} />
                  <Radar name="Használók" dataKey="Használók" stroke="hsl(var(--chart-2))" fill="hsl(var(--chart-2))" fillOpacity={0.6} />
                  <Radar name="Nem használók" dataKey="Nem használók" stroke="hsl(var(--chart-3))" fill="hsl(var(--chart-3))" fillOpacity={0.6} />
                  <Legend />
                  <Tooltip />
                </RadarChart>
              </ResponsiveContainer>
            </div>
            <p className="text-xs text-muted-foreground text-center mt-4">
              Minél nagyobb a színes terület, annál magasabb a bizalmi szint. A félelem mutatók fordítva értelmezendők (alacsony érték = nagyobb félelem).
            </p>
          </CardContent>
        </Card>

        {/* Bizalmi Akadályok */}
        <Card id="barriers-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('barriers-card', 'bizalmi-akadalyok')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Bizalmi Akadályok</CardTitle>
            <CardDescription>Mi tartja vissza a munkavállalókat attól, hogy használják a programot?</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={barriersChartData} layout="vertical" barSize={60}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis 
                    dataKey="name" 
                    type="category" 
                    width={250}
                    tick={{ fontSize: 12 }}
                    interval={0}
                  />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[0, 8, 8, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* 3. sor: Anonimitás összehasonlítás */}
      <Card id="anonymity-comparison-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('anonymity-comparison-card', 'anonimitas-osszehasonlitas')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle className="text-lg">Anonimitásba Vetett Bizalom</CardTitle>
          <CardDescription>Összehasonlítás használók és nem használók között (1-5 skála)</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {anonymityComparison.map((group) => (
            <div key={group.group} className="space-y-3">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <div 
                    className="w-4 h-4 rounded-full" 
                    style={{ backgroundColor: group.color }}
                  />
                  <div>
                    <p className="font-semibold">{group.group}</p>
                    <p className="text-xs text-muted-foreground">{group.count} válaszadó</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-2xl font-bold">{group.score}</p>
                  <p className="text-xs text-muted-foreground">/ 5.0</p>
                </div>
              </div>
              <Progress 
                value={group.score * 20} 
                style={{ '--progress-background': group.color } as React.CSSProperties}
                className="h-3"
              />
              <div className="bg-muted/30 p-3 rounded-md">
                <p className="text-xs text-muted-foreground">
                  {group.score >= 4 ? '✓ Magas bizalmi szint az anonimitásban' : 
                   group.score >= 3 ? '→ Közepes bizalmi szint' : 
                   '⚠ Alacsony bizalmi szint - fejlesztést igényel'}
                </p>
              </div>
            </div>
          ))}
          
          <div className="pt-4 border-t">
            <div className="flex items-center justify-between text-sm">
              <span className="text-muted-foreground">Különbség:</span>
              <span className="font-semibold">
                {Math.abs(anonymityComparison[0].score - anonymityComparison[1].score).toFixed(1)} pont
              </span>
            </div>
            <p className="text-xs text-muted-foreground mt-2">
              {anonymityComparison[0].score > anonymityComparison[1].score 
                ? 'A használók jobban bíznak az anonimitásban' 
                : anonymityComparison[0].score < anonymityComparison[1].score
                ? 'A nem használók jobban bíznak az anonimitásban'
                : 'Mindkét csoport hasonlóan bízik az anonimitásban'}
            </p>
          </div>
        </CardContent>
      </Card>

      {/* 4. sor: Félelem összehasonlítások */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Munkaadói Félelem Összehasonlítás */}
        <Card id="employer-fear-comparison-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('employer-fear-comparison-card', 'munkaadoi-felelem-osszehasonlitas')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Félelem a Munkaadó Reakciójától</CardTitle>
            <CardDescription>Használók vs. Nem használók (1-5 skála, magasabb = nagyobb félelem)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {employerFearComparison.map((group) => (
              <div key={group.group} className="space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div 
                      className="w-4 h-4 rounded-full" 
                      style={{ backgroundColor: group.color }}
                    />
                    <div>
                      <p className="font-semibold">{group.group}</p>
                      <p className="text-xs text-muted-foreground">{group.count} válaszadó</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-2xl font-bold">{group.score}</p>
                    <p className="text-xs text-muted-foreground">/ 5.0</p>
                  </div>
                </div>
                <Progress 
                  value={group.score * 20} 
                  style={{ '--progress-background': group.color } as React.CSSProperties}
                  className="h-3"
                />
                <div className="bg-muted/30 p-3 rounded-md">
                  <p className="text-xs text-muted-foreground">
                    {group.score <= 2 ? '✓ Alacsony félelemszint' : 
                     group.score <= 3.5 ? '→ Közepes félelemszint' : 
                     '⚠ Magas félelemszint - kommunikáció szükséges'}
                  </p>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        {/* Kollégák Félelem Összehasonlítás */}
        <Card id="colleagues-fear-comparison-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('colleagues-fear-comparison-card', 'kollegak-felelem-osszehasonlitas')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Félelem a Kollégák Megítélésétől</CardTitle>
            <CardDescription>Használók vs. Nem használók (1-5 skála, magasabb = nagyobb félelem)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {colleaguesFearComparison.map((group) => (
              <div key={group.group} className="space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div 
                      className="w-4 h-4 rounded-full" 
                      style={{ backgroundColor: group.color }}
                    />
                    <div>
                      <p className="font-semibold">{group.group}</p>
                      <p className="text-xs text-muted-foreground">{group.count} válaszadó</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-2xl font-bold">{group.score}</p>
                    <p className="text-xs text-muted-foreground">/ 5.0</p>
                  </div>
                </div>
                <Progress 
                  value={group.score * 20} 
                  style={{ '--progress-background': group.color } as React.CSSProperties}
                  className="h-3"
                />
                <div className="bg-muted/30 p-3 rounded-md">
                  <p className="text-xs text-muted-foreground">
                    {group.score <= 2 ? '✓ Alacsony félelemszint' : 
                     group.score <= 3.5 ? '→ Közepes félelemszint' : 
                     '⚠ Magas félelemszint - stigma csökkentés szükséges'}
                  </p>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>
      </div>

      {/* 5. sor: Bizalmi törés - Nehézségek */}
      <Card id="trust-barriers-difficulties-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('trust-barriers-difficulties-card', 'bizalmi-tores-nehezsegek')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle className="text-lg">
            Mi okozhat bizalmi törést a program használata során?
          </CardTitle>
          <CardDescription>Nehézségek a használók körében ({usedResponses.length} fő)</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {difficultiesChartData.length > 0 ? (
            difficultiesChartData.map((item, index) => (
              <div key={index} className="space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex-1">
                    <p className="font-semibold text-sm">{item.name}</p>
                    <p className="text-xs text-muted-foreground">{item.count} említés</p>
                  </div>
                  <div className="text-right">
                    <p className="text-2xl font-bold">{item.percentage.toFixed(1)}%</p>
                  </div>
                </div>
                <Progress 
                  value={item.percentage} 
                  style={{ '--progress-background': '#04565f' } as React.CSSProperties}
                  className="h-3"
                />
              </div>
            ))
          ) : (
            <div className="text-center py-8">
              <p className="text-muted-foreground">Nem jeleztek nehézségeket a használók</p>
              <p className="text-xs text-muted-foreground mt-2">✓ Ez pozitív jel a bizalom szempontjából</p>
            </div>
          )}
        </CardContent>
      </Card>

      {/* 6. sor: Bizalmi profil részletesen */}
      <Card id="trust-profile-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('trust-profile-card', 'bizalmi-profil-reszletesen')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle className="text-lg">Használói Bizalmi Profil Részletesen</CardTitle>
          <CardDescription>Átfogó értékelés a használók körében ({usedResponses.length} fő)</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {trustProfileData.map((item, index) => (
            <div key={index} className="space-y-2">
              <div className="flex items-start justify-between">
                <div>
                  <p className="font-semibold">{item.category}</p>
                  <p className="text-xs text-muted-foreground">{item.description}</p>
                </div>
                <div className="text-right">
                  <p className="text-2xl font-bold">{item.score.toFixed(1)}</p>
                  <p className="text-xs text-muted-foreground">/ 5.0</p>
                </div>
              </div>
              <Progress 
                value={item.score * 20} 
                style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
                className="h-3"
              />
              <div className="flex justify-between text-xs text-muted-foreground">
                <span>1 - Nagyon alacsony</span>
                <span>5 - Nagyon magas</span>
              </div>
            </div>
          ))}
        </CardContent>
      </Card>

      {/* Statisztikai összefoglaló */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Statisztikai Összefoglaló</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Válaszadók összesen</p>
              <p className="text-2xl font-bold">{totalCount}</p>
            </div>
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Használók</p>
              <p className="text-2xl font-bold">{usedResponses.length} ({totalCount > 0 ? ((usedResponses.length / totalCount) * 100).toFixed(1) : 0}%)</p>
            </div>
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Nem használók</p>
              <p className="text-2xl font-bold">{notUsedResponses.length} ({totalCount > 0 ? ((notUsedResponses.length / totalCount) * 100).toFixed(1) : 0}%)</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default TrustWillingness;
