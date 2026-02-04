import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@/components/ui/collapsible";
import { 
  Calendar, 
  Clock, 
  MapPin, 
  User, 
  ChevronDown,
  ChevronUp,
  Euro,
  AlertTriangle
} from "lucide-react";

type CrisisStatus = "used" | "organizing" | "available";

interface CrisisIntervention {
  id: string;
  activityId: string;
  date: string | null;
  startTime: string | null;
  endTime: string | null;
  duration: string | null;
  country: string | null;
  city: string | null;
  expert: string | null;
  expertEmail: string | null;
  expertPhone: string | null;
  status: CrisisStatus;
  isFree: boolean;
  price: number | null;
  currency: string;
}

// Mock data based on Laravel structure
const mockCrisisInterventions: Record<string, CrisisIntervention[]> = {
  HU: [
    {
      id: "1",
      activityId: "CI-2024-001",
      date: "2024-03-10",
      startTime: "08:00",
      endTime: "16:00",
      duration: "8 óra",
      country: "Magyarország",
      city: "Budapest",
      expert: "Dr. Szabó László",
      expertEmail: "szabo.laszlo@example.com",
      expertPhone: "+36 30 111 2222",
      status: "used",
      isFree: false,
      price: 850000,
      currency: "HUF",
    },
    {
      id: "2",
      activityId: "CI-2024-002",
      date: "2024-04-15",
      startTime: "09:00",
      endTime: "17:00",
      duration: "8 óra",
      country: "Magyarország",
      city: "Győr",
      expert: "Dr. Kiss Éva",
      expertEmail: "kiss.eva@example.com",
      expertPhone: "+36 20 333 4444",
      status: "organizing",
      isFree: false,
      price: 750000,
      currency: "HUF",
    },
    {
      id: "3",
      activityId: "CI-2024-003",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      country: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isFree: true,
      price: null,
      currency: "HUF",
    },
  ],
  RO: [
    {
      id: "4",
      activityId: "CI-2024-004",
      date: "2024-02-20",
      startTime: "10:00",
      endTime: "18:00",
      duration: "8 óra",
      country: "România",
      city: "Cluj-Napoca",
      expert: "Dr. Ionescu Andrei",
      expertEmail: "ionescu.andrei@example.com",
      expertPhone: "+40 722 111 222",
      status: "used",
      isFree: false,
      price: 15000,
      currency: "RON",
    },
    {
      id: "5",
      activityId: "CI-2024-005",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      country: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isFree: false,
      price: null,
      currency: "RON",
    },
  ],
  SK: [
    {
      id: "6",
      activityId: "CI-2024-006",
      date: "2024-05-05",
      startTime: "08:00",
      endTime: "14:00",
      duration: "6 óra",
      country: "Slovensko",
      city: "Košice",
      expert: "Mgr. Kováč Peter",
      expertEmail: "kovac.peter@example.com",
      expertPhone: "+421 902 111 222",
      status: "organizing",
      isFree: false,
      price: 2500,
      currency: "EUR",
    },
  ],
  CZ: [
    {
      id: "7",
      activityId: "CI-2024-007",
      date: "2024-01-15",
      startTime: "09:00",
      endTime: "15:00",
      duration: "6 óra",
      country: "Česko",
      city: "Brno",
      expert: "PhDr. Černý Pavel",
      expertEmail: "cerny.pavel@example.com",
      expertPhone: "+420 603 111 222",
      status: "used",
      isFree: true,
      price: null,
      currency: "CZK",
    },
    {
      id: "8",
      activityId: "CI-2024-008",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      country: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isFree: false,
      price: null,
      currency: "CZK",
    },
  ],
};

const getStatusConfig = (status: CrisisStatus) => {
  switch (status) {
    case "used":
      return {
        label: "Felhasznált",
        bgClass: "bg-destructive",
        textClass: "text-destructive-foreground",
      };
    case "organizing":
      return {
        label: "Szervezés alatt",
        bgClass: "bg-[#ffc107]",
        textClass: "text-gray-900",
      };
    case "available":
      return {
        label: "Felhasználható",
        bgClass: "bg-[#82f5ae]",
        textClass: "text-gray-900",
      };
  }
};

const CrisisCard = ({
  crisis,
  isOpen,
  onOpenChange,
}: {
  crisis: CrisisIntervention;
  isOpen: boolean;
  onOpenChange: (open: boolean) => void;
}) => {
  const statusConfig = getStatusConfig(crisis.status);

  return (
    <Card className="overflow-hidden">
      <CardHeader className="pb-3">
        <div className="flex items-start justify-between gap-2">
          <div className="flex-1 min-w-0">
            <Badge className={`${statusConfig.bgClass} ${statusConfig.textClass} mb-2`}>
              {statusConfig.label}
            </Badge>
            <CardTitle className="text-base font-semibold">
              Krízisintervenció
            </CardTitle>
            <p className="text-xs text-muted-foreground mt-1">
              #{crisis.activityId}
            </p>
          </div>
          <AlertTriangle className="h-5 w-5 text-destructive flex-shrink-0" />
        </div>
      </CardHeader>
      
      <CardContent className="pt-0">
        {/* Quick info always visible */}
        <div className="space-y-2 text-sm">
          {crisis.date ? (
            <div className="flex items-center gap-2 text-muted-foreground">
              <Calendar className="h-4 w-4" />
              <span>{crisis.date}</span>
              {crisis.startTime && (
                <>
                  <Clock className="h-4 w-4 ml-2" />
                  <span>{crisis.startTime} - {crisis.endTime}</span>
                </>
              )}
            </div>
          ) : (
            <div className="flex items-center gap-2 text-muted-foreground">
              <Calendar className="h-4 w-4" />
              <span className="italic">Még nincs időpont</span>
            </div>
          )}
          
          {crisis.city ? (
            <div className="flex items-center gap-2 text-muted-foreground">
              <MapPin className="h-4 w-4" />
              <span>{crisis.city}</span>
            </div>
          ) : (
            <div className="flex items-center gap-2 text-muted-foreground">
              <MapPin className="h-4 w-4" />
              <span className="italic">Még nincs helyszín</span>
            </div>
          )}

          {/* Price - always visible for crisis */}
          <div className="flex items-center gap-2 text-muted-foreground">
            <Euro className="h-4 w-4" />
            {crisis.isFree ? (
              <span className="font-medium">Ingyenes</span>
            ) : crisis.price ? (
              <span className="font-medium">
                {crisis.price.toLocaleString()} {crisis.currency}
              </span>
            ) : (
              <span className="italic">Még nincs ár</span>
            )}
          </div>
        </div>

        {/* Expandable details */}
        <Collapsible open={isOpen} onOpenChange={onOpenChange} className="mt-4">
          <CollapsibleTrigger asChild>
            <Button 
              variant="ghost" 
              size="sm" 
              className="w-full justify-between hover:bg-muted/50"
              onPointerDown={(e) => e.stopPropagation()}
            >
              <span className="text-xs">
                {isOpen ? "Részletek elrejtése" : "Részletek megtekintése"}
              </span>
              {isOpen ? (
                <ChevronUp className="h-4 w-4" />
              ) : (
                <ChevronDown className="h-4 w-4" />
              )}
            </Button>
          </CollapsibleTrigger>
          
          <CollapsibleContent className="pt-3 space-y-3">
            <div className="border-t pt-3 space-y-2 text-sm">
              {/* Expert */}
              <div className="flex items-start gap-2">
                <User className="h-4 w-4 text-muted-foreground mt-0.5" />
                <div>
                  <p className="font-medium">Szakértő</p>
                  {crisis.expert ? (
                    <div className="text-muted-foreground">
                      <p>{crisis.expert}</p>
                      {crisis.expertEmail && (
                        <p className="text-xs">{crisis.expertEmail}</p>
                      )}
                      {crisis.expertPhone && (
                        <p className="text-xs">{crisis.expertPhone}</p>
                      )}
                    </div>
                  ) : (
                    <p className="text-muted-foreground italic">Még nincs szakértő</p>
                  )}
                </div>
              </div>

              {/* Duration */}
              {crisis.duration && (
                <div className="flex items-center gap-2">
                  <Clock className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="font-medium">Időtartam</p>
                    <p className="text-muted-foreground">{crisis.duration}</p>
                  </div>
                </div>
              )}

              {/* Country */}
              {crisis.country && (
                <div className="flex items-center gap-2">
                  <MapPin className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="font-medium">Ország</p>
                    <p className="text-muted-foreground">{crisis.country}</p>
                  </div>
                </div>
              )}
            </div>
          </CollapsibleContent>
        </Collapsible>
      </CardContent>
    </Card>
  );
};

const CrisisInterventions = () => {
  const [selectedCountry, setSelectedCountry] = useState("HU");
  const interventions = mockCrisisInterventions[selectedCountry] || [];

  const [openById, setOpenById] = useState<Record<string, boolean>>({});

  const countByStatus = (status: CrisisStatus) =>
    interventions.filter((c) => c.status === status).length;

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold">Krízisintervenciók</h2>
        <p className="text-muted-foreground">
          Krízisintervenciók áttekintése és kezelése
        </p>
      </div>

      {/* Summary stats */}
      <div className="grid grid-cols-3 gap-4">
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-2xl font-bold text-destructive">
              {countByStatus("used")}
            </p>
            <p className="text-xs text-muted-foreground">Felhasznált</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-2xl font-bold text-[#ffc107]">
              {countByStatus("organizing")}
            </p>
            <p className="text-xs text-muted-foreground">Szervezés alatt</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4 pb-4 text-center">
            <p className="text-2xl font-bold text-[#82f5ae]">
              {countByStatus("available")}
            </p>
            <p className="text-xs text-muted-foreground">Felhasználható</p>
          </CardContent>
        </Card>
      </div>

      {/* Country tabs */}
      <Tabs value={selectedCountry} onValueChange={setSelectedCountry}>
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="HU">Magyarország</TabsTrigger>
          <TabsTrigger value="RO">Románia</TabsTrigger>
          <TabsTrigger value="SK">Szlovákia</TabsTrigger>
          <TabsTrigger value="CZ">Csehország</TabsTrigger>
        </TabsList>

        {Object.keys(mockCrisisInterventions).map((country) => (
          <TabsContent key={country} value={country} className="mt-6">
            {mockCrisisInterventions[country].length > 0 ? (
              <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                {mockCrisisInterventions[country].map((crisis) => (
                  <CrisisCard
                    key={crisis.id}
                    crisis={crisis}
                    isOpen={!!openById[crisis.id]}
                    onOpenChange={(open) =>
                      setOpenById((prev) => ({
                        ...prev,
                        [crisis.id]: open,
                      }))
                    }
                  />
                ))}
              </div>
            ) : (
              <Card className="border-dashed border-2">
                <CardContent className="py-12 text-center">
                  <p className="text-muted-foreground">
                    Nincsenek krízisintervenciók ebben az országban.
                  </p>
                </CardContent>
              </Card>
            )}
          </TabsContent>
        ))}
      </Tabs>
    </div>
  );
};

export default CrisisInterventions;
