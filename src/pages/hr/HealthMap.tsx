import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";

// Mock countries matching ProgramReports
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország' },
  { id: 'ro', name: 'Románia' },
  { id: 'sk', name: 'Szlovákia' },
  { id: 'cz', name: 'Csehország' },
];

// Problem types (rows) - matching Laravel structure
const PROBLEM_TYPES = [
  { id: 1, name: 'Pszichológia', key: 'psychology' },
  { id: 2, name: 'Jog', key: 'law' },
  { id: 3, name: 'Pénzügy', key: 'finance' },
  { id: 7, name: 'Health Coaching', key: 'healthCoaching' },
];

// Age groups (columns) - matching Laravel structure
const AGE_GROUPS = [
  { id: 11, label: '19 alatt', key: 'under19' },
  { id: 12, label: '20-29', key: '20-29' },
  { id: 13, label: '30-39', key: '30-39' },
  { id: 14, label: '40-49', key: '40-49' },
  { id: 15, label: '50-59', key: '50-59' },
  { id: 16, label: '60+', key: '60+' },
];

// Circle sizes
type CircleSize = 'large' | 'medium' | 'small';

interface HealthCircle {
  size: CircleSize;
  ageId: number;
  problemTypeId: number;
  gender: 'male' | 'female';
}

// Mock health map data per country
const MOCK_HEALTH_DATA: Record<string, HealthCircle[]> = {
  hu: [
    // Psychology - dominant in 30-39 and 40-49
    { size: 'large', ageId: 13, problemTypeId: 1, gender: 'female' },
    { size: 'medium', ageId: 14, problemTypeId: 1, gender: 'female' },
    { size: 'small', ageId: 12, problemTypeId: 1, gender: 'male' },
    { size: 'medium', ageId: 13, problemTypeId: 1, gender: 'male' },
    // Law - spread across ages
    { size: 'large', ageId: 14, problemTypeId: 2, gender: 'female' },
    { size: 'small', ageId: 15, problemTypeId: 2, gender: 'male' },
    // Finance - mostly 40-49
    { size: 'large', ageId: 14, problemTypeId: 3, gender: 'male' },
    { size: 'medium', ageId: 13, problemTypeId: 3, gender: 'female' },
    // Health Coaching - younger demographics
    { size: 'medium', ageId: 12, problemTypeId: 7, gender: 'female' },
    { size: 'small', ageId: 13, problemTypeId: 7, gender: 'male' },
  ],
  ro: [
    { size: 'large', ageId: 12, problemTypeId: 1, gender: 'female' },
    { size: 'medium', ageId: 13, problemTypeId: 1, gender: 'female' },
    { size: 'small', ageId: 14, problemTypeId: 1, gender: 'male' },
    { size: 'large', ageId: 13, problemTypeId: 2, gender: 'male' },
    { size: 'medium', ageId: 14, problemTypeId: 3, gender: 'female' },
    { size: 'small', ageId: 12, problemTypeId: 7, gender: 'female' },
  ],
  sk: [
    { size: 'large', ageId: 14, problemTypeId: 1, gender: 'female' },
    { size: 'medium', ageId: 13, problemTypeId: 1, gender: 'male' },
    { size: 'large', ageId: 15, problemTypeId: 2, gender: 'male' },
    { size: 'small', ageId: 14, problemTypeId: 3, gender: 'female' },
    { size: 'medium', ageId: 13, problemTypeId: 7, gender: 'female' },
  ],
  cz: [
    { size: 'large', ageId: 13, problemTypeId: 1, gender: 'female' },
    { size: 'medium', ageId: 14, problemTypeId: 1, gender: 'male' },
    { size: 'small', ageId: 12, problemTypeId: 1, gender: 'female' },
    { size: 'medium', ageId: 14, problemTypeId: 2, gender: 'female' },
    { size: 'large', ageId: 13, problemTypeId: 3, gender: 'male' },
    { size: 'small', ageId: 12, problemTypeId: 7, gender: 'male' },
  ],
};

// Circle component
const HealthCircle = ({ 
  size, 
  gender,
  offset = false 
}: { 
  size: CircleSize; 
  gender: 'male' | 'female';
  offset?: boolean;
}) => {
  const sizeClasses = {
    large: 'w-12 h-12 md:w-16 md:h-16',
    medium: 'w-8 h-8 md:w-12 md:h-12',
    small: 'w-5 h-5 md:w-8 md:h-8',
  };

  const colorClass = gender === 'female' ? 'bg-[#6610f2]' : 'bg-[#82f5ae]';
  
  return (
    <div 
      className={`
        ${sizeClasses[size]} 
        ${colorClass} 
        rounded-full 
        opacity-90
        shadow-lg
        ${offset ? 'translate-x-2' : ''}
        transition-transform hover:scale-110
      `}
      title={gender === 'female' ? 'Nő' : 'Férfi'}
    />
  );
};

// Background color themes
type BackgroundTheme = 'green' | 'light-grey' | 'medium-grey' | 'transparent';

const BACKGROUND_THEMES: { id: BackgroundTheme; name: string; lightClass: string; darkClass: string }[] = [
  { id: 'green', name: 'Zöld', lightClass: 'bg-[#82f5ae]/30', darkClass: 'bg-[#82f5ae]/20' },
  { id: 'light-grey', name: 'Világos szürke', lightClass: 'bg-gray-100', darkClass: 'bg-gray-50' },
  { id: 'medium-grey', name: 'Közepes szürke', lightClass: 'bg-gray-200', darkClass: 'bg-gray-100' },
  { id: 'transparent', name: 'Átlátszó', lightClass: 'bg-muted/20', darkClass: 'bg-muted/10' },
];

// Row component for each problem type
const HealthMapRow = ({ 
  problemType, 
  circles, 
  isLight,
  bgTheme
}: { 
  problemType: typeof PROBLEM_TYPES[0]; 
  circles: HealthCircle[];
  isLight: boolean;
  bgTheme: BackgroundTheme;
}) => {
  const rowCircles = circles.filter(c => c.problemTypeId === problemType.id);
  const theme = BACKGROUND_THEMES.find(t => t.id === bgTheme) || BACKGROUND_THEMES[0];
  
  // Text color based on background theme
  const getTextColor = () => {
    switch (bgTheme) {
      case 'green':
        return 'text-[#04565f]/20';
      case 'light-grey':
      case 'medium-grey':
        return 'text-gray-400/40';
      case 'transparent':
        return 'text-muted-foreground/15';
      default:
        return 'text-muted-foreground/10';
    }
  };
  
  return (
    <div className="relative">
      {/* Background label */}
      <div className="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none">
        <span className={`text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-bold ${getTextColor()} uppercase whitespace-nowrap select-none`}>
          {problemType.name}
        </span>
      </div>
      
      {/* Grid cells */}
      <div className="grid grid-cols-6 gap-0 relative" style={{ minHeight: '120px' }}>
        {AGE_GROUPS.map((ageGroup, colIdx) => {
          const cellCircles = rowCircles.filter(c => c.ageId === ageGroup.id);
          const isLastColumn = colIdx === AGE_GROUPS.length - 1;
          
          return (
            <div 
              key={ageGroup.id}
              className={`
                flex items-center justify-center gap-1 p-2
                ${isLight ? theme.lightClass : theme.darkClass}
                min-h-[100px] md:min-h-[120px]
                ${bgTheme === 'transparent' ? 'border-b border-r border-gray-200' : ''}
                ${bgTheme === 'transparent' && isLastColumn ? 'border-r-0' : ''}
              `}
            >
              {cellCircles.map((circle, idx) => (
                <HealthCircle 
                  key={idx}
                  size={circle.size}
                  gender={circle.gender}
                  offset={cellCircles.length > 1 && idx > 0}
                />
              ))}
            </div>
          );
        })}
      </div>
    </div>
  );
};

const HealthMap = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  const [bgTheme, setBgTheme] = useState<BackgroundTheme>('light-grey');
  
  const currentCircles = MOCK_HEALTH_DATA[selectedCountry] || [];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 className="text-2xl font-bold">Egészség Térkép</h2>
          <p className="text-muted-foreground">
            Problématípusok megoszlása korosztályonként és nem szerint
          </p>
        </div>
        
        {/* Background Theme Selector */}
        <div className="flex items-center gap-2">
          <span className="text-sm text-muted-foreground">Háttér:</span>
          <div className="flex gap-1">
            {BACKGROUND_THEMES.map((theme) => (
              <button
                key={theme.id}
                onClick={() => setBgTheme(theme.id)}
                className={`px-3 py-1.5 text-xs rounded-md transition-colors ${
                  bgTheme === theme.id 
                    ? 'bg-[#04565f] text-white' 
                    : 'bg-muted hover:bg-muted/80 text-muted-foreground'
                }`}
              >
                {theme.name}
              </button>
            ))}
          </div>
        </div>
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

      {/* Legend */}
      <Card>
        <CardContent className="py-4">
          <div className="flex flex-wrap items-center justify-center gap-6">
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 rounded-full bg-[#6610f2]" />
              <span className="text-sm text-muted-foreground">Nő</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 rounded-full bg-[#82f5ae]" />
              <span className="text-sm text-muted-foreground">Férfi</span>
            </div>
            <div className="border-l pl-6 flex items-center gap-4">
              <div className="flex items-center gap-1">
                <div className="w-4 h-4 rounded-full bg-muted-foreground/40" />
                <span className="text-xs text-muted-foreground">Kisebb igénybevétel</span>
              </div>
              <div className="flex items-center gap-1">
                <div className="w-6 h-6 rounded-full bg-muted-foreground/40" />
                <span className="text-xs text-muted-foreground">Közepes igénybevétel</span>
              </div>
              <div className="flex items-center gap-1">
                <div className="w-8 h-8 rounded-full bg-muted-foreground/40" />
                <span className="text-xs text-muted-foreground">Nagy igénybevétel</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Health Map Grid */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Probléma típusok és korosztályok mátrixa</CardTitle>
        </CardHeader>
        <CardContent className="p-0 overflow-x-auto">
          <div className="min-w-[600px]">
            {/* Problem type rows */}
            <div className="space-y-1">
              {PROBLEM_TYPES.map((pt, idx) => (
                <HealthMapRow 
                  key={pt.id}
                  problemType={pt}
                  circles={currentCircles}
                  isLight={idx % 2 === 0}
                  bgTheme={bgTheme}
                />
              ))}
            </div>
            
            {/* Age group labels bar */}
            <div className="grid grid-cols-6 gap-1 mt-4">
              {AGE_GROUPS.map(() => (
                <div key={crypto.randomUUID()} className="h-2 bg-muted rounded-full" />
              ))}
            </div>
            
            {/* Age group labels */}
            <div className="grid grid-cols-6 gap-1 mt-2 text-center">
              {AGE_GROUPS.map((ag) => (
                <div key={ag.id} className="text-xs sm:text-sm font-medium text-muted-foreground uppercase">
                  {ag.label}
                </div>
              ))}
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Summary Stats - 3 cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-lg font-bold text-foreground">
              Legmagasabb igénybevétel:
            </p>
            <p className="text-sm text-[#04565f] font-medium">
              {(() => {
                const problemCounts = PROBLEM_TYPES.map(pt => ({
                  name: pt.name,
                  count: currentCircles.filter(c => c.problemTypeId === pt.id).length
                }));
                const maxProblem = problemCounts.reduce((max, p) => p.count > max.count ? p : max, problemCounts[0]);
                return maxProblem?.name || '-';
              })()}
            </p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-lg font-bold text-foreground">
              Legtöbb igénybevevő nem szerint:
            </p>
            <p className="text-sm text-[#04565f] font-medium">
              {currentCircles.filter(c => c.gender === 'female').length >= 
               currentCircles.filter(c => c.gender === 'male').length ? 'Nő' : 'Férfi'}
            </p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-lg font-bold text-foreground">
              Legaktívabb korcsoport:
            </p>
            <p className="text-sm text-[#04565f] font-medium">
              {AGE_GROUPS.find(ag => 
                currentCircles.filter(c => c.ageId === ag.id).length === 
                Math.max(...AGE_GROUPS.map(a => currentCircles.filter(c => c.ageId === a.id).length))
              )?.label || '-'}
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Info Card */}
      <Card className="bg-muted/30">
        <CardContent className="py-4">
          <p className="text-sm text-muted-foreground text-center">
            Az Egészség Térkép vizualizálja a problématípusok és korosztályok kapcsolatát. 
            A körök mérete a gyakoriságot, színe pedig a nemet jelöli. 
            A nagyobb körök több esetet jelentenek az adott kategóriában.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default HealthMap;
