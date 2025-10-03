import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, RadarChart, PolarGrid, PolarAngleAxis, PolarRadiusAxis, Radar } from 'recharts';

interface PDFExportRendererProps {
  auditData: any;
  responses: any[];
}

const COLORS = ['#8b5cf6', '#6366f1', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];

const PDFExportRenderer = ({ auditData, responses }: PDFExportRendererProps) => {
  // Helper functions
  const calculateAverage = (values: number[]) => {
    if (values.length === 0) return '0.0';
    return ((values.reduce((a, b) => a + b, 0) / values.length)).toFixed(1);
  };

  // Filter responses by branch
  const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const redirectResponses = responses.filter(r => r.employee_metadata?.branch === 'redirect');
  const totalCount = responses.length;

  // Overview metrics
  const usageRate = totalCount > 0 ? ((usedResponses.length / totalCount) * 100).toFixed(1) : '0.0';
  const awarenessRate = totalCount > 0 ? (((usedResponses.length + notUsedResponses.length) / totalCount) * 100).toFixed(1) : '0.0';
  const redirectRate = totalCount > 0 ? ((redirectResponses.length / totalCount) * 100).toFixed(1) : '0.0';

  // Awareness metrics
  const awarenessResponses = [...usedResponses, ...notUsedResponses];
  const overallUnderstanding = calculateAverage(
    awarenessResponses
      .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
      .filter(v => v !== undefined)
  );

  const usedUnderstandingScore = calculateAverage(
    usedResponses.map(r => r.responses?.u_awareness_understanding).filter(v => v !== undefined)
  );

  const notUsedUnderstandingScore = calculateAverage(
    notUsedResponses.map(r => r.responses?.nu_awareness_understanding).filter(v => v !== undefined)
  );

  // Awareness source data
  const sourceData: { [key: string]: number } = {};
  awarenessResponses.forEach(r => {
    const sources = r.responses?.u_awareness_source || r.responses?.nu_awareness_source;
    if (Array.isArray(sources)) {
      sources.forEach(source => {
        sourceData[source] = (sourceData[source] || 0) + 1;
      });
    }
  });

  const sourceChartData = Object.entries(sourceData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value)
    .slice(0, 6);

  // Trust metrics
  const overallAnonymity = calculateAverage(
    awarenessResponses
      .map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity)
      .filter(v => v !== undefined)
  );

  const usedAnonymityScore = calculateAverage(
    usedResponses.map(r => r.responses?.u_trust_anonymity).filter(v => v !== undefined)
  );

  const notUsedAnonymityScore = calculateAverage(
    notUsedResponses.map(r => r.responses?.nu_trust_anonymity).filter(v => v !== undefined)
  );

  // Usage metrics
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

  const topicData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const topics = r.responses?.u_usage_topic;
    if (Array.isArray(topics)) {
      topics.forEach(topic => {
        topicData[topic] = (topicData[topic] || 0) + 1;
      });
    }
  });

  const topicChartData = Object.entries(topicData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value)
    .slice(0, 6);

  const channelData: { [key: string]: number } = {};
  usedResponses.forEach(r => {
    const channels = r.responses?.u_usage_channel;
    if (Array.isArray(channels)) {
      channels.forEach(channel => {
        channelData[channel] = (channelData[channel] || 0) + 1;
      });
    }
  });

  const channelChartData = Object.entries(channelData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value)
    .slice(0, 6);

  const familyYes = usedResponses.filter(r => r.responses?.u_usage_family === 'yes' || r.responses?.u_usage_family === 'Igen').length;
  const familyNo = usedResponses.filter(r => r.responses?.u_usage_family === 'no' || r.responses?.u_usage_family === 'Nem').length;

  // Impact metrics
  const satisfaction = calculateAverage(
    usedResponses.map(r => r.responses?.u_impact_satisfaction).filter(v => v !== undefined)
  );

  const problemSolving = calculateAverage(
    usedResponses.map(r => r.responses?.u_impact_problem_solving).filter(v => v !== undefined)
  );

  const wellbeing = calculateAverage(
    usedResponses.map(r => r.responses?.u_impact_wellbeing).filter(v => v !== undefined)
  );

  const performance = calculateAverage(
    usedResponses.map(r => r.responses?.u_impact_performance).filter(v => v !== undefined)
  );

  const consistency = calculateAverage(
    usedResponses.map(r => r.responses?.u_impact_consistency).filter(v => v !== undefined)
  );

  const npsScores = usedResponses
    .map(r => r.responses?.u_impact_nps)
    .filter(v => v !== undefined) as number[];
  
  let npsScore = 0;
  let promoters = 0;
  let passives = 0;
  let detractors = 0;

  if (npsScores.length > 0) {
    promoters = npsScores.filter(score => score >= 9).length;
    passives = npsScores.filter(score => score >= 7 && score <= 8).length;
    detractors = npsScores.filter(score => score <= 6).length;
    npsScore = Math.round(((promoters - detractors) / npsScores.length) * 100);
  }

  const impactRadarData = [
    { metric: 'Elégedettség', value: parseFloat(satisfaction), fullMark: 5 },
    { metric: 'Probléma megoldás', value: parseFloat(problemSolving), fullMark: 5 },
    { metric: 'Jóllét', value: parseFloat(wellbeing), fullMark: 5 },
    { metric: 'Teljesítmény', value: parseFloat(performance), fullMark: 5 },
    { metric: 'Konzisztencia', value: parseFloat(consistency), fullMark: 5 },
  ];

  // Motivation metrics
  const reasonsData: { [key: string]: number } = {};
  notUsedResponses.forEach(r => {
    const reasons = r.responses?.nu_motivation_reasons;
    if (Array.isArray(reasons)) {
      reasons.forEach(reason => {
        reasonsData[reason] = (reasonsData[reason] || 0) + 1;
      });
    }
  });

  const reasonsChartData = Object.entries(reasonsData)
    .map(([name, value]) => ({ name, value }))
    .sort((a, b) => b.value - a.value)
    .slice(0, 6);

  return (
    <div className="space-y-8 p-8 bg-white" style={{ width: '210mm' }}>
      {/* Page 1: Overview */}
      <div className="page-break-after space-y-6">
        <h1 className="text-3xl font-bold mb-6 text-center">EAP Pulse Jelentés</h1>
        <h2 className="text-xl font-semibold mb-4">Összefoglaló</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Használói Arány</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(usageRate)} maxValue={100} size={120} label={`${usageRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Ismertségi Arány</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(awarenessRate)} maxValue={100} size={120} label={`${awarenessRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Megértés Szintje</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{overallUnderstanding}</div>
                <p className="text-xs text-muted-foreground">1-5 skála</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Válaszadók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{totalCount}</div>
                <p className="text-xs text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 2: Awareness */}
      <div className="page-break-after space-y-6">
        <h2 className="text-xl font-semibold mb-4">Ismertség</h2>
        
        <div className="grid grid-cols-2 gap-4 mb-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Általános Ismertség</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(awarenessRate)} maxValue={100} size={120} label={`${awarenessRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Megértés</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{overallUnderstanding}</div>
                <p className="text-xs text-muted-foreground">1-5 skála</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Használók megértése</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{usedUnderstandingScore}</div>
                <p className="text-xs text-muted-foreground">{usedResponses.length} fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Nem használók megértése</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{notUsedUnderstandingScore}</div>
                <p className="text-xs text-muted-foreground">{notUsedResponses.length} fő</p>
              </div>
            </CardContent>
          </Card>
        </div>

        {sourceChartData.length > 0 && (
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Információ forrásai</CardTitle>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={200}>
                <BarChart data={sourceChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={80} fontSize={10} />
                  <YAxis fontSize={10} />
                  <Tooltip />
                  <Bar dataKey="value" fill="#8b5cf6" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        )}
      </div>

      {/* Page 3: Trust */}
      <div className="page-break-after space-y-6">
        <h2 className="text-xl font-semibold mb-4">Bizalom & Hajlandóság</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Anonimitás Bizalom</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={(parseFloat(overallAnonymity) / 5) * 100} maxValue={100} size={120} label={overallAnonymity} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Használók bizalma</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{usedAnonymityScore}</div>
                <p className="text-xs text-muted-foreground">{usedResponses.length} fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Nem használók bizalma</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{notUsedAnonymityScore}</div>
                <p className="text-xs text-muted-foreground">{notUsedResponses.length} fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Bizalmi Index</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{overallAnonymity}</div>
                <p className="text-xs text-muted-foreground">1-5 skála</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 4: Usage */}
      <div className="page-break-after space-y-6">
        <h2 className="text-xl font-semibold mb-4">Használat</h2>
        
        <div className="grid grid-cols-2 gap-4 mb-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Használói Arány</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(usageRate)} maxValue={100} size={120} label={`${usageRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Családi használat</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{familyYes}</div>
                <p className="text-xs text-muted-foreground">család is használta</p>
              </div>
            </CardContent>
          </Card>
        </div>

        {frequencyChartData.length > 0 && (
          <Card className="mb-4">
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Használat gyakorisága</CardTitle>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={180}>
                <BarChart data={frequencyChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={80} fontSize={10} />
                  <YAxis fontSize={10} />
                  <Tooltip />
                  <Bar dataKey="value" fill="#6366f1" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        )}

        <div className="grid grid-cols-2 gap-4">
          {topicChartData.length > 0 && (
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm">Témakörök</CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={150}>
                  <PieChart>
                    <Pie data={topicChartData} dataKey="value" nameKey="name" cx="50%" cy="50%" outerRadius={50} label={(entry) => entry.name.substring(0, 15)}>
                      {topicChartData.map((_, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          )}

          {channelChartData.length > 0 && (
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm">Csatornák</CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={150}>
                  <PieChart>
                    <Pie data={channelChartData} dataKey="value" nameKey="name" cx="50%" cy="50%" outerRadius={50} label={(entry) => entry.name.substring(0, 15)}>
                      {channelChartData.map((_, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          )}
        </div>
      </div>

      {/* Page 5: Impact */}
      <div className="page-break-after space-y-6">
        <h2 className="text-xl font-semibold mb-4">Hatás</h2>
        
        <div className="grid grid-cols-2 gap-4 mb-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">NPS Score</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{npsScore}</div>
                <p className="text-xs text-muted-foreground">-100 to +100</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Elégedettség</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={(parseFloat(satisfaction) / 5) * 100} maxValue={100} size={120} label={satisfaction} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Promoters</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{promoters}</div>
                <p className="text-xs text-muted-foreground">9-10 pontot adók</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Detractors</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-2xl font-bold">{detractors}</div>
                <p className="text-xs text-muted-foreground">0-6 pontot adók</p>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm">Hatás mutatók átlaga</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={200}>
              <RadarChart data={impactRadarData}>
                <PolarGrid />
                <PolarAngleAxis dataKey="metric" fontSize={10} />
                <PolarRadiusAxis domain={[0, 5]} fontSize={10} />
                <Radar name="Átlag" dataKey="value" stroke="#8b5cf6" fill="#8b5cf6" fillOpacity={0.6} />
                <Tooltip />
              </RadarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Page 6: Motivation */}
      <div className="page-break-after space-y-6">
        <h2 className="text-xl font-semibold mb-4">Motiváció</h2>
        
        <div className="grid grid-cols-2 gap-4 mb-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Nem használók száma</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{notUsedResponses.length}</div>
                <p className="text-xs text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>

        {reasonsChartData.length > 0 && (
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Nem használat okai</CardTitle>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={200}>
                <BarChart data={reasonsChartData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" angle={-45} textAnchor="end" height={100} fontSize={10} />
                  <YAxis fontSize={10} />
                  <Tooltip />
                  <Bar dataKey="value" fill="#ef4444" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        )}
      </div>

      {/* Page 7: Demographics */}
      <div className="space-y-6">
        <h2 className="text-xl font-semibold mb-4">Demográfia</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Összesen</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{totalCount}</div>
                <p className="text-xs text-muted-foreground">válaszadó</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Használók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{usedResponses.length}</div>
                <p className="text-xs text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Nem Használók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{notUsedResponses.length}</div>
                <p className="text-xs text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm">Nem Tudtak Róla</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-3xl font-bold">{redirectResponses.length}</div>
                <p className="text-xs text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default PDFExportRenderer;