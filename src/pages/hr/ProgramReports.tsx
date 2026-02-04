import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { Download, Users, Phone, Laptop, AlertCircle, TrendingUp, Brain, Scale, Briefcase, Heart, GraduationCap, ChevronLeft, ChevronRight } from "lucide-react";
import { PieChart, Pie, Cell, ResponsiveContainer, BarChart, Bar, XAxis, YAxis, Tooltip, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis } from "recharts";
import { Progress } from "@/components/ui/progress";
import { GaugeChart } from "@/components/ui/gauge-chart";

// Mock data for program reports - Countries
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország', flag: '🇭🇺' },
  { id: 'ro', name: 'Románia', flag: '🇷🇴' },
  { id: 'sk', name: 'Szlovákia', flag: '🇸🇰' },
  { id: 'cz', name: 'Csehország', flag: '🇨🇿' },
];

interface QuarterData {
  quarter: number;
  year: number;
  hasData: boolean;
  isCurrent: boolean;
}

const generateQuarters = (startYear: number, startQuarter: number, count: number): QuarterData[] => {
  const quarters: QuarterData[] = [];
  let year = startYear;
  let quarter = startQuarter;
  
  for (let i = 0; i < count; i++) {
    quarters.push({
      quarter,
      year,
      hasData: true,
      isCurrent: i === count - 1,
    });
    
    quarter++;
    if (quarter > 4) {
      quarter = 1;
      year++;
    }
  }
  
  return quarters;
};

const MOCK_QUARTERS = generateQuarters(2023, 3, 6);

// Mock data for each country - comprehensive data matching Laravel reports
interface CountryReportData {
  // Case statistics
  closedCases: { current: number; cumulated: number };
  interruptedCases: { current: number; cumulated: number };
  unreachableCases: { current: number; cumulated: number };
  inProgressCases: number;
  
  // Consultations & activities
  totalConsultations: { current: number; cumulated: number };
  onsiteConsultations: { current: number; cumulated: number };
  workshopParticipants: { current: number; cumulated: number };
  crisisParticipants: { current: number; cumulated: number };
  onlineLogins: { current: number; cumulated: number };
  
  // Problem types distribution (percentages)
  problemTypes: {
    psychology: number;
    law: number;
    finance: number;
    health: number;
    coaching: number;
  };
  
  // Demographics
  genderDistribution: { male: number; female: number };
  ageDistribution: {
    '18-25': number;
    '26-35': number;
    '36-45': number;
    '46-55': number;
    '56+': number;
  };
  familyStatus: { employee: number; familyMember: number };
  
  // Consultation mode
  consultationMode: {
    inPerson: number;
    phone: number;
    online: number;
  };
  
  // Program usage
  usageRate: number;
  globalUsageComparison: number;
  bestMonth: string;
  
  // Satisfaction
  satisfactionScore: number;
  
  cumulatedText: string;
}

const MOCK_DATA_BY_COUNTRY: Record<string, CountryReportData> = {
  hu: {
    closedCases: { current: 45, cumulated: 320 },
    interruptedCases: { current: 8, cumulated: 42 },
    unreachableCases: { current: 5, cumulated: 28 },
    inProgressCases: 12,
    totalConsultations: { current: 156, cumulated: 892 },
    onsiteConsultations: { current: 24, cumulated: 145 },
    workshopParticipants: { current: 120, cumulated: 560 },
    crisisParticipants: { current: 12, cumulated: 48 },
    onlineLogins: { current: 234, cumulated: 1240 },
    problemTypes: { psychology: 42, law: 18, finance: 22, health: 10, coaching: 8 },
    genderDistribution: { male: 38, female: 62 },
    ageDistribution: { '18-25': 8, '26-35': 28, '36-45': 35, '46-55': 22, '56+': 7 },
    familyStatus: { employee: 78, familyMember: 22 },
    consultationMode: { inPerson: 25, phone: 45, online: 30 },
    usageRate: 4.8,
    globalUsageComparison: 3.2,
    bestMonth: 'Október',
    satisfactionScore: 4.6,
    cumulatedText: '2023. Q3 - 2024. Q4',
  },
  ro: {
    closedCases: { current: 32, cumulated: 210 },
    interruptedCases: { current: 5, cumulated: 28 },
    unreachableCases: { current: 3, cumulated: 18 },
    inProgressCases: 8,
    totalConsultations: { current: 98, cumulated: 542 },
    onsiteConsultations: { current: 18, cumulated: 98 },
    workshopParticipants: { current: 80, cumulated: 340 },
    crisisParticipants: { current: 6, cumulated: 24 },
    onlineLogins: { current: 156, cumulated: 820 },
    problemTypes: { psychology: 48, law: 15, finance: 20, health: 12, coaching: 5 },
    genderDistribution: { male: 35, female: 65 },
    ageDistribution: { '18-25': 12, '26-35': 32, '36-45': 30, '46-55': 18, '56+': 8 },
    familyStatus: { employee: 82, familyMember: 18 },
    consultationMode: { inPerson: 20, phone: 50, online: 30 },
    usageRate: 3.9,
    globalUsageComparison: 2.6,
    bestMonth: 'November',
    satisfactionScore: 4.4,
    cumulatedText: '2023. Q3 - 2024. Q4',
  },
  sk: {
    closedCases: { current: 28, cumulated: 180 },
    interruptedCases: { current: 4, cumulated: 22 },
    unreachableCases: { current: 2, cumulated: 14 },
    inProgressCases: 6,
    totalConsultations: { current: 76, cumulated: 420 },
    onsiteConsultations: { current: 12, cumulated: 72 },
    workshopParticipants: { current: 60, cumulated: 280 },
    crisisParticipants: { current: 4, cumulated: 18 },
    onlineLogins: { current: 112, cumulated: 580 },
    problemTypes: { psychology: 45, law: 20, finance: 18, health: 11, coaching: 6 },
    genderDistribution: { male: 40, female: 60 },
    ageDistribution: { '18-25': 10, '26-35': 30, '36-45': 32, '46-55': 20, '56+': 8 },
    familyStatus: { employee: 80, familyMember: 20 },
    consultationMode: { inPerson: 22, phone: 48, online: 30 },
    usageRate: 3.5,
    globalUsageComparison: 2.3,
    bestMonth: 'Szeptember',
    satisfactionScore: 4.5,
    cumulatedText: '2023. Q3 - 2024. Q4',
  },
  cz: {
    closedCases: { current: 38, cumulated: 245 },
    interruptedCases: { current: 6, cumulated: 32 },
    unreachableCases: { current: 4, cumulated: 20 },
    inProgressCases: 10,
    totalConsultations: { current: 112, cumulated: 620 },
    onsiteConsultations: { current: 20, cumulated: 115 },
    workshopParticipants: { current: 90, cumulated: 420 },
    crisisParticipants: { current: 8, cumulated: 32 },
    onlineLogins: { current: 178, cumulated: 920 },
    problemTypes: { psychology: 44, law: 17, finance: 21, health: 11, coaching: 7 },
    genderDistribution: { male: 37, female: 63 },
    ageDistribution: { '18-25': 9, '26-35': 29, '36-45': 33, '46-55': 21, '56+': 8 },
    familyStatus: { employee: 79, familyMember: 21 },
    consultationMode: { inPerson: 24, phone: 46, online: 30 },
    usageRate: 4.2,
    globalUsageComparison: 2.8,
    bestMonth: 'Október',
    satisfactionScore: 4.5,
    cumulatedText: '2023. Q3 - 2024. Q4',
  },
};

const QUARTER_LABELS: Record<number, string> = {
  1: 'Q1',
  2: 'Q2',
  3: 'Q3',
  4: 'Q4',
};

// Color palette for charts
const CHART_COLORS = {
  primary: '#04565f',      // sötétzöld
  secondary: '#82f5ae',    // világoszöld
  tertiary: '#004144',     // mélyzöld
  accent: '#ffc107',       // sárga
  highlight: '#6610f2',    // lila
};

const PROBLEM_TYPE_COLORS = [
  CHART_COLORS.primary,
  CHART_COLORS.secondary,
  CHART_COLORS.tertiary,
  CHART_COLORS.accent,
  CHART_COLORS.highlight,
];

const ProgramReports = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  const [selectedQuarterIndex, setSelectedQuarterIndex] = useState(MOCK_QUARTERS.length - 1);
  const [showCumulated, setShowCumulated] = useState(false);

  const currentData = MOCK_DATA_BY_COUNTRY[selectedCountry];
  const selectedQuarter = MOCK_QUARTERS[selectedQuarterIndex];

  // Prepare chart data
  const problemTypeData = [
    { name: 'Pszichológia', value: currentData.problemTypes.psychology, icon: Brain },
    { name: 'Jogi', value: currentData.problemTypes.law, icon: Scale },
    { name: 'Pénzügy', value: currentData.problemTypes.finance, icon: Briefcase },
    { name: 'Egészség', value: currentData.problemTypes.health, icon: Heart },
    { name: 'Coaching', value: currentData.problemTypes.coaching, icon: GraduationCap },
  ];

  const genderData = [
    { name: 'Férfi', value: currentData.genderDistribution.male },
    { name: 'Nő', value: currentData.genderDistribution.female },
  ];

  const ageData = Object.entries(currentData.ageDistribution).map(([name, value]) => ({
    name,
    value,
  }));

  const consultationModeData = [
    { name: 'Személyes', value: currentData.consultationMode.inPerson, icon: Users },
    { name: 'Telefon', value: currentData.consultationMode.phone, icon: Phone },
    { name: 'Online', value: currentData.consultationMode.online, icon: Laptop },
  ];

  const radarData = [
    { subject: 'Pszichológia', value: currentData.problemTypes.psychology, fullMark: 100 },
    { subject: 'Jogi', value: currentData.problemTypes.law, fullMark: 100 },
    { subject: 'Pénzügy', value: currentData.problemTypes.finance, fullMark: 100 },
    { subject: 'Egészség', value: currentData.problemTypes.health, fullMark: 100 },
    { subject: 'Coaching', value: currentData.problemTypes.coaching, fullMark: 100 },
  ];

  const handlePrevQuarter = () => {
    if (selectedQuarterIndex > 0) {
      setSelectedQuarterIndex(selectedQuarterIndex - 1);
    }
  };

  const handleNextQuarter = () => {
    if (selectedQuarterIndex < MOCK_QUARTERS.length - 1) {
      setSelectedQuarterIndex(selectedQuarterIndex + 1);
    }
  };

  const getValue = (data: { current: number; cumulated: number }) => 
    showCumulated ? data.cumulated : data.current;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold">Program Riportok</h2>
          <p className="text-muted-foreground">
            Negyedéves statisztikák és program teljesítmény
          </p>
        </div>
        <Button variant="outline" className="gap-2">
          <Download className="h-4 w-4" />
          Exportálás
        </Button>
      </div>

      {/* Country Tabs */}
      <Tabs value={selectedCountry} onValueChange={setSelectedCountry}>
        <TabsList className="bg-muted/50 p-1">
          {MOCK_COUNTRIES.map((country) => (
            <TabsTrigger 
              key={country.id} 
              value={country.id}
              className="gap-2 data-[state=active]:bg-[#04565f] data-[state=active]:text-white"
            >
              <span>{country.flag}</span>
              {country.name}
            </TabsTrigger>
          ))}
        </TabsList>
      </Tabs>

      {/* Quarter Navigation */}
      <Card className="border-muted">
        <CardContent className="py-4">
          <div className="flex items-center justify-between">
            <Button 
              variant="ghost" 
              size="icon"
              onClick={handlePrevQuarter}
              disabled={selectedQuarterIndex === 0}
            >
              <ChevronLeft className="h-5 w-5" />
            </Button>
            
            <div className="flex items-center gap-8">
              {MOCK_QUARTERS.map((q, index) => (
                <button
                  key={`${q.year}-${q.quarter}`}
                  onClick={() => q.hasData && setSelectedQuarterIndex(index)}
                  disabled={!q.hasData}
                  className={`flex flex-col items-center transition-all ${
                    !q.hasData ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'
                  }`}
                >
                  <div 
                    className={`h-3 w-3 rounded-full mb-1 transition-all ${
                      index === selectedQuarterIndex
                        ? 'bg-[#04565f] ring-4 ring-[#82f5ae]/50 scale-125'
                        : index < selectedQuarterIndex
                          ? 'bg-[#04565f]'
                          : 'bg-muted-foreground/30'
                    }`}
                  />
                  <span className={`text-xs ${
                    index === selectedQuarterIndex 
                      ? 'text-[#04565f] font-semibold' 
                      : 'text-muted-foreground'
                  }`}>
                    {QUARTER_LABELS[q.quarter]}'{String(q.year).slice(-2)}
                  </span>
                </button>
              ))}
            </div>
            
            <Button 
              variant="ghost" 
              size="icon"
              onClick={handleNextQuarter}
              disabled={selectedQuarterIndex === MOCK_QUARTERS.length - 1}
            >
              <ChevronRight className="h-5 w-5" />
            </Button>
          </div>
        </CardContent>
      </Card>

      {/* Toggle: Current vs Cumulated */}
      <div className="flex items-center justify-center gap-4">
        <span className={`text-sm ${!showCumulated ? 'font-semibold text-foreground' : 'text-muted-foreground'}`}>
          Aktuális negyedév
        </span>
        <button
          onClick={() => setShowCumulated(!showCumulated)}
          className={`relative w-14 h-7 rounded-full transition-colors ${
            showCumulated ? 'bg-[#04565f]' : 'bg-muted'
          }`}
        >
          <div className={`absolute top-1 w-5 h-5 rounded-full bg-white shadow transition-transform ${
            showCumulated ? 'translate-x-8' : 'translate-x-1'
          }`} />
        </button>
        <span className={`text-sm ${showCumulated ? 'font-semibold text-foreground' : 'text-muted-foreground'}`}>
          Kumulált ({currentData.cumulatedText})
        </span>
      </div>

      {/* Main Statistics Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Closed Cases */}
        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Lezárt esetek</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.primary }}>
                {getValue(currentData.closedCases)}
              </p>
              {!showCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.closedCases.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Interrupted Cases */}
        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Megszakított</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.accent }}>
                {getValue(currentData.interruptedCases)}
              </p>
              {!showCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.interruptedCases.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Consultations */}
        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Konzultációk</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.secondary }}>
                {getValue(currentData.totalConsultations)}
              </p>
              {!showCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.totalConsultations.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Online Logins */}
        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Online belépések</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.tertiary }}>
                {getValue(currentData.onlineLogins)}
              </p>
              {!showCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.onlineLogins.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Secondary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Workshop résztvevők</p>
                <p className="text-2xl font-bold">{getValue(currentData.workshopParticipants)}</p>
              </div>
              <Users className="h-8 w-8 text-muted-foreground/50" />
            </div>
          </CardContent>
        </Card>

        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Krízisintervenció</p>
                <p className="text-2xl font-bold">{getValue(currentData.crisisParticipants)}</p>
              </div>
              <AlertCircle className="h-8 w-8 text-muted-foreground/50" />
            </div>
          </CardContent>
        </Card>

        <Card className="border-muted">
          <CardContent className="pt-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Helyszíni konzultáció</p>
                <p className="text-2xl font-bold">{getValue(currentData.onsiteConsultations)}</p>
              </div>
              <Users className="h-8 w-8 text-muted-foreground/50" />
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Problem Types & Demographics */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Problem Types Radar */}
        <Card className="border-muted">
          <CardHeader>
            <CardTitle className="text-lg">Problématípusok megoszlása</CardTitle>
            <CardDescription>A konzultációk témái százalékos arányban</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[300px]">
              <ResponsiveContainer width="100%" height="100%">
                <RadarChart data={radarData}>
                  <PolarGrid stroke="hsl(var(--border))" />
                  <PolarAngleAxis 
                    dataKey="subject" 
                    tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 12 }}
                  />
                  <PolarRadiusAxis 
                    angle={90} 
                    domain={[0, 100]} 
                    tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 10 }}
                  />
                  <Radar
                    name="Arány"
                    dataKey="value"
                    stroke={CHART_COLORS.primary}
                    fill={CHART_COLORS.secondary}
                    fillOpacity={0.5}
                    strokeWidth={2}
                  />
                </RadarChart>
              </ResponsiveContainer>
            </div>
            
            {/* Legend with values */}
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-4">
              {problemTypeData.map((item, index) => (
                <div key={item.name} className="flex items-center gap-2 text-sm">
                  <div 
                    className="w-3 h-3 rounded-full" 
                    style={{ backgroundColor: PROBLEM_TYPE_COLORS[index] }}
                  />
                  <span className="text-muted-foreground">{item.name}:</span>
                  <span className="font-semibold">{item.value}%</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Gender & Age Distribution */}
        <Card className="border-muted">
          <CardHeader>
            <CardTitle className="text-lg">Demográfia</CardTitle>
            <CardDescription>Nemek és korosztályok eloszlása</CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {/* Gender */}
            <div>
              <p className="text-sm font-medium mb-3">Nemek aránya</p>
              <div className="flex items-center gap-4">
                <div className="flex-1">
                  <div className="flex justify-between text-sm mb-1">
                    <span>Férfi</span>
                    <span className="font-semibold">{genderData[0].value}%</span>
                  </div>
                  <Progress 
                    value={genderData[0].value} 
                    className="h-3"
                    style={{ 
                      '--progress-background': CHART_COLORS.primary 
                    } as React.CSSProperties}
                  />
                </div>
                <div className="flex-1">
                  <div className="flex justify-between text-sm mb-1">
                    <span>Nő</span>
                    <span className="font-semibold">{genderData[1].value}%</span>
                  </div>
                  <Progress 
                    value={genderData[1].value} 
                    className="h-3"
                    style={{ 
                      '--progress-background': CHART_COLORS.secondary 
                    } as React.CSSProperties}
                  />
                </div>
              </div>
            </div>

            {/* Age Distribution */}
            <div>
              <p className="text-sm font-medium mb-3">Korosztályok</p>
              <div className="h-[180px]">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={ageData} layout="vertical">
                    <XAxis type="number" domain={[0, 100]} hide />
                    <YAxis 
                      dataKey="name" 
                      type="category" 
                      width={50}
                      tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 12 }}
                    />
                    <Tooltip 
                      formatter={(value: number) => [`${value}%`, 'Arány']}
                      contentStyle={{ 
                        backgroundColor: 'hsl(var(--background))',
                        border: '1px solid hsl(var(--border))',
                        borderRadius: '8px'
                      }}
                    />
                    <Bar 
                      dataKey="value" 
                      fill={CHART_COLORS.primary}
                      radius={[0, 4, 4, 0]}
                    />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Consultation Mode & Usage */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Consultation Mode */}
        <Card className="border-muted">
          <CardHeader>
            <CardTitle className="text-lg">Konzultáció módja</CardTitle>
            <CardDescription>Személyes, telefon, online</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-[200px]">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={consultationModeData}
                    cx="50%"
                    cy="50%"
                    innerRadius={50}
                    outerRadius={80}
                    paddingAngle={2}
                    dataKey="value"
                  >
                    {consultationModeData.map((_, index) => (
                      <Cell 
                        key={`cell-${index}`} 
                        fill={[CHART_COLORS.primary, CHART_COLORS.secondary, CHART_COLORS.tertiary][index]}
                      />
                    ))}
                  </Pie>
                  <Tooltip 
                    formatter={(value: number) => [`${value}%`, 'Arány']}
                    contentStyle={{ 
                      backgroundColor: 'hsl(var(--background))',
                      border: '1px solid hsl(var(--border))',
                      borderRadius: '8px'
                    }}
                  />
                </PieChart>
              </ResponsiveContainer>
            </div>
            <div className="flex justify-center gap-4 mt-2">
              {consultationModeData.map((item, index) => (
                <div key={item.name} className="flex items-center gap-1.5 text-sm">
                  <div 
                    className="w-2.5 h-2.5 rounded-full" 
                    style={{ backgroundColor: [CHART_COLORS.primary, CHART_COLORS.secondary, CHART_COLORS.tertiary][index] }}
                  />
                  <span className="text-muted-foreground">{item.name}</span>
                  <span className="font-semibold">{item.value}%</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Usage Rate */}
        <Card className="border-muted">
          <CardHeader>
            <CardTitle className="text-lg">Program kihasználtság</CardTitle>
            <CardDescription>Éves igénybevételi arány</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="flex flex-col items-center">
              <GaugeChart
                value={currentData.usageRate}
                maxValue={10}
                size={160}
                label={`${currentData.usageRate}%`}
                sublabel="kihasználtság"
                gaugeColor={CHART_COLORS.accent}
                cornerRadius={20}
              />
              <div className="mt-4 text-center">
                <p className="text-sm text-muted-foreground">
                  Legjobb hónap: <span className="font-semibold text-foreground">{currentData.bestMonth}</span>
                </p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Satisfaction Score */}
        <Card className="border-muted">
          <CardHeader>
            <CardTitle className="text-lg">Elégedettség</CardTitle>
            <CardDescription>Ügyfélelégedettségi index (1-5)</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="flex flex-col items-center">
              <div className="relative">
                <div 
                  className="text-6xl font-bold"
                  style={{ color: currentData.satisfactionScore >= 4 ? CHART_COLORS.primary : CHART_COLORS.accent }}
                >
                  {currentData.satisfactionScore}
                </div>
                <div className="text-sm text-muted-foreground text-center mt-1">/ 5.0</div>
              </div>
              
              {/* Visual scale */}
              <div className="w-full mt-6 px-4">
                <div className="relative h-2 bg-muted rounded-full">
                  <div 
                    className="absolute top-0 left-0 h-full rounded-full transition-all"
                    style={{ 
                      width: `${(currentData.satisfactionScore / 5) * 100}%`,
                      backgroundColor: currentData.satisfactionScore >= 4 ? CHART_COLORS.primary : CHART_COLORS.accent
                    }}
                  />
                  <div 
                    className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                    style={{ 
                      left: `calc(${(currentData.satisfactionScore / 5) * 100}% - 8px)`,
                      backgroundColor: currentData.satisfactionScore >= 4 ? CHART_COLORS.primary : CHART_COLORS.accent
                    }}
                  />
                </div>
                <div className="flex justify-between mt-1 text-xs text-muted-foreground">
                  <span>1</span>
                  <span>2</span>
                  <span>3</span>
                  <span>4</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Family Status */}
      <Card className="border-muted">
        <CardHeader>
          <CardTitle className="text-lg">Munkavállaló / Családtag arány</CardTitle>
          <CardDescription>A szolgáltatást igénybe vevők megoszlása</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex items-center gap-8">
            <div className="flex-1">
              <div className="flex items-center justify-between mb-2">
                <span className="text-sm font-medium">Munkavállaló</span>
                <span className="text-lg font-bold" style={{ color: CHART_COLORS.primary }}>
                  {currentData.familyStatus.employee}%
                </span>
              </div>
              <div className="h-4 bg-muted rounded-full overflow-hidden">
                <div 
                  className="h-full rounded-full transition-all"
                  style={{ 
                    width: `${currentData.familyStatus.employee}%`,
                    backgroundColor: CHART_COLORS.primary
                  }}
                />
              </div>
            </div>
            <div className="flex-1">
              <div className="flex items-center justify-between mb-2">
                <span className="text-sm font-medium">Családtag</span>
                <span className="text-lg font-bold" style={{ color: CHART_COLORS.secondary }}>
                  {currentData.familyStatus.familyMember}%
                </span>
              </div>
              <div className="h-4 bg-muted rounded-full overflow-hidden">
                <div 
                  className="h-full rounded-full transition-all"
                  style={{ 
                    width: `${currentData.familyStatus.familyMember}%`,
                    backgroundColor: CHART_COLORS.secondary
                  }}
                />
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Global Comparison */}
      <Card className="border-muted bg-gradient-to-r from-[#04565f] to-[#004144]">
        <CardContent className="py-8">
          <div className="flex items-center justify-between text-white">
            <div className="flex items-center gap-4">
              <TrendingUp className="h-10 w-10 opacity-80" />
              <div>
                <p className="text-lg font-medium opacity-90">Regionális összehasonlítás</p>
                <p className="text-sm opacity-70">A program teljesítménye a régió átlagához képest</p>
              </div>
            </div>
            <div className="text-right">
              <p className="text-5xl font-bold">{currentData.globalUsageComparison}×</p>
              <p className="text-sm opacity-70">a régió átlagának</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default ProgramReports;
