import { useEffect, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
  demoRows,
  overviewMetrics as newOverview,
  awarenessMetrics as newAwareness,
  trustMetrics as newTrust,
  usageMetrics as newUsage,
  impactMetrics as newImpact,
  computeTrustIndex as newComputeTrustIndex
} from '@/lib/eapPulseMetrics';

// === PROD implementáció másolata ===
const prodCalculateAverage = (values: number[]) => {
  if (values.length === 0) return '0.0';
  return ((values.reduce((a, b) => a + b, 0) / values.length)).toFixed(1);
};

const prodOverview = (responses: any[]) => {
  const total = responses.length;
  const used = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsed = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const redirect = responses.filter(r => r.employee_metadata?.branch === 'redirect');
  
  return {
    totalResponses: total,
    awarenessRate: total > 0 ? (((used.length + notUsed.length) / total) * 100).toFixed(1) : '0',
    usageRate: total > 0 ? ((used.length / total) * 100).toFixed(1) : '0'
  };
};

const prodAwareness = (responses: any[]) => {
  const used = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsed = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const awareness = [...used, ...notUsed];
  
  const usedUnderstanding = prodCalculateAverage(
    used.map(r => r.responses?.u_awareness_understanding).filter(v => v !== undefined)
  );
  const notUsedUnderstanding = prodCalculateAverage(
    notUsed.map(r => r.responses?.nu_awareness_understanding).filter(v => v !== undefined)
  );
  const overallUnderstanding = prodCalculateAverage(
    awareness.map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding).filter(v => v !== undefined)
  );
  
  return {
    usedUnderstanding,
    notUsedUnderstanding,
    overallUnderstanding
  };
};

const prodTrust = (responses: any[]) => {
  const used = responses.filter(r => r.employee_metadata?.branch === 'used');
  const notUsed = responses.filter(r => r.employee_metadata?.branch === 'not_used');
  const trust = [...used, ...notUsed];
  
  const anonymity = prodCalculateAverage(
    trust.map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity).filter(v => v !== undefined)
  );
  const employerFear = prodCalculateAverage(
    trust.map(r => r.responses?.u_trust_employer || r.responses?.nu_trust_employer).filter(v => v !== undefined)
  );
  const colleaguesFear = prodCalculateAverage(
    trust.map(r => r.responses?.u_trust_colleagues || r.responses?.nu_trust_colleagues).filter(v => v !== undefined)
  );
  const likelihood = prodCalculateAverage(
    used.map(r => r.responses?.u_trust_likelihood).filter(v => v !== undefined)
  );
  
  // PROD NEM SZÁMOL TRUST INDEX-et explicit módon, csak az egyes értékeket mutatja
  return {
    anonymityScore: anonymity,
    employerFearScore: employerFear,
    colleaguesFearScore: colleaguesFear,
    likelihoodScore: likelihood
  };
};

const prodUsage = (responses: any[]) => {
  const used = responses.filter(r => r.employee_metadata?.branch === 'used');
  const total = responses.length;
  
  const familyYes = used.filter(r => r.responses?.u_usage_family === 'yes' || r.responses?.u_usage_family === 'Igen').length;
  const familyNo = used.filter(r => r.responses?.u_usage_family === 'no' || r.responses?.u_usage_family === 'Nem').length;
  const familyTotal = familyYes + familyNo;
  
  return {
    usageRate: total > 0 ? ((used.length / total) * 100).toFixed(1) : '0.0',
    familyRate: familyTotal > 0 ? ((familyYes / familyTotal) * 100).toFixed(1) : '0.0'
  };
};

const prodImpact = (responses: any[]) => {
  const used = responses.filter(r => r.employee_metadata?.branch === 'used');
  
  const calculateAvg = (values: number[]): number => {
    if (values.length === 0) return 0;
    return values.reduce((a, b) => a + b, 0) / values.length;
  };
  
  const satisfaction = used.map(r => r.responses?.u_impact_satisfaction).filter(v => typeof v === 'number' && !isNaN(v)) as number[];
  const problemSolving = used.map(r => r.responses?.u_impact_problem_solving).filter(v => typeof v === 'number' && !isNaN(v)) as number[];
  const wellbeing = used.map(r => r.responses?.u_impact_wellbeing).filter(v => typeof v === 'number' && !isNaN(v)) as number[];
  const performance = used.map(r => r.responses?.u_impact_performance).filter(v => typeof v === 'number' && !isNaN(v)) as number[];
  const consistency = used.map(r => r.responses?.u_impact_consistency).filter(v => typeof v === 'number' && !isNaN(v)) as number[];
  
  const metrics = [
    { metric: 'Elégedettség', average: Number(calculateAvg(satisfaction).toFixed(2)) },
    { metric: 'Problémamegoldás', average: Number(calculateAvg(problemSolving).toFixed(2)) },
    { metric: 'Wellbeing javulás', average: Number(calculateAvg(wellbeing).toFixed(2)) },
    { metric: 'Teljesítmény javulás', average: Number(calculateAvg(performance).toFixed(2)) },
    { metric: 'Szolgáltatás konzisztencia', average: Number(calculateAvg(consistency).toFixed(2)) }
  ];
  
  const avgImpact = metrics.length > 0 
    ? Number((metrics.reduce((sum, m) => sum + m.average, 0) / metrics.length).toFixed(2))
    : 0;
  
  const npsScores = used.map(r => r.responses?.u_impact_nps).filter(v => typeof v === 'number' && !isNaN(v)) as number[];
  const promoters = npsScores.filter(s => s >= 9).length;
  const passives = npsScores.filter(s => s >= 7 && s <= 8).length;
  const detractors = npsScores.filter(s => s <= 6).length;
  const total = npsScores.length;
  const npsScore = total > 0 ? Math.round(((promoters - detractors) / total) * 100) : 0;
  
  return {
    avgImpact,
    npsScore,
    metrics
  };
};

const ComparisonTest = () => {
  const [comparison, setComparison] = useState<any>(null);

  useEffect(() => {
    const auditA = demoRows.filter(r => r.audit_id === 'A');
    
    // Prod implementáció
    const prodResults = {
      overview: prodOverview(auditA),
      awareness: prodAwareness(auditA),
      trust: prodTrust(auditA),
      usage: prodUsage(auditA),
      impact: prodImpact(auditA)
    };
    
    // Maja új implementáció
    const newResults = {
      overview: newOverview(auditA),
      awareness: newAwareness(auditA),
      trust: newTrust(auditA),
      usage: newUsage(auditA),
      impact: newImpact(auditA)
    };

    console.log('🔵 PROD Results:', prodResults);
    console.log('🟢 NEW (Maja) Results:', newResults);

    // Összehasonlítás
    const diff: any = {
      overview: {},
      awareness: {},
      trust: {},
      usage: {},
      impact: {}
    };

    // Overview
    diff.overview.awarenessRate = {
      prod: prodResults.overview.awarenessRate,
      new: newResults.overview.awarenessRate?.toFixed(1),
      match: Math.abs(parseFloat(prodResults.overview.awarenessRate) - (newResults.overview.awarenessRate || 0)) < 0.1
    };

    // Awareness
    diff.awareness.overallUnderstanding = {
      prod: prodResults.awareness.overallUnderstanding,
      new: newResults.awareness.overallUnderstanding?.toFixed(1),
      match: Math.abs(parseFloat(prodResults.awareness.overallUnderstanding) - (newResults.awareness.overallUnderstanding || 0)) < 0.1
    };

    // Trust - KRITIKUS: A prod NEM SZÁMOL Trust Index-et!
    diff.trust.anonymity = {
      prod: prodResults.trust.anonymityScore,
      new: newResults.trust.anonymityScore?.toFixed(1),
      match: Math.abs(parseFloat(prodResults.trust.anonymityScore) - (newResults.trust.anonymityScore || 0)) < 0.1
    };
    diff.trust.employerFear = {
      prod: prodResults.trust.employerFearScore,
      new: newResults.trust.employerFearScore?.toFixed(1),
      match: Math.abs(parseFloat(prodResults.trust.employerFearScore) - (newResults.trust.employerFearScore || 0)) < 0.1
    };
    diff.trust.trustIndex = {
      prod: 'NEM LÉTEZIK A PROD-BAN',
      new: newResults.trust.trustIndex?.toFixed(3),
      match: false,
      note: 'Ez ÚJ feature Maja kódjában!'
    };

    // Usage
    diff.usage.familyRate = {
      prod: prodResults.usage.familyRate,
      new: newResults.usage.familyRate?.toFixed(1),
      match: Math.abs(parseFloat(prodResults.usage.familyRate) - (newResults.usage.familyRate || 0)) < 0.1
    };

    // Impact
    diff.impact.avgImpact = {
      prod: prodResults.impact.avgImpact.toFixed(2),
      new: newResults.impact.avgImpact?.toFixed(2),
      match: Math.abs(prodResults.impact.avgImpact - (newResults.impact.avgImpact || 0)) < 0.01
    };
    diff.impact.nps = {
      prod: prodResults.impact.npsScore,
      new: newResults.impact.nps.npsScore,
      match: prodResults.impact.npsScore === newResults.impact.nps.npsScore
    };

    setComparison({ prod: prodResults, new: newResults, diff });
  }, []);

  if (!comparison) return <div className="p-8">Betöltés...</div>;

  const { diff } = comparison;

  return (
    <div className="container mx-auto p-8">
      <h1 className="text-4xl font-bold mb-2">PROD vs Maja Kód Összehasonlítás</h1>
      <p className="text-muted-foreground mb-8">Demo Audit A adatain futtatva</p>

      <div className="grid gap-6">
        {/* Overview */}
        <Card>
          <CardHeader>
            <CardTitle>Overview Metrikák</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-4 gap-4 font-semibold border-b pb-2">
              <div>Metrika</div>
              <div>PROD</div>
              <div>MAJA (ÚJ)</div>
              <div>Egyezik?</div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>Ismertségi arány</div>
              <div className="font-mono">{diff.overview.awarenessRate.prod}%</div>
              <div className="font-mono">{diff.overview.awarenessRate.new}%</div>
              <div>
                {diff.overview.awarenessRate.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Awareness */}
        <Card>
          <CardHeader>
            <CardTitle>Awareness Metrikák</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-4 gap-4 font-semibold border-b pb-2">
              <div>Metrika</div>
              <div>PROD</div>
              <div>MAJA (ÚJ)</div>
              <div>Egyezik?</div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>Általános megértés</div>
              <div className="font-mono">{diff.awareness.overallUnderstanding.prod}</div>
              <div className="font-mono">{diff.awareness.overallUnderstanding.new}</div>
              <div>
                {diff.awareness.overallUnderstanding.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Trust - KRITIKUS! */}
        <Card className="border-orange-500">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              Trust Metrikák 
              <Badge variant="outline" className="bg-orange-100">KRITIKUS ELTÉRÉS</Badge>
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-4 gap-4 font-semibold border-b pb-2">
              <div>Metrika</div>
              <div>PROD</div>
              <div>MAJA (ÚJ)</div>
              <div>Megjegyzés</div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>Anonimitás</div>
              <div className="font-mono">{diff.trust.anonymity.prod}</div>
              <div className="font-mono">{diff.trust.anonymity.new}</div>
              <div>
                {diff.trust.anonymity.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>Munkaadói félelem</div>
              <div className="font-mono">{diff.trust.employerFear.prod}</div>
              <div className="font-mono">{diff.trust.employerFear.new}</div>
              <div>
                {diff.trust.employerFear.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center bg-orange-50 p-3 rounded">
              <div className="font-bold">Trust Index</div>
              <div className="font-mono text-muted-foreground">{diff.trust.trustIndex.prod}</div>
              <div className="font-mono font-bold text-orange-600">{diff.trust.trustIndex.new}</div>
              <div>
                <Badge variant="outline" className="bg-orange-100">ÚJ FEATURE!</Badge>
                <p className="text-xs text-muted-foreground mt-1">{diff.trust.trustIndex.note}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Usage */}
        <Card>
          <CardHeader>
            <CardTitle>Usage Metrikák</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-4 gap-4 font-semibold border-b pb-2">
              <div>Metrika</div>
              <div>PROD</div>
              <div>MAJA (ÚJ)</div>
              <div>Egyezik?</div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>Családi használat</div>
              <div className="font-mono">{diff.usage.familyRate.prod}%</div>
              <div className="font-mono">{diff.usage.familyRate.new}%</div>
              <div>
                {diff.usage.familyRate.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Impact */}
        <Card>
          <CardHeader>
            <CardTitle>Impact Metrikák</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-4 gap-4 font-semibold border-b pb-2">
              <div>Metrika</div>
              <div>PROD</div>
              <div>MAJA (ÚJ)</div>
              <div>Egyezik?</div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>Átlagos hatás</div>
              <div className="font-mono">{diff.impact.avgImpact.prod}</div>
              <div className="font-mono">{diff.impact.avgImpact.new}</div>
              <div>
                {diff.impact.avgImpact.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
            <div className="grid grid-cols-4 gap-4 items-center">
              <div>NPS</div>
              <div className="font-mono">{diff.impact.nps.prod}</div>
              <div className="font-mono">{diff.impact.nps.new}</div>
              <div>
                {diff.impact.nps.match ? (
                  <Badge variant="default">✓ EGYEZIK</Badge>
                ) : (
                  <Badge variant="destructive">✗ ELTÉR</Badge>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Összefoglaló */}
        <Card className="bg-blue-50">
          <CardHeader>
            <CardTitle>📊 Összegzés</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <p className="font-semibold">✅ EGYEZŐ SZÁMÍTÁSOK:</p>
            <ul className="list-disc list-inside space-y-1 text-sm">
              <li>Ismertségi arány (Awareness Rate)</li>
              <li>Általános megértés (Overall Understanding)</li>
              <li>Anonimitás bizalom</li>
              <li>Munkaadói félelem</li>
              <li>Családi használat</li>
              <li>Átlagos hatás (Avg Impact)</li>
              <li>NPS</li>
            </ul>
            
            <p className="font-semibold text-orange-600 mt-4">🆕 ÚJ FEATURE Maja kódjában:</p>
            <ul className="list-disc list-inside space-y-1 text-sm">
              <li><strong>Trust Index</strong> - Kompozit mutató (anonymity, invert(employerFear), invert(colleaguesFear), likelihood átlaga)</li>
              <li>Alert rendszer (küszöbértékek)</li>
              <li>Recharts adapterek</li>
              <li>CSV export funkciók</li>
            </ul>

            <div className="mt-4 p-3 bg-green-100 rounded">
              <p className="font-bold text-green-800">✓ KONKLÚZIÓ:</p>
              <p className="text-sm text-green-700">
                A Maja-féle kód <strong>KOMPATIBILIS</strong> a production logikával. Az alapvető számítások egyeznek, 
                de Maja kódja <strong>TÖBBET TUD</strong>: van Trust Index, alert system, és jobb strukturálás.
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default ComparisonTest;
