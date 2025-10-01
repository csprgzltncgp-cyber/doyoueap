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
  onStartDateChange,
  onExpiresAtChange,
  onEnableRecurrenceChange,
  onRecurrenceFrequencyChange,
  onNext,
  onBack,
}: Step4Props) => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Időzítés és ismétlés</h2>
        <p className="text-muted-foreground">
          Állítsa be az audit időtartamát és opcionálisan az ismétlését
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Időtartam</CardTitle>
          <CardDescription>
            Határozza meg az audit kezdő és záró dátumát
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="start-date">Kezdő dátum *</Label>
            <Input
              id="start-date"
              type="datetime-local"
              value={startDate}
              onChange={(e) => onStartDateChange(e.target.value)}
              required
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="expires-at">Záró dátum *</Label>
            <Input
              id="expires-at"
              type="datetime-local"
              value={expiresAt}
              onChange={(e) => onExpiresAtChange(e.target.value)}
              required
            />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Ismétlés (opcionális)</CardTitle>
          <CardDescription>
            Az audit automatikusan megismétlődhet megadott időközönként
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="flex items-center space-x-2">
            <Checkbox
              id="enable-recurrence"
              checked={enableRecurrence}
              onCheckedChange={(checked) => onEnableRecurrenceChange(checked as boolean)}
            />
            <Label htmlFor="enable-recurrence" className="cursor-pointer">
              Ismétlődő audit beállítása
            </Label>
          </div>

          {enableRecurrence && (
            <div className="space-y-2">
              <Label htmlFor="recurrence-frequency">Gyakoriság</Label>
              <Select value={recurrenceFrequency} onValueChange={onRecurrenceFrequencyChange}>
                <SelectTrigger id="recurrence-frequency">
                  <SelectValue placeholder="Válassz gyakoriságot" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="monthly">Havonta</SelectItem>
                  <SelectItem value="quarterly">Negyedévente</SelectItem>
                  <SelectItem value="biannually">Félévente</SelectItem>
                  <SelectItem value="annually">Évente</SelectItem>
                </SelectContent>
              </Select>
              <p className="text-sm text-muted-foreground">
                A következő auditok automatikusan létrejönnek a beállítások másolásával
              </p>
            </div>
          )}
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button onClick={onNext} disabled={!startDate || !expiresAt}>
          Következő lépés
        </Button>
      </div>
    </div>
  );
};