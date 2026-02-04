import { useState } from 'react';
import { Menu, Target, BarChart3, TrendingUp, Download, FileEdit, Settings as SettingsIcon, Trophy, Code, Building2, Map, Calendar, AlertTriangle, Gift, ThumbsUp, PieChart, Upload } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { usePackage } from '@/hooks/usePackage';

interface MobileDashboardNavProps {
  section: string;
  subSection: string;
  onNavigate: (section: string, sub?: string) => void;
  hasAudits?: boolean;
}

export function MobileDashboardNav({ section, subSection, onNavigate, hasAudits = true }: MobileDashboardNavProps) {
  const { packageType } = usePackage();
  const [open, setOpen] = useState(false);

  const handleNavClick = (newSection: string, newSub?: string) => {
    onNavigate(newSection, newSub);
    setOpen(false);
  };

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button variant="ghost" className="md:hidden text-white hover:text-white hover:bg-white/20">
          Dashboard Menü
        </Button>
      </SheetTrigger>
      <SheetContent side="left" className="w-[320px] overflow-y-auto">
        <SheetHeader>
          <SheetTitle>Dashboard Menü</SheetTitle>
        </SheetHeader>
        <nav className="flex flex-col gap-2 mt-6">
          <button
            onClick={() => handleNavClick('focus')}
            className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
              section === 'focus' 
                ? 'bg-[#3572ef] text-white font-semibold' 
                : 'hover:bg-muted'
            }`}
          >
            <Target className="h-4 w-4" />
            <span>Focus</span>
          </button>

          <Accordion type="single" collapsible defaultValue={section === 'eap-pulse' ? 'eap-pulse' : undefined}>
            <AccordionItem value="eap-pulse" className="border-none">
              <AccordionTrigger 
                className={`px-4 py-3 rounded-md hover:no-underline ${
                  section === 'eap-pulse' 
                    ? 'bg-[#3572ef] text-white' 
                    : 'hover:bg-muted'
                }`}
              >
                <div className="flex items-center gap-3">
                  <BarChart3 className="h-4 w-4" />
                  <span>EAP Pulse</span>
                </div>
              </AccordionTrigger>
              <AccordionContent className="pb-0 pt-2 pl-4">
                <div className="flex flex-col gap-1">
                  <button
                    onClick={() => handleNavClick('eap-pulse', 'step-by-step')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'step-by-step' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Lépésről lépésre
                  </button>
                  <button
                    onClick={() => handleNavClick('eap-pulse', 'communication-support')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'communication-support' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Kommunikációs támogatás
                  </button>
                  <button
                    onClick={() => handleNavClick('eap-pulse', 'audit-questionnaire')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'audit-questionnaire' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    EAP Pulse demo
                  </button>
                  <button
                    onClick={() => handleNavClick('eap-pulse', 'create-audit')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'create-audit' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Új Felmérés
                  </button>
                  <button
                    onClick={() => handleNavClick('eap-pulse', 'raffles')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'raffles' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Sorsolások
                  </button>
                  <button
                    onClick={() => handleNavClick('eap-pulse', 'gifts')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'gifts' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Ajándékok
                  </button>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>

          <Accordion type="single" collapsible defaultValue={
            section === 'reports' || section === 'health-map' || section === 'satisfaction' || section === 'program-usage' 
              ? 'statistics' 
              : undefined
          }>
            <AccordionItem value="statistics" className="border-none">
              <AccordionTrigger 
                className={`px-4 py-3 rounded-md hover:no-underline ${
                  section === 'reports' || section === 'health-map' || section === 'satisfaction' || section === 'program-usage'
                    ? 'bg-[#3572ef] text-white' 
                    : 'hover:bg-muted'
                }`}
              >
                <div className="flex items-center gap-3">
                  <TrendingUp className="h-4 w-4" />
                  <span>Statisztikák</span>
                </div>
              </AccordionTrigger>
              <AccordionContent className="pb-0 pt-2 pl-4">
                <div className="flex flex-col gap-1">
                  {/* Program Riportok Section */}
                  <div className="px-2 py-1.5 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                    Program riportok
                  </div>
                  <button
                    onClick={() => handleNavClick('reports', 'program')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'program'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Negyedéves riport
                  </button>
                  <button
                    onClick={() => handleNavClick('health-map')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      section === 'health-map'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Egészség Térkép
                  </button>
                  <button
                    onClick={() => handleNavClick('satisfaction')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      section === 'satisfaction'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Elégedettségi Index
                  </button>
                  <button
                    onClick={() => handleNavClick('program-usage')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      section === 'program-usage'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Program Használat
                  </button>

                  {/* EAP Pulse Riportok Section */}
                  <div className="px-2 py-1.5 mt-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider border-t border-muted pt-3">
                    EAP Pulse riportok
                  </div>
                  <button
                    onClick={() => handleNavClick('reports', 'overview')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      (!subSection || subSection === 'overview') 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Összefoglaló
                  </button>
                  <button
                    onClick={() => hasAudits && handleNavClick('reports', 'awareness')}
                    disabled={!hasAudits}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      hasAudits 
                        ? 'hover:bg-muted/50 cursor-pointer' 
                        : 'opacity-40 cursor-not-allowed'
                    } ${
                      subSection === 'awareness' ? 'bg-muted font-medium' : ''
                    }`}
                  >
                    Ismertség
                  </button>
                  <button
                    onClick={() => hasAudits && handleNavClick('reports', 'trust')}
                    disabled={!hasAudits}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      hasAudits 
                        ? 'hover:bg-muted/50 cursor-pointer' 
                        : 'opacity-40 cursor-not-allowed'
                    } ${
                      subSection === 'trust' ? 'bg-muted font-medium' : ''
                    }`}
                  >
                    Bizalom
                  </button>
                  <button
                    onClick={() => hasAudits && handleNavClick('reports', 'usage')}
                    disabled={!hasAudits}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      hasAudits 
                        ? 'hover:bg-muted/50 cursor-pointer' 
                        : 'opacity-40 cursor-not-allowed'
                    } ${
                      subSection === 'usage' ? 'bg-muted font-medium' : ''
                    }`}
                  >
                    Használat
                  </button>
                  <button
                    onClick={() => hasAudits && handleNavClick('reports', 'impact')}
                    disabled={!hasAudits}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      hasAudits 
                        ? 'hover:bg-muted/50 cursor-pointer' 
                        : 'opacity-40 cursor-not-allowed'
                    } ${
                      subSection === 'impact' ? 'bg-muted font-medium' : ''
                    }`}
                  >
                    Hatás
                  </button>
                  {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                    <>
                      <button
                        onClick={() => hasAudits && handleNavClick('reports', 'preferences')}
                        disabled={!hasAudits}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          hasAudits 
                            ? 'hover:bg-muted/50 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
                          subSection === 'preferences' ? 'bg-muted font-medium' : ''
                        }`}
                      >
                        Preferenciák
                      </button>
                      <button
                        onClick={() => hasAudits && handleNavClick('reports', 'demographics')}
                        disabled={!hasAudits}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          hasAudits 
                            ? 'hover:bg-muted/50 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
                          subSection === 'demographics' ? 'bg-muted font-medium' : ''
                        }`}
                      >
                        Demográfia
                      </button>
                      <button
                        onClick={() => hasAudits && handleNavClick('reports', 'trends')}
                        disabled={!hasAudits}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          hasAudits 
                            ? 'hover:bg-muted/50 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
                          subSection === 'trends' ? 'bg-muted font-medium' : ''
                        }`}
                      >
                        Trendek
                      </button>
                      <button
                        onClick={() => hasAudits && handleNavClick('reports', 'compare')}
                        disabled={!hasAudits}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          hasAudits 
                            ? 'hover:bg-muted/50 cursor-pointer' 
                            : 'opacity-40 cursor-not-allowed'
                        } ${
                          subSection === 'compare' ? 'bg-muted font-medium' : ''
                        }`}
                      >
                        Összehasonlítás
                      </button>
                    </>
                  )}
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>

          <Accordion type="single" collapsible defaultValue={
            section === 'workshops' || section === 'crisis' || section === 'lottery' 
              ? 'events' 
              : undefined
          }>
            <AccordionItem value="events" className="border-none">
              <AccordionTrigger 
                className={`px-4 py-3 rounded-md hover:no-underline ${
                  section === 'workshops' || section === 'crisis' || section === 'lottery'
                    ? 'bg-[#3572ef] text-white' 
                    : 'hover:bg-muted'
                }`}
              >
                <div className="flex items-center gap-3">
                  <Calendar className="h-4 w-4" />
                  <span>Események</span>
                </div>
              </AccordionTrigger>
              <AccordionContent className="pb-0 pt-2 pl-4">
                <div className="flex flex-col gap-1">
                  <button
                    onClick={() => handleNavClick('workshops')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      section === 'workshops'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Workshopok
                  </button>
                  <button
                    onClick={() => handleNavClick('crisis')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      section === 'crisis'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Krízisintervenciók
                  </button>
                  <button
                    onClick={() => handleNavClick('lottery')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      section === 'lottery'
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Nyereményjáték
                  </button>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>

          <button
            onClick={() => handleNavClick('export')}
            className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
              section === 'export' 
                ? 'bg-[#3572ef] text-white font-semibold' 
                : 'hover:bg-muted'
            }`}
          >
            <Download className="h-4 w-4" />
            <span>Export</span>
          </button>

          <button
            onClick={() => handleNavClick('data-submission')}
            className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
              section === 'data-submission' 
                ? 'bg-[#3572ef] text-white font-semibold' 
                : 'hover:bg-muted'
            }`}
          >
            <Upload className="h-4 w-4" />
            <span>Adatok Küldése</span>
          </button>

          {packageType === 'partner' && (
            <>
              <button
                onClick={() => handleNavClick('api')}
                className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
                  section === 'api' 
                    ? 'bg-[#3572ef] text-white font-semibold' 
                    : 'hover:bg-muted'
                }`}
              >
                <Code className="h-4 w-4" />
                <span>API</span>
              </button>

              <button
                onClick={() => handleNavClick('partner-center')}
                className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
                  section === 'partner-center' 
                    ? 'bg-[#3572ef] text-white font-semibold' 
                    : 'hover:bg-muted'
                }`}
              >
                <Building2 className="h-4 w-4" />
                <span>Partner Központ</span>
              </button>
            </>
          )}

          {packageType === 'enterprise' && (
            <button
              onClick={() => handleNavClick('api')}
              className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
                section === 'api' 
                  ? 'bg-[#3572ef] text-white font-semibold' 
                  : 'hover:bg-muted'
              }`}
            >
              <Code className="h-4 w-4" />
              <span>API</span>
            </button>
          )}

          <button
            onClick={() => handleNavClick('settings')}
            className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
              section === 'settings' 
                ? 'bg-[#3572ef] text-white font-semibold' 
                : 'hover:bg-muted'
            }`}
          >
            <SettingsIcon className="h-4 w-4" />
            <span>Beállítások</span>
          </button>
        </nav>
      </SheetContent>
    </Sheet>
  );
}
