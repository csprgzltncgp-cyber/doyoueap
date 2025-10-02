import { toast } from "sonner";

export const exportCardToPNG = async (cardId: string, fileName: string) => {
  try {
    const html2canvas = (await import('html2canvas')).default;
    const element = document.getElementById(cardId);
    
    if (!element) {
      toast.error('Panel nem található');
      return;
    }

    const canvas = await html2canvas(element, {
      backgroundColor: '#ffffff',
      scale: 2,
    });

    const link = document.createElement('a');
    link.download = `${fileName}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();

    toast.success('PNG sikeresen letöltve!');
  } catch (error) {
    console.error('Error exporting PNG:', error);
    toast.error('Hiba a PNG exportálás során');
  }
};

// Available chart cards for export
export const exportableCharts = [
  // Overview tab
  { id: 'utilization-card', name: 'Igénybevétel (Utilization)', tab: 'overview', fileName: 'igénybevétel' },
  { id: 'satisfaction-card', name: 'Elégedettségi Index', tab: 'overview', fileName: 'elégedettség' },
  { id: 'awareness-card', name: 'Ismertség', tab: 'overview', fileName: 'ismertség' },
  { id: 'trust-card', name: 'Bizalom', tab: 'overview', fileName: 'bizalom' },
  { id: 'usage-card', name: 'Használat', tab: 'overview', fileName: 'használat' },
  { id: 'impact-card', name: 'Hatás', tab: 'overview', fileName: 'hatás' },
  
  // Trust tab
  { id: 'trust-users-anonymity-card', name: 'Használók - Anonimitás', tab: 'trust', fileName: 'használók-anonimitás' },
  { id: 'trust-users-employer-card', name: 'Használók - Munkaadói félelem', tab: 'trust', fileName: 'használók-munkaadói-félelem' },
  { id: 'trust-users-likelihood-card', name: 'Használók - Hajlandóság', tab: 'trust', fileName: 'használók-hajlandóság' },
  { id: 'trust-non-users-card', name: 'Nem használók - Bizalmi indexek', tab: 'trust', fileName: 'nem-használók-bizalom' },
  { id: 'trust-nu-anonymity-card', name: 'Nem használók - Anonimitás', tab: 'trust', fileName: 'nem-használók-anonimitás' },
  { id: 'trust-nu-employer-card', name: 'Nem használók - Munkaadói félelem', tab: 'trust', fileName: 'nem-használók-munkaadói-félelem' },
  { id: 'trust-nu-colleagues-card', name: 'Nem használók - Kollégák', tab: 'trust', fileName: 'nem-használók-kollégák' },
  
  // Usage tab
  { id: 'usage-satisfaction-card', name: 'Használat - Elégedettség', tab: 'usage', fileName: 'használat-elégedettség' },
  { id: 'usage-problem-solving-card', name: 'Használat - Problémamegoldás', tab: 'usage', fileName: 'problémamegoldás' },
  { id: 'usage-nps-card', name: 'Használat - NPS', tab: 'usage', fileName: 'NPS' },
  { id: 'usage-consistency-card', name: 'Használat - Konzisztencia', tab: 'usage', fileName: 'konzisztencia' },
  
  // Impact tab
  { id: 'impact-performance-card', name: 'Hatás - Teljesítmény', tab: 'impact', fileName: 'teljesítmény-hatás' },
  { id: 'impact-problem-solving-card', name: 'Hatás - Problémamegoldás', tab: 'impact', fileName: 'hatás-problémamegoldás' },
  { id: 'impact-wellbeing-card', name: 'Hatás - Jóllét', tab: 'impact', fileName: 'jóllét-hatás' },
  { id: 'impact-satisfaction-card', name: 'Hatás - Elégedettség', tab: 'impact', fileName: 'hatás-elégedettség' },
  { id: 'impact-consistency-card', name: 'Hatás - Konzisztencia', tab: 'impact', fileName: 'szolgáltatás-konzisztencia' },
  
  // Motivation tab
  { id: 'motivation-what-card', name: 'Motiváció - Top motivátorok', tab: 'motivation', fileName: 'motivátorok' },
  { id: 'motivation-expert-card', name: 'Motiváció - Szakértő típus', tab: 'motivation', fileName: 'szakértő-típus' },
  { id: 'motivation-channel-card', name: 'Motiváció - Kommunikációs csatorna', tab: 'motivation', fileName: 'kommunikációs-csatorna' },
  
  // Demographics tab
  { id: 'demographics-gender-card', name: 'Demográfia - Nem', tab: 'demographics', fileName: 'nem-megoszlás' },
  { id: 'demographics-age-card', name: 'Demográfia - Korcsoport', tab: 'demographics', fileName: 'korcsoport-megoszlás' },
];
