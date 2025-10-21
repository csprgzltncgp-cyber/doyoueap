import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ReportNavigation } from "@/components/navigation/ReportNavigation";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { formatAuditName } from "@/lib/auditUtils";
import { useIsMobile } from "@/hooks/use-mobile";
import Motivation from "./Motivation";
import Preferences from "./Preferences";

interface CombinedPreferencesProps {
  selectedAuditId: string;
  audits: Array<{
    id: string;
    start_date: string;
    program_name: string;
    access_mode: string;
    recurrence_config: any;
    is_active: boolean;
    expires_at: string | null;
  }>;
  onAuditChange: (id: string) => void;
  packageType?: string;
  companies?: Array<{ id: string; company_name: string }>;
  selectedCompanyId?: string;
  onCompanyChange?: (id: string) => void;
}

const CombinedPreferences = ({ selectedAuditId, audits, onAuditChange, packageType, companies = [], selectedCompanyId, onCompanyChange }: CombinedPreferencesProps) => {
  const isMobile = useIsMobile();
  
  const formatAuditNameForMobile = (audit: any) => {
    const date = new Date(audit.start_date).toLocaleDateString('hu-HU', {
      month: 'short',
      day: 'numeric'
    });
    return `${date} - ${audit.program_name}`;
  };

  return (
    <div className="space-y-6">
      {/* Fejléc */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <div className="flex items-center gap-4 mb-2">
              <h2 className="text-2xl font-bold">Preferenciák Elemzése</h2>
              <ReportNavigation currentTab="preferences" />
            </div>
            <p className="text-muted-foreground text-sm">
              Használók és nem használók szolgáltatás preferenciáinak és kommunikációs igényeinek részletes elemzése
            </p>
          </div>
        </div>
        {audits.length > 0 && (
          <div className="flex-1 md:max-w-[300px] md:ml-auto">
            <label className="text-xs text-muted-foreground mb-1.5 block">
              Felmérés kiválasztása
            </label>
            <Select value={selectedAuditId} onValueChange={onAuditChange}>
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Válassz felmérést" />
              </SelectTrigger>
              <SelectContent>
                {audits.map((audit) => (
                  <SelectItem key={audit.id} value={audit.id}>
                    {isMobile ? formatAuditNameForMobile(audit) : formatAuditName(audit)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        )}
      </div>

      {/* Tabok */}
      <Tabs defaultValue="non-users" className="w-full">
        <TabsList className="grid w-full grid-cols-2">
          <TabsTrigger value="non-users">
            {isMobile ? "Nem használók" : "Azok, akik eddig nem vették igénybe a programot"}
          </TabsTrigger>
          <TabsTrigger value="users">
            {isMobile ? "Aktív használók" : "A program aktív felhasználói"}
          </TabsTrigger>
        </TabsList>
        
        <TabsContent value="non-users" className="mt-6">
          <Motivation 
            selectedAuditId={selectedAuditId} 
            audits={audits} 
            onAuditChange={onAuditChange}
          />
        </TabsContent>
        
        <TabsContent value="users" className="mt-6">
          <Preferences 
            selectedAuditId={selectedAuditId} 
            audits={audits} 
            onAuditChange={onAuditChange}
          />
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default CombinedPreferences;
