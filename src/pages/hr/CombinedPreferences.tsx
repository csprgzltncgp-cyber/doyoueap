import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { ReportNavigation } from "@/components/navigation/ReportNavigation";
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

      {/* Tab selector és Felmérés kiválasztó egy sorban */}
      <div className="flex flex-col md:flex-row md:items-center gap-4 mb-6">
        <Tabs defaultValue="non-users" className="flex-1">
          <TabsList className="grid w-full max-w-2xl grid-cols-2 h-9">
            <TabsTrigger value="non-users" className="text-xs">Azok, akik eddig nem vették igénybe a programot</TabsTrigger>
            <TabsTrigger value="users" className="text-xs">A program aktív felhasználói</TabsTrigger>
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

        {audits.length > 0 && (
          <div className="w-full md:w-auto md:min-w-[280px]">
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
                    {formatAuditName(audit)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        )}
      </div>
    </div>
  );
};

export default CombinedPreferences;
