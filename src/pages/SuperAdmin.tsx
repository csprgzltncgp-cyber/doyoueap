import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
import logo from '@/assets/logo.png';

const SuperAdmin = () => {
  const [isLogin, setIsLogin] = useState(true);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [fullName, setFullName] = useState('');
  const [verificationCode, setVerificationCode] = useState('');
  const [showVerification, setShowVerification] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      // Create user account with email verification
      const { data: authData, error: authError } = await supabase.auth.signUp({
        email,
        password,
        options: {
          emailRedirectTo: `${window.location.origin}/email-confirmed`,
          data: {
            full_name: fullName,
          },
        },
      });

      if (authError) throw authError;

      // Store pending approval request (will be processed after email verification)
      if (authData.user) {
        const { error: dbError } = await supabase
          .from('admin_approval_requests')
          .insert({
            user_id: authData.user.id,
            email: email,
            full_name: fullName,
            approval_token: '', // Will be set after email verification
            pending_verification: true,
            expires_at: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 days
          });

        if (dbError) {
          console.error('Database error:', dbError);
        }
      }

      toast.success('Regisztráció sikeres! Ellenőrizd az email címedet a megerősítő linkért.');
      setIsLogin(true);
      setEmail('');
      setPassword('');
      setFullName('');
    } catch (error: any) {
      toast.error(error.message || 'Regisztrációs hiba');
    } finally {
      setIsLoading(false);
    }
  };

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password,
      });

      if (error) throw error;

      // Check if email is verified
      if (!data.user?.email_confirmed_at) {
        await supabase.auth.signOut();
        toast.error('Kérlek erősítsd meg az email címedet először!');
        setIsLoading(false);
        return;
      }

      // Check and send approval email if needed
      await supabase.functions.invoke('check-email-verification', {
        body: { userId: data.user.id },
      });

      toast.success('Sikeres bejelentkezés!');
      navigate('/admin');
    } catch (error: any) {
      toast.error(error.message || 'Bejelentkezési hiba');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-background flex items-center justify-center p-4">
      <Card className="w-full max-w-md">
        <CardHeader className="text-center">
          <img src={logo} alt="doyoueap" className="h-12 mx-auto mb-4" />
          <CardTitle>Super Admin</CardTitle>
          <CardDescription>
            {isLogin ? 'Jelentkezz be az admin felületre' : 'Regisztrálj admin jogosultságért'}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={isLogin ? handleLogin : handleRegister} className="space-y-4">
            {!isLogin && (
              <div className="space-y-2">
                <Label htmlFor="fullName">Teljes Név</Label>
                <Input
                  id="fullName"
                  type="text"
                  placeholder="Teljes Neved"
                  value={fullName}
                  onChange={(e) => setFullName(e.target.value)}
                  required
                />
              </div>
            )}
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                placeholder="email@example.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Jelszó</Label>
              <Input
                id="password"
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
            <Button type="submit" className="w-full" disabled={isLoading}>
              {isLoading ? 'Feldolgozás...' : (isLogin ? 'Bejelentkezés' : 'Regisztráció')}
            </Button>
            <Button
              type="button"
              variant="ghost"
              className="w-full"
              onClick={() => {
                setIsLogin(!isLogin);
                setEmail('');
                setPassword('');
                setFullName('');
              }}
            >
              {isLogin ? 'Regisztráció' : 'Már van fiókom'}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};

export default SuperAdmin;
