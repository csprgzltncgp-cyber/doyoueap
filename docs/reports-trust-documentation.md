# Reports - Bizalom & Készség Tab - Teljes Dokumentáció

Ez a dokumentáció tartalmazza a Bizalom (Trust & Willingness) tab összes részletét, számítását, megjelenését és funkcióit.

## Színséma

A használt színek:
- **chart-1**: `240 100% 30%` (sötétkék) - NEM HASZNÁLT
- **chart-2**: `225 100% 60%` (#3366ff) - ALAPÉRTELMEZETT KÉK, minden diagram és progress bar színe
- **chart-3**: `195 100% 60%` (cián) - Nem használók kategória
- **chart-4**: `180 100% 80%` (világos cián) - Nem használt
- **Piros (alacsony értékek)**: `hsl(0, 84%, 60%)` - Figyelmeztetés alacsony bizalmi szintnél

## 1. Főbb Számított Értékek

### Alapadatok - Branch szerinti szűrés
```typescript
const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
const trustResponses = [...usedResponses, ...notUsedResponses]; // Mindketten tudtak a programról
```

### Bizalmi pontszámok - Anonimitás

#### Használók anonimitás bizalma
```typescript
const usedAnonymityScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_trust_anonymity)
    .filter(v => v !== undefined)
);
```

#### Nem használók anonimitás bizalma
```typescript
const notUsedAnonymityScore = calculateAverage(
  notUsedResponses
    .map(r => r.responses?.nu_trust_anonymity)
    .filter(v => v !== undefined)
);
```

#### Összesített anonimitás bizalom
```typescript
const overallAnonymityScore = calculateAverage(
  trustResponses
    .map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity)
    .filter(v => v !== undefined)
);
```

### Félelem mutatók

#### Munkaadói félelem
```typescript
const usedEmployerFearScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_trust_employer)
    .filter(v => v !== undefined)
);

const notUsedEmployerFearScore = calculateAverage(
  notUsedResponses
    .map(r => r.responses?.nu_trust_employer)
    .filter(v => v !== undefined)
);

const overallEmployerFearScore = calculateAverage(
  trustResponses
    .map(r => r.responses?.u_trust_employer || r.responses?.nu_trust_employer)
    .filter(v => v !== undefined)
);
```

#### Kollégák megítélésétől való félelem
```typescript
const usedColleaguesFearScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_trust_colleagues)
    .filter(v => v !== undefined)
);

const notUsedColleaguesFearScore = calculateAverage(
  notUsedResponses
    .map(r => r.responses?.nu_trust_colleagues)
    .filter(v => v !== undefined)
);

const overallColleaguesFearScore = calculateAverage(
  trustResponses
    .map(r => r.responses?.u_trust_colleagues || r.responses?.nu_trust_colleagues)
    .filter(v => v !== undefined)
);
```

### Használati hajlandóság (csak használók)
```typescript
const likelihoodScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_trust_likelihood)
    .filter(v => v !== undefined)
);
```

### Akadályok elemzése
```typescript
const barriersData: { [key: string]: number } = {};
usedResponses.forEach(r => {
  const barrier = r.responses?.u_trust_barriers;
  if (barrier) {
    barriersData[barrier] = (barriersData[barrier] || 0) + 1;
  }
});

const barriersChartData = Object.entries(barriersData)
  .map(([name, value]) => ({ name, value }))
  .sort((a, b) => b.value - a.value);
```

### Összehasonlító adatok

#### Anonimitás összehasonlítás
```typescript
const anonymityComparison = [
  {
    group: 'Használók',
    score: parseFloat(usedAnonymityScore),
    count: usedResponses.length,
    color: 'hsl(var(--chart-2))'
  },
  {
    group: 'Nem használók',
    score: parseFloat(notUsedAnonymityScore),
    count: notUsedResponses.length,
    color: 'hsl(var(--chart-3))'
  }
];
```

#### Munkaadói félelem összehasonlítás
```typescript
const employerFearComparison = [
  {
    group: 'Használók',
    score: parseFloat(usedEmployerFearScore),
    count: usedResponses.length,
    color: 'hsl(var(--chart-2))'
  },
  {
    group: 'Nem használók',
    score: parseFloat(notUsedEmployerFearScore),
    count: notUsedResponses.length,
    color: 'hsl(var(--chart-3))'
  }
];
```

#### Kollégák félelem összehasonlítás
```typescript
const colleaguesFearComparison = [
  {
    group: 'Használók',
    score: parseFloat(usedColleaguesFearScore),
    count: usedResponses.length,
    color: 'hsl(var(--chart-2))'
  },
  {
    group: 'Nem használók',
    score: parseFloat(notUsedColleaguesFearScore),
    count: notUsedResponses.length,
    color: 'hsl(var(--chart-3))'
  }
];
```

### Bizalmi profil (használók)
```typescript
const trustProfileData = [
  { 
    category: 'Anonimitás', 
    score: parseFloat(usedAnonymityScore),
    description: 'Bizalom az anonimitás védelmében'
  },
  { 
    category: 'Munkaadó', 
    score: 5 - parseFloat(usedEmployerFearScore), // Fordított skála
    description: 'Nincs félelem a munkaadó reakciójától'
  },
  { 
    category: 'Kollégák', 
    score: 5 - parseFloat(usedColleaguesFearScore), // Fordított skála
    description: 'Nincs félelem a kollégák megítélésétől'
  },
  { 
    category: 'Jövőbeli használat', 
    score: parseFloat(likelihoodScore),
    description: 'Hajlandóság ismételt használatra'
  }
];
```

**KRITIKUS**: A munkaadói és kollégák félelemnél FORDÍTOTT skálát használunk (5 - érték), mert alacsonyabb félelem = jobb bizalmi szint!

### Átfogó bizalmi index
```typescript
const overallTrustIndex = (trustProfileData.reduce((sum, item) => sum + item.score, 0) / trustProfileData.length);
```

### Radar chart adatok
```typescript
const radarData = [
  {
    subject: 'Anonimitás',
    Használók: parseFloat(usedAnonymityScore),
    'Nem használók': parseFloat(notUsedAnonymityScore),
    fullMark: 5
  },
  {
    subject: 'Munkaadói bizalom',
    Használók: 5 - parseFloat(usedEmployerFearScore),
    'Nem használók': 5 - parseFloat(notUsedEmployerFearScore),
    fullMark: 5
  },
  {
    subject: 'Kollégák bizalom',
    Használók: 5 - parseFloat(usedColleaguesFearScore),
    'Nem használók': 5 - parseFloat(notUsedColleaguesFearScore),
    fullMark: 5
  }
];
```

## 2. Kártyák Részletes Specifikációja

### 2.1. Anonimitás Bizalom Kártya (Overall Anonymity Card)

**ID:** `overall-anonymity-card`
**Komponens:** GaugeChart
**Icon:** Lock
**Paraméterek:**
```typescript
<GaugeChart 
  value={(parseFloat(overallAnonymityScore) / 5) * 100} 
  maxValue={100}
  size={220}
  label={overallAnonymityScore}
  sublabel={`${trustResponses.length} válaszadó`}
  cornerRadius={30}
/>
```

**Megjelenés:**
- Félkör alakú gauge chart
- Méret: 220px
- Kék színnel töltődik (#3366ff, chart-2)
- Lekerekített végek (cornerRadius: 30)

**Leírás:** "Mennyire bíznak az anonimitás védelmében"

### 2.2. Átfogó Bizalmi Index Kártya (Trust Index Card)

**ID:** `trust-index-card`
**Stílus:** Animált háttér gradiens + DINAMIKUS SZÍN
**Icon:** Shield

**KRITIKUS - Dinamikus színezés:**
```typescript
// Háttér színe
background: `linear-gradient(to top, ${
  overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))'
} 0%, ... )`

// Szám színe
color: overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))'
```

**Színlogika:**
- **< 3.0**: PIROS (`hsl(0, 84%, 60%)`) + AlertTriangle ikon
- **≥ 3.0**: KÉK (`hsl(var(--chart-2))`)

**Háttér animáció:**
```typescript
<div 
  className="absolute inset-0 transition-all duration-500"
  style={{
    background: `linear-gradient(to top, ${
      overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))'
    } 0%, ${
      overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))'
    } ${(overallTrustIndex / 5) * 100}%, transparent ${(overallTrustIndex / 5) * 100}%, transparent 100%)`,
    opacity: 0.1
  }}
/>
```

**Tartalom:**
```typescript
<div className="text-6xl font-bold" style={{ color: overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))' }}>
  {overallTrustIndex.toFixed(1)}
</div>
<p className="text-sm text-muted-foreground mt-2 flex items-center justify-center gap-2">
  {overallTrustIndex >= 4 ? (
    '✓ Magas bizalmi szint'
  ) : overallTrustIndex >= 3 ? (
    '→ Közepes bizalmi szint'
  ) : (
    <>
      <AlertTriangle className="w-4 h-4 text-red-600" /> Alacsony bizalmi szint - fejlesztést igényel
    </>
  )}
</p>
```

### 2.3. Munkaadói Félelem Kártya (Employer Fear Card)

**ID:** `employer-fear-card`
**Stílus:** Animált háttér gradiens (KÉK)
**Icon:** AlertTriangle

**FONTOS:** A háttér MINDIG kék (chart-2), NEM cián!

**Háttér animáció:**
```typescript
background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(parseFloat(overallEmployerFearScore) / 5) * 100}%, transparent ${(parseFloat(overallEmployerFearScore) / 5) * 100}%, transparent 100%)`
opacity: 0.1
```

**Tartalom:**
```typescript
<div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
  {overallEmployerFearScore}
</div>
<p className="text-sm text-muted-foreground mt-2">
  {parseFloat(overallEmployerFearScore) <= 2 
    ? '✓ Alacsony félelemszint' 
    : parseFloat(overallEmployerFearScore) <= 3.5
    ? '→ Közepes félelemszint'
    : '⚠ Magas félelemszint'}
</p>
```

**Értelmezés:** Magasabb érték = nagyobb félelem (1-5 skála)

### 2.4. Jövőbeli Használat Kártya (Likelihood Card)

**ID:** `likelihood-card`
**Komponens:** GaugeChart
**Icon:** TrendingUp
**Paraméterek:**
```typescript
<GaugeChart 
  value={(parseFloat(likelihoodScore) / 5) * 100} 
  maxValue={100}
  size={220}
  label={likelihoodScore}
  sublabel={`${usedResponses.length} használó`}
  cornerRadius={30}
/>
```

**Leírás:** "Mennyire valószínű, hogy újra igénybe veszik"

### 2.5. Bizalmi Dimenziók Radar Chart (Trust Radar Card)

**ID:** `trust-radar-card`
**Komponens:** RadarChart (Recharts)
**Méret:** h-[350px]

**Paraméterek:**
```typescript
<RadarChart data={radarData}>
  <PolarGrid />
  <PolarAngleAxis dataKey="subject" />
  <PolarRadiusAxis angle={90} domain={[0, 5]} />
  <Radar name="Használók" dataKey="Használók" stroke="hsl(var(--chart-2))" fill="hsl(var(--chart-2))" fillOpacity={0.6} />
  <Radar name="Nem használók" dataKey="Nem használók" stroke="hsl(var(--chart-3))" fill="hsl(var(--chart-3))" fillOpacity={0.6} />
  <Legend />
  <Tooltip />
</RadarChart>
```

**Megjelenés:**
- 3 tengely: Anonimitás, Munkaadói bizalom, Kollégák bizalom
- Kék terület: Használók
- Cián terület: Nem használók
- fillOpacity: 0.6 (átlátszóság)

**Magyarázó szöveg:**
```typescript
<p className="text-xs text-muted-foreground text-center mt-4">
  Minél nagyobb a színes terület, annál magasabb a bizalmi szint. A félelem mutatók fordítva értelmezendők (alacsony érték = nagyobb félelem).
</p>
```

### 2.6. Bizalmi Akadályok Kártya (Barriers Card)

**ID:** `barriers-card`
**Komponens:** BarChart (Recharts - Horizontal)
**Méret:** h-[350px]

**Paraméterek:**
```typescript
<BarChart data={barriersChartData} layout="vertical">
  <CartesianGrid strokeDasharray="3 3" />
  <XAxis type="number" />
  <YAxis dataKey="name" type="category" width={150} />
  <Tooltip />
  <Bar dataKey="value" fill="hsl(var(--chart-3))" radius={[0, 8, 8, 0]} />
</BarChart>
```

**Megjelenés:**
- Vízszintes oszlopdiagram
- Oszlopok jobbra lekerekítettek (radius: [0, 8, 8, 0])
- Cián szín (chart-3)
- Y tengely: 150px széles

**Leírás:** "Mi tartja vissza a használókat? (használók körében)"

### 2.7. Anonimitásba Vetett Bizalom Kártya (Anonymity Comparison Card)

**ID:** `anonymity-comparison-card`
**Komponens:** Progress bar sorozat
**Progress bar magasság:** `h-3` (STANDARD MAGASSÁG)

**Paraméterek minden progress barhoz:**
```typescript
<Progress 
  value={group.score * 20} 
  style={{ '--progress-background': group.color } as React.CSSProperties}
  className="h-3"
/>
```

**Struktúra minden csoporthoz:**
```typescript
{anonymityComparison.map((group) => (
  <div key={group.group} className="space-y-3">
    <div className="flex items-center justify-between">
      <div className="flex items-center gap-3">
        <div className="w-4 h-4 rounded-full" style={{ backgroundColor: group.color }} />
        <div>
          <p className="font-semibold">{group.group}</p>
          <p className="text-xs text-muted-foreground">{group.count} válaszadó</p>
        </div>
      </div>
      <div className="text-right">
        <p className="text-2xl font-bold">{group.score}</p>
        <p className="text-xs text-muted-foreground">/ 5.0</p>
      </div>
    </div>
    <Progress value={group.score * 20} style={{ '--progress-background': group.color }} className="h-3" />
    <div className="bg-muted/30 p-3 rounded-md">
      <p className="text-xs text-muted-foreground">
        {group.score >= 4 ? '✓ Magas bizalmi szint az anonimitásban' : 
         group.score >= 3 ? '→ Közepes bizalmi szint' : 
         '⚠ Alacsony bizalmi szint - fejlesztést igényel'}
      </p>
    </div>
  </div>
))}
```

**Különbség számítás:**
```typescript
<div className="pt-4 border-t">
  <div className="flex items-center justify-between text-sm">
    <span className="text-muted-foreground">Különbség:</span>
    <span className="font-semibold">
      {Math.abs(anonymityComparison[0].score - anonymityComparison[1].score).toFixed(1)} pont
    </span>
  </div>
  <p className="text-xs text-muted-foreground mt-2">
    {anonymityComparison[0].score > anonymityComparison[1].score 
      ? 'A használók jobban bíznak az anonimitásban' 
      : anonymityComparison[0].score < anonymityComparison[1].score
      ? 'A nem használók jobban bíznak az anonimitásban'
      : 'Mindkét csoport hasonlóan bízik az anonimitásban'}
  </p>
</div>
```

### 2.8. Félelem a Munkaadó Reakciójától Kártya (Employer Fear Comparison Card)

**ID:** `employer-fear-comparison-card`
**Komponens:** Progress bar sorozat
**Progress bar magasság:** `h-3` (STANDARD MAGASSÁG)

**Struktúra:** Ugyanaz, mint az Anonimitás összehasonlításnál

**Értékelési logika:**
```typescript
{group.score <= 2 ? '✓ Alacsony félelemszint' : 
 group.score <= 3.5 ? '→ Közepes félelemszint' : 
 '⚠ Magas félelemszint - kommunikáció szükséges'}
```

**Leírás:** "Használók vs. Nem használók (1-5 skála, magasabb = nagyobb félelem)"

### 2.9. Félelem a Kollégák Megítélésétől Kártya (Colleagues Fear Comparison Card)

**ID:** `colleagues-fear-comparison-card`
**Komponens:** Progress bar sorozat
**Progress bar magasság:** `h-3` (STANDARD MAGASSÁG)

**Struktúra:** Ugyanaz, mint az Anonimitás összehasonlításnál

**Értékelési logika:**
```typescript
{group.score <= 2 ? '✓ Alacsony félelemszint' : 
 group.score <= 3.5 ? '→ Közepes félelemszint' : 
 '⚠ Magas félelemszint - stigma csökkentés szükséges'}
```

**Leírás:** "Használók vs. Nem használók (1-5 skála, magasabb = nagyobb félelem)"

### 2.10. Használói Bizalmi Profil Részletesen Kártya (Trust Profile Card)

**ID:** `trust-profile-card`
**Komponens:** Progress bar sorozat
**Progress bar magasság:** `h-3` (STANDARD MAGASSÁG)

**Paraméterek minden progress barhoz:**
```typescript
<Progress 
  value={item.score * 20} 
  style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
  className="h-3"
/>
```

**Struktúra minden dimenzióhoz:**
```typescript
{trustProfileData.map((item, index) => (
  <div key={index} className="space-y-2">
    <div className="flex items-start justify-between">
      <div>
        <p className="font-semibold">{item.category}</p>
        <p className="text-xs text-muted-foreground">{item.description}</p>
      </div>
      <div className="text-right">
        <p className="text-2xl font-bold">{item.score.toFixed(1)}</p>
        <p className="text-xs text-muted-foreground">/ 5.0</p>
      </div>
    </div>
    <Progress 
      value={item.score * 20} 
      style={{ '--progress-background': 'hsl(var(--chart-2))' }}
      className="h-3"
    />
    <div className="flex justify-between text-xs text-muted-foreground">
      <span>1 - Nagyon alacsony</span>
      <span>5 - Nagyon magas</span>
    </div>
  </div>
))}
```

**Leírás:** "Átfogó értékelés a használók körében ({usedResponses.length} fő)"

**FONTOS:** A Munkaadó és Kollégák dimenziók fordított skálát használnak!

### 2.11. Statisztikai Összefoglaló Kártya

**Komponens:** 3 oszlopos grid
**Megjelenés:** bg-muted/50 háttér, lekerekített sarkok

**Mutatók:**
1. Válaszadók összesen: `totalCount`
2. Használók: `usedResponses.length ({percentage}%)`
3. Nem használók: `notUsedResponses.length ({percentage}%)`

## 3. Export Funkció

**Függvény:** `exportCardToPNG(cardId: string, fileName: string)`
**Használt library:** html2canvas

**Implementáció:**
```typescript
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
```

**Export gombok minden kártyán:**
```typescript
<Button
  variant="ghost"
  size="icon"
  className="absolute right-2 top-2 h-8 w-8"
  onClick={() => exportCardToPNG('card-id', 'filename')}
>
  <Download className="h-4 w-4" />
</Button>
```

**Fájlnevek:**
- overall-anonymity-card → `altalanos-anonimitas-bizalom.png`
- trust-index-card → `atfogo-bizalmi-index.png`
- employer-fear-card → `munkaadoi-felelem.png`
- likelihood-card → `hasznalati-hajlandosag.png`
- trust-radar-card → `bizalmi-dimenzio-radar.png`
- barriers-card → `bizalmi-akadalyok.png`
- anonymity-comparison-card → `anonimitas-osszehasonlitas.png`
- employer-fear-comparison-card → `munkaadoi-felelem-osszehasonlitas.png`
- colleagues-fear-comparison-card → `kollegak-felelem-osszehasonlitas.png`
- trust-profile-card → `bizalmi-profil-reszletesen.png`

## 4. Layout Struktúra

```typescript
<div className="space-y-6">
  {/* Fejléc */}
  <div>
    <h2 className="text-2xl font-bold mb-2">Bizalom & Készség Részletes Elemzése</h2>
    <p className="text-muted-foreground text-sm">
      Az EAP program bizalmi szintjének, anonimitás védelmének és használati hajlandóságának átfogó kiértékelése
    </p>
  </div>

  {/* 1. sor: Fő bizalmi mutatók (4 gauge/metric kártya) */}
  <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
    {/* Anonimitás Bizalom, Bizalmi Index, Munkaadói Félelem, Jövőbeli Használat */}
  </div>

  {/* 2. sor: Radar chart + Bizalmi akadályok */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Radar Chart + Horizontal Bar Chart */}
  </div>

  {/* 3. sor: Anonimitás összehasonlítás (full width) */}
  <Card id="anonymity-comparison-card">
    {/* Progress bars + comparison */}
  </Card>

  {/* 4. sor: Félelem összehasonlítások */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Munkaadói félelem + Kollégák félelem */}
  </div>

  {/* 5. sor: Bizalmi profil részletesen (full width) */}
  <Card id="trust-profile-card">
    {/* 4 dimenzió progress bar-okkal */}
  </Card>

  {/* Statisztikai összefoglaló */}
  <Card>
    {/* 3 oszlopos grid összefoglaló adatokkal */}
  </Card>
</div>
```

## 5. Használt Recharts Komponensek

```typescript
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  Legend, 
  ResponsiveContainer, 
  PieChart, 
  Pie, 
  Cell, 
  RadarChart, 
  Radar, 
  PolarGrid, 
  PolarAngleAxis, 
  PolarRadiusAxis 
} from 'recharts';
```

**GaugeChart** - Saját komponens: `src/components/ui/gauge-chart.tsx`
**Progress** - Shadcn komponens: `src/components/ui/progress.tsx`

## 6. Függőségek

- `html2canvas` - PNG export
- `recharts` - BarChart, RadarChart
- `lucide-react` - Ikonok (Download, Shield, Lock, AlertTriangle, TrendingUp)
- `sonner` - Toast notifications
- `@/components/ui/gauge-chart` - Saját gauge chart komponens
- `@/components/ui/progress` - Progress bar komponens

## 7. Helper Függvények

```typescript
const calculateAverage = (values: number[]) => {
  if (values.length === 0) return '0.0';
  return ((values.reduce((a, b) => a + b, 0) / values.length)).toFixed(1);
};
```

## 8. Adatforrások

**Táblák:**
- `audits` - Felmérések listája
- `audit_responses` - Válaszok

**Főbb mezők:**
- `responses.employee_metadata.branch` - 'used', 'not_used', 'redirect'
- `responses.u_trust_anonymity` - Használók anonimitás bizalma (1-5)
- `responses.nu_trust_anonymity` - Nem használók anonimitás bizalma (1-5)
- `responses.u_trust_employer` - Használók munkaadói félelme (1-5)
- `responses.nu_trust_employer` - Nem használók munkaadói félelme (1-5)
- `responses.u_trust_colleagues` - Használók kollégák félelme (1-5)
- `responses.nu_trust_colleagues` - Nem használók kollégák félelme (1-5)
- `responses.u_trust_likelihood` - Jövőbeli használati hajlandóság (1-5)
- `responses.u_trust_barriers` - Bizalmi akadályok (string)

## 9. Kritikus Design Elemek

### Progress Bar Egységesítés
**FONTOS:** Minden progress bar magassága egységesen `h-3` az ÖSSZES tabon (Awareness, Trust, Reports, RunningAudits)!

```typescript
className="h-3"  // STANDARD MAGASSÁG
```

### Dinamikus Színezés - Bizalmi Index
**KRITIKUS:** A Bizalmi Index kártya DINAMIKUSAN színezi a hátteret és a számot:
- **< 3.0**: PIROS + AlertTriangle ikon
- **≥ 3.0**: KÉK (chart-2)

```typescript
// Piros ha alacsony
overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))'
```

### Animált Háttér Gradient
```typescript
<div 
  className="absolute inset-0 transition-all duration-500"
  style={{
    background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(score / 5) * 100}%, transparent ${(score / 5) * 100}%, transparent 100%)`,
    opacity: 0.1
  }}
/>
```

### Z-index Rétegződés
```typescript
<CardHeader className="relative z-10">
<CardContent className="space-y-4 relative z-10">
```

### Diagram Magasság Egységesítés
**FONTOS:** Minden vízszintes diagram (BarChart, RadarChart) konténer magassága egységesen `h-[350px]`!

```typescript
<div className="h-[350px]">
  <ResponsiveContainer width="100%" height="100%">
```

### Fordított Skála
**KRITIKUS:** A félelem mutatók fordított skálát használnak a bizalmi profilban és radar chartban!

```typescript
// Fordítás: magasabb félelem = alacsonyabb bizalom
score: 5 - parseFloat(usedEmployerFearScore)
```

## 10. Színhasználat

**Kategóriák szerinti színek:**
- **Használók:** `hsl(var(--chart-2))` - Kék (#3366ff)
- **Nem használók:** `hsl(var(--chart-3))` - Cián
- **Akadályok:** `hsl(var(--chart-3))` - Cián
- **Figyelmeztetés (alacsony bizalmi index):** `hsl(0, 84%, 60%)` - Piros

**Progress bar színezés:**
```typescript
style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
```

**FONTOS:** A Munkaadói Félelem kártya háttere MINDIG kék (chart-2), NEM cián!

## 11. Responsive Viselkedés

**Grid breakpoints:**
- Mobile: 1 oszlop (`grid-cols-1`)
- Tablet: 2 oszlop (`md:grid-cols-2`)
- Desktop (1. sor): 4 oszlop (`md:grid-cols-4`)

**Gauge Chart méretei:**
- Fő bizalmi mutatók: `size={220}`
- Overview tabon (ha használod): `size={280}` (nagyobb)

## 12. Loading State

```typescript
if (loading) {
  return (
    <div className="flex items-center justify-center h-96">
      <p>Betöltés...</p>
    </div>
  );
}
```

## 13. Visszaállítási Checklist

Ha újra kell implementálni ezt a tabot:

1. ✅ Importáld a szükséges függőségeket (recharts, lucide-react, sonner, html2canvas)
2. ✅ Hozd létre az összes számított értéket (branch szerinti szűrés, átlagok, fordított skálák)
3. ✅ Implementáld az export funkcionalitást (exportCardToPNG)
4. ✅ Adj hozzá minden kártyát az ID-vel
5. ✅ **KRITIKUS:** Állítsd be a progress bar magasságot `h-3`-ra
6. ✅ **KRITIKUS:** Állítsd be a diagram konténerek magasságát `h-[350px]`-re
7. ✅ **KRITIKUS:** Implementáld a dinamikus színezést a Bizalmi Index kártyán (piros ha < 3)
8. ✅ **KRITIKUS:** A Munkaadói Félelem kártya háttere MINDIG kék (chart-2)
9. ✅ Használd a helyes színsémát (chart-2 alapértelmezett, chart-3 nem használók)
10. ✅ Implementáld az animált háttér gradienst a metric kártyákon
11. ✅ Adj hozzá z-index layeringet a relative positioned kártyákhoz
12. ✅ **KRITIKUS:** Fordított skála a félelem mutatóknál (5 - érték)
13. ✅ Adj hozzá magyarázó szöveget a radar charthoz
14. ✅ Teszteld az export funkcionalitást minden kártyán
15. ✅ Ellenőrizd a responsive viselkedést
16. ✅ Validáld az adatszűrést és számításokat

## 14. Gyakori Hibák és Megoldások

**Hiba:** Progress bar különböző magasságúak
**Megoldás:** Minden progress bar-hoz adjunk `className="h-3"`

**Hiba:** Diagramok különböző magasságúak
**Megoldás:** Minden diagram konténer `h-[350px]`

**Hiba:** Háttér gradiens nem jelenik meg
**Megoldás:** Ellenőrizd az opacity (0.1) és a z-index (z-10) beállításokat

**Hiba:** Bizalmi Index nem piros alacsony értéknél
**Megoldás:** Ellenőrizd a feltételt: `overallTrustIndex < 3 ? 'hsl(0, 84%, 60%)' : 'hsl(var(--chart-2))'`

**Hiba:** Munkaadói Félelem kártya cián háttérrel
**Megoldás:** A háttérnek `hsl(var(--chart-2))` kell lennie, NEM `hsl(var(--chart-3))`

**Hiba:** Radar chart félelem mutatói nem megfelelők
**Megoldás:** Használd a fordított skálát: `5 - parseFloat(fearScore)`

**Hiba:** Export nem működik
**Megoldás:** Dinamikusan importáljuk a html2canvas-t: `(await import('html2canvas')).default`

**Hiba:** Minden adat 0
**Megoldás:** 
1. Frissítsd az oldalt (F5)
2. Ellenőrizd, hogy a selectedAuditId megfelelő-e
3. Nézd meg a konzolt (F12) hibákért
4. Ellenőrizd az adatbázis RLS szabályokat

## 15. Értelmezési Útmutatók

### Anonimitás Bizalom
- **Magas (≥4)**: Jó bizalmi szint, az alkalmazottak bíznak a rendszerben
- **Közepes (3-3.9)**: Fejlesztendő terület
- **Alacsony (<3)**: Sürgős kommunikáció szükséges

### Félelem Mutatók
- **Alacsony (≤2)**: Nincs jelentős akadály
- **Közepes (2.1-3.5)**: Figyelmet igényel
- **Magas (>3.5)**: Komoly akadály a használatban

### Radar Chart
- **Nagyobb terület**: Jobb bizalmi szint
- **Hasonló alakú területek**: Konzisztens bizalmi viszony
- **Eltérő alakú területek**: Különböző dimenziókban eltérő bizalom

### Bizalmi Profil
- **Fordított skála használat**: A munkaadói és kollégák dimenziók magasabb értéke jobb (kevesebb félelem)
- **4+ átlag**: Kiváló bizalmi környezet
- **3-3.9 átlag**: Fejleszthető
- **<3 átlag**: Azonnali beavatkozás szükséges
