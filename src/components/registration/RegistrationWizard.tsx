import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { EmailPasswordStep } from './EmailPasswordStep';
import { EmailValidationStep } from './EmailValidationStep';
import { CompanyDataStep } from './CompanyDataStep';
import { PackageSelectionStep } from './PackageSelectionStep';
import { PaymentStep } from './PaymentStep';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { supabase } from '@/integrations/supabase/client';
import { useToast } from '@/hooks/use-toast';

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
  
  // Billing address
  billingAddressSameAsCompany: boolean;
  billingAddress: string;
  billingCity: string;
  billingPostalCode: string;
  
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
  billingAddressSameAsCompany: true,
  billingAddress: '',
  billingCity: '',
  billingPostalCode: '',
  selectedPackage: 'pro',
  billingCycle: 'yearly',
  termsAccepted: false,
  privacyAccepted: false,
  dpaAccepted: false,
};

export const RegistrationWizard = () => {
  const navigate = useNavigate();
  const { toast } = useToast();
  const [step, setStep] = useState(0); // 0: email+password, 1: validation, 2-4: registration steps
  const [data, setData] = useState<RegistrationData>(initialData);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
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

  const handleFinishRegistration = async () => {
    setIsSubmitting(true);
    
    try {
      // Step 1: Create Supabase Auth user
      const { data: signUpData, error: signUpError } = await supabase.auth.signUp({
        email,
        password,
        options: {
          emailRedirectTo: `${window.location.origin}/`,
          data: {
            full_name: data.contactName,
          }
        },
      });

      if (signUpError) {
        toast({
          title: "Regisztrációs hiba",
          description: signUpError.message,
          variant: "destructive",
        });
        setIsSubmitting(false);
        return;
      }

      if (!signUpData.user) {
        toast({
          title: "Hiba",
          description: "Nem sikerült létrehozni a fiókot",
          variant: "destructive",
        });
        setIsSubmitting(false);
        return;
      }

      // Step 2: Sign in the user
      const { data: signInData, error: signInError } = await supabase.auth.signInWithPassword({
        email,
        password,
      });

      if (signInError) {
        toast({
          title: "Bejelentkezési hiba",
          description: signInError.message,
          variant: "destructive",
        });
        setIsSubmitting(false);
        return;
      }

      // Step 3: Update the user's profile with all registration data
      const { error: updateError } = await supabase
        .from('profiles')
        .update({
          company_name: data.companyName,
          full_name: data.contactName,
          country: data.country,
          vat_id: data.vatId,
          address: data.address,
          city: data.city,
          postal_code: data.postalCode,
          industry: data.industry,
          employee_count: data.employeeCount,
          contact_phone: data.contactPhone,
          billing_address_same_as_company: data.billingAddressSameAsCompany,
          billing_address: data.billingAddress,
          billing_city: data.billingCity,
          billing_postal_code: data.billingPostalCode,
          selected_package: data.selectedPackage,
          billing_cycle: data.billingCycle,
        })
        .eq('id', signInData.user.id);

      if (updateError) {
        console.error('Error updating profile:', updateError);
      }

      // Step 4: Show success message
      toast({
        title: "Sikeres regisztráció!",
        description: "Üdvözöljük a rendszerben.",
      });

      // Step 5: Redirect to HR Dashboard
      navigate('/hr');
      
    } catch (error: any) {
      console.error('Registration error:', error);
      toast({
        title: "Hiba történt",
        description: "Kérjük, próbálja újra később.",
        variant: "destructive",
      });
    } finally {
      setIsSubmitting(false);
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

          <div className="flex justify-end mt-6">
            {step > 2 && (
              <Button
                variant="outline"
                onClick={prevStep}
                className="mr-auto"
              >
                <ChevronLeft className="h-4 w-4 mr-2" />
                Vissza
              </Button>
            )}
            {step < 4 ? (
              <Button 
                onClick={nextStep}
                disabled={step === 3 && !data.selectedPackage}
              >
                Tovább
                <ChevronRight className="h-4 w-4 ml-2" />
              </Button>
            ) : (
              <Button onClick={handleFinishRegistration} disabled={isSubmitting}>
                {isSubmitting ? 'Feldolgozás...' : 'Regisztráció befejezése'}
              </Button>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
