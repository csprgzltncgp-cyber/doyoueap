import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { FileText, Image as ImageIcon, Download } from 'lucide-react';

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
      const selectedAudit = audits.find(a => a.id === selectedAuditId);
      if (!selectedAudit) return;

      // Note: In a real implementation, you would call an edge function
      // that generates a PDF using a library like pdfmake or puppeteer
      toast.info('PDF export funkció fejlesztés alatt...');
      
      // Placeholder for future implementation
      // const { data, error } = await supabase.functions.invoke('generate-report-pdf', {
      //   body: { auditId: selectedAuditId }
      // });
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
      toast.info(`${section} PNG export funkció fejlesztés alatt...`);
      
      // Placeholder for future implementation using html2canvas
      // This would capture specific chart elements and download as PNG
    } catch (error) {
      console.error('Error exporting PNG:', error);
      toast.error('Hiba történt a PNG exportálás során');
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

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5" />
              PDF Jelentés
            </CardTitle>
            <CardDescription>
              Komplett jelentés 6 oldalon
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-sm space-y-2">
              <p><strong>Tartalom:</strong></p>
              <ul className="list-disc list-inside ml-4 space-y-1">
                <li>1. oldal: Összefoglaló - Igénybevétel & Elégedettségi Index</li>
                <li>2. oldal: 4 fő téma áttekintése (Awareness, Trust, Usage, Impact)</li>
                <li>3. oldal: Awareness & Trust részletezők</li>
                <li>4. oldal: Usage & Impact riportok</li>
                <li>5. oldal: Motivációs faktorok & Demográfiai bontás</li>
                <li>6. oldal: Trendek & User kategóriák</li>
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
          <p><strong>PDF jelentés:</strong> Készíts komplett prezentációt vezetőségnek egyetlen gombnyomással</p>
          <p><strong>CSV export:</strong> Elemezd tovább az adatokat Excel-ben vagy más eszközökkel</p>
          <p><strong>PNG grafikonok:</strong> Illeszd be a grafikonokat prezentációkba vagy dokumentumokba</p>
        </CardContent>
      </Card>
    </div>
  );
};

export default Export;
