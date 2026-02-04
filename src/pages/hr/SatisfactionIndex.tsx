import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { GaugeChart } from "@/components/ui/gauge-chart";
import { TrendingUp, Award, Calendar } from "lucide-react";

// Mock countries
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország' },
  { id: 'ro', name: 'Románia' },
  { id: 'sk', name: 'Szlovákia' },
  { id: 'cz', name: 'Csehország' },
];

// Mock satisfaction data per country (1-10 scale)
const MOCK_SATISFACTION_DATA: Record<string, {
  value: number;
  previousValue: number;
  totalResponses: number;
  bestMonth: string;
}> = {
  hu: { value: 8.4, previousValue: 8.1, totalResponses: 234, bestMonth: 'Március' },
  ro: { value: 7.9, previousValue: 7.5, totalResponses: 156, bestMonth: 'Április' },
  sk: { value: 8.1, previousValue: 8.0, totalResponses: 89, bestMonth: 'Február' },
  cz: { value: 8.7, previousValue: 8.4, totalResponses: 178, bestMonth: 'Január' },
};

const getRatingLabel = (value: number): string => {
  if (value >= 9) return 'Kiváló';
  if (value >= 8) return 'Nagyon jó';
  if (value >= 7) return 'Jó';
  if (value >= 6) return 'Megfelelő';
  if (value >= 5) return 'Elfogadható';
  return 'Fejlesztendő';
};

const SatisfactionIndex = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  
  const currentData = MOCK_SATISFACTION_DATA[selectedCountry];
  const trend = currentData.value - currentData.previousValue;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h2 className="text-2xl font-bold">Elégedettségi Index (PSI)</h2>
        <p className="text-muted-foreground">
          Program Satisfaction Index - Ügyfélelégedettségi mutató
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

      {/* Main Gauge Display */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* PSI Gauge */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Aktuális PSI érték</CardTitle>
            <CardDescription>
              Ügyfélelégedettségi mutató 1-10 skálán
            </CardDescription>
          </CardHeader>
          <CardContent className="flex flex-col items-center pb-8">
            <GaugeChart 
              value={currentData.value * 10} 
              maxValue={100}
              size={400}
              label={`${currentData.value.toFixed(1).replace('.', ',')} / 10`}
            />
            <div className="mt-4 text-center">
              <p className="text-2xl font-bold text-[#04565f]">
                {getRatingLabel(currentData.value)}
              </p>
              <div className="flex items-center justify-center gap-2 mt-2">
                <TrendingUp className={`h-4 w-4 ${trend >= 0 ? 'text-[#82f5ae]' : 'text-destructive'}`} />
                <span className={`text-sm font-medium ${trend >= 0 ? 'text-[#04565f]' : 'text-destructive'}`}>
                  {trend >= 0 ? '+' : ''}{trend.toFixed(1).replace('.', ',')} az előző időszakhoz képest
                </span>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Stats Cards */}
        <div className="space-y-4">
          {/* Rating Card */}
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-[#82f5ae]/20 rounded-lg">
                  <Award className="h-6 w-6 text-[#04565f]" />
                </div>
                <div className="flex-1">
                  <p className="text-sm text-muted-foreground">Minősítés</p>
                  <p className="text-xl font-bold text-foreground">{getRatingLabel(currentData.value)}</p>
                </div>
                <div className="text-right">
                  <p className="text-3xl font-bold text-[#04565f]">
                    {currentData.value.toFixed(1).replace('.', ',')}
                  </p>
                  <p className="text-xs text-muted-foreground">/ 10</p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Responses Card */}
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-[#ffc107]/20 rounded-lg">
                  <TrendingUp className="h-6 w-6 text-[#04565f]" />
                </div>
                <div className="flex-1">
                  <p className="text-sm text-muted-foreground">Válaszok száma</p>
                  <p className="text-xl font-bold text-foreground">{currentData.totalResponses} válasz</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-muted-foreground">Előző érték</p>
                  <p className="text-lg font-semibold text-muted-foreground">
                    {currentData.previousValue.toFixed(1).replace('.', ',')}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Best Month Card */}
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className="p-3 bg-[#04565f]/10 rounded-lg">
                  <Calendar className="h-6 w-6 text-[#04565f]" />
                </div>
                <div className="flex-1">
                  <p className="text-sm text-muted-foreground">Legjobb hónap</p>
                  <p className="text-xl font-bold text-foreground">{currentData.bestMonth}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-muted-foreground">Mérési időszak</p>
                  <p className="text-lg font-semibold text-[#04565f]">2024</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Scale Reference */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">PSI Skála</CardTitle>
          <CardDescription>Az elégedettségi index értelmezése</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-3 justify-center">
            {[
              { range: '9-10', label: 'Kiváló', color: '#04565f' },
              { range: '8-8.9', label: 'Nagyon jó', color: '#82f5ae' },
              { range: '7-7.9', label: 'Jó', color: '#ffc107' },
              { range: '6-6.9', label: 'Megfelelő', color: '#ffc107' },
              { range: '5-5.9', label: 'Elfogadható', color: '#ff9800' },
              { range: '1-4.9', label: 'Fejlesztendő', color: '#f44336' },
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
            A PSI (Program Satisfaction Index) a lezárt tanácsadási esetek 1-10 skálán mért értékeléseinek átlaga. 
            A munkavállalók a tanácsadás lezárásakor értékelik az általuk kapott szolgáltatás minőségét.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default SatisfactionIndex;
