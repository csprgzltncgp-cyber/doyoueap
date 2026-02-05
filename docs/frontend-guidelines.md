# Frontend Szabályozás - doyoueap

Ez a dokumentum összefoglalja a projekt frontend fejlesztési szabályait, színpalettáját és komponens használati irányelveit.

## Színpaletta

### Elsődleges színek

| Szín neve | HEX kód | RGB | Használat |
|-----------|---------|-----|-----------|
| **Mélyzöld** | `#004144` | `rgb(0, 65, 68)` | Kördiagram 1. szín, kiegészítő hangsúly |
| **Sötétzöld** | `#04565f` | `rgb(4, 86, 95)` | Fő akciószín, gombok, ikonok, progress bar-ok |
| **Világoszöld** | `#82f5ae` | `rgb(130, 245, 174)` | Aktív badge-ek, másodlagos kiemelések |
| **Narancs** | `#ea892b` | `rgb(234, 137, 43)` | Átlagos/közepes szintű mutatók |
| **Sárga** | `#ffc107` | `rgb(255, 193, 7)` | Progress bar alapszín, Gauge chart |
| **Lila** | `#6610f2` | `rgb(102, 16, 242)` | Élő esetek, felhasznált workshopok, NPS |
| **Piros** | `#ff0033` | `rgb(255, 0, 51)` | Kritikus állapot, hibák, figyelmeztetések |

### Státusz színek

| Állapot | Szín | Használat |
|---------|------|-----------|
| **Aktív** | `#82f5ae` (világoszöld) | Aktív felmérések, hátralévő napok badge |
| **Lezárt** | `#04565f` (sötétzöld) | Lezárt felmérések badge, fehér szöveggel |
| **Élő** | `#6610f2` (lila) | Élő esetek, ping animációval |
| **Felhasznált** | `#6610f2` (lila) | Workshopok státusz |
| **Szervezés alatt** | `#ffc107` (sárga) | Workshopok státusz |
| **Felhasználható** | `#82f5ae` (világoszöld) | Workshopok státusz |

### Tiltott színek

Az alábbi színek **NEM HASZNÁLHATÓK** a projektben:
- `#3572ef` - Kék (korábbi accent)
- `#10b981` - Emerald zöld
- `#f59e0b` - Amber
- `#ef4444` - Tailwind red (használd helyette: `#ff0033`)

---

## Kördiagram színezési szabály

Minden kördiagram (PieChart) esetén az alábbi színezési sorrend kötelező:

```typescript
// Pie chart color rule
const pieColors = ['#004144', '#04565f', '#82f5ae'];
const getFadedDarkGreen = (i: number) => `rgba(4, 86, 95, ${0.7 - (i - 3) * 0.15})`;
const color = index < 3 ? pieColors[index] : getFadedDarkGreen(index);
```

| Sorrend | Szín | Leírás |
|---------|------|--------|
| 1. | `#004144` | Mélyzöld |
| 2. | `#04565f` | Sötétzöld |
| 3. | `#82f5ae` | Világoszöld |
| 4+ | `rgba(4, 86, 95, 0.7)` | Halványított sötétzöld (csökkenő opacitással) |

---

## Komponens irányelvek

### Progress Bar

- **Alapértelmezett szín**: `#ffc107` (sárga)
- **Riportokban**: `#04565f` (sötétzöld) - `--progress-background` CSS változóval
- **Audit folyamatban**: `#82f5ae` (világoszöld)

```tsx
<Progress 
  value={percentage} 
  className="h-3"
  style={{ '--progress-background': '#04565f' } as React.CSSProperties}
/>
```

### Gauge Chart

- **Alapértelmezett**: `#ffc107` (sárga)
- **Kritikus érték esetén**: `#ff0033` (piros)

### Badge-ek

```tsx
// Aktív állapot
<Badge className="bg-[#82f5ae] text-foreground">Aktív</Badge>

// Lezárt állapot
<Badge className="bg-[#04565f] text-white">Lezárt</Badge>

// Egyedi badge (lila)
<Badge className="bg-[#ff66ff20] text-purple-700 border-purple-300">Egyedi</Badge>
```

---

## Tipográfia

### Betűtípus

- **Család**: Calibri (TTF-ből betöltve)
- **Fallback**: Arial, sans-serif
- **Tailwind osztály**: `font-sans`

### Font fájlok

A font fájlok helye: `public/fonts/`
- `Calibri.ttf`
- `Calibri-Bold.ttf`
- `Calibri-Italic.ttf`
- `Calibri-BoldItalic.ttf`

---

## Vizualizációk színei

### Trends oldal

| Mutató | Szín |
|--------|------|
| Ismertség | `#04565f` (sötétzöld) |
| Bizalom/Használat | `#82f5ae` (világoszöld) |
| Elégedettség | `#004144` (mélyzöld) |
| Trend ikonok | `#04565f` (sötétzöld) |

### Kategória diagramok

| Kategória | Szín |
|-----------|------|
| Nem tudott róla | `#04565f` (sötétzöld) |
| Nem használta | `#82f5ae` (világoszöld) |
| Használta | `#004144` (mélyzöld) |

### Összehasonlítás

| Elem | Szín |
|------|------|
| 1. felmérés | `#04565f` (sötétzöld) |
| 2. felmérés | `#82f5ae` (világoszöld) |

---

## Felület színek

### Háttér és szöveg

```css
/* Használd a Tailwind semantic tokeneket */
bg-background      /* Fő háttér */
bg-muted/50        /* Másodlagos háttér */
text-foreground    /* Fő szöveg */
text-muted-foreground  /* Másodlagos szöveg */
```

### Szegélyek

```css
border-border      /* Alapértelmezett szegély */
border-gray-200    /* Halvány szegély (rácsok, táblázatok) */
```

---

## Egészség Térkép UI

- **Cella háttér**: Váltakozó `bg-muted/20` és `bg-muted/10`
- **Tengely vonalak**: `border-gray-200`
- **Kategória feliratok**: `text-muted-foreground/15` (alacsony opacitás)

---

## Workshopok és Krízisintervenciók

### Státusz színek

| Státusz | Szín |
|---------|------|
| Felhasznált | `#6610f2` (lila) |
| Szervezés alatt | `#ffc107` (sárga) |
| Felhasználható | `#82f5ae` (világoszöld) |

### Kártya elrendezés

- `items-start` igazítás a gridben
- Exkluzív lenyitás (egyszerre csak egy lehet nyitva)
- Workshopok: flip-animáció
- Krízisintervenciók: Collapsible komponens

---

## Logók és branding

| Logó | Fájl | Használat |
|------|------|-----------|
| Fő logó | `src/assets/logo_black_v2.png` | Auth, Dashboard, fejléc |
| Magazin logó | `src/assets/thejournalist_logo.png` | Magazin oldalak |
| EAP Pulse logó | `src/assets/eap-pulse-logo-cgp.png` | Audit oldalak |
| 4Score logó | `src/assets/4score_logo_cgp.png` | Riport oldalak |
| Favicon | `public/favicon.png` | Böngésző tab |

---

## AI Riportok

Az AI által generált diagramok színezése automatikusan történik a frontend által:
- **Kördiagramok**: A fenti kördiagram szabály szerint
- **Oszlopdiagramok**: `#04565f` (sötétzöld)
- **Vonaldiagramok**: `#04565f` (sötétzöld)
- **Progress bar**: Sárga (alapértelmezett)
- **Gauge**: Sárga (alapértelmezett)

Az AI prompt-ban NE adjunk meg színeket - a frontend kezeli automatikusan.

---

## Fontos szabályok

1. **Soha ne használj hardcoded színeket közvetlenül** - használd a dokumentált HEX kódokat
2. **Kördiagramoknál mindig a megadott sorrendet kövesd**
3. **Progress bar-oknál a kontextustól függően válaszd a megfelelő színt**
4. **Badge-eknél a státusz színeket kövesd**
5. **Tiltott színeket soha ne használj**

---

*Utolsó frissítés: 2026-02-05*
