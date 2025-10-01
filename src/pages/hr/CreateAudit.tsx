import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'sonner';
import { Upload } from 'lucide-react';

interface Questionnaire {
  id: string;
  title: string;
  description: string;
}

const CreateAudit = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [questionnaires, setQuestionnaires] = useState<Questionnaire[]>([]);
  const [loading, setLoading] = useState(false);
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [newAudit, setNewAudit] = useState({
    companyName: '',
    questionnaireId: '',
    eapProgramUrl: 'https://doyoueap.hu',
    expiresAt: '',
  });

  useEffect(() => {
    fetchQuestionnaires();
  }, []);

  const fetchQuestionnaires = async () => {
    const { data } = await supabase
      .from('questionnaires')
      .select('id, title, description')
      .eq('is_active', true);

    if (data) {
      setQuestionnaires(data);
    }
  };

  const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      if (file.size > 2 * 1024 * 1024) {
        toast.error('A fájl mérete maximum 2MB lehet');
        return;
      }
      setLogoFile(file);
    }
  };

  const handleCreateAudit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!newAudit.companyName || !newAudit.questionnaireId) {
      toast.error('Kérlek töltsd ki az összes kötelező mezőt');
      return;
    }

    setLoading(true);

    try {
      let logoUrl = null;

      // Upload logo if provided
      if (logoFile) {
        const fileExt = logoFile.name.split('.').pop();
        const fileName = `${Math.random()}.${fileExt}`;
        const filePath = `logos/${fileName}`;

        const { error: uploadError } = await supabase.storage
          .from('audit-assets')
          .upload(filePath, logoFile);

        if (uploadError) throw uploadError;

        const { data: { publicUrl } } = supabase.storage
          .from('audit-assets')
          .getPublicUrl(filePath);

        logoUrl = publicUrl;
      }

      // Generate token
      const { data: tokenData, error: tokenError } = await supabase.rpc(
        'generate_access_token'
      );

      if (tokenError) throw tokenError;

      // Create audit
      const { error } = await supabase.from('audits').insert({
        hr_user_id: user?.id,
        company_name: newAudit.companyName,
        questionnaire_id: newAudit.questionnaireId,
        access_token: tokenData,
        logo_url: logoUrl,
        eap_program_url: newAudit.eapProgramUrl,
        expires_at: newAudit.expiresAt || null,
      });

      if (error) throw error;

      toast.success('Audit sikeresen létrehozva!');
      navigate('/hr');
    } catch (error) {
      console.error('Error creating audit:', error);
      toast.error('Hiba történt az audit létrehozásakor');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-8">
      <Card className="max-w-2xl mx-auto">
        <CardHeader>
          <CardTitle>Új Audit Létrehozása</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleCreateAudit} className="space-y-6">
            <div className="space-y-2">
              <Label htmlFor="companyName">Cég neve *</Label>
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
              <Label htmlFor="questionnaire">Kérdőív *</Label>
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

            <div className="space-y-2">
              <Label htmlFor="logo">Cég logó (opcionális, max 2MB)</Label>
              <div className="flex items-center gap-4">
                <Input
                  id="logo"
                  type="file"
                  accept="image/*"
                  onChange={handleLogoChange}
                  className="hidden"
                />
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => document.getElementById('logo')?.click()}
                >
                  <Upload className="mr-2 h-4 w-4" />
                  {logoFile ? logoFile.name : 'Logo feltöltése'}
                </Button>
              </div>
              <p className="text-sm text-muted-foreground">
                Ha nem töltesz fel logót, az alapértelmezett DoYouEAP logó jelenik meg
              </p>
            </div>

            <div className="space-y-2">
              <Label htmlFor="eapUrl">EAP program weboldal</Label>
              <Input
                id="eapUrl"
                type="url"
                value={newAudit.eapProgramUrl}
                onChange={(e) =>
                  setNewAudit({ ...newAudit, eapProgramUrl: e.target.value })
                }
                placeholder="https://doyoueap.hu"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="expiresAt">Lejárat dátuma (opcionális)</Label>
              <Input
                id="expiresAt"
                type="datetime-local"
                value={newAudit.expiresAt}
                onChange={(e) =>
                  setNewAudit({ ...newAudit, expiresAt: e.target.value })
                }
              />
            </div>

            <div className="flex gap-4">
              <Button 
                type="button" 
                variant="outline" 
                onClick={() => navigate('/hr')}
                className="flex-1"
              >
                Mégse
              </Button>
              <Button type="submit" disabled={loading} className="flex-1">
                {loading ? 'Létrehozás...' : 'Audit Létrehozása'}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};

export default CreateAudit;
