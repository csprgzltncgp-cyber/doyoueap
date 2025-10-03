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
  { id: 'participation-card', name: 'Részvételi arány', tab: 'overview', fileName: 'részvétel' },
  { id: 'satisfaction-metrics-card', name: 'Elégedettségi mutatók', tab: 'overview', fileName: 'elégedettségi-mutatók' },
  { id: 'awareness-card', name: 'Ismertség', tab: 'overview', fileName: 'ismertség' },
  { id: 'trust-card', name: 'Bizalom', tab: 'overview', fileName: 'bizalom' },
  { id: 'usage-card', name: 'Használat', tab: 'overview', fileName: 'használat' },
  { id: 'impact-card', name: 'Hatás', tab: 'overview', fileName: 'hatás' },
  
  // Awareness tab
  { id: 'overall-awareness-card', name: 'Awareness - Általános', tab: 'awareness', fileName: 'awareness-altalanos' },
  { id: 'understanding-card', name: 'Awareness - Megértés', tab: 'awareness', fileName: 'awareness-megértes' },
  { id: 'overall-knowledge-card', name: 'Awareness - Általános tudásszint', tab: 'awareness', fileName: 'awareness-tudasszint' },
  { id: 'unawareness-card', name: 'Awareness - Nem tudtak róla', tab: 'awareness', fileName: 'nem-tudtak-rola' },
  { id: 'sources-card', name: 'Awareness - Források', tab: 'awareness', fileName: 'awareness-forrasok' },
  { id: 'frequency-card', name: 'Awareness - Gyakoriság', tab: 'awareness', fileName: 'awareness-gyakorisag' },
  { id: 'info-sufficiency-card', name: 'Awareness - Info elégségesség', tab: 'awareness', fileName: 'info-elegsegesség' },
  { id: 'comparison-card', name: 'Awareness - Összehasonlítás', tab: 'awareness', fileName: 'awareness-osszehasonlitas' },
  { id: 'awareness-profile-card', name: 'Awareness - Profil', tab: 'awareness', fileName: 'awareness-profil' },
  
  // Trust tab
  { id: 'overall-anonymity-card', name: 'Bizalom - Általános anonimitás', tab: 'trust', fileName: 'anonimitas-altalanos' },
  { id: 'trust-index-card', name: 'Bizalom - Bizalmi index', tab: 'trust', fileName: 'bizalmi-index' },
  { id: 'employer-fear-card', name: 'Bizalom - Munkaadói félelem', tab: 'trust', fileName: 'munkaadoi-felelem' },
  { id: 'likelihood-card', name: 'Bizalom - Hajlandóság', tab: 'trust', fileName: 'hajlandosag' },
  { id: 'trust-radar-card', name: 'Bizalom - Radar használók', tab: 'trust', fileName: 'bizalom-radar-hasznalok' },
  { id: 'barriers-card', name: 'Bizalom - Akadályok', tab: 'trust', fileName: 'akadalyok' },
  { id: 'anonymity-comparison-card', name: 'Bizalom - Anonimitás összehasonlítás', tab: 'trust', fileName: 'anonimitas-osszehasonlitas' },
  { id: 'employer-fear-comparison-card', name: 'Bizalom - Munkaadói félelem összehasonlítás', tab: 'trust', fileName: 'munkaadoi-felelem-osszehasonlitas' },
  { id: 'colleagues-fear-comparison-card', name: 'Bizalom - Kollégák félelem', tab: 'trust', fileName: 'kollegak-felelem' },
  { id: 'trust-profile-card', name: 'Bizalom - Profil nem használók', tab: 'trust', fileName: 'bizalom-profil-nem-hasznalok' },
  
  // Usage tab
  { id: 'usage-rate-card', name: 'Használat - Használati arány', tab: 'usage', fileName: 'hasznalati-arany' },
  { id: 'family-usage-card', name: 'Használat - Családi használat', tab: 'usage', fileName: 'csaladi-hasznalat' },
  { id: 'top-topic-card', name: 'Használat - Top témakör', tab: 'usage', fileName: 'top-temakor' },
  { id: 'top-channel-card', name: 'Használat - Top csatorna', tab: 'usage', fileName: 'top-csatorna' },
  { id: 'frequency-card', name: 'Használat - Gyakoriság', tab: 'usage', fileName: 'hasznalat-gyakorisag' },
  { id: 'family-distribution-card', name: 'Használat - Családi megoszlás', tab: 'usage', fileName: 'csaladi-megoszlas' },
  { id: 'topics-card', name: 'Használat - Témakörök', tab: 'usage', fileName: 'temakorok' },
  { id: 'channels-card', name: 'Használat - Csatornák', tab: 'usage', fileName: 'csatornak' },
  { id: 'time-to-care-card', name: 'Használat - Időtartam gondoskodásig', tab: 'usage', fileName: 'idotartam-gondoskodas' },
  { id: 'usage-intensity-card', name: 'Használat - Intenzitás', tab: 'usage', fileName: 'hasznalat-intenzitas' },
  
  // Impact tab
  { id: 'impact-nps-card', name: 'Hatás - NPS', tab: 'impact', fileName: 'hatas-nps' },
  { id: 'impact-avg-card', name: 'Hatás - Átlagos hatás', tab: 'impact', fileName: 'atlagos-hatas' },
  { id: 'impact-metrics-card', name: 'Hatás - Mutatók', tab: 'impact', fileName: 'hatas-mutatok' },
  { id: 'impact-radar-card', name: 'Hatás - Radar', tab: 'impact', fileName: 'hatas-radar' },
  
  // Motivation tab
  { id: 'motivators-card', name: 'Motiváció - Top motivátorok', tab: 'motivation', fileName: 'motivatorok' },
  { id: 'expert-preference-card', name: 'Motiváció - Szakértő típus', tab: 'motivation', fileName: 'szakerto-tipus' },
  { id: 'channel-preference-card', name: 'Motiváció - Kommunikációs csatorna', tab: 'motivation', fileName: 'kommunikacios-csatorna' },
  
  // Demographics tab
  { id: 'category-distribution-card', name: 'Demográfia - Kategória megoszlás', tab: 'demographics', fileName: 'kategoria-megoszlas' },
  { id: 'comparison-chart-card', name: 'Demográfia - Összehasonlítás', tab: 'demographics', fileName: 'demografia-osszehasonlitas' },
  { id: 'gender-distribution-card', name: 'Demográfia - Nem megoszlás', tab: 'demographics', fileName: 'nem-megoszlas' },
  { id: 'age-distribution-card', name: 'Demográfia - Korcsoport', tab: 'demographics', fileName: 'korcsoport-megoszlas' },
];
