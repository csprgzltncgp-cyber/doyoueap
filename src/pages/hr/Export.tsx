import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { FileText, Image as ImageIcon, Download, FileSpreadsheet } from 'lucide-react';

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
      // Dynamic import to avoid build issues
      const jsPDF = (await import('jspdf')).default;
      
      const selectedAudit = audits.find(a => a.id === selectedAuditId);
      if (!selectedAudit) return;

      const { data: responses, error } = await supabase
        .from('audit_responses')
        .select('responses, employee_metadata, submitted_at')
        .eq('audit_id', selectedAuditId);

      if (error) throw error;

      if (!responses || responses.length === 0) {
        toast.error('Nincs adat az exportáláshoz');
        return;
      }

      const pdf = new jsPDF('p', 'mm', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      
      pdf.setFontSize(24);
      pdf.text('Audit Jelentés', pageWidth / 2, 40, { align: 'center' });
      pdf.setFontSize(16);
      pdf.text(formatAuditName(selectedAudit), pageWidth / 2, 55, { align: 'center' });
      pdf.setFontSize(12);
      pdf.text(`Generálva: ${new Date().toLocaleDateString('hu-HU')}`, pageWidth / 2, 70, { align: 'center' });
      pdf.text(`Összes válasz: ${responses.length}`, pageWidth / 2, 80, { align: 'center' });

      const usedBranch = responses.filter(r => r.employee_metadata?.branch === 'used').length;
      const notUsedBranch = responses.filter(r => r.employee_metadata?.branch === 'not_used').length;
      const redirectBranch = responses.filter(r => r.employee_metadata?.branch === 'redirect').length;

      pdf.addPage();
      pdf.setFontSize(18);
      pdf.text('Összefoglaló Statisztikák', 20, 20);
      pdf.setFontSize(12);
      pdf.text(`Használók: ${usedBranch} (${((usedBranch / responses.length) * 100).toFixed(1)}%)`, 20, 35);
      pdf.text(`Nem használók: ${notUsedBranch} (${((notUsedBranch / responses.length) * 100).toFixed(1)}%)`, 20, 45);
      pdf.text(`Nem tudtak róla: ${redirectBranch} (${((redirectBranch / responses.length) * 100).toFixed(1)}%)`, 20, 55);

      pdf.save(`audit_jelentés_${formatAuditName(selectedAudit)}_${Date.now()}.pdf`);
      toast.success('PDF sikeresen exportálva!');
    } catch (error) {
      console.error('Error exporting PDF:', error);
      toast.error('Hiba történt a PDF exportálás során');
    } finally {
      setExporting(false);
    }
  };

  const handleExportPNG = async (section: string) => {
    setExporting(true);
    try {
      window.open(`/hr/statistics?tab=${section.toLowerCase()}`, '_blank');
      toast.info(`Kérlek, nyisd meg a Statistics oldalt, majd használd a böngésző "Mentés képként" funkcióját a ${section} grafikon mentéséhez.`);
    } catch (error) {
      console.error('Error exporting PNG:', error);
      toast.error('Hiba történt a PNG exportálás során');
    } finally {
      setExporting(false);
    }
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
                <li>Összefoglaló statisztikák</li>
                <li>Válaszadók megoszlása (Használók/Nem használók)</li>
                <li>Alapvető metrikák</li>
                <li>Dátum és audit információk</li>
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
            Egyedi grafikonok exportálása képként
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Összefoglaló')}
              disabled={exporting || !selectedAuditId}
            >
              Összefoglaló
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Igénybevétel')}
              disabled={exporting || !selectedAuditId}
            >
              Igénybevétel
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Elégedettség')}
              disabled={exporting || !selectedAuditId}
            >
              Elégedettség
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Kategóriák')}
              disabled={exporting || !selectedAuditId}
            >
              Kategóriák
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Awareness')}
              disabled={exporting || !selectedAuditId}
            >
              Awareness
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Trust')}
              disabled={exporting || !selectedAuditId}
            >
              Trust
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Usage')}
              disabled={exporting || !selectedAuditId}
            >
              Usage
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Impact')}
              disabled={exporting || !selectedAuditId}
            >
              Impact
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Motiváció')}
              disabled={exporting || !selectedAuditId}
            >
              Motiváció
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Demográfia')}
              disabled={exporting || !selectedAuditId}
            >
              Demográfia
            </Button>
            <Button 
              variant="outline" 
              onClick={() => handleExportPNG('Trendek')}
              disabled={exporting || !selectedAuditId}
            >
              Trendek
            </Button>
          </div>
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
