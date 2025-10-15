/*
 * EAP Pulse – Egyfájlos mérési utilok + config + demoRows + tesztek (kommentben)
 * Prod‑paritás beállításokkal (Trust Index ✔, invert ✔, Overall Understanding = egyszerű átlag)
 */

// =========================
// Típusok
// =========================
export type Branch = 'redirect' | 'not_used' | 'used';

export interface Audit {
  id: string;
  hr_user_id: string;
  questionnaire_id: string;
  access_mode: 'public_link' | 'email';
  has_qr_assets?: boolean;
  gift_id?: string | null;
  start_date?: string; // ISO
  expires_at?: string; // ISO
}

export interface ResponseRow {
  id: string;
  audit_id: string;
  submitted_at?: string;
  participant_id_hash?: string;
  employee_metadata: { branch: Branch; [k: string]: any };
  responses: Record<string, any>;
}

// =========================
// Konfig – Trust Index komponensek (prod‑paritás)
// =========================
export const trustConfig = {
  components: <const>[
    { key: 'anonymity',      type: 'direct'  },  // 1–5
    { key: 'employerFear',   type: 'invert5' },  // 1–5 → invert
    { key: 'colleaguesFear', type: 'invert5' },
    { key: 'likelihood',     type: 'direct'  }
  ]
};

// =========================
// Alap helper függvények
// =========================
const toNum = (v: any): number | null => { const n = Number(v); return Number.isFinite(n) ? n : null; };
export const calculateAverage = (values: any[]): number | null => {
  const nums = (values || []).map(toNum).filter((v): v is number => v !== null);
  if (nums.length === 0) return null;
  return nums.reduce((a, b) => a + b, 0) / nums.length;
};
export const invert5 = (v: number | null): number | null => (v == null ? null : 6 - v);

// =========================
// Branch szeparálás
// =========================
export const splitByBranch = (rows: ResponseRow[]) => ({
  used: rows.filter(r => r.employee_metadata?.branch === 'used'),
  notUsed: rows.filter(r => r.employee_metadata?.branch === 'not_used'),
  redirect: rows.filter(r => r.employee_metadata?.branch === 'redirect')
});

// =========================
// Overview
// =========================
export const overviewMetrics = (rows: ResponseRow[]) => {
  const total = rows.length;
  const { used, notUsed, redirect } = splitByBranch(rows);
  const usedCount = used.length, notUsedCount = notUsed.length, redirectCount = redirect.length;
  const pct = (n: number) => (total === 0 ? null : (n / total) * 100);
  return {
    totalResponses: total,
    awarenessRate: pct(usedCount + notUsedCount),
    usageRate: pct(usedCount),
    redirectRate: pct(redirectCount),
    notUsedRate: pct(notUsedCount),
    usedRate: pct(usedCount)
  };
};

// =========================
// Awareness
// =========================
export const awarenessMetrics = (rows: ResponseRow[]) => {
  const { used, notUsed } = splitByBranch(rows);
  const usedUnderstandingVals = used.map(r => r.responses.u_awareness_understanding);
  const notUsedUnderstandingVals = notUsed.map(r => r.responses.nu_awareness_understanding);

  const usedUnderstanding = calculateAverage(usedUnderstandingVals);
  const notUsedUnderstanding = calculateAverage(notUsedUnderstandingVals);

  // PROD PARITÁS: OverallUnderstanding = EGYSZERŰ ÁTLAG a két átlagból (nem súlyozott)
  const overallUnderstanding = calculateAverage([usedUnderstanding, notUsedUnderstanding]);

  const howToUseScore = calculateAverage(used.map(r => r.responses.u_awareness_how_to_use));
  const accessibilityScore = calculateAverage(used.map(r => r.responses.u_awareness_accessibility));

  const sourceCounts: Record<string, number> = {};
  rows.forEach(r => {
    const list = r.responses.u_awareness_source || r.responses.nu_awareness_source || [];
    (list as any[]).forEach(s => {
      const key = String(s);
      sourceCounts[key] = (sourceCounts[key] || 0) + 1;
    });
  });

  return { usedUnderstanding, notUsedUnderstanding, overallUnderstanding, howToUseScore, accessibilityScore, sourceCounts };
};

// =========================
// Trust & Willingness
// =========================
export const computeTrustIndex = (rows: ResponseRow[]) => {
  const get = (k: string) => {
    switch (k) {
      case 'anonymity':
        return calculateAverage(rows.map(r => r.responses.u_trust_anonymity ?? r.responses.nu_trust_anonymity));
      case 'employerFear':
        return calculateAverage(rows.map(r => r.responses.u_trust_employer ?? r.responses.nu_trust_employer));
      case 'colleaguesFear':
        return calculateAverage(rows.map(r => r.responses.u_trust_colleagues ?? r.responses.nu_trust_colleagues));
      case 'likelihood':
        return calculateAverage(rows.map(r => r.responses.u_trust_likelihood ?? r.responses.nu_trust_likelihood));
      default:
        return null;
    }
  };
  const vals: number[] = [];
  trustConfig.components.forEach(c => {
    const v = get(c.key);
    if (v == null) return;
    vals.push(c.type === 'invert5' ? (6 - v) : v);
  });
  return vals.length ? calculateAverage(vals) : null;
};

export const trustMetrics = (rows: ResponseRow[]) => {
  const { used, notUsed } = splitByBranch(rows);

  const anonymityScore = calculateAverage([
    ...used.map(r => r.responses.u_trust_anonymity),
    ...notUsed.map(r => r.responses.nu_trust_anonymity)
  ]);
  const employerFearScore = calculateAverage(rows.map(r => r.responses.u_trust_employer ?? r.responses.nu_trust_employer));
  const colleaguesFearScore = calculateAverage(rows.map(r => r.responses.u_trust_colleagues ?? r.responses.nu_trust_colleagues));
  const likelihoodScore = calculateAverage(rows.map(r => r.responses.u_trust_likelihood ?? r.responses.nu_trust_likelihood));

  const trustIndex = computeTrustIndex(rows);

  const alerts: any[] = [];
  if (trustIndex != null && trustIndex < 3.0) alerts.push({ type: 'error', title: 'Alacsony bizalmi szint', threshold: 3.0, actual: trustIndex, recommendation: 'Fokozott transzparencia, anonimitás kommunikációjának erősítése.' });
  if (anonymityScore != null && anonymityScore < 2.5) alerts.push({ type: 'error', title: 'Alacsony anonimitási bizalom', threshold: 2.5, actual: anonymityScore, recommendation: 'Anonimitás technikai és folyamatbeli garanciák kiemelése.' });
  if (employerFearScore != null && employerFearScore > 3.5) alerts.push({ type: 'warning', title: 'Magas munkaadói félelem', threshold: 3.5, actual: employerFearScore, recommendation: 'Vezetői üzenetek, biztonságos bejelentkezés hangsúlyozása.' });

  return { anonymityScore, employerFearScore, colleaguesFearScore, likelihoodScore, trustIndex, alerts };
};

// =========================
// Usage – used branch
// =========================
export const usageMetrics = (rows: ResponseRow[]) => {
  const { used } = splitByBranch(rows);
  const frequency: Record<string, number> = { '1 alkalom': 0, '2–3 alkalom': 0, '4–6 alkalom': 0, 'Több mint 6 alkalom': 0 };
  used.forEach(r => {
    const n = toNum(r.responses.u_usage_frequency);
    if (n == null) return;
    if (n === 1) frequency['1 alkalom']++;
    else if (n >= 2 && n <= 3) frequency['2–3 alkalom']++;
    else if (n >= 4 && n <= 6) frequency['4–6 alkalom']++;
    else if (n > 6) frequency['Több mint 6 alkalom']++;
  });
  const topicsPerUser = used.map(r => (r.responses.u_usage_topic || []).length);
  const avgTopicsPerUser = calculateAverage(topicsPerUser);
  const familyYes = used.filter(r => (r.responses.u_usage_family || '').toString().toLowerCase() === 'igen').length;
  const familyRate = used.length ? (familyYes / used.length) * 100 : null;
  return { frequency, avgTopicsPerUser, familyRate };
};

// =========================
// Impact – used branch
// =========================
export const impactMetrics = (rows: ResponseRow[]) => {
  const { used } = splitByBranch(rows);
  const fields: Array<[string, string]> = [
    ['Elégedettség', 'u_impact_satisfaction'],
    ['Problémamegoldás', 'u_impact_problem_solving'],
    ['Wellbeing javulás', 'u_impact_wellbeing'],
    ['Teljesítmény javulás', 'u_impact_performance'],
    ['Szolgáltatás konzisztencia', 'u_impact_consistency']
  ];

  const metrics = fields.map(([metric, key]) => ({ metric, average: calculateAverage(used.map(r => r.responses[key])) }));
  const valid = metrics.map(m => m.average).filter((v): v is number => v != null);
  const avgImpact = valid.length ? calculateAverage(valid) : null;

  const npsScores = used.map(r => toNum(r.responses.u_impact_nps)).filter((v): v is number => v != null);
  const nps = (() => {
    const total = npsScores.length;
    if (!total) return { npsScore: null, promoters: 0, passives: 0, detractors: 0 };
    const promoters = npsScores.filter(s => s >= 9).length;
    const passives = npsScores.filter(s => s >= 7 && s <= 8).length;
    const detractors = npsScores.filter(s => s <= 6).length;
    const npsScore = Math.round(((promoters - detractors) / total) * 100);
    return { npsScore, promoters, passives, detractors };
  })();

  const alerts: any[] = [];
  if (avgImpact != null && avgImpact < 2.5) alerts.push({ type: 'error', title: 'Alacsony hatékonyság', threshold: 2.5, actual: avgImpact, recommendation: 'Gyorsabb elérhetőség, jobb illesztés a problématípusokhoz.' });
  if (nps.npsScore != null && nps.npsScore < 0) alerts.push({ type: 'error', title: 'Negatív NPS', actual: nps.npsScore, recommendation: 'Részletes visszajelzések gyűjtése, minőségi javító intézkedések.' });

  return { metrics, avgImpact, nps, alerts };
};

// =========================
// Motivation – not_used branch
// =========================
export const motivationMetrics = (rows: ResponseRow[]) => {
  const { notUsed } = splitByBranch(rows);
  const motivatorCounts: Record<string, number> = {};
  notUsed.forEach(r => {
    const list = r.responses.nu_motivation_what || [];
    (list as any[]).forEach(item => { const key = String(item); motivatorCounts[key] = (motivatorCounts[key] || 0) + 1; });
  });
  const expertPreference: Record<string, number> = {};
  notUsed.forEach(r => { const v = r.responses.nu_motivation_expert; if (v != null) { const key = String(v); expertPreference[key] = (expertPreference[key] || 0) + 1; } });
  const channelPreference: Record<string, number> = {};
  notUsed.forEach(r => { const v = r.responses.nu_motivation_channel; if (v != null) { const key = String(v); channelPreference[key] = (channelPreference[key] || 0) + 1; } });
  return { motivatorCounts, expertPreference, channelPreference };
};

// =========================
// Trendek & összehasonlítás
// =========================
export const buildTrends = (audits: Audit[], getResponsesForAudit: (auditId: string) => ResponseRow[]) => {
  return audits.map(audit => ({
    date: audit.start_date ? new Date(audit.start_date) : null,
    overview: overviewMetrics(getResponsesForAudit(audit.id)),
    awareness: awarenessMetrics(getResponsesForAudit(audit.id)),
    trust: trustMetrics(getResponsesForAudit(audit.id)),
    usage: usageMetrics(getResponsesForAudit(audit.id)),
    impact: impactMetrics(getResponsesForAudit(audit.id))
  })).sort((a, b) => (a.date?.getTime() ?? 0) - (b.date?.getTime() ?? 0));
};

export const compareTwoAudits = (
  a: { id: string; rows: ResponseRow[] },
  b: { id: string; rows: ResponseRow[] }
) => {
  const mk = (rows: ResponseRow[]) => ({
    overview: overviewMetrics(rows),
    awareness: awarenessMetrics(rows),
    trust: trustMetrics(rows),
    usage: usageMetrics(rows),
    impact: impactMetrics(rows)
  });
  const A = mk(a.rows), B = mk(b.rows);
  return {
    overview: {
      totalA: A.overview.totalResponses,
      totalB: B.overview.totalResponses,
      awarenessRateDiff: (B.overview.awarenessRate ?? 0) - (A.overview.awarenessRate ?? 0),
      usageRateDiff: (B.overview.usageRate ?? 0) - (A.overview.usageRate ?? 0)
    },
    awareness: {
      overallUnderstandingDiff: (B.awareness.overallUnderstanding ?? 0) - (A.awareness.overallUnderstanding ?? 0)
    },
    trust: { trustIndexDiff: (B.trust.trustIndex ?? 0) - (A.trust.trustIndex ?? 0) },
    usage: { familyRateDiff: (B.usage.familyRate ?? 0) - (A.usage.familyRate ?? 0) },
    impact: {
      avgImpactDiff: (B.impact.avgImpact ?? 0) - (A.impact.avgImpact ?? 0),
      npsDiff: ((B.impact.nps.npsScore ?? 0) - (A.impact.nps.npsScore ?? 0))
    }
  };
};

// =========================
/* Recharts adapterek & CSV export */
// =========================
export const toPieData = (counts: Record<string, number>) => Object.entries(counts).map(([name, value]) => ({ name, value }));
export const toBarData = (buckets: Record<string, number>) => Object.entries(buckets).map(([name, value]) => ({ name, value }));
export const toGauge = (val: number | null, max = 5) => (val == null ? null : Math.round((val / max) * 100));

export const toCSV = (rows: Array<Record<string, any>>) => {
  if (!rows.length) return '';
  const headers = Array.from(new Set(rows.flatMap(r => Object.keys(r))));
  const esc = (v: any) => { const s = (v ?? '').toString().replace(/\"/g, '\"\"'); return /[\",\n]/.test(s) ? `"${s}"` : s; };
  return [ headers.join(','), ...rows.map(r => headers.map(h => esc(r[h])).join(',')) ].join('\n');
};

// =========================
// Demo dataset – gyors paritás ellenőrzéshez
// =========================
export const demoRows: ResponseRow[] = [
  // Audit A
  { id: 'r1', audit_id: 'A', employee_metadata: { branch: 'used' }, responses: {
    u_awareness_understanding: 4, u_awareness_how_to_use: 4, u_awareness_accessibility: 3,
    u_trust_anonymity: 5, u_trust_employer: 2, u_trust_colleagues: 3, u_trust_likelihood: 5,
    u_usage_frequency: 2, u_usage_topic: ['Pszichológiai támogatás','Jogi tanácsadás'], u_usage_family: 'Igen',
    u_impact_satisfaction: 4, u_impact_problem_solving: 4, u_impact_wellbeing: 3, u_impact_performance: 4, u_impact_consistency: 4,
    u_impact_nps: 9
  }},
  { id: 'r2', audit_id: 'A', employee_metadata: { branch: 'not_used' }, responses: {
    nu_awareness_understanding: 3, nu_trust_anonymity: 3, nu_trust_employer: 4, nu_trust_colleagues: 3, nu_trust_likelihood: 2,
    nu_motivation_what: ['Több információ','Könnyebb elérés'], nu_motivation_expert: 'Pszichológus', nu_motivation_channel: 'Telefon'
  }},
  { id: 'r3', audit_id: 'A', employee_metadata: { branch: 'redirect' }, responses: {}},

  // Audit B (javuló számok)
  { id: 'r4', audit_id: 'B', employee_metadata: { branch: 'used' }, responses: {
    u_awareness_understanding: 5, u_awareness_how_to_use: 5, u_awareness_accessibility: 4,
    u_trust_anonymity: 5, u_trust_employer: 2, u_trust_colleagues: 2, u_trust_likelihood: 5,
    u_usage_frequency: 4, u_usage_topic: ['Pszichológiai támogatás'], u_usage_family: 'Nem',
    u_impact_satisfaction: 5, u_impact_problem_solving: 5, u_impact_wellbeing: 4, u_impact_performance: 4, u_impact_consistency: 5,
    u_impact_nps: 10
  }},
  { id: 'r5', audit_id: 'B', employee_metadata: { branch: 'not_used' }, responses: {
    nu_awareness_understanding: 4, nu_trust_anonymity: 4, nu_trust_employer: 3, nu_trust_colleagues: 3, nu_trust_likelihood: 3
  }}
];

// Gyors self‑check a demoRows‑szal (Node/ts-node alatt hívható)
export const selfCheck = () => {
  const A = demoRows.filter(r => r.audit_id === 'A');
  const B = demoRows.filter(r => r.audit_id === 'B');
  return {
    A: {
      overview: overviewMetrics(A),
      awareness: awarenessMetrics(A),
      trust: trustMetrics(A),
      usage: usageMetrics(A),
      impact: impactMetrics(A)
    },
    B: {
      overview: overviewMetrics(B),
      awareness: awarenessMetrics(B),
      trust: trustMetrics(B),
      usage: usageMetrics(B),
      impact: impactMetrics(B)
    },
    compare: compareTwoAudits({ id: 'A', rows: A }, { id: 'B', rows: B })
  };
};

/* ==============================================
   Jest tesztek – másold ki __tests__/metrics.test.ts néven
   ==============================================
import { calculateAverage, invert5, impactMetrics, computeTrustIndex, demoRows, awarenessMetrics } from '../eap-pulse-metrics.single';

test('calculateAverage handles nulls/empty', () => {
  expect(calculateAverage([1, 2, 3, null, undefined])).toBe(2);
  expect(calculateAverage([])).toBeNull();
});

test('invert5 works on 1..5', () => {
  expect(invert5(1)).toBe(5);
  expect(invert5(3)).toBe(3);
  expect(invert5(5)).toBe(1);
});

test('awareness overallUnderstanding = simple average of subgroup averages', () => {
  const A = demoRows.filter(r => r.audit_id === 'A');
  const m = awarenessMetrics(A);
  const manual = calculateAverage([m.usedUnderstanding, m.notUsedUnderstanding]);
  expect(m.overallUnderstanding).toBeCloseTo(manual!, 5);
});

test('impactMetrics computes NPS and averages', () => {
  const A = demoRows.filter(r => r.audit_id === 'A');
  const m = impactMetrics(A);
  expect(m.nps.npsScore).toBe(100); // promoters=1, detractors=0
  expect(m.avgImpact).not.toBeNull();
});

test('trustIndex improves from A to B on demoRows', () => {
  const A = demoRows.filter(r => r.audit_id === 'A');
  const B = demoRows.filter(r => r.audit_id === 'B');
  const idxA = computeTrustIndex(A)!;
  const idxB = computeTrustIndex(B)!;
  expect(idxB).toBeGreaterThan(idxA);
});
*/
