import { useState } from 'react';
import { Menu, Target, BarChart3, TrendingUp, Download, FileEdit, Settings as SettingsIcon, Trophy, Code, Building2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { usePackage } from '@/hooks/usePackage';

interface MobileDashboardNavProps {
  section: string;
  subSection: string;
  onNavigate: (section: string, sub?: string) => void;
}

export function MobileDashboardNav({ section, subSection, onNavigate }: MobileDashboardNavProps) {
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

          <Accordion type="single" collapsible defaultValue={section === 'reports' ? 'reports' : undefined}>
            <AccordionItem value="reports" className="border-none">
              <AccordionTrigger 
                className={`px-4 py-3 rounded-md hover:no-underline ${
                  section === 'reports' 
                    ? 'bg-[#3572ef] text-white' 
                    : 'hover:bg-muted'
                }`}
              >
                <div className="flex items-center gap-3">
                  <TrendingUp className="h-4 w-4" />
                  <span>Riportok</span>
                </div>
              </AccordionTrigger>
              <AccordionContent className="pb-0 pt-2 pl-4">
                <div className="flex flex-col gap-1">
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
                    onClick={() => handleNavClick('reports', 'awareness')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'awareness' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Ismertség
                  </button>
                  <button
                    onClick={() => handleNavClick('reports', 'trust')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'trust' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Bizalom
                  </button>
                  <button
                    onClick={() => handleNavClick('reports', 'usage')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'usage' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Használat
                  </button>
                  <button
                    onClick={() => handleNavClick('reports', 'impact')}
                    className={`px-4 py-2 rounded-md text-left text-sm ${
                      subSection === 'impact' 
                        ? 'bg-muted font-medium' 
                        : 'hover:bg-muted/50'
                    }`}
                  >
                    Hatás
                  </button>
                  {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                    <>
                      <button
                        onClick={() => handleNavClick('reports', 'preferences')}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          subSection === 'preferences' 
                            ? 'bg-muted font-medium' 
                            : 'hover:bg-muted/50'
                        }`}
                      >
                        Preferenciák
                      </button>
                      <button
                        onClick={() => handleNavClick('reports', 'demographics')}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          subSection === 'demographics' 
                            ? 'bg-muted font-medium' 
                            : 'hover:bg-muted/50'
                        }`}
                      >
                        Demográfia
                      </button>
                      <button
                        onClick={() => handleNavClick('reports', 'trends')}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          subSection === 'trends' 
                            ? 'bg-muted font-medium' 
                            : 'hover:bg-muted/50'
                        }`}
                      >
                        Trendek
                      </button>
                      <button
                        onClick={() => handleNavClick('reports', 'compare')}
                        className={`px-4 py-2 rounded-md text-left text-sm ${
                          subSection === 'compare' 
                            ? 'bg-muted font-medium' 
                            : 'hover:bg-muted/50'
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

          {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
            <button
              onClick={() => handleNavClick('custom-survey')}
              className={`flex items-center gap-3 px-4 py-3 rounded-md transition-colors text-left ${
                section === 'custom-survey' 
                  ? 'bg-[#3572ef] text-white font-semibold' 
                  : 'hover:bg-muted'
              }`}
            >
              <FileEdit className="h-4 w-4" />
              <span>Egyedi Felmérés</span>
            </button>
          )}

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
