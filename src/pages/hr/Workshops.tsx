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
  BookOpen,
  ChevronDown,
  ChevronUp,
  Gift,
  Euro
} from "lucide-react";

type WorkshopStatus = "used" | "organizing" | "available";

interface Workshop {
  id: string;
  activityId: string;
  theme: string;
  date: string | null;
  startTime: string | null;
  endTime: string | null;
  duration: string | null;
  city: string | null;
  expert: string | null;
  expertEmail: string | null;
  expertPhone: string | null;
  status: WorkshopStatus;
  isGift: boolean;
  isFree: boolean;
  price: number | null;
  currency: string;
  participants: number | null;
  language: string | null;
}

// Mock data based on Laravel structure
const mockWorkshops: Record<string, Workshop[]> = {
  HU: [
    {
      id: "1",
      activityId: "WS-2024-001",
      theme: "Stresszkezelés",
      date: "2024-03-15",
      startTime: "09:00",
      endTime: "12:00",
      duration: "3 óra",
      city: "Budapest",
      expert: "Dr. Kovács Anna",
      expertEmail: "kovacs.anna@example.com",
      expertPhone: "+36 30 123 4567",
      status: "used",
      isGift: false,
      isFree: false,
      price: 450000,
      currency: "HUF",
      participants: 25,
      language: "hu",
    },
    {
      id: "2",
      activityId: "WS-2024-002",
      theme: "Burnout prevenció",
      date: "2024-04-20",
      startTime: "14:00",
      endTime: "17:00",
      duration: "3 óra",
      city: "Debrecen",
      expert: "Nagy Péter",
      expertEmail: "nagy.peter@example.com",
      expertPhone: "+36 20 987 6543",
      status: "organizing",
      isGift: false,
      isFree: false,
      price: 380000,
      currency: "HUF",
      participants: null,
      language: "hu",
    },
    {
      id: "3",
      activityId: "WS-2024-003",
      theme: "Csapatépítés",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isGift: true,
      isFree: true,
      price: null,
      currency: "HUF",
      participants: null,
      language: null,
    },
    {
      id: "4",
      activityId: "WS-2024-004",
      theme: "Kommunikáció fejlesztés",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isGift: false,
      isFree: false,
      price: null,
      currency: "HUF",
      participants: null,
      language: null,
    },
  ],
  RO: [
    {
      id: "5",
      activityId: "WS-2024-005",
      theme: "Managementul stresului",
      date: "2024-02-28",
      startTime: "10:00",
      endTime: "13:00",
      duration: "3 óra",
      city: "București",
      expert: "Dr. Popescu Maria",
      expertEmail: "popescu.maria@example.com",
      expertPhone: "+40 721 123 456",
      status: "used",
      isGift: false,
      isFree: false,
      price: 8500,
      currency: "RON",
      participants: 30,
      language: "ro",
    },
    {
      id: "6",
      activityId: "WS-2024-006",
      theme: "Leadership",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isGift: false,
      isFree: false,
      price: null,
      currency: "RON",
      participants: null,
      language: null,
    },
  ],
  SK: [
    {
      id: "7",
      activityId: "WS-2024-007",
      theme: "Zvládanie stresu",
      date: "2024-05-10",
      startTime: "09:00",
      endTime: "12:00",
      duration: "3 óra",
      city: "Bratislava",
      expert: "Mgr. Horváth Jana",
      expertEmail: "horvath.jana@example.com",
      expertPhone: "+421 905 123 456",
      status: "organizing",
      isGift: false,
      isFree: false,
      price: 1200,
      currency: "EUR",
      participants: null,
      language: "sk",
    },
  ],
  CZ: [
    {
      id: "8",
      activityId: "WS-2024-008",
      theme: "Zvládání stresu",
      date: "2024-01-20",
      startTime: "13:00",
      endTime: "16:00",
      duration: "3 óra",
      city: "Praha",
      expert: "PhDr. Novák Jan",
      expertEmail: "novak.jan@example.com",
      expertPhone: "+420 602 123 456",
      status: "used",
      isGift: true,
      isFree: true,
      price: null,
      currency: "CZK",
      participants: 22,
      language: "cs",
    },
    {
      id: "9",
      activityId: "WS-2024-009",
      theme: "Work-life balance",
      date: null,
      startTime: null,
      endTime: null,
      duration: null,
      city: null,
      expert: null,
      expertEmail: null,
      expertPhone: null,
      status: "available",
      isGift: false,
      isFree: false,
      price: null,
      currency: "CZK",
      participants: null,
      language: null,
    },
  ],
};

const getStatusConfig = (status: WorkshopStatus) => {
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

const WorkshopCard = ({
  workshop,
  isOpen,
  onOpenChange,
}: {
  workshop: Workshop;
  isOpen: boolean;
  onOpenChange: (open: boolean) => void;
}) => {
  const statusConfig = getStatusConfig(workshop.status);

  return (
    <Card className="overflow-hidden">
      <CardHeader className="pb-3">
        <div className="flex items-start justify-between gap-2">
          <div className="flex-1 min-w-0">
            <Badge className={`${statusConfig.bgClass} ${statusConfig.textClass} mb-2`}>
              {statusConfig.label}
            </Badge>
            <CardTitle className="text-base font-semibold truncate">
              {workshop.theme}
            </CardTitle>
            <p className="text-xs text-muted-foreground mt-1">
              #{workshop.activityId}
            </p>
          </div>
          {workshop.isGift && (
            <Gift className="h-5 w-5 text-destructive flex-shrink-0" />
          )}
        </div>
      </CardHeader>
      
      <CardContent className="pt-0">
        {/* Quick info always visible */}
        <div className="space-y-2 text-sm">
          {workshop.date ? (
            <div className="flex items-center gap-2 text-muted-foreground">
              <Calendar className="h-4 w-4" />
              <span>{workshop.date}</span>
              {workshop.startTime && (
                <>
                  <Clock className="h-4 w-4 ml-2" />
                  <span>{workshop.startTime} - {workshop.endTime}</span>
                </>
              )}
            </div>
          ) : (
            <div className="flex items-center gap-2 text-muted-foreground">
              <Calendar className="h-4 w-4" />
              <span className="italic">Még nincs időpont</span>
            </div>
          )}
          
          {workshop.city ? (
            <div className="flex items-center gap-2 text-muted-foreground">
              <MapPin className="h-4 w-4" />
              <span>{workshop.city}</span>
            </div>
          ) : (
            <div className="flex items-center gap-2 text-muted-foreground">
              <MapPin className="h-4 w-4" />
              <span className="italic">Még nincs helyszín</span>
            </div>
          )}
        </div>

        {/* Expandable details */}
        <Collapsible open={isOpen} onOpenChange={onOpenChange} className="mt-4">
          <CollapsibleTrigger asChild>
            <Button 
              variant="ghost" 
              size="sm" 
              className="w-full justify-between hover:bg-muted/50"
              onPointerDown={(e) => e.stopPropagation()}
              onClick={(e) => e.stopPropagation()}
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
                  {workshop.expert ? (
                    <div className="text-muted-foreground">
                      <p>{workshop.expert}</p>
                      {workshop.expertEmail && (
                        <p className="text-xs">{workshop.expertEmail}</p>
                      )}
                      {workshop.expertPhone && (
                        <p className="text-xs">{workshop.expertPhone}</p>
                      )}
                    </div>
                  ) : (
                    <p className="text-muted-foreground italic">Még nincs szakértő</p>
                  )}
                </div>
              </div>

              {/* Duration */}
              {workshop.duration && (
                <div className="flex items-center gap-2">
                  <Clock className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="font-medium">Időtartam</p>
                    <p className="text-muted-foreground">{workshop.duration}</p>
                  </div>
                </div>
              )}

              {/* Participants */}
              {workshop.participants && (
                <div className="flex items-center gap-2">
                  <BookOpen className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="font-medium">Résztvevők</p>
                    <p className="text-muted-foreground">{workshop.participants} fő</p>
                  </div>
                </div>
              )}

              {/* Price */}
              <div className="flex items-center gap-2">
                <Euro className="h-4 w-4 text-muted-foreground" />
                <div>
                  <p className="font-medium">Ár</p>
                  {workshop.isFree ? (
                    <p className="text-muted-foreground">
                      Ingyenes {workshop.isGift && "(Ajándék)"}
                    </p>
                  ) : workshop.price ? (
                    <p className="text-muted-foreground">
                      {workshop.price.toLocaleString()} {workshop.currency}
                    </p>
                  ) : (
                    <p className="text-muted-foreground italic">Még nincs ár</p>
                  )}
                </div>
              </div>

              {/* Language */}
              {workshop.language && (
                <div className="flex items-center gap-2 pt-2 border-t">
                  <Badge variant="outline" className="text-xs">
                    {workshop.language.toUpperCase()}
                  </Badge>
                </div>
              )}
            </div>
          </CollapsibleContent>
        </Collapsible>
      </CardContent>
    </Card>
  );
};

const Workshops = () => {
  const [selectedCountry, setSelectedCountry] = useState("HU");
  const workshops = mockWorkshops[selectedCountry] || [];

  // Ensure at most one card is open at a time.
  const [openId, setOpenId] = useState<string | null>(null);

  const countByStatus = (status: WorkshopStatus) =>
    workshops.filter((w) => w.status === status).length;

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold">Workshopok</h2>
        <p className="text-muted-foreground">
          Workshop foglalások és résztvevők áttekintése
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

        {Object.keys(mockWorkshops).map((country) => (
          <TabsContent key={country} value={country} className="mt-6">
            {mockWorkshops[country].length > 0 ? (
              <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                {mockWorkshops[country].map((workshop) => (
                  <WorkshopCard
                    key={workshop.id}
                    workshop={workshop}
                    isOpen={openId === workshop.id}
                    onOpenChange={(open) => setOpenId(open ? workshop.id : null)}
                  />
                ))}
              </div>
            ) : (
              <Card className="border-dashed border-2">
                <CardContent className="py-12 text-center">
                  <p className="text-muted-foreground">
                    Nincsenek workshopok ebben az országban.
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

export default Workshops;
