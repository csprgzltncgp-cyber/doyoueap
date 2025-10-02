import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { exportableCharts } from '@/lib/exportUtils';
import { FileText, Image as ImageIcon, Download, FileSpreadsheet } from 'lucide-react';

let exportIframe: HTMLIFrameElement | null = null;

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
      toast.error('Hiba történt az auditek betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const handleExportPDF = async () => {
    setExporting(true);
    try {
      const jsPDF = (await import('jspdf')).default;
      const html2canvas = (await import('html2canvas')).default;
      
      const selectedAudit = audits.find(a => a.id === selectedAuditId);
      if (!selectedAudit) return;

      toast.info('PDF generálása folyamatban... Ez eltarthat egy kis ideig.');

      const pdf = new jsPDF('p', 'mm', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const margin = 10;
      const cardWidth = (pageWidth - 3 * margin) / 2; // 2 columns
      
      // Title page
      pdf.setFontSize(24);
      pdf.text('Audit Jelentés', pageWidth / 2, 40, { align: 'center' });
      pdf.setFontSize(16);
      pdf.text(formatAuditName(selectedAudit), pageWidth / 2, 55, { align: 'center' });
      pdf.setFontSize(12);
      pdf.text(`Generálva: ${new Date().toLocaleDateString('hu-HU')}`, pageWidth / 2, 70, { align: 'center' });

      // Define all tabs and their card IDs
      const tabsToExport = [
        {
          name: 'Összefoglaló',
          url: '/hr/statistics?tab=overview',
          cardIds: ['utilization-card', 'satisfaction-card', 'awareness-card', 'trust-card', 'usage-card', 'impact-card']
        },
        {
          name: 'Ismertség',
          url: '/hr/statistics?tab=awareness',
          cardIds: ['awareness-pie-card', 'awareness-bar-card']
        },
        {
          name: 'Bizalom & Hajlandóság',
          url: '/hr/statistics?tab=trust',
          cardIds: ['trust-users-anonymity-card', 'trust-users-employer-card', 'trust-users-likelihood-card', 'trust-non-users-card', 'trust-nu-anonymity-card', 'trust-nu-employer-card', 'trust-nu-colleagues-card']
        },
        {
          name: 'Használat',
          url: '/hr/statistics?tab=usage',
          cardIds: ['usage-satisfaction-card', 'usage-problem-solving-card', 'usage-nps-card', 'usage-consistency-card']
        },
        {
          name: 'Hatás',
          url: '/hr/statistics?tab=impact',
          cardIds: ['impact-performance-card', 'impact-problem-solving-card', 'impact-wellbeing-card', 'impact-satisfaction-card', 'impact-consistency-card']
        },
        {
          name: 'Motiváció',
          url: '/hr/statistics?tab=motivation',
          cardIds: ['motivation-what-card', 'motivation-expert-card', 'motivation-channel-card']
        },
        {
          name: 'Demográfia',
          url: '/hr/statistics?tab=demographics',
          cardIds: ['demographics-gender-card', 'demographics-age-card']
        }
      ];

      // Capture each tab
      for (let i = 0; i < tabsToExport.length; i++) {
        const tab = tabsToExport[i];
        
        pdf.addPage();

        // Add tab title
        pdf.setFontSize(18);
        pdf.text(tab.name, margin, 15);
        
        let yPosition = 25;
        let xPosition = margin;
        let columnIndex = 0;

        // Create hidden iframe to load the page
        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.width = '1200px';
        iframe.style.height = '3000px';
        iframe.style.left = '-9999px';
        iframe.src = tab.url;
        document.body.appendChild(iframe);

        // Wait for iframe to load
        await new Promise<void>((resolve) => {
          iframe.onload = () => {
            setTimeout(() => resolve(), 2500); // Wait for data to load
          };
        });

        // Capture each card
        for (let cardIndex = 0; cardIndex < tab.cardIds.length; cardIndex++) {
          const cardId = tab.cardIds[cardIndex];
          const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;
          if (!iframeDoc) continue;

          const element = iframeDoc.getElementById(cardId);
          if (!element) {
            console.warn(`Card not found: ${cardId}`);
            continue;
          }

          const canvas = await html2canvas(element, {
            backgroundColor: '#ffffff',
            scale: 1.5,
            allowTaint: true,
            useCORS: true,
            windowWidth: 1200,
          });

          const imgData = canvas.toDataURL('image/png');
          const aspectRatio = canvas.height / canvas.width;
          const imgHeight = cardWidth * aspectRatio;

          // Check if we need a new page
          if (yPosition + imgHeight > pageHeight - margin) {
            pdf.addPage();
            pdf.setFontSize(18);
            pdf.text(`${tab.name} (folytatás)`, margin, 15);
            yPosition = 25;
            xPosition = margin;
            columnIndex = 0;
          }

          // Add image in 2-column layout
          pdf.addImage(imgData, 'PNG', xPosition, yPosition, cardWidth, imgHeight);

          // Move to next position
          columnIndex++;
          if (columnIndex % 2 === 0) {
            // Move to next row
            yPosition += imgHeight + 5;
            xPosition = margin;
          } else {
            // Move to second column
            xPosition = margin + cardWidth + margin;
          }
        }

        // Remove iframe
        document.body.removeChild(iframe);
      }

      pdf.save(`audit_jelentés_${formatAuditName(selectedAudit)}_${Date.now()}.pdf`);
      toast.success('PDF sikeresen exportálva!');
    } catch (error) {
      console.error('Error exporting PDF:', error);
      toast.error('Hiba történt a PDF exportálás során');
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
      // Dynamic import to avoid build issues
      const XLSX = await import('xlsx');
      
      const { data, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata, submitted_at')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;

      if (!data || data.length === 0) {
        toast.error('Nincs adat az exportáláshoz');
        return;
      }

      const excelData = data.map(r => ({
        'Beküldés ideje': new Date(r.submitted_at).toLocaleString('hu-HU'),
        'Ág': r.employee_metadata?.branch || '',
        'Nem': r.responses?.gender || '',
        'Életkor': r.responses?.age || '',
        'Ismertség': r.responses?.awareness_heard || '',
        'Használat': r.employee_metadata?.branch === 'used' ? 'Igen' : 'Nem',
        'Elégedettség': r.responses?.u_impact_satisfaction || '',
        'NPS': r.responses?.u_impact_nps || '',
        'Bizalom': r.responses?.u_trust_anonymity || r.responses?.nu_trust_anonymity || '',
      }));

      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.json_to_sheet(excelData);

      ws['!cols'] = [
        { wch: 20 }, { wch: 15 }, { wch: 10 }, { wch: 12 },
        { wch: 15 }, { wch: 10 }, { wch: 12 }, { wch: 8 }, { wch: 12 }
      ];

      XLSX.utils.book_append_sheet(wb, ws, 'Válaszok');
      XLSX.writeFile(wb, `audit_export_${selectedAuditId}_${Date.now()}.xlsx`);

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
    <div className="p-8 space-y-6">
      <div>
        <h1 className="text-3xl font-bold mb-2">Export & Jelentések</h1>
        <p className="text-muted-foreground">Auditok exportálása különböző formátumokban</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Audit Kiválasztása</CardTitle>
          <CardDescription>Válaszd ki az exportálandó auditot</CardDescription>
        </CardHeader>
        <CardContent>
          <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
            <SelectTrigger className="w-full">
              <SelectValue placeholder="Válassz auditot" />
            </SelectTrigger>
            <SelectContent>
              {audits.map((audit) => (
                <SelectItem key={audit.id} value={audit.id}>
                  {formatAuditName(audit)}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </CardContent>
      </Card>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5" />
              PDF Jelentés
            </CardTitle>
            <CardDescription>
              Komplett jelentés alapstatisztikákkal
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-sm space-y-2">
              <p><strong>Tartalom:</strong></p>
              <ul className="list-disc list-inside ml-4 space-y-1">
                <li>Összefoglaló - 6 kártya</li>
                <li>Ismertség - 2 kártya (kördiagram, részletes)</li>
                <li>Bizalom & Hajlandóság - 7 kártya</li>
                <li>Használat - 4 kártya</li>
                <li>Hatás - 5 kártya</li>
                <li>Motiváció - 3 kártya</li>
                <li>Demográfia - 2 kártya</li>
              </ul>
            </div>
            <Button 
              onClick={handleExportPDF} 
              disabled={exporting || !selectedAuditId}
              className="w-full"
            >
              <Download className="mr-2 h-4 w-4" />
              PDF Letöltése
            </Button>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileSpreadsheet className="h-5 w-5" />
              Excel Export
            </CardTitle>
            <CardDescription>
              Strukturált adatok táblázatban
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-sm space-y-2">
              <p><strong>Tartalom:</strong></p>
              <ul className="list-disc list-inside ml-4 space-y-1">
                <li>Minden válasz strukturált formában</li>
                <li>Demográfiai adatok oszlopokban</li>
                <li>Kulcs metrikák (Elégedettség, NPS, stb.)</li>
                <li>Szűrhető és elemezhető táblázat</li>
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

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Download className="h-5 w-5" />
              CSV Adatexport
            </CardTitle>
            <CardDescription>
              Nyers válaszadatok exportálása
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-sm space-y-2">
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
            <TabsList className="grid grid-cols-6 mb-4">
              <TabsTrigger value="overview">Összefoglaló</TabsTrigger>
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

      <Card className="bg-blue-50 dark:bg-blue-950 border-blue-200">
        <CardHeader>
          <CardTitle>💡 Export Tippek</CardTitle>
        </CardHeader>
        <CardContent className="text-sm space-y-2">
          <p><strong>PDF jelentés:</strong> Készíts gyors összefoglalót alapstatisztikákkal és audit információkkal</p>
          <p><strong>Excel export:</strong> Elemezd tovább az adatokat strukturált táblázatban szűrési lehetőségekkel</p>
          <p><strong>CSV export:</strong> Import nyers adatokat más elemző eszközökbe</p>
          <p><strong>PNG grafikonok:</strong> Kattints a Statistics oldalon található grafikonokra, majd használd a böngésző screenshot funkcióját</p>
        </CardContent>
      </Card>
    </div>
  );
};

export default Export;
