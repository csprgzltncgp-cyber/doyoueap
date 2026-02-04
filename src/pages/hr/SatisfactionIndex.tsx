import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ThumbsUp } from "lucide-react";

// Mock countries
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország' },
  { id: 'ro', name: 'Románia' },
  { id: 'sk', name: 'Szlovákia' },
  { id: 'cz', name: 'Csehország' },
];

// Mock satisfaction data per country (1-10 scale)
const MOCK_SATISFACTION_DATA: Record<string, number> = {
  hu: 8.4,
  ro: 7.9,
  sk: 8.1,
  cz: 8.7,
};

const SatisfactionIndex = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  const [animatedValue, setAnimatedValue] = useState(0);
  const [showTooltip, setShowTooltip] = useState(false);
  
  const currentValue = MOCK_SATISFACTION_DATA[selectedCountry] || 0;

  // Animate the value when country changes
  useEffect(() => {
    setAnimatedValue(0);
    const timer = setTimeout(() => {
      setAnimatedValue(currentValue);
      // Show tooltip after animation
      setTimeout(() => setShowTooltip(true), 300);
    }, 100);
    return () => clearTimeout(timer);
  }, [selectedCountry, currentValue]);

  const percentagePosition = (currentValue / 10) * 100;

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
      <Tabs value={selectedCountry} onValueChange={(v) => {
        setShowTooltip(false);
        setSelectedCountry(v);
      }}>
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

      {/* Description Card */}
      <Card className="bg-[#6610f2] text-white border-none">
        <CardContent className="py-6">
          <p className="text-sm md:text-base leading-relaxed">
            A tanácsadási eset lezárása előtt, minőségbiztosítási célból, rövid elégedettségi felmérést 
            végzünk, hogy megtudjuk, mennyire voltak elégedettek a munkavállalók a program használatával. 
            1-10 skálán választják ki azt a számot, amely leginkább megfelel a tanácsadás minőségének 
            (1 - legalacsonyabb, 10 - legmagasabb). Ezen értékelések átlaga havonta egyszer frissülve 
            jelenik meg ezen a felületen. Ezt nevezzük PSI-nek, azaz Program Satisfaction Indexnek.
          </p>
        </CardContent>
      </Card>

      {/* Main Satisfaction Display */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Elégedettségi mutató</CardTitle>
        </CardHeader>
        <CardContent className="pt-0 pb-8">
          <div className="relative pt-16 pb-4">
            {/* Tooltip bubble */}
            {showTooltip && currentValue > 0 && (
              <div 
                className="absolute top-0 transition-all duration-500 ease-out"
                style={{ 
                  left: `${percentagePosition}%`,
                  transform: 'translateX(-50%)'
                }}
              >
                <div className="relative">
                  <div className="bg-[#6610f2] text-white px-4 py-2 rounded-lg shadow-lg">
                    <span className="text-2xl font-bold">{currentValue.toFixed(1).replace('.', ',')}</span>
                  </div>
                  <div className="absolute left-1/2 -bottom-2 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-[#6610f2] transform -translate-x-1/2" />
                </div>
              </div>
            )}

            {/* Thumbs up icon */}
            <div 
              className="absolute transition-all duration-700 ease-out cursor-pointer group"
              style={{ 
                left: `${animatedValue * 10}%`,
                top: '40px',
                transform: 'translateX(-50%)'
              }}
              onMouseEnter={() => setShowTooltip(true)}
              onMouseLeave={() => setShowTooltip(false)}
            >
              <div className="bg-[#82f5ae] p-3 rounded-full shadow-lg group-hover:scale-110 transition-transform">
                <ThumbsUp className="w-8 h-8 text-[#04565f]" />
              </div>
            </div>

            {/* Progress bar */}
            <div className="w-full bg-muted rounded-full h-3 mt-8">
              <div 
                className="bg-[#6610f2] h-3 rounded-full transition-all duration-700 ease-out"
                style={{ width: `${animatedValue * 10}%` }}
              />
            </div>

            {/* Scale numbers */}
            <div className="flex justify-between mt-4 px-1">
              {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((num) => (
                <span 
                  key={num} 
                  className={`text-lg font-bold ${
                    num <= Math.round(currentValue) 
                      ? 'text-[#6610f2]' 
                      : 'text-muted-foreground'
                  }`}
                >
                  {num}
                </span>
              ))}
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-lg font-bold text-foreground">
              Aktuális PSI érték:
            </p>
            <p className="text-2xl text-[#04565f] font-bold">
              {currentValue.toFixed(1).replace('.', ',')} / 10
            </p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-lg font-bold text-foreground">
              Minősítés:
            </p>
            <p className="text-sm text-[#04565f] font-medium">
              {currentValue >= 9 ? 'Kiváló' : 
               currentValue >= 8 ? 'Nagyon jó' : 
               currentValue >= 7 ? 'Jó' : 
               currentValue >= 6 ? 'Megfelelő' : 
               currentValue >= 5 ? 'Elfogadható' : 'Fejlesztendő'}
            </p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-lg font-bold text-foreground">
              Mérési időszak:
            </p>
            <p className="text-sm text-[#04565f] font-medium">
              2024. Q1 - Q4
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Info Card */}
      <Card className="bg-muted/30">
        <CardContent className="py-4">
          <p className="text-sm text-muted-foreground text-center">
            Az elégedettségi index a lezárt tanácsadási esetek értékeléseiből számított átlag. 
            Az adatok havonta frissülnek és visszamenőleg kumulálódnak az aktuális naptári évre vonatkozóan.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default SatisfactionIndex;
