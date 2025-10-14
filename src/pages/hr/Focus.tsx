import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { Calendar, Download, FileImage, Clock, Play, Flag, Trash2, CheckCircle2, Gift, Users, ExternalLink, RefreshCw, TrendingUp, Award, Sparkles } from 'lucide-react';
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
        .select('full_name, employee_count')
        .eq('id', user.id)
        .maybeSingle();

      if (error) {
        console.error('Error fetching user profile:', error);
        return;
      }
      
      if (data?.full_name) {
        setUserName(data.full_name);
      }
      
      // Store employee count for later use
      if (data?.employee_count) {
        const match = data.employee_count.match(/(\d+)-(\d+)/);
        if (match) {
          (window as any).__employeeCount = parseInt(match[2]); // Use upper bound
        } else {
          const singleNumber = parseInt(data.employee_count);
          if (!isNaN(singleNumber)) {
            (window as any).__employeeCount = singleNumber;
          }
        }
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
        .select('id, start_date, program_name, access_mode, recurrence_config, is_active, expires_at, gift_id, target_responses, email_count')
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
      <div className="space-y-6 pt-20 md:pt-0">
        <Skeleton className="h-32 w-full" />
        <div className="grid gap-4">
          <Skeleton className="h-64 w-full" />
          <Skeleton className="h-64 w-full" />
        </div>
      </div>
    );
  }

  const groupedDownloads = groupDownloadsByAudit();
  const activeAudits = audits.filter(audit => !audit.isExpired);
  const expiredAudits = audits.filter(audit => audit.isExpired);

  return (
    <div className="space-y-8 pt-20 md:pt-0">
      {/* Hero üdvözlő szekció */}
      <div className="relative overflow-hidden rounded-xl bg-white border border-border p-8 shadow-lg">
        <div className="relative z-10 flex items-start justify-between">
          <div className="space-y-2">
            <h1 className="text-3xl font-bold text-foreground">
              {getGreeting()}, {userName || 'Felhasználó'}!
            </h1>
            <p className="text-muted-foreground text-lg max-w-2xl">
              Üdv újra itt! Itt találja a futó felmérések áttekintését és a letöltési előzményeit.
            </p>
          </div>
          <div className="hidden md:flex gap-3">
            <div className="bg-white border border-border rounded-lg p-4 text-center min-w-[100px] shadow-md">
              <TrendingUp className="h-5 w-5 text-foreground mx-auto mb-1" />
              <div className="text-2xl font-bold text-foreground">{activeAudits.length}</div>
              <div className="text-xs text-foreground font-medium">Aktív</div>
            </div>
            <div className="bg-white border border-border rounded-lg p-4 text-center min-w-[100px] shadow-md">
              <Award className="h-5 w-5 text-foreground mx-auto mb-1" />
              <div className="text-2xl font-bold text-foreground">{expiredAudits.length}</div>
              <div className="text-xs text-foreground font-medium">Lezárt</div>
            </div>
          </div>
        </div>
      </div>

      {/* Futó felmérések */}
      <div className="space-y-5">
        <div className="flex items-center gap-3">
          <div className="bg-primary/10 p-2 rounded-lg">
            <Play className="h-5 w-5 text-primary" />
          </div>
          <h2 className="text-2xl font-bold">Futó felmérések</h2>
        </div>

        {activeAudits.length === 0 ? (
          <Card className="border-dashed border-2">
            <CardContent className="py-12 text-center">
              <Play className="h-12 w-12 text-muted-foreground/40 mx-auto mb-3" />
              <p className="text-muted-foreground">Jelenleg nincs futó felmérés</p>
            </CardContent>
          </Card>
        ) : (
          <div className="grid gap-5">
            {activeAudits.map((audit, index) => (
              <Card 
                key={audit.id} 
                className="group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden animate-fade-in"
                style={{ animationDelay: `${index * 100}ms` }}
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-full -z-10"></div>
                <CardHeader className="pb-4">
                  <div className="flex items-start justify-between gap-4">
                    <div className="space-y-2 flex-1">
                      <CardTitle className="text-xl group-hover:text-primary transition-colors">
                        {formatAuditName(audit)}
                      </CardTitle>
                      <div className="flex items-center gap-2 text-muted-foreground">
                        <div className="bg-primary/10 p-1.5 rounded">
                          <Users className="h-4 w-4 text-primary" />
                        </div>
                        <span className="font-semibold text-foreground">{audit.responseCount}</span>
                        <span className="text-sm">
                          válasz
                          {(() => {
                            if (audit.target_responses) {
                              return ` / ${audit.target_responses} (célszám)`;
                            } else if (audit.email_count) {
                              return ` / ${audit.email_count} (email cím)`;
                            } else if ((window as any).__employeeCount) {
                              return ` / ${(window as any).__employeeCount} (munkavállalói létszám)`;
                            }
                            return ' érkezett';
                          })()}
                        </span>
                      </div>
                    </div>
                    {audit.daysRemaining !== null && audit.daysRemaining >= 0 && (
                      <Badge 
                        variant={audit.daysRemaining < 7 ? 'destructive' : 'outline'}
                        className={`gap-1.5 px-3 py-1.5 ${audit.daysRemaining >= 7 ? 'bg-foreground text-background border-foreground' : ''}`}
                      >
                        <Clock className="h-3.5 w-3.5" />
                        <span className="font-semibold">{audit.daysRemaining}</span>
                        nap hátra
                      </Badge>
                    )}
                  </div>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div className="flex items-center gap-3 text-sm bg-muted/50 p-3 rounded-lg">
                      <Calendar className="h-4 w-4 text-primary flex-shrink-0" />
                      <div>
                        <p className="text-xs text-muted-foreground">Kezdés</p>
                        <p className="font-medium">{format(new Date(audit.start_date), 'yyyy. MM. dd.', { locale: hu })}</p>
                      </div>
                    </div>
                    {audit.expires_at && (
                      <div className="flex items-center gap-3 text-sm bg-muted/50 p-3 rounded-lg">
                        <Flag className="h-4 w-4 text-primary flex-shrink-0" />
                        <div>
                          <p className="text-xs text-muted-foreground">Lejárat</p>
                          <p className="font-medium">{format(new Date(audit.expires_at), 'yyyy. MM. dd.', { locale: hu })}</p>
                        </div>
                      </div>
                    )}
                  </div>
                  
                  <div className="flex flex-wrap gap-2">
                    {audit.recurrence_config?.enabled && (
                      <Badge variant="outline" className="gap-1.5 bg-blue-50 text-blue-700 border-blue-200">
                        <RefreshCw className="h-3.5 w-3.5" />
                        Ismétlődő
                      </Badge>
                    )}
                    {audit.giftInfo && (
                      <Badge 
                        variant="outline" 
                        className="gap-1.5 bg-amber-50 text-amber-700 border-amber-200"
                      >
                        <Gift className="h-3.5 w-3.5" />
                        {audit.giftInfo.name} ({audit.giftInfo.value_eur} EUR)
                      </Badge>
                    )}
                  </div>

                  {audit.drawInfo && (
                    <div className="mt-4 p-4 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg space-y-3">
                      <div className="flex items-center gap-2 text-sm font-semibold text-green-700">
                        <CheckCircle2 className="h-5 w-5" />
                        <span>Sorsolás megtörtént</span>
                      </div>
                      <div className="grid grid-cols-2 gap-3 text-sm">
                        <div className="flex items-center gap-2">
                          <Users className="h-4 w-4 text-green-600" />
                          <span className="text-muted-foreground">{audit.drawInfo.candidates_count} résztvevő</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <Calendar className="h-4 w-4 text-green-600" />
                          <span className="text-muted-foreground">{format(new Date(audit.drawInfo.created_at), 'MM. dd. HH:mm', { locale: hu })}</span>
                        </div>
                      </div>
                      {audit.drawInfo.report_url && (
                        <Button variant="outline" size="sm" className="w-full bg-white hover:bg-green-50" asChild>
                          <a href={audit.drawInfo.report_url} target="_blank" rel="noopener noreferrer">
                            <Download className="h-4 w-4 mr-2" />
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
        <div className="space-y-5">
          <div className="flex items-center gap-3">
            <div className="bg-primary/10 p-2 rounded-lg">
              <Flag className="h-5 w-5 text-primary" />
            </div>
            <h2 className="text-2xl font-bold">Lejárt felmérések</h2>
          </div>

          <div className="grid gap-5">
            {expiredAudits.map((audit, index) => (
              <Card 
                key={audit.id} 
                className="opacity-80 hover:opacity-100 transition-all duration-300 animate-fade-in"
                style={{ animationDelay: `${index * 100}ms` }}
              >
                <CardHeader className="pb-4">
                  <div className="flex items-start justify-between gap-4">
                    <div className="space-y-2 flex-1">
                      <CardTitle className="text-xl">
                        {formatAuditName(audit)}
                      </CardTitle>
                      <div className="flex items-center gap-2 text-muted-foreground">
                        <div className="bg-muted p-1.5 rounded">
                          <Users className="h-4 w-4" />
                        </div>
                        <span className="font-semibold text-foreground">{audit.responseCount}</span>
                        <span className="text-sm">
                          válasz
                          {(() => {
                            if (audit.target_responses) {
                              return ` / ${audit.target_responses} (célszám)`;
                            } else if (audit.email_count) {
                              return ` / ${audit.email_count} (email cím)`;
                            } else if ((window as any).__employeeCount) {
                              return ` / ${(window as any).__employeeCount} (munkavállalói létszám)`;
                            }
                            return ' érkezett';
                          })()}
                        </span>
                      </div>
                    </div>
                    <Badge variant="secondary" className="gap-1.5 px-3 py-1.5">
                      <CheckCircle2 className="h-3.5 w-3.5" />
                      Lezárva
                    </Badge>
                  </div>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div className="flex items-center gap-3 text-sm bg-muted/50 p-3 rounded-lg">
                      <Calendar className="h-4 w-4 text-primary flex-shrink-0" />
                      <div>
                        <p className="text-xs text-muted-foreground">Kezdés</p>
                        <p className="font-medium">{format(new Date(audit.start_date), 'yyyy. MM. dd.', { locale: hu })}</p>
                      </div>
                    </div>
                    {audit.expires_at && (
                      <div className="flex items-center gap-3 text-sm bg-muted/50 p-3 rounded-lg">
                        <Flag className="h-4 w-4 text-primary flex-shrink-0" />
                        <div>
                          <p className="text-xs text-muted-foreground">Lezárva</p>
                          <p className="font-medium">{format(new Date(audit.expires_at), 'yyyy. MM. dd.', { locale: hu })}</p>
                        </div>
                      </div>
                    )}
                  </div>
                  
                  <div className="flex flex-wrap gap-2">
                    {audit.recurrence_config?.enabled && (
                      <Badge variant="outline" className="gap-1.5 bg-blue-50 text-blue-700 border-blue-200">
                        <RefreshCw className="h-3.5 w-3.5" />
                        Ismétlődő
                      </Badge>
                    )}
                    {audit.giftInfo && (
                      <Badge 
                        variant="outline" 
                        className="gap-1.5 bg-amber-50 text-amber-700 border-amber-200"
                      >
                        <Gift className="h-3.5 w-3.5" />
                        {audit.giftInfo.name} ({audit.giftInfo.value_eur} EUR)
                      </Badge>
                    )}
                  </div>

                  {audit.drawInfo && (
                    <div className="mt-4 p-4 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg space-y-3">
                      <div className="flex items-center gap-2 text-sm font-semibold text-green-700">
                        <CheckCircle2 className="h-5 w-5" />
                        <span>Sorsolás megtörtént</span>
                      </div>
                      <div className="grid grid-cols-2 gap-3 text-sm">
                        <div className="flex items-center gap-2">
                          <Users className="h-4 w-4 text-green-600" />
                          <span className="text-muted-foreground">{audit.drawInfo.candidates_count} résztvevő</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <Calendar className="h-4 w-4 text-green-600" />
                          <span className="text-muted-foreground">{format(new Date(audit.drawInfo.created_at), 'MM. dd. HH:mm', { locale: hu })}</span>
                        </div>
                      </div>
                      {audit.drawInfo.report_url && (
                        <Button variant="outline" size="sm" className="w-full bg-white hover:bg-green-50" asChild>
                          <a href={audit.drawInfo.report_url} target="_blank" rel="noopener noreferrer">
                            <Download className="h-4 w-4 mr-2" />
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
      <div className="space-y-5">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="bg-primary/10 p-2 rounded-lg">
              <Download className="h-5 w-5 text-primary" />
            </div>
            <h2 className="text-2xl font-bold">Export előzmények</h2>
          </div>
          {Object.keys(groupedDownloads).length > 0 && (
            <Button
              variant="outline"
              size="sm"
              onClick={deleteAllExportHistory}
              className="gap-2 hover:bg-destructive/10 hover:text-destructive hover:border-destructive"
            >
              <Trash2 className="h-4 w-4" />
              Összes törlése
            </Button>
          )}
        </div>

        {Object.keys(groupedDownloads).length === 0 ? (
          <Card className="border-dashed border-2">
            <CardContent className="py-12 text-center">
              <Download className="h-12 w-12 text-muted-foreground/40 mx-auto mb-3" />
              <p className="text-muted-foreground">Még nem exportáltál riportot</p>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {Object.entries(groupedDownloads).map(([auditId, data], groupIndex) => (
              <Card 
                key={auditId} 
                className="overflow-hidden hover:shadow-lg transition-shadow duration-300 animate-fade-in"
                style={{ animationDelay: `${groupIndex * 100}ms` }}
              >
                <CardHeader className="bg-muted/30 border-b">
                  <div className="flex items-center justify-between">
                    <div>
                      <CardTitle className="text-lg">{data.auditName}</CardTitle>
                      <CardDescription className="text-sm">
                        {data.downloads.length} exportált fájl
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="p-4">
                  <div className="space-y-2">
                    {data.downloads.map((download, idx) => (
                      <div
                        key={idx}
                        className="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r from-muted/50 to-muted/30 hover:from-muted/70 hover:to-muted/50 transition-all duration-200 group"
                      >
                        <div className="flex items-center gap-3 flex-1">
                          <div className="bg-background p-2 rounded-lg group-hover:bg-primary/10 transition-colors">
                            <FileImage className="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                          </div>
                          <div className="flex flex-col flex-1">
                            <span className="text-sm font-medium group-hover:text-primary transition-colors">{download.fileName}</span>
                            <span className="text-xs text-muted-foreground uppercase tracking-wider">{download.fileType}</span>
                          </div>
                        </div>
                        <div className="flex items-center gap-2 text-xs text-muted-foreground">
                          <Clock className="h-3 w-3" />
                          {format(new Date(download.timestamp), 'MM. dd. HH:mm', { locale: hu })}
                        </div>
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
