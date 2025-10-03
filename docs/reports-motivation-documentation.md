# Motiváció Riport Dokumentáció

## Áttekintés
A Motiváció riport a **nem használó válaszadók** motivációs tényezőit, preferenciáit és igényeit elemzi. Ez a riport megmutatja, hogy mi ösztönözné a nem használókat az EAP program igénybevételére.

## Célcsoport
- **Kizárólag nem használók**: Csak azok a válaszadók, akik az `employee_metadata.branch = 'not_used'` kategóriába tartoznak
- Segít megérteni a nem használók igényeit és preferenciáit

## Fő Komponensek

### 1. Mi kellene a használathoz? (Top Motivátorok)
**Kártya ID**: `motivators-card`

**Funkció**:
- Megmutatja a top motivációs tényezőket, amelyek ösztönöznék a nem használókat
- Több választás lehetséges, így egy válaszadó több motivátort is megjelölhet

**Adatforrás**:
- Tábla: `audit_responses`
- Mező: `responses.nu_motivation_what` (tömb)
- Szűrés: `employee_metadata.branch = 'not_used'`

**Megjelenítés**:
- Horizontális Progress Bar-ok kék színnel (#3366ff)
- Rendezés: Érték szerint csökkenő sorrendben
- Megjelenített adat: Említések száma

**Példa adatok**:
```javascript
[
  { name: "Jobb kommunikáció a szolgáltatásról", value: 45 },
  { name: "Többféle kommunikációs csatorna", value: 38 },
  { name: "Gyorsabb válaszidő", value: 32 }
]
```

### 2. Preferált szakértő típus
**Kártya ID**: `expert-preference-card`

**Funkció**:
- Megmutatja, milyen típusú szakértőt preferálnának a nem használók
- Egyetlen választás lehetséges válaszadónként

**Adatforrás**:
- Tábla: `audit_responses`
- Mező: `responses.nu_motivation_expert` (string)
- Szűrés: `employee_metadata.branch = 'not_used'`

**Megjelenítés**:
- Horizontális Progress Bar-ok kék színnel (#3366ff)
- Rendezés: Érték szerint csökkenő sorrendben
- Megjelenített adat: Válaszadók száma (fő)

**Példa adatok**:
```javascript
[
  { name: "Pszichológus", value: 52 },
  { name: "Coach", value: 28 },
  { name: "Tanácsadó", value: 15 }
]
```

### 3. Preferált kommunikációs csatorna
**Kártya ID**: `channel-preference-card`

**Funkció**:
- Megmutatja, milyen elérhetőségi módot preferálnának (telefon, online chat, stb.)
- Egyetlen választás lehetséges válaszadónként

**Adatforrás**:
- Tábla: `audit_responses`
- Mező: `responses.nu_motivation_channel` (string)
- Szűrés: `employee_metadata.branch = 'not_used'`

**Megjelenítés**:
- Horizontális Progress Bar-ok kék színnel (#3366ff)
- Rendezés: Érték szerint csökkenő sorrendben
- Megjelenített adat: Válaszadók száma (fő)

**Példa adatok**:
```javascript
[
  { name: "Telefonos konzultáció", value: 48 },
  { name: "Online chat", value: 33 },
  { name: "Video hívás", value: 14 }
]
```

## Adatfeldolgozás

### 1. Motivátorok feldolgozása
```typescript
const motivatorCounts: Record<string, number> = {};
data.forEach(r => {
  const values = r.responses?.nu_motivation_what;
  if (Array.isArray(values)) {
    values.forEach((val: string) => {
      motivatorCounts[val] = (motivatorCounts[val] || 0) + 1;
    });
  }
});
const motivatorsArray = Object.entries(motivatorCounts)
  .map(([name, value]) => ({ name, value }))
  .sort((a, b) => b.value - a.value);
```

### 2. Szakértő típus feldolgozása
```typescript
const expertCounts: Record<string, number> = {};
data.forEach(r => {
  const val = r.responses?.nu_motivation_expert;
  if (val) {
    expertCounts[val] = (expertCounts[val] || 0) + 1;
  }
});
const expertArray = Object.entries(expertCounts)
  .map(([name, value]) => ({ name, value }))
  .sort((a, b) => b.value - a.value);
```

### 3. Csatorna feldolgozása
```typescript
const channelCounts: Record<string, number> = {};
data.forEach(r => {
  const val = r.responses?.nu_motivation_channel;
  if (val) {
    channelCounts[val] = (channelCounts[val] || 0) + 1;
  }
});
const channelArray = Object.entries(channelCounts)
  .map(([name, value]) => ({ name, value }))
  .sort((a, b) => b.value - a.value);
```

## Vizualizáció Szabályok

### Progress Bar Számítás
```typescript
const maxValue = data[0].value; // Első (legnagyobb) érték
const percentage = maxValue > 0 ? (item.value / maxValue) * 100 : 0;
```

### Színséma
- **Progress Bar szín**: `#3366ff` (kék)
- Egyenletes vizuális megjelenés minden kártyán
- A legnagyobb érték mindig 100%-os szélességű

## Export Funkció

Minden kártya exportálható PNG formátumban:
- **Motivátorok**: `motivátorok.png`
- **Szakértő típus**: `szakértő-típus.png`
- **Kommunikációs csatorna**: `kommunikációs-csatorna.png`

Export kód:
```typescript
const exportCardToPNG = async (cardId: string, fileName: string) => {
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
};
```

## Speciális Helyzetek

### Nincs nem használó válaszadó
Ha `notUsedCount === 0`:
```
"Még nincs kiértékelt adat a nem használók körében"
```

### Üres adathalmazok
Ha egy adott kategóriában nincs adat:
```
"Nincs adat"
```

## Használati Útmutató

### HR szemszögből:
1. **Kommunikációs stratégia optimalizálása**: A top motivátorok megmutatják, mire van szükség a program népszerűsítéséhez
2. **Szakértő kiválasztás**: A preferált szakértő típusok alapján testre szabható a szolgáltatás
3. **Csatorna optimalizálás**: A preferált csatornák alapján hatékonyabb elérhetőség biztosítható

### Fejlesztési lehetőségek:
- A motivátorok alapján új kommunikációs kampányok tervezése
- A preferált szakértők típusának bővítése a szolgáltatási portfólióban
- Az elérhetőségi csatornák diverzifikálása

## Kapcsolódó adatbázis mezők

```sql
-- Motiváció adatok
SELECT 
  responses->>'nu_motivation_what' as motivators,
  responses->>'nu_motivation_expert' as expert_type,
  responses->>'nu_motivation_channel' as channel,
  employee_metadata->>'branch' as branch
FROM audit_responses
WHERE employee_metadata->>'branch' = 'not_used'
  AND audit_id = 'selected_audit_id';
```

## Verzió
- **Létrehozva**: 2025. október 3.
- **Komponens**: `src/pages/hr/Motivation.tsx`
- **Utolsó módosítás**: 2025. október 3.
