import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { EmailPasswordStep } from './EmailPasswordStep';
import { EmailValidationStep } from './EmailValidationStep';
import { CompanyDataStep } from './CompanyDataStep';
import { PackageSelectionStep } from './PackageSelectionStep';
import { PaymentStep } from './PaymentStep';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export interface RegistrationData {
  // Company data
  companyName: string;
  country: string;
  vatId: string;
  address: string;
  city: string;
  postalCode: string;
  industry: string;
  employeeCount: string;
  contactName: string;
  contactPhone: string;
  companyDomain: string;
  
  // Package selection
  selectedPackage: 'starter' | 'pro' | 'enterprise' | null;
  billingCycle: 'monthly' | 'yearly';
  
  // Consents
  termsAccepted: boolean;
  privacyAccepted: boolean;
  dpaAccepted: boolean;
}

const initialData: RegistrationData = {
  companyName: '',
  country: '',
  vatId: '',
  address: '',
  city: '',
  postalCode: '',
  industry: '',
  employeeCount: '',
  contactName: '',
  contactPhone: '',
  companyDomain: '',
  selectedPackage: null,
  billingCycle: 'yearly',
  termsAccepted: false,
  privacyAccepted: false,
  dpaAccepted: false,
};

export const RegistrationWizard = () => {
  const [step, setStep] = useState(0); // 0: email+password, 1: validation, 2-4: registration steps
  const [data, setData] = useState<RegistrationData>(initialData);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const totalSteps = 3; // Only count actual registration steps

  const handleContinueToValidation = (userEmail: string, userPassword: string) => {
    setEmail(userEmail);
    setPassword(userPassword);
    setStep(1); // Move to email validation
  };

  const handleEmailVerified = () => {
    setStep(2); // Move to first registration step (company data)
  };

  const handleBackFromValidation = () => {
    setStep(0); // Back to email/password step
  };

  const updateData = (updates: Partial<RegistrationData>) => {
    setData(prev => ({ ...prev, ...updates }));
  };

  const nextStep = () => {
    if (step < 4) { // 0,1: pre-reg, 2,3,4: actual steps
      setStep(step + 1);
    }
  };

  const prevStep = () => {
    if (step > 2) { // Can't go back from company data (step 2)
      setStep(step - 1);
    }
  };

  // Only show progress for actual registration steps (2-4)
  const progress = step >= 2 ? ((step - 1) / totalSteps) * 100 : 0;

  // Step 0: Email + Password
  if (step === 0) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <EmailPasswordStep 
          onContinue={handleContinueToValidation}
          onBack={() => window.history.back()}
        />
      </div>
    );
  }

  // Step 1: Email Validation
  if (step === 1) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background p-4">
        <EmailValidationStep 
          email={email}
          password={password}
          onEmailVerified={handleEmailVerified}
          onBack={handleBackFromValidation}
        />
      </div>
    );
  }

  // Steps 2-4: Actual registration
  const currentRegistrationStep = step - 1; // 2->1, 3->2, 4->3

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4">
      <Card className="w-full max-w-3xl">
        <CardHeader>
          <div className="flex items-center gap-2 mb-4">
            <img src="/src/assets/doyoueap-logo.png" alt="doyoueap" className="h-8" />
          </div>
          <CardTitle>Céges regisztráció</CardTitle>
          <CardDescription>
            {step === 2 && 'Cégadatok megadása'}
            {step === 3 && 'Csomag kiválasztása'}
            {step === 4 && 'Fizetési adatok'}
          </CardDescription>
          <div className="mt-4 space-y-2">
            <div className="flex justify-between text-sm text-muted-foreground">
              <span>{currentRegistrationStep}. lépés / {totalSteps}</span>
              <span>{Math.round(progress)}%</span>
            </div>
            <Progress value={progress} />
          </div>
        </CardHeader>
        <CardContent>
          {step === 2 && (
            <CompanyDataStep data={data} updateData={updateData} />
          )}
          {step === 3 && (
            <PackageSelectionStep data={data} updateData={updateData} />
          )}
          {step === 4 && (
            <PaymentStep data={data} updateData={updateData} />
          )}

          <div className="flex justify-between mt-6">
            <Button
              variant="outline"
              onClick={prevStep}
              disabled={step === 2}
            >
              <ChevronLeft className="h-4 w-4 mr-2" />
              Vissza
            </Button>
            <div className="flex gap-2">
              <Button variant="ghost">
                Folytatom később
              </Button>
              {step < 4 ? (
                <Button onClick={nextStep}>
                  Tovább
                  <ChevronRight className="h-4 w-4 ml-2" />
                </Button>
              ) : (
                <Button>
                  Regisztráció befejezése
                </Button>
              )}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
