import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { toast } from 'sonner';
import { TrendingUp, TrendingDown, Minus } from 'lucide-react';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

type Audit = StandardAudit;

interface ComparisonData {
  metric: string;
  first: number;
  second: number;
}

const Compare = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [showAllAudits, setShowAllAudits] = useState(false);
  const [firstAuditId, setFirstAuditId] = useState<string>('');
  const [secondAuditId, setSecondAuditId] = useState<string>('');
  const [comparisonData, setComparisonData] = useState<ComparisonData[]>([]);
  const [categoryData, setCategoryData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (firstAuditId && secondAuditId && firstAuditId !== secondAuditId) {
      fetchComparisonData();
    }
  }, [firstAuditId, secondAuditId]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select(AUDIT_SELECT_FIELDS)
        .eq('is_active', true)
        .order('start_date', { ascending: true });

      if (data && data.length > 0) {
        setAudits(data);
        if (data.length >= 2) {
          setFirstAuditId(data[0].id);
          setSecondAuditId(data[data.length - 1].id);
        }
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt az auditek betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const calculateAverage = (values: number[]): number => {
    if (values.length === 0) return 0;
    const sum = values.reduce((acc, val) => acc + val, 0);
    return sum / values.length;
  };

  const fetchComparisonData = async () => {
    try {
      const [data1, data2] = await Promise.all([
        supabase.from('audit_responses').select('responses, employee_metadata').eq('audit_id', firstAuditId),
        supabase.from('audit_responses').select('responses, employee_metadata').eq('audit_id', secondAuditId)
      ]);

      if (data1.error) throw data1.error;
      if (data2.error) throw data2.error;

      const responses1 = data1.data || [];
      const responses2 = data2.data || [];

      // Calculate metrics for first audit
      const used1 = responses1.filter(r => r.employee_metadata?.branch === 'used');
      const notUsed1 = responses1.filter(r => r.employee_metadata?.branch === 'not_used');
      const notKnew1 = responses1.filter(r => r.employee_metadata?.branch === 'redirect');
      
      const awareness1 = [
        ...used1.map(r => r.responses?.u_awareness_understanding),
        ...notUsed1.map(r => r.responses?.nu_awareness_understanding)
      ].filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

      const trust1 = [
        ...used1.map(r => r.responses?.u_trust_anonymity),
        ...notUsed1.map(r => r.responses?.nu_trust_anonymity)
      ].filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

      const usage1 = responses1.length > 0 ? (used1.length / responses1.length) * 100 : 0;

      const impact1 = used1
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

      // Calculate metrics for second audit
      const used2 = responses2.filter(r => r.employee_metadata?.branch === 'used');
      const notUsed2 = responses2.filter(r => r.employee_metadata?.branch === 'not_used');
      const notKnew2 = responses2.filter(r => r.employee_metadata?.branch === 'redirect');
      
      const awareness2 = [
        ...used2.map(r => r.responses?.u_awareness_understanding),
        ...notUsed2.map(r => r.responses?.nu_awareness_understanding)
      ].filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

      const trust2 = [
        ...used2.map(r => r.responses?.u_trust_anonymity),
        ...notUsed2.map(r => r.responses?.nu_trust_anonymity)
      ].filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

      const usage2 = responses2.length > 0 ? (used2.length / responses2.length) * 100 : 0;

      const impact2 = used2
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

      // Set comparison data
      setComparisonData([
        {
          metric: 'Ismertség',
          first: Number(calculateAverage(awareness1).toFixed(2)),
          second: Number(calculateAverage(awareness2).toFixed(2))
        },
        {
          metric: 'Bizalom',
          first: Number(calculateAverage(trust1).toFixed(2)),
          second: Number(calculateAverage(trust2).toFixed(2))
        },
        {
          metric: 'Használat (%)',
          first: Number(usage1.toFixed(1)),
          second: Number(usage2.toFixed(1))
        },
        {
          metric: 'Elégedettség',
          first: Number(calculateAverage(impact1).toFixed(2)),
          second: Number(calculateAverage(impact2).toFixed(2))
        }
      ]);

      // Category distribution
      const total1 = responses1.length;
      const total2 = responses2.length;
      
      setCategoryData([
        {
          category: 'Nem ismerte',
          first: total1 > 0 ? Number(((notKnew1.length / total1) * 100).toFixed(1)) : 0,
          second: total2 > 0 ? Number(((notKnew2.length / total2) * 100).toFixed(1)) : 0
        },
        {
          category: 'Ismerte, de nem használta',
          first: total1 > 0 ? Number(((notUsed1.length / total1) * 100).toFixed(1)) : 0,
          second: total2 > 0 ? Number(((notUsed2.length / total2) * 100).toFixed(1)) : 0
        },
        {
          category: 'Használta',
          first: total1 > 0 ? Number(((used1.length / total1) * 100).toFixed(1)) : 0,
          second: total2 > 0 ? Number(((used2.length / total2) * 100).toFixed(1)) : 0
        }
      ]);

    } catch (error) {
      console.error('Error fetching comparison data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    }
  };

  const getTrendIcon = (current: number, previous: number) => {
    const diff = current - previous;
    if (diff > 0.5) return <TrendingUp className="h-8 w-8" style={{ color: '#3572ef' }} />;
    if (diff < -0.5) return <TrendingDown className="h-8 w-8 text-red-600" />;
    return <Minus className="h-8 w-8" style={{ color: '#3572ef' }} />;
  };

  const getTrendText = (current: number, previous: number) => {
    const diff = current - previous;
    return diff > 0 ? `+${diff.toFixed(1)}` : diff.toFixed(1);
  };

  const getTrendDescription = (current: number, previous: number, metricName: string) => {
    const diff = current - previous;
    
    if (diff > 0.5) {
      return `${metricName} növekedett`;
    } else if (diff < -0.5) {
      return `${metricName} csökkent`;
    } else {
      return `${metricName} stagnált`;
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  const firstAudit = audits.find(a => a.id === firstAuditId);
  const secondAudit = audits.find(a => a.id === secondAuditId);

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Összehasonlítás</h2>
        <p className="text-muted-foreground">Két felmérés összehasonlítása</p>
      </div>

      {audits.length === 0 ? (
        <Card>
          <CardContent className="pt-6">
            <p className="text-center text-muted-foreground">Még nincs aktív felmérés</p>
          </CardContent>
        </Card>
      ) : audits.length < 2 ? (
        <Card>
          <CardContent className="pt-6">
            <p className="text-center text-muted-foreground">Legalább két felmérés szükséges az összehasonlításhoz</p>
          </CardContent>
        </Card>
      ) : (
        <>
          <Card>
            <CardHeader>
              <CardTitle>Felmérések kiválasztása</CardTitle>
              <CardDescription>Válaszd ki, mely felméréseket szeretnéd összehasonlítani</CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="first-audit">
                    Első felmérés
                  </Label>
                  <Select
                    value={firstAuditId}
                    onValueChange={setFirstAuditId}
                  >
                    <SelectTrigger id="first-audit">
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
                </div>

                <div className="space-y-2">
                  <Label htmlFor="second-audit">
                    Második felmérés
                  </Label>
                  <Select
                    value={secondAuditId}
                    onValueChange={setSecondAuditId}
                  >
                    <SelectTrigger id="second-audit">
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
                </div>
              </div>
            </CardContent>
          </Card>

          {firstAuditId === secondAuditId ? (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-muted-foreground">Válassz két különböző felmérést</p>
              </CardContent>
            </Card>
          ) : comparisonData.length === 0 ? (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-muted-foreground">A kiválasztott felmérésekhez még nincs válasz</p>
              </CardContent>
            </Card>
          ) : (
            <>
              {/* Comparison Summary Cards */}
              <Card>
                <CardHeader>
                  <CardTitle>Fő mutatók összehasonlítása</CardTitle>
                  <CardDescription>Az első és második felmérés eredményeinek közvetlen összehasonlítása</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {comparisonData.map((item, idx) => {
                      const isUsage = item.metric === 'Használat (%)';
                      const diff = item.second - item.first;
                      return (
                        <div key={idx} className="space-y-3">
                          <div className="text-sm font-medium text-muted-foreground">
                            {item.metric.replace(' (%)', '')}
                          </div>
                          <div className="flex items-baseline gap-4">
                            <div>
                              <div className="text-xs text-muted-foreground mb-1">Első felmérés</div>
                              <div className="text-2xl font-bold">
                                {item.first}{isUsage ? '%' : ''}
                              </div>
                            </div>
                            <div className="flex items-center">
                              {getTrendIcon(item.second, item.first)}
                            </div>
                            <div>
                              <div className="text-xs text-muted-foreground mb-1">Második felmérés</div>
                              <div className="text-2xl font-bold">
                                {item.second}{isUsage ? '%' : ''}
                              </div>
                            </div>
                          </div>
                          <div className="text-sm">
                            <span className={diff > 0 ? 'text-green-600' : diff < 0 ? 'text-red-600' : 'text-muted-foreground'}>
                              {diff > 0 ? '+' : ''}{diff.toFixed(1)}{isUsage ? '%' : ''} különbség
                            </span>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                </CardContent>
              </Card>

              {/* Category Distribution Comparison */}
              {categoryData.length > 0 && (
                <Card>
                  <CardHeader>
                    <CardTitle>Kategória megoszlás összehasonlítása</CardTitle>
                    <CardDescription>Az EAP program ismerete és használata (%)</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <ResponsiveContainer width="100%" height={300}>
                      <BarChart data={categoryData}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="category" />
                        <YAxis />
                        <Tooltip />
                        <Legend />
                        <Bar dataKey="first" name={firstAudit ? formatAuditName(firstAudit) : 'Első'} fill="#3572ef" radius={[8, 8, 0, 0]} />
                        <Bar dataKey="second" name={secondAudit ? formatAuditName(secondAudit) : 'Második'} fill="#3abef9" radius={[8, 8, 0, 0]} />
                      </BarChart>
                    </ResponsiveContainer>
                  </CardContent>
                </Card>
              )}

              {/* Main Metrics Comparison */}
              <Card>
                <CardHeader>
                  <CardTitle>Részletes összehasonlítás</CardTitle>
                  <CardDescription>Fő mutatók értékeinek összehasonlítása</CardDescription>
                </CardHeader>
                <CardContent>
                  <ResponsiveContainer width="100%" height={350}>
                    <BarChart data={comparisonData}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="metric" />
                      <YAxis />
                      <Tooltip />
                      <Legend />
                      <Bar dataKey="first" name={firstAudit ? formatAuditName(firstAudit) : 'Első'} fill="#3572ef" radius={[8, 8, 0, 0]} />
                      <Bar dataKey="second" name={secondAudit ? formatAuditName(secondAudit) : 'Második'} fill="#3abef9" radius={[8, 8, 0, 0]} />
                    </BarChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>
            </>
          )}
        </>
      )}
    </div>
  );
};

export default Compare;
