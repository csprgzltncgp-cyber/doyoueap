import pptxgen from "pptxgenjs";
import html2canvas from "html2canvas";
import { toast } from "sonner";
import { exportableCharts } from "./exportUtils";

export const exportAllChartsToPPT = async (currentTab: string) => {
  try {
    toast.info("PPT generálás folyamatban...");
    
    // Filter charts for current tab
    const chartsToExport = exportableCharts.filter(chart => chart.tab === currentTab);
    
    if (chartsToExport.length === 0) {
      toast.error("Nincs exportálható diagram ezen a fülön");
      return;
    }

    const pptx = new pptxgen();
    
    // Add title slide
    const titleSlide = pptx.addSlide();
    titleSlide.addText("EAP Pulse Riport", {
      x: 0.5,
      y: 2,
      w: 9,
      h: 1,
      fontSize: 44,
      bold: true,
      color: "363636",
      align: "center",
    });
    
    titleSlide.addText(`${getTabName(currentTab)} - Részletes elemzés`, {
      x: 0.5,
      y: 3,
      w: 9,
      h: 0.5,
      fontSize: 24,
      color: "666666",
      align: "center",
    });

    // Generate images and add slides
    for (const chart of chartsToExport) {
      const element = document.getElementById(chart.id);
      
      if (!element) {
        console.warn(`Element not found: ${chart.id}`);
        continue;
      }

      try {
        // Capture chart as canvas
        const canvas = await html2canvas(element, {
          backgroundColor: '#ffffff',
          scale: 2,
          logging: false,
        });

        // Convert to base64
        const imageData = canvas.toDataURL('image/png');

        // Add slide with chart
        const slide = pptx.addSlide();
        
        // Add chart title
        slide.addText(chart.name, {
          x: 0.5,
          y: 0.3,
          w: 9,
          h: 0.5,
          fontSize: 20,
          bold: true,
          color: "363636",
        });

        // Add chart image
        slide.addImage({
          data: imageData,
          x: 0.5,
          y: 1,
          w: 9,
          h: 5.5,
        });
        
      } catch (error) {
        console.error(`Error capturing chart ${chart.id}:`, error);
      }
    }

    // Save the presentation
    const fileName = `EAP_Pulse_${getTabName(currentTab)}_${new Date().toISOString().split('T')[0]}.pptx`;
    await pptx.writeFile({ fileName });
    
    toast.success("PPT sikeresen létrehozva!");
  } catch (error) {
    console.error("Error creating PPT:", error);
    toast.error("Hiba a PPT létrehozása során");
  }
};

const getTabName = (tab: string): string => {
  const tabNames: Record<string, string> = {
    overview: "Áttekintés",
    awareness: "Ismertség",
    trust: "Bizalom",
    usage: "Használat",
    impact: "Hatás",
    motivation: "Motiváció",
    demographics: "Demográfia",
  };
  return tabNames[tab] || tab;
};
