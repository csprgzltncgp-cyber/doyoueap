import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { toast } from 'sonner';
import { formatAuditName } from '@/lib/auditUtils';
import { exportableCharts } from '@/lib/exportUtils';
import { exportAllChartsToPPT } from '@/lib/pptExportUtils';
import { Presentation, Image as ImageIcon, Download, FileSpreadsheet, History, Trash2 } from 'lucide-react';
import { usePackage } from '@/hooks/usePackage';

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
  company_name?: string;
  partner_company_id?: string | null;
}

const Export = () => {
  const { packageType } = usePackage();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [selectedCompanyId, setSelectedCompanyId] = useState<string>('all');
  const [companies, setCompanies] = useState<Array<{ id: string; company_name: string }>>([]);
  const [loading, setLoading] = useState(true);
  const [exporting, setExporting] = useState(false);
  const [exportHistory, setExportHistory] = useState<any[]>([]);
  const [loadingHistory, setLoadingHistory] = useState(true);

  useEffect(() => {
    if (packageType === 'partner') {
      fetchCompanies();
    }
    fetchAudits();
    fetchExportHistory();

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
  }, [packageType]);

  const fetchCompanies = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { data, error } = await supabase
        .from('companies')
        .select('id, company_name')
        .eq('partner_user_id', user.id)
        .order('company_name');

      if (error) throw error;
      setCompanies(data || []);
    } catch (error) {
      console.error('Error fetching companies:', error);
    }
  };

  // Refetch audits when selected company changes
  useEffect(() => {
    if (packageType === 'partner') {
      fetchAudits();
    }
  }, [selectedCompanyId, packageType]);

  const fetchAudits = async () => {
    try {
      let query = supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at, company_name, partner_company_id')
        .eq('is_active', true);

      // Filter by company for partners
      if (packageType === 'partner' && selectedCompanyId && selectedCompanyId !== 'all') {
        query = query.eq('partner_company_id', selectedCompanyId);
      }

      const { data } = await query.order('start_date', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      } else {
        setAudits([]);
        setSelectedAuditId('');
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const fetchExportHistory = async () => {
    try {
      console.log('fetchExportHistory called');
      
      const { data: { user } } = await supabase.auth.getUser();
      console.log('Fetching history for user:', user?.id);
      
      if (!user) {
        console.error('No user found in fetchExportHistory');
        return;
      }

      const { data, error } = await supabase
        .from('export_history')
        .select('*')
        .order('created_at', { ascending: false })
        .limit(50);

      console.log('Export history data:', data);
      console.log('Export history error:', error);

      if (error) throw error;
      setExportHistory(data || []);
    } catch (error) {
      console.error('Error fetching export history:', error);
    } finally {
      setLoadingHistory(false);
    }
  };

  const saveExportToHistory = async (auditId: string, fileName: string, fileType: string) => {
    try {
      console.log('saveExportToHistory called:', { auditId, fileName, fileType });
      
      const { data: { user } } = await supabase.auth.getUser();
      console.log('User:', user?.id);
      
      if (!user) {
        console.error('No user found');
        toast.error('Nincs bejelentkezve felhasználó');
        return;
      }

      const audit = audits.find(a => a.id === auditId);
      const auditName = audit ? formatAuditName(audit) : 'Ismeretlen felmérés';
      
      console.log('Inserting to export_history:', {
        user_id: user.id,
        audit_id: auditId,
        audit_name: auditName,
        file_name: fileName,
        file_type: fileType
      });

      const { data, error } = await supabase
        .from('export_history')
        .insert({
          user_id: user.id,
          audit_id: auditId,
          audit_name: auditName,
          file_name: fileName,
          file_type: fileType
        })
        .select();

      if (error) {
        console.error('Error saving export history:', error);
        toast.error(`Hiba az előzmény mentésekor: ${error.message}`);
        throw error;
      }
      
      console.log('Export history saved successfully:', data);
      
      // Refresh history
      await fetchExportHistory();
    } catch (error) {
      console.error('Error saving export history:', error);
    }
  };

  const deleteHistoryItem = async (id: string) => {
    try {
      const { error } = await supabase
        .from('export_history')
        .delete()
        .eq('id', id);

      if (error) throw error;
      
      toast.success('Előzmény törölve');
      await fetchExportHistory();
    } catch (error) {
      console.error('Error deleting history item:', error);
      toast.error('Hiba történt a törlés során');
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

      // Calculate statistics
      const usedBranch = responses.filter(r => r.employee_metadata?.branch === 'used');
      const notUsedBranch = responses.filter(r => r.employee_metadata?.branch === 'not_used');
      const notAwareBranch = responses.filter(r => r.employee_metadata?.branch === 'not_aware');

      // Create presentation
      const pres = new pptxgen();
      pres.layout = 'LAYOUT_16x9';
      
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
        y: 3.0,
        w: '90%',
        fontSize: 24,
        color: 'FFFFFF',
        align: 'center'
      });
      slide.addText(`${responses.length} válaszadó`, {
        x: 0.5,
        y: 3.8,
        w: '90%',
        fontSize: 18,
        color: 'FFFFFF',
        align: 'center'
      });

      // Összefoglaló slide
      slide = pres.addSlide();
      slide.addText('Összefoglaló', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // Main stats in boxes
      const usagePercent = ((usedBranch.length / responses.length) * 100).toFixed(1);
      const notUsedPercent = ((notUsedBranch.length / responses.length) * 100).toFixed(1);
      const notAwarePercent = ((notAwareBranch.length / responses.length) * 100).toFixed(1);

      // Row 1: Total responses
      slide.addShape(pres.ShapeType.rect, {
        x: 1.5,
        y: 1.5,
        w: 7,
        h: 1.2,
        fill: { color: 'f0f9ff' },
        line: { color: '3572ef', width: 2 }
      });
      slide.addText('Összesen válaszadó', {
        x: 1.5,
        y: 1.6,
        w: 3.5,
        fontSize: 20,
        color: '1e40af',
        align: 'center'
      });
      slide.addText(`${responses.length}`, {
        x: 5.0,
        y: 1.5,
        w: 3.5,
        h: 1.2,
        fontSize: 44,
        bold: true,
        color: '3572ef',
        align: 'center',
        valign: 'middle'
      });

      // Row 2: A program aktív felhasználói
      slide.addShape(pres.ShapeType.rect, {
        x: 1.5,
        y: 2.9,
        w: 7,
        h: 0.9,
        fill: { color: 'dcfce7' },
        line: { color: '22c55e', width: 2 }
      });
      slide.addText('Használók: A program aktív felhasználói', {
        x: 1.5,
        y: 3.0,
        w: 3.5,
        fontSize: 18,
        color: '166534',
        align: 'center'
      });
      slide.addText(`${usedBranch.length} (${usagePercent}%)`, {
        x: 5.0,
        y: 2.9,
        w: 3.5,
        h: 0.9,
        fontSize: 24,
        bold: true,
        color: '22c55e',
        align: 'center',
        valign: 'middle'
      });

      // Row 3: Akik eddig nem vették igénybe a programot
      slide.addShape(pres.ShapeType.rect, {
        x: 1.5,
        y: 3.9,
        w: 7,
        h: 0.9,
        fill: { color: 'fef3c7' },
        line: { color: 'f59e0b', width: 2 }
      });
      slide.addText('Nem használók: Akik eddig nem vették igénybe a programot', {
        x: 1.5,
        y: 4.0,
        w: 3.5,
        fontSize: 18,
        color: '92400e',
        align: 'center'
      });
      slide.addText(`${notUsedBranch.length} (${notUsedPercent}%)`, {
        x: 5.0,
        y: 3.9,
        w: 3.5,
        h: 0.9,
        fontSize: 24,
        bold: true,
        color: 'f59e0b',
        align: 'center',
        valign: 'middle'
      });

      // Row 4: Nem tudtak róla
      slide.addShape(pres.ShapeType.rect, {
        x: 1.5,
        y: 4.9,
        w: 7,
        h: 0.9,
        fill: { color: 'fee2e2' },
        line: { color: 'ef4444', width: 2 }
      });
      slide.addText('Nem tudtak a programról', {
        x: 1.5,
        y: 5.0,
        w: 3.5,
        fontSize: 18,
        color: '991b1b',
        align: 'center'
      });
      slide.addText(`${notAwareBranch.length} (${notAwarePercent}%)`, {
        x: 5.0,
        y: 4.9,
        w: 3.5,
        h: 0.9,
        fontSize: 24,
        bold: true,
        color: 'ef4444',
        align: 'center',
        valign: 'middle'
      });

      // Tudatosság slide
      slide = pres.addSlide();
      slide.addText('Tudatosság', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // EAP knowledge levels - as text stats
      const awarenessLevels = {};
      responses.forEach(r => {
        const level = r.responses?.eap_knowledge || 'Nincs adat';
        awarenessLevels[level] = (awarenessLevels[level] || 0) + 1;
      });

      let yPos = 1.5;
      Object.entries(awarenessLevels).forEach(([level, count], idx) => {
        const percentage = ((count as number / responses.length) * 100).toFixed(1);
        
        slide.addShape(pres.ShapeType.rect, {
          x: 1,
          y: yPos,
          w: 8,
          h: 0.8,
          fill: { color: idx === 0 ? '22c55e' : idx === 1 ? 'f59e0b' : 'ef4444' },
          line: { type: 'none' }
        });
        
        slide.addText(level, {
          x: 1.2,
          y: yPos + 0.1,
          w: 5,
          fontSize: 16,
          color: 'FFFFFF',
          bold: true
        });
        
        slide.addText(`${count} (${percentage}%)`, {
          x: 6.5,
          y: yPos + 0.1,
          w: 2.3,
          fontSize: 16,
          color: 'FFFFFF',
          align: 'right',
          bold: true
        });
        
        yPos += 1.0;
      });

      // Bizalom & Hajlandóság slide
      slide = pres.addSlide();
      slide.addText('Bizalom & Hajlandóság', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // Trust index for users
      const trustScores = usedBranch
        .map(r => r.responses?.u_trust_anonymity)
        .filter(score => typeof score === 'number' && !isNaN(score));
      const avgTrust = trustScores.length > 0 
        ? (trustScores.reduce((a, b) => a + b, 0) / trustScores.length).toFixed(1)
        : 'N/A';

      slide.addText(`${avgTrust}/5`, {
        x: 1.5,
        y: 1.5,
        w: 3,
        h: 1.2,
        fontSize: 48,
        bold: true,
        color: '3572ef',
        align: 'center'
      });
      slide.addText('Átlagos bizalmi index\n(használók)', {
        x: 1.5,
        y: 2.8,
        w: 3,
        fontSize: 16,
        align: 'center'
      });

      // Willingness data
      const willingnessData = {};
      notUsedBranch.forEach(r => {
        const willingness = r.responses?.willingness_to_use || 'Nincs adat';
        willingnessData[willingness] = (willingnessData[willingness] || 0) + 1;
      });

      if (Object.keys(willingnessData).length > 0) {
        const willingnessChartData = [{
          name: 'Hajlandóság',
          labels: Object.keys(willingnessData),
          values: Object.values(willingnessData)
        }];

        slide.addChart(pres.ChartType.pie, willingnessChartData, {
          x: 5.5,
          y: 1.2,
          w: 4,
          h: 3.5,
          showLegend: true,
          showTitle: true,
          title: 'Használati hajlandóság (nem használók)',
          chartColors: ['22c55e', 'f59e0b', 'ef4444']
        });
      }

      // Használat slide
      slide = pres.addSlide();
      slide.addText('Használat', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // Usage stats as text
      const frequencyData = {};
      usedBranch.forEach(r => {
        const freq = r.responses?.u_usage_frequency || 'Nincs adat';
        frequencyData[freq] = (frequencyData[freq] || 0) + 1;
      });

      const topicsData = {};
      usedBranch.forEach(r => {
        const topics = r.responses?.u_usage_topic;
        if (Array.isArray(topics)) {
          topics.forEach(topic => {
            topicsData[topic] = (topicsData[topic] || 0) + 1;
          });
        }
      });

      // Left column: Frequency
      slide.addText('Gyakoriság:', {
        x: 1,
        y: 1.3,
        fontSize: 20,
        bold: true,
        color: '3572ef'
      });

      let yFreq = 1.8;
      Object.entries(frequencyData).slice(0, 5).forEach(([freq, count]) => {
        slide.addText(`${freq}: ${count}`, {
          x: 1,
          y: yFreq,
          w: 3.5,
          fontSize: 16
        });
        yFreq += 0.5;
      });

      // Right column: Topics
      slide.addText('Témakörök:', {
        x: 5.5,
        y: 1.3,
        fontSize: 20,
        bold: true,
        color: '22c55e'
      });

      const topTopics = Object.entries(topicsData)
        .sort(([, a], [, b]) => (b as number) - (a as number))
        .slice(0, 5);

      let yTopic = 1.8;
      topTopics.forEach(([topic, count]) => {
        slide.addText(`${topic}: ${count}`, {
          x: 5.5,
          y: yTopic,
          w: 3.5,
          fontSize: 16
        });
        yTopic += 0.5;
      });

      // Hatás slide
      slide = pres.addSlide();
      slide.addText('Hatás', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // NPS calculation
      const npsScores = usedBranch
        .map(r => r.responses?.u_impact_nps)
        .filter(score => typeof score === 'number' && !isNaN(score));
      
      const promoters = npsScores.filter(score => score >= 9).length;
      const detractors = npsScores.filter(score => score <= 6).length;
      const nps = npsScores.length > 0 
        ? (((promoters - detractors) / npsScores.length) * 100).toFixed(0)
        : 'N/A';

      slide.addText(`${nps}`, {
        x: 1.5,
        y: 1.5,
        w: 3,
        h: 1.2,
        fontSize: 56,
        bold: true,
        color: parseInt(nps) >= 0 ? '22c55e' : 'ef4444',
        align: 'center'
      });
      slide.addText('Net Promoter Score', {
        x: 1.5,
        y: 2.8,
        w: 3,
        fontSize: 16,
        align: 'center'
      });

      // Impact metrics
      const performanceData = {};
      usedBranch.forEach(r => {
        const impact = r.responses?.performance_impact || r.responses?.u_impact_performance || 'Nincs adat';
        performanceData[impact] = (performanceData[impact] || 0) + 1;
      });

      if (Object.keys(performanceData).length > 0) {
        const performanceChartData = [{
          name: 'Hatás',
          labels: Object.keys(performanceData),
          values: Object.values(performanceData)
        }];

        slide.addChart(pres.ChartType.pie, performanceChartData, {
          x: 5.5,
          y: 1.2,
          w: 4,
          h: 3.5,
          showLegend: true,
          showTitle: true,
          title: 'Teljesítményre gyakorolt hatás',
          chartColors: ['22c55e', 'f59e0b', 'ef4444']
        });
      }

      // Motiváció slide
      slide = pres.addSlide();
      slide.addText('Motiváció', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // Motivators for non-users
      const motivatorsData = {};
      notUsedBranch.forEach(r => {
        const motivators = r.responses?.motivators || r.responses?.nu_motivation_what;
        if (Array.isArray(motivators)) {
          motivators.forEach(mot => {
            motivatorsData[mot] = (motivatorsData[mot] || 0) + 1;
          });
        }
      });

      const motivatorsEntries = Object.entries(motivatorsData)
        .sort(([, a], [, b]) => (b as number) - (a as number))
        .slice(0, 5);

      if (motivatorsEntries.length > 0) {
        const motivatorsChartData = [{
          name: 'Motivátorok',
          labels: motivatorsEntries.map(([name]) => name),
          values: motivatorsEntries.map(([, value]) => value as number)
        }];

        slide.addChart(pres.ChartType.bar, motivatorsChartData, {
          x: 0.5,
          y: 1.2,
          w: 9,
          h: 3.5,
          showLegend: false,
          showTitle: true,
          title: 'Top 5 motiváló tényező (nem használók)',
          barDir: 'bar',
          chartColors: ['f59e0b']
        });
      } else {
        slide.addText('Nincs elegendő adat a motivációs elemzéshez', {
          x: 2,
          y: 2.5,
          w: 6,
          fontSize: 18,
          align: 'center',
          color: '666666'
        });
      }

      // Demográfia slide
      slide = pres.addSlide();
      slide.addText('Demográfia', {
        x: 0.5,
        y: 0.3,
        fontSize: 32,
        bold: true,
        color: '3572ef'
      });

      // Gender distribution - as text stats
      const genderData = {};
      responses.forEach(r => {
        const gender = r.responses?.gender || 'Nincs adat';
        genderData[gender] = (genderData[gender] || 0) + 1;
      });

      slide.addText('Nem szerinti megoszlás:', {
        x: 1,
        y: 1.3,
        fontSize: 20,
        bold: true,
        color: '3572ef'
      });

      let yGender = 1.8;
      Object.entries(genderData).forEach(([gender, count]) => {
        const percentage = ((count as number / responses.length) * 100).toFixed(1);
        slide.addText(`${gender}: ${count} (${percentage}%)`, {
          x: 1,
          y: yGender,
          w: 3.5,
          fontSize: 16
        });
        yGender += 0.5;
      });

      // Age distribution - as text stats
      const ageData = {};
      responses.forEach(r => {
        const age = r.responses?.age || 'Nincs adat';
        ageData[age] = (ageData[age] || 0) + 1;
      });

      slide.addText('Korosztály szerinti megoszlás:', {
        x: 5.5,
        y: 1.3,
        fontSize: 20,
        bold: true,
        color: '22c55e'
      });

      let yAge = 1.8;
      Object.entries(ageData).forEach(([age, count]) => {
        const percentage = ((count as number / responses.length) * 100).toFixed(1);
        slide.addText(`${age}: ${count} (${percentage}%)`, {
          x: 5.5,
          y: yAge,
          w: 3.5,
          fontSize: 16
        });
        yAge += 0.5;
      });

      // Save presentation
      const fileName = `eap_pulse_jelentes_${formatAuditName(selectedAudit)}_${Date.now()}.pptx`;
      await pres.writeFile({ fileName });
      
      // Save download to history
      await saveExportToHistory(selectedAuditId, fileName, 'PowerPoint');
      
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
    
    // Save download to history
    await saveExportToHistory(selectedAuditId, `${fileName}.png`, 'PNG');
    
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
    
    // Set the source to the reports page with auto-export parameters
    exportIframe.src = `/?section=reports&sub=${chart.tab}&autoExport=${cardId}&fileName=${fileName}&inIframe=true`;
    
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
        'Kategória': r.employee_metadata?.branch === 'used' ? 'Használók: A program aktív felhasználói' : r.employee_metadata?.branch === 'not_used' ? 'Nem használók: Akik eddig nem vették igénybe a programot' : 'Nem tudott róla',
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
        XLSX.utils.book_append_sheet(wb, wsUserAware, 'Használók: A program aktív felhasználói');
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
        XLSX.utils.book_append_sheet(wb, wsUserTrust, 'Használók: A program aktív felhasználói - Bizalom');
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
        XLSX.utils.book_append_sheet(wb, wsUserUsage, 'Használók: A program aktív felhasználói - Használat');
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
        XLSX.utils.book_append_sheet(wb, wsUserImpact, 'Használók: A program aktív felhasználói - Hatás');
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
        XLSX.utils.book_append_sheet(wb, wsUserPref, 'Használók: A program aktív felhasználói - Preferenciák');
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
        XLSX.utils.book_append_sheet(wb, wsNonUsers, 'Nem használók: Akik eddig nem vették igénybe a programot');
      }

      const fileName = `eap_pulse_export_${selectedAuditId}_${Date.now()}.xlsx`;
      XLSX.writeFile(wb, fileName);
      
      // Save download to history
      await saveExportToHistory(selectedAuditId, fileName, 'Excel');
      
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
      const fileName = `audit_export_${selectedAuditId}_${Date.now()}.csv`;
      link.setAttribute('href', url);
      link.setAttribute('download', fileName);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      // Save download to history
      await saveExportToHistory(selectedAuditId, fileName, 'CSV');

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
    <div className="space-y-6 pt-20 md:pt-0">
      <div className="space-y-4 mb-6">
        <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div className="flex-1">
            <h2 className="text-2xl font-bold mb-2">Export & Jelentések</h2>
            <p className="text-muted-foreground">Felmérések exportálása különböző formátumokban</p>
          </div>
        </div>
        
        {audits.length > 0 && (
          <div className="flex flex-col md:flex-row md:items-end gap-4">
            <div className="flex flex-col md:flex-row gap-4 md:ml-auto">
              {/* Company selector for partner users */}
              {packageType === 'partner' && companies.length > 0 && (
                <div className="flex-1 md:max-w-[300px]">
                  <label className="text-xs text-muted-foreground mb-1.5 block">
                    Ügyfélcég szűrése
                  </label>
                  <Select value={selectedCompanyId} onValueChange={setSelectedCompanyId}>
                    <SelectTrigger className="w-full">
                      <SelectValue placeholder="Válassz ügyfélcéget" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Összes ügyfélcég</SelectItem>
                      {companies.map((company) => (
                        <SelectItem key={company.id} value={company.id}>
                          {company.company_name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              )}

              {/* Audit selector - only show if company is selected for partner users */}
              {(packageType !== 'partner' || (packageType === 'partner' && selectedCompanyId && selectedCompanyId !== 'all')) && (
                <div className="flex-1 md:max-w-[300px]">
                  <label className="text-xs text-muted-foreground mb-1.5 block">
                    Felmérés kiválasztása
                  </label>
                  <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
                    <SelectTrigger className="w-full">
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
              )}
            </div>
          </div>
        )}
      </div>

      <div className={`grid gap-6 ${packageType === 'starter' ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'}`}>

        {/* PowerPoint - Only for Professional, Enterprise and Partner */}
        {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
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
                  <li>Preferenciák</li>
                  <li>Demográfia</li>
                </ul>
              </div>
              <Button 
                onClick={handleExportPPT} 
                disabled={exporting || !selectedAuditId}
                variant="outline"
              >
                <Download className="mr-2 h-4 w-4" />
                PowerPoint Letöltése
              </Button>
            </CardContent>
          </Card>
        )}

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
                {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                  <>
                    <li>Preferenciák - Szakértő típus, csatorna preferenciák (használók és nem használók)</li>
                    <li>Demográfia - Nem, korcsoport, kategória megoszlások</li>
                  </>
                )}
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


        {/* CSV - Only for Professional, Enterprise and Partner */}
        {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
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
        )}

        <Card className={`flex flex-col ${(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') ? 'lg:col-span-3' : ''}`}>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <ImageIcon className="h-5 w-5" />
              PNG Grafikonok
            </CardTitle>
            <CardDescription>
              Letölthető grafikonok témakörök szerint
            </CardDescription>
          </CardHeader>
          <CardContent className="flex-1">
            <Tabs defaultValue="overview" className="w-full">
              <div className="overflow-x-auto mb-4">
                <TabsList className="inline-flex w-auto min-w-full">
                  <TabsTrigger value="overview" className="flex-shrink-0">Összefoglaló</TabsTrigger>
                  <TabsTrigger value="awareness" className="flex-shrink-0">Ismertség</TabsTrigger>
                  <TabsTrigger value="trust" className="flex-shrink-0">Bizalom</TabsTrigger>
                  <TabsTrigger value="usage" className="flex-shrink-0">Használat</TabsTrigger>
                  <TabsTrigger value="impact" className="flex-shrink-0">Hatás</TabsTrigger>
                  {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                    <>
                      <TabsTrigger value="motivation" className="flex-shrink-0">Preferenciák</TabsTrigger>
                      <TabsTrigger value="demographics" className="flex-shrink-0">Demográfia</TabsTrigger>
                    </>
                  )}
                </TabsList>
              </div>

              <TabsContent value="overview" className="space-y-2">
                <p className="text-sm text-muted-foreground mb-4">Gyors áttekintés - főbb mutatók</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {exportableCharts.filter(c => c.tab === 'overview').map(chart => (
                    <Button 
                      key={chart.id}
                      variant="outline" 
                      onClick={() => handleExportPNG(chart.id, chart.fileName)}
                      disabled={exporting || !selectedAuditId}
                      className="justify-start truncate"
                    >
                      <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                      <span className="truncate">{chart.name}</span>
                    </Button>
                  ))}
                </div>
              </TabsContent>

              <TabsContent value="awareness" className="space-y-2">
                <p className="text-sm text-muted-foreground mb-4">Ismertség és awareness mutatók</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {exportableCharts.filter(c => c.tab === 'awareness').map(chart => (
                    <Button 
                      key={chart.id}
                      variant="outline" 
                      onClick={() => handleExportPNG(chart.id, chart.fileName)}
                      disabled={exporting || !selectedAuditId}
                      className="justify-start truncate"
                    >
                      <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                      <span className="truncate">{chart.name}</span>
                    </Button>
                  ))}
                </div>
              </TabsContent>

              <TabsContent value="trust" className="space-y-2">
                <p className="text-sm text-muted-foreground mb-4">Bizalom és hajlandóság mutatók</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {exportableCharts.filter(c => c.tab === 'trust').map(chart => (
                    <Button 
                      key={chart.id}
                      variant="outline" 
                      onClick={() => handleExportPNG(chart.id, chart.fileName)}
                      disabled={exporting || !selectedAuditId}
                      className="justify-start truncate"
                    >
                      <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                      <span className="truncate">{chart.name}</span>
                    </Button>
                  ))}
                </div>
              </TabsContent>

              <TabsContent value="usage" className="space-y-2">
                <p className="text-sm text-muted-foreground mb-4">Használati statisztikák</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {exportableCharts.filter(c => c.tab === 'usage').map(chart => (
                    <Button 
                      key={chart.id}
                      variant="outline" 
                      onClick={() => handleExportPNG(chart.id, chart.fileName)}
                      disabled={exporting || !selectedAuditId}
                      className="justify-start truncate"
                    >
                      <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                      <span className="truncate">{chart.name}</span>
                    </Button>
                  ))}
                </div>
              </TabsContent>

              <TabsContent value="impact" className="space-y-2">
                <p className="text-sm text-muted-foreground mb-4">Hatás mutatók</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {exportableCharts.filter(c => c.tab === 'impact').map(chart => (
                    <Button 
                      key={chart.id}
                      variant="outline" 
                      onClick={() => handleExportPNG(chart.id, chart.fileName)}
                      disabled={exporting || !selectedAuditId}
                      className="justify-start truncate"
                    >
                      <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                      <span className="truncate">{chart.name}</span>
                    </Button>
                  ))}
                </div>
              </TabsContent>


              {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                <TabsContent value="motivation" className="space-y-2">
                  <p className="text-sm text-muted-foreground mb-4">Preferenciák - használók és nem használók</p>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    {exportableCharts.filter(c => c.tab === 'motivation').map(chart => (
                      <Button 
                        key={chart.id}
                        variant="outline" 
                        onClick={() => handleExportPNG(chart.id, chart.fileName)}
                        disabled={exporting || !selectedAuditId}
                        className="justify-start truncate"
                      >
                        <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                        <span className="truncate">{chart.name}</span>
                      </Button>
                    ))}
                  </div>
                </TabsContent>
              )}


              {(packageType === 'professional' || packageType === 'enterprise' || packageType === 'partner') && (
                <TabsContent value="demographics" className="space-y-2">
                  <p className="text-sm text-muted-foreground mb-4">Demográfiai megoszlások</p>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    {exportableCharts.filter(c => c.tab === 'demographics').map(chart => (
                      <Button 
                        key={chart.id}
                        variant="outline" 
                        onClick={() => handleExportPNG(chart.id, chart.fileName)}
                        disabled={exporting || !selectedAuditId}
                        className="justify-start truncate"
                      >
                        <Download className="mr-2 h-4 w-4 flex-shrink-0" />
                        <span className="truncate">{chart.name}</span>
                      </Button>
                    ))}
                  </div>
                </TabsContent>
              )}


            </Tabs>
          </CardContent>
        </Card>
      </div>

      {/* Export History */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <History className="h-5 w-5" />
            Export Előzmények
          </CardTitle>
          <CardDescription>
            Letöltött fájlok listája
          </CardDescription>
        </CardHeader>
        <CardContent>
          {loadingHistory ? (
            <p className="text-muted-foreground text-center py-8">Betöltés...</p>
          ) : exportHistory.length === 0 ? (
            <p className="text-muted-foreground text-center py-8">Még nincs export előzmény</p>
          ) : (
            <div className="space-y-2">
              {exportHistory.map((item) => (
                <div 
                  key={item.id} 
                  className="flex items-center justify-between p-3 border rounded-lg hover:bg-muted/50 transition-colors"
                >
                  <div className="flex-1 min-w-0 pr-4">
                    <p className="font-medium truncate">{item.file_name}</p>
                    <p className="text-sm text-muted-foreground truncate">
                      {item.audit_name} • {item.file_type} • {new Date(item.created_at).toLocaleString('hu-HU')}
                    </p>
                  </div>
                  <Button
                    variant="ghost"
                    size="icon"
                    onClick={() => deleteHistoryItem(item.id)}
                    className="text-destructive hover:text-destructive"
                  >
                    <Trash2 className="h-4 w-4" />
                  </Button>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

    </div>
  );
};

export default Export;
