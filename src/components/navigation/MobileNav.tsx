import { useState } from 'react';
import { Menu } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger, SheetHeader, SheetTitle } from '@/components/ui/sheet';

interface MobileNavProps {
  user: any;
  role: string | null;
  section: string;
  onNavigate: (path: string) => void;
  onLogout: () => void;
}

export function MobileNav({ user, role, section, onNavigate, onLogout }: MobileNavProps) {
  const [open, setOpen] = useState(false);

  const handleNavClick = (path: string) => {
    onNavigate(path);
    setOpen(false);
  };

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button variant="ghost" size="icon" className="md:hidden">
          <Menu className="h-5 w-5" />
        </Button>
      </SheetTrigger>
      <SheetContent side="left" className="w-[280px]">
        <SheetHeader>
          <SheetTitle>Menü</SheetTitle>
        </SheetHeader>
        <nav className="flex flex-col gap-4 mt-6">
          <button
            onClick={() => handleNavClick('/bemutatkozas')}
            className="text-left px-4 py-2 rounded-md hover:bg-muted transition-colors"
          >
            EAP Pulse
          </button>
          <button
            onClick={() => handleNavClick('/arak')}
            className="text-left px-4 py-2 rounded-md hover:bg-muted transition-colors"
          >
            Árak és Csomagok
          </button>
          <button
            onClick={() => handleNavClick('/magazin')}
            className="text-left px-4 py-2 rounded-md hover:bg-muted transition-colors"
          >
            The Journalist!
          </button>
          {user && role === 'hr' && (
            <button
              onClick={() => handleNavClick('/?section=focus')}
              className={`text-left px-4 py-2 rounded-md transition-colors ${
                section 
                  ? 'bg-[#3572ef] text-white font-semibold' 
                  : 'hover:bg-muted'
              }`}
            >
              Dashboard
            </button>
          )}
          <div className="border-t pt-4 mt-4">
            {user ? (
              <Button onClick={onLogout} variant="outline" className="w-full">
                Kilépés
              </Button>
            ) : (
              <Button onClick={() => handleNavClick('/auth')} className="w-full">
                Bejelentkezés
              </Button>
            )}
          </div>
        </nav>
      </SheetContent>
    </Sheet>
  );
}
