import { useEffect, useState } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
import { CheckCircle, XCircle, Loader2 } from 'lucide-react';
import logo from '@/assets/logo_black_v2.png';

const ApproveAdmin = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
  const [message, setMessage] = useState('');

  useEffect(() => {
    const approveUser = async () => {
      const token = searchParams.get('token');
      const userId = searchParams.get('userId');

      if (!token || !userId) {
        setStatus('error');
        setMessage('Érvénytelen jóváhagyási link');
        return;
      }

      try {
        const { data, error } = await supabase.functions.invoke('approve-admin-access', {
          body: { token, userId },
        });

        if (error) throw error;

        if (data?.success) {
          setStatus('success');
          setMessage('Admin hozzáférés sikeresen jóváhagyva!');
          toast.success('Admin jóváhagyva!');
          setTimeout(() => navigate('/superadmin'), 3000);
        } else {
          setStatus('error');
          setMessage(data?.message || 'Jóváhagyási hiba');
        }
      } catch (error: any) {
        setStatus('error');
        setMessage(error.message || 'Hiba történt a jóváhagyás során');
        toast.error('Jóváhagyási hiba');
      }
    };

    approveUser();
  }, [searchParams, navigate]);

  return (
    <div className="min-h-screen bg-background flex items-center justify-center p-4">
      <Card className="w-full max-w-md">
        <CardHeader className="text-center">
          <img src={logo} alt="doyoueap" className="h-12 mx-auto mb-4" />
          <CardTitle>Admin Jóváhagyás</CardTitle>
          <CardDescription>
            {status === 'loading' && 'Feldolgozás...'}
            {status === 'success' && 'Sikeres jóváhagyás'}
            {status === 'error' && 'Hiba történt'}
          </CardDescription>
        </CardHeader>
        <CardContent className="text-center">
          {status === 'loading' && (
            <Loader2 className="h-16 w-16 animate-spin mx-auto text-primary" />
          )}
          {status === 'success' && (
            <>
              <CheckCircle className="h-16 w-16 mx-auto text-green-500 mb-4" />
              <p className="text-sm text-muted-foreground">{message}</p>
              <p className="text-sm text-muted-foreground mt-2">
                Átirányítás a bejelentkezéshez...
              </p>
            </>
          )}
          {status === 'error' && (
            <>
              <XCircle className="h-16 w-16 mx-auto text-red-500 mb-4" />
              <p className="text-sm text-muted-foreground">{message}</p>
            </>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default ApproveAdmin;
