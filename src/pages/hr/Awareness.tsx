import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis } from 'recharts';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Download, Eye, Info, TrendingUp, Users } from 'lucide-react';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { Progress } from '@/components/ui/progress';
import { formatAuditName } from '@/lib/auditUtils';

interface AwarenessProps {
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

const Awareness = ({ selectedAuditId, audits, onAuditChange }: AwarenessProps) => {
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
  const redirectResponses = responses.filter(r => r.employee_metadata?.branch === 'redirect');
  const awarenessResponses = [...usedResponses, ...notUsedResponses];

  // Ismertség pontszámok
  const usedUnderstandingScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_awareness_understanding)
      .filter(v => v !== undefined)
  );

  const notUsedUnderstandingScore = calculateAverage(
    notUsedResponses
      .map(r => r.responses?.nu_awareness_understanding)
      .filter(v => v !== undefined)
  );

  const overallUnderstandingScore = calculateAverage(
    awarenessResponses
      .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
      .filter(v => v !== undefined)
  );

  const howToUseScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_awareness_how_to_use)
      .filter(v => v !== undefined)
  );

  const accessibilityScore = calculateAverage(
    usedResponses
      .map(r => r.responses?.u_awareness_accessibility)
      .filter(v => v !== undefined)
  );

  // Információs források elemzése
  const sourceData: { [key: string]: number } = {};
  awarenessResponses.forEach(r => {
    const sources = r.responses?.u_awareness_source || r.responses?.nu_awareness_source || [];
    sources.forEach((source: string) => {
      sourceData[source] = (sourceData[source] || 0) + 1;
    });
  });

  const sourceChartData = Object.entries(sourceData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Kommunikációs gyakoriság
  const frequencyData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const freq = r.responses?.u_awareness_frequency;
    if (freq) {
      frequencyData[freq] = (frequencyData[freq] || 0) + 1;
    }
  });

  const frequencyChartData = Object.entries(frequencyData).map(([name, value]) => ({
    name,
    value
  }));

  // Információ elégségesség
  const hasEnoughInfo = usedResponses.filter(r => r.responses?.u_awareness_info === 'yes').length;
  const notEnoughInfo = usedResponses.filter(r => r.responses?.u_awareness_info === 'no').length;
  const infoSufficiencyData = [
    { name: 'Elegendő', value: hasEnoughInfo, color: 'hsl(var(--chart-2))' },
    { name: 'Nem elegendő', value: notEnoughInfo, color: 'hsl(var(--chart-3))' }
  ].filter(item => item.value > 0);

  // Megértés összehasonlítás (egyetlen mérhető metrika mindkét csoportban)
  const understandingComparison = [
    {
      group: 'Használók',
      score: parseFloat(usedUnderstandingScore),
      count: usedResponses.length,
      color: 'hsl(var(--chart-2))'
    },
    {
      group: 'Nem használók',
      score: parseFloat(notUsedUnderstandingScore),
      count: notUsedResponses.length,
      color: 'hsl(var(--chart-3))'
    }
  ];

  // Részletes ismertségi profil (több dimenzió a használók körében)
  const awarenessProfileData = [
    { 
      category: 'Megértés', 
      score: parseFloat(usedUnderstandingScore),
      description: 'Mennyire értik a szolgáltatást'
    },
    { 
      category: 'Használat ismerete', 
      score: parseFloat(howToUseScore),
      description: 'Hogyan vehetik igénybe'
    },
    { 
      category: 'Elérhetőség', 
      score: parseFloat(accessibilityScore),
      description: 'Mennyire érzik elérhetőnek'
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
  const awarenessRate = totalCount > 0 ? ((awarenessResponses.length / totalCount) * 100).toFixed(1) : '0';
  const redirectRate = totalCount > 0 ? ((redirectResponses.length / totalCount) * 100).toFixed(1) : '0';

  return (
    <div className="space-y-6">
      {/* Fejléc és összefoglaló kártyák */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <h2 className="text-2xl font-bold mb-2">Ismertség Részletes Elemzése</h2>
            <p className="text-muted-foreground text-sm">
              Az EAP program ismeretének, megértésének és kommunikációjának átfogó kiértékelése
            </p>
          </div>
        </div>
        {audits.length > 0 && (
          <div className="w-full md:max-w-[300px] md:ml-auto">
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
        )}
      </div>

      {/* 1. sor: Fő ismertségi mutatók */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {/* Összesített ismertség */}
        <Card id="overall-awareness-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('overall-awareness-card', 'osszes-ismertseg')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Eye className="w-5 h-5" />
              Általános Ismertség
            </CardTitle>
            <CardDescription>A program ismeretének aránya</CardDescription>
          </CardHeader>
          <CardContent>
            <GaugeChart 
              value={parseFloat(awarenessRate)} 
              maxValue={100}
              size={220}
              label={`${awarenessRate}%`}
              sublabel={`${awarenessResponses.length} / ${totalCount} fő`}
              cornerRadius={30}
            />
            <p className="text-xs text-muted-foreground text-center mt-4">
              A válaszolók közül ennyien tudtak a programról (használók + nem használók)
            </p>
          </CardContent>
        </Card>

        {/* Általános Megértés Szintje */}
        <Card className="relative overflow-hidden" id="understanding-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(parseFloat(overallUnderstandingScore) / 5) * 100}%, transparent ${(parseFloat(overallUnderstandingScore) / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('understanding-card', 'megertes')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Info className="w-5 h-5" />
              Általános Megértés
            </CardTitle>
            <CardDescription>1-5 skála</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 relative z-10">
            <div className="text-center">
              <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                {overallUnderstandingScore}
              </div>
              <p className="text-sm text-muted-foreground mt-2">
                Mennyire értik a munkavállalók az EAP szolgáltatást
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Általános Tudásszint (használók) */}
        <Card className="relative overflow-hidden" id="overall-knowledge-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${((awarenessProfileData.reduce((sum, item) => sum + item.score, 0) / awarenessProfileData.length) / 5) * 100}%, transparent ${((awarenessProfileData.reduce((sum, item) => sum + item.score, 0) / awarenessProfileData.length) / 5) * 100}%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('overall-knowledge-card', 'altalanos-tudasszint')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <TrendingUp className="w-5 h-5" />
              Általános Tudásszint
            </CardTitle>
            <CardDescription>Használók átlaga (1-5 skála)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 relative z-10">
            <div className="text-center">
              <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
                {(awarenessProfileData.reduce((sum, item) => sum + item.score, 0) / awarenessProfileData.length).toFixed(1)}
              </div>
              <p className="text-sm text-muted-foreground mt-2">
                {awarenessProfileData.reduce((sum, item) => sum + item.score, 0) / awarenessProfileData.length >= 3.5 
                  ? '✓ A használók jól ismerik a szolgáltatást' 
                  : '→ Van még fejlesztési lehetőség a kommunikációban'}
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Nem tudtak róla */}
        <Card id="unawareness-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('unawareness-card', 'nem-tudtak-rola')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Users className="w-5 h-5" />
              Nem Ismerték
            </CardTitle>
            <CardDescription>Tájékozatlan munkavállalók</CardDescription>
          </CardHeader>
          <CardContent>
            <GaugeChart 
              value={parseFloat(redirectRate)} 
              maxValue={100}
              size={220}
              label={`${redirectRate}%`}
              sublabel={`${redirectResponses.length} / ${totalCount} fő`}
              cornerRadius={30}
            />
            <p className="text-xs text-muted-foreground text-center mt-4">
              A válaszolók közül ennyien NEM tudtak a programról
            </p>
          </CardContent>
        </Card>
      </div>

      {/* 2. sor: Információs források és Gyakoriság */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Információs források */}
        <Card id="sources-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('sources-card', 'informacios-forrasok')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Információs Források</CardTitle>
            <CardDescription>Honnan értesültek a programról</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={sourceChartData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis dataKey="name" type="category" width={150} />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[0, 8, 8, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        {/* Kommunikációs gyakoriság */}
        <Card id="frequency-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('frequency-card', 'kommunikacios-gyakorisag')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Kommunikációs Gyakoriság</CardTitle>
            <CardDescription>Milyen gyakran kapnak információt (csak használók)</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px] flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={frequencyChartData}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={120}
                    paddingAngle={2}
                    dataKey="value"
                  >
                    {frequencyChartData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={`hsl(var(--chart-${(index % 4) + 1}))`} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </div>
            <div className="flex flex-wrap gap-4 justify-center mt-4">
              {frequencyChartData.map((entry, index) => (
                <div key={index} className="flex items-center gap-2">
                  <div 
                    className="w-3 h-3 rounded-full" 
                    style={{ backgroundColor: `hsl(var(--chart-${(index % 4) + 1}))` }}
                  />
                  <span className="text-sm text-foreground">
                    {entry.name}: {entry.value}
                  </span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* 3. sor: Információ elégségesség */}
      <Card id="info-sufficiency-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('info-sufficiency-card', 'informacio-elegsegeseg')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle className="text-lg">Információ Elégségesség</CardTitle>
          <CardDescription>Használók körében: kaptak-e elegendő információt?</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[350px] flex items-center justify-center">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={infoSufficiencyData}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={120}
                  paddingAngle={2}
                  dataKey="value"
                >
                  {infoSufficiencyData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </div>
          <div className="flex flex-wrap gap-4 justify-center mt-4">
            {infoSufficiencyData.map((entry, index) => (
              <div key={index} className="flex items-center gap-2">
                <div 
                  className="w-3 h-3 rounded-full" 
                  style={{ backgroundColor: entry.color }}
                />
                <span className="text-sm text-foreground">
                  {entry.name}: {entry.value} ({((entry.value / usedResponses.length) * 100).toFixed(0)}%)
                </span>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>


      {/* 4. sor: Összehasonlítások és részletes profilok */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Megértés összehasonlítása */}
        <Card id="comparison-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('comparison-card', 'megértes-osszehasonlitas')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Szolgáltatás Megértésének Szintje</CardTitle>
            <CardDescription>Összehasonlítás használók és nem használók között (1-5 skála)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {understandingComparison.map((group) => (
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
                    {group.score >= 4 ? '✓ Magas megértési szint' : 
                     group.score >= 3 ? '→ Közepes megértési szint' : 
                     '⚠ Alacsony megértési szint - fejlesztést igényel'}
                  </p>
                </div>
              </div>
            ))}
            
            <div className="pt-4 border-t">
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Különbség:</span>
                <span className="font-semibold">
                  {Math.abs(understandingComparison[0].score - understandingComparison[1].score).toFixed(1)} pont
                </span>
              </div>
              <p className="text-xs text-muted-foreground mt-2">
                {understandingComparison[0].score > understandingComparison[1].score 
                  ? 'A használók jobban értik a szolgáltatást' 
                  : 'A nem használók hasonlóan értik a szolgáltatást'}
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Használói tudásszint részletesen */}
        <Card id="awareness-profile-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('awareness-profile-card', 'hasznaloi-tudasszint')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Használói Tudásszint Részletesen</CardTitle>
            <CardDescription>Átfogó értékelés a használók körében ({usedResponses.length} fő)</CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {awarenessProfileData.map((item, index) => (
              <div key={index} className="space-y-2">
                <div className="flex items-start justify-between">
                  <div>
                    <p className="font-semibold">{item.category}</p>
                    <p className="text-xs text-muted-foreground">{item.description}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-2xl font-bold">{item.score}</p>
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
      </div>

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
              <p className="text-sm text-muted-foreground">Tudtak a programról</p>
              <p className="text-2xl font-bold">{awarenessResponses.length} ({awarenessRate}%)</p>
            </div>
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Nem tudtak róla</p>
              <p className="text-2xl font-bold">{redirectResponses.length} ({redirectRate}%)</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default Awareness;
