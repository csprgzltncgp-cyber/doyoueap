import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit } from '@/lib/auditUtils';
import { Calendar, Mail, MousePointerClick, CheckCircle, Clock, Copy, ExternalLink, Link } from 'lucide-react';
import { QRCodeSVG } from 'qrcode.react';

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

  useEffect(() => {
    fetchRunningAudits();
  }, []);

  const fetchRunningAudits = async () => {
    try {
      // Fetch active audits
      const { data: auditsData, error: auditsError } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at, access_token')
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
                  </div>
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
