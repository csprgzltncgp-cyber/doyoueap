import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ReportNavigation } from "@/components/navigation/ReportNavigation";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { formatAuditName } from "@/lib/auditUtils";
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
}

const CombinedPreferences = ({ selectedAuditId, audits, onAuditChange }: CombinedPreferencesProps) => {
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
      </div>

      {/* Felmérés kiválasztása */}
      <div className="flex flex-col gap-4">
        <Select value={selectedAuditId} onValueChange={onAuditChange}>
          <SelectTrigger className="w-full md:w-80">
            <SelectValue placeholder="Válasszon felmérést" />
          </SelectTrigger>
          <SelectContent>
            {audits.map((audit) => (
              <SelectItem key={audit.id} value={audit.id}>
                {formatAuditName(audit)}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>

        {/* Tabok a dropdown alatt */}
        <Tabs defaultValue="non-users" className="w-full">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="non-users">Azok, akik eddig nem vették igénybe a programot</TabsTrigger>
            <TabsTrigger value="users">A program aktív felhasználói</TabsTrigger>
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
    </div>
  );
};

export default CombinedPreferences;
