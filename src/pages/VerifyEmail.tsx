import { useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const VerifyEmail = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [status, setStatus] = useState<'verifying' | 'success' | 'error'>('verifying');
  const [message, setMessage] = useState('Email cím ellenőrzése...');

  useEffect(() => {
    const token = searchParams.get('token');
    
    if (!token) {
      setStatus('error');
      setMessage('Hiányzó megerősítő token.');
      return;
    }

    verifyToken(token);
  }, [searchParams]);

  const verifyToken = async (token: string) => {
    try {
      // Use the secure server-side function to verify the email
      const { data, error } = await supabase
        .rpc('verify_email_with_token', { _token: token });

      if (error) {
        console.error('Verification error:', error);
        setStatus('error');
        setMessage('Hiba történt a megerősítés során. Kérjük, próbálja újra.');
        return;
      }

      // Check the response from the secure function
      if (data.success) {
        setStatus('success');
        setMessage(data.message || 'Email cím sikeresen megerősítve! Bezárhatja ezt az ablakot és folytathatja a regisztrációt.');
      } else {
        setStatus('error');
        setMessage(data.error || 'Hiba történt a megerősítés során.');
      }
    } catch (error) {
      console.error('Verification error:', error);
      setStatus('error');
      setMessage('Hiba történt. Kérjük, próbálja újra később.');
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4">
      <Card className="w-full max-w-md">
        <CardHeader>
          <div className="flex items-center gap-2 mb-4">
            <img src="/src/assets/logo.png" alt="doyoueap" className="h-8" />
          </div>
          <CardTitle>
            {status === 'success' && '✓ Sikeres megerősítés'}
            {status === 'error' && '✗ Hiba történt'}
            {status === 'verifying' && 'Ellenőrzés...'}
          </CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground mb-4">{message}</p>
          
          {status === 'success' && (
            <p className="text-sm text-muted-foreground">
              Térjen vissza a regisztrációs ablakba a folytatáshoz.
            </p>
          )}

          {status === 'error' && (
            <Button onClick={() => navigate('/auth')} className="w-full">
              Vissza a bejelentkezéshez
            </Button>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default VerifyEmail;
