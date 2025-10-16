import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Download, Settings, User, MessageCircle, Clock, Video } from 'lucide-react';
import { formatAuditName } from '@/lib/auditUtils';
import { ReportNavigation } from '@/components/navigation/ReportNavigation';
import fourScoreLogo from '@/assets/4score_logo.svg';

interface PreferencesProps {
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

const Preferences = ({ selectedAuditId, audits, onAuditChange }: PreferencesProps) => {
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

  // Csak használók válaszai
  const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');

  // Szakértő preferencia elemzése
  const expertData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const expert = r.responses?.u_pref_expert;
    if (expert) {
      expertData[expert] = (expertData[expert] || 0) + 1;
    }
  });

  const expertChartData = Object.entries(expertData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Csatorna preferencia elemzése (multiple choice)
  const channelData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const channels = r.responses?.u_pref_channel;
    if (Array.isArray(channels)) {
      channels.forEach((channel: string) => {
        channelData[channel] = (channelData[channel] || 0) + 1;
      });
    }
  });

  const channelChartData = Object.entries(channelData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Elérhetőség preferencia elemzése (multiple choice)
  const availabilityData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const availability = r.responses?.u_pref_availability;
    if (Array.isArray(availability)) {
      availability.forEach((item: string) => {
        availabilityData[item] = (availabilityData[item] || 0) + 1;
      });
    }
  });

  const availabilityChartData = Object.entries(availabilityData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Tartalomtípus preferencia elemzése (multiple choice)
  const contentTypeData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const contentTypes = r.responses?.u_pref_content_type;
    if (Array.isArray(contentTypes)) {
      contentTypes.forEach((type: string) => {
        contentTypeData[type] = (contentTypeData[type] || 0) + 1;
      });
    }
  });

  const contentTypeChartData = Object.entries(contentTypeData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Kommunikációs gyakoriság preferencia elemzése
  const commFrequencyData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const freq = r.responses?.u_pref_comm_frequency;
    if (freq) {
      commFrequencyData[freq] = (commFrequencyData[freq] || 0) + 1;
    }
  });

  const commFrequencyChartData = Object.entries(commFrequencyData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Fejléc */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <div className="flex items-center gap-4 mb-2">
              <h2 className="text-2xl font-bold">Preferenciák Elemzése</h2>
              <ReportNavigation currentTab="preferences" />
            </div>
            <p className="text-muted-foreground text-sm">
              A felhasználók szolgáltatás preferenciáinak és kommunikációs igényeinek részletes elemzése
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

      {/* 1. sor: Szakértő és Csatorna Preferencia */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Szakértő Preferencia */}
        <Card id="expert-preference-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('expert-preference-card', 'szakerto-preferencia')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <User className="w-5 h-5" />
              Szakértő Preferencia
            </CardTitle>
            <CardDescription>Milyen szakértőt preferálnak a felhasználók?</CardDescription>
          </CardHeader>
          <CardContent>
            {expertChartData.length > 0 ? (
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={expertChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={100} />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[8, 8, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <p className="text-muted-foreground text-center py-8">Nincs adat</p>
            )}
          </CardContent>
        </Card>

        {/* Csatorna Preferencia */}
        <Card id="channel-preference-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('channel-preference-card', 'csatorna-preferencia')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <MessageCircle className="w-5 h-5" />
              Csatorna Preferencia
            </CardTitle>
            <CardDescription>Milyen kommunikációs csatornákat részesítenek előnyben?</CardDescription>
          </CardHeader>
          <CardContent>
            {channelChartData.length > 0 ? (
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={channelChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={100} />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-3))" radius={[8, 8, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <p className="text-muted-foreground text-center py-8">Nincs adat</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* 2. sor: Elérhetőség és Tartalomtípus */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Elérhetőség Preferencia */}
        <Card id="availability-preference-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('availability-preference-card', 'elerhetoseg-preferencia')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Clock className="w-5 h-5" />
              Elérhetőség Preferencia
            </CardTitle>
            <CardDescription>Mikor szeretnék elérni a szolgáltatást?</CardDescription>
          </CardHeader>
          <CardContent>
            {availabilityChartData.length > 0 ? (
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={availabilityChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={100} />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-4))" radius={[8, 8, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <p className="text-muted-foreground text-center py-8">Nincs adat</p>
            )}
          </CardContent>
        </Card>

        {/* Tartalomtípus Preferencia */}
        <Card id="content-type-preference-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('content-type-preference-card', 'tartalomtipus-preferencia')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Video className="w-5 h-5" />
              Tartalomtípus Preferencia
            </CardTitle>
            <CardDescription>Milyen tartalmakat részesítenek előnyben?</CardDescription>
          </CardHeader>
          <CardContent>
            {contentTypeChartData.length > 0 ? (
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={contentTypeChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={100} />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-5))" radius={[8, 8, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <p className="text-muted-foreground text-center py-8">Nincs adat</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* 3. sor: Kommunikációs Gyakoriság */}
      <div className="grid grid-cols-1 gap-6">
        <Card id="comm-frequency-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('comm-frequency-card', 'kommunikacios-gyakorisag')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Settings className="w-5 h-5" />
              Kommunikációs Gyakoriság Preferencia
            </CardTitle>
            <CardDescription>Milyen gyakran szeretnének kommunikációt kapni az EAP programról?</CardDescription>
          </CardHeader>
          <CardContent>
            {commFrequencyChartData.length > 0 ? (
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={commFrequencyChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={100} />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-1))" radius={[8, 8, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <p className="text-muted-foreground text-center py-8">Nincs adat</p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default Preferences;
