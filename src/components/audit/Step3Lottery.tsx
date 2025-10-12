import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { supabase } from '@/integrations/supabase/client';
import { Gift, Info, Check } from 'lucide-react';
import { toast } from '@/hooks/use-toast';

interface Gift {
  id: string;
  name: string;
  value_eur: number;
}

interface Step3LotteryProps {
  giftId: string;
  drawMode: 'auto' | 'manual';
  lotteryConsent: boolean;
  onGiftIdChange: (value: string) => void;
  onDrawModeChange: (value: 'auto' | 'manual') => void;
  onLotteryConsentChange: (value: boolean) => void;
  onNext: () => void;
  onBack: () => void;
}

const formatEUR = (value: number): string => {
  const formatted = new Intl.NumberFormat('hu-HU', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(value);
  return formatted.replace(/\s/g, ' ') + ' €';
};

export const Step3Lottery = ({
  giftId,
  drawMode,
  lotteryConsent,
  onGiftIdChange,
  onDrawModeChange,
  onLotteryConsentChange,
  onNext,
  onBack,
}: Step3LotteryProps) => {
  const [gifts, setGifts] = useState<Gift[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedGift, setSelectedGift] = useState<Gift | null>(null);
  const [hasLottery, setHasLottery] = useState<'yes' | 'no'>('no');

  useEffect(() => {
    fetchGifts();
  }, []);

  useEffect(() => {
    if (giftId && gifts.length > 0) {
      const gift = gifts.find(g => g.id === giftId);
      setSelectedGift(gift || null);
      setHasLottery('yes');
    } else if (giftId) {
      setHasLottery('yes');
    }
  }, [giftId, gifts]);

  const fetchGifts = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('gifts')
        .select('id, name, value_eur')
        .eq('is_active', true)
        .order('is_default', { ascending: false })
        .order('name');

      if (error) throw error;
      setGifts(data || []);
    } catch (error) {
      console.error('Error fetching gifts:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült betölteni az ajándékokat.',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const handleNext = () => {
    // If "no lottery" is selected, clear gift ID and proceed
    if (hasLottery === 'no') {
      onGiftIdChange('');
      onNext();
      return;
    }

    // If lottery is enabled, validate fields
    if (!giftId) {
      toast({
        title: 'Hiányzó mező',
        description: 'Kérjük, válasszon ki egy ajándékot.',
        variant: 'destructive',
      });
      return;
    }

    if (!lotteryConsent) {
      toast({
        title: 'Hiányzó hozzájárulás',
        description: 'El kell fogadnia az adatkezelési feltételeket.',
        variant: 'destructive',
      });
      return;
    }

    onNext();
  };

  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Nyereményjáték beállítása</h2>
        <p className="text-muted-foreground text-lg">
          Növeld a részvételi kedvet nyereményjátékkal
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Gift className="h-6 w-6" />
            Nyereményjáték beállítása
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-6 pt-6">
          {/* Lottery enable/disable */}
          <div className="space-y-4">
            <Label className="text-base font-medium">Nyereményjáték *</Label>
            <div className="grid gap-3">
              <button
                type="button"
                onClick={() => {
                  setHasLottery('no');
                  onGiftIdChange('');
                  onLotteryConsentChange(false);
                }}
                className={`
                  relative w-full p-4 rounded-lg border-2 transition-all text-left
                  ${hasLottery === 'no' 
                    ? 'border-primary bg-primary/5 shadow-md' 
                    : 'border-border hover:border-primary/50 hover:bg-muted/30'
                  }
                  flex items-center justify-between
                `}
              >
                <span className="text-base font-medium">Nincs nyereményjáték</span>
                {hasLottery === 'no' && (
                  <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                    <Check className="w-4 h-4 text-primary-foreground" />
                  </div>
                )}
              </button>

              <button
                type="button"
                onClick={() => setHasLottery('yes')}
                className={`
                  relative w-full p-4 rounded-lg border-2 transition-all text-left
                  ${hasLottery === 'yes' 
                    ? 'border-primary bg-primary/5 shadow-md' 
                    : 'border-border hover:border-primary/50 hover:bg-muted/30'
                  }
                  flex items-center justify-between
                `}
              >
                <span className="text-base font-medium">Van nyereményjáték</span>
                {hasLottery === 'yes' && (
                  <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                    <Check className="w-4 h-4 text-primary-foreground" />
                  </div>
                )}
              </button>
            </div>
          </div>

          {/* Show lottery options only if enabled */}
          {hasLottery === 'yes' && (
            <>
              {/* Gift selection */}
              <div className="space-y-2">
                <Label htmlFor="gift" className="text-base font-medium">Ajándék kiválasztása *</Label>
                <Select value={giftId} onValueChange={onGiftIdChange} disabled={loading}>
                  <SelectTrigger id="gift" className="h-12 text-base">
                    <SelectValue placeholder={loading ? 'Betöltés...' : 'Válasszon ajándékot'} />
                  </SelectTrigger>
                  <SelectContent>
                    {gifts.map((gift) => (
                      <SelectItem key={gift.id} value={gift.id}>
                        {gift.name} ({formatEUR(gift.value_eur)})
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              {/* Selected gift value (read-only) */}
              {selectedGift && (
                <div className="space-y-2 p-4 bg-primary/5 rounded-lg border border-primary/20">
                  <Label className="text-base">Ajándék értéke</Label>
                  <div className="text-3xl font-bold text-primary">
                    {formatEUR(selectedGift.value_eur)}
                  </div>
                </div>
              )}

              {/* Draw mode */}
              <div className="space-y-4">
                <Label className="text-base font-medium">Sorsolás módja *</Label>
                <div className="grid gap-3">
                  <button
                    type="button"
                    onClick={() => onDrawModeChange('auto')}
                    className={`
                      relative w-full p-4 rounded-lg border-2 transition-all text-left
                      ${drawMode === 'auto' 
                        ? 'border-primary bg-primary/5 shadow-md' 
                        : 'border-border hover:border-primary/50 hover:bg-muted/30'
                      }
                      flex items-center justify-between
                    `}
                  >
                    <span className="text-base">Automatikus záráskor</span>
                    {drawMode === 'auto' && (
                      <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                        <Check className="w-4 h-4 text-primary-foreground" />
                      </div>
                    )}
                  </button>

                  <button
                    type="button"
                    onClick={() => onDrawModeChange('manual')}
                    className={`
                      relative w-full p-4 rounded-lg border-2 transition-all text-left
                      ${drawMode === 'manual' 
                        ? 'border-primary bg-primary/5 shadow-md' 
                        : 'border-border hover:border-primary/50 hover:bg-muted/30'
                      }
                      flex items-center justify-between
                    `}
                  >
                    <span className="text-base">Manuális indítás</span>
                    {drawMode === 'manual' && (
                      <div className="flex-shrink-0 w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                        <Check className="w-4 h-4 text-primary-foreground" />
                      </div>
                    )}
                  </button>
                </div>
              </div>

              {/* Eligibility conditions */}
              <div className="space-y-3">
                <Label className="text-base font-medium">Jogosultság feltételek</Label>
                <div className="space-y-2 ml-2">
                  <div className="flex items-start space-x-2">
                    <Checkbox id="complete" checked disabled />
                    <Label htmlFor="complete" className="font-normal text-sm leading-relaxed cursor-default">
                      Csak teljesen kitöltött kérdőívek vehetnek részt
                    </Label>
                  </div>
                  <div className="flex items-start space-x-2">
                    <Checkbox id="unique" checked disabled />
                    <Label htmlFor="unique" className="font-normal text-sm leading-relaxed cursor-default">
                      Egy beküldés = egy esély
                    </Label>
                  </div>
                </div>
                <p className="text-xs text-muted-foreground ml-2">
                  Ezek a feltételek alapértelmezetten érvényben vannak és nem módosíthatók.
                </p>
              </div>

              {/* Legal consent */}
              <div className="space-y-3">
                <div className="flex items-start space-x-2">
                  <Checkbox 
                    id="consent" 
                    checked={lotteryConsent}
                    onCheckedChange={onLotteryConsentChange}
                  />
                  <Label htmlFor="consent" className="font-normal text-sm leading-relaxed cursor-pointer">
                    Elolvastam és elfogadom az{' '}
                    <a 
                      href="#" 
                      className="text-primary underline"
                      onClick={(e) => {
                        e.preventDefault();
                        toast({
                          title: 'Adatkezelési tájékoztató',
                          description: 'Az ajándéksorsolás céljából kezeljük a nyereménykódot és az opcionálisan megadott e-mail címet.',
                        });
                      }}
                    >
                      nyereményjáték adatkezelését
                    </a>
                    {' '}*
                  </Label>
                </div>
              </div>

              {/* Info alert */}
              <Alert className="border-primary/50 bg-primary/5">
                <Info className="h-5 w-5 text-primary" />
                <AlertDescription className="text-base">
                  <strong>Fontos:</strong> A sorsolás során generált nyereménykód jelenik meg a kitöltők számára. 
                  Az anonimitás megőrzése érdekében a rendszer nem tárolja a személyes adatokat.
                </AlertDescription>
              </Alert>
            </>
          )}
        </CardContent>
      </Card>

      {/* Navigation buttons */}
      <div className="flex justify-between pt-4">
        <Button type="button" variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button type="button" onClick={handleNext} size="lg">
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
