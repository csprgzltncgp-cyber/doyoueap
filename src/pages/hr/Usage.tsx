import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Download, Activity, Clock, Users, MessageSquare, Phone } from 'lucide-react';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { Progress } from '@/components/ui/progress';

interface UsageProps {
  selectedAuditId: string;
}

const Usage = ({ selectedAuditId }: UsageProps) => {
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

  // Csak használók
  const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  const totalCount = responses.length;
  const usageRate = totalCount > 0 ? ((usedResponses.length / totalCount) * 100).toFixed(1) : '0.0';

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

  return (
    <div className="space-y-6">
      {/* Fejléc */}
      <div>
        <h2 className="text-2xl font-bold mb-2">Használat Részletes Elemzése</h2>
        <p className="text-muted-foreground text-sm">
          Az EAP program használati szokásainak, csatornáinak és témáinak átfogó kiértékelése
        </p>
      </div>

      {/* 1. sor: Fő használati mutatók */}
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
          <CardContent>
            <GaugeChart 
              value={parseFloat(usageRate)} 
              maxValue={100}
              size={220}
              label={`${usageRate}%`}
              sublabel={`${usedResponses.length} / ${totalCount} fő`}
              cornerRadius={30}
            />
            <p className="text-xs text-muted-foreground text-center mt-4">
              A válaszolók közül ennyien használták a programot
            </p>
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
          <CardContent>
            <GaugeChart 
              value={parseFloat(familyRate)} 
              maxValue={100}
              size={220}
              label={`${familyRate}%`}
              sublabel={`${familyYes} / ${familyTotal} fő`}
              cornerRadius={30}
            />
            <p className="text-xs text-muted-foreground text-center mt-4">
              A használók közül ennyien családtaggal együtt vették igénybe
            </p>
          </CardContent>
        </Card>

        {/* Leggyakoribb Témakör */}
        <Card className="relative overflow-hidden" id="top-topic-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) 60%, transparent 60%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
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
          <CardContent className="space-y-4 relative z-10">
            <div className="text-center">
              {topTopic ? (
                <>
                  <div className="text-3xl font-bold mb-2" style={{ color: 'hsl(var(--chart-2))' }}>
                    {topTopic.name}
                  </div>
                  <p className="text-sm text-muted-foreground">
                    {topTopic.value} említés ({usedResponses.length > 0 ? ((topTopic.value / usedResponses.length) * 100).toFixed(0) : 0}%)
                  </p>
                  <p className="text-xs text-muted-foreground mt-2">
                    {parseFloat(avgTopicsPerUser) >= 2 ? 'A használók átlagosan több témában is igénybe veszik a szolgáltatást' : 'A használók általában egy-két témában veszik igénybe'}
                  </p>
                </>
              ) : (
                <p className="text-sm text-muted-foreground">Nincs adat</p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Leggyakoribb Csatorna */}
        <Card className="relative overflow-hidden" id="top-channel-card">
          <div 
            className="absolute inset-0 transition-all duration-500"
            style={{
              background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) 60%, transparent 60%, transparent 100%)`,
              opacity: 0.1
            }}
          />
          <CardHeader className="relative z-10">
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
          <CardContent className="space-y-4 relative z-10">
            <div className="text-center">
              {topChannel ? (
                <>
                  <div className="text-3xl font-bold mb-2" style={{ color: 'hsl(var(--chart-2))' }}>
                    {topChannel.name}
                  </div>
                  <p className="text-sm text-muted-foreground">
                    {topChannel.value} használat ({usedResponses.length > 0 ? ((topChannel.value / usedResponses.length) * 100).toFixed(0) : 0}%)
                  </p>
                  <p className="text-xs text-muted-foreground mt-2">
                    {parseFloat(avgChannelsPerUser) >= 2 ? 'A használók többféle csatornát is kipróbálnak' : 'A használók általában egy csatornát preferálnak'}
                  </p>
                </>
              ) : (
                <p className="text-sm text-muted-foreground">Nincs adat</p>
              )}
            </div>
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
            <CardDescription>Milyen gyakran vették igénybe a szolgáltatást</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[350px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={frequencyChartData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis dataKey="name" type="category" width={150} />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[0, 8, 8, 0]} />
                </BarChart>
              </ResponsiveContainer>
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
                  label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
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
            {topicChartData.slice(0, 6).map((entry, index) => (
              <div key={index} className="flex items-center gap-2">
                <div 
                  className="w-3 h-3 rounded-full" 
                  style={{ backgroundColor: `hsl(var(--chart-${(index % 4) + 1}))` }}
                />
                <span className="text-sm text-foreground">
                  {entry.name}: {entry.value}
                </span>
              </div>
            ))}
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
            <div className="h-[350px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={channelChartData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis dataKey="name" type="category" width={150} />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[0, 8, 8, 0]} />
                </BarChart>
              </ResponsiveContainer>
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
            <div className="h-[350px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={timeToCareChartData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" />
                  <YAxis dataKey="name" type="category" width={150} />
                  <Tooltip />
                  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[0, 8, 8, 0]} />
                </BarChart>
              </ResponsiveContainer>
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
                  ? '✓ Magas családi bevonás - a program hatékonyan terjed a családon belül' 
                  : parseFloat(familyRate) >= 15
                  ? '→ Közepes családi bevonás'
                  : 'ℹ Alacsony családi bevonás - érdemes népszerűsíteni a családi használatot'}
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
              <p className="text-sm text-muted-foreground">Témakörök száma</p>
              <p className="text-2xl font-bold">{topicChartData.length}</p>
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
