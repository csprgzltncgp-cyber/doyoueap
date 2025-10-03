# Hatás (Impact) Riport Dokumentáció

## Cél
A Hatás tab célja az EAP program használóinak (branch: 'used') körében mért hatás és elégedettség részletes elemzése. Ez a riport megmutatja, hogy mennyire volt hatékony a program a felhasználók számára, és hol van fejlesztési potenciál.

## Adatforrás
- **Adattábla**: `audit_responses`
- **Szűrés**: Csak azok a válaszadók, akiknél `employee_metadata.branch === 'used'`
- **Kérdések**: 
  - `u_impact_nps`: Net Promoter Score (0-10 skála)
  - `u_impact_satisfaction`: Általános elégedettség (1-5 skála)
  - `u_impact_problem_solving`: Problémamegoldás (1-5 skála)
  - `u_impact_wellbeing`: Wellbeing javulás (1-5 skála)
  - `u_impact_performance`: Teljesítmény javulás (1-5 skála)
  - `u_impact_consistency`: Szolgáltatás konzisztencia (1-5 skála)

---

## Kártyák részletes leírása

### 1. Net Promoter Score (NPS) Kártya

**Cél**: Ajánlási hajlandóság mérése, amely megmutatja, hogy a használók mennyire ajánlanák másoknak a programot.

**Vizualizáció típusa**: Számegyenes (-100-tól +100-ig)

**Számítás**:
```javascript
const npsScores = data.map(r => r.responses?.u_impact_nps).filter(v => v !== undefined && v !== null);
const promoters = npsScores.filter(score => score >= 9).length;
const passives = npsScores.filter(score => score >= 7 && score <= 8).length;
const detractors = npsScores.filter(score => score <= 6).length;
const total = npsScores.length;
const npsScore = Math.round(((promoters - detractors) / total) * 100);
```

**Kategóriák**:
- **Promóterek (9-10 pont)**: Aktív ajánlók
- **Passzívak (7-8 pont)**: Elégedettek, de nem aktív ajánlók (nem számítanak bele az NPS-be)
- **Kritikusok (0-6 pont)**: Elégedetlenek

**Értékelési skála**:
- **+50 felett**: Kiváló
- **0 körül**: Semleges (egyensúlyban vannak az ajánlók és kritikusok)
- **0 alatt**: Fejlesztendő

**Színezés**:
- Kék (#3366ff átmenetek): 0 feletti értékek
- Piros (`--destructive`): 0 és alatti értékek
- Számegyenes: Világoskék -> közepes kék -> sötét kék átmenet
- Indikátor: Legsötétebb kék (#050c9c)

**Magyarázó szöveg**: 
- Tartalmazza az NPS skála magyarázatát
- Megmutatja, hogy mit jelentenek a -100-tól +100-ig terjedő értékek
- Közli a kérdést: "0-10 skálán mennyire ajánlaná kollégáinak a programot?"

**Export**: PNG formátum, fájlnév: `hatás-nps.png`

---

### 2. Átlagos Hatás Érték Kártya

**Cél**: Az 5 hatás terület összesített átlagának megjelenítése egyetlen mutatóban.

**Vizualizáció típusa**: Ív diagram (Gauge Chart)

**Számítás**:
```javascript
const avgImpact = impactData.length > 0 
  ? Number((impactData.reduce((sum, m) => sum + m.average, 0) / impactData.length).toFixed(2))
  : 0;
```

**Skála**: 1-5 (Likert skála átlaga)

**Értékelési szint**:
- **4.5 felett**: Kiváló hatás
- **3.5-4.5**: Jó hatás
- **2.5-3.5**: Közepes hatás
- **2.5 alatt**: Fejlesztendő

**Magyarázó szöveg**:
- **Értékelt területek**: Elégedettség, Problémamegoldás, Wellbeing, Teljesítmény, Konzisztencia
- Az 5 terület összesített átlaga
- Egyetlen számban összefoglalva látható a program összhatása

**Export**: PNG formátum, fájlnév: `hatás-átlag.png`

---

### 3. Hatás Területek Kártya

**Cél**: Mind az 5 hatás terület egyenkénti részletes megjelenítése külön gauge diagramokon.

**Vizualizáció típusa**: 5 darab ív diagram (Gauge Chart) rácselrendezésben

**Területek**:

1. **Elégedettség** (`u_impact_satisfaction`)
   - Magyarázat: "Általános elégedettség a programmal"
   - Skála: 1-5

2. **Problémamegoldás** (`u_impact_problem_solving`)
   - Magyarázat: "Mennyire segített a program a problémák kezelésében"
   - Skála: 1-5

3. **Wellbeing javulás** (`u_impact_wellbeing`)
   - Magyarázat: "Jóllét és mentális egészség javulása"
   - Skála: 1-5

4. **Teljesítmény javulás** (`u_impact_performance`)
   - Magyarázat: "Munkahelyi teljesítmény pozitív változása"
   - Skála: 1-5

5. **Szolgáltatás konzisztencia** (`u_impact_consistency`)
   - Magyarázat: "A szolgáltatás minőségének következetessége"
   - Skála: 1-5

**Számítás módszer** (minden területre):
```javascript
const values = data
  .map(r => r.responses?.u_impact_[area])
  .filter(v => v !== undefined && v !== null);
const average = values.reduce((a, b) => a + b, 0) / values.length;
```

**Elrendezés**: 
- Grid: `grid-cols-1 md:grid-cols-2 lg:grid-cols-3`
- Minden diagram külön háttérrel (`bg-muted/30`) és kerekített sarkokkal
- Magyarázó szöveg az ív alatt

**Export**: PNG formátum, fájlnév: `hatás-területek.png`

---

### 4. Hatás Profil Kártya (Radar Diagram)

**Cél**: Az 5 hatás terület összehasonlító, vizuális áttekintése radar diagramon.

**Vizualizáció típusa**: Radar Chart (pókhálószerű diagram)

**Adatok**: Ugyanazok az 5 terület mint a Hatás Területek kártyánál

**Magyarázó szöveg**:
- Mit mutat a diagram: "A radar diagram az 5 hatás terület egyidejű áttekintését teszi lehetővé"
- Értelmezés: "Minél nagyobb a kitöltött terület, annál jobb az összhatás"
- Kiegyensúlyozottság: "Az egyenletes ötszög kiegyensúlyozott hatást jelez"

**Fejlesztési javaslatok (dinamikus)**:

A kártya alján egy piros (`bg-destructive/10`, `border-destructive/20`) sávban jelennek meg a javaslatok:

**Logika**:
```javascript
const lowestMetric = impactData.reduce((min, item) => 
  item.average < min.average ? item : min
);
const allBelowFour = impactData.filter(item => item.average < 4);

if (lowestMetric.average >= 4.5) {
  // Kiváló eredmények
  return "Kiváló eredmények minden területen! Folytassák így!";
}

if (allBelowFour.length >= 3) {
  // Több terület fejlesztendő
  return "Több területen is érdemes fejleszteni. Kezdjék a {lowestMetric.metric} területtel ({value}), majd folytassák a többi alacsonyabb értékű területtel.";
}

// Egy terület kiemelkedően gyenge
return "A legnagyobb fejlesztési potenciál a {lowestMetric.metric} területen van (jelenlegi érték: {value}). Érdemes külön figyelmet fordítani erre a területre.";
```

**Javaslatok megjelenítése**:
- Piros háttér (`bg-destructive/10`)
- Piros keret (`border-destructive/20`)
- AlertTriangle ikon (piros, `w-4 h-4`)
- Cím: "Fejlesztési javaslatok" (piros színnel)
- Dinamikus szöveg a logika alapján (magázó forma)

**Export**: PNG formátum, fájlnév: `hatás-profil.png`

---

## Technikai részletek

### State Management
```javascript
const [impactData, setImpactData] = useState<ImpactMetric[]>([]);
const [npsData, setNpsData] = useState<NPSData>({ 
  promoters: 0, 
  passives: 0, 
  detractors: 0, 
  npsScore: 0 
});
const [loading, setLoading] = useState(true);
const [usedCount, setUsedCount] = useState(0);
```

### Data Fetching
```javascript
const fetchImpactData = async (auditId: string) => {
  const { data, error } = await supabase
    .from('audit_responses')
    .select('responses, employee_metadata')
    .eq('audit_id', auditId)
    .eq('employee_metadata->>branch', 'used');
  
  // Feldolgozás és state frissítés
};
```

### Export funkció
Minden kártya rendelkezik PNG export funkcióval:
```javascript
const exportCardToPNG = async (cardId: string, fileName: string) => {
  const element = document.getElementById(cardId);
  const canvas = await html2canvas(element, {
    backgroundColor: '#ffffff',
    scale: 2,
    logging: false,
  });
  // Letöltés
};
```

---

## Design döntések

### Színpaletta
- **Elsődleges kék**: `#3366ff` (diagramok alapszíne)
- **Sötét kék**: `#050c9c` (NPS indikátor)
- **Piros**: `hsl(var(--destructive))` = `hsl(0, 84%, 60%)` (fejlesztendő értékek)
- **Háttér árnyalatok**: `bg-muted/50`, `bg-muted/30`

### Ikonok
- Letöltés gomb: `Download` ikon (Lucide React)
- Fejlesztési javaslatok: `AlertTriangle` ikon (piros)

### Elrendezés
- Grid layout: 2 oszlopos fő metrikák (NPS, Átlag)
- 3 oszlopos részletes területek (responsive: 1->2->3)
- Teljes széles radar és fejlesztési javaslatok

### Magyarázó szövegek formátuma
```tsx
<div className="mb-4 p-3 bg-muted/50 rounded-lg text-sm">
  <p className="text-muted-foreground">
    <strong>Cím:</strong> Szöveg
  </p>
</div>
```

---

## Üres állapot kezelése

Ha nincs adat (`usedCount === 0`):
```tsx
<div className="text-center py-12 text-muted-foreground">
  Még nincs kiértékelt adat a programot használók körében
</div>
```

---

## Fő különbségek a többi tabtól

1. **NPS számegyenes**: Egyedi vizualizáció -100-tól +100-ig
2. **5 különálló gauge**: Minden metrika külön diagrammal
3. **Radar diagram**: Összehasonlító nézet
4. **Dinamikus javaslatok**: Automatikus értékelés és fejlesztési tanácsok
5. **Piros/kék színezés**: Értékalapú színválasztás (0 alatt piros)
6. **Magázó forma**: Professzionális kommunikáció

---

## Jövőbeli fejlesztési lehetőségek

1. Trendek megjelenítése időben (ha több felmérés van)
2. Demográfiai csoportok szerinti bontás
3. NPS score részletesebb elemzése (comment-ek ha vannak)
4. Export PDF formátumban is
5. Összehasonlítás benchmark-okkal (iparági átlagok)
6. Email/Presentation ready formátum

---

## Karbantartási jegyzetek

- **FONTOS**: A színek mindig a design system tokeneket használják (`--destructive`, `--primary`)
- **FONTOS**: Minden szöveg magázó formában van
- **FONTOS**: Az NPS számítása szigorúan követi a hivatalos NPS módszertant
- **FONTOS**: Az átlagszámítások csak defined és non-null értékeket vesznek figyelembe
- **FONTOS**: Az emoji-k el lettek távolítva minden helyről (felhasználói kérésre)
