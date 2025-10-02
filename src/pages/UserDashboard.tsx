import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { toast } from 'sonner';
import { QuestionRenderer } from '@/components/survey/QuestionRenderer';
import { Progress } from '@/components/ui/progress';
import { Globe } from 'lucide-react';
import logo from '@/assets/doyoueap-logo.png';

interface Questionnaire {
  title: string;
  description: string;
  questions: {
    structure: string;
    demographics: any;
    branch_selector: any;
    branches: any;
  };
}

interface Audit {
  id: string;
  program_name: string;
  is_active: boolean;
  expires_at: string | null;
  logo_url: string | null;
  eap_program_url: string | null;
  available_languages: string[];
  custom_colors: { primary?: string };
  questionnaire: Questionnaire;
}

const UserDashboard = () => {
  const { token } = useParams<{ token: string }>();
  const [audit, setAudit] = useState<Audit | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [responses, setResponses] = useState<Record<string, any>>({});
  const [submitting, setSubmitting] = useState(false);
  const [currentStep, setCurrentStep] = useState<'language_select' | 'welcome' | 'demographics' | 'branch_selector' | 'branch_questions' | 'eap_info' | 'thank_you'>('language_select');
  const [selectedLanguage, setSelectedLanguage] = useState<string>('');
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
          program_name,
          is_active,
          expires_at,
          logo_url,
          eap_program_url,
          available_languages,
          custom_colors,
          questionnaire:questionnaires (
            title,
            description,
            questions
          )
        `)
        .eq('access_token', token)
        .maybeSingle();

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

  const validateCurrentQuestions = (questions: any[]): boolean => {
    for (const q of questions) {
      if (q.required && !responses[q.id]) {
        return false;
      }
    }
    return true;
  };

  const handleDemographicsNext = () => {
    const demoQuestions = audit?.questionnaire.questions.demographics.questions || [];
    if (!validateCurrentQuestions(demoQuestions)) {
      toast.error('Kérjük válaszolj minden kötelező kérdésre!');
      return;
    }
    setCurrentStep('branch_selector');
  };

  const handleBranchSelection = () => {
    const branchAnswer = responses['eap_knowledge'];
    if (!branchAnswer) {
      toast.error('Kérjük válaszd ki az egyik opciót!');
      return;
    }

    const branches = audit?.questionnaire.questions.branch_selector.branches;
    const branchKey = branches[branchAnswer];

    if (branchKey === 'redirect') {
      // Show EAP info page
      setCurrentStep('eap_info');
      return;
    }

    setSelectedBranch(branchKey);
    setCurrentStep('branch_questions');
    setCurrentBlockIndex(0);
  };

  const handleBlockNext = () => {
    if (!audit || !selectedBranch) return;

    const branch = audit.questionnaire.questions.branches[selectedBranch];
    const currentBlock = branch.blocks[currentBlockIndex];
    
    if (!validateCurrentQuestions(currentBlock.questions)) {
      toast.error('Kérjük válaszolj minden kötelező kérdésre!');
      return;
    }

    if (currentBlockIndex < branch.blocks.length - 1) {
      setCurrentBlockIndex(currentBlockIndex + 1);
    } else {
      // Last block, submit
      handleSubmit(new Event('submit') as any);
    }
  };

  const handleBlockPrevious = () => {
    if (currentBlockIndex > 0) {
      setCurrentBlockIndex(currentBlockIndex - 1);
    } else {
      setCurrentStep('branch_selector');
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
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
            branch: selectedBranch || 'redirect',
          },
        });

      if (error) throw error;

      setCurrentStep('thank_you');
    } catch (err) {
      console.error('Error submitting response:', err);
      toast.error('Hiba történt a válaszok mentésekor');
    } finally {
      setSubmitting(false);
    }
  };

  const getTotalProgress = () => {
    if (!audit || !selectedBranch) return 0;
    
    const branch = audit.questionnaire.questions.branches[selectedBranch];
    if (!branch) return 0;
    
    const totalBlocks = branch.blocks.length + 2; // +2 for demographics and branch selector
    let completedSteps = 0;
    
    if (currentStep === 'branch_selector') completedSteps = 1;
    else if (currentStep === 'branch_questions') completedSteps = 2 + currentBlockIndex;
    
    return (completedSteps / totalBlocks) * 100;
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

  if (!audit.questionnaire.questions.structure) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Hiba</CardTitle>
          </CardHeader>
          <CardContent>
            <Alert variant="destructive">
              <AlertDescription>Érvénytelen kérdőív struktúra</AlertDescription>
            </Alert>
          </CardContent>
        </Card>
      </div>
    );
  }

  const renderDemographics = () => {
    const demoQuestions = audit.questionnaire.questions.demographics.questions;
    
    return (
      <div className="space-y-6">
        <div>
          <h3 className="text-lg font-semibold mb-4">
            {audit.questionnaire.questions.demographics.title}
          </h3>
        </div>
        {demoQuestions.map((q: any) => (
          <QuestionRenderer
            key={q.id}
            question={q}
            value={responses[q.id]}
            onChange={(value) => handleResponseChange(q.id, value)}
          />
        ))}
        <Button 
          onClick={handleDemographicsNext} 
          className="w-full"
          style={{
            backgroundColor: primaryColor,
            borderColor: primaryColor,
          }}
        >
          Tovább
        </Button>
      </div>
    );
  };

  const renderBranchSelector = () => {
    const branchSelector = audit.questionnaire.questions.branch_selector;
    const programName = audit.program_name || 'EAP';
    
    // Create modified question with dynamic program name
    const modifiedQuestion = {
      ...branchSelector,
      question: `Tudtad, hogy a munkahelyeden elérhető egy támogatási program, amit ${programName} néven ismerhetsz? Ez a szolgáltatás segítséget nyújt neked és családodnak különböző munkahelyi vagy magánéleti kihívások kezeléséhez, például stresszhelyzetekben, konfliktusok megoldásában vagy akár pénzügyi tanácsadásban is.`
    };
    
    return (
      <div className="space-y-6">
        <QuestionRenderer
          question={modifiedQuestion}
          value={responses[branchSelector.id]}
          onChange={(value) => handleResponseChange(branchSelector.id, value)}
        />
        <Button 
          onClick={handleBranchSelection} 
          className="w-full"
          style={{
            backgroundColor: primaryColor,
            borderColor: primaryColor,
          }}
        >
          Tovább
        </Button>
      </div>
    );
  };

  const renderBranchQuestions = () => {
    if (!selectedBranch) return null;
    
    const branch = audit.questionnaire.questions.branches[selectedBranch];
    const currentBlock = branch.blocks[currentBlockIndex];
    const isLastBlock = currentBlockIndex === branch.blocks.length - 1;
    
    return (
      <div className="space-y-6">
        
        {currentBlock.questions.map((q: any) => (
          <QuestionRenderer
            key={q.id}
            question={q}
            value={responses[q.id]}
            onChange={(value) => handleResponseChange(q.id, value)}
          />
        ))}
        
        <div className="flex gap-4">
          <Button
            type="button"
            variant="outline"
            onClick={handleBlockPrevious}
            className="flex-1"
          >
            Vissza
          </Button>
          <Button
            onClick={handleBlockNext}
            disabled={submitting}
            className="flex-1"
            style={{
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            }}
          >
            {submitting ? 'Küldés...' : isLastBlock ? 'Befejezés' : 'Tovább'}
          </Button>
        </div>
      </div>
    );
  };

  const primaryColor = audit.custom_colors?.primary || '#3b82f6';

  const LANGUAGE_NAMES: Record<string, string> = {
    HU: 'Magyar',
    EN: 'English',
    DE: 'Deutsch',
    FR: 'Français',
    ES: 'Español',
    IT: 'Italiano',
    PT: 'Português',
    RO: 'Română',
    PL: 'Polski',
    NL: 'Nederlands',
    SV: 'Svenska',
    DA: 'Dansk',
    FI: 'Suomi',
    NO: 'Norsk',
    CS: 'Čeština',
    SK: 'Slovenčina',
    BG: 'Български',
    HR: 'Hrvatski',
    EL: 'Ελληνικά',
    ZH: '中文',
    JA: '日本語',
    KO: '한국어',
    AR: 'العربية',
    RU: 'Русский',
    TR: 'Türkçe',
  };

  const renderLanguageSelect = () => (
    <div className="space-y-6">
      <div className="grid gap-3">
        {audit?.available_languages?.map((langCode) => (
          <Button
            key={langCode}
            variant={selectedLanguage === langCode ? "default" : "outline"}
            className="w-full justify-start text-lg py-6"
            onClick={() => {
              setSelectedLanguage(langCode);
              setCurrentStep('welcome');
            }}
            style={selectedLanguage === langCode ? {
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            } : undefined}
          >
            {LANGUAGE_NAMES[langCode] || langCode}
          </Button>
        ))}
      </div>
    </div>
  );

  const renderWelcome = () => (
    <div className="space-y-6 text-center">
      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Üdvözlünk!</h2>
        <p className="text-muted-foreground">
          Ez a felmérés anonim, a kitöltés kb. 6–9 perc. A válaszok kizárólag összesítve, 
          statisztikai formában jelennek meg.
        </p>
      </div>
      <Button 
        onClick={() => setCurrentStep('demographics')} 
        className="w-full"
        style={{
          backgroundColor: primaryColor,
          borderColor: primaryColor,
        }}
      >
        Kezdés
      </Button>
    </div>
  );

  const renderEapInfo = () => (
    <div className="space-y-6">
      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Mi az EAP?</h2>
        <p className="text-foreground">
          Az EAP (Employee Assistance Program) egy munkavállalói segítő program, amely 
          különböző élethelyzetekben nyújt támogatást.
        </p>
        <p className="text-foreground">
          A program keretében hozzáférhetsz pszichológiai tanácsadáshoz, jogi segítséghez, 
          és számos más szolgáltatáshoz, amelyek segíthetnek a munkahelyi és magánéleti 
          kihívások kezelésében.
        </p>
        <div className="p-4 bg-secondary rounded-lg">
          <p className="font-semibold mb-2">Kattints az alábbi linkre és látogasd meg a program hivatalos weboldalát!</p>
          <a 
            href={audit?.eap_program_url || 'https://doyoueap.hu'} 
            target="_blank" 
            rel="noopener noreferrer"
            className="text-primary hover:underline"
          >
            {audit?.eap_program_url || 'doyoueap.hu'}
          </a>
        </div>
      </div>
      <Button 
        onClick={() => handleSubmit(new Event('submit') as any)} 
        className="w-full"
        style={{
          backgroundColor: primaryColor,
          borderColor: primaryColor,
        }}
      >
        Befejezés
      </Button>
    </div>
  );

  const renderThankYou = () => (
    <div className="space-y-6 text-center">
      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Köszönjük a részvételt!</h2>
        <p className="text-muted-foreground">
          Válaszaid segítenek abban, hogy munkáltatód még jobb munkahelyi környezetet 
          alakíthasson ki.
        </p>
        <p className="text-muted-foreground">
          Ez az ablak most bezárható.
        </p>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-background p-4 sm:p-8">
      <div className="max-w-3xl mx-auto">
        <Card>
          <CardHeader>
            <div className="flex justify-center mb-4">
              <img 
                src={audit?.logo_url || logo} 
                alt="Logo" 
                className="h-12 object-contain"
              />
            </div>
            {currentStep === 'branch_questions' && (
              <Progress 
                value={getTotalProgress()} 
                className="mt-4"
                style={{
                  '--progress-background': primaryColor
                } as React.CSSProperties}
              />
            )}
          </CardHeader>
          <CardContent>
            {currentStep === 'language_select' && renderLanguageSelect()}
            {currentStep === 'welcome' && renderWelcome()}
            {currentStep === 'demographics' && renderDemographics()}
            {currentStep === 'branch_selector' && renderBranchSelector()}
            {currentStep === 'branch_questions' && renderBranchQuestions()}
            {currentStep === 'eap_info' && renderEapInfo()}
            {currentStep === 'thank_you' && renderThankYou()}
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default UserDashboard;
