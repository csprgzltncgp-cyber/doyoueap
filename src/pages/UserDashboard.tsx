import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
import { QuestionRenderer } from '@/components/survey/QuestionRenderer';
import { Progress } from '@/components/ui/progress';
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

  useEffect(() => {
    if (audit && currentStep === 'language_select') {
      if (audit.available_languages && audit.available_languages.length === 1) {
        setSelectedLanguage(audit.available_languages[0]);
        setCurrentStep('welcome');
      }
    }
  }, [audit, currentStep]);

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
        setError('Érvénytelen felmérés link');
        return;
      }
      if (!data.is_active) {
        setError('Ez a felmérés már nem aktív');
        return;
      }
      if (data.expires_at && new Date(data.expires_at) < new Date()) {
        setError('Ez a felmérés lejárt');
        return;
      }

      setAudit(data as any);
    } catch (err) {
      console.error('Error fetching audit:', err);
      setError('Hiba történt a felmérés betöltésekor');
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
      setCurrentStep('eap_info');
      return;
    }

    setSelectedBranch(branchKey);
    setCurrentBlockIndex(0);
    setCurrentStep('branch_questions');
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
      handleSubmit();
    }
  };

  const handleSubmit = async () => {
    if (!audit || submitting) return;

    setSubmitting(true);

    try {
      const employeeMetadata = {
        branch: selectedBranch || 'redirect',
        submitted_at: new Date().toISOString(),
      };

      const { error } = await supabase
        .from('audit_responses')
        .insert({
          audit_id: audit.id,
          responses: responses,
          employee_metadata: employeeMetadata,
        });

      if (error) throw error;

      setCurrentStep('thank_you');
    } catch (error) {
      console.error('Error submitting responses:', error);
      toast.error('Hiba történt a válaszok elküldésekor');
    } finally {
      setSubmitting(false);
    }
  };

  const getTotalProgress = () => {
    if (!audit || !selectedBranch) return 0;
    
    const branch = audit.questionnaire.questions.branches[selectedBranch];
    if (!branch) return 0;
    
    const totalBlocks = branch.blocks.length + 2;
    let completedSteps = 0;
    
    if (currentStep === 'branch_selector') completedSteps = 1;
    else if (currentStep === 'branch_questions') completedSteps = 2 + currentBlockIndex;
    
    return (completedSteps / totalBlocks) * 100;
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center" style={{ backgroundColor: '#f5f5f5' }}>
        <p className="text-lg">Betöltés...</p>
      </div>
    );
  }

  if (error || !audit || !audit.questionnaire.questions.structure) {
    return (
      <div className="min-h-screen flex items-center justify-center p-4" style={{ backgroundColor: '#f5f5f5' }}>
        <div className="bg-white rounded-2xl p-8 max-w-md text-center">
          <h2 className="text-xl font-semibold mb-4">Hiba történt</h2>
          <p className="text-gray-600">{error || 'Felmérés nem található'}</p>
        </div>
      </div>
    );
  }

  const renderLanguageSelect = () => (
    <div className="space-y-8 text-center">
      <h2 className="text-2xl font-medium">
        Válassz nyelvet / Select language
      </h2>
      
      <div className="flex flex-col gap-3 max-w-md mx-auto">
        {audit.available_languages.map(lang => (
          <button
            key={lang}
            onClick={() => {
              setSelectedLanguage(lang);
              setCurrentStep('welcome');
            }}
            className="px-8 py-4 bg-gray-200 hover:bg-gray-300 rounded-full text-base font-medium transition-colors"
          >
            {lang === 'HU' ? '🇭🇺 Magyar' : lang === 'EN' ? '🇬🇧 English' : lang}
          </button>
        ))}
      </div>
    </div>
  );

  const renderWelcome = () => (
    <div className="space-y-8 text-center">
      <div className="space-y-4">
        <h2 className="text-2xl font-medium">
          Köszönjük, hogy időt szánsz ránk!
        </h2>
        <p className="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
          A kérdőív anonim és kb. 5-10 percet vesz igénybe. Válaszaid segítenek nekünk, 
          hogy tovább fejlesszük a {audit.program_name || 'programot'}.
        </p>
      </div>

      <button
        onClick={() => setCurrentStep('demographics')}
        className="px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide"
      >
        Tovább
      </button>
    </div>
  );

  const renderDemographics = () => {
    const demoQuestions = audit.questionnaire.questions.demographics.questions;
    
    return (
      <div className="space-y-6">
        <div className="text-center space-y-2">
          <h3 className="text-xl font-medium">
            {audit.questionnaire.questions.demographics.title}
          </h3>
          {audit.questionnaire.questions.demographics.description && (
            <p className="text-gray-600 italic">
              {audit.questionnaire.questions.demographics.description}
            </p>
          )}
        </div>

        <div className="space-y-6">
          {demoQuestions.map((question: any) => (
            <QuestionRenderer
              key={question.id}
              question={question}
              value={responses[question.id]}
              onChange={(value) => handleResponseChange(question.id, value)}
            />
          ))}
        </div>

        <div className="flex gap-4 justify-center pt-4">
          <button
            onClick={() => setCurrentStep('welcome')}
            className="px-12 py-4 bg-cyan-400 hover:bg-cyan-500 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide"
          >
            Vissza
          </button>
          <button
            onClick={handleDemographicsNext}
            className="px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide"
          >
            Tovább
          </button>
        </div>
      </div>
    );
  };

  const renderBranchSelector = () => {
    const branchSelectorData = audit.questionnaire.questions.branch_selector;

    return (
      <div className="space-y-8 text-center">
        <h3 className="text-xl font-medium">{branchSelectorData.question}</h3>

        <div className="flex flex-wrap gap-3 justify-center max-w-2xl mx-auto">
          {branchSelectorData.options.map((option: string) => {
            const isSelected = responses['eap_knowledge'] === option;
            return (
              <button
                key={option}
                onClick={() => handleResponseChange('eap_knowledge', option)}
                className={`px-8 py-4 rounded-full text-base font-medium transition-colors ${
                  isSelected 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-gray-200 hover:bg-gray-300'
                }`}
              >
                {option}
              </button>
            );
          })}
        </div>

        <div className="flex gap-4 justify-center pt-4">
          <button
            onClick={() => setCurrentStep('demographics')}
            className="px-12 py-4 bg-cyan-400 hover:bg-cyan-500 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide"
          >
            Vissza
          </button>
          <button
            onClick={handleBranchSelection}
            className="px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide"
          >
            Tovább
          </button>
        </div>
      </div>
    );
  };

  const renderBranchQuestions = () => {
    if (!selectedBranch) return null;

    const branch = audit.questionnaire.questions.branches[selectedBranch];
    if (!branch) return null;

    const currentBlock = branch.blocks[currentBlockIndex];

    return (
      <div className="space-y-6">
        <div className="text-center space-y-2">
          <h3 className="text-xl font-medium">{currentBlock.title}</h3>
          {currentBlock.description && (
            <p className="text-gray-600 italic">{currentBlock.description}</p>
          )}
        </div>

        <div className="space-y-6">
          {currentBlock.questions.map((question: any) => (
            <QuestionRenderer
              key={question.id}
              question={question}
              value={responses[question.id]}
              onChange={(value) => handleResponseChange(question.id, value)}
            />
          ))}
        </div>

        <div className="flex gap-4 justify-center pt-4">
          <button
            onClick={() => {
              if (currentBlockIndex > 0) {
                setCurrentBlockIndex(currentBlockIndex - 1);
              } else {
                setCurrentStep('branch_selector');
              }
            }}
            className="px-12 py-4 bg-cyan-400 hover:bg-cyan-500 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide"
          >
            Vissza
          </button>
          <button
            onClick={handleBlockNext}
            disabled={submitting}
            className="px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide disabled:opacity-50"
          >
            {currentBlockIndex < branch.blocks.length - 1 ? 'Tovább' : 'Befejezés'}
          </button>
        </div>
      </div>
    );
  };

  const renderEapInfo = () => (
    <div className="space-y-8 text-center">
      <div className="space-y-4">
        <h2 className="text-2xl font-medium">
          Ismerd meg az EAP programot!
        </h2>
        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
          Ha szeretnél többet megtudni a programról, látogass el az alábbi oldalra:
        </p>
        <a
          href={audit.eap_program_url || 'https://doyoueap.hu'}
          target="_blank"
          rel="noopener noreferrer"
          className="inline-block text-blue-600 hover:text-blue-700 font-medium underline"
        >
          {audit.eap_program_url || 'https://doyoueap.hu'}
        </a>
      </div>

      <button
        onClick={() => handleSubmit()}
        disabled={submitting}
        className="px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-base font-semibold transition-colors uppercase tracking-wide disabled:opacity-50"
      >
        Befejezés
      </button>
    </div>
  );

  const renderThankYou = () => (
    <div className="space-y-8 text-center">
      <div className="space-y-4">
        <h2 className="text-2xl font-medium">
          Köszönjük a válaszaidat!
        </h2>
        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
          Válaszaid nagy segítségünkre lesznek a program fejlesztésében.
        </p>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen p-6" style={{ backgroundColor: '#f5f5f5' }}>
      <div className="max-w-4xl mx-auto">
        {/* Logo */}
        <div className="flex justify-center mb-8">
          <img 
            src={audit.logo_url || logo} 
            alt="Logo" 
            className="h-12 object-contain"
          />
        </div>

        {/* Progress Bar */}
        {currentStep === 'branch_questions' && (
          <div className="mb-8">
            <Progress value={getTotalProgress()} className="h-2" />
          </div>
        )}

        {/* Main Content */}
        <div className="bg-white rounded-2xl p-8 shadow-sm">
          {currentStep === 'language_select' && renderLanguageSelect()}
          {currentStep === 'welcome' && renderWelcome()}
          {currentStep === 'demographics' && renderDemographics()}
          {currentStep === 'branch_selector' && renderBranchSelector()}
          {currentStep === 'branch_questions' && renderBranchQuestions()}
          {currentStep === 'eap_info' && renderEapInfo()}
          {currentStep === 'thank_you' && renderThankYou()}
        </div>
      </div>
    </div>
  );
};

export default UserDashboard;
