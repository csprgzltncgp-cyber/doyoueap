import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { toast } from 'sonner';
import { Step1AccessMode } from '@/components/audit/Step1AccessMode';
import { Step2Communication } from '@/components/audit/Step2Communication';
import { Step3Branding } from '@/components/audit/Step3Branding';
import { Step4Timing } from '@/components/audit/Step4Timing';
import { Step5Languages } from '@/components/audit/Step5Languages';
import { Step6ProgramName } from '@/components/audit/Step6ProgramName';
import { Step7Summary } from '@/components/audit/Step7Summary';

const CreateAudit = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(1);
  const [loading, setLoading] = useState(false);

  // Audit data state
  const [companyName, setCompanyName] = useState('');
  const [programName, setProgramName] = useState('DoYouEAP');
  const [accessMode, setAccessMode] = useState('public_link');
  const [communicationText, setCommunicationText] = useState('');
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [customColors, setCustomColors] = useState({
    primary: '#3b82f6',
    secondary: '#8b5cf6',
    accent: '#10b981',
    background: '#f3f4f6',
  });
  const [startDate, setStartDate] = useState('');
  const [expiresAt, setExpiresAt] = useState('');
  const [enableRecurrence, setEnableRecurrence] = useState(false);
  const [recurrenceFrequency, setRecurrenceFrequency] = useState('quarterly');
  const [selectedLanguages, setSelectedLanguages] = useState(['HU']);

  const totalSteps = 7;

  const handleNext = () => {
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
    if (!companyName || !startDate || !expiresAt) {
      toast.error('Kérlek töltsd ki az összes kötelező mezőt');
      return;
    }

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

      // Generate token
      const { data: tokenData, error: tokenError } = await supabase.rpc(
        'generate_access_token'
      );

      if (tokenError) throw tokenError;

      // Get the first active questionnaire
      const { data: questionnaires } = await supabase
        .from('questionnaires')
        .select('id')
        .eq('is_active', true)
        .limit(1);

      if (!questionnaires || questionnaires.length === 0) {
        throw new Error('Nincs aktív kérdőív');
      }

      // Create audit
      const { error } = await supabase.from('audits').insert({
        hr_user_id: user?.id,
        company_name: companyName,
        program_name: programName,
        questionnaire_id: questionnaires[0].id,
        access_token: tokenData,
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
        eap_program_url: 'https://doyoueap.hu',
      });

      if (error) throw error;

      toast.success('Audit sikeresen létrehozva és elindítva!');
      navigate('/hr');
    } catch (error) {
      console.error('Error creating audit:', error);
      toast.error('Hiba történt az audit létrehozásakor');
    } finally {
      setLoading(false);
    }
  };

  const auditData = {
    companyName,
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
  };

  return (
    <div className="p-8 max-w-4xl mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl font-bold mb-2">Új Audit Indítása</h1>
        <p className="text-muted-foreground">
          Lépés {currentStep} / {totalSteps}
        </p>
        <Progress value={(currentStep / totalSteps) * 100} className="mt-4" />
      </div>

      <Card>
        <CardContent className="pt-6">
          {currentStep === 1 && (
            <Step1AccessMode
              accessMode={accessMode}
              onAccessModeChange={setAccessMode}
              onNext={handleNext}
            />
          )}

          {currentStep === 2 && (
            <Step2Communication
              communicationText={communicationText}
              onCommunicationTextChange={setCommunicationText}
              accessMode={accessMode}
              onNext={handleNext}
              onBack={handleBack}
            />
          )}

          {currentStep === 3 && (
            <Step3Branding
              logoFile={logoFile}
              customColors={customColors}
              onLogoChange={setLogoFile}
              onColorsChange={setCustomColors}
              onNext={handleNext}
              onBack={handleBack}
            />
          )}

          {currentStep === 4 && (
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

          {currentStep === 5 && (
            <Step5Languages
              selectedLanguages={selectedLanguages}
              onLanguagesChange={setSelectedLanguages}
              onNext={handleNext}
              onBack={handleBack}
            />
          )}

          {currentStep === 6 && (
            <Step6ProgramName
              programName={programName}
              companyName={companyName}
              onProgramNameChange={setProgramName}
              onCompanyNameChange={setCompanyName}
              onNext={handleNext}
              onBack={handleBack}
            />
          )}

          {currentStep === 7 && (
            <Step7Summary
              auditData={auditData}
              onSubmit={handleSubmit}
              onBack={handleBack}
              loading={loading}
            />
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default CreateAudit;