import pptxgen from "pptxgenjs";
import { toast } from "sonner";
import { exportableCharts } from "./exportUtils";

// Helper to generate PNG from chart using iframe method
const generateChartPNG = (chartId: string, tab: string): Promise<string> => {
  return new Promise((resolve, reject) => {
    const iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.visibility = 'hidden';
    
    const handleMessage = (event: MessageEvent) => {
      if (event.data.type === 'EXPORT_COMPLETE') {
        window.removeEventListener('message', handleMessage);
        if (iframe.parentNode) {
          iframe.parentNode.removeChild(iframe);
        }
        resolve(event.data.imageData);
      } else if (event.data.type === 'EXPORT_ERROR') {
        window.removeEventListener('message', handleMessage);
        if (iframe.parentNode) {
          iframe.parentNode.removeChild(iframe);
        }
        reject(new Error(event.data.error));
      }
    };
    
    window.addEventListener('message', handleMessage);
    
    iframe.src = `/hr/reports?sub=${tab}&autoExport=${chartId}&fileName=temp&inIframe=true`;
    document.body.appendChild(iframe);
    
    // Timeout after 10 seconds
    setTimeout(() => {
      window.removeEventListener('message', handleMessage);
      if (iframe.parentNode) {
        iframe.parentNode.removeChild(iframe);
      }
      reject(new Error('Timeout waiting for chart export'));
    }, 10000);
  });
};

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

    // Generate PNG images for each chart
    toast.info("Diagramok generálása...");
    const chartImages: { name: string; imageData: string }[] = [];
    
    for (let i = 0; i < chartsToExport.length; i++) {
      const chart = chartsToExport[i];
      toast.info(`Diagram ${i + 1}/${chartsToExport.length} generálása...`);
      
      try {
        const imageData = await generateChartPNG(chart.id, chart.tab);
        chartImages.push({
          name: chart.name,
          imageData: imageData
        });
      } catch (error) {
        console.error(`Error generating chart ${chart.id}:`, error);
        toast.error(`Hiba a(z) "${chart.name}" diagram generálásánál`);
      }
    }

    // Add slides with generated images
    toast.info("PPT összeállítása...");
    for (const chartImage of chartImages) {
      const slide = pptx.addSlide();
      
      // Add chart title
      slide.addText(chartImage.name, {
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
        data: chartImage.imageData,
        x: 0.5,
        y: 1,
        w: 9,
        h: 5.5,
      });
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
