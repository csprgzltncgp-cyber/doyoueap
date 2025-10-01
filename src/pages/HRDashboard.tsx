import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useState, useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
import { Copy, QrCode } from 'lucide-react';

interface Questionnaire {
  id: string;
  title: string;
  description: string;
}

const HRDashboard = () => {
  const { user, signOut } = useAuth();
  const [questionnaires, setQuestionnaires] = useState<Questionnaire[]>([]);
  const [selectedQuestionnaire, setSelectedQuestionnaire] = useState('');
  const [companyName, setCompanyName] = useState('');
  const [isCreating, setIsCreating] = useState(false);
  const [generatedLink, setGeneratedLink] = useState('');
  const [showLinkDialog, setShowLinkDialog] = useState(false);

  useEffect(() => {
    fetchQuestionnaires();
  }, []);

  const fetchQuestionnaires = async () => {
    try {
      const { data, error } = await supabase
        .from('questionnaires')
        .select('id, title, description')
        .eq('is_active', true);

      if (error) throw error;
      setQuestionnaires(data || []);
    } catch (error) {
      console.error('Error fetching questionnaires:', error);
      toast.error('Nem sikerült betölteni a kérdőíveket');
    }
  };

  const createAudit = async () => {
    if (!selectedQuestionnaire || !companyName || !user) return;

    setIsCreating(true);
    try {
      // Generate token using database function
      const { data: tokenData, error: tokenError } = await supabase
        .rpc('generate_access_token');

      if (tokenError) throw tokenError;

      const { data, error } = await supabase
        .from('audits')
        .insert({
          hr_user_id: user.id,
          company_name: companyName,
          questionnaire_id: selectedQuestionnaire,
          access_token: tokenData,
          is_active: true,
        })
        .select()
        .single();

      if (error) throw error;

      const link = `${window.location.origin}/survey/${tokenData}`;
      setGeneratedLink(link);
      setShowLinkDialog(true);
      toast.success('Audit sikeresen létrehozva!');
      
      // Reset form
      setSelectedQuestionnaire('');
      setCompanyName('');
    } catch (error: any) {
      console.error('Error creating audit:', error);
      toast.error('Hiba történt az audit létrehozásakor');
    } finally {
      setIsCreating(false);
    }
  };

  const copyLink = () => {
    navigator.clipboard.writeText(generatedLink);
    toast.success('Link másolva!');
  };

  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">HR Felület</h1>
          <Button onClick={signOut} variant="outline">
            Kijelentkezés
          </Button>
        </div>

        <div className="grid gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Új Audit indítása</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="company">Cég neve</Label>
                <Input
                  id="company"
                  value={companyName}
                  onChange={(e) => setCompanyName(e.target.value)}
                  placeholder="Pl. Acme Corporation"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="questionnaire">Kérdőív kiválasztása</Label>
                <Select value={selectedQuestionnaire} onValueChange={setSelectedQuestionnaire}>
                  <SelectTrigger>
                    <SelectValue placeholder="Válasszon kérdőívet" />
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

              <Button 
                onClick={createAudit}
                disabled={!selectedQuestionnaire || !companyName || isCreating}
                className="w-full"
              >
                {isCreating ? 'Létrehozás...' : 'Audit indítása és Link generálása'}
              </Button>
            </CardContent>
          </Card>

          <div className="grid md:grid-cols-2 gap-4">
            <Card>
              <CardHeader>
                <CardTitle>Dashboard</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Statisztikák és grafikonok (hamarosan)
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Auditok</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Korábbi auditok listája (hamarosan)
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>

      <Dialog open={showLinkDialog} onOpenChange={setShowLinkDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Audit Link Generálva</DialogTitle>
            <DialogDescription>
              Küldd el ezt a linket a dolgozóknak a kérdőív kitöltéséhez
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div className="flex gap-2">
              <Input value={generatedLink} readOnly />
              <Button onClick={copyLink} size="icon">
                <Copy className="h-4 w-4" />
              </Button>
            </div>
            <p className="text-sm text-muted-foreground">
              QR kód generálás hamarosan elérhető lesz
            </p>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default HRDashboard;
