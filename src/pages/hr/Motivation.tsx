import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Progress } from '@/components/ui/progress';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-react';

interface MotivationProps {
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

interface MotivationData {
  name: string;
  value: number;
}

const Motivation = ({ selectedAuditId, audits, onAuditChange }: MotivationProps) => {
  const [motivatorsData, setMotivatorsData] = useState<MotivationData[]>([]);
  const [expertData, setExpertData] = useState<MotivationData[]>([]);
  const [channelData, setChannelData] = useState<MotivationData[]>([]);
  const [loading, setLoading] = useState(true);
  const [notUsedCount, setNotUsedCount] = useState(0);

  useEffect(() => {
    if (selectedAuditId) {
      fetchMotivationData(selectedAuditId);
    }
  }, [selectedAuditId]);

  const fetchMotivationData = async (auditId: string) => {
    try {
      console.log('Fetching motivation data for audit:', auditId);
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses')
        .eq('audit_id', auditId)
        .eq('employee_metadata->>branch', 'not_used');

      if (error) {
        console.error('Error fetching motivation data:', error);
        throw error;
      }

      console.log('Motivation data received:', data?.length, 'responses');

      if (!data || data.length === 0) {
        console.log('No not_used responses found');
        setMotivatorsData([]);
        setExpertData([]);
        setChannelData([]);
        setNotUsedCount(0);
        return;
      }

      setNotUsedCount(data.length);

      // Count motivators (multiple choice)
      const motivatorCounts: Record<string, number> = {};
      data.forEach(r => {
        const values = r.responses?.nu_motivation_what;
        if (Array.isArray(values)) {
          values.forEach((val: string) => {
            motivatorCounts[val] = (motivatorCounts[val] || 0) + 1;
          });
        }
      });
      const motivatorsArray = Object.entries(motivatorCounts)
        .map(([name, value]) => ({ name, value }))
        .sort((a, b) => b.value - a.value);
      setMotivatorsData(motivatorsArray);

      // Count expert preference (single choice)
      const expertCounts: Record<string, number> = {};
      data.forEach(r => {
        const val = r.responses?.nu_motivation_expert;
        if (val) {
          expertCounts[val] = (expertCounts[val] || 0) + 1;
        }
      });
      const expertArray = Object.entries(expertCounts)
        .map(([name, value]) => ({ name, value }))
        .sort((a, b) => b.value - a.value);
      setExpertData(expertArray);

      // Count channel preference (single choice)
      const channelCounts: Record<string, number> = {};
      data.forEach(r => {
        const val = r.responses?.nu_motivation_channel;
        if (val) {
          channelCounts[val] = (channelCounts[val] || 0) + 1;
        }
      });
      const channelArray = Object.entries(channelCounts)
        .map(([name, value]) => ({ name, value }))
        .sort((a, b) => b.value - a.value);
      setChannelData(channelArray);

    } catch (error) {
      console.error('Error fetching motivation data:', error);
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

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="text-lg">Betöltés...</div>
      </div>
    );
  }

  if (notUsedCount === 0) {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-start gap-4 mb-6">
          <div className="flex-1">
            <h2 className="text-2xl font-bold mb-2">Motiváció Riport</h2>
            <p className="text-muted-foreground">
              Mi motiválná a nem használókat a program igénybevételére
            </p>
          </div>
          {audits.length > 0 && (
            <div className="min-w-[300px]">
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
        <div className="text-center py-12 text-muted-foreground">
          Még nincs kiértékelt adat a nem használók körében
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6 pt-20">
      <div className="flex justify-between items-start gap-4 mb-6">
        <div className="flex-1">
          <h2 className="text-2xl font-bold mb-2">Motiváció Riport</h2>
          <p className="text-muted-foreground">
            Mi motiválná a nem használókat a program igénybevételére ({notUsedCount} nem használó válaszadó)
          </p>
        </div>
        {audits.length > 0 && (
          <div className="min-w-[300px]">
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

      {/* Top Motivators */}
      <Card id="motivators-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('motivators-card', 'motivátorok')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle>Mi kellene a használathoz?</CardTitle>
          <CardDescription>Top motivátorok, amelyek ösztönöznék a nem használókat a program igénybevételére</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {motivatorsData.length === 0 ? (
            <p className="text-sm text-muted-foreground text-center py-4">Nincs adat</p>
          ) : (
            motivatorsData.map((item, index) => {
              const maxValue = motivatorsData[0].value;
              const percentage = maxValue > 0 ? (item.value / maxValue) * 100 : 0;
              return (
                <div key={item.name} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">{item.name}</span>
                    <span className="text-sm text-muted-foreground">{item.value} említés</span>
                  </div>
                  <Progress 
                    value={percentage} 
                    style={{ '--progress-background': '#3366ff' } as React.CSSProperties}
                    className="h-3"
                  />
                </div>
              );
            })
          )}
        </CardContent>
      </Card>

      {/* Expert Preference */}
      <Card id="expert-preference-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('expert-preference-card', 'szakértő-típus')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle>Preferált szakértő típus</CardTitle>
          <CardDescription>Milyen típusú szakértőt preferálnának a nem használók</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {expertData.length === 0 ? (
            <p className="text-sm text-muted-foreground text-center py-4">Nincs adat</p>
          ) : (
            expertData.map((item, index) => {
              const maxValue = expertData[0].value;
              const percentage = maxValue > 0 ? (item.value / maxValue) * 100 : 0;
              return (
                <div key={item.name} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">{item.name}</span>
                    <span className="text-sm text-muted-foreground">{item.value} fő</span>
                  </div>
                  <Progress 
                    value={percentage} 
                    style={{ '--progress-background': '#3366ff' } as React.CSSProperties}
                    className="h-3"
                  />
                </div>
              );
            })
          )}
        </CardContent>
      </Card>

      {/* Channel Preference */}
      <Card id="channel-preference-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('channel-preference-card', 'kommunikációs-csatorna')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle>Preferált kommunikációs csatorna</CardTitle>
          <CardDescription>Milyen elérhetőségi módot preferálnának (telefon, online chat, stb.)</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {channelData.length === 0 ? (
            <p className="text-sm text-muted-foreground text-center py-4">Nincs adat</p>
          ) : (
            channelData.map((item, index) => {
              const maxValue = channelData[0].value;
              const percentage = maxValue > 0 ? (item.value / maxValue) * 100 : 0;
              return (
                <div key={item.name} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">{item.name}</span>
                    <span className="text-sm text-muted-foreground">{item.value} fő</span>
                  </div>
                  <Progress 
                    value={percentage} 
                    style={{ '--progress-background': '#3366ff' } as React.CSSProperties}
                    className="h-3"
                  />
                </div>
              );
            })
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default Motivation;
