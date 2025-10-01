import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Textarea } from '@/components/ui/textarea';
import { toast } from 'sonner';

interface Audit {
  id: string;
  company_name: string;
  is_active: boolean;
  expires_at: string | null;
  questionnaire: {
    title: string;
    description: string;
    questions: any[];
  };
}

const UserDashboard = () => {
  const { token } = useParams<{ token: string }>();
  const [audit, setAudit] = useState<Audit | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [responses, setResponses] = useState<Record<string, any>>({});
  const [submitting, setSubmitting] = useState(false);

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
          questionnaire:questionnaires (
            title,
            description,
            questions
          )
        `)
        .eq('access_token', token)
        .single();

      if (error) throw error;

      if (!data) {
        setError('Érvénytelen audit link');
        return;
      }

      if (!data.is_active) {
        setError('Ez az audit már nem aktív');
        return;
      }

      if (data.expires_at && new Date(data.expires_at) < new Date()) {
        setError('Ez az audit lejárt');
        return;
      }

      setAudit(data as any);
    } catch (err) {
      console.error('Error fetching audit:', err);
      setError('Hiba történt az audit betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const handleResponseChange = (questionId: number, value: any) => {
    setResponses(prev => ({
      ...prev,
      [questionId]: value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!audit) return;

    // Validate all questions are answered
    const unansweredQuestions = audit.questionnaire.questions.filter(
      (q: any) => !responses[q.id]
    );

    if (unansweredQuestions.length > 0) {
      toast.error('Kérjük válaszolj minden kérdésre!');
      return;
    }
    
    setSubmitting(true);
    
    try {
      const { error } = await supabase
        .from('audit_responses')
        .insert({
          audit_id: audit.id,
          responses,
          employee_metadata: {
            submitted_at: new Date().toISOString(),
          },
        });

      if (error) throw error;

      toast.success('Köszönjük a kitöltést!');
      // Disable form after successful submission
      setAudit(null);
    } catch (err) {
      console.error('Error submitting response:', err);
      toast.error('Hiba történt a válaszok mentésekor');
    } finally {
      setSubmitting(false);
    }
  };

  const renderQuestion = (question: any) => {
    const { id, question: text, type } = question;

    switch (type) {
      case 'scale':
        const scale = question.scale || 5;
        return (
          <div key={id} className="space-y-3">
            <Label className="text-base font-medium">{text}</Label>
            <RadioGroup
              value={responses[id]?.toString()}
              onValueChange={(value) => handleResponseChange(id, parseInt(value))}
              className="flex justify-between"
            >
              {Array.from({ length: scale }, (_, i) => i + 1).map((value) => (
                <div key={value} className="flex flex-col items-center space-y-2">
                  <RadioGroupItem value={value.toString()} id={`q${id}-${value}`} />
                  <Label htmlFor={`q${id}-${value}`} className="text-sm font-normal">
                    {value}
                  </Label>
                </div>
              ))}
            </RadioGroup>
            <div className="flex justify-between text-xs text-muted-foreground">
              <span>Egyáltalán nem</span>
              <span>Teljes mértékben</span>
            </div>
          </div>
        );

      case 'yesno':
        return (
          <div key={id} className="space-y-3">
            <Label className="text-base font-medium">{text}</Label>
            <RadioGroup
              value={responses[id]}
              onValueChange={(value) => handleResponseChange(id, value)}
              className="flex gap-4"
            >
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="yes" id={`q${id}-yes`} />
                <Label htmlFor={`q${id}-yes`} className="font-normal">Igen</Label>
              </div>
              <div className="flex items-center space-x-2">
                <RadioGroupItem value="no" id={`q${id}-no`} />
                <Label htmlFor={`q${id}-no`} className="font-normal">Nem</Label>
              </div>
            </RadioGroup>
          </div>
        );

      case 'text':
        return (
          <div key={id} className="space-y-3">
            <Label className="text-base font-medium">{text}</Label>
            <Textarea
              value={responses[id] || ''}
              onChange={(e) => handleResponseChange(id, e.target.value)}
              placeholder="Írd ide a válaszod..."
              className="min-h-[100px]"
            />
          </div>
        );

      default:
        return null;
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <p>Betöltés...</p>
      </div>
    );
  }

  if (error || !audit) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Hiba történt</CardTitle>
          </CardHeader>
          <CardContent>
            <Alert variant="destructive">
              <AlertDescription>{error || 'Audit nem található'}</AlertDescription>
            </Alert>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-3xl mx-auto">
        <Card>
          <CardHeader>
            <CardTitle>{audit.questionnaire.title}</CardTitle>
            <CardDescription>
              {audit.company_name} - {audit.questionnaire.description}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-8">
              {audit.questionnaire.questions.map((question: any) => 
                renderQuestion(question)
              )}

              <Button type="submit" disabled={submitting} className="w-full">
                {submitting ? 'Küldés...' : 'Válaszok küldése'}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default UserDashboard;
