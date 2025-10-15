import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar } from 'recharts';
import { toast } from 'sonner';
import { TrendingUp, TrendingDown, Minus } from 'lucide-react';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

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

const Trends = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [showAllAudits, setShowAllAudits] = useState(true);
  const [firstAuditId, setFirstAuditId] = useState<string>('');
  const [secondAuditId, setSecondAuditId] = useState<string>('');
  const [selectedAuditIds, setSelectedAuditIds] = useState<string[]>([]);
  const [trendData, setTrendData] = useState<TrendData[]>([]);
  const [categoryTrend, setCategoryTrend] = useState<CategoryTrend[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (showAllAudits) {
      setSelectedAuditIds(audits.map(a => a.id));
    } else {
      const ids = [firstAuditId, secondAuditId].filter(Boolean);
      setSelectedAuditIds(ids);
    }
  }, [showAllAudits, firstAuditId, secondAuditId, audits]);

  useEffect(() => {
    if (selectedAuditIds.length > 0) {
      fetchTrendData(selectedAuditIds);
    }
  }, [selectedAuditIds]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select(AUDIT_SELECT_FIELDS)
        .eq('is_active', true)
        .order('start_date', { ascending: true });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditIds(data.map(a => a.id));
        if (data.length >= 2) {
          setFirstAuditId(data[0].id);
          setSecondAuditId(data[1].id);
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

  const fetchTrendData = async (auditIds: string[]) => {
    try {
      const trendsWithDate: Array<TrendData & { sortDate: Date }> = [];
      const catTrendsWithDate: Array<CategoryTrend & { sortDate: Date }> = [];

      for (const auditId of auditIds) {
        const audit = audits.find(a => a.id === auditId);
        if (!audit) continue;

        const { data, error } = await supabase
          .from('audit_responses')
          .select('responses, employee_metadata')
          .eq('audit_id', auditId);

        if (error) throw error;
        if (!data || data.length === 0) continue;

        const sortDate = new Date(audit.start_date);
        const displayDate = sortDate.toLocaleDateString('hu-HU', { 
          year: 'numeric', 
          month: 'short',
          day: 'numeric'
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
        catTrendsWithDate.push({
          date: displayDate,
          sortDate,
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
        ].filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

        // Trust (anonymity)
        const trustValues = [
          ...usedResponses.map(r => r.responses?.u_trust_anonymity),
          ...notUsedResponses.map(r => r.responses?.nu_trust_anonymity)
        ].filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

        // Usage rate
        const usageRate = total > 0 ? (used / total) * 100 : 0;

        // Impact (satisfaction)
        const impactValues = usedResponses
          .map(r => r.responses?.u_impact_satisfaction)
          .filter(v => v !== undefined && v !== null && typeof v === 'number') as number[];

        trendsWithDate.push({
          date: displayDate,
          sortDate,
          awareness: awarenessValues.length > 0 ? Number(calculateAverage(awarenessValues).toFixed(2)) : 0,
          trust: trustValues.length > 0 ? Number(calculateAverage(trustValues).toFixed(2)) : 0,
          usage: Number(usageRate.toFixed(1)),
          impact: impactValues.length > 0 ? Number(calculateAverage(impactValues).toFixed(2)) : 0,
        });
      }

      // Sort by actual date
      const sortedTrends = trendsWithDate
        .sort((a, b) => a.sortDate.getTime() - b.sortDate.getTime())
        .map(({ sortDate, ...rest }) => rest);

      const sortedCatTrends = catTrendsWithDate
        .sort((a, b) => a.sortDate.getTime() - b.sortDate.getTime())
        .map(({ sortDate, ...rest }) => rest);

      setTrendData(sortedTrends);
      setCategoryTrend(sortedCatTrends);
      
      console.log('Trend data loaded:', {
        count: sortedTrends.length,
        data: sortedTrends,
        lastItem: sortedTrends[sortedTrends.length - 1],
        secondLastItem: sortedTrends[sortedTrends.length - 2]
      });
    } catch (error) {
      console.error('Error fetching trend data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    }
  };

  const getTrendIcon = (current: number, previous: number, metric?: string) => {
    if (!previous) return <Minus className="h-8 w-8" style={{ color: '#050c9c' }} />;
    const diff = current - previous;
    
    if (metric === 'usage') {
      if (diff > 0.5) return <TrendingUp className="h-8 w-8" style={{ color: '#3572ef' }} />;
      if (diff < -0.5) return <TrendingDown className="h-8 w-8" style={{ color: '#3abef9' }} />;
      return <Minus className="h-8 w-8" style={{ color: '#050c9c' }} />;
    }
    
    if (diff > 0.5) return <TrendingUp className="h-8 w-8 text-green-600" />;
    if (diff < -0.5) return <TrendingDown className="h-8 w-8 text-red-600" />;
    return <Minus className="h-8 w-8" style={{ color: '#050c9c' }} />;
  };

  const getTrendText = (current: number, previous: number) => {
    if (!previous) return '—';
    const diff = current - previous;
    return diff > 0 ? `+${diff.toFixed(1)}` : diff.toFixed(1);
  };

  const getTrendDescription = (current: number, previous: number, metricName: string) => {
    if (!previous) return `Nincs előző adat az összehasonlításhoz`;
    const diff = current - previous;
    
    if (diff > 0.5) {
      return `${metricName} növekvő tendenciát mutat`;
    } else if (diff < -0.5) {
      return `${metricName} csökkenő tendenciát mutat`;
    } else {
      return `${metricName} stagnál, nincs jelentős változás`;
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Időbeli Trendek</h2>
        <p className="text-muted-foreground">Változások és trendek követése az idő során</p>
      </div>

      {audits.length === 0 ? (
        <Card>
          <CardContent className="pt-6">
            <p className="text-center text-muted-foreground">Még nincs aktív felmérés</p>
          </CardContent>
        </Card>
      ) : (
        <>
          <Card>
            <CardHeader>
              <CardTitle>Felmérések kiválasztása</CardTitle>
              <CardDescription>Válaszd ki, mely felmérések adatait szeretnéd összehasonlítani</CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="flex items-center gap-3">
                <Switch
                  id="show-all"
                  checked={showAllAudits}
                  onCheckedChange={setShowAllAudits}
                />
                <Label htmlFor="show-all" className="text-sm font-medium cursor-pointer">
                  Összes felmérés kiválasztása
                </Label>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="first-audit" className={showAllAudits ? "text-muted-foreground" : ""}>
                    Ezt a felmérést szeretném
                  </Label>
                  <Select
                    value={firstAuditId}
                    onValueChange={setFirstAuditId}
                    disabled={showAllAudits}
                  >
                    <SelectTrigger id="first-audit" className={showAllAudits ? "opacity-50" : ""}>
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
                  <Label htmlFor="second-audit" className={showAllAudits ? "text-muted-foreground" : ""}>
                    Ezzel a felméréssel összehasonlítani
                  </Label>
                  <Select
                    value={secondAuditId}
                    onValueChange={setSecondAuditId}
                    disabled={showAllAudits}
                  >
                    <SelectTrigger id="second-audit" className={showAllAudits ? "opacity-50" : ""}>
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

          {selectedAuditIds.length === 0 ? (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-muted-foreground">Válassz ki legalább egy felmérést</p>
              </CardContent>
            </Card>
          ) : trendData.length === 0 ? (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-muted-foreground">A kiválasztott felmérésekhez még nincs válasz</p>
              </CardContent>
            </Card>
          ) : (
            <>
              {/* Trend Summary Cards - MOVED TO TOP */}
              {(() => {
                const validTrends = trendData.filter(t => 
                  t.awareness > 0 || t.trust > 0 || t.usage > 0 || t.impact > 0
                );
                
                if (validTrends.length < 2) return null;
                
                const lastTrend = validTrends[validTrends.length - 1];
                const secondLastTrend = validTrends[validTrends.length - 2];
                
                return (
                <Card className="mb-6">
                  <CardHeader>
                    <CardTitle>Fő mutatók trendje</CardTitle>
                    <CardDescription>Az előző felméréshez képesti változások</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                      <div className="space-y-2">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                          <span className="font-medium">Ismertség</span>
                        </div>
                        <div className="flex items-center gap-3">
                          {getTrendIcon(lastTrend.awareness, secondLastTrend.awareness)}
                          <div className="text-4xl font-bold">
                            {getTrendText(lastTrend.awareness, secondLastTrend.awareness)}
                          </div>
                        </div>
                        <p className="text-xs text-muted-foreground">
                          {getTrendDescription(lastTrend.awareness, secondLastTrend.awareness, "Az ismertség")}
                        </p>
                        <div className="text-xs text-muted-foreground">
                          Aktuális érték: {lastTrend.awareness} (1-5 skála)
                        </div>
                      </div>

                      <div className="space-y-2">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                          <span className="font-medium">Bizalom</span>
                        </div>
                        <div className="flex items-center gap-3">
                          {getTrendIcon(lastTrend.trust, secondLastTrend.trust)}
                          <div className="text-4xl font-bold">
                            {getTrendText(lastTrend.trust, secondLastTrend.trust)}
                          </div>
                        </div>
                        <p className="text-xs text-muted-foreground">
                          {getTrendDescription(lastTrend.trust, secondLastTrend.trust, "A bizalom")}
                        </p>
                        <div className="text-xs text-muted-foreground">
                          Aktuális érték: {lastTrend.trust} (1-5 skála)
                        </div>
                      </div>

                      <div className="space-y-2">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                          <span className="font-medium">Használat</span>
                        </div>
                        <div className="flex items-center gap-3">
                          {getTrendIcon(lastTrend.usage, secondLastTrend.usage, 'usage')}
                          <div className="text-4xl font-bold">
                            {getTrendText(lastTrend.usage, secondLastTrend.usage)}%
                          </div>
                        </div>
                        <p className="text-xs text-muted-foreground">
                          {getTrendDescription(lastTrend.usage, secondLastTrend.usage, "A használat")}
                        </p>
                        <div className="text-xs text-muted-foreground">
                          Aktuális arány: {lastTrend.usage}%
                        </div>
                      </div>

                      <div className="space-y-2">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                          <span className="font-medium">Elégedettség</span>
                        </div>
                        <div className="flex items-center gap-3">
                          {getTrendIcon(lastTrend.impact, secondLastTrend.impact)}
                          <div className="text-4xl font-bold">
                            {getTrendText(lastTrend.impact, secondLastTrend.impact)}
                          </div>
                        </div>
                        <p className="text-xs text-muted-foreground">
                          {getTrendDescription(lastTrend.impact, secondLastTrend.impact, "Az elégedettség")}
                        </p>
                        <div className="text-xs text-muted-foreground">
                          Aktuális érték: {lastTrend.impact} (1-5 skála)
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
                );
              })()}

              {/* Category Distribution Trend */}
              {(() => {
                const validCatTrends = categoryTrend.filter(t => 
                  t.notKnew > 0 || t.notUsed > 0 || t.used > 0
                );
                
                if (validCatTrends.length === 0) return null;
                
                return (
              <Card>
                <CardHeader>
                  <CardTitle>Kategória megoszlás időbeli alakulása</CardTitle>
                  <CardDescription>Az EAP program ismerete és használata (%)</CardDescription>
                </CardHeader>
                <CardContent>
                  <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={validCatTrends}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="date" />
                      <YAxis />
                      <Tooltip />
                      <Legend />
                      <Bar dataKey="notKnew" name="Nem ismerte" fill="#050c9c" radius={[8, 8, 0, 0]} />
                      <Bar dataKey="notUsed" name="Ismerte, de nem használta" fill="#3572ef" radius={[8, 8, 0, 0]} />
                      <Bar dataKey="used" name="Használta" fill="#3abef9" radius={[8, 8, 0, 0]} />
                    </BarChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>
                );
              })()}

              {/* Main Metrics Trends */}
              {(() => {
                const validTrends = trendData.filter(t => 
                  t.awareness > 0 || t.trust > 0 || t.usage > 0 || t.impact > 0
                );
                
                if (validTrends.length === 0) return null;
                
                return (
              <Card>
                <CardHeader>
                  <CardTitle>Ismertség, bizalom és elégedettség alakulása</CardTitle>
                  <CardDescription>1-5 skálán mért mutatók változása</CardDescription>
                </CardHeader>
                <CardContent>
                  {(() => {
                    if (validTrends.length < 2) return null;
                    const lastTrend = validTrends[validTrends.length - 1];
                    const secondLastTrend = validTrends[validTrends.length - 2];
                    
                    return (
                      <div className="mb-6 p-4 bg-muted/50 rounded-lg space-y-2">
                        <p className="text-sm font-medium">Trend összefoglaló:</p>
                        <ul className="text-sm text-muted-foreground space-y-1">
                          <li>• {getTrendDescription(lastTrend.awareness, secondLastTrend.awareness, "Az ismertség")}</li>
                          <li>• {getTrendDescription(lastTrend.trust, secondLastTrend.trust, "A bizalom")}</li>
                          <li>• {getTrendDescription(lastTrend.impact, secondLastTrend.impact, "Az elégedettség")}</li>
                        </ul>
                      </div>
                    );
                  })()}
                  <ResponsiveContainer width="100%" height={350}>
                    <LineChart data={validTrends}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="date" />
                      <YAxis domain={[0, 5]} />
                      <Tooltip />
                      <Legend />
                      <Line 
                        type="monotone" 
                        dataKey="awareness" 
                        name="Ismertség" 
                        stroke="#3572ef" 
                        strokeWidth={2}
                      />
                      <Line 
                        type="monotone" 
                        dataKey="trust" 
                        name="Bizalom" 
                        stroke="#3abef9" 
                        strokeWidth={2}
                      />
                      <Line 
                        type="monotone" 
                        dataKey="impact" 
                        name="Elégedettség" 
                        stroke="#050c9c" 
                        strokeWidth={2}
                        strokeDasharray="5 5"
                      />
                    </LineChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>
                );
              })()}

              {/* Usage Trend */}
              {(() => {
                const validTrends = trendData.filter(t => 
                  t.awareness > 0 || t.trust > 0 || t.usage > 0 || t.impact > 0
                );
                
                if (validTrends.length === 0) return null;
                
                return (
              <Card>
                <CardHeader>
                  <CardTitle>Használati arány alakulása</CardTitle>
                  <CardDescription>Hány százalék használta már az EAP programot</CardDescription>
                </CardHeader>
                <CardContent>
                  {(() => {
                    if (validTrends.length < 2) return null;
                    const lastTrend = validTrends[validTrends.length - 1];
                    const secondLastTrend = validTrends[validTrends.length - 2];
                    
                    return (
                      <div className="mb-6 p-4 bg-muted/50 rounded-lg">
                        <p className="text-sm font-medium">Trend összefoglaló:</p>
                        <p className="text-sm text-muted-foreground mt-1">
                          • {getTrendDescription(lastTrend.usage, secondLastTrend.usage, "A használat")}
                        </p>
                      </div>
                    );
                  })()}
                  <ResponsiveContainer width="100%" height={300}>
                    <LineChart data={validTrends}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="date" />
                      <YAxis domain={[0, 100]} label={{ value: 'Használati arány (%)', angle: -90, position: 'insideLeft' }} />
                      <Tooltip />
                      <Legend />
                      <Line 
                        type="monotone" 
                        dataKey="usage" 
                        name="Használati arány" 
                        stroke="#3572ef" 
                        strokeWidth={2}
                      />
                    </LineChart>
                  </ResponsiveContainer>
                </CardContent>
              </Card>
                );
              })()}
            </>
          )}
        </>
      )}
    </div>
  );
};

export default Trends;
