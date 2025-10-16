import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit } from '@/lib/auditUtils';
import { Calendar as CalendarIcon, CalendarDays, Mail, MousePointerClick, CheckCircle, Clock, Copy, ExternalLink, Link, Trash2, Trophy, FileDown, RefreshCw, Edit } from 'lucide-react';
import { QRCodeSVG } from 'qrcode.react';
import jsPDF from 'jspdf';
import { format, addDays } from 'date-fns';
import { hu } from 'date-fns/locale';
import { cn } from '@/lib/utils';
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
  giftName?: string;
}

const RunningAudits = () => {
  const [audits, setAudits] = useState<AuditMetrics[]>([]);
  const [loading, setLoading] = useState(true);
  const [drawResult, setDrawResult] = useState<{ token: string; count: number } | null>(null);
  const [activeTab, setActiveTab] = useState<'active' | 'expired'>('active');
  const [subscriptionPackage, setSubscriptionPackage] = useState<string | null>(null);
  
  // Edit dialogs state
  const [editingExpiryAuditId, setEditingExpiryAuditId] = useState<string | null>(null);
  const [editingRecurrenceAuditId, setEditingRecurrenceAuditId] = useState<string | null>(null);
  const [editingGiftAuditId, setEditingGiftAuditId] = useState<string | null>(null);
  const [newExpiryDate, setNewExpiryDate] = useState<Date | undefined>();
  const [isCalendarOpen, setIsCalendarOpen] = useState(false);
  const [recurrenceSettings, setRecurrenceSettings] = useState({
    enabled: false,
    frequency: 'quarterly' as 'quarterly' | 'biannually' | 'annually',
  });
  const [selectedGiftId, setSelectedGiftId] = useState<string | null>(null);
  const [availableGifts, setAvailableGifts] = useState<Array<{ id: string; name: string }>>([]);

  useEffect(() => {
    fetchRunningAudits();
    fetchGifts();
  }, []);

  const fetchGifts = async () => {
    try {
      const { data, error } = await supabase
        .from('gifts')
        .select('id, name')
        .eq('is_active', true)
        .order('name');

      if (error) throw error;
      setAvailableGifts(data || []);
    } catch (error) {
      console.error('Error fetching gifts:', error);
    }
  };

  const fetchRunningAudits = async () => {
    try {
      // Fetch user's profile to get employee count and company name
      const { data: { user } } = await supabase.auth.getUser();
      const { data: profile } = await supabase
        .from('profiles')
        .select('employee_count, company_name')
        .eq('id', user?.id)
        .single();

      // Fetch company subscription package
      if (profile?.company_name) {
        const { data: companyData } = await supabase
          .from('companies')
          .select('subscription_package')
          .eq('company_name', profile.company_name)
          .maybeSingle();
        
        if (companyData) {
          setSubscriptionPackage(companyData.subscription_package);
        }
      }

      // Fetch active audits with company info for partners
      const { data: auditsData, error: auditsError } = await supabase
        .from('audits')
        .select(`
          id, 
          start_date, 
          program_name, 
          access_mode, 
          recurrence_config, 
          is_active, 
          expires_at, 
          access_token, 
          gift_id, 
          draw_mode, 
          draw_status, 
          status, 
          target_responses, 
          email_count,
          company_name,
          partner_company_id
        `)
        .eq('is_active', true)
        .order('start_date', { ascending: false });

      if (auditsError) throw auditsError;

      if (!auditsData || auditsData.length === 0) {
        setAudits([]);
        setLoading(false);
        return;
      }

      // Parse employee count from profile (format: "10-50" -> use upper bound)
      let employeeCount: number | null = null;
      if (profile?.employee_count) {
        const match = profile.employee_count.match(/(\d+)-(\d+)/);
        if (match) {
          employeeCount = parseInt(match[2]); // Use upper bound
        } else {
          const singleNumber = parseInt(profile.employee_count);
          if (!isNaN(singleNumber)) {
            employeeCount = singleNumber;
          }
        }
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

        // Fetch gift name if gift_id exists
        let giftName: string | undefined;
        if (audit.gift_id) {
          const { data: giftData } = await supabase
            .from('gifts')
            .select('name')
            .eq('id', audit.gift_id)
            .single();
          giftName = giftData?.name;
        }

        // Calculate days remaining
        const now = new Date();
        const expiresAt = audit.expires_at ? new Date(audit.expires_at) : null;
        const daysRemaining = expiresAt 
          ? Math.max(0, Math.ceil((expiresAt.getTime() - now.getTime()) / (1000 * 60 * 60 * 24)))
          : 0;

        const metrics: AuditMetrics = {
          audit,
          responsesCount: responses?.length || 0,
          daysRemaining,
          completionPercentage: 0,
          giftName,
        };

        // Set totalEmployees based on priority
        if (employeeCount) {
          metrics.totalEmployees = employeeCount;
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

  const handleUpdateExpiry = async (auditId: string) => {
    if (!newExpiryDate) return;

    try {
      const { error } = await supabase
        .from('audits')
        .update({ expires_at: newExpiryDate.toISOString() })
        .eq('id', auditId);

      if (error) throw error;

      toast.success('Lejárat sikeresen módosítva');
      setEditingExpiryAuditId(null);
      setNewExpiryDate(undefined);
      setIsCalendarOpen(false);
      fetchRunningAudits();
    } catch (error) {
      console.error('Error updating expiry:', error);
      toast.error('Hiba történt a lejárat módosításakor');
    }
  };

  const handleUpdateRecurrence = async (auditId: string) => {
    try {
      const { error } = await supabase
        .from('audits')
        .update({ 
          recurrence_config: {
            enabled: recurrenceSettings.enabled,
            frequency: recurrenceSettings.frequency,
          }
        })
        .eq('id', auditId);

      if (error) throw error;

      toast.success('Ismétlődés sikeresen módosítva');
      setEditingRecurrenceAuditId(null);
      fetchRunningAudits();
    } catch (error) {
      console.error('Error updating recurrence:', error);
      toast.error('Hiba történt az ismétlődés módosításakor');
    }
  };

  const handleUpdateGift = async (auditId: string) => {
    try {
      const { error } = await supabase
        .from('audits')
        .update({ gift_id: selectedGiftId })
        .eq('id', auditId);

      if (error) throw error;

      toast.success('Fődíj sikeresen módosítva');
      setEditingGiftAuditId(null);
      setSelectedGiftId(null);
      fetchRunningAudits();
    } catch (error) {
      console.error('Error updating gift:', error);
      toast.error('Hiba történt a fődíj módosításakor');
    }
  };

  const handleToggleDrawMode = async (auditId: string, currentMode: 'auto' | 'manual' | null) => {
    const newMode = currentMode === 'auto' ? 'manual' : 'auto';
    
    try {
      const { error } = await supabase
        .from('audits')
        .update({ draw_mode: newMode })
        .eq('id', auditId);

      if (error) throw error;

      toast.success(`Sorsolási mód váltva: ${newMode === 'auto' ? 'Automatikus' : 'Manuális'}`);
      fetchRunningAudits();
    } catch (error) {
      console.error('Error toggling draw mode:', error);
      toast.error('Hiba történt a sorsolási mód váltásakor');
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

  const activeAudits = audits.filter(m => m.daysRemaining > 0);
  const expiredAudits = audits.filter(m => m.daysRemaining === 0);

  if (loading) {
    return (
      <div className="p-8 flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  const renderAuditCard = (metrics: AuditMetrics) => (
    <Card key={metrics.audit.id}>
      <CardHeader>
        <div className="flex items-start justify-between">
          <div>
            <CardTitle className="text-xl mb-2">
              {formatAuditName(metrics.audit)}
            </CardTitle>
            {metrics.audit.partner_company_id && (
              <p className="text-sm text-muted-foreground mb-2">
                Ügyfélcég: {metrics.audit.company_name}
              </p>
            )}
            <div className="flex items-center gap-2 mt-2">
              <Badge variant="outline" className="gap-1">
                {getAccessModeIcon(metrics.audit.access_mode)}
                {getAccessModeLabel(metrics.audit.access_mode)}
              </Badge>
              <Badge 
                variant="secondary" 
                className="gap-1 cursor-pointer hover:bg-secondary/80 transition-colors"
                onClick={() => {
                  setEditingExpiryAuditId(metrics.audit.id);
                  setNewExpiryDate(metrics.audit.expires_at ? new Date(metrics.audit.expires_at) : addDays(new Date(), 30));
                }}
              >
                <Clock className="h-3 w-3" />
                {metrics.daysRemaining} nap van hátra
                <Edit className="h-3 w-3 ml-1" />
              </Badge>
              {metrics.audit.recurrence_config?.enabled && (
                <Badge 
                  variant="outline" 
                  className="gap-1 bg-blue-50 text-blue-700 border-blue-300 cursor-pointer hover:bg-blue-100 transition-colors"
                  onClick={() => {
                    setEditingRecurrenceAuditId(metrics.audit.id);
                    setRecurrenceSettings({
                      enabled: metrics.audit.recurrence_config?.enabled || false,
                      frequency: metrics.audit.recurrence_config?.frequency || 'monthly',
                    });
                  }}
                >
                  <RefreshCw className="h-3 w-3" />
                  Ismétlődő
                  <Edit className="h-3 w-3 ml-1" />
                </Badge>
              )}
              {metrics.audit.gift_id && metrics.audit.draw_status !== 'completed' && (
                <Badge 
                  variant="outline" 
                  className="gap-1 text-purple-700 border-purple-300 cursor-pointer hover:opacity-80 transition-all"
                  style={{ backgroundColor: '#ff66ff20' }}
                  onClick={() => handleToggleDrawMode(metrics.audit.id, metrics.audit.draw_mode)}
                >
                  <Trophy className="h-3 w-3" />
                  {metrics.audit.draw_mode === 'auto' 
                    ? 'Automatikus sorsolással' 
                    : 'Manuális sorsolással'
                  }
                  <Edit className="h-3 w-3 ml-1" />
                </Badge>
              )}
              {metrics.audit.gift_id && metrics.audit.draw_status === 'completed' && (
                <Badge 
                  variant="outline" 
                  className="gap-1 bg-green-50 text-green-700 border-green-300"
                >
                  <Trophy className="h-3 w-3" />
                  Sorsolt
                </Badge>
              )}
              {metrics.audit.gift_id && metrics.giftName && (
                <Badge 
                  variant="outline" 
                  className="gap-1 bg-yellow-50 text-yellow-700 border-yellow-300 cursor-pointer hover:bg-yellow-100 transition-colors"
                  onClick={() => {
                    setEditingGiftAuditId(metrics.audit.id);
                    setSelectedGiftId(metrics.audit.gift_id || null);
                  }}
                >
                  <Trophy className="h-3 w-3" />
                  Fődíj: {metrics.giftName}
                  <Edit className="h-3 w-3 ml-1" />
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

      <CardContent className="space-y-4">
        {/* Response metrics */}
        <div>
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm font-medium">Kitöltöttség</span>
            <span className="text-sm text-muted-foreground">
              {metrics.responsesCount} kitöltés
              {(() => {
                // Priority: 1. target_responses (overrides everything)
                //          2. email_count (for tokenes mode)
                //          3. totalEmployees (from profile)
                //          4. no target (just show count)
                if (metrics.audit.target_responses) {
                  return ` / ${metrics.audit.target_responses} (célszám)`;
                } else if (metrics.audit.email_count) {
                  return ` / ${metrics.audit.email_count} (email cím)`;
                } else if (metrics.totalEmployees) {
                  return ` / ${metrics.totalEmployees} (munkavállalói létszám)`;
                }
                return '';
              })()}
            </span>
          </div>
          {(() => {
            // Show progress bar only if we have a target
            // Priority: target_responses > email_count > totalEmployees
            const target = metrics.audit.target_responses || 
                          metrics.audit.email_count ||
                          metrics.totalEmployees;
            
            if (target) {
              const percentage = Math.min((metrics.responsesCount / target) * 100, 100);
              return <Progress value={percentage} className="h-2" />;
            }
            return null;
          })()}
        </div>

        {/* Access mode specific information */}
        <Tabs defaultValue="info" className="w-full">
          <TabsList className="grid w-full grid-cols-3">
            <TabsTrigger value="info">Infó</TabsTrigger>
            <TabsTrigger value="link">Link</TabsTrigger>
            {metrics.audit.access_mode === 'qr_code' && (
              <TabsTrigger value="qr">QR kód</TabsTrigger>
            )}
            {metrics.audit.access_mode !== 'qr_code' && (
              <TabsTrigger value="stats">Statisztika</TabsTrigger>
            )}
          </TabsList>
          
          <TabsContent value="info" className="space-y-3 pt-4">
            <div className="flex items-center gap-2 text-sm">
              <CalendarDays className="h-4 w-4 text-muted-foreground" />
              <span className="font-medium">Lejárat:</span>
              <span className="text-muted-foreground">
                {metrics.audit.expires_at 
                  ? new Date(metrics.audit.expires_at).toLocaleDateString('hu-HU', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric'
                    })
                  : 'Nincs beállítva'
                }
              </span>
            </div>
            
            {metrics.audit.recurrence_config?.enabled && (
              <div className="flex items-center gap-2 text-sm">
                <RefreshCw className="h-4 w-4 text-muted-foreground" />
                <span className="font-medium">Következő futás:</span>
                <span className="text-muted-foreground">
                  {(() => {
                    const freq = metrics.audit.recurrence_config?.frequency;
                    return freq === 'monthly' ? 'Havonta' :
                           freq === 'quarterly' ? 'Negyedévente' :
                           freq === 'biannually' ? 'Félévente' :
                           freq === 'annually' ? 'Évente' : 'Nincs beállítva';
                  })()}
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
          </TabsContent>
          
          <TabsContent value="link" className="space-y-3 pt-4">
            <div className="flex items-center gap-2">
              <Input 
                value={getSurveyUrl(metrics.audit.access_token)} 
                readOnly 
                className="flex-1"
              />
              <Button 
                variant="outline" 
                size="icon"
                onClick={() => copyToClipboard(getSurveyUrl(metrics.audit.access_token))}
              >
                <Copy className="h-4 w-4" />
              </Button>
              <Button 
                variant="outline" 
                size="icon"
                onClick={() => window.open(getSurveyUrl(metrics.audit.access_token), '_blank')}
              >
                <ExternalLink className="h-4 w-4" />
              </Button>
            </div>
          </TabsContent>

          {metrics.audit.access_mode === 'qr_code' && (
            <TabsContent value="qr" className="pt-4 space-y-4">
              <div className="flex justify-center p-4 bg-white rounded-lg" id={`qr-code-${metrics.audit.id}`}>
                <QRCodeSVG
                  value={getSurveyUrl(metrics.audit.access_token)}
                  size={200}
                  level="H"
                  includeMargin={true}
                />
              </div>
              <div className="flex justify-center gap-2">
                <Button
                  variant="outline"
                  onClick={() => {
                    const svgElement = document.querySelector(`#qr-code-${metrics.audit.id} svg`) as SVGSVGElement;
                    if (!svgElement) return;

                    const svgData = new XMLSerializer().serializeToString(svgElement);
                    const blob = new Blob([svgData], { type: 'image/svg+xml' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `qr-kod-${formatAuditName(metrics.audit).replace(/\s+/g, '-')}.svg`;
                    a.click();
                    URL.revokeObjectURL(url);
                    toast.success('SVG QR kód letöltve!');
                  }}
                  className="gap-2"
                >
                  <FileDown className="h-4 w-4" />
                  SVG letöltése
                </Button>
                <Button
                  variant="outline"
                  onClick={() => {
                    const svgElement = document.querySelector(`#qr-code-${metrics.audit.id} svg`) as SVGSVGElement;
                    if (!svgElement) return;

                    const svgData = new XMLSerializer().serializeToString(svgElement);
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const img = new Image();
                    
                    img.onload = () => {
                      // Generate high-res PNG (2000x2000)
                      canvas.width = 2000;
                      canvas.height = 2000;
                      ctx?.drawImage(img, 0, 0, 2000, 2000);
                      canvas.toBlob((blob) => {
                        if (blob) {
                          const url = URL.createObjectURL(blob);
                          const a = document.createElement('a');
                          a.href = url;
                          a.download = `qr-kod-${formatAuditName(metrics.audit).replace(/\s+/g, '-')}-2000px.png`;
                          a.click();
                          URL.revokeObjectURL(url);
                          toast.success('Nagy felbontású PNG QR kód letöltve!');
                        }
                      });
                    };
                    
                    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
                  }}
                  className="gap-2"
                >
                  <FileDown className="h-4 w-4" />
                  PNG letöltése (2000px)
                </Button>
              </div>
            </TabsContent>
          )}

          {metrics.audit.access_mode !== 'qr_code' && (
            <TabsContent value="stats" className="space-y-3 pt-4">
              {metrics.audit.access_mode === 'tokenes' && (
                <>
                  <div className="flex items-center gap-2 text-sm">
                    <Mail className="h-4 w-4 text-muted-foreground" />
                    <span className="font-medium">Elküldött emailek:</span>
                    <span className="text-muted-foreground">
                      {metrics.emailsSent || 0}
                    </span>
                  </div>
                  <div className="flex items-center gap-2 text-sm">
                    <MousePointerClick className="h-4 w-4 text-muted-foreground" />
                    <span className="font-medium">Megnyitott emailek:</span>
                    <span className="text-muted-foreground">
                      {metrics.emailsOpened || 0}
                    </span>
                  </div>
                </>
              )}
              <div className="flex items-center gap-2 text-sm">
                <CheckCircle className="h-4 w-4 text-muted-foreground" />
                <span className="font-medium">Kitöltések:</span>
                <span className="text-muted-foreground">
                  {metrics.responsesCount}
                </span>
              </div>
            </TabsContent>
          )}
        </Tabs>
      </CardContent>
    </Card>
  );

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
        <h2 className="text-2xl font-bold mb-2">Futó/Lezárt Felmérések</h2>
        <p className="text-muted-foreground text-sm">
          Áttekintés az aktív és lezárt felmérésekről
        </p>
      </div>

      <Tabs value={activeTab} onValueChange={(v) => setActiveTab(v as 'active' | 'expired')} className="space-y-6">
        <TabsList className="grid w-full max-w-md grid-cols-2">
          <TabsTrigger value="active">
            Futó felmérések ({activeAudits.length})
          </TabsTrigger>
          <TabsTrigger value="expired">
            Lezárt felmérések ({expiredAudits.length})
          </TabsTrigger>
        </TabsList>

        <TabsContent value="active" className="space-y-6">
          {activeAudits.length === 0 ? (
            <Card>
              <CardContent className="py-8 text-center text-muted-foreground">
                Jelenleg nincs futó felmérés
              </CardContent>
            </Card>
          ) : (
            <div className="grid gap-6">
              {activeAudits.map(renderAuditCard)}
            </div>
          )}
        </TabsContent>

        <TabsContent value="expired" className="space-y-6">
          {expiredAudits.length === 0 ? (
            <Card>
              <CardContent className="py-8 text-center text-muted-foreground">
                Jelenleg nincs lezárt felmérés
              </CardContent>
            </Card>
          ) : (
            <div className="grid gap-6">
              {expiredAudits.map(renderAuditCard)}
            </div>
          )}
        </TabsContent>
      </Tabs>

      {/* Expiry Date Edit Dialog */}
      <AlertDialog open={editingExpiryAuditId !== null} onOpenChange={(open) => !open && setEditingExpiryAuditId(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Lejárat módosítása</AlertDialogTitle>
            <AlertDialogDescription>
              Válassz új lejárati dátumot a felméréshez
            </AlertDialogDescription>
          </AlertDialogHeader>
          <div className="py-4">
            <Popover open={isCalendarOpen} onOpenChange={setIsCalendarOpen}>
              <PopoverTrigger asChild>
                <Button
                  variant="outline"
                  className={cn(
                    "w-full justify-start text-left font-normal",
                    !newExpiryDate && "text-muted-foreground"
                  )}
                >
                  <CalendarIcon className="mr-2 h-4 w-4" />
                  {newExpiryDate ? format(newExpiryDate, 'yyyy. MM. dd.', { locale: hu }) : "Válassz dátumot"}
                </Button>
              </PopoverTrigger>
              <PopoverContent className="w-auto p-0 bg-background z-50" align="start">
                <Calendar
                  mode="single"
                  selected={newExpiryDate}
                  onSelect={(date) => {
                    setNewExpiryDate(date);
                    setIsCalendarOpen(false);
                  }}
                  disabled={(date) => date < new Date()}
                  initialFocus
                  className={cn("p-3 pointer-events-auto")}
                />
              </PopoverContent>
            </Popover>
          </div>
          <AlertDialogFooter>
            <AlertDialogCancel>Mégse</AlertDialogCancel>
            <AlertDialogAction onClick={() => editingExpiryAuditId && handleUpdateExpiry(editingExpiryAuditId)}>
              Mentés
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* Recurrence Edit Dialog */}
      <AlertDialog open={editingRecurrenceAuditId !== null} onOpenChange={(open) => !open && setEditingRecurrenceAuditId(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Ismétlődés beállítása</AlertDialogTitle>
            <AlertDialogDescription>
              Állítsd be az ismétlődési gyakoriságot
            </AlertDialogDescription>
          </AlertDialogHeader>
          <div className="space-y-4 py-4">
            <div className="flex items-center justify-between">
              <Label htmlFor="recurrence-enabled">Ismétlődő felmérés</Label>
              <Switch
                id="recurrence-enabled"
                checked={recurrenceSettings.enabled}
                onCheckedChange={(checked) => 
                  setRecurrenceSettings({ ...recurrenceSettings, enabled: checked })
                }
              />
            </div>
            {recurrenceSettings.enabled && (
              <div className="space-y-2">
                <Label>Gyakoriság</Label>
                <div className="grid grid-cols-2 gap-2">
                  {(() => {
                    // Define available frequencies based on subscription package
                    let availableFrequencies: Array<{ value: string; label: string }> = [];
                    
                    if (subscriptionPackage === 'premium' || subscriptionPackage === 'enterprise') {
                      availableFrequencies = [
                        { value: 'quarterly', label: 'Negyedévente' },
                        { value: 'biannually', label: 'Félévente' },
                        { value: 'annually', label: 'Évente' },
                      ];
                    } else {
                      // Basic or no package: only biannually and annually
                      availableFrequencies = [
                        { value: 'biannually', label: 'Félévente' },
                        { value: 'annually', label: 'Évente' },
                      ];
                    }
                    
                    return availableFrequencies.map((freq) => (
                      <Button
                        key={freq.value}
                        type="button"
                        variant={recurrenceSettings.frequency === freq.value ? 'default' : 'outline'}
                        onClick={() => setRecurrenceSettings({ ...recurrenceSettings, frequency: freq.value as any })}
                      >
                        {freq.label}
                      </Button>
                    ));
                  })()}
                </div>
              </div>
            )}
          </div>
          <AlertDialogFooter>
            <AlertDialogCancel>Mégse</AlertDialogCancel>
            <AlertDialogAction onClick={() => editingRecurrenceAuditId && handleUpdateRecurrence(editingRecurrenceAuditId)}>
              Mentés
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* Gift Edit Dialog */}
      <AlertDialog open={editingGiftAuditId !== null} onOpenChange={(open) => !open && setEditingGiftAuditId(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Fődíj módosítása</AlertDialogTitle>
            <AlertDialogDescription>
              Válaszd ki az új fődíjat ehhez a felméréshez
            </AlertDialogDescription>
          </AlertDialogHeader>
          <div className="py-4 space-y-2">
            <Label>Elérhető ajándékok</Label>
            <div className="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto">
              <Button
                variant={selectedGiftId === null ? 'default' : 'outline'}
                onClick={() => setSelectedGiftId(null)}
                className="w-full justify-start"
              >
                Nincs fődíj
              </Button>
              {availableGifts.map((gift) => (
                <Button
                  key={gift.id}
                  variant={selectedGiftId === gift.id ? 'default' : 'outline'}
                  onClick={() => setSelectedGiftId(gift.id)}
                  className="w-full justify-start"
                >
                  <Trophy className="h-4 w-4 mr-2" />
                  {gift.name}
                </Button>
              ))}
            </div>
          </div>
          <AlertDialogFooter>
            <AlertDialogCancel>Mégse</AlertDialogCancel>
            <AlertDialogAction 
              onClick={() => editingGiftAuditId && handleUpdateGift(editingGiftAuditId)}
            >
              Mentés
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
};

export default RunningAudits;
