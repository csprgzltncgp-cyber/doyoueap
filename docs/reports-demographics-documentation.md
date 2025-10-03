# Demográfiai Elemzés - Dokumentáció

## Áttekintés
A Demográfiai Elemzés tab átfogó betekintést nyújt a válaszadók demográfiai összetételébe, lehetővé téve a szegmentált elemzést kor és nem szerint.

## Fő jellemzők

### 1. Demográfiai Szűrők
- **Nem szerinti szűrés**: Összes / Férfi / Nő
- **Korcsoport szűrés**: Összes / 18-24 / 25-36 / 37-44 / 45-58 / 58+
- Valós idejű frissítés: A szűrők változtatásakor az összes mutató automatikusan frissül

### 2. Összefoglaló Mutatók
Négy összefoglaló kártya mutatja a kiválasztott demográfiai csoport alapstatisztikáit:
- **Összesen**: A szűrt csoport teljes létszáma
- **Nem tudott róla**: Akik nem ismerték az EAP programot
- **Nem használta**: Akik ismerték, de nem használták
- **Használta**: Akik igénybe vették a szolgáltatást

### 3. Kategória Megoszlás
**Kör diagram** a három felhasználói kategória arányával:
- Nem tudott róla (piros)
- Nem használta (narancssárga)
- Használta (zöld)

**Magyarázat**: A válaszadók három csoportba sorolása az EAP program ismerete és használata alapján

**Funkciók**:
- Vizuális megjelenítés kördiagramon
- Színes pöttyös legenda a diagram mellett
- PNG export lehetőség

### 4. Összehasonlítás
**Oszlopdiagram** a teljes minta és a szűrt csoport összehasonlításához három kulcsmutatóban:
- Szolgáltatás megértése (1-5 skála)
- Anonimitásba vetett bizalom (1-5 skála)
- Elégedettség (1-5 skála)

**Magyarázat**: A kiválasztott demográfiai csoport és a teljes minta összehasonlítása három kulcsmutatóban

**Funkciók**:
- Kétféle oszlop: teljes minta (kék) és szűrt csoport (zöld)
- Dinamikus megjelenítés: ha nincs szűrés, csak a teljes minta jelenik meg
- PNG export lehetőség

### 5. Nem szerinti megoszlás
**Progress bar vizualizáció** a nemek megoszlásának bemutatására:
- Horizontális progress bar minden nem kategóriához
- Létszám és százalék megjelenítése
- #3366ff kék szín

**Funkciók**:
- Válaszadók számának és százalékos arányának megjelenítése
- Vizuális progress bar reprezentáció
- PNG export lehetőség

### 6. Korcsoport megoszlás
**Progress bar vizualizáció** a korcsoportok megoszlásának bemutatására:
- Horizontális progress bar minden korcsoporthoz
- Rendezett megjelenítés (18-24, 25-36, 37-44, 45-58, 58+)
- #3366ff kék szín

**Funkciók**:
- Válaszadók számának és százalékos arányának megjelenítése
- Vizuális progress bar reprezentáció
- PNG export lehetőség

## Adatforrások

### Használt táblák
- `audit_responses`: Válaszok és demográfiai adatok
  - `responses.gender`: Nem
  - `responses.age`: Korcsoport
  - `employee_metadata.branch`: Felhasználói kategória (redirect/not_used/used)

### Számított metrikák

#### Awareness mutatók
- **u_awareness_understanding**: Szolgáltatás megértése (használók)
- **nu_awareness_understanding**: Szolgáltatás megértése (nem használók)

#### Trust mutatók
- **u_trust_anonymity**: Anonimitásba vetett bizalom (használók)
- **nu_trust_anonymity**: Anonimitásba vetett bizalom (nem használók)

#### Impact mutatók
- **u_impact_satisfaction**: Elégedettség (csak használók)

## Használati esetek

### 1. Nemi különbségek elemzése
Válassz ki egy nemet a szűrőből, és figyeld meg:
- Eltérő-e a programismeret és használat a nemek között?
- Van-e szignifikáns különbség az elégedettségben?

### 2. Generációs elemzés
Válassz ki egy korcsoportot:
- Mely korcsoportok ismerik leginkább a programot?
- Mely korcsoportok használják legaktívabban?

### 3. Célzott kommunikáció tervezése
Azonosítsd be:
- Mely demográfiai csoportok vannak alulreprezentálva a használatban
- Mely csoportokhoz kell jobban eljuttatni az információt

### 4. Benchmark elemzés
Hasonlítsd össze a demográfiai csoportokat:
- Mely csoport mutat magasabb elégedettséget?
- Mely csoport bízik jobban az anonimitásban?

## Export funkciók
Minden kártya jobb felső sarkában található egy letöltés ikon, amellyel PNG formátumban exportálható a vizualizáció prezentációkhoz és jelentésekhez.

## Üres állapot kezelése
Ha egy demográfiai csoportban nincs válaszadó, akkor "Nincs adat a kiválasztott demográfiai csoportban" üzenet jelenik meg.

## Technikai részletek

### Komponens
- **Fájl**: `src/pages/hr/Demographics.tsx`
- **Props**: `selectedAuditId` - A kiválasztott felmérés azonosítója

### Függőségek
- recharts: Diagramok (PieChart, BarChart)
- html2canvas: PNG export
- Progress komponens: Vizuális bar megjelenítés

### Szín paletta
- Progress bar: #3366ff (kék)
- Kategória kördiagram: chart-4 (piros), chart-3 (narancssárga), chart-2 (zöld)
- Összehasonlító diagram: chart-2 (kék - teljes), chart-3 (zöld - szűrt)
