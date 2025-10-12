import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { usePackage } from '@/hooks/usePackage';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Button } from '@/components/ui/button';
import { AlertCircle } from 'lucide-react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { toast } from 'sonner';
import { Step1AccessMode } from '@/components/audit/Step1AccessMode';
import { Step3Lottery } from '@/components/audit/Step3Lottery';
import { Step3Distribution } from '@/components/audit/Step3Distribution';
import { Step3Branding } from '@/components/audit/Step3Branding';
import { Step4Timing } from '@/components/audit/Step4Timing';
import { Step5Languages } from '@/components/audit/Step5Languages';
import { Step6ProgramName } from '@/components/audit/Step6ProgramName';
import { Step7Summary } from '@/components/audit/Step7Summary';
import eapPulseLogo from '@/assets/eap-pulse-logo.png';

const CreateAudit = () => {
  const { user } = useAuth();
  const { packageType } = usePackage();
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(1);
  const [loading, setLoading] = useState(false);
  const [auditsThisYear, setAuditsThisYear] = useState(0);
  const [isLimitReached, setIsLimitReached] = useState(false);
  const [checkingLimit, setCheckingLimit] = useState(true);

  // EAP Pulse data state
  const [programName, setProgramName] = useState('');
  const [accessMode, setAccessMode] = useState('public_link');
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [customColors, setCustomColors] = useState({
    primary: '#3b82f6',
    secondary: '#8b5cf6',
    accent: '#10b981',
    background: '#f3f4f6',
  });
  const [startDate, setStartDate] = useState(() => {
    const now = new Date();
    return new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
  });
  const [expiresAt, setExpiresAt] = useState('');
  const [enableRecurrence, setEnableRecurrence] = useState(false);
  const [recurrenceFrequency, setRecurrenceFrequency] = useState('biannually');
  const [selectedLanguages, setSelectedLanguages] = useState(['HU']);
  const [emailListFile, setEmailListFile] = useState<File | null>(null);
  const [accessToken, setAccessToken] = useState('');
  const [emailSubject, setEmailSubject] = useState('Segítsd jobbá tenni a {{program_név}} programot!');
  const [emailFrom, setEmailFrom] = useState('noreply@doyoueap.com');
  const [emailBody, setEmailBody] = useState('');
  const [eapProgramUrl, setEapProgramUrl] = useState('');
  
  // Lottery state
  const [giftId, setGiftId] = useState('');
  const [drawMode, setDrawMode] = useState<'auto' | 'manual'>('auto');
  const [lotteryConsent, setLotteryConsent] = useState(false);

  // Adjust total steps based on package - skip branding step for starter
  const totalSteps = packageType === 'starter' ? 7 : 8;

  // Check audit limit based on package
  useEffect(() => {
    const checkAuditLimit = async () => {
      if (!user?.id || !packageType) {
        setCheckingLimit(false);
        return;
      }

      try {
        // Calculate date one year ago
        const oneYearAgo = new Date();
        oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);

        // Count audits created in the last year
        const { count, error } = await supabase
          .from('audits')
          .select('*', { count: 'exact', head: true })
          .eq('hr_user_id', user.id)
          .gte('created_at', oneYearAgo.toISOString());

        if (error) throw error;

        const auditCount = count || 0;
        setAuditsThisYear(auditCount);

        // Set limits based on package
        const limits: Record<string, number> = {
          starter: 2,
          professional: 3,
          enterprise: 4,
          partner: Infinity // No limit for Partner Program
        };

        const limit = limits[packageType] || 0;
        setIsLimitReached(auditCount >= limit && limit !== Infinity);
      } catch (error) {
        console.error('Error checking audit limit:', error);
      } finally {
        setCheckingLimit(false);
      }
    };

    checkAuditLimit();
  }, [user?.id, packageType]);

  const handleNext = async () => {
    // Generate token after step 3 (access mode step, before distribution)
    if (currentStep === 3 && !accessToken) {
      try {
        const { data: tokenData, error: tokenError } = await supabase.rpc(
          'generate_access_token'
        );
        if (tokenError) throw tokenError;
        setAccessToken(tokenData);
      } catch (error) {
        console.error('Error generating token:', error);
        toast.error('Hiba történt a token generálásakor');
        return;
      }
    }
    
    if (currentStep < totalSteps) {
      setCurrentStep(currentStep + 1);
    }
  };

  const handleBack = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1);
    }
  };

  const handleSubmit = async () => {
    if (!expiresAt) {
      toast.error('A záró dátum megadása kötelező');
      return;
    }

    setLoading(true);

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

      // Generate token early so it can be shown in step 3
      if (!accessToken) {
        const { data: tokenData, error: tokenError } = await supabase.rpc(
          'generate_access_token'
        );

        if (tokenError) throw tokenError;
        setAccessToken(tokenData);
      }

      // Get the first active questionnaire
      const { data: questionnaires } = await supabase
        .from('questionnaires')
        .select('id')
        .eq('is_active', true)
        .limit(1);

      if (!questionnaires || questionnaires.length === 0) {
        throw new Error('Nincs aktív kérdőív');
      }

      // Get company_name for the current user
      const { data: profileData } = await supabase
        .from('profiles')
        .select('company_name')
        .eq('id', user?.id)
        .single();

      if (!profileData?.company_name) {
        throw new Error('A cég neve nincs kitöltve a profilban. Kérjük, adja meg, majd próbálja újra.');
      }

      // Create EAP Pulse assessment
      const { error } = await supabase.from('audits').insert({
        hr_user_id: user?.id,
        company_name: profileData.company_name,
        program_name: programName,
        questionnaire_id: questionnaires[0].id,
        access_token: accessToken,
        access_mode: accessMode,
        communication_text: emailBody,
        logo_url: logoUrl,
        custom_colors: customColors,
        start_date: startDate,
        expires_at: expiresAt,
        closes_at: expiresAt,
        status: 'running',
        recurrence_config: enableRecurrence
          ? { enabled: true, frequency: recurrenceFrequency }
          : { enabled: false },
        available_languages: selectedLanguages,
        eap_program_url: eapProgramUrl,
        gift_id: giftId || null,
        draw_mode: giftId ? drawMode : null,
        draw_status: giftId ? 'none' : 'none',
      });

      if (error) throw error;

      toast.success('EAP Pulse felmérés sikeresen létrehozva és elindítva!');
      navigate('/?section=eap-pulse&sub=running-audits');
    } catch (error) {
      console.error('Error creating assessment:', error);
      toast.error('Hiba történt a felmérés létrehozásakor');
    } finally {
      setLoading(false);
    }
  };

  const auditData = {
    programName,
    accessMode,
    emailBody,
    logoFile,
    customColors,
    startDate,
    expiresAt,
    enableRecurrence,
    recurrenceFrequency,
    selectedLanguages,
    eapProgramUrl,
    giftId,
    drawMode,
  };

  
  if (checkingLimit) {
    return (
      <div className="flex justify-center items-center min-h-[400px]">
        <p className="text-muted-foreground">Betöltés...</p>
      </div>
    );
  }

  if (isLimitReached) {
    return (
      <div className="space-y-6">
        <Alert variant="destructive">
          <AlertCircle className="h-4 w-4" />
          <AlertTitle>Elérted a felmérési limitedet</AlertTitle>
          <AlertDescription>
            {packageType === 'starter' && `Starter csomag esetén évente maximum 2 új felmérést indíthatsz. Jelenleg ${auditsThisYear} felmérést indítottál az elmúlt évben.`}
            {packageType === 'professional' && `Professional csomag esetén évente maximum 3 új felmérést indíthatsz. Jelenleg ${auditsThisYear} felmérést indítottál az elmúlt évben.`}
          </AlertDescription>
        </Alert>
        <Card>
          <CardContent className="py-8 text-center space-y-4">
            <p className="text-muted-foreground">
              A limit újrakezdődik az első felmérés indításának évfordulóján.
            </p>
            <p className="font-medium">
              Frissíts magasabb csomagra, hogy több felmérést indíthass!
            </p>
            <Button onClick={() => navigate('/?section=settings')}>
              Csomag frissítése
            </Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-8 pb-8">
      <div className="max-w-4xl mx-auto bg-gradient-to-r from-primary/10 to-primary/5 rounded-xl p-6 border border-primary/20">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h2 className="text-3xl font-bold">Új EAP Pulse Felmérés</h2>
            <p className="text-muted-foreground mt-2 text-base">
              Lépés {currentStep} / {totalSteps}
            </p>
          </div>
          <img src={eapPulseLogo} alt="EAP Pulse" className="h-10" />
        </div>
        <Progress 
          value={(currentStep / totalSteps) * 100} 
          className="h-3 bg-background/50"
        />
      </div>

      <div className="max-w-4xl mx-auto space-y-6">
        {currentStep === 1 && (
          <Step6ProgramName
            programName={programName}
            eapProgramUrl={eapProgramUrl}
            onProgramNameChange={setProgramName}
            onEapProgramUrlChange={setEapProgramUrl}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 2 && (
          <Step3Lottery
            giftId={giftId}
            drawMode={drawMode}
            lotteryConsent={lotteryConsent}
            onGiftIdChange={setGiftId}
            onDrawModeChange={setDrawMode}
            onLotteryConsentChange={setLotteryConsent}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 3 && (
          <Step1AccessMode
            accessMode={accessMode}
            onAccessModeChange={setAccessMode}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 4 && (
          <Step3Distribution
            accessMode={accessMode}
            accessToken={accessToken}
            communicationText={emailBody}
            emailSubject={emailSubject}
            emailFrom={emailFrom}
            emailBody={emailBody}
            onEmailSubjectChange={setEmailSubject}
            onEmailFromChange={setEmailFrom}
            onEmailBodyChange={setEmailBody}
            onEmailListUpload={setEmailListFile}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 5 && packageType !== 'starter' && (
          <Step3Branding
            logoFile={logoFile}
            customColors={customColors}
            onLogoChange={setLogoFile}
            onColorsChange={setCustomColors}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {((packageType === 'starter' && currentStep === 5) || (packageType !== 'starter' && currentStep === 6)) && (
          <Step4Timing
            startDate={startDate}
            expiresAt={expiresAt}
            enableRecurrence={enableRecurrence}
            recurrenceFrequency={recurrenceFrequency}
            packageType={packageType}
            onStartDateChange={setStartDate}
            onExpiresAtChange={setExpiresAt}
            onEnableRecurrenceChange={setEnableRecurrence}
            onRecurrenceFrequencyChange={setRecurrenceFrequency}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {((packageType === 'starter' && currentStep === 6) || (packageType !== 'starter' && currentStep === 7)) && (
          <Step5Languages
            selectedLanguages={selectedLanguages}
            onLanguagesChange={setSelectedLanguages}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {((packageType === 'starter' && currentStep === 7) || (packageType !== 'starter' && currentStep === 8)) && (
          <Step7Summary
            auditData={auditData}
            onSubmit={handleSubmit}
            onBack={handleBack}
            loading={loading}
          />
        )}
      </div>
    </div>
  );
};

export default CreateAudit;
