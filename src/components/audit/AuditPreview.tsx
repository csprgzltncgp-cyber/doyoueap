import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Progress } from '@/components/ui/progress';
import { QuestionRenderer } from '@/components/survey/QuestionRenderer';
import { Eye, ChevronRight, ChevronLeft } from 'lucide-react';
import logo from '@/assets/eap-pulse-logo-blue.png';
import { supabase } from '@/integrations/supabase/client';

interface AuditPreviewProps {
  auditData: {
    programName: string;
    customColors: {
      primary?: string;
    };
    giftId: string;
    drawMode: 'auto' | 'manual';
    expiresAt: string;
    logoFile: File | null;
  };
  onNext: () => void;
  onBack: () => void;
}

interface Gift {
  id: string;
  name: string;
  value_eur: number;
  description: string | null;
  image_url: string | null;
}

interface Questionnaire {
  title: string;
  description: string;
  questions: {
    structure: string;
    demographics: {
      title: string;
      questions: any[];
    };
    branch_selector: {
      id: string;
      question: string;
      type: string;
      options: string[];
      branches: Record<string, string>;
    };
    branches: Record<string, {
      title: string;
      blocks: {
        title: string;
        questions: any[];
      }[];
    }>;
  };
}

export const AuditPreview = ({ auditData, onNext, onBack }: AuditPreviewProps) => {
  const [currentStep, setCurrentStep] = useState<'welcome' | 'demographics' | 'branch_selector' | 'branch_questions'>('welcome');
  const [questionnaire, setQuestionnaire] = useState<Questionnaire | null>(null);
  const [gift, setGift] = useState<Gift | null>(null);
  const [currentBlockIndex, setCurrentBlockIndex] = useState(0);
  const [selectedBranch, setSelectedBranch] = useState<string>('know_and_use');
  const [logoPreview, setLogoPreview] = useState<string | null>(null);

  const primaryColor = auditData.customColors?.primary || '#3b82f6';
  const hasLottery = !!auditData.giftId;

  useEffect(() => {
    fetchQuestionnaireAndGift();
    if (auditData.logoFile) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setLogoPreview(reader.result as string);
      };
      reader.readAsDataURL(auditData.logoFile);
    }
  }, [auditData.giftId, auditData.logoFile]);

  const fetchQuestionnaireAndGift = async () => {
    try {
      // Fetch questionnaire
      const { data: questionnaireData, error: qError } = await supabase
        .from('questionnaires')
        .select('title, description, questions')
        .eq('is_active', true)
        .limit(1)
        .single();

      if (qError) throw qError;
      setQuestionnaire(questionnaireData as Questionnaire);

      // Fetch gift if exists
      if (auditData.giftId) {
        const { data: giftData, error: gError } = await supabase
          .from('gifts')
          .select('id, name, value_eur, description, image_url')
          .eq('id', auditData.giftId)
          .single();

        if (!gError && giftData) {
          setGift(giftData);
        }
      }
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };

  const formatEUR = (value: number): string => {
    const formatted = new Intl.NumberFormat('hu-HU', {
      style: 'currency',
      currency: 'EUR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value);
    return formatted.replace('EUR', '€').trim();
  };

  const getTotalProgress = () => {
    if (!questionnaire || !selectedBranch) return { percent: 0, current: 0, total: 0 };
    
    const branch = questionnaire.questions.branches[selectedBranch];
    if (!branch) return { percent: 0, current: 0, total: 0 };
    
    const totalSteps = branch.blocks.length;
    const currentStepNum = currentBlockIndex + 1;
    
    return {
      percent: totalSteps > 0 ? (currentStepNum / totalSteps) * 100 : 0,
      current: currentStepNum,
      total: totalSteps
    };
  };

  const handleBlockNext = () => {
    if (!questionnaire || !selectedBranch) return;

    const branch = questionnaire.questions.branches[selectedBranch];
    if (currentBlockIndex < branch.blocks.length - 1) {
      setCurrentBlockIndex(currentBlockIndex + 1);
    } else {
      // Finished preview
      onNext();
    }
  };

  const handleBlockPrevious = () => {
    if (currentBlockIndex > 0) {
      setCurrentBlockIndex(currentBlockIndex - 1);
    } else {
      setCurrentStep('branch_selector');
    }
  };

  const renderWelcome = () => (
    <div className="space-y-6">
      <div className="space-y-4 text-center">
        <h2 className="text-2xl font-bold">Üdvözlünk!</h2>
        <p className="text-muted-foreground">
          Ez a felmérés anonim, a kitöltés kb. 6–9 perc. A válaszok kizárólag összesítve, 
          statisztikai formában jelennek meg.
        </p>
      </div>
      
      {hasLottery && gift && (
        <div className="grid md:grid-cols-2 gap-6">
          <Alert className="border-primary/20 bg-primary/5">
            <AlertDescription className="space-y-3">
              <p className="font-semibold text-lg">Nyereményjáték!</p>
              <p>
                A felmérés kitöltésével automatikusan részt veszel egy <strong>{gift.name}</strong> értékű 
                ajándék sorsolásán (értéke: <strong>{formatEUR(gift.value_eur)}</strong>).
              </p>
              <div className="space-y-2 text-sm">
                <p>
                  <strong>Fontos információk:</strong>
                </p>
                <ul className="list-disc list-inside space-y-1 text-left">
                  <li>A felmérés végén kapsz egy egyedi <strong>sorsolási kódot</strong></li>
                  <li>Ezt a kódot <strong>mindenképpen mentsd el vagy írd fel</strong> – ezzel tudsz nyerni!</li>
                  <li>
                    A felmérés végén megadhatsz egy e-mail-címet, ha szeretnéd, hogy e-mailben értesítsünk a nyeremény esetén.
                  </li>
                  <li>
                    A sorsolás lezárultával azonban a HR-osztály a nyertes sorszámot közzéteszi, így e-mail megadása nélkül is értesülni fogsz róla, ha Te nyertél.
                    {auditData.drawMode === 'auto' ? (
                      auditData.expiresAt ? (
                        <> A sorsolás automatikusan megtörténik: <strong>{new Date(auditData.expiresAt).toLocaleDateString('hu-HU')}</strong></>
                      ) : (
                        <> A sorsolás automatikusan megtörténik.</>
                      )
                    ) : (
                      auditData.expiresAt ? (
                        <> A sorsolás a felmérés lezárása után történik: <strong>{new Date(auditData.expiresAt).toLocaleDateString('hu-HU')}</strong></>
                      ) : (
                        <> A sorsolás a felmérés lezárása után történik.</>
                      )
                    )}
                  </li>
                </ul>
              </div>
            </AlertDescription>
          </Alert>

          <Card className="overflow-hidden">
            <CardHeader className="pb-3">
              <CardTitle className="text-lg">Fődíj</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {gift.image_url && (
                <div className="relative aspect-video w-full overflow-hidden rounded-lg bg-muted">
                  <img 
                    src={gift.image_url} 
                    alt={gift.name}
                    className="h-full w-full object-cover"
                  />
                </div>
              )}
              <div className="space-y-2">
                <h3 className="font-semibold text-lg">{gift.name}</h3>
                <p className="text-2xl font-bold text-primary">{formatEUR(gift.value_eur)}</p>
                {gift.description && (
                  <p className="text-sm text-muted-foreground">{gift.description}</p>
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      )}
      
      <div className="flex justify-center">
        <Button 
          onClick={() => setCurrentStep('demographics')}
          className="px-12"
          style={{
            backgroundColor: primaryColor,
            borderColor: primaryColor,
          }}
        >
          Kezdés
        </Button>
      </div>
    </div>
  );

  const renderDemographics = () => {
    if (!questionnaire) return null;
    const demoQuestions = questionnaire.questions.demographics.questions;
    
    return (
      <div className="space-y-6">
        <div>
          <h3 className="text-lg font-semibold mb-4">
            {questionnaire.questions.demographics.title}
          </h3>
        </div>
        {demoQuestions.slice(0, 2).map((q: any) => (
          <QuestionRenderer
            key={q.id}
            question={q}
            value={undefined}
            onChange={() => {}}
          />
        ))}
        <div className="flex gap-4">
          <Button 
            onClick={() => setCurrentStep('welcome')} 
            variant="outline"
            className="flex-1"
          >
            <ChevronLeft className="h-4 w-4 mr-2" />
            Vissza
          </Button>
          <Button 
            onClick={() => setCurrentStep('branch_selector')} 
            className="flex-1"
            style={{
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            }}
          >
            Tovább
            <ChevronRight className="h-4 w-4 ml-2" />
          </Button>
        </div>
      </div>
    );
  };

  const renderBranchSelector = () => {
    if (!questionnaire) return null;
    const branchSelector = questionnaire.questions.branch_selector;
    const programName = auditData.programName || 'EAP';
    
    const modifiedQuestion = {
      ...branchSelector,
      question: `Tudtad, hogy a munkahelyeden elérhető egy támogatási program, amit ${programName} néven ismerhetsz? Ez a szolgáltatás segítséget nyújt neked és családodnak különböző munkahelyi vagy magánéleti kihívások kezeléséhez, például stresszhelyzetekben, konfliktusok megoldásában vagy akár pénzügyi tanácsadásban is.`
    };
    
    return (
      <div className="space-y-6">
        <QuestionRenderer
          question={modifiedQuestion}
          value={undefined}
          onChange={() => {}}
        />
        <div className="flex gap-4">
          <Button 
            onClick={() => setCurrentStep('demographics')} 
            variant="outline"
            className="flex-1"
          >
            <ChevronLeft className="h-4 w-4 mr-2" />
            Vissza
          </Button>
          <Button 
            disabled
            className="flex-1 opacity-50 cursor-not-allowed"
            style={{
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            }}
          >
            Tovább
            <ChevronRight className="h-4 w-4 ml-2" />
          </Button>
        </div>
      </div>
    );
  };

  const renderBranchQuestions = () => {
    if (!questionnaire || !selectedBranch) return null;
    
    const branch = questionnaire.questions.branches?.[selectedBranch];
    if (!branch || !branch.blocks || branch.blocks.length === 0) {
      return (
        <div className="text-center py-8 text-muted-foreground">
          Betöltés...
        </div>
      );
    }
    
    const currentBlock = branch.blocks[currentBlockIndex];
    if (!currentBlock || !currentBlock.questions) {
      return null;
    }
    
    const isLastBlock = currentBlockIndex === branch.blocks.length - 1;
    
    return (
      <div className="space-y-6">
        {currentBlock.questions.slice(0, 2).map((q: any) => (
          <QuestionRenderer
            key={q.id}
            question={q}
            value={undefined}
            onChange={() => {}}
          />
        ))}
        
        <div className="flex gap-4">
          <Button
            type="button"
            variant="outline"
            onClick={handleBlockPrevious}
            className="flex-1"
          >
            <ChevronLeft className="h-4 w-4 mr-2" />
            Vissza
          </Button>
          <Button
            onClick={handleBlockNext}
            className="flex-1"
            style={{
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            }}
          >
            {isLastBlock ? 'Előnézet befejezése' : 'Tovább'}
            <ChevronRight className="h-4 w-4 ml-2" />
          </Button>
        </div>
      </div>
    );
  };

  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Felmérés előnézete</h2>
        <p className="text-muted-foreground text-lg">
          Nézd meg, hogyan fogják látni a munkavállalók a felmérésedet
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Eye className="h-6 w-6" />
            Előnézet
          </CardTitle>
          <CardDescription className="text-base">
            Lépkedj végig a felmérés nyitó oldalain, ahogy majd a kitöltők látják
          </CardDescription>
        </CardHeader>
        <CardContent className="pt-6">
          {!questionnaire ? (
            <div className="min-h-[600px] flex items-center justify-center">
              <p className="text-muted-foreground">Betöltés...</p>
            </div>
          ) : (
            <div className="min-h-[600px] bg-background/50 rounded-lg border-2 border-dashed border-muted p-8">
              <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex justify-center">
                  <img 
                    src={logoPreview || logo} 
                    alt="Logo" 
                    className="h-12 object-contain"
                  />
                </div>
                
                {currentStep === 'welcome' && renderWelcome()}
                {currentStep === 'demographics' && renderDemographics()}
                {currentStep === 'branch_selector' && renderBranchSelector()}
              </div>
            </div>
          )}
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button onClick={onBack} variant="outline" size="lg">
          Vissza
        </Button>
        <Button onClick={onNext} size="lg">
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
