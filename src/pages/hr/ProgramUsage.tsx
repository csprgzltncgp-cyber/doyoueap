import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { GaugeChart } from "@/components/ui/gauge-chart";
import { TrendingUp, Calendar, Brain, Users, Clock, Globe } from "lucide-react";
import { Progress } from "@/components/ui/progress";

// Mock countries
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország' },
  { id: 'ro', name: 'Románia' },
  { id: 'sk', name: 'Szlovákia' },
  { id: 'cz', name: 'Csehország' },
];

// Available years
const YEARS = [
  { value: '2024', label: '2024' },
  { value: '2023', label: '2023' },
  { value: '2022', label: '2022' },
];

// Mock program usage data per country and year
interface YearlyData {
  usagePercent: number;
  previousUsage: number;
  globalComparison: number;
  bestMonth: string;
  topProblemType: string;
  dominantGender: { label: string; percentage: number };
  dominantAgeGroup: { label: string; percentage: number };
}

const MOCK_USAGE_DATA: Record<string, Record<string, YearlyData>> = {
  hu: {
    '2024': {
      usagePercent: 4.8,
      previousUsage: 4.1,
      globalComparison: 3.2,
      bestMonth: 'Március',
      topProblemType: 'Szorongás, stressz',
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '36-45 év', percentage: 35 },
    },
    '2023': {
      usagePercent: 4.1,
      previousUsage: 3.2,
      globalComparison: 2.8,
      bestMonth: 'November',
      topProblemType: 'Szorongás, stressz',
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 34 },
    },
    '2022': {
      usagePercent: 3.2,
      previousUsage: 2.8,
      globalComparison: 2.1,
      bestMonth: 'Október',
      topProblemType: 'Depresszió',
      dominantGender: { label: 'Nő', percentage: 58 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
    },
  },
  ro: {
    '2024': {
      usagePercent: 3.9,
      previousUsage: 3.4,
      globalComparison: 2.6,
      bestMonth: 'November',
      topProblemType: 'Szorongás, stressz',
      dominantGender: { label: 'Nő', percentage: 65 },
      dominantAgeGroup: { label: '26-35 év', percentage: 32 },
    },
    '2023': {
      usagePercent: 3.4,
      previousUsage: 2.8,
      globalComparison: 2.3,
      bestMonth: 'Október',
      topProblemType: 'Depresszió',
      dominantGender: { label: 'Nő', percentage: 64 },
      dominantAgeGroup: { label: '26-35 év', percentage: 31 },
    },
    '2022': {
      usagePercent: 2.8,
      previousUsage: 2.4,
      globalComparison: 1.9,
      bestMonth: 'Szeptember',
      topProblemType: 'Depresszió',
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '26-35 év', percentage: 30 },
    },
  },
  sk: {
    '2024': {
      usagePercent: 3.5,
      previousUsage: 3.0,
      globalComparison: 2.3,
      bestMonth: 'Szeptember',
      topProblemType: 'Munkahelyi konfliktus',
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
    },
    '2023': {
      usagePercent: 3.0,
      previousUsage: 2.5,
      globalComparison: 2.0,
      bestMonth: 'Szeptember',
      topProblemType: 'Munkahelyi konfliktus',
      dominantGender: { label: 'Nő', percentage: 59 },
      dominantAgeGroup: { label: '36-45 év', percentage: 31 },
    },
    '2022': {
      usagePercent: 2.5,
      previousUsage: 2.2,
      globalComparison: 1.7,
      bestMonth: 'Augusztus',
      topProblemType: 'Munkahelyi konfliktus',
      dominantGender: { label: 'Nő', percentage: 58 },
      dominantAgeGroup: { label: '36-45 év', percentage: 30 },
    },
  },
  cz: {
    '2024': {
      usagePercent: 4.2,
      previousUsage: 3.6,
      globalComparison: 2.8,
      bestMonth: 'Október',
      topProblemType: 'Szorongás, stressz',
      dominantGender: { label: 'Nő', percentage: 63 },
      dominantAgeGroup: { label: '36-45 év', percentage: 33 },
    },
    '2023': {
      usagePercent: 3.6,
      previousUsage: 3.0,
      globalComparison: 2.4,
      bestMonth: 'November',
      topProblemType: 'Párkapcsolati probléma',
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
    },
    '2022': {
      usagePercent: 3.0,
      previousUsage: 2.6,
      globalComparison: 2.0,
      bestMonth: 'Október',
      topProblemType: 'Párkapcsolati probléma',
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 31 },
    },
  },
};

const getUsageRating = (value: number): string => {
  if (value >= 5) return 'Kiemelkedő';
  if (value >= 4) return 'Magas';
  if (value >= 3) return 'Átlag feletti';
  if (value >= 2) return 'Átlagos';
  return 'Fejlesztendő';
};

const ProgramUsage = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  const [selectedYear, setSelectedYear] = useState('2024');
  
  const currentData = MOCK_USAGE_DATA[selectedCountry][selectedYear];
  const trend = currentData.usagePercent - currentData.previousUsage;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 className="text-2xl font-bold">Program Használat</h2>
          <p className="text-muted-foreground">
            Éves igénybevételi statisztikák és demográfia
          </p>
        </div>
        
        {/* Year Selector */}
        <Select value={selectedYear} onValueChange={setSelectedYear}>
          <SelectTrigger className="w-[140px]">
            <Calendar className="h-4 w-4 mr-2 text-muted-foreground" />
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            {YEARS.map((year) => (
              <SelectItem key={year.value} value={year.value}>
                {year.label}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      {/* Country Tabs */}
      <Tabs value={selectedCountry} onValueChange={setSelectedCountry}>
        <TabsList className="bg-muted/50 p-1">
          {MOCK_COUNTRIES.map((country) => (
            <TabsTrigger 
              key={country.id} 
              value={country.id}
              className="data-[state=active]:bg-[#04565f] data-[state=active]:text-white"
            >
              {country.name}
            </TabsTrigger>
          ))}
        </TabsList>
      </Tabs>

      {/* Main Display - Gauge + Stats */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Usage Gauge */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Éves igénybevétel</CardTitle>
            <CardDescription>
              A program igénybevételi rátája az adott évben
            </CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col items-center pb-8">
            <GaugeChart 
              value={currentData.usagePercent * 10} 
              maxValue={100}
              size={400}
              label={`${currentData.usagePercent.toFixed(1).replace('.', ',')}%`}
            />
            <div className="-mt-16 text-center">
              <p className="text-2xl font-bold text-[#04565f]">
                {getUsageRating(currentData.usagePercent)}
              </p>
              <div className="flex items-center justify-center gap-2 mt-2">
                <TrendingUp className={`h-4 w-4 ${trend >= 0 ? 'text-[#82f5ae]' : 'text-destructive'}`} />
                <span className={`text-sm font-medium ${trend >= 0 ? 'text-[#04565f]' : 'text-destructive'}`}>
                  {trend >= 0 ? '+' : ''}{trend.toFixed(1).replace('.', ',')}% az előző évhez képest
                </span>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Stats Cards */}
        <div className="flex flex-col gap-4">
          {/* Global Comparison Card */}
          <Card className="flex-1">
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-[#82f5ae]/20 rounded-lg">
                  <Globe className="h-6 w-6 text-[#04565f]" />
                </div>
                <div className="flex-1">
                  <p className="text-sm text-muted-foreground">Regionális összehasonlítás</p>
                  <p className="text-xl font-bold text-foreground">Piaci átlag felett</p>
                </div>
                <div className="text-right">
                  <p className="text-3xl font-bold text-[#04565f]">
                    ×{currentData.globalComparison.toFixed(1).replace('.', ',')}
                  </p>
                  <p className="text-xs text-muted-foreground">szorzó</p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Best Month Card */}
          <Card className="flex-1">
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-[#ffc107]/20 rounded-lg">
                  <Calendar className="h-6 w-6 text-[#04565f]" />
                </div>
                <div className="flex-1">
                  <p className="text-sm text-muted-foreground">Legmagasabb igénybevétel</p>
                  <p className="text-xl font-bold text-foreground">{currentData.bestMonth}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-muted-foreground">Év</p>
                  <p className="text-lg font-semibold text-[#04565f]">{selectedYear}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Top Problem Type Card */}
          <Card className="flex-1">
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-[#04565f]/10 rounded-lg">
                  <Brain className="h-6 w-6 text-[#04565f]" />
                </div>
                <div className="flex-1">
                  <p className="text-sm text-muted-foreground">Legnépszerűbb tanácsadás</p>
                  <p className="text-xl font-bold text-foreground">{currentData.topProblemType}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Demographics Section */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {/* Gender Card */}
        <Card>
          <CardHeader className="pb-3">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#82f5ae]/20 rounded-lg">
                <Users className="h-5 w-5 text-[#04565f]" />
              </div>
              <div>
                <CardTitle className="text-base">Programot használók többsége</CardTitle>
                <CardDescription>Nem szerinti megoszlás</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="flex items-center justify-between mb-3">
              <span className="text-2xl font-bold text-[#04565f]">{currentData.dominantGender.label}</span>
              <span className="text-lg font-semibold text-muted-foreground">{currentData.dominantGender.percentage}%</span>
            </div>
            <Progress 
              value={currentData.dominantGender.percentage} 
              className="h-3"
              style={{ '--progress-background': '#04565f' } as React.CSSProperties}
            />
          </CardContent>
        </Card>

        {/* Age Group Card */}
        <Card>
          <CardHeader className="pb-3">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#ffc107]/20 rounded-lg">
                <Clock className="h-5 w-5 text-[#04565f]" />
              </div>
              <div>
                <CardTitle className="text-base">Domináns korcsoport</CardTitle>
                <CardDescription>Életkor szerinti megoszlás</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="flex items-center justify-between mb-3">
              <span className="text-2xl font-bold text-[#04565f]">{currentData.dominantAgeGroup.label}</span>
              <span className="text-lg font-semibold text-muted-foreground">{currentData.dominantAgeGroup.percentage}%</span>
            </div>
            <Progress 
              value={currentData.dominantAgeGroup.percentage} 
              className="h-3"
              style={{ '--progress-background': '#04565f' } as React.CSSProperties}
            />
          </CardContent>
        </Card>
      </div>

      {/* Usage Scale Reference */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Igénybevételi skála</CardTitle>
          <CardDescription>A programhasználati ráta értelmezése</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-3 justify-center">
            {[
              { range: '5%+', label: 'Kiemelkedő', color: '#04565f' },
              { range: '4-5%', label: 'Magas', color: '#82f5ae' },
              { range: '3-4%', label: 'Átlag feletti', color: '#ffc107' },
              { range: '2-3%', label: 'Átlagos', color: '#ff9800' },
              { range: '<2%', label: 'Fejlesztendő', color: '#f44336' },
            ].map((item) => (
              <div 
                key={item.range}
                className="flex items-center gap-2 px-3 py-2 bg-muted/50 rounded-lg"
              >
                <div 
                  className="w-3 h-3 rounded-full" 
                  style={{ backgroundColor: item.color }}
                />
                <span className="text-sm font-medium">{item.range}:</span>
                <span className="text-sm text-muted-foreground">{item.label}</span>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Info Card */}
      <Card className="bg-muted/30">
        <CardContent className="py-4">
          <p className="text-sm text-muted-foreground text-center">
            A programhasználati adatok az EAP program éves igénybevételét mutatják a jogosult munkavállalók arányában.
            A regionális összehasonlítás a CGP Europe ügyfélkörén kívüli, 10 közép-európai ország átlagához viszonyít.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default ProgramUsage;
