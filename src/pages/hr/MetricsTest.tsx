import { useEffect, useState } from 'react';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  demoRows,
  selfCheck,
  overviewMetrics,
  awarenessMetrics,
  trustMetrics,
  usageMetrics,
  impactMetrics,
  computeTrustIndex,
  compareTwoAudits
} from '@/lib/eapPulseMetrics';

const MetricsTest = () => {
  const [results, setResults] = useState<any>(null);

  useEffect(() => {
    console.log('🧪 Running metrics self-check...');
    const checkResults = selfCheck();
    console.log('✅ Self-check results:', checkResults);
    setResults(checkResults);
  }, []);

  const runManualTests = () => {
    const A = demoRows.filter(r => r.audit_id === 'A');
    const B = demoRows.filter(r => r.audit_id === 'B');

    console.log('📊 Manual test results:');
    console.log('Audit A Overview:', overviewMetrics(A));
    console.log('Audit A Awareness:', awarenessMetrics(A));
    console.log('Audit A Trust:', trustMetrics(A));
    console.log('Audit A Usage:', usageMetrics(A));
    console.log('Audit A Impact:', impactMetrics(A));
    console.log('Audit A Trust Index:', computeTrustIndex(A));
    
    console.log('Audit B Overview:', overviewMetrics(B));
    console.log('Audit B Awareness:', awarenessMetrics(B));
    console.log('Audit B Trust:', trustMetrics(B));
    console.log('Audit B Usage:', usageMetrics(B));
    console.log('Audit B Impact:', impactMetrics(B));
    console.log('Audit B Trust Index:', computeTrustIndex(B));

    console.log('Comparison A vs B:', compareTwoAudits(
      { id: 'A', rows: A },
      { id: 'B', rows: B }
    ));
  };

  if (!results) {
    return (
      <div className="container mx-auto p-8">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
          <p className="text-muted-foreground">Running metrics tests...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="container mx-auto p-8">
      <div className="mb-8">
        <h1 className="text-4xl font-bold mb-2">EAP Pulse Metrics Test</h1>
        <p className="text-muted-foreground">
          Demo dataset results - Check console for detailed output
        </p>
      </div>

      <div className="mb-6">
        <Button onClick={runManualTests}>
          Run Manual Tests (check console)
        </Button>
      </div>

      <div className="grid gap-6">
        {/* Audit A Results */}
        <Card className="p-6">
          <h2 className="text-2xl font-bold mb-4">Audit A Results</h2>
          
          <div className="grid gap-4">
            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Overview</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.A.overview, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Awareness</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.A.awareness, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Trust</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.A.trust, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Usage</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.A.usage, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Impact</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.A.impact, null, 2)}
              </pre>
            </div>
          </div>
        </Card>

        {/* Audit B Results */}
        <Card className="p-6">
          <h2 className="text-2xl font-bold mb-4">Audit B Results</h2>
          
          <div className="grid gap-4">
            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Overview</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.B.overview, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Awareness</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.B.awareness, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Trust</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.B.trust, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Usage</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.B.usage, null, 2)}
              </pre>
            </div>

            <div className="border-b pb-3">
              <h3 className="font-semibold mb-2">Impact</h3>
              <pre className="text-sm bg-muted p-3 rounded overflow-auto">
                {JSON.stringify(results.B.impact, null, 2)}
              </pre>
            </div>
          </div>
        </Card>

        {/* Comparison */}
        <Card className="p-6">
          <h2 className="text-2xl font-bold mb-4">A vs B Comparison</h2>
          <pre className="text-sm bg-muted p-3 rounded overflow-auto">
            {JSON.stringify(results.compare, null, 2)}
          </pre>
        </Card>
      </div>
    </div>
  );
};

export default MetricsTest;
