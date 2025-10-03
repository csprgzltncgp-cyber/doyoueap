import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

type Audit = StandardAudit;

interface ChartData {
  name: string;
  value: number;
  percentage: string;
}

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

const Motivation = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [motivationData, setMotivationData] = useState<ChartData[]>([]);
  const [expertData, setExpertData] = useState<ChartData[]>([]);
  const [channelData, setChannelData] = useState<ChartData[]>([]);
  const [availabilityData, setAvailabilityData] = useState<ChartData[]>([]);
  const [communicationData, setCommunicationData] = useState<ChartData[]>([]);
  const [loading, setLoading] = useState(true);
  const [notUsedCount, setNotUsedCount] = useState(0);

  useEffect(() => {
    fetchAudits();
  }, []);

  useEffect(() => {
    if (selectedAuditId) {
      fetchMotivationData(selectedAuditId);
    }
  }, [selectedAuditId]);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select(AUDIT_SELECT_FIELDS)
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

  const countMultipleChoice = (responses: any[], key: string): { [key: string]: number } => {
    const counts: { [key: string]: number } = {};
    responses.forEach(r => {
      const values = r.responses?.[key];
      if (Array.isArray(values)) {
        values.forEach(val => {
          counts[val] = (counts[val] || 0) + 1;
        });
      }
    });
    return counts;
  };

  const countSingleChoice = (responses: any[], key: string): { [key: string]: number } => {
    const counts: { [key: string]: number } = {};
    responses.forEach(r => {
      const value = r.responses?.[key];
      if (value) {
        counts[value] = (counts[value] || 0) + 1;
      }
    });
    return counts;
  };

  const toChartData = (counts: { [key: string]: number }): ChartData[] => {
    const total = Object.values(counts).reduce((sum, val) => sum + val, 0);
    return Object.entries(counts)
      .map(([name, value]) => ({
        name,
        value,
        percentage: total > 0 ? ((value / total) * 100).toFixed(1) : '0',
      }))
      .sort((a, b) => b.value - a.value);
  };

  const fetchMotivationData = async (auditId: string) => {
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'not_used');

      if (error) throw error;

      if (!data || data.length === 0) {
        setMotivationData([]);
        setExpertData([]);
        setChannelData([]);
        setAvailabilityData([]);
        setCommunicationData([]);
        setNotUsedCount(0);
        return;
      }

      setNotUsedCount(data.length);

      // What would motivate them (multiple choice)
      const motivation = countMultipleChoice(data, 'nu_motivation_what');
      setMotivationData(toChartData(motivation));

      // Expert preference (single choice)
      const expert = countSingleChoice(data, 'nu_motivation_expert');
      setExpertData(toChartData(expert));

      // Channel preference (single choice)
      const channel = countSingleChoice(data, 'nu_motivation_channel');
      setChannelData(toChartData(channel));

      // Availability preference (single choice)
      const availability = countSingleChoice(data, 'nu_motivation_availability');
      setAvailabilityData(toChartData(availability));

      // Communication preference (single choice)
      const communication = countSingleChoice(data, 'nu_motivation_communication');
      setCommunicationData(toChartData(communication));

    } catch (error) {
      console.error('Error fetching motivation data:', error);
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
        <h1 className="text-3xl font-bold">Motiváció Riport</h1>
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

      <Card>
        <CardHeader>
          <CardTitle>Nem használók száma</CardTitle>
          <CardDescription>
            Akik tudnak az EAP-ról, de még nem használták
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-4xl font-bold">{notUsedCount}</p>
        </CardContent>
      </Card>

      {notUsedCount === 0 ? (
        <Card className="p-12">
          <CardContent className="text-center text-muted-foreground">
            <p className="text-lg mb-2">Nincs megjeleníthető adat</p>
            <p className="text-sm">Ehhez a felméréshez még nincsenek olyan válaszok, ahol a kitöltők tudtak a programról, de nem használták. Csak "Nem tudtam róla" vagy "Használtam" válaszok érkeztek eddig.</p>
          </CardContent>
        </Card>
      ) : (
        <>
          <Card>
            <CardHeader>
              <CardTitle>Mi kellene ahhoz, hogy kipróbálják?</CardTitle>
              <CardDescription>Több válasz lehetséges - Fejlesztési irányok azonosítása</CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={400}>
                <BarChart data={motivationData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis type="category" dataKey="name" width={200} />
                  <Tooltip />
                  <Bar dataKey="value" fill="#3b82f6" />
                </BarChart>
              </ResponsiveContainer>
              <div className="mt-4 grid grid-cols-2 gap-2">
                {motivationData.map((item, index) => (
                  <div 
                    key={item.name}
                    className="p-3 rounded border flex justify-between items-center"
                    style={{ borderLeft: `4px solid ${COLORS[index % COLORS.length]}` }}
                  >
                    <span className="text-sm">{item.name}</span>
                    <span className="font-bold">{item.value} ({item.percentage}%)</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          <div className="grid grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Szakértő Preferencia</CardTitle>
                <CardDescription>Milyen szakértőt használnának leginkább</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <PieChart>
                    <Pie
                      data={expertData}
                      cx="50%"
                      cy="50%"
                      labelLine={false}
                      label={(entry) => `${entry.name}: ${entry.value}`}
                      outerRadius={100}
                      fill="#8884d8"
                      dataKey="value"
                    >
                      {expertData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Csatorna Preferencia</CardTitle>
                <CardDescription>Milyen csatornát tartanának kényelmesnek</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={channelData}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="value" fill="#10b981" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>

          <div className="grid grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Elérhetőség Preferencia</CardTitle>
                <CardDescription>Mikor érnék el szívesen a szolgáltatást</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={availabilityData}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="value" fill="#f59e0b" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Kommunikáció Preferencia</CardTitle>
                <CardDescription>Milyen formában kommunikáljunk róla</CardDescription>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <PieChart>
                    <Pie
                      data={communicationData}
                      cx="50%"
                      cy="50%"
                      labelLine={false}
                      label={(entry) => `${entry.name}: ${entry.value}`}
                      outerRadius={100}
                      fill="#8884d8"
                      dataKey="value"
                    >
                      {communicationData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>

          <Card className="bg-blue-50 dark:bg-blue-950 border-blue-200">
            <CardHeader>
              <CardTitle>💡 Fejlesztési Javaslatok</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-2 text-sm">
                <p><strong>Top 3 motiváló tényező:</strong></p>
                <ul className="list-disc list-inside ml-4">
                  {motivationData.slice(0, 3).map((item) => (
                    <li key={item.name}>{item.name} ({item.value} említés)</li>
                  ))}
                </ul>
                <p className="mt-4">
                  <strong>Ajánlás:</strong> Fókuszálj ezekre a területekre a program népszerűsítéséhez és az elfogadás növeléséhez.
                </p>
              </div>
            </CardContent>
          </Card>
        </>
      )}
    </div>
  );
};

export default Motivation;
