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

// Available chart cards for export - SYNCHRONIZED WITH REPORTS PAGES
export const exportableCharts = [
  // Overview tab (from Reports.tsx)
  { id: 'awareness-card', name: 'Ismertség', tab: 'overview', fileName: 'ismertség' },
  { id: 'trust-card', name: 'Bizalom', tab: 'overview', fileName: 'bizalom' },
  { id: 'usage-card', name: 'Használat', tab: 'overview', fileName: 'használat' },
  { id: 'impact-card', name: 'Hatás', tab: 'overview', fileName: 'hatás' },
  { id: 'utilization-card', name: 'Igénybevétel', tab: 'overview', fileName: 'igénybevétel' },
  { id: 'satisfaction-card', name: 'Elégedettségi Index', tab: 'overview', fileName: 'elégedettség' },
  { id: 'participation-card', name: 'Részvételi arány', tab: 'overview', fileName: 'részvétel' },
  { id: 'satisfaction-metrics-card', name: 'Elégedettségi mutatók', tab: 'overview', fileName: 'elégedettségi-mutatók' },
  
  // Awareness tab (from Awareness.tsx)
  { id: 'overall-awareness-card', name: 'Ismertség - Általános', tab: 'awareness', fileName: 'ismertség-altalanos' },
  { id: 'understanding-card', name: 'Ismertség - Általános megértés szintje', tab: 'awareness', fileName: 'megértés-szintje' },
  { id: 'overall-knowledge-card', name: 'Ismertség - Általános tudásszint', tab: 'awareness', fileName: 'tudasszint' },
  { id: 'unawareness-card', name: 'Ismertség - Nem tudtak róla', tab: 'awareness', fileName: 'nem-tudtak-rola' },
  { id: 'sources-card', name: 'Ismertség - Információs források', tab: 'awareness', fileName: 'forrasok' },
  { id: 'frequency-card', name: 'Ismertség - Kommunikációs gyakoriság', tab: 'awareness', fileName: 'gyakorisag' },
  { id: 'info-sufficiency-card', name: 'Ismertség - Információ elégségesség', tab: 'awareness', fileName: 'info-elegsegesség' },
  { id: 'comparison-card', name: 'Ismertség - Összehasonlítás', tab: 'awareness', fileName: 'osszehasonlitas' },
  { id: 'awareness-profile-card', name: 'Ismertség - Profil', tab: 'awareness', fileName: 'profil' },
  
  // Trust tab (from TrustWillingness.tsx)
  { id: 'trust-index-card', name: 'Bizalom - Bizalmi index', tab: 'trust', fileName: 'bizalmi-index' },
  { id: 'likelihood-card', name: 'Bizalom - Jövőbeli használat', tab: 'trust', fileName: 'jovobeli-hasznalat' },
  { id: 'overall-anonymity-card', name: 'Bizalom - Anonimitás bizalom', tab: 'trust', fileName: 'anonimitas-bizalom' },
  { id: 'employer-fear-card', name: 'Bizalom - Munkaadói félelem', tab: 'trust', fileName: 'munkaadoi-felelem' },
  { id: 'trust-radar-card', name: 'Bizalom - Bizalmi dimenziók radar', tab: 'trust', fileName: 'radar-hasznalok' },
  { id: 'barriers-card', name: 'Bizalom - Bizalmi akadályok', tab: 'trust', fileName: 'akadalyok' },
  { id: 'anonymity-comparison-card', name: 'Bizalom - Anonimitás összehasonlítás', tab: 'trust', fileName: 'anonimitas-osszehasonlitas' },
  { id: 'employer-fear-comparison-card', name: 'Bizalom - Munkaadói félelem összehasonlítás', tab: 'trust', fileName: 'munkaadoi-felelem-osszehasonlitas' },
  { id: 'colleagues-fear-comparison-card', name: 'Bizalom - Kollégák félelme', tab: 'trust', fileName: 'kollegak-felelme' },
  { id: 'trust-barriers-difficulties-card', name: 'Bizalom - Nehézségek', tab: 'trust', fileName: 'nehezsegek' },
  { id: 'trust-profile-card', name: 'Bizalom - Profil nem használók', tab: 'trust', fileName: 'profil-nem-hasznalok' },
  
  // Usage tab (from Usage.tsx)
  { id: 'would-use-future-card', name: 'Használat - Használati index', tab: 'usage', fileName: 'hasznalati-index' },
  { id: 'plan-to-use-card', name: 'Használat - Közeljövőbeni tervek', tab: 'usage', fileName: 'kozeljovo-tervek' },
  { id: 'usage-rate-card', name: 'Használat - Használók aránya', tab: 'usage', fileName: 'hasznalok-aranya' },
  { id: 'family-usage-card', name: 'Használat - Családi használat', tab: 'usage', fileName: 'csaladi-hasznalat' },
  { id: 'top-topic-card', name: 'Használat - Top témakör', tab: 'usage', fileName: 'top-temakor' },
  { id: 'top-channel-card', name: 'Használat - Top csatorna', tab: 'usage', fileName: 'top-csatorna' },
  { id: 'frequency-card', name: 'Használat - Gyakoriság', tab: 'usage', fileName: 'hasznalat-gyakorisag' },
  { id: 'family-distribution-card', name: 'Használat - Családi megoszlás', tab: 'usage', fileName: 'csaladi-megoszlas' },
  { id: 'topics-card', name: 'Használat - Témakörök', tab: 'usage', fileName: 'temakorok' },
  { id: 'channels-card', name: 'Használat - Csatornák', tab: 'usage', fileName: 'csatornak' },
  { id: 'time-to-care-card', name: 'Használat - Időtartam gondoskodásig', tab: 'usage', fileName: 'idotartam-gondoskodas' },
  { id: 'usage-intensity-card', name: 'Használat - Intenzitás profil', tab: 'usage', fileName: 'intenzitas-profil' },
  
  // Impact tab (from Impact.tsx)
  { id: 'impact-avg-card', name: 'Hatás - Átlagos hatás', tab: 'impact', fileName: 'atlagos-hatas' },
  { id: 'impact-nps-card', name: 'Hatás - NPS', tab: 'impact', fileName: 'hatas-nps' },
  { id: 'impact-metrics-card', name: 'Hatás - Mutatók', tab: 'impact', fileName: 'hatas-mutatok' },
  { id: 'impact-radar-card', name: 'Hatás - Radar', tab: 'impact', fileName: 'hatas-radar' },
  
  // Motivation tab (non-users preferences from Motivation.tsx)
  { id: 'motivators-card', name: 'Motiváció - Top motivátorok', tab: 'motivation', fileName: 'motivatorok' },
  { id: 'expert-preference-card', name: 'Motiváció - Szakértő típus', tab: 'motivation', fileName: 'szakerto-tipus' },
  { id: 'channel-preference-card', name: 'Motiváció - Kommunikációs csatorna', tab: 'motivation', fileName: 'kommunikacios-csatorna' },
  
  // Preferences tab (users preferences from Preferences.tsx)
  { id: 'expert-preference-card', name: 'Preferenciák - Szakértő típus', tab: 'preferences', fileName: 'preferenciák-szakerto' },
  { id: 'channel-preference-card', name: 'Preferenciák - Kommunikációs csatorna', tab: 'preferences', fileName: 'preferenciák-csatorna' },
  { id: 'availability-preference-card', name: 'Preferenciák - Elérhetőség', tab: 'preferences', fileName: 'preferenciák-elerhetoseg' },
  { id: 'content-type-preference-card', name: 'Preferenciák - Tartalomtípus', tab: 'preferences', fileName: 'preferenciák-tartalomtipus' },
  { id: 'comm-frequency-card', name: 'Preferenciák - Kommunikációs gyakoriság', tab: 'preferences', fileName: 'preferenciák-komm-gyakorisag' },
  
  // Demographics tab (from Demographics.tsx)
  { id: 'category-distribution-card', name: 'Demográfia - Kategória megoszlás', tab: 'demographics', fileName: 'kategoria-megoszlas' },
  { id: 'comparison-chart-card', name: 'Demográfia - Összehasonlítás', tab: 'demographics', fileName: 'demografia-osszehasonlitas' },
  { id: 'gender-distribution-card', name: 'Demográfia - Nem megoszlás', tab: 'demographics', fileName: 'nem-megoszlas' },
  { id: 'age-distribution-card', name: 'Demográfia - Korcsoport', tab: 'demographics', fileName: 'korcsoport-megoszlas' },
];
