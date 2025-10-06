import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { exportableCharts } from '@/lib/exportUtils';
import { Presentation, Image as ImageIcon, Download, FileSpreadsheet } from 'lucide-react';

let exportIframe: HTMLIFrameElement | null = null;

// NOTE: "Audit" in code represents "Felmérés" (EAP Pulse Survey) in the UI
interface Audit {
  id: string;
  start_date: string;
  program_name: string;
  access_mode: string;
  recurrence_config: any;
  is_active: boolean;
  expires_at: string | null;
}

const Export = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [exporting, setExporting] = useState(false);

  useEffect(() => {
    fetchAudits();

    // Listen for messages from the iframe
    const handleMessage = (event: MessageEvent) => {
      if (event.data.type === 'EXPORT_COMPLETE') {
        setExporting(false);
        
        // Download the image from the data URL
        const { imageData, fileName } = event.data;
        const link = document.createElement('a');
        link.download = `${fileName}.png`;
        link.href = imageData;
        link.click();
        
        toast.success('PNG sikeresen letöltve!');
        
        // Remove the iframe
        if (exportIframe && exportIframe.parentNode) {
          exportIframe.parentNode.removeChild(exportIframe);
          exportIframe = null;
        }
      } else if (event.data.type === 'EXPORT_ERROR') {
        setExporting(false);
        toast.error('Hiba a PNG exportálás során');
        
        // Remove the iframe
        if (exportIframe && exportIframe.parentNode) {
          exportIframe.parentNode.removeChild(exportIframe);
          exportIframe = null;
        }
      }
    };

    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, []);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at')
        .order('start_date', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const handleExportPPT = async () => {
    setExporting(true);
    try {
      const pptxgen = (await import('pptxgenjs')).default;
      
      const selectedAudit = audits.find(a => a.id === selectedAuditId);
      if (!selectedAudit) {
        toast.error('Válassz ki egy felmérést!');
        setExporting(false);
        return;
      }

      toast.info('PowerPoint generálása folyamatban... Kérlek várj!');

      // Fetch responses
      const { data: responses, error } = await supabase
        .from('audit_responses')
        .select('*')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;

      if (!responses || responses.length === 0) {
        toast.error('Nincs adat ehhez a felméréshez');
        setExporting(false);
        return;
      }

      // Create presentation
      const pres = new pptxgen();
      
      // Title slide
      let slide = pres.addSlide();
      slide.background = { color: '3572ef' };
      slide.addText('EAP Pulse Jelentés', {
        x: 0.5,
        y: 2.0,
        w: '90%',
        h: 1.5,
        fontSize: 44,
        bold: true,
        color: 'FFFFFF',
        align: 'center'
      });
      slide.addText(formatAuditName(selectedAudit), {
        x: 0.5,
        y: 3.5,
        w: '90%',
        fontSize: 24,
        color: 'FFFFFF',
        align: 'center'
      });

      // Összefoglaló slide
      slide = pres.addSlide();
      slide.addText('Összefoglaló', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText([
        { text: 'Összefoglaló statisztikák:\n\n', options: { fontSize: 18, bold: true } },
        { text: `• Válaszadók száma: ${responses.length}\n`, options: { fontSize: 16 } },
        { text: `• Felmérés neve: ${selectedAudit.program_name}\n`, options: { fontSize: 16 } },
        { text: `• Kezdés: ${new Date(selectedAudit.start_date).toLocaleDateString('hu-HU')}\n`, options: { fontSize: 16 } }
      ], {
        x: 0.5,
        y: 1.5,
        w: '90%'
      });

      // Tudatosság slide
      slide = pres.addSlide();
      slide.addText('Tudatosság', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText('EAP program ismertségének mérése', {
        x: 0.5,
        y: 1.5,
        fontSize: 16
      });

      // Bizalom & Hajlandóság slide
      slide = pres.addSlide();
      slide.addText('Bizalom & Hajlandóság', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText('Alkalmazottak bizalmi szintjének elemzése', {
        x: 0.5,
        y: 1.5,
        fontSize: 16
      });

      // Használat slide
      slide = pres.addSlide();
      slide.addText('Használat', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText('EAP program használati statisztikák', {
        x: 0.5,
        y: 1.5,
        fontSize: 16
      });

      // Hatás slide
      slide = pres.addSlide();
      slide.addText('Hatás', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText('Program hatásának mérése', {
        x: 0.5,
        y: 1.5,
        fontSize: 16
      });

      // Motiváció slide
      slide = pres.addSlide();
      slide.addText('Motiváció', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText('Használati motivációk elemzése', {
        x: 0.5,
        y: 1.5,
        fontSize: 16
      });

      // Demográfia slide
      slide = pres.addSlide();
      slide.addText('Demográfia', {
        x: 0.5,
        y: 0.5,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });
      slide.addText('Válaszadók demográfiai megoszlása', {
        x: 0.5,
        y: 1.5,
        fontSize: 16
      });

      // Save presentation
      await pres.writeFile({ fileName: `eap_pulse_jelentes_${formatAuditName(selectedAudit)}_${Date.now()}.pptx` });
      toast.success('PowerPoint sikeresen exportálva!');
    } catch (error) {
      console.error('Error exporting PPT:', error);
      toast.error('Hiba történt a PowerPoint exportálás során');
    } finally {
      setExporting(false);
    }
  };

  const handleExportPNG = async (cardId: string, fileName: string) => {
    const chart = exportableCharts.find(c => c.id === cardId);
    if (!chart) return;

    setExporting(true);
    
    // Create hidden iframe
    if (exportIframe && exportIframe.parentNode) {
      exportIframe.parentNode.removeChild(exportIframe);
    }
    
    exportIframe = document.createElement('iframe');
    exportIframe.style.position = 'absolute';
    exportIframe.style.width = '0';
    exportIframe.style.height = '0';
    exportIframe.style.border = 'none';
    exportIframe.style.visibility = 'hidden';
    
    // Set the source to the statistics page with auto-export parameters
    exportIframe.src = `/hr/statistics?tab=${chart.tab}&autoExport=${cardId}&fileName=${fileName}&inIframe=true`;
    
    document.body.appendChild(exportIframe);
    
    toast.info('Grafikon exportálása folyamatban...');
  };

  const handleExportExcel = async () => {
    setExporting(true);
    try {
      const XLSX = await import('xlsx');
      
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata, submitted_at')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        toast.error('Nincs adat az exportáláshoz');
        setExporting(false);
        return;
      }

      const wb = XLSX.utils.book_new();

      // Sheet 1: Demográfia
      const demographicsData = data.map(r => ({
        'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
        'Kategória': r.employee_metadata?.branch === 'used' ? 'Használó' : r.employee_metadata?.branch === 'not_used' ? 'Nem használó' : 'Nem tudott róla',
        'Nem': r.responses?.gender || '',
        'Életkor': r.responses?.age || '',
        'EAP Ismertség': r.responses?.eap_knowledge || '',
      }));
      const wsDemo = XLSX.utils.json_to_sheet(demographicsData);
      wsDemo['!cols'] = [{ wch: 20 }, { wch: 15 }, { wch: 10 }, { wch: 12 }, { wch: 30 }];
      XLSX.utils.book_append_sheet(wb, wsDemo, 'Demográfia');

      // Sheet 2: Használók - Awareness
      const usersAwareness = data
        .filter(r => r.employee_metadata?.branch === 'used')
        .map(r => ({
          'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
          'Szolgáltatás megértése (1-5)': r.responses?.u_awareness_understanding || '',
          'Igénybevételi tudás (1-5)': r.responses?.u_awareness_how_to_use || '',
          'Elérhetőség érzete (1-5)': r.responses?.u_awareness_accessibility || '',
          'Tájékoztatás gyakorisága': r.responses?.u_awareness_frequency || '',
          'Honnan hallott róla': Array.isArray(r.responses?.u_awareness_source) ? r.responses.u_awareness_source.join(', ') : '',
        }));
      if (usersAwareness.length > 0) {
        const wsUserAware = XLSX.utils.json_to_sheet(usersAwareness);
        wsUserAware['!cols'] = [{ wch: 20 }, { wch: 25 }, { wch: 25 }, { wch: 25 }, { wch: 20 }, { wch: 30 }];
        XLSX.utils.book_append_sheet(wb, wsUserAware, 'Használók - Awareness');
      }

      // Sheet 3: Használók - Bizalom
      const usersTrust = data
        .filter(r => r.employee_metadata?.branch === 'used')
        .map(r => ({
          'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
          'Bizalom anonimitásban (1-5)': r.responses?.u_trust_anonymity || '',
          'Munkaadói félelem (1-5)': r.responses?.u_trust_employer || '',
          'Kollégák tudomása (1-5)': r.responses?.u_trust_colleagues || '',
          'Hajlandóság jövőben (1-5)': r.responses?.u_trust_likelihood || '',
          'Akadályok': r.responses?.u_trust_barriers || '',
        }));
      if (usersTrust.length > 0) {
        const wsUserTrust = XLSX.utils.json_to_sheet(usersTrust);
        wsUserTrust['!cols'] = [{ wch: 20 }, { wch: 25 }, { wch: 25 }, { wch: 25 }, { wch: 25 }, { wch: 20 }];
        XLSX.utils.book_append_sheet(wb, wsUserTrust, 'Használók - Bizalom');
      }

      // Sheet 4: Használók - Használat
      const usersUsage = data
        .filter(r => r.employee_metadata?.branch === 'used')
        .map(r => ({
          'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
          'Használat gyakorisága': r.responses?.u_usage_frequency || '',
          'Témakör': Array.isArray(r.responses?.u_usage_topic) ? r.responses.u_usage_topic.join(', ') : '',
          'Csatorna': Array.isArray(r.responses?.u_usage_channel) ? r.responses.u_usage_channel.join(', ') : '',
          'Család is használta': r.responses?.u_usage_family === 'yes' ? 'Igen' : 'Nem',
          'Időtartam gondoskodásig': r.responses?.u_usage_time_to_care || '',
        }));
      if (usersUsage.length > 0) {
        const wsUserUsage = XLSX.utils.json_to_sheet(usersUsage);
        wsUserUsage['!cols'] = [{ wch: 20 }, { wch: 20 }, { wch: 30 }, { wch: 25 }, { wch: 20 }, { wch: 25 }];
        XLSX.utils.book_append_sheet(wb, wsUserUsage, 'Használók - Használat');
      }

      // Sheet 5: Használók - Hatás
      const usersImpact = data
        .filter(r => r.employee_metadata?.branch === 'used')
        .map(r => ({
          'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
          'Teljesítmény javulás (1-5)': r.responses?.u_impact_performance || '',
          'Problémamegoldás (1-5)': r.responses?.u_impact_problem_solving || '',
          'Jóllét javulás (1-5)': r.responses?.u_impact_wellbeing || '',
          'Elégedettség (1-5)': r.responses?.u_impact_satisfaction || '',
          'Konzisztencia (1-5)': r.responses?.u_impact_consistency || '',
          'NPS (0-10)': r.responses?.u_impact_nps || '',
        }));
      if (usersImpact.length > 0) {
        const wsUserImpact = XLSX.utils.json_to_sheet(usersImpact);
        wsUserImpact['!cols'] = [{ wch: 20 }, { wch: 25 }, { wch: 25 }, { wch: 25 }, { wch: 20 }, { wch: 20 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsUserImpact, 'Használók - Hatás');
      }

      // Sheet 6: Használók - Preferenciák
      const usersPref = data
        .filter(r => r.employee_metadata?.branch === 'used')
        .map(r => ({
          'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
          'Preferált szakértő': r.responses?.u_pref_expert || '',
          'Preferált csatorna': r.responses?.u_pref_channel || '',
          'Preferált elérhetőség': r.responses?.u_pref_availability || '',
          'Preferált tartalom típus': r.responses?.u_pref_content_type || '',
          'Kommunikáció gyakorisága': r.responses?.u_pref_comm_frequency || '',
        }));
      if (usersPref.length > 0) {
        const wsUserPref = XLSX.utils.json_to_sheet(usersPref);
        wsUserPref['!cols'] = [{ wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 25 }, { wch: 25 }];
        XLSX.utils.book_append_sheet(wb, wsUserPref, 'Használók - Preferenciák');
      }

      // Sheet 7: Nem használók
      const nonUsers = data
        .filter(r => r.employee_metadata?.branch === 'not_used')
        .map(r => ({
          'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
          'Megértés (1-5)': r.responses?.nu_awareness_understanding || '',
          'Hasznossági észlelés (1-5)': r.responses?.nu_usefulness_perception || '',
          'Honnan hallott róla': Array.isArray(r.responses?.nu_awareness_source) ? r.responses.nu_awareness_source.join(', ') : '',
          'Bizalom anonimitásban (1-5)': r.responses?.nu_trust_anonymity || '',
          'Munkaadói félelem (1-5)': r.responses?.nu_trust_employer || '',
          'Kollégák tudomása (1-5)': r.responses?.nu_trust_colleagues || '',
          'Mi motiválná': Array.isArray(r.responses?.nu_motivation_what) ? r.responses.nu_motivation_what.join(', ') : '',
          'Preferált szakértő': r.responses?.nu_motivation_expert || '',
          'Preferált csatorna': r.responses?.nu_motivation_channel || '',
          'Preferált elérhetőség': r.responses?.nu_motivation_availability || '',
          'Preferált kommunikáció': r.responses?.nu_motivation_communication || '',
        }));
      if (nonUsers.length > 0) {
        const wsNonUsers = XLSX.utils.json_to_sheet(nonUsers);
        wsNonUsers['!cols'] = [{ wch: 20 }, { wch: 15 }, { wch: 20 }, { wch: 30 }, { wch: 25 }, { wch: 25 }, { wch: 25 }, { wch: 40 }, { wch: 20 }, { wch: 20 }, { wch: 25 }, { wch: 25 }];
        XLSX.utils.book_append_sheet(wb, wsNonUsers, 'Nem használók');
      }

      XLSX.writeFile(wb, `eap_pulse_export_${selectedAuditId}_${Date.now()}.xlsx`);
      toast.success('Excel sikeresen exportálva!');
    } catch (error) {
      console.error('Error exporting Excel:', error);
      toast.error('Hiba történt az Excel exportálás során');
    } finally {
      setExporting(false);
    }
  };

  const handleExportCSV = async () => {
    setExporting(true);
    try {
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata, submitted_at')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        toast.error('Nincs adat az exportáláshoz');
        return;
      }

      // Convert to CSV
      const headers = ['Beküldés ideje', 'Ág', 'Nem', 'Életkor', 'Válaszok'];
      const rows = data.map(r => [
        new Date(r.submitted_at).toLocaleString('hu-HU'),
        r.employee_metadata?.branch || '',
        r.responses?.gender || '',
        r.responses?.age || '',
        JSON.stringify(r.responses)
      ]);

      const csvContent = [
        headers.join(','),
        ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
      ].join('\n');

      // Download CSV
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);
      link.setAttribute('href', url);
      link.setAttribute('download', `audit_export_${selectedAuditId}_${Date.now()}.csv`);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      toast.success('CSV sikeresen exportálva!');
    } catch (error) {
      console.error('Error exporting CSV:', error);
      toast.error('Hiba történt a CSV exportálás során');
    } finally {
      setExporting(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-start gap-4 mb-6">
        <div>
          <h2 className="text-2xl font-bold mb-2">Export & Jelentések</h2>
          <p className="text-muted-foreground">Felmérések exportálása különböző formátumokban</p>
        </div>
        
        <div className="min-w-[300px]">
          <label className="text-xs text-muted-foreground mb-1.5 block">
            Felmérés kiválasztása
          </label>
          <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
            <SelectTrigger>
              <SelectValue placeholder="Felmérés kiválasztása" />
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
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card className="flex flex-col">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Presentation className="h-5 w-5" />
              PowerPoint Prezentáció
            </CardTitle>
            <CardDescription>
              Komplett jelentés prezentációs formában
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 flex-1 flex flex-col">
            <div className="text-sm space-y-2 flex-1">
              <p><strong>Tartalom (slide-onként):</strong></p>
              <ul className="list-disc list-inside ml-4 space-y-1">
                <li>Címlap</li>
                <li>Összefoglaló statisztikák</li>
                <li>Tudatosság</li>
                <li>Bizalom & Hajlandóság</li>
                <li>Használat</li>
                <li>Hatás</li>
                <li>Motiváció</li>
                <li>Demográfia</li>
              </ul>
            </div>
            <Button 
              onClick={handleExportPPT} 
              disabled={exporting || !selectedAuditId}
              className="w-full"
              style={{
                backgroundColor: '#3572ef',
                color: 'white'
              }}
            >
              <Download className="mr-2 h-4 w-4" />
              PowerPoint Letöltése
            </Button>
          </CardContent>
        </Card>

        <Card className="flex flex-col">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileSpreadsheet className="h-5 w-5" />
              Excel Export
            </CardTitle>
            <CardDescription>
              Strukturált adatok táblázatban
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 flex-1 flex flex-col">
            <div className="text-sm space-y-2 flex-1">
              <p><strong>Tartalom:</strong></p>
              <ul className="list-disc list-inside ml-4 space-y-1">
                <li>Összefoglaló - Fő mutatók (igénybevétel, elégedettség, részvétel)</li>
                <li>Ismertség - Awareness szintek, források, gyakoriság, megértés</li>
                <li>Bizalom - Használók és nem használók bizalmi indexei, hajlandóság</li>
                <li>Használat - Gyakoriság, témák, csatornák, családi használat</li>
                <li>Hatás - NPS, teljesítmény, jóllét, problémamegoldás mutatók</li>
                <li>Motiváció - Motivátorok, szakértő típus, csatorna preferenciák (nem használók)</li>
                <li>Demográfia - Nem, korcsoport, kategória megoszlások</li>
              </ul>
            </div>
            <Button 
              onClick={handleExportExcel} 
              disabled={exporting || !selectedAuditId}
              variant="outline"
              className="w-full"
            >
              <Download className="mr-2 h-4 w-4" />
              Excel Letöltése
            </Button>
          </CardContent>
        </Card>

        <Card className="flex flex-col">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Download className="h-5 w-5" />
              CSV Adatexport
            </CardTitle>
            <CardDescription>
              Nyers válaszadatok exportálása
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4 flex-1 flex flex-col">
            <div className="text-sm space-y-2 flex-1">
              <p><strong>Tartalom:</strong></p>
              <ul className="list-disc list-inside ml-4 space-y-1">
                <li>Összes válaszadó összes válasza</li>
                <li>Demográfiai adatok</li>
                <li>Beküldés időpontja</li>
                <li>Kategória (ág) információ</li>
              </ul>
            </div>
            <Button 
              onClick={handleExportCSV} 
              disabled={exporting || !selectedAuditId}
              variant="outline"
              className="w-full"
            >
              <Download className="mr-2 h-4 w-4" />
              CSV Letöltése
            </Button>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <ImageIcon className="h-5 w-5" />
            PNG Grafikonok
          </CardTitle>
          <CardDescription>
            Letölthető grafikonok témakörök szerint
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="overview" className="w-full">
            <TabsList className="grid grid-cols-7 mb-4">
              <TabsTrigger value="overview">Összefoglaló</TabsTrigger>
              <TabsTrigger value="awareness">Ismertség</TabsTrigger>
              <TabsTrigger value="trust">Bizalom</TabsTrigger>
              <TabsTrigger value="usage">Használat</TabsTrigger>
              <TabsTrigger value="impact">Hatás</TabsTrigger>
              <TabsTrigger value="motivation">Motiváció</TabsTrigger>
              <TabsTrigger value="demographics">Demográfia</TabsTrigger>
            </TabsList>

            <TabsContent value="overview" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Gyors áttekintés - főbb mutatók</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'overview').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>

            <TabsContent value="awareness" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Ismertség és awareness mutatók</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'awareness').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>

            <TabsContent value="trust" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Bizalom és hajlandóság mutatók</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'trust').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>

            <TabsContent value="usage" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Használati statisztikák</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'usage').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>

            <TabsContent value="impact" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Hatás mutatók</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'impact').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>

            <TabsContent value="motivation" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Motivációs tényezők</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'motivation').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>

            <TabsContent value="demographics" className="space-y-2">
              <p className="text-sm text-muted-foreground mb-4">Demográfiai megoszlások</p>
              <div className="grid grid-cols-2 gap-2">
                {exportableCharts.filter(c => c.tab === 'demographics').map(chart => (
                  <Button 
                    key={chart.id}
                    variant="outline" 
                    onClick={() => handleExportPNG(chart.id, chart.fileName)}
                    disabled={exporting || !selectedAuditId}
                    className="justify-start"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {chart.name}
                  </Button>
                ))}
              </div>
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>

    </div>
  );
};

export default Export;
