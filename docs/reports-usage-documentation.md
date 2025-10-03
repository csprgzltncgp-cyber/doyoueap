# Használat (Usage) Riport Dokumentáció

## Áttekintés

A Használat riport az EAP program használati szokásainak, csatornáinak és témáinak átfogó kiértékelését nyújtja. A riport kizárólag azokra a válaszadókra fókuszál, akik már igénybe vették a programot (`branch === 'used'`).

**Elérési út:** `Reports > Használat`  
**Komponens:** `src/pages/hr/Usage.tsx`  
**Adatforrás:** `audit_responses` tábla, szűrve `employee_metadata.branch === 'used'`

---

## Fejléc

```
Használat Részletes Elemzése
Az EAP program használati szokásainak, csatornáinak és témáinak átfogó kiértékelése
```

---

## 1. Fő Használati Mutatók (4 kártya)

### 1.1 Használói Arány

**Típus:** Gauge Chart (íves mérő)  
**Metrika:** Használók aránya az összes válaszadón belül

**Számítás:**
```javascript
usageRate = (usedResponses.length / totalCount) * 100
```

**Megjelenítés:**
- Gauge chart 220px méretben
- Központi címke: `{usageRate}%`
- Alcímke: `{usedResponses.length} / {totalCount} fő`
- Magyarázat: "A válaszolók közül ennyien használták a programot"

**Export:** `hasznalok-aranya.png`

---

### 1.2 Családi Használat

**Típus:** Gauge Chart (íves mérő)  
**Metrika:** Hányan vették igénybe családtaggal együtt

**Adatforrás:**
- `u_usage_family === 'yes'` vagy `'Igen'` → Család is használta
- `u_usage_family === 'no'` vagy `'Nem'` → Csak a válaszadó használta

**Számítás:**
```javascript
familyYes = responses.filter(r => r.responses?.u_usage_family === 'yes' || 'Igen').length
familyNo = responses.filter(r => r.responses?.u_usage_family === 'no' || 'Nem').length
familyRate = (familyYes / (familyYes + familyNo)) * 100
```

**Megjelenítés:**
- Gauge chart 220px méretben
- Központi címke: `{familyRate}%`
- Alcímke: `{familyYes} / {familyTotal} fő`
- Magyarázat: "A használók közül ennyien családtaggal együtt vették igénybe"

**Export:** `csaladi-hasznalat.png`

---

### 1.3 Leggyakoribb Téma

**Típus:** Gauge Chart (íves mérő) + dinamikus háttér  
**Metrika:** Legtöbbet használt szolgáltatási téma

**Adatforrás:** `u_usage_topic` (többszörös választás, array)

**Számítás:**
```javascript
topicData = {}
usedResponses.forEach(r => {
  if (Array.isArray(r.responses?.u_usage_topic)) {
    r.responses.u_usage_topic.forEach(topic => {
      topicData[topic] = (topicData[topic] || 0) + 1
    })
  }
})
topTopic = Object.entries(topicData).sort((a, b) => b[1] - a[1])[0]
```

**Megjelenítés:**
- Gauge chart 220px méretben
- Központi címke: `{percentage}%` (nagy betűvel)
- Alcímke: `{topTopic.name}` (pl. "Pszichológiai")
- Magyarázat: Dinamikus szöveg az átlagos témaszám alapján
  - Ha avgTopicsPerUser >= 2: "A használók átlagosan több témában is igénybe veszik a szolgáltatást"
  - Egyébként: "A használók általában egy-két témában veszik igénybe"

**Export:** `leggyakoribb-temak.png`

---

### 1.4 Leggyakoribb Csatorna

**Típus:** Gauge Chart (íves mérő)  
**Metrika:** Preferált kommunikációs csatorna

**Adatforrás:** `u_usage_channel` (többszörös választás, array)

**Számítás:**
```javascript
channelData = {}
usedResponses.forEach(r => {
  if (Array.isArray(r.responses?.u_usage_channel)) {
    r.responses.u_usage_channel.forEach(channel => {
      channelData[channel] = (channelData[channel] || 0) + 1
    })
  }
})
topChannel = Object.entries(channelData).sort((a, b) => b[1] - a[1])[0]
```

**Megjelenítés:**
- Gauge chart 220px méretben
- Központi címke: `{percentage}%` (nagy betűvel)
- Alcímke: `{topChannel.name}` (pl. "Chat")
- Magyarázat: Dinamikus szöveg az átlagos csatornaszám alapján
  - Ha avgChannelsPerUser >= 2: "A használók többféle csatornát is kipróbálnak"
  - Egyébként: "A használók általában egy csatornát preferálnak"

**Export:** `leggyakoribb-csatorna.png`

---

## 2. Használat Gyakorisága és Családi Használat (2 kártya)

### 2.1 Használat Gyakorisága

**Típus:** Donut Chart (kördiagram üres középpel)  
**Metrika:** Hányszor fordultak a szolgáltatáshoz

**Adatforrás:** `u_usage_frequency`

**Gyakoriság kategóriák:**
- "1" - Egyszeri használat
- "2-3" - 2-3 alkalom
- "4-6" - 4-6 alkalom
- "Rendszeresen" - Több mint 6 alkalom

**Számítás:**
```javascript
frequencyData = {}
usedResponses.forEach(r => {
  const freq = r.responses?.u_usage_frequency
  if (freq) {
    frequencyData[freq] = (frequencyData[freq] || 0) + 1
  }
})
```

**Megjelenítés:**
- Donut chart 300px magasságban
- Belső sugár: 60px, külső sugár: 120px
- Padding angle: 2
- Címkék: NEM láthatóak a diagramon
- Jelmagyarázat alul: `{name} alkalom: {percentage}%`
  - Példa: "2-3 alkalom: 67%"
  - Példa: "4-6 alkalom: 33%"
- Színek: `hsl(var(--chart-1))` - `hsl(var(--chart-4))` rotációban
- Magyarázó szöveg: "Ez mutatja, hogy a használók hányszor (hány alkalommal) fordultak a szolgáltatáshoz. Például 1 alkalommal, 2-3 alkalommal, 4-6 alkalommal, vagy rendszeresen (több mint 6 alkalom)."

**Export:** `hasznalati-gyakorisag.png`

---

### 2.2 Családi Használat Megoszlása

**Típus:** Donut Chart (kördiagram üres középpel)  
**Metrika:** Hozzátartozók bevonása a szolgáltatásba

**Adatforrás:** `u_usage_family`

**Számítás:**
```javascript
familyChartData = [
  { 
    name: 'Igen, család is használta', 
    value: familyYes, 
    color: 'hsl(var(--chart-2))' 
  },
  { 
    name: 'Csak én használtam', 
    value: familyNo, 
    color: 'hsl(var(--chart-3))' 
  }
].filter(item => item.value > 0)
```

**Megjelenítés:**
- Donut chart 350px magasságban
- Belső sugár: 60px, külső sugár: 120px
- Padding angle: 2
- Jelmagyarázat alul: `{name}: {value} ({percentage}%)`

**Export:** `csaladi-hasznalat-eloszlas.png`

---

## 3. Igénybe Vett Témakörök

**Típus:** Donut Chart (kördiagram üres középpel)  
**Metrika:** Milyen problémák kapcsán keresték meg a szolgáltatást

**Adatforrás:** `u_usage_topic` (többszörös választás, array)

**Lehetséges témák:**
- Pszichológiai
- Jogi
- Pénzügyi
- Karriertanácsadás
- Családi problémák
- stb.

**Számítás:**
```javascript
topicData = {}
usedResponses.forEach(r => {
  const topics = r.responses?.u_usage_topic
  if (Array.isArray(topics)) {
    topics.forEach(topic => {
      topicData[topic] = (topicData[topic] || 0) + 1
    })
  }
})
topicChartData = Object.entries(topicData)
  .map(([name, value]) => ({ name, value }))
  .sort((a, b) => b.value - a.value)
```

**Megjelenítés:**
- Donut chart 350px magasságban
- Belső sugár: 60px, külső sugár: 120px
- Padding angle: 2
- Címkék: NEM láthatóak a diagramon
- Jelmagyarázat alul: Első 6 téma
  - Formátum: `{name}: {percentage}%`
  - Példa: "Pszichológiai: 50%"
  - Példa: "Jogi: 50%"
- Ha több mint 6 téma: "+X további téma"
- Színek: `hsl(var(--chart-1))` - `hsl(var(--chart-4))` rotációban
- Lábjegyzet: "Átlagosan {avgTopicsPerUser} témakört említenek a használók"

**Átlagos témaszám:**
```javascript
avgTopicsPerUser = usedResponses.reduce((sum, r) => 
  sum + (Array.isArray(r.responses?.u_usage_topic) ? r.responses.u_usage_topic.length : 0), 0
) / usedResponses.length
```

**Export:** `temakorok.png`

---

## 4. Használt Csatornák és Időtartam (2 kártya)

### 4.1 Használt Csatornák

**Típus:** Donut Chart (kördiagram üres középpel)  
**Metrika:** Elérési módok preferenciája

**Adatforrás:** `u_usage_channel` (többszörös választás, array)

**Lehetséges csatornák:**
- Chat
- Telefon
- Email
- Videó hívás
- Személyes találkozó
- stb.

**Számítás:**
```javascript
channelData = {}
usedResponses.forEach(r => {
  const channels = r.responses?.u_usage_channel
  if (Array.isArray(channels)) {
    channels.forEach(channel => {
      channelData[channel] = (channelData[channel] || 0) + 1
    })
  }
})
```

**Megjelenítés:**
- Donut chart 350px magasságban
- Belső sugár: 60px, külső sugár: 120px
- Padding angle: 2
- Címkék: NEM láthatóak a diagramon
- Jelmagyarázat alul: `{name}: {percentage}%`
- Színek: `hsl(var(--chart-1))` - `hsl(var(--chart-4))` rotációban
- Lábjegyzet: "Átlagosan {avgChannelsPerUser} csatornát próbálnak ki a használók"

**Átlagos csatornaszám:**
```javascript
avgChannelsPerUser = usedResponses.reduce((sum, r) => 
  sum + (Array.isArray(r.responses?.u_usage_channel) ? r.responses.u_usage_channel.length : 0), 0
) / usedResponses.length
```

**Export:** `csatornak.png`

---

### 4.2 Időtartam Gondoskodásig

**Típus:** Donut Chart (kördiagram üres középpel)  
**Metrika:** Mennyi idő alatt kaptak ellátást

**Adatforrás:** `u_usage_time_to_care`

**Lehetséges kategóriák:**
- "Azonnal (24 órán belül)"
- "1-3 napon belül"
- "1 héten belül"
- "1-2 héten belül"
- "Több mint 2 hét"

**Számítás:**
```javascript
timeToCareData = {}
usedResponses.forEach(r => {
  const time = r.responses?.u_usage_time_to_care
  if (time) {
    timeToCareData[time] = (timeToCareData[time] || 0) + 1
  }
})
```

**Megjelenítés:**
- Donut chart 350px magasságban
- Belső sugár: 60px, külső sugár: 120px
- Padding angle: 2
- Címkék: NEM láthatóak a diagramon
- Jelmagyarázat alul: `{name}: {percentage}%`
- Színek: `hsl(var(--chart-1))` - `hsl(var(--chart-4))` rotációban

**Export:** `idotartam-gondoskodasig.png`

---

## Interaktív Funkciók

### Export PNG

Minden kártya jobb felső sarkában található egy letöltés gomb:
- Ikon: Download (Lucide React)
- Funkció: `exportCardToPNG(cardId, fileName)`
- Technológia: html2canvas
- Beállítások:
  - Háttér: fehér (#ffffff)
  - Skála: 2x (nagy felbontás)
- Visszajelzés: Toast üzenet sikeres/sikertelen exportáláskor

### Vizuális Visszajelzés

- **Betöltési állapot:** "Betöltés..." üzenet központozva
- **Hibaüzenetek:** Toast értesítések
- **Tooltip:** Minden diagramon hover-re megjelenik részletes információ

---

## Technikai Részletek

### Használt könyvtárak
```javascript
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, 
         ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { GaugeChart } from '@/components/ui/gauge-chart';
import html2canvas from 'html2canvas';
```

### Színpaletta
- `--chart-1`: Első szín (forgó használat)
- `--chart-2`: Második szín (kiemelt metrikák)
- `--chart-3`: Harmadik szín (alternatív)
- `--chart-4`: Negyedik szín (kiegészítő)

### Adatszűrés
```javascript
const usedResponses = responses.filter(r => r.employee_metadata?.branch === 'used');
```

### Százalék számítás sablon
```javascript
const total = chartData.reduce((sum, item) => sum + item.value, 0);
const percentage = total > 0 ? ((item.value / total) * 100).toFixed(0) : 0;
```

---

## Fontosabb Design Döntések

1. **NEM használunk darabszámokat** - Csak százalékok a jelmagyarázatokban és a diagramokon
2. **Kördiagramok következetessége** - Minden diagram donut chart formátumú (kivéve a gauge chartok)
3. **Százalék középen, név alatta** - A gauge chartokon a százalék a fő címke
4. **Dinamikus színezés** - Rotációs színhasználat a chart-1 - chart-4 között
5. **Magyarázó szövegek** - Minden komplex metrikához tartozik segítő szöveg
6. **Átlagok kimutatása** - Az átlagos témaszám és csatornaszám segít értelmezni a használati mintákat

---

## Karbantartási Jegyzetek

- A `u_usage_*` prefixű mezők a használói ágon (used branch) lévő kérdésekhez tartoznak
- Az array típusú mezők (topics, channels) többszörös választást engedélyeznek
- A family használat külön mérőszámként és külön eloszlásként is szerepel
- Az export funkció minden kártyán egyenként működik, nincs teljes oldal export
- A gauge chartok automatikusan skálázódnak 0-100% között

---

**Utolsó frissítés:** 2025-01-03  
**Verzió:** 1.0  
**Felelős:** HR Dashboard fejlesztő csapat
