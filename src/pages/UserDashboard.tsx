import { useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';

interface Audit {
  id: string;
  company_name: string;
  is_active: boolean;
  expires_at: string | null;
  questionnaires: {
    title: string;
    description: string;
    questions: any[];
  };
}

const UserDashboard = () => {
  const { token } = useParams();
  const [audit, setAudit] = useState<Audit | null>(null);
  const [loading, setLoading] = useState(true);
  const [responses, setResponses] = useState<Record<string, any>>({});
  const [submitted, setSubmitted] = useState(false);

  useEffect(() => {
    if (token) {
      fetchAudit();
    }
  }, [token]);

  const fetchAudit = async () => {
    try {
      const { data, error } = await supabase
        .from('audits')
        .select(`
          id,
          company_name,
          is_active,
          expires_at,
          questionnaires (
            title,
            description,
            questions
          )
        `)
        .eq('access_token', token)
        .single();

      if (error) throw error;

      if (!data.is_active) {
        toast.error('Ez az audit már nem aktív');
        return;
      }

      if (data.expires_at && new Date(data.expires_at) < new Date()) {
        toast.error('Ez az audit lejárt');
        return;
      }

      setAudit(data as any);
    } catch (error: any) {
      console.error('Error fetching audit:', error);
      toast.error('Nem sikerült betölteni a kérdőívet');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async () => {
    if (!audit) return;

    try {
      const { error } = await supabase
        .from('audit_responses')
        .insert({
          audit_id: audit.id,
          responses: responses,
          employee_metadata: {
            submitted_at: new Date().toISOString(),
            user_agent: navigator.userAgent,
          },
        });

      if (error) throw error;

      toast.success('Köszönjük a válaszokat!');
      setSubmitted(true);
    } catch (error: any) {
      console.error('Error submitting response:', error);
      toast.error('Hiba történt a küldés során');
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p>Betöltés...</p>
      </div>
    );
  }

  if (!audit) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Érvénytelen kérdőív</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              Ez a kérdőív link érvénytelen vagy lejárt.
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (submitted) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Köszönjük!</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              Válaszai sikeresen elküldve. Ezt az ablakot most bezárhatja.
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-3xl mx-auto">
        <Card className="mb-6">
          <CardHeader>
            <CardTitle>{audit.questionnaires.title}</CardTitle>
            <CardDescription>
              {audit.company_name} • {audit.questionnaires.description}
            </CardDescription>
          </CardHeader>
        </Card>

        <Card>
          <CardContent className="pt-6 space-y-6">
            <p className="text-muted-foreground">
              Kérdőív kérdések itt jelennek meg...
              (A kérdések struktúrája később kerül implementálásra)
            </p>

            <Button onClick={handleSubmit} className="w-full">
              Válaszok beküldése
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default UserDashboard;
