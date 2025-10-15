import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar } from 'recharts';
import { toast } from 'sonner';
import { TrendingUp, TrendingDown, Minus, Download } from 'lucide-react';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';
import { Button } from '@/components/ui/button';
import { GaugeChart } from '@/components/ui/gauge-chart';

type Audit = StandardAudit;

interface TrendData {
  date: string;
  awareness: number;
  trust: number;
  usage: number;
  impact: number;
}

interface CategoryTrend {
  date: string;
  notKnew: number;
  notUsed: number;
  used: number;
}

interface TrendsProps {
  selectedAuditId: string;
  audits: Audit[];
  onAuditChange: (id: string) => void;
}

const Trends = ({ selectedAuditId, audits, onAuditChange }: TrendsProps) => {
  const [compareAuditId, setCompareAuditId] = useState<string>('none');
  const [trendData, setTrendData] = useState<TrendData[]>([]);
  const [categoryTrend, setCategoryTrend] = useState<CategoryTrend[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (selectedAuditId) {
      const auditIds = compareAuditId === 'none' ? [selectedAuditId] : [selectedAuditId, compareAuditId];
      fetchTrendData(auditIds);
    }
  }, [selectedAuditId, compareAuditId]);

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

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchTrendData = async (auditIds: string[]) => {
    try {
      setLoading(true);
      const trends: TrendData[] = [];
      const catTrends: CategoryTrend[] = [];

      for (const auditId of auditIds) {
        const audit = audits.find(a => a.id === auditId);
        if (!audit) continue;

        const { data, error } = await supabase
          .from('audit_responses')
          .select('responses, employee_metadata')
          .eq('audit_id', auditId);

        if (error) throw error;
        if (!data || data.length === 0) continue;

        const date = new Date(audit.start_date).toLocaleDateString('hu-HU', { 
          year: 'numeric', 
          month: 'short' 
        });

        // Category counts
        let notKnew = 0, notUsed = 0, used = 0;
        data.forEach(r => {
          const branch = r.employee_metadata?.branch;
          if (branch === 'redirect') notKnew++;
          else if (branch === 'not_used') notUsed++;
          else if (branch === 'used') used++;
        });

        const total = data.length;
        catTrends.push({
          date,
          notKnew: total > 0 ? Number(((notKnew / total) * 100).toFixed(1)) : 0,
          notUsed: total > 0 ? Number(((notUsed / total) * 100).toFixed(1)) : 0,
          used: total > 0 ? Number(((used / total) * 100).toFixed(1)) : 0,
        });

        // Awareness (understanding)
        const usedResponses = data.filter(r => r.employee_metadata?.branch === 'used');
        const notUsedResponses = data.filter(r => r.employee_metadata?.branch === 'not_used');

        const awarenessValues = [
          ...usedResponses.map(r => r.responses?.u_awareness_understanding),
          ...notUsedResponses.map(r => r.responses?.nu_awareness_understanding)
        ].filter(v => v !== undefined && v !== null) as number[];

        // Trust (anonymity)
        const trustValues = [
          ...usedResponses.map(r => r.responses?.u_trust_anonymity),
          ...notUsedResponses.map(r => r.responses?.nu_trust_anonymity)
        ].filter(v => v !== undefined && v !== null) as number[];

        // Usage rate
        const usageRate = total > 0 ? (used / total) * 100 : 0;

        // Impact (satisfaction)
        const impactValues = usedResponses
          .map(r => r.responses?.u_impact_satisfaction)
          .filter(v => v !== undefined && v !== null) as number[];

        trends.push({
          date,
          awareness: Number(calculateAverage(awarenessValues).toFixed(2)),
          trust: Number(calculateAverage(trustValues).toFixed(2)),
          usage: Number(usageRate.toFixed(1)),
          impact: Number(calculateAverage(impactValues).toFixed(2)),
        });
      }

      setTrendData(trends);
      setCategoryTrend(catTrends);
    } catch (error) {
      console.error('Error fetching trend data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const getTrendIcon = (current: number, previous: number) => {
    if (!previous) return <Minus className="h-4 w-4 text-gray-400" />;
    const diff = current - previous;
    if (diff > 0.5) return <TrendingUp className="h-4 w-4 text-green-600" />;
    if (diff < -0.5) return <TrendingDown className="h-4 w-4 text-red-600" />;
    return <Minus className="h-4 w-4 text-gray-400" />;
  };

  const getTrendText = (current: number, previous: number) => {
    if (!previous) return '—';
    const diff = current - previous;
    return diff > 0 ? `+${diff.toFixed(1)}` : diff.toFixed(1);
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  const COLORS = {
    primary: 'hsl(var(--primary))',
    success: 'hsl(var(--success))',
    warning: 'hsl(var(--warning))',
    destructive: 'hsl(var(--destructive))',
    chart5: 'hsl(var(--chart-5))',
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  const availableComparisons = audits.filter(a => a.id !== selectedAuditId);

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
          <h2 className="text-2xl font-bold mb-2">Időbeli Trendek</h2>
          <p className="text-muted-foreground">Változások és trendek követése az idő során</p>
        </div>
        <div className="flex items-center gap-4">
          <Select value={selectedAuditId} onValueChange={onAuditChange}>
            <SelectTrigger className="w-[280px]">
              <SelectValue placeholder="Válassz felmérést" />
            </SelectTrigger>
            <SelectContent>
              {audits.map(audit => (
                <SelectItem key={audit.id} value={audit.id}>
                  {formatAuditName(audit)}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          {availableComparisons.length > 0 && (
            <Select value={compareAuditId} onValueChange={setCompareAuditId}>
              <SelectTrigger className="w-[280px]">
                <SelectValue placeholder="Összehasonlítás..." />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="none">Nincs összehasonlítás</SelectItem>
                {availableComparisons.map(audit => (
                  <SelectItem key={audit.id} value={audit.id}>
                    {formatAuditName(audit)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          )}
        </div>
      </div>

      {trendData.length === 0 ? (
        <Card>
          <CardContent className="pt-6">
            <p className="text-center text-muted-foreground py-12">
              A kiválasztott felméréshez még nincs válasz
            </p>
          </CardContent>
        </Card>
      ) : (
        <>
          {/* Category Distribution Trend */}
          <Card id="trend-category-distribution">
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Kategória megoszlás időbeli alakulása</CardTitle>
                <CardDescription>Az EAP program ismerete és használata (%)</CardDescription>
              </div>
              <Button
                variant="outline"
                size="icon"
                onClick={() => exportCardToPNG('trend-category-distribution', 'kategoria-megoszlas-trend')}
              >
                <Download className="h-4 w-4" />
              </Button>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={350}>
                <BarChart data={categoryTrend}>
                  <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                  <XAxis 
                    dataKey="date" 
                    stroke="hsl(var(--muted-foreground))"
                    tick={{ fill: 'hsl(var(--foreground))' }}
                  />
                  <YAxis 
                    stroke="hsl(var(--muted-foreground))"
                    tick={{ fill: 'hsl(var(--foreground))' }}
                  />
                  <Tooltip 
                    contentStyle={{ 
                      backgroundColor: 'hsl(var(--card))',
                      borderColor: 'hsl(var(--border))',
                      color: 'hsl(var(--foreground))'
                    }}
                  />
                  <Legend wrapperStyle={{ color: 'hsl(var(--foreground))' }} />
                  <Bar dataKey="notKnew" name="Nem ismerte" fill={COLORS.destructive} />
                  <Bar dataKey="notUsed" name="Ismerte, de nem használta" fill={COLORS.warning} />
                  <Bar dataKey="used" name="Használta" fill={COLORS.success} />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          {/* Main Metrics Trends */}
          <Card id="trend-main-metrics">
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Fő mutatók alakulása</CardTitle>
                <CardDescription>Ismertség, bizalom, használat és elégedettség változása</CardDescription>
              </div>
              <Button
                variant="outline"
                size="icon"
                onClick={() => exportCardToPNG('trend-main-metrics', 'fo-mutatok-trend')}
              >
                <Download className="h-4 w-4" />
              </Button>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={400}>
                <LineChart data={trendData}>
                  <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                  <XAxis 
                    dataKey="date" 
                    stroke="hsl(var(--muted-foreground))"
                    tick={{ fill: 'hsl(var(--foreground))' }}
                  />
                  <YAxis 
                    stroke="hsl(var(--muted-foreground))"
                    tick={{ fill: 'hsl(var(--foreground))' }}
                  />
                  <Tooltip 
                    contentStyle={{ 
                      backgroundColor: 'hsl(var(--card))',
                      borderColor: 'hsl(var(--border))',
                      color: 'hsl(var(--foreground))'
                    }}
                  />
                  <Legend wrapperStyle={{ color: 'hsl(var(--foreground))' }} />
                  <Line 
                    type="monotone" 
                    dataKey="awareness" 
                    name="Ismertség (1-5)" 
                    stroke={COLORS.primary}
                    strokeWidth={3}
                    dot={{ fill: COLORS.primary, r: 5 }}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="trust" 
                    name="Bizalom (1-5)" 
                    stroke={COLORS.success}
                    strokeWidth={3}
                    dot={{ fill: COLORS.success, r: 5 }}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="usage" 
                    name="Használati arány (%)" 
                    stroke={COLORS.warning}
                    strokeWidth={3}
                    dot={{ fill: COLORS.warning, r: 5 }}
                  />
                  <Line 
                    type="monotone" 
                    dataKey="impact" 
                    name="Elégedettség (1-5)" 
                    stroke={COLORS.chart5}
                    strokeWidth={3}
                    dot={{ fill: COLORS.chart5, r: 5 }}
                  />
                </LineChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          {/* Trend Summary Cards */}
          {trendData.length > 1 && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <Card>
                <CardHeader className="pb-3">
                  <CardDescription className="text-xs">Átlagos Ismertség</CardDescription>
                  <CardTitle className="text-3xl font-bold">
                    {trendData[trendData.length - 1].awareness}
                    <span className="text-sm text-muted-foreground ml-1">/5</span>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="flex items-center gap-2">
                    {getTrendIcon(
                      trendData[trendData.length - 1].awareness,
                      trendData[trendData.length - 2].awareness
                    )}
                    <span className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].awareness,
                        trendData[trendData.length - 2].awareness
                      )}
                    </span>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="pb-3">
                  <CardDescription className="text-xs">Átlagos Bizalom</CardDescription>
                  <CardTitle className="text-3xl font-bold">
                    {trendData[trendData.length - 1].trust}
                    <span className="text-sm text-muted-foreground ml-1">/5</span>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="flex items-center gap-2">
                    {getTrendIcon(
                      trendData[trendData.length - 1].trust,
                      trendData[trendData.length - 2].trust
                    )}
                    <span className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].trust,
                        trendData[trendData.length - 2].trust
                      )}
                    </span>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="pb-3">
                  <CardDescription className="text-xs">Használati Arány</CardDescription>
                  <CardTitle className="text-3xl font-bold">
                    {trendData[trendData.length - 1].usage}
                    <span className="text-sm text-muted-foreground ml-1">%</span>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="flex items-center gap-2">
                    {getTrendIcon(
                      trendData[trendData.length - 1].usage,
                      trendData[trendData.length - 2].usage
                    )}
                    <span className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].usage,
                        trendData[trendData.length - 2].usage
                      )}
                    </span>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="pb-3">
                  <CardDescription className="text-xs">Átlagos Elégedettség</CardDescription>
                  <CardTitle className="text-3xl font-bold">
                    {trendData[trendData.length - 1].impact}
                    <span className="text-sm text-muted-foreground ml-1">/5</span>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="flex items-center gap-2">
                    {getTrendIcon(
                      trendData[trendData.length - 1].impact,
                      trendData[trendData.length - 2].impact
                    )}
                    <span className="text-sm text-muted-foreground">
                      {getTrendText(
                        trendData[trendData.length - 1].impact,
                        trendData[trendData.length - 2].impact
                      )}
                    </span>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default Trends;
