import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { GaugeChart } from "@/components/ui/gauge-chart";
import { TrendingUp, Calendar, Brain, Users, Clock, Globe, Award } from "lucide-react";
import { Progress } from "@/components/ui/progress";

// Mock countries
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország' },
  { id: 'ro', name: 'Románia' },
  { id: 'sk', name: 'Szlovákia' },
  { id: 'cz', name: 'Csehország' },
];

// Available years
const YEARS = [2022, 2023, 2024, 2025];

// Mock program usage data per country and year
interface YearlyData {
  usagePercent: number;
  globalComparison: number; // multiplier or percentage
  bestMonth: string;
  topProblemType: { label: string; icon: string };
  dominantGender: { label: string; percentage: number };
  dominantAgeGroup: { label: string; percentage: number };
  showBadge: boolean;
}

const MOCK_USAGE_DATA: Record<string, Record<number, YearlyData>> = {
  hu: {
    2022: {
      usagePercent: 3.2,
      globalComparison: 2.1,
      bestMonth: 'Október',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 58 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
      showBadge: false,
    },
    2023: {
      usagePercent: 4.1,
      globalComparison: 2.8,
      bestMonth: 'November',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 34 },
      showBadge: true,
    },
    2024: {
      usagePercent: 4.8,
      globalComparison: 3.2,
      bestMonth: 'Március',
      topProblemType: { label: 'Munkahelyi konfliktus', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '36-45 év', percentage: 35 },
      showBadge: true,
    },
    2025: {
      usagePercent: 5.2,
      globalComparison: 3.5,
      bestMonth: 'Január',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 64 },
      dominantAgeGroup: { label: '26-35 év', percentage: 33 },
      showBadge: true,
    },
  },
  ro: {
    2022: {
      usagePercent: 2.8,
      globalComparison: 1.9,
      bestMonth: 'Szeptember',
      topProblemType: { label: 'Depresszió', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '26-35 év', percentage: 30 },
      showBadge: false,
    },
    2023: {
      usagePercent: 3.4,
      globalComparison: 2.3,
      bestMonth: 'Október',
      topProblemType: { label: 'Depresszió', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 64 },
      dominantAgeGroup: { label: '26-35 év', percentage: 31 },
      showBadge: false,
    },
    2024: {
      usagePercent: 3.9,
      globalComparison: 2.6,
      bestMonth: 'November',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 65 },
      dominantAgeGroup: { label: '26-35 év', percentage: 32 },
      showBadge: true,
    },
    2025: {
      usagePercent: 4.3,
      globalComparison: 2.9,
      bestMonth: 'Február',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 66 },
      dominantAgeGroup: { label: '26-35 év', percentage: 34 },
      showBadge: true,
    },
  },
  sk: {
    2022: {
      usagePercent: 2.5,
      globalComparison: 1.7,
      bestMonth: 'Augusztus',
      topProblemType: { label: 'Munkahelyi konfliktus', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 58 },
      dominantAgeGroup: { label: '36-45 év', percentage: 30 },
      showBadge: false,
    },
    2023: {
      usagePercent: 3.0,
      globalComparison: 2.0,
      bestMonth: 'Szeptember',
      topProblemType: { label: 'Munkahelyi konfliktus', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 59 },
      dominantAgeGroup: { label: '36-45 év', percentage: 31 },
      showBadge: false,
    },
    2024: {
      usagePercent: 3.5,
      globalComparison: 2.3,
      bestMonth: 'Szeptember',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
      showBadge: false,
    },
    2025: {
      usagePercent: 3.8,
      globalComparison: 2.5,
      bestMonth: 'Március',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 61 },
      dominantAgeGroup: { label: '36-45 év', percentage: 33 },
      showBadge: false,
    },
  },
  cz: {
    2022: {
      usagePercent: 3.0,
      globalComparison: 2.0,
      bestMonth: 'Október',
      topProblemType: { label: 'Párkapcsolati probléma', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 31 },
      showBadge: false,
    },
    2023: {
      usagePercent: 3.6,
      globalComparison: 2.4,
      bestMonth: 'November',
      topProblemType: { label: 'Párkapcsolati probléma', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
      showBadge: false,
    },
    2024: {
      usagePercent: 4.2,
      globalComparison: 2.8,
      bestMonth: 'Október',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 63 },
      dominantAgeGroup: { label: '36-45 év', percentage: 33 },
      showBadge: true,
    },
    2025: {
      usagePercent: 4.6,
      globalComparison: 3.1,
      bestMonth: 'Január',
      topProblemType: { label: 'Szorongás, stressz', icon: 'brain' },
      dominantGender: { label: 'Nő', percentage: 64 },
      dominantAgeGroup: { label: '36-45 év', percentage: 34 },
      showBadge: true,
    },
  },
};

const ProgramUsage = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  const [selectedYear, setSelectedYear] = useState(2024);
  
  const currentData = MOCK_USAGE_DATA[selectedCountry][selectedYear];
  
  // Format global comparison display
  const formatGlobalComparison = (value: number) => {
    if (value >= 1) {
      return `× ${value.toFixed(1).replace('.', ',')}`;
    }
    return `${(value * 100).toFixed(0)}%`;
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h2 className="text-2xl font-bold">Program Használat</h2>
        <p className="text-muted-foreground">
          Éves statisztikák és demográfiai adatok
        </p>
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

      {/* Year Selection Timeline */}
      <Card className="bg-[#04565f]">
        <CardContent className="py-8">
          <div className="relative w-full max-w-2xl mx-auto">
            {/* Timeline line */}
            <div className="absolute top-4 left-0 right-0 h-1 bg-white/30 rounded-full" />
            
            {/* Year points */}
            <div className="relative flex justify-between">
              {YEARS.map((year) => {
                const isSelected = selectedYear === year;
                const isFuture = year > 2025;
                
                return (
                  <button
                    key={year}
                    onClick={() => !isFuture && setSelectedYear(year)}
                    disabled={isFuture}
                    className={`
                      flex flex-col items-center gap-2 transition-all
                      ${isFuture ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer group'}
                    `}
                  >
                    <div className={`
                      w-8 h-8 rounded-full flex items-center justify-center transition-all
                      ${isSelected 
                        ? 'bg-[#82f5ae] ring-4 ring-white/30' 
                        : 'bg-white group-hover:bg-[#82f5ae]'
                      }
                    `}>
                      <div className={`
                        w-4 h-4 rounded-full transition-all
                        ${isSelected ? 'bg-[#04565f]' : 'bg-white group-hover:bg-[#04565f]'}
                      `} />
                    </div>
                    <span className={`
                      text-sm font-medium transition-all
                      ${isSelected ? 'text-[#82f5ae]' : 'text-white/80 group-hover:text-white'}
                    `}>
                      {year}
                    </span>
                  </button>
                );
              })}
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Main Metrics Grid - 2x3 layout matching Laravel */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Usage Percentage - with GaugeChart */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-base text-center text-[#04565f] uppercase font-bold">
              A program éves igénybevétele
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col items-center">
            <GaugeChart 
              value={currentData.usagePercent * 10} 
              maxValue={100}
              size={200}
              label={`${currentData.usagePercent.toFixed(1).replace('.', ',')}%`}
            />
            {currentData.showBadge && (
              <div className="flex items-center gap-2 mt-2 px-3 py-1 bg-[#82f5ae]/20 rounded-full">
                <Award className="h-4 w-4 text-[#04565f]" />
                <span className="text-xs font-medium text-[#04565f]">Kiemelkedő eredmény</span>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Global Comparison - with GaugeChart */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-base text-center text-[#04565f] uppercase font-bold">
              Igénybevétel a régió piaci átlagához
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col items-center">
            <GaugeChart 
              value={Math.min(currentData.globalComparison * 20, 100)} 
              maxValue={100}
              size={200}
              label={formatGlobalComparison(currentData.globalComparison)}
              gaugeColor="#82f5ae"
            />
            <p className="text-xs text-muted-foreground text-center mt-2 max-w-[200px]">
              A viszonyítási alap a régió 10 országának átlagos EAP igénybevétele
            </p>
          </CardContent>
        </Card>

        {/* Best Month */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-base text-center text-[#04565f] uppercase font-bold">
              A legmagasabb igénybevétel hónapja
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col items-center justify-center min-h-[200px]">
            <div className="p-4 bg-[#ffc107]/20 rounded-full mb-4">
              <Calendar className="h-12 w-12 text-[#04565f]" />
            </div>
            <div className="bg-[#04565f] text-white px-8 py-4 rounded-xl">
              <p className="text-2xl font-bold text-center">{currentData.bestMonth}</p>
            </div>
          </CardContent>
        </Card>

        {/* Top Problem Type */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-base text-center text-[#04565f] uppercase font-bold">
              A legnépszerűbb tanácsadás
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col items-center justify-center min-h-[200px]">
            <div className="p-4 bg-[#04565f]/10 rounded-full mb-4">
              <Brain className="h-12 w-12 text-[#04565f]" />
            </div>
            <div className="bg-[#04565f] text-white px-8 py-4 rounded-xl">
              <p className="text-lg font-bold text-center">{currentData.topProblemType.label}</p>
            </div>
          </CardContent>
        </Card>

        {/* Dominant Gender */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-base text-center text-[#04565f] uppercase font-bold">
              A programot használók többsége
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col items-center justify-center min-h-[200px]">
            <div className="p-4 bg-[#82f5ae]/20 rounded-full mb-4">
              <Users className="h-12 w-12 text-[#04565f]" />
            </div>
            <div className="bg-[#04565f] text-white px-8 py-4 rounded-xl">
              <p className="text-2xl font-bold text-center">{currentData.dominantGender.label}</p>
            </div>
            <div className="mt-3 w-full max-w-[200px]">
              <div className="flex justify-between text-xs text-muted-foreground mb-1">
                <span>{currentData.dominantGender.label}</span>
                <span>{currentData.dominantGender.percentage}%</span>
              </div>
              <Progress 
                value={currentData.dominantGender.percentage} 
                className="h-2 [&>div]:bg-[#04565f]"
              />
            </div>
          </CardContent>
        </Card>

        {/* Dominant Age Group */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-base text-center text-[#04565f] uppercase font-bold">
              A programot használók életkora
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col items-center justify-center min-h-[200px]">
            <div className="p-4 bg-[#ffc107]/20 rounded-full mb-4">
              <Clock className="h-12 w-12 text-[#04565f]" />
            </div>
            <div className="bg-[#04565f] text-white px-8 py-4 rounded-xl">
              <p className="text-2xl font-bold text-center">{currentData.dominantAgeGroup.label}</p>
            </div>
            <div className="mt-3 w-full max-w-[200px]">
              <div className="flex justify-between text-xs text-muted-foreground mb-1">
                <span>{currentData.dominantAgeGroup.label}</span>
                <span>{currentData.dominantAgeGroup.percentage}%</span>
              </div>
              <Progress 
                value={currentData.dominantAgeGroup.percentage} 
                className="h-2 [&>div]:bg-[#04565f]"
              />
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Year Summary Card */}
      <Card className="bg-muted/30">
        <CardContent className="py-4">
          <div className="flex items-center justify-center gap-4 flex-wrap">
            <div className="flex items-center gap-2">
              <TrendingUp className="h-4 w-4 text-[#04565f]" />
              <span className="text-sm text-muted-foreground">
                <strong className="text-foreground">{selectedYear}.</strong> évi adatok
              </span>
            </div>
            <div className="flex items-center gap-2">
              <Globe className="h-4 w-4 text-[#04565f]" />
              <span className="text-sm text-muted-foreground">
                {MOCK_COUNTRIES.find(c => c.id === selectedCountry)?.name}
              </span>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Info Card */}
      <Card className="bg-muted/30">
        <CardContent className="py-4">
          <p className="text-sm text-muted-foreground text-center">
            A programhasználati adatok az EAP program éves igénybevételét és a felhasználók demográfiai jellemzőit mutatják. 
            A regionális összehasonlítás a CGP Europe saját ügyfélkörén kívüli, 10 országos átlaghoz viszonyít.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default ProgramUsage;
