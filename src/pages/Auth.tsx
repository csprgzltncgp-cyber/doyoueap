import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { RegistrationWizard } from '@/components/registration/RegistrationWizard';
import logo from '@/assets/logo.png';

const Auth = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showRegistration, setShowRegistration] = useState(false);
  const [isDemoMode, setIsDemoMode] = useState(true);
  const { signIn, user } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (user) {
      navigate('/?section=focus');
    }
  }, [user, navigate]);

  const handleSignIn = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    await signIn(email, password);
    setIsLoading(false);
  };

  const handleDemoLogin = async () => {
    setIsLoading(true);
    // Demo login with predefined credentials
    await signIn('demo@eappulse.com', 'demo123456');
    setIsLoading(false);
  };

  if (showRegistration) {
    return <RegistrationWizard />;
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4">
      <Card className="w-full max-w-md">
        <CardHeader>
          <div className="flex items-center gap-2 mb-4">
            <img src={logo} alt="doyoueap" className="h-8" />
          </div>
          <CardTitle>Bejelentkezés</CardTitle>
          <CardDescription>
            {isDemoMode ? 'Demo verzió kipróbálása' : 'Jelentkezzen be fiókjába'}
          </CardDescription>
        </CardHeader>
        <CardContent>
          {/* Mode Switch */}
          <div className="flex items-center justify-between mb-6 p-3 bg-muted/50 rounded-lg">
            <div className="flex items-center gap-2">
              <span className={`text-sm ${!isDemoMode ? 'font-semibold text-foreground' : 'text-muted-foreground'}`}>
                Jelszavas
              </span>
              <Switch
                checked={isDemoMode}
                onCheckedChange={setIsDemoMode}
              />
              <span className={`text-sm ${isDemoMode ? 'font-semibold text-foreground' : 'text-muted-foreground'}`}>
                Demo
              </span>
            </div>
          </div>

          {isDemoMode ? (
            /* Demo Login */
            <div className="space-y-4">
              <p className="text-sm text-muted-foreground text-center">
                Próbálja ki a rendszert demo módban, felhasználónév és jelszó nélkül.
              </p>
              <Button 
                onClick={handleDemoLogin} 
                className="w-full" 
                style={{ backgroundColor: '#04565f' }}
                disabled={isLoading}
              >
                {isLoading ? 'Folyamatban...' : 'Demo belépés'}
              </Button>
            </div>
          ) : (
            /* Password Login */
            <form onSubmit={handleSignIn} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  type="email"
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
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                />
              </div>
              <Button type="submit" className="w-full" disabled={isLoading}>
                {isLoading ? 'Folyamatban...' : 'Bejelentkezés'}
              </Button>
            </form>
          )}
          
          {/* Registration section hidden
          <div className="mt-4 text-center border-t pt-4">
            <p className="text-sm text-muted-foreground mb-2">
              Még nincs fiókja?
            </p>
            <Button 
              variant="outline" 
              className="w-full"
              onClick={() => setShowRegistration(true)}
            >
              Céges regisztráció
            </Button>
          </div>
          */}
        </CardContent>
      </Card>
    </div>
  );
};

export default Auth;
