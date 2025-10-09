import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { toast } from 'sonner';
import { Step1AccessMode } from '@/components/audit/Step1AccessMode';
import { Step2Communication } from '@/components/audit/Step2Communication';
import { Step3Distribution } from '@/components/audit/Step3Distribution';
import { Step3Branding } from '@/components/audit/Step3Branding';
import { Step4Timing } from '@/components/audit/Step4Timing';
import { Step5Languages } from '@/components/audit/Step5Languages';
import { Step6ProgramName } from '@/components/audit/Step6ProgramName';
import { Step7Summary } from '@/components/audit/Step7Summary';
import eapPulseLogo from '@/assets/eap-pulse-logo.png';

const CreateAudit = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(1);
  const [loading, setLoading] = useState(false);

  // EAP Pulse data state
  const [programName, setProgramName] = useState('');
  const [accessMode, setAccessMode] = useState('public_link');
  const [communicationText, setCommunicationText] = useState('');
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
  const [recurrenceFrequency, setRecurrenceFrequency] = useState('quarterly');
  const [selectedLanguages, setSelectedLanguages] = useState(['HU']);
  const [emailListFile, setEmailListFile] = useState<File | null>(null);
  const [accessToken, setAccessToken] = useState('');
  const [emailSubject, setEmailSubject] = useState('Segítsd jobbá tenni a {{program_név}} programot!');
  const [emailFrom, setEmailFrom] = useState('noreply@doyoueap.com');
  const [eapProgramUrl, setEapProgramUrl] = useState('');

  const totalSteps = 8;

  const handleNext = async () => {
    // Generate token after step 3 (communication step, before distribution)
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

      // Create EAP Pulse assessment
      const { error } = await supabase.from('audits').insert({
        hr_user_id: user?.id,
        program_name: programName,
        questionnaire_id: questionnaires[0].id,
        access_token: accessToken,
        access_mode: accessMode,
        communication_text: communicationText,
        logo_url: logoUrl,
        custom_colors: customColors,
        start_date: startDate,
        expires_at: expiresAt,
        recurrence_config: enableRecurrence
          ? { enabled: true, frequency: recurrenceFrequency }
          : { enabled: false },
        available_languages: selectedLanguages,
        eap_program_url: eapProgramUrl,
      });

      if (error) throw error;

      toast.success('EAP Pulse felmérés sikeresen létrehozva és elindítva!');
      navigate('/hr/eap-audit?sub=running-audits');
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
    communicationText,
    logoFile,
    customColors,
    startDate,
    expiresAt,
    enableRecurrence,
    recurrenceFrequency,
    selectedLanguages,
    eapProgramUrl,
  };

  return (
    <div className="space-y-6">
      <div className="mb-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-2xl font-bold">Új EAP Pulse Felmérés Indítása</h2>
          <img src={eapPulseLogo} alt="EAP Pulse" className="h-6" />
        </div>
        <p className="text-muted-foreground text-sm">
          Lépés {currentStep} / {totalSteps}
        </p>
        <Progress 
          value={(currentStep / totalSteps) * 100} 
          className="mt-4"
          style={{
            '--progress-background': 'linear-gradient(90deg, #3572ef 0%, #3572ef 100%)'
          } as React.CSSProperties}
        />
      </div>

      <div className="space-y-6">
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
          <Step1AccessMode
            accessMode={accessMode}
            onAccessModeChange={setAccessMode}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 3 && (
          <Step2Communication
            communicationText={communicationText}
            onCommunicationTextChange={setCommunicationText}
            accessMode={accessMode}
            programName={programName}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 4 && (
          <Step3Distribution
            accessMode={accessMode}
            accessToken={accessToken}
            communicationText={communicationText}
            emailSubject={emailSubject}
            emailFrom={emailFrom}
            onEmailSubjectChange={setEmailSubject}
            onEmailFromChange={setEmailFrom}
            onEmailListUpload={setEmailListFile}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 5 && (
          <Step3Branding
            logoFile={logoFile}
            customColors={customColors}
            onLogoChange={setLogoFile}
            onColorsChange={setCustomColors}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 6 && (
          <Step4Timing
            startDate={startDate}
            expiresAt={expiresAt}
            enableRecurrence={enableRecurrence}
            recurrenceFrequency={recurrenceFrequency}
            onStartDateChange={setStartDate}
            onExpiresAtChange={setExpiresAt}
            onEnableRecurrenceChange={setEnableRecurrence}
            onRecurrenceFrequencyChange={setRecurrenceFrequency}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 7 && (
          <Step5Languages
            selectedLanguages={selectedLanguages}
            onLanguagesChange={setSelectedLanguages}
            onNext={handleNext}
            onBack={handleBack}
          />
        )}

        {currentStep === 8 && (
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
