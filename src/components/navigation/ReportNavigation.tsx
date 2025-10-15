import { ChevronLeft, ChevronRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useNavigate } from "react-router-dom";

interface ReportNavigationProps {
  currentTab: string;
}

const REPORT_TABS = [
  { value: "overview", label: "Áttekintés" },
  { value: "awareness", label: "Ismertség" },
  { value: "trust", label: "Bizalom" },
  { value: "usage", label: "Használat" },
  { value: "impact", label: "Hatás" },
  { value: "motivation", label: "Motiváció" },
  { value: "categories", label: "Kategóriák" },
  { value: "demographics", label: "Demográfia" },
  { value: "trends", label: "Trendek" },
  { value: "compare", label: "Összehasonlítás" },
  { value: "methodology", label: "Módszertan" },
];

export const ReportNavigation = ({ currentTab }: ReportNavigationProps) => {
  const navigate = useNavigate();
  
  const currentIndex = REPORT_TABS.findIndex(tab => tab.value === currentTab);
  const previousTab = currentIndex > 0 ? REPORT_TABS[currentIndex - 1] : null;
  const nextTab = currentIndex < REPORT_TABS.length - 1 ? REPORT_TABS[currentIndex + 1] : null;

  const handleNavigate = (tabValue: string) => {
    navigate(`/hr/reports?section=reports&sub=${tabValue}`);
  };

  return (
    <div className="flex items-center gap-2">
      {previousTab && (
        <Button
          variant="outline"
          size="sm"
          onClick={() => handleNavigate(previousTab.value)}
          className="gap-1"
        >
          <ChevronLeft className="h-4 w-4" />
          <span className="hidden sm:inline">{previousTab.label}</span>
        </Button>
      )}
      {nextTab && (
        <Button
          variant="outline"
          size="sm"
          onClick={() => handleNavigate(nextTab.value)}
          className="gap-1"
        >
          <span className="hidden sm:inline">{nextTab.label}</span>
          <ChevronRight className="h-4 w-4" />
        </Button>
      )}
    </div>
  );
};
