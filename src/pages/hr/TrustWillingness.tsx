import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis } from 'recharts';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Download, Shield, Users, AlertTriangle, TrendingUp, Lock, Eye } from 'lucide-react';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { Progress } from '@/components/ui/progress';

interface TrustWillingnessProps {
  selectedAuditId: string;
}

const TrustWillingness = ({ selectedAuditId }: TrustWillingnessProps) => {
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

  // Akadályok elemzése (használók)
  const barriersData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const barrier = r.responses?.u_trust_barriers;
    if (barrier) {
      barriersData[barrier] = (barriersData[barrier] || 0) + 1;
    }
  });

  const barriersChartData = Object.entries(barriersData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Összehasonlító adatok - Anonimitás
  const anonymityComparison = [
    {
      group: 'Használók',
      score: parseFloat(usedAnonymityScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Nem használók',
      score: parseFloat(notUsedAnonymityScore),
      count: notUsedResponses.length,
      color: 'hsl(var(--chart-3))'
    }
  ];

  // Összehasonlító adatok - Munkaadói félelem
  const employerFearComparison = [
    {
      group: 'Használók',
      score: parseFloat(usedEmployerFearScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Nem használók',
      score: parseFloat(notUsedEmployerFearScore),
      count: notUsedResponses.length,
      color: 'hsl(var(--chart-3))'
    }
  ];

  // Összehasonlító adatok - Kollégák megítélése
  const colleaguesFearComparison = [
    {
      group: 'Használók',
      score: parseFloat(usedColleaguesFearScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Nem használók',
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

  const totalCount = responses.length;

  return (
    <div className="space-y-6">
      {/* Fejléc */}
      <div>
        <h2 className="text-2xl font-bold mb-2">Bizalom & Készség Részletes Elemzése</h2>
        <p className="text-muted-foreground text-sm">
          Az EAP program bizalmi szintjének, anonimitás védelmének és használati hajlandóságának átfogó kiértékelése
        </p>
      </div>

      {/* 1. sor: Fő bizalmi mutatók */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {/* Általános Bizalom az Anonimitásban */}
        <Card id="overall-anonymity-card">
          <CardHeader className="relative">
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
            <CardDescription>Általános szint (1-5 skála)</CardDescription>
          </CardHeader>
          <CardContent>
            <GaugeChart 
              value={(parseFloat(overallAnonymityScore) / 5) * 100} 
              maxValue={100}
              size={220}
              label={overallAnonymityScore}
              sublabel={`${trustResponses.length} válaszadó`}
              cornerRadius={30}
            />
            <p className="text-xs text-muted-foreground text-center mt-4">
              Mennyire bíznak az anonimitás védelmében
            </p>
          </CardContent>
        </Card>

        {/* Átfogó Bizalmi Index */}
        <Card className="relative overflow-hidden" id="trust-index-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(overallTrustIndex / 5) * 100}%, transparent ${(overallTrustIndex / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('trust-index-card', 'atfogo-bizalmi-index')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Shield className="w-5 h-5" />
              Bizalmi Index
            </CardTitle>
            <CardDescription>Használók átlaga (1-5 skála)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 relative z-10">
            <div className="text-center">
              <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                {overallTrustIndex.toFixed(1)}
              </div>
              <p className="text-sm text-muted-foreground mt-2">
                {overallTrustIndex >= 4 
                  ? '✓ Magas bizalmi szint' 
                  : overallTrustIndex >= 3
                  ? '→ Közepes bizalmi szint'
                  : '⚠ Alacsony bizalmi szint - fejlesztést igényel'}
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Munkaadói Félelem */}
        <Card className="relative overflow-hidden" id="employer-fear-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, hsl(var(--chart-3)) 0%, hsl(var(--chart-3)) ${(parseFloat(overallEmployerFearScore) / 5) * 100}%, transparent ${(parseFloat(overallEmployerFearScore) / 5) * 100}%, transparent 100%)`,
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
              <AlertTriangle className="w-5 h-5" />
              Munkaadói Félelem
            </CardTitle>
            <CardDescription>1-5 skála (magasabb = nagyobb félelem)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 relative z-10">
            <div className="text-center">
              <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-3))' }}>
                {overallEmployerFearScore}
              </div>
              <p className="text-sm text-muted-foreground mt-2">
                {parseFloat(overallEmployerFearScore) <= 2 
                  ? '✓ Alacsony félelemszint' 
                  : parseFloat(overallEmployerFearScore) <= 3.5
                  ? '→ Közepes félelemszint'
                  : '⚠ Magas félelemszint'}
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Használati Hajlandóság */}
        <Card id="likelihood-card">
          <CardHeader className="relative">
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
            <CardDescription>Használók hajlandósága (1-5 skála)</CardDescription>
          </CardHeader>
          <CardContent>
            <GaugeChart 
              value={(parseFloat(likelihoodScore) / 5) * 100} 
              maxValue={100}
              size={220}
              label={likelihoodScore}
              sublabel={`${usedResponses.length} használó`}
              cornerRadius={30}
            />
            <p className="text-xs text-muted-foreground text-center mt-4">
              Mennyire valószínű, hogy újra igénybe veszik
            </p>
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
            <CardDescription>Mi tartja vissza a használókat? (használók körében)</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={barriersChartData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis dataKey="name" type="category" width={150} />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-3))" radius={[0, 8, 8, 0]} />
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

      {/* 5. sor: Bizalmi profil részletesen */}
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
