import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { supabase } from '@/integrations/supabase/client';
import { useToast } from '@/hooks/use-toast';
import { Eye, EyeOff, Mail, Lock, CheckCircle } from 'lucide-react';

interface EmailPasswordStepProps {
  onContinue: (email: string, password: string) => void;
  onBack: () => void;
}

export const EmailPasswordStep = ({ onContinue, onBack }: EmailPasswordStepProps) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const { toast } = useToast();

  const validateEmail = (email: string) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const validatePassword = (password: string) => {
    return password.length >= 8;
  };

  const handleContinue = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // Validation
    if (!validateEmail(email)) {
      toast({
        title: "Érvénytelen email cím",
        description: "Kérjük, adjon meg egy érvényes email címet.",
        variant: "destructive",
      });
      return;
    }

    if (!validatePassword(password)) {
      toast({
        title: "Túl rövid jelszó",
        description: "A jelszónak legalább 8 karakter hosszúnak kell lennie.",
        variant: "destructive",
      });
      return;
    }

    if (password !== confirmPassword) {
      toast({
        title: "A jelszavak nem egyeznek",
        description: "Kérjük, ellenőrizze, hogy mindkét jelszómező azonos.",
        variant: "destructive",
      });
      return;
    }

    setIsLoading(true);

    try {
      // Check if email already exists
      const { data: existingVerification } = await supabase
        .from('email_verifications')
        .select('verified')
        .eq('email', email)
        .single();

      if (existingVerification?.verified) {
        toast({
          title: "Ez az email már használatban van",
          description: "Kérjük, használjon másik email címet vagy jelentkezzen be.",
          variant: "destructive",
        });
        setIsLoading(false);
        return;
      }

      // Continue to email verification step
      onContinue(email, password);
    } catch (error: any) {
      console.error('Error:', error);
      toast({
        title: "Hiba történt",
        description: "Kérjük, próbálja újra később.",
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  const passwordStrength = password.length >= 12 ? 'strong' : password.length >= 8 ? 'medium' : 'weak';

  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <div className="flex items-center gap-2 mb-4">
          <img src="/src/assets/doyoueap-logo.png" alt="doyoueap" className="h-8" />
        </div>
        <CardTitle>Fiók létrehozása</CardTitle>
        <CardDescription>
          Kezdje a regisztrációt email címe és egy biztonságos jelszó megadásával
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleContinue} className="space-y-6">
          {/* Email field */}
          <div className="space-y-2">
            <Label htmlFor="email" className="flex items-center gap-2">
              <Mail className="h-4 w-4" />
              Email cím (felhasználónév)
            </Label>
            <Input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="pelda@vallalatom.hu"
              required
            />
            <p className="text-sm text-muted-foreground">
              Ezt az email címet fogja használni bejelentkezéshez is.
            </p>
          </div>

          {/* Password field */}
          <div className="space-y-2">
            <Label htmlFor="password" className="flex items-center gap-2">
              <Lock className="h-4 w-4" />
              Jelszó
            </Label>
            <div className="relative">
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Minimum 8 karakter"
                required
              />
              <Button
                type="button"
                variant="ghost"
                size="sm"
                className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                onClick={() => setShowPassword(!showPassword)}
              >
                {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
              </Button>
            </div>
            {password && (
              <div className="space-y-1">
                <div className="flex gap-1">
                  <div className={`h-1 flex-1 rounded ${password.length >= 8 ? 'bg-green-500' : 'bg-gray-200'}`} />
                  <div className={`h-1 flex-1 rounded ${password.length >= 10 ? 'bg-green-500' : 'bg-gray-200'}`} />
                  <div className={`h-1 flex-1 rounded ${password.length >= 12 ? 'bg-green-500' : 'bg-gray-200'}`} />
                </div>
                <p className={`text-xs ${passwordStrength === 'strong' ? 'text-green-600' : passwordStrength === 'medium' ? 'text-yellow-600' : 'text-red-600'}`}>
                  {passwordStrength === 'strong' && '✓ Erős jelszó'}
                  {passwordStrength === 'medium' && 'Közepes erősségű jelszó'}
                  {passwordStrength === 'weak' && 'Gyenge jelszó - minimum 8 karakter szükséges'}
                </p>
              </div>
            )}
          </div>

          {/* Confirm password field */}
          <div className="space-y-2">
            <Label htmlFor="confirmPassword">Jelszó megerősítése</Label>
            <div className="relative">
              <Input
                id="confirmPassword"
                type={showConfirmPassword ? "text" : "password"}
                value={confirmPassword}
                onChange={(e) => setConfirmPassword(e.target.value)}
                placeholder="Írja be újra a jelszót"
                required
              />
              <Button
                type="button"
                variant="ghost"
                size="sm"
                className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                onClick={() => setShowConfirmPassword(!showConfirmPassword)}
              >
                {showConfirmPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
              </Button>
            </div>
            {confirmPassword && (
              <p className={`text-xs flex items-center gap-1 ${password === confirmPassword ? 'text-green-600' : 'text-red-600'}`}>
                {password === confirmPassword ? (
                  <>
                    <CheckCircle className="h-3 w-3" />
                    A jelszavak egyeznek
                  </>
                ) : (
                  'A jelszavak nem egyeznek'
                )}
              </p>
            )}
          </div>

          {/* Action buttons */}
          <div className="flex gap-2 pt-4">
            <Button type="button" variant="outline" onClick={onBack}>
              Vissza
            </Button>
            <Button 
              type="submit" 
              disabled={isLoading || !email || !password || !confirmPassword || password !== confirmPassword}
              className="flex-1"
            >
              {isLoading ? 'Folyamatban...' : 'Tovább az email megerősítéshez'}
            </Button>
          </div>
        </form>

        <div className="mt-6 p-4 bg-muted/50 rounded-lg">
          <h4 className="text-sm font-medium mb-2">Mi következik ezután?</h4>
          <ol className="text-sm text-muted-foreground space-y-1 list-decimal list-inside">
            <li>Email megerősítés - küldünk egy linket a megadott címre</li>
            <li>Céges adatok megadása</li>
            <li>Csomag kiválasztása</li>
            <li>Fizetési adatok megadása</li>
          </ol>
        </div>
      </CardContent>
    </Card>
  );
};
