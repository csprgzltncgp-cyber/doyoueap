import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { supabase } from '@/integrations/supabase/client';
import { useToast } from '@/hooks/use-toast';

interface EmailValidationStepProps {
  onEmailVerified: (email: string) => void;
  onBack: () => void;
}

export const EmailValidationStep = ({ onEmailVerified, onBack }: EmailValidationStepProps) => {
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [emailSent, setEmailSent] = useState(false);
  const { toast } = useToast();

  const handleSendVerification = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!email || !email.includes('@')) {
      toast({
        title: "Hibás email cím",
        description: "Kérjük, adjon meg egy érvényes email címet.",
        variant: "destructive",
      });
      return;
    }

    setIsLoading(true);

    try {
      const { data, error } = await supabase.functions.invoke('send-verification-email', {
        body: { email },
      });

      if (error) throw error;

      setEmailSent(true);
      toast({
        title: "Email elküldve!",
        description: "Kérjük, ellenőrizze a postafiókját és kattintson a megerősítő linkre.",
      });

      // Start checking verification status
      checkVerificationStatus(email);
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

  const checkVerificationStatus = async (emailToCheck: string) => {
    const interval = setInterval(async () => {
      try {
        const { data, error } = await supabase
          .from('email_verifications')
          .select('verified')
          .eq('email', emailToCheck)
          .eq('verified', true)
          .single();

        if (data && data.verified) {
          clearInterval(interval);
          toast({
            title: "Email megerősítve!",
            description: "Most folytathatja a regisztrációt.",
          });
          onEmailVerified(emailToCheck);
        }
      } catch (error) {
        // Verification not yet complete, continue checking
      }
    }, 3000); // Check every 3 seconds

    // Stop checking after 10 minutes
    setTimeout(() => clearInterval(interval), 600000);
  };

  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <CardTitle>Email cím megerősítése</CardTitle>
        <CardDescription>
          A regisztráció első lépéseként kérjük, erősítse meg email címét.
        </CardDescription>
      </CardHeader>
      <CardContent>
        {!emailSent ? (
          <form onSubmit={handleSendVerification} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="email">Email cím</Label>
              <Input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="pelda@ceges.email.com"
                required
              />
              <p className="text-sm text-muted-foreground">
                Erre az email címre küldünk egy megerősítő linket.
              </p>
            </div>

            <div className="flex gap-2">
              <Button type="button" variant="outline" onClick={onBack}>
                Vissza
              </Button>
              <Button type="submit" disabled={isLoading} className="flex-1">
                {isLoading ? 'Küldés...' : 'Megerősítő email küldése'}
              </Button>
            </div>
          </form>
        ) : (
          <div className="space-y-4">
            <div className="bg-primary/10 border border-primary/20 rounded-lg p-4">
              <p className="text-sm">
                <strong>Email elküldve:</strong> {email}
              </p>
              <p className="text-sm text-muted-foreground mt-2">
                Kérjük, ellenőrizze a postafiókját és kattintson a megerősítő linkre. 
                Az oldal automatikusan továbblép, amint megerősíti az email címét.
              </p>
            </div>

            <div className="flex gap-2">
              <Button 
                variant="outline" 
                onClick={() => {
                  setEmailSent(false);
                  setEmail('');
                }}
              >
                Új email cím használata
              </Button>
              <Button 
                variant="outline"
                onClick={handleSendVerification}
                disabled={isLoading}
              >
                Email újraküldése
              </Button>
            </div>
          </div>
        )}
      </CardContent>
    </Card>
  );
};
