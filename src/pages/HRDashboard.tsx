import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { toast } from 'sonner';
import { ExternalLink, QrCode, Plus } from 'lucide-react';

interface Questionnaire {
  id: string;
  title: string;
  description: string;
}

interface Audit {
  id: string;
  program_name: string;
  access_token: string;
  is_active: boolean;
  created_at: string;
  expires_at: string | null;
  questionnaire: Questionnaire;
}

const HRDashboard = () => {
  const { signOut } = useAuth();
  const navigate = useNavigate();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAudits();
  }, []);

  const fetchAudits = async () => {
    try {
      const { data: auditsData } = await supabase
        .from('audits')
        .select(`
          id,
          program_name,
          access_token,
          is_active,
          created_at,
          expires_at,
          questionnaire:questionnaires (
            id,
            title,
            description
          )
        `)
        .order('created_at', { ascending: false });

      if (auditsData) {
        setAudits(auditsData as any);
      }
    } catch (error) {
      console.error('Error fetching data:', error);
      toast.error('Hiba történt az adatok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const getSurveyUrl = (token: string) => {
    return `${window.location.origin}/survey/${token}`;
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.success('Link vágólapra másolva!');
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">Fő Dashboard</h1>
          <Button onClick={signOut} variant="outline">
            Kijelentkezés
          </Button>
        </div>

        <div className="mb-8">
          <Button onClick={() => navigate('/hr/create-audit')}>
            <Plus className="mr-2 h-4 w-4" />
            Új Audit Indítása
          </Button>
        </div>

        <div className="space-y-4">
          <h2 className="text-2xl font-bold">Aktív Auditok</h2>
          
          {audits.length === 0 ? (
            <Card>
              <CardContent className="py-8 text-center text-muted-foreground">
                Még nincs audit létrehozva
              </CardContent>
            </Card>
          ) : (
            audits.map((audit) => (
              <Card key={audit.id}>
                <CardHeader>
                  <CardTitle>
                    {audit.program_name} - {audit.questionnaire.title}
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="flex items-center gap-2">
                    <Input
                      value={getSurveyUrl(audit.access_token)}
                      readOnly
                      className="flex-1"
                    />
                    <Button
                      variant="outline"
                      size="icon"
                      onClick={() => copyToClipboard(getSurveyUrl(audit.access_token))}
                    >
                      <ExternalLink className="h-4 w-4" />
                    </Button>
                    <Button variant="outline" size="icon">
                      <QrCode className="h-4 w-4" />
                    </Button>
                  </div>
                  <div className="flex justify-between text-sm text-muted-foreground">
                    <p>Létrehozva: {new Date(audit.created_at).toLocaleDateString('hu-HU')}</p>
                    {audit.expires_at && (
                      <p>Lejár: {new Date(audit.expires_at).toLocaleDateString('hu-HU')}</p>
                    )}
                  </div>
                </CardContent>
              </Card>
            ))
          )}
        </div>
      </div>
    </div>
  );
};

export default HRDashboard;
