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
      // Check if token exists and is not expired
      const { data: verification, error: fetchError } = await supabase
        .from('email_verifications')
        .select('*')
        .eq('token', token)
        .single();

      if (fetchError || !verification) {
        setStatus('error');
        setMessage('Érvénytelen vagy lejárt megerősítő link.');
        return;
      }

      // Check if already verified
      if (verification.verified) {
        setStatus('success');
        setMessage('Ez az email cím már megerősítésre került.');
        return;
      }

      // Check if expired
      const expiresAt = new Date(verification.expires_at);
      if (expiresAt < new Date()) {
        setStatus('error');
        setMessage('Ez a megerősítő link lejárt. Kérjük, kérjen újat.');
        return;
      }

      // Mark as verified
      const { error: updateError } = await supabase
        .from('email_verifications')
        .update({ verified: true })
        .eq('token', token);

      if (updateError) {
        setStatus('error');
        setMessage('Hiba történt a megerősítés során. Kérjük, próbálja újra.');
        return;
      }

      setStatus('success');
      setMessage('Email cím sikeresen megerősítve! Bezárhatja ezt az ablakot és folytathatja a regisztrációt.');
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
