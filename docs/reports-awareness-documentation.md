# Reports - Awareness Tab - Teljes Dokumentáció

Ez a dokumentáció tartalmazza az Awareness tab összes részletét, számítását, megjelenését és funkcióit.

## Színséma

A használt színek:
- **chart-1**: `240 100% 30%` (sötétkék) - NEM HASZNÁLT
- **chart-2**: `225 100% 60%` (#3366ff) - ALAPÉRTELMEZETT KÉK, minden diagram és progress bar színe
- **chart-3**: `195 100% 60%` (cián) - Nem használók kategória
- **chart-4**: `180 100% 80%` (világos cián) - Nem tudtak róla kategória

## 1. Főbb Számított Értékek

### Alapadatok - Branch szerinti szűrés
```typescript
const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
const notUsedResponses = responses.filter(r => r.employee_metadata?.branch === 'not_used');
const redirectResponses = responses.filter(r => r.employee_metadata?.branch === 'redirect');
const awarenessResponses = [...usedResponses, ...notUsedResponses]; // Mindketten tudtak a programról
```

### Ismertség pontszámok

#### Használók megértési szintje
```typescript
const usedUnderstandingScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_awareness_understanding)
    .filter(v => v !== undefined)
);
```

#### Nem használók megértési szintje
```typescript
const notUsedUnderstandingScore = calculateAverage(
  notUsedResponses
    .map(r => r.responses?.nu_awareness_understanding)
    .filter(v => v !== undefined)
);
```

#### Összesített megértés
```typescript
const overallUnderstandingScore = calculateAverage(
  awarenessResponses
    .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
    .filter(v => v !== undefined)
);
```

#### Használói tudásszint dimenzió (csak használók)
```typescript
const howToUseScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_awareness_how_to_use)
    .filter(v => v !== undefined)
);

const accessibilityScore = calculateAverage(
  usedResponses
    .map(r => r.responses?.u_awareness_accessibility)
    .filter(v => v !== undefined)
);
```

### Ismertségi arányok
```typescript
const totalCount = responses.length;
const awarenessRate = totalCount > 0 ? ((awarenessResponses.length / totalCount) * 100).toFixed(1) : '0';
const redirectRate = totalCount > 0 ? ((redirectResponses.length / totalCount) * 100).toFixed(1) : '0';
```

### Információs források elemzése
```typescript
const sourceData: { [key: string]: number } = {};
awarenessResponses.forEach(r => {
  const sources = r.responses?.u_awareness_source || r.responses?.nu_awareness_source || [];
  sources.forEach((source: string) => {
    sourceData[source] = (sourceData[source] || 0) + 1;
  });
});

const sourceChartData = Object.entries(sourceData)
  .map(([name, value]) => ({ name, value }))
  .sort((a, b) => b.value - a.value);
```

### Kommunikációs gyakoriság
```typescript
const frequencyData: { [key: string]: number } = {};
usedResponses.forEach(r => {
  const freq = r.responses?.u_awareness_frequency;
  if (freq) {
    frequencyData[freq] = (frequencyData[freq] || 0) + 1;
  }
});

const frequencyChartData = Object.entries(frequencyData).map(([name, value]) => ({
  name,
  value
}));
```

### Információ elégségesség
```typescript
const hasEnoughInfo = usedResponses.filter(r => r.responses?.u_awareness_info === 'yes').length;
const notEnoughInfo = usedResponses.filter(r => r.responses?.u_awareness_info === 'no').length;
const infoSufficiencyData = [
  { name: 'Elegendő', value: hasEnoughInfo, color: 'hsl(var(--chart-2))' },
  { name: 'Nem elegendő', value: notEnoughInfo, color: 'hsl(var(--chart-3))' }
].filter(item => item.value > 0);
```

### Megértés összehasonlítás (használók vs nem használók)
```typescript
const understandingComparison = [
  {
    group: 'Használók',
    score: parseFloat(usedUnderstandingScore),
    count: usedResponses.length,
    color: 'hsl(var(--chart-2))'
  },
  {
    group: 'Nem használók',
    score: parseFloat(notUsedUnderstandingScore),
    count: notUsedResponses.length,
    color: 'hsl(var(--chart-3))'
  }
];
```

### Részletes ismertségi profil (használók)
```typescript
const awarenessProfileData = [
  { 
    category: 'Megértés', 
    score: parseFloat(usedUnderstandingScore),
    description: 'Mennyire értik a szolgáltatást'
  },
  { 
    category: 'Használat ismerete', 
    score: parseFloat(howToUseScore),
    description: 'Hogyan vehetik igénybe'
  },
  { 
    category: 'Elérhetőség', 
    score: parseFloat(accessibilityScore),
    description: 'Mennyire érzik elérhetőnek'
  }
];
```

## 2. Kártyák Részletes Specifikációja

### 2.1. Általános Ismertség Kártya (Overall Awareness Card)

**ID:** `overall-awareness-card`
**Komponens:** GaugeChart
**Paraméterek:**
```typescript
<GaugeChart 
  value={parseFloat(awarenessRate)} 
  maxValue={100}
  size={220}
  label={`${awarenessRate}%`}
  sublabel={`${awarenessResponses.length} / ${totalCount} fő`}
  cornerRadius={30}
/>
```

**Megjelenés:**
- Félkör alakú gauge chart
- Méret: 220px
- Kék színnel töltődik (#3366ff, chart-2)
- Lekerekített végek (cornerRadius: 30)
- Icon: Eye (lucide-react)

**Leírás:**
"A válaszolók közül ennyien tudtak a programról (használók + nem használók)"

### 2.2. Általános Megértés Kártya (Understanding Card)

**ID:** `understanding-card`
**Stílus:** Animált háttér gradiens
**Icon:** Info

**Háttér animáció:**
```typescript
<div 
  className="absolute inset-0 transition-all duration-500"
  style={{
    background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(parseFloat(overallUnderstandingScore) / 5) * 100}%, transparent ${(parseFloat(overallUnderstandingScore) / 5) * 100}%, transparent 100%)`,
    opacity: 0.1
  }}
/>
```

**Tartalom:**
```typescript
<div className="text-center">
  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
    {overallUnderstandingScore}
  </div>
  <p className="text-sm text-muted-foreground mt-2">
    Mennyire értik a munkavállalók az EAP szolgáltatást
  </p>
</div>
```

### 2.3. Általános Tudásszint Kártya (Overall Knowledge Card)

**ID:** `overall-knowledge-card`
**Stílus:** Animált háttér gradiens
**Icon:** TrendingUp

**Számítás:**
```typescript
const avgKnowledge = awarenessProfileData.reduce((sum, item) => sum + item.score, 0) / awarenessProfileData.length;
```

**Háttér animáció:**
```typescript
background: `linear-gradient(to top, hsl(var(--chart-2)) 0%, hsl(var(--chart-2)) ${(avgKnowledge / 5) * 100}%, transparent ${(avgKnowledge / 5) * 100}%, transparent 100%)`
opacity: 0.1
```

**Tartalom:**
```typescript
<div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
  {avgKnowledge.toFixed(1)}
</div>
<p className="text-sm text-muted-foreground mt-2">
  {avgKnowledge >= 3.5 
    ? '✓ A használók jól ismerik a szolgáltatást' 
    : '→ Van még fejlesztési lehetőség a kommunikációban'}
</p>
```

### 2.4. Nem Ismerték Kártya (Unawareness Card)

**ID:** `unawareness-card`
**Komponens:** GaugeChart
**Icon:** Users
**Paraméterek:**
```typescript
<GaugeChart 
  value={parseFloat(redirectRate)} 
  maxValue={100}
  size={220}
  label={`${redirectRate}%`}
  sublabel={`${redirectResponses.length} / ${totalCount} fő`}
  cornerRadius={30}
/>
```

**Leírás:**
"A válaszolók közül ennyien NEM tudtak a programról"

### 2.5. Információs Források Kártya (Sources Card)

**ID:** `sources-card`
**Komponens:** BarChart (Recharts - Horizontal)
**Méret:** h-[350px]

**Paraméterek:**
```typescript
<BarChart data={sourceChartData} layout="vertical">
  <CartesianGrid strokeDasharray="3 3" />
  <XAxis type="number" />
  <YAxis dataKey="name" type="category" width={150} />
  <Tooltip />
  <Bar dataKey="value" fill="hsl(var(--chart-2))" radius={[0, 8, 8, 0]} />
</BarChart>
```

**Megjelenés:**
- Vízszintes oszlopdiagram
- Oszlopok jobbra lekerekítettek (radius: [0, 8, 8, 0])
- Kék szín (#3366ff)
- Y tengely: 150px széles
- Rács: szaggatott vonalak

### 2.6. Kommunikációs Gyakoriság Kártya (Frequency Card)

**ID:** `frequency-card`
**Komponens:** PieChart (Recharts)
**Méret:** h-[350px]

**Paraméterek:**
```typescript
<PieChart>
  <Pie
    data={frequencyChartData}
    cx="50%"
    cy="50%"
    innerRadius={60}
    outerRadius={120}
    paddingAngle={2}
    dataKey="value"
  >
    {frequencyChartData.map((entry, index) => (
      <Cell key={`cell-${index}`} fill={`hsl(var(--chart-${(index % 4) + 1}))`} />
    ))}
  </Pie>
  <Tooltip />
</PieChart>
```

**Legenda:**
```typescript
<div className="flex flex-wrap gap-4 justify-center mt-4">
  {frequencyChartData.map((entry, index) => (
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
</div>
```

**Leírás:** "Milyen gyakran kapnak információt (csak használók)"

### 2.7. Információ Elégségesség Kártya (Info Sufficiency Card)

**ID:** `info-sufficiency-card`
**Komponens:** PieChart (Recharts)
**Méret:** h-[350px]

**Paraméterek:**
```typescript
<PieChart>
  <Pie
    data={infoSufficiencyData}
    cx="50%"
    cy="50%"
    innerRadius={60}
    outerRadius={120}
    paddingAngle={2}
    dataKey="value"
  >
    {infoSufficiencyData.map((entry, index) => (
      <Cell key={`cell-${index}`} fill={entry.color} />
    ))}
  </Pie>
  <Tooltip />
</PieChart>
```

**Legenda:**
```typescript
{infoSufficiencyData.map((entry, index) => (
  <div key={index} className="flex items-center gap-2">
    <div 
      className="w-3 h-3 rounded-full" 
      style={{ backgroundColor: entry.color }}
    />
    <span className="text-sm text-foreground">
      {entry.name}: {entry.value} ({((entry.value / usedResponses.length) * 100).toFixed(0)}%)
    </span>
  </div>
))}
```

**Leírás:** "Használók körében: kaptak-e elegendő információt?"

### 2.8. Szolgáltatás Megértésének Szintje Kártya (Comparison Card)

**ID:** `comparison-card`
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
{understandingComparison.map((group) => (
  <div key={group.group} className="space-y-3">
    <div className="flex items-center justify-between">
      <div className="flex items-center gap-3">
        <div 
          className="w-4 h-4 rounded-full" 
          style={{ backgroundColor: group.color }}
        />
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
        {group.score >= 4 ? '✓ Magas megértési szint' : 
         group.score >= 3 ? '→ Közepes megértési szint' : 
         '⚠ Alacsony megértési szint - fejlesztést igényel'}
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
      {Math.abs(understandingComparison[0].score - understandingComparison[1].score).toFixed(1)} pont
    </span>
  </div>
  <p className="text-xs text-muted-foreground mt-2">
    {understandingComparison[0].score > understandingComparison[1].score 
      ? 'A használók jobban értik a szolgáltatást' 
      : 'A nem használók hasonlóan értik a szolgáltatást'}
  </p>
</div>
```

### 2.9. Használói Tudásszint Részletesen Kártya (Awareness Profile Card)

**ID:** `awareness-profile-card`
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
{awarenessProfileData.map((item, index) => (
  <div key={index} className="space-y-2">
    <div className="flex items-start justify-between">
      <div>
        <p className="font-semibold">{item.category}</p>
        <p className="text-xs text-muted-foreground">{item.description}</p>
      </div>
      <div className="text-right">
        <p className="text-2xl font-bold">{item.score}</p>
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

### 2.10. Statisztikai Összefoglaló Kártya

**Komponens:** 3 oszlopos grid
**Megjelenés:** bg-muted/50 háttér, lekerekített sarkok

**Mutatók:**
1. Válaszadók összesen: `totalCount`
2. Tudtak a programról: `awarenessResponses.length ({awarenessRate}%)`
3. Nem tudtak róla: `redirectResponses.length ({redirectRate}%)`

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
- overall-awareness-card → `osszes-ismertseg.png`
- understanding-card → `megertes.png`
- overall-knowledge-card → `altalanos-tudasszint.png`
- unawareness-card → `nem-tudtak-rola.png`
- sources-card → `informacios-forrasok.png`
- frequency-card → `kommunikacios-gyakorisag.png`
- info-sufficiency-card → `informacio-elegsegeseg.png`
- comparison-card → `megértes-osszehasonlitas.png`
- awareness-profile-card → `hasznaloi-tudasszint.png`

## 4. Layout Struktúra

```typescript
<div className="space-y-6">
  {/* Fejléc */}
  <div>
    <h2 className="text-2xl font-bold mb-2">Ismertség Részletes Elemzése</h2>
    <p className="text-muted-foreground text-sm">
      Az EAP program ismeretének, megértésének és kommunikációjának átfogó kiértékelése
    </p>
  </div>

  {/* 1. sor: Fő ismertségi mutatók (4 gauge/metric kártya) */}
  <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
    {/* Általános Ismertség, Általános Megértés, Általános Tudásszint, Nem Ismerték */}
  </div>

  {/* 2. sor: Információs források + Kommunikációs gyakoriság */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Horizontal Bar Chart + Pie Chart */}
  </div>

  {/* 3. sor: Információ elégségesség (full width) */}
  <Card id="info-sufficiency-card">
    {/* Pie Chart */}
  </Card>

  {/* 4. sor: Összehasonlítások és részletes profilok */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Szolgáltatás Megértésének Szintje + Használói Tudásszint Részletesen */}
  </div>

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
- `recharts` - BarChart, PieChart, RadarChart
- `lucide-react` - Ikonok (Download, Eye, Info, Users, TrendingUp)
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
- `responses.u_awareness_understanding` - Használók megértése (1-5)
- `responses.nu_awareness_understanding` - Nem használók megértése (1-5)
- `responses.u_awareness_how_to_use` - Használat ismerete (1-5)
- `responses.u_awareness_accessibility` - Elérhetőség érzete (1-5)
- `responses.u_awareness_source` - Információs források (array)
- `responses.nu_awareness_source` - Nem használók információs forrásai (array)
- `responses.u_awareness_frequency` - Kommunikációs gyakoriság
- `responses.u_awareness_info` - Információ elégségesség ('yes'/'no')

## 9. Kritikus Design Elemek

### Progress Bar Egységesítés
**FONTOS:** Minden progress bar magassága egységesen `h-3` az ÖSSZES tabon (Awareness, Reports, RunningAudits)!

```typescript
className="h-3"  // STANDARD MAGASSÁG
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
**FONTOS:** Minden vízszintes diagram (BarChart, PieChart) konténer magassága egységesen `h-[350px]`!

```typescript
<div className="h-[350px]">
  <ResponsiveContainer width="100%" height="100%">
```

## 10. Színhasználat

**Kategóriák szerinti színek:**
- **Használók:** `hsl(var(--chart-2))` - Kék (#3366ff)
- **Nem használók:** `hsl(var(--chart-3))` - Cián
- **Nem tudtak róla:** `hsl(var(--chart-4))` - Világos cián
- **Rotáló színek diagramokon:** `hsl(var(--chart-${(index % 4) + 1}))`

**Progress bar színezés:**
```typescript
style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
```

## 11. Responsive Viselkedés

**Grid breakpoints:**
- Mobile: 1 oszlop (`grid-cols-1`)
- Tablet: 2 oszlop (`md:grid-cols-2`)
- Desktop (1. sor): 4 oszlop (`md:grid-cols-4`)

**Gauge Chart méretei:**
- Fő ismertségi mutatók: `size={220}`
- Overview tabon: `size={280}` (nagyobb)

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
2. ✅ Hozd létre az összes számított értéket (branch szerinti szűrés, átlagok)
3. ✅ Implementáld az export funkcionalitást (exportCardToPNG)
4. ✅ Adj hozzá minden kártyát az ID-vel
5. ✅ **KRITIKUS:** Állítsd be a progress bar magasságot `h-3`-ra
6. ✅ **KRITIKUS:** Állítsd be a diagram konténerek magasságát `h-[350px]`-re
7. ✅ Használd a helyes színsémát (chart-2 alapértelmezett)
8. ✅ Implementáld az animált háttér gradienst a metric kártyákon
9. ✅ Adj hozzá z-index layeringet a relative positioned kártyákhoz
10. ✅ Teszteld az export funkcionalitást minden kártyán
11. ✅ Ellenőrizd a responsive viselkedést
12. ✅ Validáld az adatszűrést és számításokat

## 14. Gyakori Hibák és Megoldások

**Hiba:** Progress bar különböző magasságúak
**Megoldás:** Minden progress bar-hoz adjunk `className="h-3"`

**Hiba:** Diagramok különböző magasságúak
**Megoldás:** Minden diagram konténer `h-[350px]`

**Hiba:** Háttér gradiens nem jelenik meg
**Megoldás:** Ellenőrizd az opacity (0.1) és a z-index (z-10) beállításokat

**Hiba:** Export nem működik
**Megoldás:** Dinamikusan importáljuk a html2canvas-t: `(await import('html2canvas')).default`
