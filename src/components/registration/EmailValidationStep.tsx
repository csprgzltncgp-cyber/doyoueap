import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { supabase } from '@/integrations/supabase/client';
import { useToast } from '@/hooks/use-toast';
import { Mail, CheckCircle, Clock, RefreshCw } from 'lucide-react';
import logo from '@/assets/logo_black_v2.png';

interface EmailValidationStepProps {
  email: string;
  password: string;
  onEmailVerified: () => void;
  onBack: () => void;
}

export const EmailValidationStep = ({ email, password, onEmailVerified, onBack }: EmailValidationStepProps) => {
  const [isLoading, setIsLoading] = useState(false);
  const [emailSent, setEmailSent] = useState(false);
  const [checkingInterval, setCheckingInterval] = useState<NodeJS.Timeout | null>(null);
  const [isVerified, setIsVerified] = useState(false);
  const { toast } = useToast();

  useEffect(() => {
    // Check if email is already verified when component mounts
    const checkInitialVerification = async () => {
      try {
        const { data } = await supabase
          .from('email_verifications')
          .select('verified')
          .eq('email', email)
          .eq('verified', true)
          .maybeSingle();

        if (data && data.verified) {
          setIsVerified(true);
          toast({
            title: "Email már megerősítve! ✓",
            description: "Folytathatja a regisztrációt.",
          });
          setTimeout(() => {
            onEmailVerified();
          }, 500);
          return;
        }
      } catch (error) {
        console.error('Error checking verification:', error);
      }

      // If not verified, send verification email
      handleSendVerification();
    };

    checkInitialVerification();
    
    return () => {
      if (checkingInterval) {
        clearInterval(checkingInterval);
        setCheckingInterval(null);
      }
    };
  }, []);

  const handleSendVerification = async () => {
    setIsLoading(true);

    try {
      const { data, error } = await supabase.functions.invoke('send-verification-email', {
        body: { 
          email,
          origin: window.location.origin 
        },
      });

      if (error) throw error;

      setEmailSent(true);
      toast({
        title: "Megerősítő email elküldve!",
        description: `Ellenőrizze postafiókját: ${email}`,
      });

      // Start checking verification status
      const interval = startCheckingVerification(email);
      setCheckingInterval(interval);
    } catch (error: any) {
      console.error('Error sending verification email:', error);
      toast({
        title: "Hiba történt",
        description: "Nem sikerült elküldeni a megerősítő emailt. Kérjük, próbálja újra.",
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  const startCheckingVerification = (emailToCheck: string): NodeJS.Timeout => {
    const interval = setInterval(async () => {
      if (isVerified) {
        clearInterval(interval);
        return;
      }
      
      try {
        const { data, error } = await supabase
          .from('email_verifications')
          .select('verified')
          .eq('email', emailToCheck)
          .eq('verified', true)
          .maybeSingle();

        if (data && data.verified && !isVerified) {
          setIsVerified(true);
          clearInterval(interval);
          setCheckingInterval(null);
          
          toast({
            title: "Email megerősítve! ✓",
            description: "Folytathatja a regisztrációt.",
          });

          // Proceed to next step after a brief delay
          setTimeout(() => {
            onEmailVerified();
          }, 500);
        }
      } catch (error) {
        // Verification not yet complete, continue checking
      }
    }, 3000); // Check every 3 seconds
    
    return interval;
  };


  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <div className="flex items-center gap-2 mb-4">
          <img src={logo} alt="doyoueap" className="h-8" />
        </div>
        <CardTitle className="flex items-center gap-2">
          <Mail className="h-6 w-6" />
          Email cím megerősítése
        </CardTitle>
        <CardDescription>
          Megerősítő emailt küldtünk a következő címre: <strong>{email}</strong>
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-6">
        {/* Status indicator */}
        <div className="bg-primary/10 border border-primary/20 rounded-lg p-6 space-y-4">
          <div className="flex items-start gap-3">
            <Clock className="h-5 w-5 text-primary mt-1 animate-pulse" />
            <div className="flex-1">
              <h4 className="font-medium mb-1">Várakozás az email megerősítésére...</h4>
              <p className="text-sm text-muted-foreground">
                Kérjük, nyissa meg postafiókját és kattintson a megerősítő linkre. 
                Az oldal automatikusan továbblép, amint megerősíti az email címét.
              </p>
            </div>
          </div>

          <div className="flex items-start gap-3 border-t pt-4">
            <CheckCircle className="h-5 w-5 text-muted-foreground mt-1" />
            <div className="flex-1">
              <h4 className="font-medium mb-1 text-muted-foreground">Mi történik ezután?</h4>
              <p className="text-sm text-muted-foreground">
                Email megerősítés után automatikusan létrehozzuk fiókját, és folytathatja 
                a regisztrációt a céges adatok megadásával.
              </p>
            </div>
          </div>
        </div>

        {/* Help section */}
        <div className="border rounded-lg p-4 space-y-3">
          <h4 className="font-medium text-sm">Nem kapta meg az emailt?</h4>
          <ul className="text-sm text-muted-foreground space-y-2 list-disc list-inside">
            <li>Ellenőrizze a spam/levélszemét mappát</li>
            <li>Várjon néhány percet - az email kézbesítés eltarthat egy ideig</li>
            <li>Ellenőrizze, hogy helyesen írta-e be az email címet</li>
          </ul>
        </div>

        {/* Action buttons */}
        <div className="flex gap-2">
          <Button 
            variant="outline" 
            onClick={onBack}
            className="flex-1"
          >
            Új email cím használata
          </Button>
          <Button 
            variant="outline"
            onClick={handleSendVerification}
            disabled={isLoading}
            className="flex items-center gap-2"
          >
            <RefreshCw className={`h-4 w-4 ${isLoading ? 'animate-spin' : ''}`} />
            Email újraküldése
          </Button>
        </div>

        <p className="text-xs text-center text-muted-foreground">
          A megerősítő link 24 órán belül lejár.
        </p>
      </CardContent>
    </Card>
  );
};
