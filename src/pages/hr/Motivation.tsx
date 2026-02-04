import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Progress } from '@/components/ui/progress';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-react';
import { ReportNavigation } from '@/components/navigation/ReportNavigation';
import fourScoreLogo from "@/assets/4score_logo.png";

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
  packageType?: string;
  companies?: Array<{ id: string; company_name: string }>;
  selectedCompanyId?: string;
  onCompanyChange?: (id: string) => void;
}

interface MotivationData {
  name: string;
  value: number;
}

const Motivation = ({ selectedAuditId, audits, onAuditChange, packageType, companies = [], selectedCompanyId, onCompanyChange }: MotivationProps) => {
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

      // Count motivators (multiple choice) - új mező + régi válaszok is
      const motivatorCounts: Record<string, number> = {};
      data.forEach(r => {
        // Új mező (nu_motivation_positive)
        const newValues = r.responses?.nu_motivation_positive;
        if (Array.isArray(newValues)) {
          newValues.forEach((val: string) => {
            if (val) {
              motivatorCounts[val] = (motivatorCounts[val] || 0) + 1;
            }
          });
        }
        
        // Régi mező (nu_motivation_what) - csak biztonság kedvéért, ha maradt pozitív érték
        const oldValues = r.responses?.nu_motivation_what;
        const positiveMotivators = [
          'Egyszerűbb elérhetőség',
          'Pozitív kollégai tapasztalatok',
          'Biztos anonimitás garanciája',
          'Több információ a szolgáltatásról',
          'Vezetői támogatás és ösztönzés',
          'Kipróbálási lehetőség mentorral'
        ];
        
        if (Array.isArray(oldValues)) {
          oldValues.forEach((val: string) => {
            if (val && positiveMotivators.includes(val)) {
              motivatorCounts[val] = (motivatorCounts[val] || 0) + 1;
            }
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

  if (audits.length === 0) {
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <div className="flex items-center gap-4">
            <h2 className="text-2xl font-bold">Motiváció Riport</h2>
            <ReportNavigation currentTab="motivation" />
          </div>
          <p className="text-muted-foreground">
            Ami megkönnyítené a program kipróbálását a nem használók körében
          </p>
          <div className="flex flex-col md:flex-row md:items-center gap-4 mb-4">
            <img src={fourScoreLogo} alt="4Score" className="h-6" />
            {packageType === 'partner' && companies.length > 0 && onCompanyChange && (
              <div className="flex-1 md:max-w-[300px] md:ml-auto">
                <label className="text-xs text-muted-foreground mb-1.5 block">
                  Ügyfélcég szűrése
                </label>
                <Select value={selectedCompanyId} onValueChange={onCompanyChange}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Válassz ügyfélcéget" />
                  </SelectTrigger>
                  <SelectContent align="start">
                    <SelectItem value="all">Összes ügyfélcég</SelectItem>
                    {companies.map((company) => (
                      <SelectItem key={company.id} value={company.id}>
                        {company.company_name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            )}
          </div>
          <div className="text-center py-12 text-muted-foreground">
            Még nincs felmérés ehhez a céghez. Hozz létre egy új felmérést az első riport elkészítéséhez.
          </div>
        </div>
      </div>
    );
  }

  if (notUsedCount === 0) {
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <div className="flex items-center gap-4">
            <h2 className="text-2xl font-bold">Motiváció Riport</h2>
            <ReportNavigation currentTab="motivation" />
          </div>
          <p className="text-muted-foreground">
            Ami megkönnyítené a program kipróbálását a nem használók körében
          </p>
        </div>
        {audits.length > 0 && (
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
        )}
        <div className="text-center py-12 text-muted-foreground">
          Még nincs kiértékelt adat a nem használók körében
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
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
          <CardTitle>Ami megkönnyítené a kipróbálást</CardTitle>
          <CardDescription>
            Azok a tényezők, amelyek motiválnák a nem használókat az EAP program igénybevételére. Több választ is megjelölhettek.
          </CardDescription>
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
                    style={{ '--progress-background': '#04565f' } as React.CSSProperties}
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
          <CardDescription>
            Milyen szakember segítségét vennék igénybe legszívesebben a nem használók, ha úgy döntenének, hogy részt vesznek a programban.
          </CardDescription>
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
                    style={{ '--progress-background': '#04565f' } as React.CSSProperties}
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
          <CardDescription>
            Milyen módon szeretnék igénybe venni a szolgáltatást a nem használók: telefonos konzultáció, online chat, személyes találkozó, stb.
          </CardDescription>
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
                    style={{ '--progress-background': '#04565f' } as React.CSSProperties}
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
