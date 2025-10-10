import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { supabase } from '@/integrations/supabase/client';
import { Gift, Info } from 'lucide-react';
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

const SKIP_LOTTERY_VALUE = 'skip_lottery';

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

  useEffect(() => {
    fetchGifts();
  }, []);

  useEffect(() => {
    if (giftId && gifts.length > 0) {
      const gift = gifts.find(g => g.id === giftId);
      setSelectedGift(gift || null);
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
    // If "skip lottery" is selected, proceed without validation
    if (giftId === SKIP_LOTTERY_VALUE) {
      onGiftIdChange(''); // Clear the gift ID
      onNext();
      return;
    }

    if (!giftId) {
      toast({
        title: 'Hiányzó mező',
        description: 'Kérjük, válasszon ki egy ajándékot vagy a "Nincs ajándéksorsolás" opciót.',
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
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Gift className="h-5 w-5" />
            Ajándéksorsolás beállítása
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-6">
          {/* Gift selection */}
          <div className="space-y-2">
            <Label htmlFor="gift">Ajándék kiválasztása *</Label>
            <Select value={giftId} onValueChange={onGiftIdChange} disabled={loading}>
              <SelectTrigger id="gift">
                <SelectValue placeholder={loading ? 'Betöltés...' : 'Válasszon ajándékot'} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value={SKIP_LOTTERY_VALUE}>
                  <span className="text-muted-foreground italic">Nincs ajándéksorsolás</span>
                </SelectItem>
                {gifts.map((gift) => (
                  <SelectItem key={gift.id} value={gift.id}>
                    {gift.name} ({formatEUR(gift.value_eur)})
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Show lottery options only if a gift is selected */}
          {giftId && giftId !== SKIP_LOTTERY_VALUE && (
            <>
              {/* Selected gift value (read-only) */}
              {selectedGift && (
                <div className="space-y-2">
                  <Label>Ajándék értéke</Label>
                  <div className="text-2xl font-bold text-primary">
                    {formatEUR(selectedGift.value_eur)}
                  </div>
                </div>
              )}

              {/* Draw mode */}
              <div className="space-y-3">
                <Label>Sorsolás módja *</Label>
                <RadioGroup value={drawMode} onValueChange={onDrawModeChange}>
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="auto" id="auto" />
                    <Label htmlFor="auto" className="font-normal cursor-pointer">
                      Automatikus záráskor
                    </Label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="manual" id="manual" />
                    <Label htmlFor="manual" className="font-normal cursor-pointer">
                      Manuális indítás
                    </Label>
                  </div>
                </RadioGroup>
              </div>

              {/* Eligibility conditions */}
              <div className="space-y-3">
                <Label>Jogosultság feltételek</Label>
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
                        // TODO: Open data processing modal
                        toast({
                          title: 'Adatkezelési tájékoztató',
                          description: 'Az ajándéksorsolás céljából kezeljük a nyereménykódot és az opcionálisan megadott e-mail címet.',
                        });
                      }}
                    >
                      ajándéksorsolás adatkezelését
                    </a>
                    {' '}*
                  </Label>
                </div>
              </div>

              {/* Info alert */}
              <Alert>
                <Info className="h-4 w-4" />
                <AlertDescription>
                  <strong>Fontos:</strong> A sorsolás során generált nyereménykód jelenik meg a kitöltők számára. 
                  Az anonimitás megőrzése érdekében a rendszer nem tárolja a személyes adatokat.
                </AlertDescription>
              </Alert>
            </>
          )}
        </CardContent>
      </Card>

      {/* Navigation buttons */}
      <div className="flex justify-between">
        <Button type="button" variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button type="button" onClick={handleNext}>
          Tovább
        </Button>
      </div>
    </div>
  );
};
