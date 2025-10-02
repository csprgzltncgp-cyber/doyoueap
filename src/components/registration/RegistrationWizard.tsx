import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
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
  contactEmail: string;
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
  contactEmail: '',
  contactPhone: '',
  companyDomain: '',
  selectedPackage: null,
  billingCycle: 'yearly',
  termsAccepted: false,
  privacyAccepted: false,
  dpaAccepted: false,
};

export const RegistrationWizard = () => {
  const [step, setStep] = useState(1);
  const [data, setData] = useState<RegistrationData>(initialData);
  const totalSteps = 3;

  const updateData = (updates: Partial<RegistrationData>) => {
    setData(prev => ({ ...prev, ...updates }));
  };

  const nextStep = () => {
    if (step < totalSteps) {
      setStep(step + 1);
    }
  };

  const prevStep = () => {
    if (step > 1) {
      setStep(step - 1);
    }
  };

  const progress = (step / totalSteps) * 100;

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4">
      <Card className="w-full max-w-3xl">
        <CardHeader>
          <div className="flex items-center gap-2 mb-4">
            <img src="/src/assets/doyoueap-logo.png" alt="doyoueap" className="h-8" />
          </div>
          <CardTitle>Céges regisztráció</CardTitle>
          <CardDescription>
            {step === 1 && 'Cégadatok megadása'}
            {step === 2 && 'Csomag kiválasztása'}
            {step === 3 && 'Fizetési adatok'}
          </CardDescription>
          <div className="mt-4 space-y-2">
            <div className="flex justify-between text-sm text-muted-foreground">
              <span>{step}. lépés / {totalSteps}</span>
              <span>{Math.round(progress)}%</span>
            </div>
            <Progress value={progress} />
          </div>
        </CardHeader>
        <CardContent>
          {step === 1 && (
            <CompanyDataStep data={data} updateData={updateData} />
          )}
          {step === 2 && (
            <PackageSelectionStep data={data} updateData={updateData} />
          )}
          {step === 3 && (
            <PaymentStep data={data} updateData={updateData} />
          )}

          <div className="flex justify-between mt-6">
            <Button
              variant="outline"
              onClick={prevStep}
              disabled={step === 1}
            >
              <ChevronLeft className="h-4 w-4 mr-2" />
              Vissza
            </Button>
            <div className="flex gap-2">
              <Button variant="ghost">
                Folytatom később
              </Button>
              {step < totalSteps ? (
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
