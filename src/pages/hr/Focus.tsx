import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Calendar, Download, FileImage, Clock, Play, Flag, Trash2, CheckCircle2, Gift, Users, ExternalLink, RefreshCw } from 'lucide-react';
import { toast } from '@/hooks/use-toast';
import { formatAuditName, StandardAudit } from '@/lib/auditUtils';
import { format, differenceInDays } from 'date-fns';
import { hu } from 'date-fns/locale';

interface ExportDownload {
  auditId: string;
  auditName: string;
  fileName: string;
  fileType: string;
  timestamp: string;
}

interface AuditWithStats extends StandardAudit {
  responseCount: number;
  daysRemaining: number | null;
  isExpired: boolean;
  drawInfo?: {
    id: string;
    winner_token: string;
    candidates_count: number;
    created_at: string;
    report_url: string | null;
  };
  giftInfo?: {
    name: string;
    value_eur: number;
  };
}

const Focus = () => {
  const { user } = useAuth();
  const [audits, setAudits] = useState<AuditWithStats[]>([]);
  const [exportDownloads, setExportDownloads] = useState<ExportDownload[]>([]);
  const [loading, setLoading] = useState(true);
  const [userName, setUserName] = useState<string>('');

  useEffect(() => {
    if (user?.id) {
      fetchUserProfile();
    }
  }, [user?.id]);

  useEffect(() => {
    fetchAudits();
    loadExportDownloads();
  }, []);

  const fetchUserProfile = async () => {
    if (!user?.id) return;
    
    try {
      const { data, error } = await supabase
        .from('profiles')
        .select('full_name')
        .eq('id', user.id)
        .maybeSingle();

      if (error) {
        console.error('Error fetching user profile:', error);
        return;
      }
      
      if (data?.full_name) {
        setUserName(data.full_name);
      }
    } catch (error) {
      console.error('Error fetching user profile:', error);
    }
  };

  const fetchAudits = async () => {
    try {
      setLoading(true);
      const { data: auditsData, error: auditsError } = await supabase
        .from('audits')
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at, gift_id')
        .eq('is_active', true)
        .order('start_date', { ascending: false });

      if (auditsError) throw auditsError;

      const auditsWithStats = await Promise.all(
        (auditsData || []).map(async (audit) => {
          const { count } = await supabase
            .from('audit_responses')
            .select('*', { count: 'exact', head: true })
            .eq('audit_id', audit.id);

          const daysRemaining = audit.expires_at
            ? differenceInDays(new Date(audit.expires_at), new Date())
            : null;

          const isExpired = audit.expires_at 
            ? new Date(audit.expires_at) < new Date()
            : false;

          // Fetch draw info if exists
          const { data: drawData } = await supabase
            .from('draws')
            .select('id, winner_token, candidates_count, ts, report_url')
            .eq('audit_id', audit.id)
            .order('ts', { ascending: false })
            .limit(1)
            .maybeSingle();

          // Fetch gift info if gift_id exists
          let giftInfo = undefined;
          if (audit.gift_id) {
            const { data: giftData } = await supabase
              .from('gifts')
              .select('name, value_eur')
              .eq('id', audit.gift_id)
              .maybeSingle();
            
            if (giftData) {
              giftInfo = giftData;
            }
          }

          return {
            ...audit,
            responseCount: count || 0,
            daysRemaining,
            isExpired,
            drawInfo: drawData ? {
              id: drawData.id,
              winner_token: drawData.winner_token,
              candidates_count: drawData.candidates_count,
              created_at: drawData.ts,
              report_url: drawData.report_url
            } : undefined,
            giftInfo
          };
        })
      );

      setAudits(auditsWithStats);
    } catch (error) {
      console.error('Error fetching audits:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadExportDownloads = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { data, error } = await supabase
        .from('export_history')
        .select('*')
        .order('created_at', { ascending: false })
        .limit(50);

      if (error) throw error;

      // Transform database records to ExportDownload format
      const downloads: ExportDownload[] = (data || []).map(record => ({
        auditId: record.audit_id,
        auditName: record.audit_name,
        fileName: record.file_name,
        fileType: record.file_type,
        timestamp: record.created_at
      }));

      setExportDownloads(downloads);
    } catch (error) {
      console.error('Error loading export downloads:', error);
    }
  };

  const deleteAllExportHistory = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { error } = await supabase
        .from('export_history')
        .delete()
        .eq('user_id', user.id);

      if (error) throw error;

      setExportDownloads([]);
      toast({
        title: "Előzmények törölve",
        description: "Az összes export előzmény sikeresen törölve lett.",
      });
    } catch (error) {
      console.error('Error deleting export history:', error);
      toast({
        title: "Hiba",
        description: "Az előzmények törlése sikertelen volt.",
        variant: "destructive",
      });
    }
  };

  const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Jó reggelt';
    if (hour < 18) return 'Jó napot';
    return 'Jó estét';
  };

  const groupDownloadsByAudit = () => {
    const grouped = exportDownloads.reduce((acc, download) => {
      if (!acc[download.auditId]) {
        acc[download.auditId] = {
          auditName: download.auditName,
          downloads: [],
        };
      }
      acc[download.auditId].downloads.push(download);
      return acc;
    }, {} as Record<string, { auditName: string; downloads: ExportDownload[] }>);

    return grouped;
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-[400px]">
        <div className="text-muted-foreground">Betöltés...</div>
      </div>
    );
  }

  const groupedDownloads = groupDownloadsByAudit();
  const activeAudits = audits.filter(audit => !audit.isExpired);
  const expiredAudits = audits.filter(audit => audit.isExpired);

  return (
    <div className="space-y-6 pt-20 md:pt-0">
      {/* Üdvözlő szekció */}
      <Card className="bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20">
        <CardHeader>
          <CardTitle className="text-2xl">
            {getGreeting()}, {userName || 'Felhasználó'}!
          </CardTitle>
          <CardDescription className="text-base">
            Üdv újra itt! Itt találja a futó felmérések áttekintését és a letöltési előzményeit.
          </CardDescription>
        </CardHeader>
      </Card>

      {/* Futó felmérések */}
      <div className="space-y-4">
        <div className="flex items-center gap-2">
          <Play className="h-5 w-5 text-primary" />
          <h2 className="text-xl font-semibold">Futó felmérések</h2>
        </div>

        {activeAudits.length === 0 ? (
          <Card>
            <CardContent className="py-8 text-center text-muted-foreground">
              Jelenleg nincs futó felmérés
            </CardContent>
          </Card>
        ) : (
          <div className="grid gap-4">
            {activeAudits.map((audit) => (
              <Card key={audit.id}>
                <CardHeader>
                  <div className="flex items-start justify-between">
                    <div className="space-y-1">
                      <CardTitle className="text-lg">
                        {formatAuditName(audit)}
                      </CardTitle>
                      <CardDescription>
                        <Users className="h-3 w-3 inline mr-1" />
                        {audit.responseCount} válasz érkezett
                      </CardDescription>
                    </div>
                    {audit.daysRemaining !== null && audit.daysRemaining >= 0 && (
                      <Badge variant={audit.daysRemaining < 7 ? 'destructive' : 'default'}>
                        <Clock className="h-3 w-3 mr-1" />
                        {audit.daysRemaining} nap hátra
                      </Badge>
                    )}
                  </div>
                </CardHeader>
                <CardContent className="space-y-3">
                  <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <Calendar className="h-4 w-4" />
                    <span>Kezdés: {format(new Date(audit.start_date), 'yyyy. MM. dd.', { locale: hu })}</span>
                  </div>
                  {audit.expires_at && (
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                      <Flag className="h-4 w-4" />
                      <span>Vége: {format(new Date(audit.expires_at), 'yyyy. MM. dd.', { locale: hu })}</span>
                    </div>
                  )}
                  {audit.recurrence_config?.enabled && (
                    <div className="flex items-center gap-2 text-sm">
                      <RefreshCw className="h-4 w-4 text-blue-600" />
                      <span className="font-medium text-blue-700">Ismétlődő felmérés</span>
                    </div>
                  )}
                  {audit.giftInfo && (
                    <Badge 
                      variant="outline" 
                      className="gap-1 bg-yellow-50 text-yellow-700 border-yellow-300"
                    >
                      <Gift className="h-3 w-3" />
                      {audit.giftInfo.name} ({audit.giftInfo.value_eur} EUR)
                    </Badge>
                  )}
                  {audit.drawInfo && (
                    <div className="mt-3 p-3 bg-muted/50 rounded-md space-y-2">
                      <div className="flex items-center gap-2 text-sm font-medium">
                        <CheckCircle2 className="h-4 w-4 text-green-600" />
                        <span>Sorsolás megtörtént</span>
                      </div>
                      <div className="text-xs text-muted-foreground space-y-1">
                        <p>Résztvevők: {audit.drawInfo.candidates_count} fő</p>
                        <p>Időpont: {format(new Date(audit.drawInfo.created_at), 'yyyy. MM. dd. HH:mm', { locale: hu })}</p>
                      </div>
                      {audit.drawInfo.report_url && (
                        <Button variant="outline" size="sm" className="w-full mt-2" asChild>
                          <a href={audit.drawInfo.report_url} target="_blank" rel="noopener noreferrer">
                            <Download className="h-3 w-3 mr-2" />
                            Sorsolás eredményének letöltése
                          </a>
                        </Button>
                      )}
                    </div>
                  )}
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>

      {/* Lejárt felmérések */}
      {expiredAudits.length > 0 && (
        <div className="space-y-4">
          <div className="flex items-center gap-2">
            <Flag className="h-5 w-5 text-muted-foreground" />
            <h2 className="text-xl font-semibold">Lejárt felmérések</h2>
          </div>

          <div className="grid gap-4">
            {expiredAudits.map((audit) => (
              <Card key={audit.id} className="opacity-75">
                <CardHeader>
                  <div className="flex items-start justify-between">
                    <div className="space-y-1">
                      <CardTitle className="text-lg">
                        {formatAuditName(audit)}
                      </CardTitle>
                      <CardDescription>
                        <Users className="h-3 w-3 inline mr-1" />
                        {audit.responseCount} válasz érkezett
                      </CardDescription>
                    </div>
                    <Badge variant="secondary">
                      <CheckCircle2 className="h-3 w-3 mr-1" />
                      Lezárva
                    </Badge>
                  </div>
                </CardHeader>
                <CardContent className="space-y-3">
                  <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <Calendar className="h-4 w-4" />
                    <span>Kezdés: {format(new Date(audit.start_date), 'yyyy. MM. dd.', { locale: hu })}</span>
                  </div>
                  {audit.expires_at && (
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                      <Flag className="h-4 w-4" />
                      <span>Lezárva: {format(new Date(audit.expires_at), 'yyyy. MM. dd.', { locale: hu })}</span>
                    </div>
                  )}
                  {audit.recurrence_config?.enabled && (
                    <div className="flex items-center gap-2 text-sm">
                      <RefreshCw className="h-4 w-4 text-blue-600" />
                      <span className="font-medium text-blue-700">Ismétlődő felmérés volt</span>
                    </div>
                  )}
                  {audit.giftInfo && (
                    <Badge 
                      variant="outline" 
                      className="gap-1 bg-yellow-50 text-yellow-700 border-yellow-300"
                    >
                      <Gift className="h-3 w-3" />
                      {audit.giftInfo.name} ({audit.giftInfo.value_eur} EUR)
                    </Badge>
                  )}
                  {audit.drawInfo && (
                    <div className="mt-3 p-3 bg-muted/50 rounded-md space-y-2">
                      <div className="flex items-center gap-2 text-sm font-medium">
                        <CheckCircle2 className="h-4 w-4 text-green-600" />
                        <span>Sorsolás megtörtént</span>
                      </div>
                      <div className="text-xs text-muted-foreground space-y-1">
                        <p>Résztvevők: {audit.drawInfo.candidates_count} fő</p>
                        <p>Időpont: {format(new Date(audit.drawInfo.created_at), 'yyyy. MM. dd. HH:mm', { locale: hu })}</p>
                      </div>
                      {audit.drawInfo.report_url && (
                        <Button variant="outline" size="sm" className="w-full mt-2" asChild>
                          <a href={audit.drawInfo.report_url} target="_blank" rel="noopener noreferrer">
                            <Download className="h-3 w-3 mr-2" />
                            Sorsolás eredményének letöltése
                          </a>
                        </Button>
                      )}
                    </div>
                  )}
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      )}

      {/* Export letöltések történet */}
      <div className="space-y-4">
        <div className="flex items-center gap-2">
          <Download className="h-5 w-5 text-primary" />
          <h2 className="text-xl font-semibold">Export előzmények</h2>
        </div>

        {Object.keys(groupedDownloads).length === 0 ? (
          <Card>
            <CardContent className="py-8 text-center text-muted-foreground">
              Még nem exportáltál riportot
            </CardContent>
          </Card>
        ) : (
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle className="text-lg">Letöltési előzmények</CardTitle>
                  <CardDescription>
                    {exportDownloads.length} export fájl összesen
                  </CardDescription>
                </div>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={deleteAllExportHistory}
                  className="gap-2"
                >
                  <Trash2 className="h-4 w-4" />
                  Előzmények törlése
                </Button>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {Object.entries(groupedDownloads).map(([auditId, data]) => (
                  <div key={auditId} className="border-b last:border-0 pb-4 last:pb-0">
                    <h3 className="font-semibold mb-3">{data.auditName}</h3>
                    <div className="space-y-2">
                      {data.downloads.map((download, idx) => (
                        <div
                          key={idx}
                          className="flex items-center justify-between p-2 rounded-lg bg-muted/50"
                        >
                          <div className="flex items-center gap-2">
                            <FileImage className="h-4 w-4 text-muted-foreground" />
                            <div className="flex flex-col">
                              <span className="text-sm font-medium">{download.fileName}</span>
                              <span className="text-xs text-muted-foreground">{download.fileType}</span>
                            </div>
                          </div>
                          <span className="text-xs text-muted-foreground">
                            {format(new Date(download.timestamp), 'yyyy. MM. dd. HH:mm', { locale: hu })}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  );
};

export default Focus;
