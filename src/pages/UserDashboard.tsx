import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { toast } from 'sonner';
import { QuestionRenderer } from '@/components/survey/QuestionRenderer';
import { Progress } from '@/components/ui/progress';
import { Globe, Download } from 'lucide-react';
import jsPDF from 'jspdf';
import logo from '@/assets/eap-pulse-logo-blue.png';

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

// NOTE: "Audit" in code represents "Felmérés" (EAP Pulse Survey) in the UI
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
  gift?: {
    name: string;
    value_eur: number;
  };
  draw_mode: 'auto' | 'manual' | null;
  closes_at: string | null;
}

const UserDashboard = () => {
  const { token } = useParams<{ token: string }>();
  const [audit, setAudit] = useState<Audit | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [responses, setResponses] = useState<Record<string, any>>({});
  const [submitting, setSubmitting] = useState(false);
  const [currentStep, setCurrentStep] = useState<'language_select' | 'welcome' | 'demographics' | 'branch_selector' | 'branch_questions' | 'eap_info' | 'email_consent' | 'thank_you'>('language_select');
  const [selectedLanguage, setSelectedLanguage] = useState<string>('');
  const [selectedBranch, setSelectedBranch] = useState<string | null>(null);
  const [currentBlockIndex, setCurrentBlockIndex] = useState(0);
  const [drawToken, setDrawToken] = useState<string | null>(null);
  const [hasLottery, setHasLottery] = useState(false);
  const [email, setEmail] = useState('');
  const [emailConsent, setEmailConsent] = useState(false);

  // Scroll to top function
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  useEffect(() => {
    if (token) {
      fetchAudit();
    }
  }, [token]);

  // Auto-skip language selection if only one language is available
  useEffect(() => {
    if (audit && currentStep === 'language_select') {
      if (audit.available_languages && audit.available_languages.length === 1) {
        setSelectedLanguage(audit.available_languages[0]);
        setCurrentStep('welcome');
        setTimeout(scrollToTop, 100);
      }
    }
  }, [audit, currentStep]);

  const fetchAudit = async () => {
    try {
      // Use the secure function to get audit data
      const { data: auditData, error: auditError } = await supabase
        .rpc('get_audit_for_survey', { _access_token: token });

      if (auditError) throw auditError;

      if (!auditData || auditData.length === 0) {
        setError('Érvénytelen felmérés link');
        return;
      }

      const audit = auditData[0];

      // Fetch the questionnaire separately
      const { data: questionnaireData, error: questionnaireError } = await supabase
        .from('questionnaires')
        .select('title, description, questions')
        .eq('id', audit.questionnaire_id)
        .single();

      if (questionnaireError) throw questionnaireError;

      // Check if lottery is enabled and fetch gift details
      const { data: auditWithGift, error: giftError } = await supabase
        .from('audits')
        .select('gift_id, draw_mode, closes_at, gifts(name, value_eur)')
        .eq('id', audit.id)
        .maybeSingle();

      if (giftError) {
        console.error('Error fetching gift:', giftError);
      }
      
      setHasLottery(!!auditWithGift?.gift_id);

      // Combine the data
      const combinedData = {
        ...audit,
        questionnaire: questionnaireData,
        gift: auditWithGift?.gift_id ? (auditWithGift as any).gifts : undefined,
        draw_mode: auditWithGift?.draw_mode || null,
        closes_at: auditWithGift?.closes_at || null
      };

      setAudit(combinedData as any);
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
    setTimeout(scrollToTop, 100);
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
      setTimeout(scrollToTop, 100);
      return;
    }

    setSelectedBranch(branchKey);
    setCurrentStep('branch_questions');
    setCurrentBlockIndex(0);
    setTimeout(scrollToTop, 100);
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
      setTimeout(scrollToTop, 100);
    } else {
      // Last block - check if lottery, then email consent, otherwise submit
      if (hasLottery) {
        setCurrentStep('email_consent');
        setTimeout(scrollToTop, 100);
      } else {
        handleSubmit(new Event('submit') as any);
      }
    }
  };

  const handleBlockPrevious = () => {
    if (currentBlockIndex > 0) {
      setCurrentBlockIndex(currentBlockIndex - 1);
      setTimeout(scrollToTop, 100);
    } else {
      setCurrentStep('branch_selector');
      setTimeout(scrollToTop, 100);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!audit) return;
    
    setSubmitting(true);
    
    try {
      // Generate a unique participant ID if lottery is enabled
      const participantId = hasLottery ? crypto.randomUUID() : undefined;
      
      const { data, error } = await supabase.functions.invoke('submit-response', {
        body: {
          audit_id: audit.id,
          responses,
          participant_id: participantId,
          email: hasLottery && email ? email : undefined,
          email_consent: hasLottery && emailConsent,
          employee_metadata: {
            submitted_at: new Date().toISOString(),
            branch: selectedBranch || 'redirect',
          },
        },
      });

      if (error) throw error;
      
      if (data?.draw_token) {
        setDrawToken(data.draw_token);
        setHasLottery(data.has_lottery);
      }

      setCurrentStep('thank_you');
      setTimeout(scrollToTop, 100);
    } catch (err: any) {
      console.error('Error submitting response:', err);
      toast.error(err.message || 'Hiba történt a válaszok mentésekor');
    } finally {
      setSubmitting(false);
    }
  };

  const getTotalProgress = () => {
    if (!audit || !selectedBranch) return { percent: 0, current: 0, total: 0 };
    
    const branch = audit.questionnaire.questions.branches[selectedBranch];
    if (!branch) return { percent: 0, current: 0, total: 0 };
    
    const totalSteps = branch.blocks.length;
    const currentStepNum = currentBlockIndex + 1;
    
    return {
      percent: totalSteps > 0 ? (currentStepNum / totalSteps) * 100 : 0,
      current: currentStepNum,
      total: totalSteps
    };
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
              <AlertDescription>{error || 'Felmérés nem található'}</AlertDescription>
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
        <div className="flex gap-4">
          <Button 
            onClick={() => {
              setCurrentStep('welcome');
              setTimeout(scrollToTop, 100);
            }} 
            variant="outline"
            className="flex-1"
          >
            Vissza
          </Button>
          <Button 
            onClick={handleDemographicsNext} 
            className="flex-1"
            style={{
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            }}
          >
            Tovább
          </Button>
        </div>
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
        <div className="flex gap-4">
          <Button 
            onClick={() => {
              setCurrentStep('demographics');
              setTimeout(scrollToTop, 100);
            }} 
            variant="outline"
            className="flex-1"
          >
            Vissza
          </Button>
          <Button 
            onClick={handleBranchSelection} 
            className="flex-1"
            style={{
              backgroundColor: primaryColor,
              borderColor: primaryColor,
            }}
          >
            Tovább
          </Button>
        </div>
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
              setTimeout(scrollToTop, 100);
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

  const formatEUR = (value: number): string => {
    const formatted = new Intl.NumberFormat('hu-HU', {
      style: 'currency',
      currency: 'EUR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value);
    return formatted.replace('EUR', '€').trim();
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
      
      {hasLottery && audit.gift && (
        <Alert className="border-primary/20 bg-primary/5">
          <AlertDescription className="space-y-3">
            <p className="font-semibold text-lg">Nyereményjáték!</p>
            <p>
              A felmérés kitöltésével automatikusan részt veszel egy <strong>{audit.gift.name}</strong> értékű 
              ajándék sorsolásán (értéke: <strong>{formatEUR(audit.gift.value_eur)}</strong>).
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
                  {audit.draw_mode === 'auto' ? (
                    audit.closes_at ? (
                      <> A sorsolás automatikusan megtörténik: <strong>{new Date(audit.closes_at).toLocaleDateString('hu-HU')}</strong></>
                    ) : (
                      <> A sorsolás automatikusan megtörténik.</>
                    )
                  ) : (
                    audit.closes_at ? (
                      <> A sorsolás a felmérés lezárása után történik: <strong>{new Date(audit.closes_at).toLocaleDateString('hu-HU')}</strong></>
                    ) : (
                      <> A sorsolás a felmérés lezárása után történik.</>
                    )
                  )}
                </li>
              </ul>
            </div>
          </AlertDescription>
        </Alert>
      )}
      
      <div className="flex justify-center">
        <Button 
          onClick={() => {
            setCurrentStep('demographics');
            setTimeout(scrollToTop, 100);
          }} 
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

  const renderEapInfo = () => {
    // Ensure URL has protocol
    const formatUrl = (url: string | null) => {
      if (!url) return 'https://doyoueap.hu';
      if (url.startsWith('http://') || url.startsWith('https://')) {
        return url;
      }
      return `https://${url}`;
    };

    const getArticle = (word: string) => {
      const firstChar = word.charAt(0).toLowerCase();
      const vowels = ['a', 'á', 'e', 'é', 'i', 'í', 'o', 'ó', 'ö', 'ő', 'u', 'ú', 'ü', 'ű'];
      return vowels.includes(firstChar) ? 'az' : 'a';
    };

    const eapUrl = formatUrl(audit?.eap_program_url || null);
    const programName = audit?.program_name || 'EAP';
    const article = getArticle(programName);
    
    return (
      <div className="space-y-6">
        <div className="space-y-4">
          <h2 className="text-2xl font-bold">Mi {article} {programName}?</h2>
          <p className="text-foreground">
            {article.charAt(0).toUpperCase() + article.slice(1)} {programName} egy munkavállalói segítő program, amely 
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
              href={eapUrl} 
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
  };

  const renderEmailConsent = () => (
    <div className="space-y-6">
      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Nyereményjáték</h2>
        <p className="text-muted-foreground">
          Köszönjük, hogy kitöltötted a felmérést! Részt vehetsz a nyereményjátékban.
        </p>
        <p className="text-sm text-muted-foreground">
          Ha szeretnél értesítést kapni a sorsolás eredményéről, kérjük add meg az e-mail címed.
          Ez opcionális, a nyereménykódot mindenképpen megkapod a következő oldalon.
        </p>
      </div>

      <div className="space-y-4">
        <div>
          <Label htmlFor="email">E-mail cím (opcionális)</Label>
          <Input
            id="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="pelda@email.com"
          />
        </div>

        {email && (
          <div className="flex items-start space-x-2">
            <input
              type="checkbox"
              id="email_consent"
              checked={emailConsent}
              onChange={(e) => setEmailConsent(e.target.checked)}
              className="mt-1"
            />
            <Label htmlFor="email_consent" className="text-sm cursor-pointer">
              Hozzájárulok, hogy az email címemet a sorsolás eredményének közléséhez használják.
              Az adatokat a sorsolás lezárását követően töröljük.
            </Label>
          </div>
        )}
      </div>

      <div className="flex gap-4">
        <Button
          variant="outline"
          onClick={() => {
            setCurrentStep('branch_questions');
            setTimeout(scrollToTop, 100);
          }}
          className="flex-1"
        >
          Vissza
        </Button>
        <Button
          onClick={() => handleSubmit(new Event('submit') as any)}
          disabled={submitting || (email && !emailConsent)}
          className="flex-1"
          style={{
            backgroundColor: primaryColor,
            borderColor: primaryColor,
          }}
        >
          {submitting ? 'Küldés...' : email ? 'Rendben, küldd el!' : 'Kihagyom'}
        </Button>
      </div>
    </div>
  );

  const downloadTokenPDF = () => {
    if (!drawToken) return;

    const doc = new jsPDF();
    
    // Title
    doc.setFontSize(20);
    doc.text('Sorsolási kód', 105, 30, { align: 'center' });
    
    // Info text
    doc.setFontSize(12);
    doc.text('Köszönjük a részvételt az EAP Pulse felmérésben!', 105, 50, { align: 'center' });
    doc.text('A sorsolási kódod:', 105, 65, { align: 'center' });
    
    // Draw token
    doc.setFontSize(24);
    doc.setFont(undefined, 'bold');
    doc.text(drawToken, 105, 85, { align: 'center' });
    
    // Instructions
    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    doc.text('Őrizd meg ezt a kódot a sorsolás időpontjáig!', 105, 105, { align: 'center' });
    doc.text('A nyertest e-mailben vagy a felmérés szervezője révén értesítjük.', 105, 115, { align: 'center' });
    
    // Footer
    doc.setFontSize(8);
    doc.text('EAP Pulse - Munkavállalói Segítő Program Felmérés', 105, 280, { align: 'center' });
    doc.text(new Date().toLocaleDateString('hu-HU'), 105, 285, { align: 'center' });
    
    doc.save(`sorsolasi-kod-${drawToken}.pdf`);
    toast.success('PDF letöltve!');
  };

  const renderThankYou = () => (
    <div className="space-y-6 text-center">
      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Köszönjük a részvételt!</h2>
        <p className="text-muted-foreground">
          Válaszaid segítenek abban, hogy munkáltatód még jobb munkahelyi környezetet 
          alakíthasson ki.
        </p>
        {hasLottery && drawToken && (
          <div className="mt-6 p-6 bg-primary/10 rounded-lg border-2 border-primary">
            <h3 className="text-xl font-bold mb-3">🎉 Sorsolási kódod:</h3>
            <div className="text-3xl font-mono font-bold text-primary mb-3">
              {drawToken}
            </div>
            <p className="text-sm text-muted-foreground mb-4">
              Jegyezd fel vagy készíts róla képernyőképet! Ezt a kódot használjuk a nyertes kisorsolásához.
            </p>
            <Button 
              onClick={downloadTokenPDF}
              variant="outline"
              className="w-full gap-2"
            >
              <Download className="h-4 w-4" />
              Kód letöltése PDF-ben
            </Button>
          </div>
        )}
        <p className="text-muted-foreground">
          Ez az ablak most bezárható.
        </p>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-background p-4 sm:p-8">
      <div className="max-w-3xl mx-auto space-y-6">
        <div className="flex justify-center">
          <img 
            src={audit?.logo_url || logo} 
            alt="Logo" 
            className="h-12 object-contain"
          />
        </div>
        {currentStep === 'branch_questions' && (() => {
          const progress = getTotalProgress();
          return (
            <div className="space-y-2">
              <div className="flex items-center justify-center text-sm text-muted-foreground">
                <span>Lépés {progress.current} / {progress.total}</span>
              </div>
              <Progress 
                value={progress.percent} 
                className="mt-4"
                style={{
                  '--progress-background': primaryColor
                } as React.CSSProperties}
              />
            </div>
          );
        })()}
        {currentStep === 'language_select' && renderLanguageSelect()}
        {currentStep === 'welcome' && renderWelcome()}
        {currentStep === 'demographics' && renderDemographics()}
        {currentStep === 'branch_selector' && renderBranchSelector()}
        {currentStep === 'branch_questions' && renderBranchQuestions()}
        {currentStep === 'eap_info' && renderEapInfo()}
        {currentStep === 'email_consent' && renderEmailConsent()}
        {currentStep === 'thank_you' && renderThankYou()}
      </div>
    </div>
  );
};

export default UserDashboard;
