import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { toast } from 'sonner';
import { QuestionRenderer } from '@/components/survey/QuestionRenderer';
import { Progress } from '@/components/ui/progress';

interface Audit {
  id: string;
  company_name: string;
  is_active: boolean;
  expires_at: string | null;
  questionnaire: {
    title: string;
    description: string;
    questions: any;
  };
}

type SurveyStep = 'demographics' | 'branch_selector' | 'branch_questions' | 'completed' | 'redirect';

const UserDashboard = () => {
  const { token } = useParams<{ token: string }>();
  const [audit, setAudit] = useState<Audit | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  // Survey state
  const [step, setStep] = useState<SurveyStep>('demographics');
  const [responses, setResponses] = useState<Record<string, any>>({});
  const [selectedBranch, setSelectedBranch] = useState<string | null>(null);
  const [currentBlockIndex, setCurrentBlockIndex] = useState(0);

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

  const handleResponseChange = (questionId: string, value: any) => {
    setResponses(prev => ({
      ...prev,
      [questionId]: value,
    }));
  };

  const validateCurrentStep = (): boolean => {
    if (!audit) return false;

    const questions = audit.questionnaire.questions;

    if (step === 'demographics') {
      const demoQuestions = questions.demographics.questions;
      return demoQuestions.every((q: any) => 
        !q.required || responses[q.id] !== undefined
      );
    }

    if (step === 'branch_selector') {
      return responses[questions.branch_selector.id] !== undefined;
    }

    if (step === 'branch_questions' && selectedBranch) {
      const branch = questions.branches[selectedBranch];
      if (currentBlockIndex < branch.blocks.length) {
        const currentBlock = branch.blocks[currentBlockIndex];
        return currentBlock.questions.every((q: any) =>
          !q.required || responses[q.id] !== undefined
        );
      }
    }

    return false;
  };

  const handleNext = () => {
    if (!validateCurrentStep()) {
      toast.error('Kérjük válaszolj minden kötelező kérdésre!');
      return;
    }

    if (step === 'demographics') {
      setStep('branch_selector');
    } else if (step === 'branch_selector') {
      const branchAnswer = responses[audit!.questionnaire.questions.branch_selector.id];
      const branches = audit!.questionnaire.questions.branch_selector.branches;
      const branchKey = branches[branchAnswer];
      
      if (branchKey === 'redirect') {
        setStep('redirect');
        // TODO: Implement redirect to HR-provided URL
        toast.info('Átirányítás...');
      } else {
        setSelectedBranch(branchKey);
        setStep('branch_questions');
        setCurrentBlockIndex(0);
      }
    } else if (step === 'branch_questions' && selectedBranch) {
      const branch = audit!.questionnaire.questions.branches[selectedBranch];
      if (currentBlockIndex < branch.blocks.length - 1) {
        setCurrentBlockIndex(currentBlockIndex + 1);
      } else {
        handleSubmit();
      }
    }
  };

  const handleBack = () => {
    if (step === 'branch_questions' && currentBlockIndex > 0) {
      setCurrentBlockIndex(currentBlockIndex - 1);
    } else if (step === 'branch_questions') {
      setStep('branch_selector');
      setSelectedBranch(null);
      setCurrentBlockIndex(0);
    } else if (step === 'branch_selector') {
      setStep('demographics');
    }
  };

  const handleSubmit = async () => {
    if (!audit) return;

    setSubmitting(true);

    try {
      const { error } = await supabase
        .from('audit_responses')
        .insert({
          audit_id: audit.id,
          responses,
          employee_metadata: {
            submitted_at: new Date().toISOString(),
            branch: selectedBranch,
            gender: responses.gender,
            age: responses.age,
          },
        });

      if (error) throw error;

      toast.success('Köszönjük a kitöltést!');
      setStep('completed');
    } catch (err) {
      console.error('Error submitting response:', err);
      toast.error('Hiba történt a válaszok mentésekor');
    } finally {
      setSubmitting(false);
    }
  };

  const calculateProgress = (): number => {
    if (!audit) return 0;
    
    const questions = audit.questionnaire.questions;
    let totalSteps = 2; // demographics + branch_selector
    
    if (selectedBranch) {
      const branch = questions.branches[selectedBranch];
      totalSteps += branch.blocks.length;
    }

    let currentStep = 0;
    if (step === 'demographics') currentStep = 0;
    else if (step === 'branch_selector') currentStep = 1;
    else if (step === 'branch_questions') currentStep = 2 + currentBlockIndex;
    else if (step === 'completed') currentStep = totalSteps;

    return (currentStep / totalSteps) * 100;
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

  if (step === 'completed') {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Köszönjük!</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              A kérdőív kitöltése sikeresen megtörtént. Válaszaid segítenek javítani az EAP programot.
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (step === 'redirect') {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Átirányítás</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">
              Átirányítunk az EAP programról szóló információkhoz...
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  const questions = audit.questionnaire.questions;
  let currentQuestions: any[] = [];
  let currentTitle = '';

  if (step === 'demographics') {
    currentQuestions = questions.demographics.questions;
    currentTitle = questions.demographics.title;
  } else if (step === 'branch_selector') {
    currentQuestions = [questions.branch_selector];
    currentTitle = 'Ágválasztó kérdés';
  } else if (step === 'branch_questions' && selectedBranch) {
    const branch = questions.branches[selectedBranch];
    const currentBlock = branch.blocks[currentBlockIndex];
    currentQuestions = currentBlock.questions;
    currentTitle = currentBlock.title;
  }

  return (
    <div className="min-h-screen bg-background p-4 md:p-8">
      <div className="max-w-3xl mx-auto">
        <Card>
          <CardHeader>
            <CardTitle>{audit.questionnaire.title}</CardTitle>
            <CardDescription>
              {audit.company_name}
            </CardDescription>
            <Progress value={calculateProgress()} className="mt-4" />
          </CardHeader>
          <CardContent>
            <div className="space-y-8">
              <h3 className="text-lg font-semibold">{currentTitle}</h3>
              
              {currentQuestions.map((question: any) => (
                <QuestionRenderer
                  key={question.id}
                  question={question}
                  value={responses[question.id]}
                  onChange={(value) => handleResponseChange(question.id, value)}
                />
              ))}

              <div className="flex gap-4 justify-between">
                {(step !== 'demographics') && (
                  <Button 
                    type="button" 
                    variant="outline" 
                    onClick={handleBack}
                  >
                    Vissza
                  </Button>
                )}
                <Button 
                  type="button" 
                  onClick={handleNext}
                  disabled={submitting}
                  className="ml-auto"
                >
                  {submitting ? 'Küldés...' : 
                   step === 'branch_questions' && selectedBranch && 
                   currentBlockIndex === questions.branches[selectedBranch].blocks.length - 1 
                     ? 'Befejezés' 
                     : 'Tovább'}
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default UserDashboard;
