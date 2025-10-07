import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Calendar, Download, FileImage, Clock, Play, Flag } from 'lucide-react';
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
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at')
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

          return {
            ...audit,
            responseCount: count || 0,
            daysRemaining,
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

  return (
    <div className="space-y-6">
      {/* Üdvözlő szekció */}
      <Card className="bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20">
        <CardHeader>
          <CardTitle className="text-2xl">
            {getGreeting()}, {userName || 'Felhasználó'}!
          </CardTitle>
          <CardDescription className="text-base">
            Üdv újra itt! Itt találod a futó felmérések áttekintését és a letöltési előzményeidet.
          </CardDescription>
        </CardHeader>
      </Card>

      {/* Futó felmérések */}
      <div className="space-y-4">
        <div className="flex items-center gap-2">
          <Play className="h-5 w-5 text-primary" />
          <h2 className="text-xl font-semibold">Futó felmérések</h2>
        </div>

        {audits.length === 0 ? (
          <Card>
            <CardContent className="py-8 text-center text-muted-foreground">
              Jelenleg nincs futó felmérés
            </CardContent>
          </Card>
        ) : (
          <div className="grid gap-4">
            {audits.map((audit) => (
              <Card key={audit.id}>
                <CardHeader>
                  <div className="flex items-start justify-between">
                    <div className="space-y-1">
                      <CardTitle className="text-lg">
                        {formatAuditName(audit)}
                      </CardTitle>
                      <CardDescription>
                        {audit.responseCount} válasz érkezett
                      </CardDescription>
                    </div>
                    {audit.daysRemaining !== null && (
                      <Badge variant={audit.daysRemaining < 7 ? 'destructive' : 'default'}>
                        <Clock className="h-3 w-3 mr-1" />
                        {audit.daysRemaining > 0
                          ? `${audit.daysRemaining} nap hátra`
                          : 'Lejárt'}
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
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>

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
          <div className="grid gap-4">
            {Object.entries(groupedDownloads).map(([auditId, data]) => (
              <Card key={auditId}>
                <CardHeader>
                  <CardTitle className="text-lg">{data.auditName}</CardTitle>
                  <CardDescription>
                    {data.downloads.length} export fájl
                  </CardDescription>
                </CardHeader>
                <CardContent>
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
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default Focus;
