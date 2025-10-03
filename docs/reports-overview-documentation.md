# Reports - Overview Tab - Teljes Dokumentáció

Ez a dokumentáció tartalmazza az Overview tab összes részletét, számítását, megjelenését és funkcióit.

## Színséma

A használt színek:
- **chart-1**: `240 100% 30%` (sötétkék) - NEM HASZNÁLT
- **chart-2**: `225 100% 60%` (#3366ff) - ALAPÉRTELMEZETT KÉK, minden diagram és progress bar színe
- **chart-3**: `195 100% 60%` (cián) - Nem használók kategória
- **chart-4**: `180 100% 80%` (világos cián) - Nem tudtak róla kategória

## 1. Főbb Számított Értékek

### Alapadatok
```typescript
const totalResponses = responses.length;
const usedBranch = responses.filter(r => r.employee_metadata?.branch === 'used').length;
const notUsedBranch = responses.filter(r => r.employee_metadata?.branch === 'not_used').length;
const redirectBranch = responses.filter(r => r.employee_metadata?.branch === 'redirect').length;
const employeeCount = // Profiles táblából, user adatai alapján
```

### Igénybevétel (Utilization)
```typescript
const utilization = totalResponses > 0 ? (usedBranch / totalResponses) * 100 : 0;
const usageRateFromRespondents = totalResponses > 0 ? (usedBranch / totalResponses) * 100 : 0;
const estimatedUsers = Math.round((utilization / 100) * employeeCount);
```

**Számítási logika:**
- A válaszolók közül hányan használták a programot (usedBranch / totalResponses)
- Ezt az arányt vetítjük ki a teljes létszámra
- Példa: Ha 14-ből 4 használta (28.6%), akkor 100 főből ~29 fő

### Részvételi arány (Participation Rate)
```typescript
const participationRate = employeeCount > 0 ? (totalResponses / employeeCount) * 100 : 0;
```

**Számítási logika:**
- Teljes válaszadók száma / teljes létszám * 100
- Példa: 14 válasz / 100 fő = 14%

### Elégedettségi Index (Satisfaction Index)
```typescript
const satisfactionScore = calculateAverage(
  responses
    .filter(r => r.employee_metadata?.branch === 'used')
    .map(r => r.responses?.u_impact_satisfaction)
    .filter(v => v !== undefined)
);
const satisfactionIndex = (parseFloat(satisfactionScore) / 5) * 100;
```

**Számítási logika:**
- Csak a használók válaszait nézzük (branch === 'used')
- `u_impact_satisfaction` mező értékeinek átlaga (1-5 skála)
- Átlagot 100-as skálára konvertáljuk

### 4Score Metrikák

#### Awareness (Ismertség)
```typescript
const awarenessResponses = responses.filter(r => 
  r.employee_metadata?.branch === 'used' || r.employee_metadata?.branch === 'not_used'
);

const awarenessScore = calculateAverage(
  awarenessResponses
    .map(r => r.responses?.u_awareness_understanding || r.responses?.nu_awareness_understanding)
    .filter(v => v !== undefined)
);
```

#### Trust (Bizalom)
```typescript
const trustResponses = responses.filter(r => 
  r.employee_metadata?.branch === 'used' || r.employee_metadata?.branch === 'not_used'
);

const trustScore = calculateAverage(
  trustResponses
    .map(r => r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity)
    .filter(v => v !== undefined)
);
```

#### Usage (Használat) - Problémamegoldás
```typescript
const problemSolvingScore = calculateAverage(
  responses
    .filter(r => r.employee_metadata?.branch === 'used')
    .map(r => r.responses?.u_impact_problem_solving)
    .filter(v => v !== undefined)
);
```

#### Impact (Hatás) - Wellbeing
```typescript
const wellbeingScore = calculateAverage(
  responses
    .filter(r => r.employee_metadata?.branch === 'used')
    .map(r => r.responses?.u_impact_wellbeing)
    .filter(v => v !== undefined)
);
```

### További Elégedettségi Mutatók

```typescript
const npsScore = calculateAverage(
  responses
    .filter(r => r.employee_metadata?.branch === 'used')
    .map(r => r.responses?.u_impact_nps)
    .filter(v => v !== undefined)
);

const performanceScore = calculateAverage(
  responses
    .filter(r => r.employee_metadata?.branch === 'used')
    .map(r => r.responses?.u_impact_performance)
    .filter(v => v !== undefined)
);

const consistencyScore = calculateAverage(
  responses
    .filter(r => r.employee_metadata?.branch === 'used')
    .map(r => r.responses?.u_impact_consistency)
    .filter(v => v !== undefined)
);

const usageScore = employeeCount > 0 ? ((usedBranch / employeeCount) * 100).toFixed(1) : '0.0';
```

## 2. Kártyák Részletes Specifikációja

### 2.1. Igénybevétel Kártya (Utilization Card)

**ID:** `utilization-card`
**Komponens:** GaugeChart
**Paraméterek:**
```typescript
<GaugeChart 
  value={utilization} 
  maxValue={100}
  size={280}
  label={`${utilization.toFixed(1)}%`}
  sublabel={`~${estimatedUsers} / ${employeeCount} fő (becsült)`}
  cornerRadius={30}
/>
```

**Megjelenés:**
- Félkör alakú gauge chart
- Méret: 280px
- Kék színnel töltődik (#3366ff, chart-2)
- Lekerekített végek (cornerRadius: 30)
- Szürke háttér az üres részre
- Középen nagy számmal a százalék
- Alatta a becsült felhasználószám

**Leírás szöveg:**
"Mivel a tényleges használói számot csak a szolgáltató ismeri, mi a felmérésből kapott arányokat vetítjük ki. A válaszolók X%-a használta a programot, ezt az arányt alkalmazzuk a teljes Y fős létszámra."

### 2.2. Elégedettségi Index Kártya (Satisfaction Index Card)

**ID:** `satisfaction-card`
**Komponens:** GaugeChart
**Paraméterek:**
```typescript
<GaugeChart 
  value={satisfactionIndex} 
  maxValue={100}
  size={280}
  label={`${satisfactionIndex.toFixed(0)}%`}
  sublabel={`${satisfactionScore}/5`}
  cornerRadius={30}
/>
```

**Megjelenés:**
- Félkör alakú gauge chart
- Méret: 280px
- Kék színnel töltődik (#3366ff, chart-2)
- Lekerekített végek (cornerRadius: 30)
- Középen a százalékos érték
- Alatta az eredeti 1-5 skálás érték

**Leírás szöveg:**
"Ez a mutató azt méri, hogy az EAP programot használók mennyire elégedettek a szolgáltatással. Az érték az általános elégedettséget tükrözi 1-5 skálán, százalékos formában megjelenítve."

### 2.3. Részvételi Arány Kártya (Participation Card)

**ID:** `participation-card`
**Komponens:** PieChart (Recharts)
**Adatok:**
```typescript
const pieData = [
  { name: 'Használók', value: usedBranch, color: 'hsl(var(--chart-2))' },
  { name: 'Nem használók', value: notUsedBranch, color: 'hsl(var(--chart-3))' },
  { name: 'Nem tudtak róla', value: redirectBranch, color: 'hsl(var(--chart-4))' },
].filter(item => item.value > 0);
```

**Paraméterek:**
```typescript
<Pie
  data={pieData}
  cx="50%"
  cy="50%"
  innerRadius={60}
  outerRadius={100}
  paddingAngle={2}
  dataKey="value"
/>
```

**Megjelenés:**
- Donut chart (belül üres)
- Magasság: 250px
- NEM jelenít meg labeleket közvetlenül a diagramon
- Alul legenda színes pöttyökkel:
  - Használók: kék (#3366ff)
  - Nem használók: cián
  - Nem tudtak róla: világos cián
- Formátum: "Kategória: szám (százalék%)"

**Leírás szövegek:**
"A részvételi arány azt mutatja, hogy a munkavállalók hány százaléka töltötte ki a felmérést. Jelenleg X fő válaszolt Y alkalmazottból."

"A kitöltők három kategóriába sorolhatók:
- **Használók:** Azok, akik ismerik ÉS használták az EAP programot
- **Nem használók:** Azok, akik tudnak a programról, de még nem használták
- **Nem tudtak róla:** Azok, akik nem hallottak a programról"

### 2.4. Elégedettségi Mutatók Kártya (Satisfaction Metrics Card)

**ID:** `satisfaction-metrics-card`
**Komponens:** Progress bar sorozat
**Minden progress bar:**
```typescript
style={{ '--progress-background': 'hsl(var(--chart-2))' } as React.CSSProperties}
```

**Mutatók:**

1. **Általános elégedettség**
   - Érték: satisfactionScore/5
   - Progress: parseFloat(satisfactionScore) * 20

2. **NPS átlag**
   - Érték: npsScore/10
   - Progress: parseFloat(npsScore) * 10
   - Leírás: "Ajánlási hajlandóság: mennyire ajánlaná másoknak a programot"

3. **Problémamegoldás**
   - Érték: problemSolvingScore/5
   - Progress: parseFloat(problemSolvingScore) * 20
   - Leírás: "Mennyire segített a program a probléma megoldásában"

4. **Teljesítmény hatás**
   - Érték: performanceScore/5
   - Progress: parseFloat(performanceScore) * 20
   - Leírás: "A program hatása a munkahelyi teljesítményre"

5. **Wellbeing hatás**
   - Érték: wellbeingScore/5
   - Progress: parseFloat(wellbeingScore) * 20
   - Leírás: "A program hatása az általános jóllétre és mentális egészségre"

6. **Konzisztencia**
   - Érték: consistencyScore/5
   - Progress: parseFloat(consistencyScore) * 20
   - Leírás: "Mennyire volt konzisztens a szolgáltatás minősége minden alkalommal"

**Megjelenés:**
- Minden mutatóhoz progress bar kék színnel (#3366ff)
- Jobbra az érték számmal
- Alatta kis szürkén a leírás

### 2.5-2.8. 4Score Kártyák (Ismertség, Bizalom, Használat, Hatás)

**Közös megjelenés:**
- Relatív pozíció a kék háttér gradiens miatt
- Háttér: linear-gradient alulról felfelé
  - Start: hsl(var(--chart-2)) 0%
  - End: érték alapján (érték/5)*100%
  - Átlátszóság: 0.1
- Z-index kezelés: header és content relative z-10

#### 2.5. Ismertség Kártya (Awareness Card)

**ID:** `awareness-card`
**Érték:** awarenessScore
**Szín:** chart-2 (#3366ff)
**Icon:** Eye

```typescript
<div className="text-center">
  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
    {awarenessScore}
  </div>
  <p className="text-sm text-muted-foreground mt-2">
    Mennyire értik a munkavállalók a szolgáltatást
  </p>
</div>
```

**Háttér töltődés:** (awarenessScore / 5) * 100%

#### 2.6. Bizalom Kártya (Trust Card)

**ID:** `trust-card`
**Érték:** trustScore
**Szín:** chart-2 (#3366ff)
**Icon:** Shield

```typescript
<div className="text-center">
  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
    {trustScore}
  </div>
  <p className="text-sm text-muted-foreground mt-2">
    Mennyire bíznak az anonimitás védelmében
  </p>
</div>
```

**Háttér töltődés:** (trustScore / 5) * 100%

#### 2.7. Használat Kártya (Usage Card)

**ID:** `usage-card`
**Érték:** problemSolvingScore
**Szín:** chart-2 (#3366ff)
**Icon:** Users
**Alcím:** "Problémamegoldás (1-5 skála)"

```typescript
<div className="text-center">
  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
    {problemSolvingScore}
  </div>
  <p className="text-sm text-muted-foreground mt-2">
    Mennyire segített a program a problémák kezelésében
  </p>
</div>
```

**Háttér töltődés:** (problemSolvingScore / 5) * 100%

#### 2.8. Hatás Kártya (Impact Card)

**ID:** `impact-card`
**Érték:** wellbeingScore
**Szín:** chart-2 (#3366ff)
**Icon:** TrendingUp

```typescript
<div className="text-center">
  <div className="text-6xl font-bold" style={{ color: 'hsl(var(--chart-2))' }}>
    {wellbeingScore}
  </div>
  <p className="text-sm text-muted-foreground mt-2">
    Jóllét javulása a program használata után
  </p>
</div>
```

**Háttér töltődés:** (wellbeingScore / 5) * 100%

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

**Minden kártyán jobb felső sarokban:**
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
- utilization-card → `igenybeveltel.png`
- satisfaction-card → `elegedettsegi-index.png`
- participation-card → `reszveteli-arany.png`
- satisfaction-metrics-card → `elegedettsegi-mutatok.png`
- awareness-card → `ismertseg.png`
- trust-card → `bizalom.png`
- usage-card → `hasznalat.png`
- impact-card → `hatas.png`

## 4. Layout Struktúra

```typescript
<div className="space-y-6">
  {/* 1. sor: Igénybevétel + Elégedettségi Index */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Utilization + Satisfaction Gauge Charts */}
  </div>

  {/* 2. sor: Részvételi arány + Elégedettségi mutatók */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Participation Pie Chart + Satisfaction Metrics Progress Bars */}
  </div>

  {/* 3. sor: 4Score kártyák */}
  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    {/* Awareness, Trust, Usage, Impact */}
  </div>
</div>
```

## 5. Használt Recharts Komponensek

```typescript
import { 
  PieChart, 
  Pie, 
  Cell, 
  ResponsiveContainer, 
  Tooltip 
} from "recharts";
```

**GaugeChart** - Saját komponens: `src/components/ui/gauge-chart.tsx`

## 6. Függőségek

- `html2canvas` - PNG export
- `recharts` - Pie chart
- `lucide-react` - Ikonok (Download, Eye, Shield, Users, TrendingUp)
- `sonner` - Toast notifications

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
- `profiles` - Felhasználói adatok (employee_count)

**Főbb mezők:**
- `responses.employee_metadata.branch` - 'used', 'not_used', 'redirect'
- `responses.u_impact_satisfaction` - Általános elégedettség
- `responses.u_impact_nps` - NPS
- `responses.u_impact_problem_solving` - Problémamegoldás
- `responses.u_impact_performance` - Teljesítmény
- `responses.u_impact_wellbeing` - Wellbeing
- `responses.u_impact_consistency` - Konzisztencia
- `responses.u_awareness_understanding` / `nu_awareness_understanding` - Ismertség
- `responses.u_trust_anonymity` / `nu_trust_anonymity` - Bizalom

## 9. Kritikus Design Elemek

1. **Minden szín HSL formátumban** kell legyen
2. **chart-2 (#3366ff)** az egyetlen használt kék szín
3. **Progress bar-ok** custom stílussal: `style={{ '--progress-background': 'hsl(var(--chart-2))' }}`
4. **4Score kártyák** gradient háttérrel, alulról felfelé töltődnek
5. **Pie chart** label nélkül, alul legenda pöttyökkel
6. **Gauge chart-ok** 280px mérettel, 30px cornerRadius-szal
7. **Export gombok** absolute pozícióban, jobb felső sarokban

## 10. Responsive Breakpoints

- `md:grid-cols-2` - 768px felett 2 oszlop
- `lg:grid-cols-4` - 1024px felett 4 oszlop (4Score kártyák)

---

**Utolsó frissítés:** 2025-10-03
**Verzió:** 1.0
**Fájl:** src/pages/hr/Reports.tsx (Overview tab)
