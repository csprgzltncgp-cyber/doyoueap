import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit } from '@/lib/auditUtils';
import { Calendar, Mail, MousePointerClick, CheckCircle, Clock, Copy, ExternalLink, Link, Trash2, Trophy, FileDown, RefreshCw } from 'lucide-react';
import { QRCodeSVG } from 'qrcode.react';
import jsPDF from 'jspdf';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";

// NOTE: "Audit" in code represents "Felmérés" (EAP Pulse Survey) in the UI
interface AuditMetrics {
  audit: StandardAudit;
  emailsSent?: number;
  emailsOpened?: number;
  responsesCount: number;
  daysRemaining: number;
  completionPercentage: number;
  totalEmployees?: number;
}

const RunningAudits = () => {
  const [audits, setAudits] = useState<AuditMetrics[]>([]);
  const [loading, setLoading] = useState(true);
  const [drawResult, setDrawResult] = useState<{ token: string; count: number } | null>(null);

  useEffect(() => {
    fetchRunningAudits();
  }, []);

  const fetchRunningAudits = async () => {
    try {
      // Fetch active audits
      const { data: auditsData, error: auditsError } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at, access_token, gift_id, draw_mode, draw_status, status')
        .eq('is_active', true)
        .order('start_date', { ascending: false });

      if (auditsError) throw auditsError;

      if (!auditsData || auditsData.length === 0) {
        setAudits([]);
        setLoading(false);
        return;
      }

      // Fetch responses for each audit
      const metricsPromises = auditsData.map(async (audit) => {
        console.log('Fetching responses for audit:', audit.id);
        const { data: responses, error: responsesError } = await supabase
          .from('audit_responses')
          .select('id')
          .eq('audit_id', audit.id);

        console.log('Responses data:', responses);
        console.log('Responses error:', responsesError);

        if (responsesError) {
          console.error('Error fetching responses:', responsesError);
          return null;
        }

        // Calculate days remaining
        const now = new Date();
        const expiresAt = audit.expires_at ? new Date(audit.expires_at) : null;
        const daysRemaining = expiresAt 
          ? Math.max(0, Math.ceil((expiresAt.getTime() - now.getTime()) / (1000 * 60 * 60 * 24)))
          : 0;

        // For now, we'll use placeholder values for email metrics
        // These would come from a separate tracking system in production
        const metrics: AuditMetrics = {
          audit,
          responsesCount: responses?.length || 0,
          daysRemaining,
          completionPercentage: 0, // Will be calculated based on target
        };

        // Add email-specific metrics for tokenes mode
        if (audit.access_mode === 'tokenes') {
          metrics.emailsSent = 0; // TODO: Implement email tracking
          metrics.emailsOpened = 0; // TODO: Implement email tracking
          metrics.totalEmployees = 0; // TODO: Get from company profile
        }

        return metrics;
      });

      const metricsData = (await Promise.all(metricsPromises)).filter(Boolean) as AuditMetrics[];
      setAudits(metricsData);
    } catch (error) {
      console.error('Error fetching running assessments:', error);
      toast.error('Hiba történt a futó felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const getAccessModeLabel = (mode: string) => {
    const modeMap: Record<string, string> = {
      'tokenes': 'Egyedi tokenes link',
      'public_link': 'Nyilvános link',
      'qr_code': 'QR kód',
    };
    return modeMap[mode] || mode;
  };

  const getAccessModeIcon = (mode: string) => {
    switch (mode) {
      case 'tokenes':
        return <Mail className="h-4 w-4" />;
      case 'public_link':
        return <MousePointerClick className="h-4 w-4" />;
      case 'qr_code':
        return <CheckCircle className="h-4 w-4" />;
      default:
        return null;
    }
  };

  const getSurveyUrl = (accessToken: string) => {
    return `${window.location.origin}/survey/${accessToken}`;
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.success('Link vágólapra másolva!');
  };

  const handleDelete = async (auditId: string) => {
    try {
      const { error } = await supabase
        .from('audits')
        .update({ is_active: false })
        .eq('id', auditId);

      if (error) throw error;

      toast.success('Felmérés sikeresen törölve');
      fetchRunningAudits();
    } catch (error) {
      console.error('Error deleting audit:', error);
      toast.error('Hiba történt a felmérés törlésekor');
    }
  };

  const handleRunDraw = async (auditId: string) => {
    try {
      const { data, error } = await supabase.functions.invoke('run-draw', {
        body: { audit_id: auditId },
      });

      if (error) throw error;

      // Fetch the response count for this audit
      const { data: responses } = await supabase
        .from('audit_responses')
        .select('id')
        .eq('audit_id', auditId);

      setDrawResult({
        token: data.winner_token,
        count: responses?.length || 0
      });
      fetchRunningAudits();
    } catch (error: any) {
      console.error('Error running draw:', error);
      toast.error(error.message || 'Hiba történt a sorsolás során');
    }
  };

  const downloadDrawReport = async (auditId: string) => {
    try {
      const { data: draw, error } = await supabase
        .from('draws')
        .select('*')
        .eq('audit_id', auditId)
        .order('ts', { ascending: false })
        .limit(1)
        .single();

      if (error) throw error;
      if (!draw) {
        toast.error('Nincs elérhető sorsolási jegyzőkönyv');
        return;
      }

      const doc = new jsPDF();
      
      // Header with larger font and color
      doc.setFontSize(24);
      doc.setFont(undefined, 'bold');
      doc.setTextColor(14, 165, 233); // Primary blue color
      doc.text('SORSOLÁSI JEGYZŐKÖNYV', 105, 25, { align: 'center' });
      
      // Subheader
      doc.setFontSize(11);
      doc.setFont(undefined, 'normal');
      doc.setTextColor(100, 100, 100);
      doc.text('EAP Pulse - Auditálható és átlátható sorsolási rendszer', 105, 35, { align: 'center' });
      
      // Reset color for content
      doc.setTextColor(0, 0, 0);
      
      // Draw a separator line
      doc.setDrawColor(14, 165, 233);
      doc.setLineWidth(0.5);
      doc.line(20, 42, 190, 42);
      
      // Draw details with better formatting
      let yPos = 55;
      
      // Company name
      doc.setFontSize(14);
      doc.setFont(undefined, 'bold');
      doc.text('Cég neve:', 20, yPos);
      doc.setFontSize(13);
      doc.setFont(undefined, 'normal');
      doc.text(draw.company_name, 20, yPos + 7);
      yPos += 22;
      
      // Draw date
      doc.setFontSize(14);
      doc.setFont(undefined, 'bold');
      doc.text('Sorsolás időpontja:', 20, yPos);
      doc.setFontSize(13);
      doc.setFont(undefined, 'normal');
      doc.text(new Date(draw.ts).toLocaleString('hu-HU', { 
        dateStyle: 'long', 
        timeStyle: 'medium' 
      }), 20, yPos + 7);
      yPos += 22;
      
      // Candidates count
      doc.setFontSize(14);
      doc.setFont(undefined, 'bold');
      doc.text('Jelentkezők száma:', 20, yPos);
      doc.setFontSize(16);
      doc.setFont(undefined, 'bold');
      doc.setTextColor(14, 165, 233);
      doc.text(draw.candidates_count.toString(), 20, yPos + 8);
      doc.setTextColor(0, 0, 0);
      yPos += 25;
      
      // Winner token - highlighted
      doc.setFillColor(14, 165, 233);
      doc.rect(15, yPos - 5, 180, 25, 'F');
      doc.setFontSize(14);
      doc.setFont(undefined, 'bold');
      doc.setTextColor(255, 255, 255);
      doc.text('NYERTES TOKEN:', 20, yPos + 3);
      doc.setFontSize(18);
      doc.setFont(undefined, 'bold');
      doc.text(draw.winner_token, 20, yPos + 12);
      doc.setTextColor(0, 0, 0);
      yPos += 35;
      
      // Separator
      doc.setDrawColor(200, 200, 200);
      doc.setLineWidth(0.3);
      doc.line(20, yPos, 190, yPos);
      yPos += 10;
      
      // Seed info for transparency
      doc.setFontSize(12);
      doc.setFont(undefined, 'bold');
      doc.text('Kriptográfiai seed (audit célra):', 20, yPos);
      yPos += 7;
      doc.setFontSize(9);
      doc.setFont(undefined, 'normal');
      doc.setTextColor(60, 60, 60);
      doc.text(draw.seed, 20, yPos, { maxWidth: 170 });
      
      // Footer
      doc.setFontSize(9);
      doc.setTextColor(100, 100, 100);
      doc.text(`Jegyzőkönyv készítve: ${new Date().toLocaleString('hu-HU')}`, 105, 280, { align: 'center' });
      doc.setFontSize(8);
      doc.text('A sorsolás auditálható és visszakereshető az EAP Pulse rendszerben', 105, 286, { align: 'center' });
      
      doc.save(`sorsolas-jegyzokonyv-${draw.id.substring(0, 8)}.pdf`);
      toast.success('PDF letöltve!');
    } catch (error) {
      console.error('Error downloading draw report:', error);
      toast.error('Hiba történt a jegyzőkönyv letöltésekor');
    }
  };

  if (loading) {
    return (
      <div className="p-8 flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  if (audits.length === 0) {
    return (
      <div className="space-y-6">
        <div className="mb-6">
          <h2 className="text-2xl font-bold mb-2">Futó Felmérések</h2>
          <p className="text-muted-foreground text-sm">
            Jelenleg nincsenek futó felmérések.
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Draw Success Dialog */}
      <AlertDialog open={!!drawResult} onOpenChange={() => setDrawResult(null)}>
        <AlertDialogContent className="max-w-2xl">
          <AlertDialogHeader className="text-center">
            <div className="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-primary/10">
              <Trophy className="h-10 w-10 text-primary" />
            </div>
            <AlertDialogTitle className="text-3xl font-bold text-center">
              Gratulálunk!
            </AlertDialogTitle>
            <AlertDialogDescription className="text-center space-y-4 pt-4">
              <p className="text-lg">
                A sorsolás sikeresen megtörtént <strong>{drawResult?.count}</strong> jelentkező közül!
              </p>
              <div className="bg-primary/5 border-2 border-primary/20 rounded-lg p-6 my-4">
                <p className="text-sm font-medium text-muted-foreground mb-2">Nyertes token:</p>
                <p className="text-2xl font-bold text-primary break-all">
                  {drawResult?.token}
                </p>
              </div>
              <p className="text-sm text-muted-foreground">
                A nyertes értesítése és a részletes jegyzőkönyv letöltése a "Jegyzőkönyv" gombbal történhet.
              </p>
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter className="sm:justify-center">
            <AlertDialogAction className="px-8">Rendben</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      <div className="mb-6">
        <h2 className="text-2xl font-bold mb-2">Futó Felmérések</h2>
        <p className="text-muted-foreground text-sm">
          Áttekintés az aktív felmérésekről és azok előrehaladásáról
        </p>
      </div>

      <div className="grid gap-6">
        {audits.map((metrics) => (
          <Card key={metrics.audit.id}>
            <CardHeader>
              <div className="flex items-start justify-between">
                <div>
                  <CardTitle className="text-xl mb-2">
                    {formatAuditName(metrics.audit)}
                  </CardTitle>
                  <div className="flex items-center gap-2 mt-2">
                    <Badge variant="outline" className="gap-1">
                      {getAccessModeIcon(metrics.audit.access_mode)}
                      {getAccessModeLabel(metrics.audit.access_mode)}
                    </Badge>
                    <Badge variant="secondary" className="gap-1">
                      <Clock className="h-3 w-3" />
                      {metrics.daysRemaining} nap van hátra
                    </Badge>
                    {metrics.audit.recurrence_config?.enabled && (
                      <Badge variant="outline" className="gap-1 bg-blue-50 text-blue-700 border-blue-300">
                        <RefreshCw className="h-3 w-3" />
                        Ismétlődő
                      </Badge>
                    )}
                    {metrics.audit.gift_id && (
                      <Badge 
                        variant="outline" 
                        className={`gap-1 ${
                          metrics.audit.draw_status === 'completed' 
                            ? 'bg-green-50 text-green-700 border-green-300' 
                            : 'bg-yellow-50 text-yellow-700 border-yellow-300'
                        }`}
                      >
                        <Trophy className="h-3 w-3" />
                        {metrics.audit.draw_status === 'completed' 
                          ? 'Sorsolt' 
                          : metrics.audit.draw_mode === 'auto' 
                            ? 'Automatikus sorsolással' 
                            : 'Manuális sorsolással'
                        }
                      </Badge>
                    )}
                  </div>
                </div>
                <div className="flex gap-2">
                  {metrics.audit.gift_id && metrics.audit.draw_status === 'completed' && (
                    <Button 
                      variant="outline" 
                      size="sm" 
                      className="gap-2"
                      onClick={() => downloadDrawReport(metrics.audit.id)}
                    >
                      <FileDown className="h-4 w-4" />
                      Jegyzőkönyv
                    </Button>
                  )}
                  {metrics.audit.gift_id && 
                   metrics.audit.draw_mode === 'manual' && 
                   metrics.audit.draw_status === 'none' && 
                   metrics.responsesCount > 0 && 
                   (metrics.audit.status === 'closed' || (metrics.audit.expires_at && new Date(metrics.audit.expires_at) < new Date())) && (
                    <AlertDialog>
                      <AlertDialogTrigger asChild>
                        <Button variant="default" size="sm" className="gap-2">
                          <Trophy className="h-4 w-4" />
                          Sorsolás
                        </Button>
                      </AlertDialogTrigger>
                      <AlertDialogContent>
                        <AlertDialogHeader>
                          <AlertDialogTitle>Sorsolás indítása</AlertDialogTitle>
                          <AlertDialogDescription>
                            {metrics.responsesCount} kitöltő közül fogsz nyertest sorsolni. 
                            A sorsolás eredménye visszavonhatatlan és auditálható. 
                            Biztosan elindítod a sorsolást?
                          </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                          <AlertDialogCancel>Mégse</AlertDialogCancel>
                          <AlertDialogAction onClick={() => handleRunDraw(metrics.audit.id)}>
                            Sorsolás indítása
                          </AlertDialogAction>
                        </AlertDialogFooter>
                      </AlertDialogContent>
                    </AlertDialog>
                  )}
                  <AlertDialog>
                    <AlertDialogTrigger asChild>
                      <Button variant="outline" size="sm" className="gap-2">
                        <Trash2 className="h-4 w-4" />
                        Törlés
                      </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>Biztosan törölni szeretnéd?</AlertDialogTitle>
                        <AlertDialogDescription>
                          Ez a felmérés inaktívvá válik és nem jelenik meg a futó felmérések között. 
                          A már kitöltött válaszok megmaradnak az adatbázisban.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogCancel>Mégse</AlertDialogCancel>
                        <AlertDialogAction onClick={() => handleDelete(metrics.audit.id)}>
                          Törlés
                        </AlertDialogAction>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <Tabs defaultValue="overview" className="w-full">
                <TabsList className="grid w-full grid-cols-3">
                  <TabsTrigger value="overview">Áttekintés</TabsTrigger>
                  <TabsTrigger value="access">Hozzáférés</TabsTrigger>
                  <TabsTrigger value="details">Részletek</TabsTrigger>
                </TabsList>

                <TabsContent value="overview" className="space-y-4 mt-4">
                  {/* Email metrics - only for tokenes mode */}
                  {metrics.audit.access_mode === 'tokenes' && (
                    <div className="grid grid-cols-2 gap-4">
                      <Card className="bg-muted/50">
                        <CardHeader className="pb-3">
                          <CardDescription className="flex items-center gap-2">
                            <Mail className="h-4 w-4" />
                            Kézbesített emailek
                          </CardDescription>
                        </CardHeader>
                        <CardContent>
                          <div className="text-2xl font-bold">
                            {metrics.emailsSent || 0}
                            {metrics.totalEmployees && (
                              <span className="text-sm font-normal text-muted-foreground">
                                {' '}/ {metrics.totalEmployees}
                              </span>
                            )}
                          </div>
                          {metrics.totalEmployees && (
                            <Progress 
                              value={(metrics.emailsSent || 0) / metrics.totalEmployees * 100} 
                              className="mt-2 h-3"
                            />
                          )}
                        </CardContent>
                      </Card>

                      <Card className="bg-muted/50">
                        <CardHeader className="pb-3">
                          <CardDescription className="flex items-center gap-2">
                            <MousePointerClick className="h-4 w-4" />
                            Link megnyitások
                          </CardDescription>
                        </CardHeader>
                        <CardContent>
                          <div className="text-2xl font-bold">
                            {metrics.emailsOpened || 0}
                            {metrics.emailsSent && (
                              <span className="text-sm font-normal text-muted-foreground">
                                {' '}/ {metrics.emailsSent}
                              </span>
                            )}
                          </div>
                          {metrics.emailsSent && metrics.emailsSent > 0 && (
                            <Progress 
                              value={(metrics.emailsOpened || 0) / metrics.emailsSent * 100} 
                              className="mt-2 h-3"
                            />
                          )}
                        </CardContent>
                      </Card>
                    </div>
                  )}

                  {/* Response count - for all modes */}
                  <Card className="bg-primary/5 border-primary/20">
                    <CardHeader className="pb-3">
                      <CardDescription className="flex items-center gap-2">
                        <CheckCircle className="h-4 w-4" />
                        Kitöltött kérdőívek
                      </CardDescription>
                    </CardHeader>
                    <CardContent>
                      <div className="text-3xl font-bold text-primary">
                        {metrics.responsesCount}
                      </div>
                      {metrics.audit.access_mode === 'tokenes' && metrics.totalEmployees && (
                        <>
                          <Progress 
                            value={metrics.responsesCount / metrics.totalEmployees * 100} 
                            className="mt-3 h-3"
                          />
                          <p className="text-sm text-muted-foreground mt-2">
                            {((metrics.responsesCount / metrics.totalEmployees) * 100).toFixed(1)}% kitöltöttség
                          </p>
                        </>
                      )}
                    </CardContent>
                  </Card>
                </TabsContent>

                <TabsContent value="access" className="space-y-4 mt-4">
                  {metrics.audit.access_token && (
                    <>
                      <div className="space-y-3">
                        <div className="flex items-center gap-2 text-sm font-medium">
                          <Link className="h-4 w-4" />
                          Felmérés link
                        </div>
                        <div className="flex items-center gap-2">
                          <code className="flex-1 px-3 py-2 bg-muted rounded text-sm overflow-x-auto">
                            {getSurveyUrl(metrics.audit.access_token)}
                          </code>
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => copyToClipboard(getSurveyUrl(metrics.audit.access_token))}
                          >
                            <Copy className="h-4 w-4" />
                          </Button>
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => window.open(getSurveyUrl(metrics.audit.access_token), '_blank')}
                          >
                            <ExternalLink className="h-4 w-4" />
                          </Button>
                        </div>
                      </div>
                      
                      {metrics.audit.access_mode === 'qr_code' && (
                        <div className="flex flex-col items-center gap-4 pt-4 border-t">
                          <div className="text-sm font-medium">QR Kód</div>
                          <div className="p-4 bg-white rounded-lg shadow-sm">
                            <QRCodeSVG 
                              value={getSurveyUrl(metrics.audit.access_token)} 
                              size={200}
                              level="H"
                              includeMargin={true}
                            />
                          </div>
                          <p className="text-xs text-muted-foreground text-center max-w-xs">
                            Szkenneld be ezt a QR kódot mobileszközzel a felmérés megnyitásához
                          </p>
                        </div>
                      )}
                    </>
                  )}
                </TabsContent>

                <TabsContent value="details" className="space-y-4 mt-4">
                  <div className="space-y-3">
                    <div className="flex items-center gap-2 text-sm">
                      <Calendar className="h-4 w-4 text-muted-foreground" />
                      <span className="font-medium">Kezdés:</span>
                      <span className="text-muted-foreground">
                        {new Date(metrics.audit.start_date).toLocaleDateString('hu-HU', {
                          year: 'numeric',
                          month: 'long',
                          day: 'numeric',
                        })}
                      </span>
                    </div>
                    
                    {metrics.audit.expires_at && (
                      <div className="flex items-center gap-2 text-sm">
                        <Calendar className="h-4 w-4 text-muted-foreground" />
                        <span className="font-medium">Lejárat:</span>
                        <span className="text-muted-foreground">
                          {new Date(metrics.audit.expires_at).toLocaleDateString('hu-HU', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          })}
                        </span>
                      </div>
                    )}
                    
                    <div className="flex items-center gap-2 text-sm">
                      <Clock className="h-4 w-4 text-muted-foreground" />
                      <span className="font-medium">Hátralévő idő:</span>
                      <span className="text-muted-foreground">
                        {metrics.daysRemaining} nap
                      </span>
                    </div>
                  </div>
                </TabsContent>
              </Tabs>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
};

export default RunningAudits;
