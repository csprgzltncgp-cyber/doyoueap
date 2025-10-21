import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Download, Activity, Clock, Users, MessageSquare, Phone, AlertTriangle } from 'lucide-react';
import fourScoreLogo from '@/assets/4score_logo.svg';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { Progress } from '@/components/ui/progress';
import { formatAuditName } from '@/lib/auditUtils';
import { ReportNavigation } from '@/components/navigation/ReportNavigation';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

interface UsageProps {
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

const Usage = ({ selectedAuditId, audits, onAuditChange, packageType, companies = [], selectedCompanyId, onCompanyChange }: UsageProps) => {
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

  // Helper function
  const calculateAverage = (values: any[]): string => {
    if (values.length === 0) return '0';
    const sum = values.reduce((acc, val) => acc + val, 0);
    return (sum / values.length).toFixed(1);
  };

  // Csak használók
  const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const totalCount = responses.length;
  const usageRate = totalCount > 0 ? ((usedResponses.length / totalCount) * 100).toFixed(1) : '0.0';

  // Trust responses split
  const usedTrustResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsedTrustResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');

  // Non-users' intention to use in the future (yes/no question, convert to percentage)
  const wouldUseYes = notUsedTrustResponses.filter(r => r.responses?.nu_usage_would_use === 'yes' || r.responses?.nu_usage_would_use === 'Igen').length;
  const wouldUseTotal = notUsedTrustResponses.filter(r => r.responses?.nu_usage_would_use === 'yes' || r.responses?.nu_usage_would_use === 'no' || r.responses?.nu_usage_would_use === 'Igen' || r.responses?.nu_usage_would_use === 'Nem').length;
  const futureUsageIntentNonUsers = wouldUseTotal > 0 
    ? ((wouldUseYes / wouldUseTotal) * 100)
    : 0;

  // Users' likelihood to use again (1-5 scale, convert to percentage)
  const usedLikelihoodValues = usedTrustResponses
    .map(r => r.responses?.u_trust_likelihood)
    .filter(v => typeof v === 'number' && !isNaN(v));
  const futureUsageIntentUsers = usedLikelihoodValues.length > 0 
    ? ((parseFloat(calculateAverage(usedLikelihoodValues)) / 5) * 100)
    : 0;

  // Combined usage score (average of both groups if both exist)
  let usageScore: string;
  if (futureUsageIntentNonUsers > 0 && futureUsageIntentUsers > 0) {
    usageScore = ((futureUsageIntentNonUsers + futureUsageIntentUsers) / 2).toFixed(1);
  } else if (futureUsageIntentNonUsers > 0) {
    usageScore = futureUsageIntentNonUsers.toFixed(1);
  } else if (futureUsageIntentUsers > 0) {
    usageScore = futureUsageIntentUsers.toFixed(1);
  } else {
    usageScore = '0.0';
  }

  // Nem használók - jövőbeni használati szándék (deprecated, kept for other cards)
  const wouldUseYesOld = notUsedResponses.filter(r => r.responses?.nu_usage_would_use === 'yes' || r.responses?.nu_usage_would_use === 'Igen').length;
  const wouldUseNoOld = notUsedResponses.filter(r => r.responses?.nu_usage_would_use === 'no' || r.responses?.nu_usage_would_use === 'Nem').length;
  const wouldUseTotalOld = wouldUseYesOld + wouldUseNoOld;
  const wouldUseRate = wouldUseTotalOld > 0 ? ((wouldUseYesOld / wouldUseTotalOld) * 100).toFixed(1) : '0.0';

  const planToUseYes = notUsedResponses.filter(r => r.responses?.nu_usage_plan_to_use === 'yes' || r.responses?.nu_usage_plan_to_use === 'Igen').length;
  const planToUseNo = notUsedResponses.filter(r => r.responses?.nu_usage_plan_to_use === 'no' || r.responses?.nu_usage_plan_to_use === 'Nem').length;
  const planToUseTotal = planToUseYes + planToUseNo;
  const planToUseRate = planToUseTotal > 0 ? ((planToUseYes / planToUseTotal) * 100).toFixed(1) : '0.0';

  // Használat gyakorisága
  const frequencyData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const freq = r.responses?.u_usage_frequency;
    if (freq) {
      frequencyData[freq] = (frequencyData[freq] || 0) + 1;
    }
  });

  const frequencyChartData = Object.entries(frequencyData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Témakörök (multiple choice)
  const topicData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const topics = r.responses?.u_usage_topic;
    if (Array.isArray(topics)) {
      topics.forEach((topic: string) => {
        topicData[topic] = (topicData[topic] || 0) + 1;
      });
    }
  });

  const topicChartData = Object.entries(topicData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Csatornák (multiple choice)
  const channelData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const channels = r.responses?.u_usage_channel;
    if (Array.isArray(channels)) {
      channels.forEach((channel: string) => {
        channelData[channel] = (channelData[channel] || 0) + 1;
      });
    }
  });

  const channelChartData = Object.entries(channelData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Család használata
  const familyYes = usedResponses.filter(r => r.responses?.u_usage_family === 'yes' || r.responses?.u_usage_family === 'Igen').length;
  const familyNo = usedResponses.filter(r => r.responses?.u_usage_family === 'no' || r.responses?.u_usage_family === 'Nem').length;
  const familyTotal = familyYes + familyNo;
  const familyRate = familyTotal > 0 ? ((familyYes / familyTotal) * 100).toFixed(1) : '0.0';

  const familyChartData = [
    { name: 'Igen, család is használta', value: familyYes, color: 'hsl(var(--chart-2))' },
    { name: 'Csak én használtam', value: familyNo, color: 'hsl(var(--chart-3))' }
  ].filter(item => item.value > 0);

  // Időtartam gondoskodásig
  const timeToCareData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const time = r.responses?.u_usage_time_to_care;
    if (time) {
      timeToCareData[time] = (timeToCareData[time] || 0) + 1;
    }
  });

  const timeToCareChartData = Object.entries(timeToCareData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value);

  // Leggyakoribb témakör
  const topTopic = topicChartData.length > 0 ? topicChartData[0] : null;
  
  // Leggyakoribb csatorna
  const topChannel = channelChartData.length > 0 ? channelChartData[0] : null;

  // Átlagos használat diverzitás (hány különböző témát/csatornát használnak)
  const avgTopicsPerUser = usedResponses.length > 0 
    ? (usedResponses.reduce((sum, r) => sum + (Array.isArray(r.responses?.u_usage_topic) ? r.responses.u_usage_topic.length : 0), 0) / usedResponses.length).toFixed(1)
    : '0.0';

  const avgChannelsPerUser = usedResponses.length > 0 
    ? (usedResponses.reduce((sum, r) => sum + (Array.isArray(r.responses?.u_usage_channel) ? r.responses.u_usage_channel.length : 0), 0) / usedResponses.length).toFixed(1)
    : '0.0';

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  // Early return with company selector for partners
  if (audits.length === 0) {
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <div className="flex items-center gap-4">
            <h2 className="text-2xl font-bold">Használat Részletes Elemzése</h2>
            <ReportNavigation currentTab="usage" />
          </div>
          <p className="text-muted-foreground text-sm">
            Az EAP program használati szokásainak, csatornáinak és témáinak átfogó kiértékelése
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

  return (
    <div className="space-y-6">
      {/* Fejléc */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <div className="flex items-center gap-4 mb-2">
              <h2 className="text-2xl font-bold">Használat Részletes Elemzése</h2>
              <ReportNavigation currentTab="usage" />
            </div>
            <p className="text-muted-foreground text-sm">
              Az EAP program használati szokásainak, csatornáinak és témáinak átfogó kiértékelése
            </p>
          </div>
        </div>
        <div className="flex flex-col md:flex-row md:items-center gap-4">
          <img src={fourScoreLogo} alt="4Score" className="h-6" />
          <div className="flex flex-col md:flex-row gap-4 md:ml-auto">
            {/* Company selector for partner users */}
            {packageType === 'partner' && companies.length > 0 && onCompanyChange && (
              <div className="flex-1 md:max-w-[300px]">
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
            {/* Audit selector */}
            {audits.length > 0 && (
              <div className="flex-1 md:max-w-[300px]">
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
        </div>
      </div>

      {/* Figyelmeztetés alacsony közeljövőbeni tervekhez */}
      {parseFloat(planToUseRate) < 10 && (
        <Alert className="border-[#ff0033] bg-transparent">
          <AlertTriangle style={{ color: '#ff0033' }} className="h-5 w-5" />
          <AlertTitle className="text-[#ff0033]">Alacsony konkrét tervezési szándék észlelve</AlertTitle>
          <AlertDescription className="text-[#ff0033]">
            A nem használók mindössze {planToUseRate}%-a ({planToUseYes} fő) tervezi konkrétan igénybe venni a közeljövőben a programot.
            Ez arra utal, hogy bár lehet általános hajlandóság, nincs azonnali elköteleződés.
            Javasolt intézkedések: egyszerűsíteni a hozzáférést, bemutatni konkrét példákat, 
            rövid távú incentívák (pl. gyors bejelentkezési lehetőség, próbaszesszió).
          </AlertDescription>
        </Alert>
      )}

      {/* 1. sor: Használat Index és Közeljövőbeni Tervek */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Használat Index */}
        <Card id="would-use-future-card" className={`border-2 ${parseFloat(usageScore) < 40 ? 'border-[#ff0033]' : 'border-[#3366ff]'}`}>
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('would-use-future-card', 'hasznalat-index')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Users className="w-5 h-5" />
              Használat Index
            </CardTitle>
            <CardDescription>Jövőbeli használati szándék</CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center">
              <GaugeChart 
                value={parseFloat(usageScore)} 
                maxValue={100}
                size={240}
                label={`${usageScore}%`}
                sublabel={`${usedLikelihoodValues.length + wouldUseTotal} válasz`}
                cornerRadius={30}
              />
            </div>
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              <p className="text-xs text-muted-foreground">
                {parseFloat(usageScore) >= 70 
                  ? 'Magas a jövőbeli használati hajlandóság.'
                  : parseFloat(usageScore) >= 40
                  ? 'Közepes a nyitottság a program jövőbeni használatára.'
                  : 'Alacsony a jövőbeni használati szándék - érdemes a bizalomépítésre és kommunikációra fókuszálni.'}
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Közeljövőbeni Tervek */}
        <Card id="plan-to-use-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('plan-to-use-card', 'kozeljovo-tervezes')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Közeljövőbeni Tervek</CardTitle>
            <CardDescription>Tervezed igénybe venni a közeljövőben?</CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center">
              <GaugeChart 
                value={parseFloat(planToUseRate)} 
                maxValue={100}
                size={240}
                label={`${planToUseRate}%`}
                sublabel={`${planToUseYes} / ${planToUseTotal} fő tervezi`}
                cornerRadius={30}
              />
            </div>
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              {parseFloat(planToUseRate) < 10 ? (
                <div className="flex items-start gap-2">
                  <AlertTriangle className="h-5 w-5 flex-shrink-0 mt-0.5" style={{ color: '#ff0033' }} />
                  <p className="text-xs" style={{ color: '#ff0033' }}>
                    Kevesen tervezik konkrétan - érdemes a program előnyeit jobban kommunikálni
                  </p>
                </div>
              ) : (
                <p className="text-xs text-muted-foreground">
                  {parseFloat(planToUseRate) >= 30 
                    ? 'Sokan konkrétan tervezik a program igénybevételét' 
                    : 'Néhányan aktívan fontolgatják a használatot'}
                </p>
              )}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* 2. sor: Fő használati mutatók */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {/* Használók Aránya */}
        <Card id="usage-rate-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('usage-rate-card', 'hasznalok-aranya')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Activity className="w-5 h-5" />
              Használói Arány
            </CardTitle>
            <CardDescription>Összes válaszadóból</CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center">
              <GaugeChart 
                value={parseFloat(usageRate)} 
                maxValue={100}
                size={220}
                label={`${usageRate}%`}
                sublabel={`${usedResponses.length} / ${totalCount} fő`}
                cornerRadius={30}
              />
            </div>
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              <p className="text-xs text-muted-foreground">
                A válaszolók közül ennyien használták a programot
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Családi Használat */}
        <Card id="family-usage-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('family-usage-card', 'csaladi-hasznalat')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Users className="w-5 h-5" />
              Családi Használat
            </CardTitle>
            <CardDescription>Hozzátartozók is igénybe vették</CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col min-h-[320px]">
            <div className="flex-1 flex items-center justify-center">
              <GaugeChart 
                value={parseFloat(familyRate)} 
                maxValue={100}
                size={220}
                label={`${familyRate}%`}
                sublabel={`${familyYes} / ${familyTotal} fő`}
                cornerRadius={30}
              />
            </div>
            <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
              <p className="text-xs text-muted-foreground">
                A használók közül ennyien családtaggal együtt vették igénybe
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Leggyakoribb Témakör */}
        <Card id="top-topic-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('top-topic-card', 'leggyakoribb-temak')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <MessageSquare className="w-5 h-5" />
              Leggyakoribb Téma
            </CardTitle>
            <CardDescription>Legtöbbet használt szolgáltatás</CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col min-h-[320px]">
            {topTopic ? (
              <>
                <div className="flex-1 flex items-center justify-center">
                  <GaugeChart 
                    value={usedResponses.length > 0 ? parseFloat(((topTopic.value / usedResponses.length) * 100).toFixed(0)) : 0} 
                    maxValue={100}
                    size={220}
                    label={`${usedResponses.length > 0 ? ((topTopic.value / usedResponses.length) * 100).toFixed(0) : 0}%`}
                    sublabel={topTopic.name}
                    cornerRadius={30}
                  />
                </div>
                <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
                  <p className="text-xs text-muted-foreground">
                    {parseFloat(avgTopicsPerUser) >= 2 ? 'A használók átlagosan több témában is igénybe veszik a szolgáltatást' : 'A használók általában egy-két témában veszik igénybe'}
                  </p>
                </div>
              </>
            ) : (
              <p className="text-sm text-muted-foreground text-center">Nincs adat</p>
            )}
          </CardContent>
        </Card>

        {/* Leggyakoribb Csatorna */}
        <Card id="top-channel-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('top-channel-card', 'leggyakoribb-csatorna')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Phone className="w-5 h-5" />
              Leggyakoribb Csatorna
            </CardTitle>
            <CardDescription>Preferált elérési mód</CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col min-h-[320px]">
            {topChannel ? (
              <>
                <div className="flex-1 flex items-center justify-center">
                  <GaugeChart 
                    value={usedResponses.length > 0 ? parseFloat(((topChannel.value / usedResponses.length) * 100).toFixed(0)) : 0} 
                    maxValue={100}
                    size={220}
                    label={`${usedResponses.length > 0 ? ((topChannel.value / usedResponses.length) * 100).toFixed(0) : 0}%`}
                    sublabel={topChannel.name}
                    cornerRadius={30}
                  />
                </div>
                <div className="bg-muted/30 p-3 rounded-md text-left mt-4">
                  <p className="text-xs text-muted-foreground">
                    {parseFloat(avgChannelsPerUser) >= 2 ? 'A használók többféle csatornát is kipróbálnak' : 'A használók általában egy csatornát preferálnak'}
                  </p>
                </div>
              </>
            ) : (
              <p className="text-sm text-muted-foreground text-center">Nincs adat</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* 2. sor: Használat gyakorisága + Családi használat részletek */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Használat Gyakorisága */}
        <Card id="frequency-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('frequency-card', 'hasznalati-gyakorisag')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Használat Gyakorisága</CardTitle>
            <CardDescription>Hányszor vették igénybe a szolgáltatást a programot ismerők közül</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px] flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={frequencyChartData.map((item, index) => ({ 
                      name: item.name, 
                      value: item.value,
                      color: `hsl(var(--chart-${(index % 4) + 1}))`
                    }))}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={120}
                    paddingAngle={2}
                    dataKey="value"
                    label={false}
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
              {frequencyChartData.map((entry, index) => {
                const total = frequencyChartData.reduce((sum, item) => sum + item.value, 0);
                const percentage = total > 0 ? ((entry.value / total) * 100).toFixed(0) : 0;
                return (
                  <div key={index} className="flex items-center gap-2">
                    <div 
                      className="w-3 h-3 rounded-full" 
                      style={{ backgroundColor: `hsl(var(--chart-${(index % 4) + 1}))` }}
                    />
                    <span className="text-sm text-foreground">
                      {entry.name} alkalom: {percentage}%
                    </span>
                  </div>
                );
              })}
            </div>
            <div className="bg-muted/30 p-3 rounded-md mt-4">
              <p className="text-xs text-muted-foreground">
                Ez mutatja, hogy a használók hányszor (hány alkalommal) fordultak a szolgáltatáshoz. 
                Például 1 alkalommal, 2-3 alkalommal, 4-6 alkalommal, vagy rendszeresen (több mint 6 alkalom).
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Családi Használat Eloszlás */}
        <Card id="family-distribution-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('family-distribution-card', 'csaladi-hasznalat-eloszlas')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Családi Használat Megoszlása</CardTitle>
            <CardDescription>Hozzátartozók bevonása a szolgáltatásba</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px] flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={familyChartData}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={120}
                    paddingAngle={2}
                    dataKey="value"
                  >
                    {familyChartData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </div>
            <div className="flex flex-wrap gap-4 justify-center mt-4">
              {familyChartData.map((entry, index) => (
                <div key={index} className="flex items-center gap-2">
                  <div 
                    className="w-3 h-3 rounded-full" 
                    style={{ backgroundColor: entry.color }}
                  />
                  <span className="text-sm text-foreground">
                    {entry.name}: {entry.value} ({familyTotal > 0 ? ((entry.value / familyTotal) * 100).toFixed(0) : 0}%)
                  </span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* 3. sor: Témakörök */}
      <Card id="topics-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('topics-card', 'temakorok')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle className="text-lg">Igénybe Vett Témakörök</CardTitle>
          <CardDescription>Milyen problémák kapcsán keresték meg a szolgáltatást (többszörös választás)</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[350px] flex items-center justify-center">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={topicChartData.map((item, index) => ({ 
                    name: item.name, 
                    value: item.value,
                    color: `hsl(var(--chart-${(index % 4) + 1}))`
                  }))}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={120}
                  paddingAngle={2}
                  dataKey="value"
                  label={false}
                >
                  {topicChartData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={`hsl(var(--chart-${(index % 4) + 1}))`} />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </div>
          <div className="flex flex-wrap gap-4 justify-center mt-4">
            {topicChartData.slice(0, 6).map((entry, index) => {
              const total = topicChartData.reduce((sum, item) => sum + item.value, 0);
              const percentage = total > 0 ? ((entry.value / total) * 100).toFixed(0) : 0;
              return (
                <div key={index} className="flex items-center gap-2">
                  <div 
                    className="w-3 h-3 rounded-full" 
                    style={{ backgroundColor: `hsl(var(--chart-${(index % 4) + 1}))` }}
                  />
                  <span className="text-sm text-foreground">
                    {entry.name}: {percentage}%
                  </span>
                </div>
              );
            })}
            {topicChartData.length > 6 && (
              <span className="text-xs text-muted-foreground">
                +{topicChartData.length - 6} további téma
              </span>
            )}
          </div>
          <p className="text-xs text-muted-foreground text-center mt-4">
            Átlagosan {avgTopicsPerUser} témakört említenek a használók
          </p>
        </CardContent>
      </Card>

      {/* 4. sor: Csatornák + Időtartam */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Használt Csatornák */}
        <Card id="channels-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('channels-card', 'csatornak')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg">Használt Csatornák</CardTitle>
            <CardDescription>Elérési módok preferenciája (többszörös választás)</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px] flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={channelChartData.map((item, index) => ({ 
                      name: item.name, 
                      value: item.value,
                      color: `hsl(var(--chart-${(index % 4) + 1}))`
                    }))}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={120}
                    paddingAngle={2}
                    dataKey="value"
                    label={false}
                  >
                    {channelChartData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={`hsl(var(--chart-${(index % 4) + 1}))`} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </div>
            <div className="flex flex-wrap gap-4 justify-center mt-4">
              {channelChartData.map((entry, index) => {
                const total = channelChartData.reduce((sum, item) => sum + item.value, 0);
                const percentage = total > 0 ? ((entry.value / total) * 100).toFixed(0) : 0;
                return (
                  <div key={index} className="flex items-center gap-2">
                    <div 
                      className="w-3 h-3 rounded-full" 
                      style={{ backgroundColor: `hsl(var(--chart-${(index % 4) + 1}))` }}
                    />
                    <span className="text-sm text-foreground">
                      {entry.name}: {percentage}%
                    </span>
                  </div>
                );
              })}
            </div>
            <p className="text-xs text-muted-foreground mt-4">
              Átlagosan {avgChannelsPerUser} csatornát próbálnak ki a használók
            </p>
          </CardContent>
        </Card>

        {/* Időtartam Gondoskodásig */}
        <Card id="time-to-care-card">
          <CardHeader className="relative">
            <Button
              variant="ghost"
              size="icon"
              className="absolute right-2 top-2 h-8 w-8"
              onClick={() => exportCardToPNG('time-to-care-card', 'idotartam-gondoskodasig')}
            >
              <Download className="h-4 w-4" />
            </Button>
            <CardTitle className="text-lg flex items-center gap-2">
              <Clock className="w-5 h-5" />
              Időtartam Gondoskodásig
            </CardTitle>
            <CardDescription>Mennyi idő alatt kaptak ellátást</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px] flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={timeToCareChartData.map((item, index) => ({ 
                      name: item.name, 
                      value: item.value,
                      color: `hsl(var(--chart-${(index % 4) + 1}))`
                    }))}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={120}
                    paddingAngle={2}
                    dataKey="value"
                    label={false}
                  >
                    {timeToCareChartData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={`hsl(var(--chart-${(index % 4) + 1}))`} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </div>
            <div className="flex flex-wrap gap-4 justify-center mt-4">
              {timeToCareChartData.map((entry, index) => {
                const total = timeToCareChartData.reduce((sum, item) => sum + item.value, 0);
                const percentage = total > 0 ? ((entry.value / total) * 100).toFixed(0) : 0;
                return (
                  <div key={index} className="flex items-center gap-2">
                    <div 
                      className="w-3 h-3 rounded-full" 
                      style={{ backgroundColor: `hsl(var(--chart-${(index % 4) + 1}))` }}
                    />
                    <span className="text-sm text-foreground">
                      {entry.name}: {percentage}%
                    </span>
                  </div>
                );
              })}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* 5. sor: Használati intenzitás profil */}
      <Card id="usage-intensity-card">
        <CardHeader className="relative">
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-2 top-2 h-8 w-8"
            onClick={() => exportCardToPNG('usage-intensity-card', 'hasznalati-intenzitas')}
          >
            <Download className="h-4 w-4" />
          </Button>
          <CardTitle className="text-lg">Használati Intenzitás Profil</CardTitle>
          <CardDescription>Átfogó kép a használat mélységéről ({usedResponses.length} használó)</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {/* Témák diverzitása */}
          <div className="space-y-2">
            <div className="flex items-start justify-between">
              <div>
                <p className="font-semibold">Témák Diverzitása</p>
                <p className="text-xs text-muted-foreground">Átlagosan hány témában veszik igénybe</p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold">{avgTopicsPerUser}</p>
                <p className="text-xs text-muted-foreground">/ téma</p>
              </div>
            </div>
            <Progress 
              value={(parseFloat(avgTopicsPerUser) / 5) * 100} 
              style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
              className="h-3"
            />
            <div className="flex justify-between text-xs text-muted-foreground">
              <span>1 - Fókuszált használat</span>
              <span>5+ - Sokrétű igénybevétel</span>
            </div>
          </div>

          {/* Csatornák diverzitása */}
          <div className="space-y-2">
            <div className="flex items-start justify-between">
              <div>
                <p className="font-semibold">Csatornák Diverzitása</p>
                <p className="text-xs text-muted-foreground">Átlagosan hány csatornát használnak</p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold">{avgChannelsPerUser}</p>
                <p className="text-xs text-muted-foreground">/ csatorna</p>
              </div>
            </div>
            <Progress 
              value={(parseFloat(avgChannelsPerUser) / 3) * 100} 
              style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
              className="h-3"
            />
            <div className="flex justify-between text-xs text-muted-foreground">
              <span>1 - Egy csatorna preferálása</span>
              <span>3+ - Multimodális használat</span>
            </div>
          </div>

          {/* Családi bevonás */}
          <div className="space-y-2">
            <div className="flex items-start justify-between">
              <div>
                <p className="font-semibold">Családi Bevonás Aránya</p>
                <p className="text-xs text-muted-foreground">Hozzátartozók is igénybe vették</p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold">{familyRate}%</p>
                <p className="text-xs text-muted-foreground">{familyYes} fő</p>
              </div>
            </div>
            <Progress 
              value={parseFloat(familyRate)} 
              style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
              className="h-3"
            />
            <div className="bg-muted/30 p-3 rounded-md">
              <p className="text-xs text-muted-foreground">
                {parseFloat(familyRate) >= 30 
                  ? 'Magas családi bevonás - a program hatékonyan terjed a családon belül' 
                  : parseFloat(familyRate) >= 15
                  ? 'Közepes családi bevonás'
                  : 'Alacsony családi bevonás - érdemes népszerűsíteni a családi használatot'}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>


      {/* Statisztikai összefoglaló */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Statisztikai Összefoglaló</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Összes válaszadó</p>
              <p className="text-2xl font-bold">{totalCount}</p>
            </div>
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Használók száma</p>
              <p className="text-2xl font-bold">{usedResponses.length} ({usageRate}%)</p>
            </div>
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Nem használók száma</p>
              <p className="text-2xl font-bold">{notUsedResponses.length}</p>
            </div>
            <div className="p-4 bg-muted/50 rounded-lg">
              <p className="text-sm text-muted-foreground">Családi használat</p>
              <p className="text-2xl font-bold">{familyYes} fő ({familyRate}%)</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default Usage;
