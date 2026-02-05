import { useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import logo from '@/assets/logo_black_v2.png';
import { useAuth } from '@/hooks/useAuth';
import { MobileNav } from '@/components/navigation/MobileNav';
import { MagazinContent } from '@/components/MagazinContent';

const Magazin = () => {
  const navigate = useNavigate();
  const { user, role, loading, signOut } = useAuth();

  return (
    <div className="min-h-screen bg-background overflow-x-hidden">
      {/* Header with Navigation */}
      <header className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 fixed md:sticky top-0 z-50 w-full">
        <div className="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center relative">
          <div className="flex items-center gap-2">
            <MobileNav 
              user={user}
              role={role}
              section=""
              onNavigate={navigate}
              onLogout={signOut}
            />
            <img 
              src={logo} 
              alt="doyoueap" 
              className="h-8 cursor-pointer" 
              onClick={() => navigate('/')}
            />
          </div>
          <div className="flex items-center gap-4">
            {user && role === 'hr' && (
              <button
                onClick={() => navigate('/?section=focus')}
                className="text-sm bg-[#04565f] text-white transition-colors px-3 py-2 rounded-md hover:bg-[#04565f]/90 hidden md:flex"
              >
                EAP Dashboard
              </button>
            )}
            {!loading && (
              user ? (
                <Button onClick={signOut} variant="outline" className="hidden md:flex">
                  Kilépés
                </Button>
              ) : (
                <Button onClick={() => navigate('/auth')} className="hidden md:flex">
                  EAP Dashboard bejelentkezés
                </Button>
              )
            )}
          </div>
        </div>
      </header>

      <MagazinContent />
    </div>
  );
};

export default Magazin;
