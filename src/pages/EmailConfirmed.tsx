import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { supabase } from '@/integrations/supabase/client';
import { CheckCircle, Loader2 } from 'lucide-react';
import logo from '@/assets/logo_black_v2.png';

const EmailConfirmed = () => {
  const navigate = useNavigate();
  const [status, setStatus] = useState<'processing' | 'success'>('processing');

  useEffect(() => {
    const processVerification = async () => {
      const { data: { user } } = await supabase.auth.getUser();
      
      if (user && user.email_confirmed_at) {
        // Trigger the approval email to admin
        await supabase.functions.invoke('check-email-verification', {
          body: { userId: user.id },
        });
        
        setStatus('success');
      }
    };

    processVerification();
  }, []);

  return (
    <div className="min-h-screen bg-background flex items-center justify-center p-4">
      <Card className="w-full max-w-md">
        <CardHeader className="text-center">
          <img src={logo} alt="doyoueap" className="h-12 mx-auto mb-4" />
          <CardTitle>Email Megerősítve</CardTitle>
          <CardDescription>
            {status === 'processing' ? 'Feldolgozás...' : 'Sikeres megerősítés'}
          </CardDescription>
        </CardHeader>
        <CardContent className="text-center space-y-4">
          {status === 'processing' && (
            <Loader2 className="h-16 w-16 animate-spin mx-auto text-primary" />
          )}
          {status === 'success' && (
            <>
              <CheckCircle className="h-16 w-16 mx-auto text-green-500 mb-4" />
              <div className="space-y-2">
                <p className="text-sm text-muted-foreground">
                  Email címed sikeresen megerősítve!
                </p>
                <p className="text-sm text-muted-foreground">
                  A rendszergazda hamarosan jóváhagyja a regisztrációdat.
                  Értesítést fogsz kapni emailben, amikor ez megtörtént.
                </p>
              </div>
              <Button onClick={() => navigate('/superadmin')} className="w-full mt-4">
                Vissza a bejelentkezéshez
              </Button>
            </>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default EmailConfirmed;
