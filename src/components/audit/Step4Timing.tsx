import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

interface Step4Props {
  startDate: string;
  expiresAt: string;
  enableRecurrence: boolean;
  recurrenceFrequency: string;
  packageType: 'starter' | 'professional' | 'enterprise' | 'partner' | null;
  onStartDateChange: (date: string) => void;
  onExpiresAtChange: (date: string) => void;
  onEnableRecurrenceChange: (enabled: boolean) => void;
  onRecurrenceFrequencyChange: (frequency: string) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step4Timing = ({
  startDate,
  expiresAt,
  enableRecurrence,
  recurrenceFrequency,
  packageType,
  onStartDateChange,
  onExpiresAtChange,
  onEnableRecurrenceChange,
  onRecurrenceFrequencyChange,
  onNext,
  onBack,
}: Step4Props) => {
  // Define available frequencies based on package type
  const getAvailableFrequencies = () => {
    if (packageType === 'starter') {
      return [
        { value: 'biannually', label: 'Félévente' }
      ];
    } else if (packageType === 'professional') {
      return [
        { value: 'biannually', label: 'Félévente' },
        { value: 'four-monthly', label: 'Négyhavonta' }
      ];
    } else if (packageType === 'enterprise' || packageType === 'partner') {
      return [
        { value: 'biannually', label: 'Félévente' },
        { value: 'four-monthly', label: 'Négyhavonta' },
        { value: 'quarterly', label: 'Negyedévente' }
      ];
    }
    // Default fallback
    return [{ value: 'biannually', label: 'Félévente' }];
  };

  const availableFrequencies = getAvailableFrequencies();

  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Időzítés és ismétlés</h2>
        <p className="text-muted-foreground text-lg">
          Állítsd be a felmérés időtartamát és opcionálisan az ismétlését
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="text-xl">Időtartam</CardTitle>
          <CardDescription className="text-base">
            Határozd meg a felmérés kezdő és záró dátumát
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6 pt-6">
          <div className="space-y-2">
            <Label htmlFor="start-date" className="text-base font-medium">Kezdő dátum</Label>
            <Input
              id="start-date"
              type="datetime-local"
              value={startDate}
              onChange={(e) => onStartDateChange(e.target.value)}
              className="h-12 text-base"
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="expires-at" className="text-base font-medium">Záró dátum</Label>
            <Input
              id="expires-at"
              type="datetime-local"
              value={expiresAt}
              onChange={(e) => onExpiresAtChange(e.target.value)}
              className="h-12 text-base"
            />
          </div>
        </CardContent>
      </Card>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="text-xl">Ismétlés (opcionális)</CardTitle>
          <CardDescription className="text-base">
            A felmérés automatikusan megismétlődhet megadott időközönként
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div className="flex items-center space-x-3">
            <Checkbox
              id="enable-recurrence"
              checked={enableRecurrence}
              onCheckedChange={(checked) => onEnableRecurrenceChange(checked as boolean)}
              className="w-5 h-5"
            />
            <Label htmlFor="enable-recurrence" className="cursor-pointer text-base font-medium">
              Ismétlődő felmérés beállítása
            </Label>
          </div>

          {enableRecurrence && (
            <div className="space-y-2 pt-4">
              <Label htmlFor="recurrence-frequency" className="text-base font-medium">Gyakoriság</Label>
              <Select value={recurrenceFrequency} onValueChange={onRecurrenceFrequencyChange}>
                <SelectTrigger id="recurrence-frequency" className="h-12 text-base">
                  <SelectValue placeholder="Válassz gyakoriságot" />
                </SelectTrigger>
                <SelectContent>
                  {availableFrequencies.map((freq) => (
                    <SelectItem key={freq.value} value={freq.value}>
                      {freq.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <p className="text-sm text-muted-foreground mt-2">
                A következő felmérések automatikusan létrejönnek a beállítások másolásával
              </p>
            </div>
          )}
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext} 
          disabled={!expiresAt}
          size="lg"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};