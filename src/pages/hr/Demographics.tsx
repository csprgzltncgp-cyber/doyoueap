import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { GaugeChart } from '@/components/ui/gauge-chart';

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

interface DemographicStats {
  total: number;
  notKnew: number;
  notUsed: number;
  used: number;
}

interface ComparisonData {
  category: string;
  overall: number;
  filtered: number;
}

const Demographics = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [selectedGender, setSelectedGender] = useState<string>('all');
  const [selectedAge, setSelectedAge] = useState<string>('all');
  const [stats, setStats] = useState<DemographicStats>({ total: 0, notKnew: 0, notUsed: 0, used: 0 });
  const [categoryData, setCategoryData] = useState<any[]>([]);
  const [awarenessComparison, setAwarenessComparison] = useState<ComparisonData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchDemographicData(selectedAuditId, selectedGender, selectedAge);
    }
  }, [selectedAuditId, selectedGender, selectedAge]);

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

  const fetchDemographicData = async (auditId: string, gender: string, age: string) => {
    try {
      const { data: allData, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId);

      if (error) throw error;

      if (!allData || allData.length === 0) {
        setStats({ total: 0, notKnew: 0, notUsed: 0, used: 0 });
        setCategoryData([]);
        setAwarenessComparison([]);
        return;
      }

      // Filter by demographics
      const filteredData = allData.filter(r => {
        const genderMatch = gender === 'all' || r.responses?.gender === gender;
        const ageMatch = age === 'all' || r.responses?.age === age;
        return genderMatch && ageMatch;
      });

      // Calculate category distribution
      let notKnew = 0, notUsed = 0, used = 0;
      filteredData.forEach(r => {
        const branch = r.employee_metadata?.branch;
        if (branch === 'redirect') notKnew++;
        else if (branch === 'not_used') notUsed++;
        else if (branch === 'used') used++;
      });

      const total = filteredData.length;
      setStats({ total, notKnew, notUsed, used });

      // Category distribution chart
      setCategoryData([
        { 
          name: 'Nem tudott róla', 
          value: notKnew, 
          percentage: total > 0 ? ((notKnew / total) * 100).toFixed(1) : 0 
        },
        { 
          name: 'Nem használta', 
          value: notUsed, 
          percentage: total > 0 ? ((notUsed / total) * 100).toFixed(1) : 0 
        },
        { 
          name: 'Használta', 
          value: used, 
          percentage: total > 0 ? ((used / total) * 100).toFixed(1) : 0 
        },
      ]);

      // Awareness comparison (overall vs filtered)
      const allUsed = allData.filter(r => r.employee_metadata?.branch === 'used');
      const filteredUsed = filteredData.filter(r => r.employee_metadata?.branch === 'used');

      const allNotUsed = allData.filter(r => r.employee_metadata?.branch === 'not_used');
      const filteredNotUsed = filteredData.filter(r => r.employee_metadata?.branch === 'not_used');

      // Calculate awareness metrics
      const allUnderstanding = [
        ...allUsed.map(r => r.responses?.u_awareness_understanding).filter(v => v),
        ...allNotUsed.map(r => r.responses?.nu_awareness_understanding).filter(v => v)
      ] as number[];

      const filteredUnderstanding = [
        ...filteredUsed.map(r => r.responses?.u_awareness_understanding).filter(v => v),
        ...filteredNotUsed.map(r => r.responses?.nu_awareness_understanding).filter(v => v)
      ] as number[];

      const allTrust = [
        ...allUsed.map(r => r.responses?.u_trust_anonymity).filter(v => v),
        ...allNotUsed.map(r => r.responses?.nu_trust_anonymity).filter(v => v)
      ] as number[];

      const filteredTrust = [
        ...filteredUsed.map(r => r.responses?.u_trust_anonymity).filter(v => v),
        ...filteredNotUsed.map(r => r.responses?.nu_trust_anonymity).filter(v => v)
      ] as number[];

      const allSatisfaction = allUsed
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v) as number[];

      const filteredSatisfaction = filteredUsed
        .map(r => r.responses?.u_impact_satisfaction)
        .filter(v => v) as number[];

      setAwarenessComparison([
        {
          category: 'Szolgáltatás megértése',
          overall: Number(calculateAverage(allUnderstanding).toFixed(2)),
          filtered: Number(calculateAverage(filteredUnderstanding).toFixed(2)),
        },
        {
          category: 'Anonimitásba bizalom',
          overall: Number(calculateAverage(allTrust).toFixed(2)),
          filtered: Number(calculateAverage(filteredTrust).toFixed(2)),
        },
        {
          category: 'Elégedettség',
          overall: Number(calculateAverage(allSatisfaction).toFixed(2)),
          filtered: Number(calculateAverage(filteredSatisfaction).toFixed(2)),
        },
      ]);

    } catch (error) {
      console.error('Error fetching demographic data:', error);
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

  return (
    <div className="p-8 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Demográfiai Bontás</h1>
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

      <Card className="bg-blue-50 dark:bg-blue-950">
        <CardHeader>
          <CardTitle>Szűrők</CardTitle>
          <CardDescription>Válaszd ki a szűrési feltételeket</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="text-sm font-medium mb-2 block">Nem</label>
              <Select value={selectedGender} onValueChange={setSelectedGender}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Összes</SelectItem>
                  <SelectItem value="Férfi">Férfi</SelectItem>
                  <SelectItem value="Nő">Nő</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label className="text-sm font-medium mb-2 block">Életkor</label>
              <Select value={selectedAge} onValueChange={setSelectedAge}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Összes</SelectItem>
                  <SelectItem value="<18">&lt;18</SelectItem>
                  <SelectItem value="18-24">18-24</SelectItem>
                  <SelectItem value="25-36">25-36</SelectItem>
                  <SelectItem value="37-44">37-44</SelectItem>
                  <SelectItem value="45-58">45-58</SelectItem>
                  <SelectItem value="58+">58+</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div className="mt-4 p-3 bg-white dark:bg-gray-900 rounded border">
            <p className="text-sm">
              <strong>Aktív szűrők:</strong> 
              {selectedGender === 'all' && selectedAge === 'all' 
                ? ' Nincs szűrő alkalmazva (összes válaszadó)' 
                : ` ${selectedGender !== 'all' ? selectedGender : 'Összes nem'}, ${selectedAge !== 'all' ? selectedAge + ' év' : 'Összes korcsoport'}`
              }
            </p>
          </div>
        </CardContent>
      </Card>

      <div className="grid grid-cols-4 gap-4">
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Összes válaszadó</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-4xl font-bold">{stats.total}</p>
          </CardContent>
        </Card>
        <Card className="border-red-200">
          <CardHeader>
            <CardTitle className="text-lg">Nem tudott róla</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-4xl font-bold text-red-600">{stats.notKnew}</p>
            <p className="text-sm text-muted-foreground">
              {stats.total > 0 ? ((stats.notKnew / stats.total) * 100).toFixed(1) : 0}%
            </p>
          </CardContent>
        </Card>
        <Card className="border-orange-200">
          <CardHeader>
            <CardTitle className="text-lg">Nem használta</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-4xl font-bold text-orange-600">{stats.notUsed}</p>
            <p className="text-sm text-muted-foreground">
              {stats.total > 0 ? ((stats.notUsed / stats.total) * 100).toFixed(1) : 0}%
            </p>
          </CardContent>
        </Card>
        <Card className="border-green-200">
          <CardHeader>
            <CardTitle className="text-lg">Használta</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-4xl font-bold text-green-600">{stats.used}</p>
            <p className="text-sm text-muted-foreground">
              {stats.total > 0 ? ((stats.used / stats.total) * 100).toFixed(1) : 0}%
            </p>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Kategória Megoszlás</CardTitle>
          <CardDescription>Szűrt adatok alapján</CardDescription>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={categoryData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip />
              <Bar dataKey="value" fill="#3b82f6" />
            </BarChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>

      {/* Gauge Charts for Awareness Comparison */}
      {awarenessComparison.length > 0 && (
        <div className="grid md:grid-cols-3 gap-6 mb-6">
          {awarenessComparison.map((item) => (
            <Card key={item.category}>
              <CardHeader>
                <CardTitle className="text-lg">{item.category}</CardTitle>
                <CardDescription>Szűrt csoport vs. Összes válaszadó</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-xs text-muted-foreground mb-2">Szűrt</p>
                    <GaugeChart
                      value={parseFloat(item.filtered.toFixed(1))}
                      maxValue={5}
                      size={120}
                      label={item.filtered.toFixed(1)}
                      sublabel="/ 5"
                    />
                  </div>
                  <div>
                    <p className="text-xs text-muted-foreground mb-2">Összes</p>
                    <GaugeChart
                      value={parseFloat(item.overall.toFixed(1))}
                      maxValue={5}
                      size={120}
                      label={item.overall.toFixed(1)}
                      sublabel="/ 5"
                    />
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}

      <Card>
        <CardHeader>
          <CardTitle>Összehasonlító elemzés</CardTitle>
          <CardDescription>Szűrt csoport vs. összes válaszadó mutatói (1-5 skála)</CardDescription>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={awarenessComparison} layout="horizontal">
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis type="category" dataKey="category" />
              <YAxis type="number" domain={[0, 5]} />
              <Tooltip />
              <Legend />
              <Bar dataKey="overall" name="Összes válaszadó" fill="#94a3b8" />
              <Bar dataKey="filtered" name="Szűrt csoport" fill="#3b82f6" />
            </BarChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Összehasonlító Elemzés</CardTitle>
          <CardDescription>
            Szűrt csoport vs. Összes válaszadó (Likert átlagok 1-5)
          </CardDescription>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={400}>
            <BarChart data={awarenessComparison}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="category" />
              <YAxis domain={[0, 5]} />
              <Tooltip />
              <Legend />
              <Bar dataKey="overall" name="Összes válaszadó" fill="#94a3b8" />
              <Bar dataKey="filtered" name="Szűrt csoport" fill="#3b82f6" />
            </BarChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>

      {stats.total > 0 && (
        <Card className="bg-yellow-50 dark:bg-yellow-950 border-yellow-200">
          <CardHeader>
            <CardTitle>📊 Demográfiai Insights</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-2 text-sm">
              <p>
                <strong>Szűrt csoport méret:</strong> {stats.total} fő 
                ({selectedGender !== 'all' ? selectedGender : 'Összes nem'}, 
                {selectedAge !== 'all' ? selectedAge + ' év' : 'Összes korcsoport'})
              </p>
              <p>
                <strong>Használati arány:</strong> {stats.total > 0 ? ((stats.used / stats.total) * 100).toFixed(1) : 0}%
              </p>
              <p>
                <strong>Legnagyobb kategória:</strong> {
                  stats.notKnew >= stats.notUsed && stats.notKnew >= stats.used ? 'Nem tudott róla' :
                  stats.notUsed >= stats.used ? 'Nem használta' : 'Használta'
                }
              </p>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
};

export default Demographics;
