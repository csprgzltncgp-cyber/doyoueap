import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { GaugeChart } from '@/components/ui/gauge-chart';
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

interface DemographicsProps {
  selectedAuditId: string;
}

const Demographics = ({ selectedAuditId }: DemographicsProps) => {
  const [selectedGender, setSelectedGender] = useState<string>('all');
  const [selectedAge, setSelectedAge] = useState<string>('all');
  const [stats, setStats] = useState<DemographicStats>({ total: 0, notKnew: 0, notUsed: 0, used: 0 });
  const [categoryData, setCategoryData] = useState<any[]>([]);
  const [awarenessComparison, setAwarenessComparison] = useState<ComparisonData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (selectedAuditId) {
      fetchDemographicData(selectedAuditId, selectedGender, selectedAge);
    }
  }, [selectedAuditId, selectedGender, selectedAge]);


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

  
  const COLORS = ['hsl(var(--chart-4))', 'hsl(var(--chart-3))', 'hsl(var(--chart-2))'];

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="text-lg">Betöltés...</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="mb-6">
        <h2 className="text-2xl font-bold mb-2">Demográfiai Elemzés</h2>
        <p className="text-muted-foreground">
          Válaszadók megoszlása kor, nem és felhasználói kategóriák szerint
        </p>
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <CardTitle>Szűrők</CardTitle>
          <CardDescription>Válassz demográfiai csoportot az elemzéshez</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm text-muted-foreground mb-2 block">Nem</label>
              <Select value={selectedGender} onValueChange={setSelectedGender}>
                <SelectTrigger>
                  <SelectValue placeholder="Válassz nemet" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Összes</SelectItem>
                  <SelectItem value="Férfi">Férfi</SelectItem>
                  <SelectItem value="Nő">Nő</SelectItem>
                </SelectContent>
              </Select>
            </div>
            
            <div>
              <label className="text-sm text-muted-foreground mb-2 block">Korcsoport</label>
              <Select value={selectedAge} onValueChange={setSelectedAge}>
                <SelectTrigger>
                  <SelectValue placeholder="Válassz korcsoportot" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Összes</SelectItem>
                  <SelectItem value="18-24">18-24</SelectItem>
                  <SelectItem value="25-36">25-36</SelectItem>
                  <SelectItem value="37-44">37-44</SelectItem>
                  <SelectItem value="45-58">45-58</SelectItem>
                  <SelectItem value="58+">58+</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardContent>
      </Card>

      {stats.total === 0 ? (
        <div className="text-center py-12 text-muted-foreground">
          Nincs adat a kiválasztott demográfiai csoportban
        </div>
      ) : (
        <>
          {/* Overview Stats */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <Card>
              <CardHeader className="pb-3">
                <CardTitle className="text-sm font-medium text-muted-foreground">Összesen</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-3xl font-bold">{stats.total}</div>
                <p className="text-xs text-muted-foreground mt-1">válaszadó</p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="pb-3">
                <CardTitle className="text-sm font-medium text-muted-foreground">Nem tudott róla</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-3xl font-bold">{stats.notKnew}</div>
                <p className="text-xs text-muted-foreground mt-1">
                  {stats.total > 0 ? ((stats.notKnew / stats.total) * 100).toFixed(1) : 0}%
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="pb-3">
                <CardTitle className="text-sm font-medium text-muted-foreground">Nem használta</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-3xl font-bold">{stats.notUsed}</div>
                <p className="text-xs text-muted-foreground mt-1">
                  {stats.total > 0 ? ((stats.notUsed / stats.total) * 100).toFixed(1) : 0}%
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="pb-3">
                <CardTitle className="text-sm font-medium text-muted-foreground">Használta</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-3xl font-bold">{stats.used}</div>
                <p className="text-xs text-muted-foreground mt-1">
                  {stats.total > 0 ? ((stats.used / stats.total) * 100).toFixed(1) : 0}%
                </p>
              </CardContent>
            </Card>
          </div>

          {/* Category Distribution */}
          <Card id="category-distribution-card">
            <CardHeader className="relative">
              <Button
                variant="ghost"
                size="icon"
                className="absolute right-2 top-2 h-8 w-8"
                onClick={() => exportCardToPNG('category-distribution-card', 'kategória-megoszlás')}
              >
                <Download className="h-4 w-4" />
              </Button>
              <CardTitle>Kategória Megoszlás</CardTitle>
              <CardDescription>Válaszadók eloszlása felhasználói kategóriák szerint</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-8">
                <div className="h-[300px] flex-1">
                  <ResponsiveContainer width="100%" height="100%">
                    <PieChart>
                      <Pie
                        data={categoryData}
                        cx="50%"
                        cy="50%"
                        outerRadius={100}
                        fill="#8884d8"
                        dataKey="value"
                      >
                        {categoryData.map((entry, index) => (
                          <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                        ))}
                      </Pie>
                      <Tooltip />
                    </PieChart>
                  </ResponsiveContainer>
                </div>
                
                <div className="flex flex-col gap-3">
                  {categoryData.map((entry, index) => (
                    <div key={entry.name} className="flex items-center gap-2">
                      <div 
                        className="w-3 h-3 rounded-full" 
                        style={{ backgroundColor: COLORS[index % COLORS.length] }}
                      />
                      <span className="text-sm text-foreground">{entry.name}</span>
                    </div>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Comparison Chart */}
          <Card id="comparison-chart-card">
            <CardHeader className="relative">
              <Button
                variant="ghost"
                size="icon"
                className="absolute right-2 top-2 h-8 w-8"
                onClick={() => exportCardToPNG('comparison-chart-card', 'összehasonlítás')}
              >
                <Download className="h-4 w-4" />
              </Button>
              <CardTitle>Összehasonlítás</CardTitle>
              <CardDescription>
                {selectedGender !== 'all' || selectedAge !== 'all' 
                  ? 'Szűrt csoport vs. teljes minta' 
                  : 'Átlagos értékek (1-5 skála)'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={awarenessComparison}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="category" />
                  <YAxis domain={[0, 5]} />
                  <Tooltip />
                  <Legend />
                  <Bar dataKey="overall" fill="hsl(var(--chart-2))" name="Teljes minta" />
                  {(selectedGender !== 'all' || selectedAge !== 'all') && (
                    <Bar dataKey="filtered" fill="hsl(var(--chart-3))" name="Szűrt csoport" />
                  )}
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </>
      )}
    </div>
  );
};

export default Demographics;
