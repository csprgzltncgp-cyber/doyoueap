import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Progress } from "@/components/ui/progress";
import { ChevronRight, TrendingUp, TrendingDown, Users, Phone, FileText, AlertTriangle, Clock } from "lucide-react";

// Mock data for program reports
const MOCK_COUNTRIES = [
  { id: 'hu', name: 'Magyarország' },
  { id: 'ro', name: 'Románia' },
  { id: 'sk', name: 'Szlovákia' },
  { id: 'cz', name: 'Csehország' },
];

interface QuarterData {
  quarter: number;
  year: number;
  hasData: boolean;
  isCurrent: boolean;
}

const MOCK_QUARTERS: QuarterData[] = [
  { quarter: 1, year: 2024, hasData: true, isCurrent: false },
  { quarter: 2, year: 2024, hasData: true, isCurrent: false },
  { quarter: 3, year: 2024, hasData: true, isCurrent: false },
  { quarter: 4, year: 2024, hasData: true, isCurrent: true },
];

interface ReportData {
  closedCases: { current: number; cumulated: number };
  interruptedCases: { current: number; cumulated: number };
  unreachableCases: { current: number; cumulated: number };
  consultations: { current: number; cumulated: number };
  workshopParticipants: { current: number; cumulated: number };
  crisisParticipants: { current: number; cumulated: number };
  onlineLogins: { current: number; cumulated: number };
  cumulatedTotal: number;
  cumulatedText: string;
}

const MOCK_DATA_BY_COUNTRY: Record<string, ReportData> = {
  hu: {
    closedCases: { current: 45, cumulated: 320 },
    interruptedCases: { current: 8, cumulated: 42 },
    unreachableCases: { current: 5, cumulated: 28 },
    consultations: { current: 156, cumulated: 892 },
    workshopParticipants: { current: 120, cumulated: 560 },
    crisisParticipants: { current: 12, cumulated: 48 },
    onlineLogins: { current: 234, cumulated: 1240 },
    cumulatedTotal: 412,
    cumulatedText: '2024. január 1. - december 31.',
  },
  ro: {
    closedCases: { current: 32, cumulated: 210 },
    interruptedCases: { current: 5, cumulated: 28 },
    unreachableCases: { current: 3, cumulated: 18 },
    consultations: { current: 98, cumulated: 542 },
    workshopParticipants: { current: 80, cumulated: 340 },
    crisisParticipants: { current: 6, cumulated: 24 },
    onlineLogins: { current: 156, cumulated: 820 },
    cumulatedTotal: 289,
    cumulatedText: '2024. január 1. - december 31.',
  },
  sk: {
    closedCases: { current: 28, cumulated: 180 },
    interruptedCases: { current: 4, cumulated: 22 },
    unreachableCases: { current: 2, cumulated: 14 },
    consultations: { current: 76, cumulated: 420 },
    workshopParticipants: { current: 60, cumulated: 280 },
    crisisParticipants: { current: 4, cumulated: 18 },
    onlineLogins: { current: 112, cumulated: 580 },
    cumulatedTotal: 234,
    cumulatedText: '2024. január 1. - december 31.',
  },
  cz: {
    closedCases: { current: 38, cumulated: 245 },
    interruptedCases: { current: 6, cumulated: 32 },
    unreachableCases: { current: 4, cumulated: 20 },
    consultations: { current: 112, cumulated: 620 },
    workshopParticipants: { current: 90, cumulated: 420 },
    crisisParticipants: { current: 8, cumulated: 32 },
    onlineLogins: { current: 178, cumulated: 920 },
    cumulatedTotal: 325,
    cumulatedText: '2024. január 1. - december 31.',
  },
};

const QUARTER_LABELS: Record<number, string> = {
  1: 'Q1',
  2: 'Q2',
  3: 'Q3',
  4: 'Q4',
};

interface CaseNumberCardProps {
  currentNumber: number;
  allNumber: number;
  text: string;
  infoText?: string;
  cardInfoText?: string;
}

const CaseNumberCard = ({ currentNumber, allNumber, text, infoText, cardInfoText }: CaseNumberCardProps) => {
  const [showCumulated, setShowCumulated] = useState(false);
  
  return (
    <Card className="border-muted">
      <CardContent className="p-6">
        <div className="flex items-center justify-between">
          <div className="flex-1">
            <p className="text-sm text-muted-foreground mb-1">{text}</p>
            <div className="flex items-baseline gap-4">
              <span className={`text-3xl font-bold ${showCumulated ? 'text-muted-foreground' : 'text-foreground'}`}>
                {showCumulated ? allNumber : currentNumber}
              </span>
              {!showCumulated && allNumber > 0 && (
                <span className="text-sm text-muted-foreground">
                  / {allNumber} összesen
                </span>
              )}
            </div>
            {cardInfoText && (
              <p className="text-xs text-muted-foreground mt-2">{cardInfoText}</p>
            )}
          </div>
          <div className="flex flex-col items-end gap-2">
            <button
              onClick={() => setShowCumulated(!showCumulated)}
              className={`text-xs px-3 py-1 rounded-full transition-colors ${
                showCumulated 
                  ? 'bg-[#04565f] text-white' 
                  : 'bg-muted text-muted-foreground hover:bg-muted/80'
              }`}
            >
              {showCumulated ? 'Aktuális' : 'Kumulált'}
            </button>
            {infoText && showCumulated && (
              <span className="text-xs text-muted-foreground">{infoText}</span>
            )}
          </div>
        </div>
      </CardContent>
    </Card>
  );
};

interface CaseNumberCardBigProps {
  text: string;
  value: number;
  info: string;
}

const CaseNumberCardBig = ({ text, value, info }: CaseNumberCardBigProps) => {
  return (
    <Card className="border-muted bg-gradient-to-r from-[#04565f] to-[#004144]">
      <CardContent className="p-8">
        <div className="text-center text-white">
          <p className="text-lg font-medium mb-2">{text}</p>
          <p className="text-5xl font-bold mb-2">{value}</p>
          <p className="text-sm opacity-80">{info}</p>
        </div>
      </CardContent>
    </Card>
  );
};

const ProgramReports = () => {
  const [selectedCountry, setSelectedCountry] = useState(MOCK_COUNTRIES[0].id);
  const [selectedQuarter, setSelectedQuarter] = useState(4);

  const currentData = MOCK_DATA_BY_COUNTRY[selectedCountry];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h2 className="text-2xl font-bold">Program Riportok</h2>
        <p className="text-muted-foreground">
          Negyedéves statisztikák: lezárt esetek, konzultációk, workshopok
        </p>
      </div>

      {/* Country Tabs */}
      <Tabs value={selectedCountry} onValueChange={setSelectedCountry}>
        <TabsList className="bg-[#82f5ae]/20 border border-[#04565f]/20">
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

      {/* Quarter Timeline */}
      <Card className="border-muted">
        <CardContent className="py-8">
          <div className="relative w-4/5 mx-auto">
            {/* Timeline bar */}
            <div className="h-2.5 bg-[#04565f] rounded-lg w-full" />
            
            {/* Quarter markers */}
            <div className="absolute top-0 left-0 right-0 flex justify-between -mt-3">
              {MOCK_QUARTERS.map((q) => (
                <button
                  key={q.quarter}
                  onClick={() => q.hasData && setSelectedQuarter(q.quarter)}
                  disabled={!q.hasData}
                  className={`flex flex-col items-center ${
                    !q.hasData ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                  }`}
                >
                  <div 
                    className={`h-8 w-8 rounded-full flex items-center justify-center mb-2 transition-all ${
                      selectedQuarter === q.quarter
                        ? 'bg-[#04565f] ring-4 ring-[#82f5ae]'
                        : q.hasData 
                          ? 'bg-[#04565f] hover:ring-2 hover:ring-[#82f5ae]/50'
                          : 'bg-muted'
                    }`}
                  >
                    {q.hasData ? (
                      selectedQuarter === q.quarter ? (
                        <div className="w-4 h-4 rounded-full bg-white" />
                      ) : (
                        <div className="w-4 h-4 rounded-full bg-[#04565f] border-2 border-white" />
                      )
                    ) : (
                      <span className="text-white text-xs">✕</span>
                    )}
                  </div>
                  <span className={`text-sm ${
                    selectedQuarter === q.quarter 
                      ? 'text-[#04565f] font-semibold' 
                      : 'text-muted-foreground'
                  }`}>
                    {q.year} - {QUARTER_LABELS[q.quarter]}
                  </span>
                </button>
              ))}
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Cumulated Total Card */}
      <CaseNumberCardBig
        text="Összesített esetek száma:"
        value={currentData.cumulatedTotal}
        info={currentData.cumulatedText}
      />

      {/* Case Cards */}
      <div className="space-y-3">
        <CaseNumberCard
          currentNumber={currentData.closedCases.current}
          allNumber={currentData.closedCases.cumulated}
          text="Lezárt esetek"
          infoText={currentData.cumulatedText}
          cardInfoText="Az adott időszakban sikeresen lezárt ügyek száma"
        />

        <CaseNumberCard
          currentNumber={currentData.interruptedCases.current}
          allNumber={currentData.interruptedCases.cumulated}
          text="Megszakított esetek"
          infoText={currentData.cumulatedText}
          cardInfoText="Az ügyfél által megszakított esetek"
        />

        <CaseNumberCard
          currentNumber={currentData.unreachableCases.current}
          allNumber={currentData.unreachableCases.cumulated}
          text="Nem elérhető ügyfelek"
          infoText={currentData.cumulatedText}
          cardInfoText="Az ügyfél nem volt elérhető a kapcsolatfelvételkor"
        />

        <div className="h-3" />

        <CaseNumberCard
          currentNumber={currentData.consultations.current}
          allNumber={currentData.consultations.cumulated}
          text="Konzultációk száma"
          infoText={currentData.cumulatedText}
        />

        <div className="h-3" />

        {currentData.workshopParticipants.cumulated > 0 && (
          <CaseNumberCard
            currentNumber={currentData.workshopParticipants.current}
            allNumber={currentData.workshopParticipants.cumulated}
            text="Workshop résztvevők"
            infoText={currentData.cumulatedText}
          />
        )}

        {currentData.crisisParticipants.cumulated > 0 && (
          <CaseNumberCard
            currentNumber={currentData.crisisParticipants.current}
            allNumber={currentData.crisisParticipants.cumulated}
            text="Krízisintervenció résztvevői"
            infoText={currentData.cumulatedText}
          />
        )}

        <div className="h-3" />

        <CaseNumberCard
          currentNumber={currentData.onlineLogins.current}
          allNumber={currentData.onlineLogins.cumulated}
          text="Online bejelentkezések"
          infoText={currentData.cumulatedText}
        />
      </div>
    </div>
  );
};

export default ProgramReports;
