import { useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { QuestionRenderer } from "@/components/survey/QuestionRenderer";
import { Progress } from "@/components/ui/progress";
import { Info } from "lucide-react";
import logo from "@/assets/logo.png";

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

export default function AuditQuestionnaire() {
  const [questionnaire, setQuestionnaire] = useState<Questionnaire | null>(null);
  const [loading, setLoading] = useState(true);
  const [demoResponses, setDemoResponses] = useState<Record<string, any>>({});
  const [currentStep, setCurrentStep] = useState<'welcome' | 'demographics' | 'branch_selector' | 'branch_questions' | 'eap_info'>('welcome');
  const [selectedBranch, setSelectedBranch] = useState<string | null>(null);
  const [currentBlockIndex, setCurrentBlockIndex] = useState(0);

  useEffect(() => {
    fetchQuestionnaire();
  }, []);

  // Scroll to top funkció - közvetlenül a main elemre görgeti
  const scrollToTop = () => {
    const mainElement = document.querySelector('main');
    if (mainElement) {
      mainElement.scrollTop = 0;
    }
  };

  const fetchQuestionnaire = async () => {
    try {
      const { data, error } = await supabase
        .from("questionnaires")
        .select("*")
        .eq("is_active", true)
        .maybeSingle();

      if (error) throw error;
      if (data) {
        setQuestionnaire(data);
      }
    } catch (error) {
      console.error("Error fetching questionnaire:", error);
    } finally {
      setLoading(false);
    }
  };

  const handleResponseChange = (questionId: string, value: any) => {
    setDemoResponses(prev => ({
      ...prev,
      [questionId]: value,
    }));
  };

  const getOverallProgress = () => {
    // Teljes felmérés progress számítása
    let current = 0;
    let total = 5; // welcome, demographics, branch_selector, branch_questions (több blokk), befejezés
    
    if (currentStep === 'welcome') current = 0;
    else if (currentStep === 'demographics') current = 1;
    else if (currentStep === 'branch_selector') current = 2;
    else if (currentStep === 'eap_info') current = 4;
    else if (currentStep === 'branch_questions' && selectedBranch) {
      const branch = questionnaire?.questions.branches[selectedBranch];
      if (branch) {
        total = 2 + branch.blocks.length; // demographics + branch_selector + blocks
        current = 2 + currentBlockIndex + 1;
      }
    }
    
    return {
      percent: total > 0 ? (current / total) * 100 : 0,
      current: current,
      total: total
    };
  };

  if (loading) {
    return (
      <div className="container mx-auto p-6">
        <Card>
          <CardContent className="p-6">
            <p className="text-center">Kérdőív betöltése...</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!questionnaire || !questionnaire.questions || !questionnaire.questions.structure) {
    return (
      <div className="container mx-auto p-6">
        <Card>
          <CardContent className="p-6">
            <p className="text-center">Nem található aktív kérdőív vagy érvénytelen struktúra.</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  const renderWelcome = () => (
    <div className="space-y-6">
      <Alert className="border-info/50 bg-info/10 mb-6">
        <Info className="h-4 w-4" />
        <AlertDescription>
          <strong>Előnézet mód:</strong> Ez az oldal csak olvasható előnézetet nyújt. A kérdőív nem módosítható, 
          így biztosítva az adatok összehasonlíthatóságát a korábbi auditokkal. 
          <br /><br />
          <strong>Navigáció:</strong> A kék gombok segítenek a különböző kérdőív szakaszok között navigálni. 
          Ezek jelzik a kattintható területeket a demo során.
        </AlertDescription>
      </Alert>
      <div className="space-y-4 text-center">
        <h2 className="text-2xl font-bold">Üdvözlünk!</h2>
        <p className="text-muted-foreground">
          Ez a felmérés anonim, a kitöltés kb. 6–9 perc. A válaszok kizárólag összesítve, 
          statisztikai formában jelennek meg.
        </p>
      </div>
      <Button onClick={() => {
        setCurrentStep('demographics');
        setTimeout(scrollToTop, 100);
      }} className="w-full bg-demo-primary hover:bg-demo-primary/90 text-white">
        Kezdés (Demo)
      </Button>
    </div>
  );

  const renderDemographics = () => {
    const demoQuestions = questionnaire.questions.demographics.questions;
    
    return (
      <div className="space-y-6">
        <div>
          <h3 className="text-lg font-semibold mb-4">
            {questionnaire.questions.demographics.title}
          </h3>
        </div>
        <div className="opacity-75 pointer-events-none">
          {demoQuestions.map((q: any) => (
            <div key={q.id} className="mb-6">
              <QuestionRenderer
                question={q}
                value={demoResponses[q.id]}
                onChange={(value) => handleResponseChange(q.id, value)}
              />
            </div>
          ))}
        </div>
        <div className="flex gap-4">
          <Button 
            variant="outline"
            onClick={() => setCurrentStep('welcome')} 
            className="flex-1"
          >
            Vissza
          </Button>
          <Button onClick={() => {
            setCurrentStep('branch_selector');
            setTimeout(scrollToTop, 100);
          }} className="flex-1 bg-demo-primary hover:bg-demo-primary/90 text-white">
            Tovább (Demo)
          </Button>
        </div>
      </div>
    );
  };

  const renderBranchSelector = () => {
    const branchSelector = questionnaire.questions.branch_selector;
    const programName = 'doyoueap';
    
    const modifiedQuestion = {
      ...branchSelector,
      question: `Tudtad, hogy a munkahelyeden elérhető egy támogatási program, amit ${programName} néven ismerhetsz? Ez a szolgáltatás segítséget nyújt neked és családodnak különböző munkahelyi vagy magánéleti kihívások kezeléséhez, például stresszhelyzetekben, konfliktusok megoldásában vagy akár pénzügyi tanácsadásban is.`
    };
    
    return (
      <div className="space-y-6">
        <div className="opacity-75 pointer-events-none">
          <QuestionRenderer
            question={modifiedQuestion}
            value={demoResponses[branchSelector.id]}
            onChange={(value) => handleResponseChange(branchSelector.id, value)}
          />
        </div>
        
        <Alert className="border-info/50 bg-info/10">
          <Info className="h-4 w-4" />
          <AlertDescription>
            <strong>Demo mód:</strong> Válaszd ki, melyik válasz ágát szeretnéd megnézni. A munkavállalók válasza alapján különböző kérdéseket kapnak.
          </AlertDescription>
        </Alert>

        <div className="grid grid-cols-1 gap-3">
          <Button 
            onClick={() => {
              setCurrentStep('eap_info');
              setTimeout(scrollToTop, 100);
            }}
            className="w-full justify-start h-auto py-4 bg-demo-primary hover:bg-demo-primary/90 text-white"
          >
            <div className="text-left">
              <div className="font-semibold">1. ág: "Nem tudom / Nem, tudok róla és nem használtam"</div>
              <div className="text-sm opacity-90">Program információs oldal megjelenítése</div>
            </div>
          </Button>

          <Button 
            onClick={() => {
              setSelectedBranch('not_used');
              setCurrentStep('branch_questions');
              setCurrentBlockIndex(0);
              setTimeout(scrollToTop, 100);
            }}
            className="w-full justify-start h-auto py-4 bg-demo-primary hover:bg-demo-primary/90 text-white"
          >
            <div className="text-left">
              <div className="font-semibold">2. ág: "Igen, tudok róla de nem használtam"</div>
              <div className="text-sm opacity-90">Tudatosság és motiváció kérdések</div>
            </div>
          </Button>

          <Button 
            onClick={() => {
              setSelectedBranch('used');
              setCurrentStep('branch_questions');
              setCurrentBlockIndex(0);
              setTimeout(scrollToTop, 100);
            }}
            className="w-full justify-start h-auto py-4 bg-demo-primary hover:bg-demo-primary/90 text-white"
          >
            <div className="text-left">
              <div className="font-semibold">3. ág: "Igen, tudok róla és használtam is"</div>
              <div className="text-sm opacity-90">Használati tapasztalatok és elégedettség</div>
            </div>
          </Button>
        </div>

        <Button 
          variant="outline"
          onClick={() => setCurrentStep('demographics')} 
          className="w-full"
        >
          Vissza
        </Button>
      </div>
    );
  };

  const renderBranchQuestions = () => {
    if (!selectedBranch) return null;
    
    const branch = questionnaire.questions.branches[selectedBranch];
    if (!branch || !branch.blocks) {
      return (
        <div className="space-y-6">
          <Alert variant="destructive">
            <AlertDescription>
              A kiválasztott ág ({selectedBranch}) nem található a kérdőívben.
            </AlertDescription>
          </Alert>
          <Button onClick={() => setCurrentStep('branch_selector')} className="w-full">
            Vissza
          </Button>
        </div>
      );
    }
    
    const currentBlock = branch.blocks[currentBlockIndex];
    const isLastBlock = currentBlockIndex === branch.blocks.length - 1;
    
    return (
      <div className="space-y-6">
        <div className="opacity-75 pointer-events-none">
          {currentBlock.questions.map((q: any) => (
            <div key={q.id} className="mb-6">
              <QuestionRenderer
                question={q}
                value={demoResponses[q.id]}
                onChange={(value) => handleResponseChange(q.id, value)}
              />
            </div>
          ))}
        </div>
        
        <div className="flex gap-4">
          <Button
            type="button"
            variant="outline"
            onClick={() => {
              if (currentBlockIndex > 0) {
                setCurrentBlockIndex(currentBlockIndex - 1);
              } else {
                setCurrentStep('branch_selector');
              }
            }}
            className="flex-1"
          >
            Vissza
          </Button>
          <Button
            onClick={() => {
              if (!isLastBlock) {
                setCurrentBlockIndex(currentBlockIndex + 1);
              } else {
                setCurrentStep('welcome');
                setCurrentBlockIndex(0);
                setSelectedBranch(null);
              }
              setTimeout(scrollToTop, 100);
            }}
            className="flex-1 bg-demo-primary hover:bg-demo-primary/90 text-white"
          >
            {isLastBlock ? 'Újrakezd (Demo)' : 'Tovább (Demo)'}
          </Button>
        </div>
      </div>
    );
  };

  const renderEapInfo = () => (
    <div className="space-y-6">
      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Mi a program?</h2>
        <p className="text-foreground">
          A munkavállalói segítő program különböző élethelyzetekben nyújt támogatást.
        </p>
        <p className="text-foreground">
          A program keretében hozzáférhetsz pszichológiai tanácsadáshoz, jogi segítséghez, 
          és számos más szolgáltatáshoz, amelyek segíthetnek a munkahelyi és magánéleti 
          kihívások kezelésében.
        </p>
        <div className="p-4 bg-secondary rounded-lg">
          <p className="font-semibold mb-2">Kattints az alábbi linkre és látogasd meg a program hivatalos weboldalát!</p>
          <a 
            href="https://doyoueap.hu" 
            target="_blank" 
            rel="noopener noreferrer"
            className="text-primary hover:underline"
          >
            doyoueap.hu
          </a>
        </div>
      </div>
      <div className="flex gap-4">
        <Button 
          variant="outline"
          onClick={() => setCurrentStep('branch_selector')} 
          className="flex-1"
        >
          Vissza
        </Button>
        <Button 
          onClick={() => {
            setCurrentStep('welcome');
            setCurrentBlockIndex(0);
            setSelectedBranch(null);
            setTimeout(scrollToTop, 100);
          }} 
          className="flex-1 bg-demo-primary hover:bg-demo-primary/90 text-white"
        >
          Újrakezd (Demo)
        </Button>
      </div>
    </div>
  );

  return (
    <div className="space-y-6">
      <div>
        <div className="mb-6 flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold mb-2">EAP Pulse felmérés előnézet</h2>
            <p className="text-muted-foreground text-sm">
              Így látják a munkavállalók a kérdőívet a felmérés során
            </p>
          </div>
          <Button onClick={fetchQuestionnaire} variant="outline">
            Újratöltés
          </Button>
        </div>

        <Card>
          <CardHeader>
            <div className="flex justify-center mb-4">
              <img 
                src={logo} 
                alt="Logo" 
                className="h-12 object-contain"
              />
            </div>
            {currentStep !== 'welcome' && currentStep !== 'branch_selector' && currentStep !== 'eap_info' && (() => {
              const progress = getOverallProgress();
              return (
                <div className="space-y-2">
                  <div className="flex items-center justify-center text-sm text-muted-foreground">
                    <span>Lépés {progress.current} / {progress.total}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="text-sm font-medium">2 /</span>
                    <Progress value={progress.percent} className="flex-1" />
                  </div>
                </div>
              );
            })()}
          </CardHeader>
          <CardContent>
            {currentStep === 'welcome' && renderWelcome()}
            {currentStep === 'demographics' && renderDemographics()}
            {currentStep === 'branch_selector' && renderBranchSelector()}
            {currentStep === 'branch_questions' && renderBranchQuestions()}
            {currentStep === 'eap_info' && renderEapInfo()}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
