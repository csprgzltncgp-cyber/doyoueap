import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { GaugeChart } from '@/components/ui/gauge-chart';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';

interface PDFExportRendererProps {
  auditData: any;
  responses: any[];
}

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
  const overallUnderstanding = calculateAverage(
    [...usedResponses, ...notUsedResponses]
      .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
      .filter(v => v !== undefined)
  );

  // Trust metrics
  const overallAnonymity = calculateAverage(
    [...usedResponses, ...notUsedResponses]
      .map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity)
      .filter(v => v !== undefined)
  );

  // Impact metrics
  const satisfaction = calculateAverage(
    usedResponses.map(r => r.responses?.u_impact_satisfaction).filter(v => v !== undefined)
  );

  const npsScores = usedResponses
    .map(r => r.responses?.u_impact_nps)
    .filter(v => v !== undefined) as number[];
  
  let npsScore = 0;
  if (npsScores.length > 0) {
    const promoters = npsScores.filter(score => score >= 9).length;
    const detractors = npsScores.filter(score => score <= 6).length;
    npsScore = Math.round(((promoters - detractors) / npsScores.length) * 100);
  }

  return (
    <div className="space-y-8 p-8 bg-white" style={{ width: '210mm', minHeight: '297mm' }}>
      {/* Page 1: Overview */}
      <div className="page-break-after">
        <h1 className="text-3xl font-bold mb-6 text-center">EAP Pulse Jelentés</h1>
        <h2 className="text-xl font-semibold mb-4">Összefoglaló</h2>
        
        <div className="grid grid-cols-2 gap-4 mb-6">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Használói Arány</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(usageRate)} maxValue={100} size={150} label={`${usageRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Ismertségi Arány</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(awarenessRate)} maxValue={100} size={150} label={`${awarenessRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Megértés Szintje</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{overallUnderstanding}</div>
                <p className="text-sm text-muted-foreground">1-5 skála</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Válaszadók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{totalCount}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 2: Awareness */}
      <div className="page-break-after">
        <h2 className="text-xl font-semibold mb-4">Ismertség</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Általános Ismertség</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(awarenessRate)} maxValue={100} size={150} label={`${awarenessRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Nem Tudtak Róla</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(redirectRate)} maxValue={100} size={150} label={`${redirectRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Megértés</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{overallUnderstanding}</div>
                <p className="text-sm text-muted-foreground">1-5 skála</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Résztvevők</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{usedResponses.length + notUsedResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 3: Trust */}
      <div className="page-break-after">
        <h2 className="text-xl font-semibold mb-4">Bizalom & Hajlandóság</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Anonimitás Bizalom</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={(parseFloat(overallAnonymity) / 5) * 100} maxValue={100} size={150} label={overallAnonymity} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Használók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{usedResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Nem Használók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{notUsedResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Bizalmi Index</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{overallAnonymity}</div>
                <p className="text-sm text-muted-foreground">1-5 skála</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 4: Usage */}
      <div className="page-break-after">
        <h2 className="text-xl font-semibold mb-4">Használat</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Használói Arány</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={parseFloat(usageRate)} maxValue={100} size={150} label={`${usageRate}%`} />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Használók Száma</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{usedResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 5: Impact */}
      <div className="page-break-after">
        <h2 className="text-xl font-semibold mb-4">Hatás</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">NPS Score</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{npsScore}</div>
                <p className="text-sm text-muted-foreground">-100 to +100</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Elégedettség</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex justify-center">
                <GaugeChart value={(parseFloat(satisfaction) / 5) * 100} maxValue={100} size={150} label={satisfaction} />
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Page 6: Demographics */}
      <div>
        <h2 className="text-xl font-semibold mb-4">Demográfia</h2>
        
        <div className="grid grid-cols-2 gap-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Összesen</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{totalCount}</div>
                <p className="text-sm text-muted-foreground">válaszadó</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Használók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{usedResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Nem Használók</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{notUsedResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="text-sm">Nem Tudtak Róla</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center">
                <div className="text-4xl font-bold">{redirectResponses.length}</div>
                <p className="text-sm text-muted-foreground">fő</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default PDFExportRenderer;
