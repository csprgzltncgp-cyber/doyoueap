import { useEffect, useState } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'sonner';
import { QrCode, Plus, ExternalLink } from 'lucide-react';

interface Questionnaire {
  id: string;
  title: string;
  description: string;
}

interface Audit {
  id: string;
  company_name: string;
  access_token: string;
  is_active: boolean;
  created_at: string;
  questionnaire: Questionnaire;
}

const HRDashboard = () => {
  const { user, signOut } = useAuth();
  const [audits, setAudits] = useState<Audit[]>([]);
  const [questionnaires, setQuestionnaires] = useState<Questionnaire[]>([]);
  const [loading, setLoading] = useState(true);
  const [isCreateDialogOpen, setIsCreateDialogOpen] = useState(false);
  const [newAudit, setNewAudit] = useState({
    companyName: '',
    questionnaireId: '',
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      // Fetch questionnaires
      const { data: questionnairesData } = await supabase
        .from('questionnaires')
        .select('id, title, description')
        .eq('is_active', true);

      if (questionnairesData) {
        setQuestionnaires(questionnairesData);
      }

      // Fetch audits
      const { data: auditsData } = await supabase
        .from('audits')
        .select(`
          id,
          company_name,
          access_token,
          is_active,
          created_at,
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

  const handleCreateAudit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!newAudit.companyName || !newAudit.questionnaireId) {
      toast.error('Kérlek töltsd ki az összes mezőt');
      return;
    }

    try {
      // Generate token via function
      const { data: tokenData, error: tokenError } = await supabase.rpc(
        'generate_access_token'
      );

      if (tokenError) throw tokenError;

      const { error } = await supabase.from('audits').insert({
        hr_user_id: user?.id,
        company_name: newAudit.companyName,
        questionnaire_id: newAudit.questionnaireId,
        access_token: tokenData,
      });

      if (error) throw error;

      toast.success('Audit sikeresen létrehozva!');
      setIsCreateDialogOpen(false);
      setNewAudit({ companyName: '', questionnaireId: '' });
      fetchData();
    } catch (error) {
      console.error('Error creating audit:', error);
      toast.error('Hiba történt az audit létrehozásakor');
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
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">HR Felület</h1>
          <Button onClick={signOut} variant="outline">
            Kijelentkezés
          </Button>
        </div>

        <div className="mb-8">
          <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
            <DialogTrigger asChild>
              <Button>
                <Plus className="mr-2 h-4 w-4" />
                Új Audit Indítása
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Új Audit Létrehozása</DialogTitle>
              </DialogHeader>
              <form onSubmit={handleCreateAudit} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="companyName">Cég neve</Label>
                  <Input
                    id="companyName"
                    value={newAudit.companyName}
                    onChange={(e) =>
                      setNewAudit({ ...newAudit, companyName: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="questionnaire">Kérdőív</Label>
                  <Select
                    value={newAudit.questionnaireId}
                    onValueChange={(value) =>
                      setNewAudit({ ...newAudit, questionnaireId: value })
                    }
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz kérdőívet" />
                    </SelectTrigger>
                    <SelectContent>
                      {questionnaires.map((q) => (
                        <SelectItem key={q.id} value={q.id}>
                          {q.title}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <Button type="submit" className="w-full">
                  Audit Létrehozása
                </Button>
              </form>
            </DialogContent>
          </Dialog>
        </div>

        <div className="space-y-4">
          <h2 className="text-2xl font-bold">Auditok</h2>
          
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
                    {audit.company_name} - {audit.questionnaire.title}
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
                  <p className="text-sm text-muted-foreground">
                    Létrehozva: {new Date(audit.created_at).toLocaleDateString('hu-HU')}
                  </p>
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
