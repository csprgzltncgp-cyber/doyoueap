import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Checkbox } from "@/components/ui/checkbox";
import { Skeleton } from "@/components/ui/skeleton";

import { Users, Phone, Laptop, AlertCircle, TrendingUp, Brain, Scale, Briefcase, Heart, GraduationCap, Building2 } from "lucide-react";
import { PieChart, Pie, Cell, ResponsiveContainer, BarChart, Bar, XAxis, YAxis, Tooltip, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis } from "recharts";
import { Progress } from "@/components/ui/progress";
import { GaugeChart } from "@/components/ui/gauge-chart";
import { useProgramReportsData, translateDistribution, VALUE_TRANSLATIONS, translateValue } from "@/hooks/useProgramReportsData";

// Quarters configuration
const QUARTERS = [
  { id: 1, label: 'Q1', hasData: true },
  { id: 2, label: 'Q2', hasData: true },
  { id: 3, label: 'Q3', hasData: true },
  { id: 4, label: 'Q4', hasData: true },
];

// Mock data for each country - comprehensive data matching Laravel reports
interface CountryReportData {
  // Case statistics
  closedCases: { current: number; cumulated: number };
  interruptedCases: { current: number; cumulated: number };
  unreachableCases: { current: number; cumulated: number };
  inProgressCases: number;
  
  // Live cases by type
  liveCases: {
    psychology: { current: number; cumulated: number };
    law: { current: number; cumulated: number };
    finance: { current: number; cumulated: number };
    healthCoaching: { current: number; cumulated: number };
  };
  
  // Record highlights
  recordHighlights: {
    mostFrequentProblem: string;
    dominantGender: { label: string; percentage: number };
    dominantAgeGroup: { label: string; percentage: number };
  };
  
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
    liveCases: {
      psychology: { current: 28, cumulated: 195 },
      law: { current: 8, cumulated: 58 },
      finance: { current: 6, cumulated: 42 },
      healthCoaching: { current: 3, cumulated: 25 },
    },
    recordHighlights: {
      mostFrequentProblem: 'Szorongás, stressz',
      dominantGender: { label: 'Nő', percentage: 62 },
      dominantAgeGroup: { label: '36-45 év', percentage: 35 },
    },
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
    liveCases: {
      psychology: { current: 18, cumulated: 126 },
      law: { current: 6, cumulated: 42 },
      finance: { current: 5, cumulated: 28 },
      healthCoaching: { current: 3, cumulated: 14 },
    },
    recordHighlights: {
      mostFrequentProblem: 'Depresszió',
      dominantGender: { label: 'Nő', percentage: 65 },
      dominantAgeGroup: { label: '26-35 év', percentage: 32 },
    },
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
    liveCases: {
      psychology: { current: 14, cumulated: 108 },
      law: { current: 6, cumulated: 36 },
      finance: { current: 5, cumulated: 24 },
      healthCoaching: { current: 3, cumulated: 12 },
    },
    recordHighlights: {
      mostFrequentProblem: 'Munkahelyi konfliktus',
      dominantGender: { label: 'Nő', percentage: 60 },
      dominantAgeGroup: { label: '36-45 év', percentage: 32 },
    },
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
    liveCases: {
      psychology: { current: 22, cumulated: 147 },
      law: { current: 7, cumulated: 49 },
      finance: { current: 6, cumulated: 33 },
      healthCoaching: { current: 3, cumulated: 16 },
    },
    recordHighlights: {
      mostFrequentProblem: 'Párkapcsolati probléma',
      dominantGender: { label: 'Nő', percentage: 63 },
      dominantAgeGroup: { label: '36-45 év', percentage: 33 },
    },
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
  // Quarter selection
  const [selectedQuarter, setSelectedQuarter] = useState(4);
  const [selectedYear] = useState(2024);
  const [selectedCountryId, setSelectedCountryId] = useState<number | null>(null);
  
  // Cumulation mode (right side) - which quarters to add together
  const [cumulatedQuarters, setCumulatedQuarters] = useState<number[]>([]);
  
  // Active mode: 'single' or 'cumulated'
  const [activeMode, setActiveMode] = useState<'single' | 'cumulated'>('single');
  
  // Fetch data from Laravel API
  const { data: apiData, loading, error, refetch } = useProgramReportsData({
    quarter: selectedQuarter,
    year: selectedYear,
    countryId: selectedCountryId,
  });
  
  // Derived data from API
  const countries = apiData?.countries || [];
  const companyName = apiData?.company?.name?.trim() || '';
  
  // Set first country as default when data loads
  useEffect(() => {
    if (countries.length > 0 && selectedCountryId === null) {
      setSelectedCountryId(countries[0].id);
    }
  }, [countries, selectedCountryId]);
  
  // Refetch when country changes
  useEffect(() => {
    if (selectedCountryId !== null) {
      refetch();
    }
  }, [selectedCountryId, refetch]);
  
  // Calculate customer satisfaction average for selected country
  const customerSatisfactionValues = apiData?.customer_satisfaction_values || [];
  const countryCSValues = customerSatisfactionValues.filter(
    (v) => String(v.country_id) === String(selectedCountryId)
  );
  const avgSatisfaction = countryCSValues.length > 0
    ? countryCSValues.reduce((sum: number, v) => sum + Number(v.value), 0) / countryCSValues.length
    : 0;
  
  // Get processed statistics from API (or fallback to mock data)
  const processedStats = apiData?.processed_statistics;
  const statsPercentages = apiData?.statistics_percentages;
  const highlights = apiData?.highlights;
  
  // Get mock data for selected country based on API country code (fallback)
  const selectedCountry = countries.find(c => c.id === selectedCountryId);
  const countryCode = selectedCountry?.code?.toLowerCase() || 'hu';
  const mockData = MOCK_DATA_BY_COUNTRY[countryCode] || MOCK_DATA_BY_COUNTRY['hu'];
  
  // Use real data if available, otherwise fallback to mock
  const hasRealData = processedStats !== null && processedStats !== undefined;
  
  // Build currentData object from real or mock data
  const currentData = hasRealData ? {
    closedCases: { current: processedStats.caseNumbers.closed, cumulated: processedStats.caseNumbers.closed },
    interruptedCases: { current: processedStats.caseNumbers.interrupted, cumulated: processedStats.caseNumbers.interrupted },
    unreachableCases: { current: processedStats.caseNumbers.clientUnreachable, cumulated: processedStats.caseNumbers.clientUnreachable },
    inProgressCases: processedStats.caseNumbers.inProgress,
    liveCases: {
      psychology: { current: processedStats.problemTypes['1'] || 0, cumulated: processedStats.problemTypes['1'] || 0 },
      law: { current: processedStats.problemTypes['2'] || 0, cumulated: processedStats.problemTypes['2'] || 0 },
      finance: { current: processedStats.problemTypes['3'] || 0, cumulated: processedStats.problemTypes['3'] || 0 },
      healthCoaching: { current: processedStats.problemTypes['4'] || 0, cumulated: processedStats.problemTypes['4'] || 0 },
    },
    recordHighlights: {
      mostFrequentProblem: highlights?.mostFrequentProblem ? translateValue('problemTypes', highlights.mostFrequentProblem.key) : mockData.recordHighlights.mostFrequentProblem,
      dominantGender: highlights?.dominantGender ? { 
        label: translateValue('gender', highlights.dominantGender.key), 
        percentage: highlights.dominantGender.percentage 
      } : mockData.recordHighlights.dominantGender,
      dominantAgeGroup: highlights?.dominantAgeGroup ? { 
        label: translateValue('age', highlights.dominantAgeGroup.key), 
        percentage: highlights.dominantAgeGroup.percentage 
      } : mockData.recordHighlights.dominantAgeGroup,
    },
    totalConsultations: { current: processedStats.consultations.total, cumulated: processedStats.consultations.total },
    onsiteConsultations: { current: processedStats.consultations.onsite, cumulated: processedStats.consultations.onsite },
    workshopParticipants: { current: processedStats.activities.workshop, cumulated: processedStats.activities.workshop },
    crisisParticipants: { current: processedStats.activities.crisis, cumulated: processedStats.activities.crisis },
    onlineLogins: { current: 0, cumulated: 0 }, // Not available in current API
    problemTypes: statsPercentages?.problemTypes ? {
      psychology: statsPercentages.problemTypes['1'] || 0,
      law: statsPercentages.problemTypes['2'] || 0,
      finance: statsPercentages.problemTypes['3'] || 0,
      health: statsPercentages.problemTypes['4'] || 0,
      coaching: statsPercentages.problemTypes['5'] || 0,
    } : mockData.problemTypes,
    genderDistribution: statsPercentages?.gender ? {
      male: statsPercentages.gender['9'] || 0,
      female: statsPercentages.gender['10'] || 0,
    } : mockData.genderDistribution,
    ageDistribution: statsPercentages?.age ? {
      '18-25': statsPercentages.age['12'] || 0,
      '26-35': statsPercentages.age['13'] || 0,
      '36-45': statsPercentages.age['14'] || 0,
      '46-55': statsPercentages.age['15'] || 0,
      '56+': statsPercentages.age['16'] || 0,
    } : mockData.ageDistribution,
    familyStatus: statsPercentages?.employeeOrFamily ? {
      employee: statsPercentages.employeeOrFamily['7'] || 0,
      familyMember: statsPercentages.employeeOrFamily['8'] || 0,
    } : mockData.familyStatus,
    consultationMode: statsPercentages?.placeOfReceipt ? {
      inPerson: statsPercentages.placeOfReceipt['5'] || 0,
      phone: statsPercentages.placeOfReceipt['3'] || 0,
      online: statsPercentages.placeOfReceipt['4'] || 0,
    } : mockData.consultationMode,
    usageRate: mockData.usageRate,
    globalUsageComparison: mockData.globalUsageComparison,
    bestMonth: mockData.bestMonth,
    satisfactionScore: avgSatisfaction || mockData.satisfactionScore,
    cumulatedText: mockData.cumulatedText,
  } : mockData;
  
  // Check if cumulation mode is active with at least one quarter selected
  const isCumulated = activeMode === 'cumulated' && cumulatedQuarters.length > 0;
  
  // Handle simple quarter selection (left side)
  const handleQuarterSelect = (quarterId: number) => {
    setSelectedQuarter(quarterId);
    setActiveMode('single');
  };
  
  // Handle cumulation toggle (right side)
  const handleCumulationToggle = (quarterId: number) => {
    setActiveMode('cumulated');
    setCumulatedQuarters(prev => {
      if (prev.includes(quarterId)) {
        const newQuarters = prev.filter(q => q !== quarterId);
        // If no quarters left, switch back to single mode
        if (newQuarters.length === 0) {
          setActiveMode('single');
        }
        return newQuarters;
      }
      return [...prev, quarterId].sort();
    });
  };
  
  // Get label for current selection
  const getSelectionLabel = () => {
    if (activeMode === 'single') {
      return `Q${selectedQuarter}`;
    }
    if (cumulatedQuarters.length === 0) {
      return 'Válassz negyedéveket';
    }
    return cumulatedQuarters.map(q => `Q${q}`).join(' + ');
  };

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

  const getValue = (data: { current: number; cumulated: number }) => 
    isCumulated ? data.cumulated : data.current;

  return (
    <div className="space-y-6">
      {/* Header with Company Name */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold">Program Riportok</h2>
          <p className="text-muted-foreground">
            Negyedéves statisztikák és program teljesítmény
          </p>
        </div>
        {companyName && (
          <div className="flex items-center gap-2 px-4 py-2 bg-[#04565f]/10 rounded-lg">
            <Building2 className="h-5 w-5 text-[#04565f]" />
            <span className="font-semibold text-[#04565f]">{companyName}</span>
          </div>
        )}
      </div>

      {/* Loading/Error State */}
      {loading && (
        <Card>
          <CardContent className="py-8">
            <div className="flex items-center justify-center gap-3">
              <Skeleton className="h-8 w-32" />
              <Skeleton className="h-8 w-32" />
              <Skeleton className="h-8 w-32" />
            </div>
          </CardContent>
        </Card>
      )}

      {error && (
        <Card className="border-destructive">
          <CardContent className="py-4">
            <div className="flex items-center gap-2 text-destructive">
              <AlertCircle className="h-5 w-5" />
              <span>Hiba történt az adatok betöltése során: {error}</span>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Country Tabs - from API */}
      {!loading && countries.length > 0 && (
        <Tabs value={String(selectedCountryId)} onValueChange={(v) => setSelectedCountryId(Number(v))}>
          <TabsList className="bg-muted/50 p-1">
            {countries.map((country) => (
              <TabsTrigger 
                key={country.id} 
                value={String(country.id)}
                className="data-[state=active]:bg-[#04565f] data-[state=active]:text-white"
              >
                {country.name}
              </TabsTrigger>
            ))}
          </TabsList>
        </Tabs>
      )}

      {/* Quarter Selection Panel - Split: Left = Single, Right = Cumulated */}
      <Card>
        <CardContent className="py-5">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            {/* Left Side: Simple Quarter Selection */}
            <div className={`flex flex-col items-center gap-3 p-4 rounded-lg transition-all ${activeMode === 'single' ? 'bg-muted/30 ring-1 ring-[#04565f]/20' : ''}`}>
              <p className="text-sm font-medium text-foreground">Negyedév választás</p>
              <div className="flex items-center gap-2">
                {QUARTERS.map((q) => (
                  <button
                    key={q.id}
                    onClick={() => q.hasData && handleQuarterSelect(q.id)}
                    disabled={!q.hasData}
                    className={`
                      px-4 py-2 text-sm font-medium rounded-lg transition-all
                      ${!q.hasData ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'}
                      ${activeMode === 'single' && selectedQuarter === q.id
                        ? 'bg-[#04565f] text-white' 
                        : 'bg-muted text-muted-foreground hover:bg-muted/80'
                      }
                    `}
                  >
                    {q.label}
                  </button>
                ))}
              </div>
              <p className="text-xs text-muted-foreground">Egy negyedév adatai</p>
            </div>


            {/* Right Side: Cumulation Selection */}
            <div className={`flex flex-col items-center gap-3 p-4 rounded-lg transition-all ${activeMode === 'cumulated' ? 'bg-muted/30 ring-1 ring-[#04565f]/20' : ''}`}>
              <p className="text-sm font-medium text-foreground">Kumulálás</p>
              <div className="flex items-center gap-4">
                {QUARTERS.map((q) => (
                  <label
                    key={q.id}
                    className={`flex items-center gap-2 cursor-pointer ${!q.hasData ? 'opacity-30 cursor-not-allowed' : ''}`}
                  >
                    <Checkbox
                      checked={cumulatedQuarters.includes(q.id)}
                      onCheckedChange={() => q.hasData && handleCumulationToggle(q.id)}
                      disabled={!q.hasData}
                      className="data-[state=checked]:bg-[#04565f] data-[state=checked]:border-[#04565f]"
                    />
                    <span className="text-sm font-medium">{q.label}</span>
                  </label>
                ))}
              </div>
              <p className="text-xs text-muted-foreground">Több negyedév összesítve</p>
            </div>
          </div>

          {/* Selection indicator */}
          <div className="flex items-center justify-center gap-2 text-sm mt-4 pt-4 border-t">
            <span className="text-muted-foreground">Kiválasztva:</span>
            <span className="font-semibold text-[#04565f]">{getSelectionLabel()}</span>
            {isCumulated && (
              <span className="text-xs bg-[#82f5ae]/20 text-[#04565f] px-2 py-0.5 rounded-full">
                Kumulált
              </span>
            )}
          </div>
        </CardContent>
      </Card>

      {/* Main Statistics Grid - Point 1: Fő mutatók */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        {/* Total Cases */}
        <Card>
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Összes eset</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.accent }}>
                {getValue(currentData.closedCases)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.closedCases.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Total Consultations */}
        <Card>
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Összes konzultáció</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.secondary }}>
                {getValue(currentData.totalConsultations)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.totalConsultations.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Workshop Participants */}
        <Card>
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">Workshop résztvevők</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.primary }}>
                {getValue(currentData.workshopParticipants)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.workshopParticipants.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Online Logins */}
        <Card>
          <CardContent className="pt-6">
            <div className="text-center">
              <p className="text-sm text-muted-foreground mb-2">EAP Online belépések</p>
              <p className="text-4xl font-bold" style={{ color: CHART_COLORS.tertiary }}>
                {getValue(currentData.onlineLogins)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground mt-1">
                  Össz: {currentData.onlineLogins.cumulated}
                </p>
              )}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Point 2: Élő esetek típusonként */}
      <Card>
        <CardHeader>
          <div className="flex items-center gap-3">
            <CardTitle className="text-lg">Élő esetek típusonként</CardTitle>
            <span className="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-destructive text-destructive-foreground">
              <span className="relative flex h-2 w-2">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-destructive-foreground opacity-75"></span>
                <span className="relative inline-flex rounded-full h-2 w-2 bg-destructive-foreground"></span>
              </span>
              ÉLŐ
            </span>
          </div>
          <CardDescription>A folyamatban lévő esetek megoszlása szolgáltatás típus szerint</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {/* Psychology */}
            <div className="flex flex-col items-center p-4 bg-destructive/10 rounded-lg border border-destructive/20">
              <Brain className="h-8 w-8 mb-2 text-destructive" />
              <p className="text-sm text-muted-foreground text-center">Pszichológia</p>
              <p className="text-3xl font-bold mt-1 text-destructive">
                {getValue(currentData.liveCases.psychology)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground">
                  Össz: {currentData.liveCases.psychology.cumulated}
                </p>
              )}
            </div>

            {/* Law */}
            <div className="flex flex-col items-center p-4 bg-destructive/10 rounded-lg border border-destructive/20">
              <Scale className="h-8 w-8 mb-2 text-destructive" />
              <p className="text-sm text-muted-foreground text-center">Jogi tanácsadás</p>
              <p className="text-3xl font-bold mt-1 text-destructive">
                {getValue(currentData.liveCases.law)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground">
                  Össz: {currentData.liveCases.law.cumulated}
                </p>
              )}
            </div>

            {/* Finance */}
            <div className="flex flex-col items-center p-4 bg-destructive/10 rounded-lg border border-destructive/20">
              <Briefcase className="h-8 w-8 mb-2 text-destructive" />
              <p className="text-sm text-muted-foreground text-center">Pénzügyi tanácsadás</p>
              <p className="text-3xl font-bold mt-1 text-destructive">
                {getValue(currentData.liveCases.finance)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground">
                  Össz: {currentData.liveCases.finance.cumulated}
                </p>
              )}
            </div>

            {/* Health Coaching */}
            <div className="flex flex-col items-center p-4 bg-destructive/10 rounded-lg border border-destructive/20">
              <Heart className="h-8 w-8 mb-2 text-destructive" />
              <p className="text-sm text-muted-foreground text-center">Egészség coaching</p>
              <p className="text-3xl font-bold mt-1 text-destructive">
                {getValue(currentData.liveCases.healthCoaching)}
              </p>
              {!isCumulated && (
                <p className="text-xs text-muted-foreground">
                  Össz: {currentData.liveCases.healthCoaching.cumulated}
                </p>
              )}
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Point 3: Rekord kiemelések */}
      <Card className="bg-gradient-to-r from-[#04565f]/5 to-[#82f5ae]/10">
        <CardHeader>
          <CardTitle className="text-lg">Az időszak kiemelései</CardTitle>
          <CardDescription>A legjellemzőbb statisztikák a kiválasztott időszakban</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Most Frequent Problem */}
            <div className="flex flex-col items-center text-center p-4 bg-background/80 rounded-lg shadow-sm">
              <div className="w-12 h-12 rounded-full flex items-center justify-center mb-3" style={{ backgroundColor: `${CHART_COLORS.primary}20` }}>
                <Brain className="h-6 w-6" style={{ color: CHART_COLORS.primary }} />
              </div>
              <p className="text-xs text-muted-foreground uppercase tracking-wide mb-1">Leggyakoribb probléma</p>
              <p className="text-lg font-bold" style={{ color: CHART_COLORS.primary }}>
                {currentData.recordHighlights.mostFrequentProblem}
              </p>
            </div>

            {/* Dominant Gender */}
            <div className="flex flex-col items-center text-center p-4 bg-background/80 rounded-lg shadow-sm">
              <div className="w-12 h-12 rounded-full flex items-center justify-center mb-3" style={{ backgroundColor: `${CHART_COLORS.secondary}30` }}>
                <Users className="h-6 w-6" style={{ color: CHART_COLORS.primary }} />
              </div>
              <p className="text-xs text-muted-foreground uppercase tracking-wide mb-1">Többség neme</p>
              <p className="text-lg font-bold" style={{ color: CHART_COLORS.primary }}>
                {currentData.recordHighlights.dominantGender.label}
              </p>
              <p className="text-sm text-muted-foreground">
                {currentData.recordHighlights.dominantGender.percentage}%
              </p>
            </div>

            {/* Dominant Age Group */}
            <div className="flex flex-col items-center text-center p-4 bg-background/80 rounded-lg shadow-sm">
              <div className="w-12 h-12 rounded-full flex items-center justify-center mb-3" style={{ backgroundColor: `${CHART_COLORS.accent}20` }}>
                <TrendingUp className="h-6 w-6" style={{ color: CHART_COLORS.accent }} />
              </div>
              <p className="text-xs text-muted-foreground uppercase tracking-wide mb-1">Legaktívabb korosztály</p>
              <p className="text-lg font-bold" style={{ color: CHART_COLORS.accent }}>
                {currentData.recordHighlights.dominantAgeGroup.label}
              </p>
              <p className="text-sm text-muted-foreground">
                {currentData.recordHighlights.dominantAgeGroup.percentage}%
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Secondary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
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

        <Card>
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

        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Folyamatban lévő esetek</p>
                <p className="text-2xl font-bold">{currentData.inProgressCases}</p>
              </div>
              <TrendingUp className="h-8 w-8 text-muted-foreground/50" />
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Problem Types & Demographics */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Problem Types Radar */}
        <Card className="relative overflow-hidden">
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
        <Card className="relative overflow-hidden">
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
        <Card>
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
        <Card>
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

        {/* Satisfaction Score - from API */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Elégedettség</CardTitle>
            <CardDescription>
              Ügyfélelégedettségi index (1-10)
              {countryCSValues.length > 0 && (
                <span className="ml-2 text-xs text-muted-foreground">
                  ({countryCSValues.length} értékelés alapján)
                </span>
              )}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="flex flex-col items-center">
              <div className="relative">
                <div 
                  className="text-6xl font-bold"
                  style={{ color: avgSatisfaction >= 8 ? CHART_COLORS.primary : avgSatisfaction >= 6 ? CHART_COLORS.accent : '#ff0033' }}
                >
                  {avgSatisfaction > 0 ? avgSatisfaction.toFixed(1) : '-'}
                </div>
                <div className="text-sm text-muted-foreground text-center mt-1">/ 10</div>
              </div>
              
              {/* Visual scale */}
              <div className="w-full mt-6 px-4">
                <div className="relative h-2 bg-muted rounded-full">
                  <div 
                    className="absolute top-0 left-0 h-full rounded-full transition-all"
                    style={{ 
                      width: `${(avgSatisfaction / 10) * 100}%`,
                      backgroundColor: avgSatisfaction >= 8 ? CHART_COLORS.primary : avgSatisfaction >= 6 ? CHART_COLORS.accent : '#ff0033'
                    }}
                  />
                  {avgSatisfaction > 0 && (
                    <div 
                      className="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full border-2 border-background shadow-md"
                      style={{ 
                        left: `calc(${(avgSatisfaction / 10) * 100}% - 8px)`,
                        backgroundColor: avgSatisfaction >= 8 ? CHART_COLORS.primary : avgSatisfaction >= 6 ? CHART_COLORS.accent : '#ff0033'
                      }}
                    />
                  )}
                </div>
                <div className="flex justify-between mt-1 text-xs text-muted-foreground">
                  <span>1</span>
                  <span>5</span>
                  <span>10</span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Family Status */}
      <Card>
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
      <Card className="bg-gradient-to-r from-[#04565f] to-[#004144]">
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
