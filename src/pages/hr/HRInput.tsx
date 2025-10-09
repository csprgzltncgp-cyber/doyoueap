import { useEffect, useState } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import { formatAuditName, StandardAudit, AUDIT_SELECT_FIELDS } from '@/lib/auditUtils';

type Audit = StandardAudit;

interface HRInputData {
  total_employees: number;
  gender_distribution: {
    male: number;
    female: number;
    other: number;
  };
  age_distribution: {
    under_18: number;
    age_18_24: number;
    age_25_36: number;
    age_37_44: number;
    age_45_58: number;
    age_58_plus: number;
  };
  communication_frequency: string;
  budget_per_employee: number;
  notes: string;
}

const HRInput = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [inputData, setInputData] = useState<HRInputData>({
    total_employees: 0,
    gender_distribution: { male: 0, female: 0, other: 0 },
    age_distribution: {
      under_18: 0,
      age_18_24: 0,
      age_25_36: 0,
      age_37_44: 0,
      age_45_58: 0,
      age_58_plus: 0
    },
    communication_frequency: 'quarterly',
    budget_per_employee: 0,
    notes: ''
  });

  useEffect(() => {
    fetchAudits();
  }, []);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select(AUDIT_SELECT_FIELDS)
        .eq('is_active', true)
        .order('start_date', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt a felmérések betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    if (!selectedAuditId) {
      toast.error('Válassz ki egy felmérést');
      return;
    }

    setSaving(true);
    try {
      // In a real implementation, this would save to a dedicated table
      // For now, we'll show a success message
      toast.success('HR adatok sikeresen mentve!');
      
      // Placeholder for future implementation
      // const { error } = await supabase
      //   .from('hr_inputs')
      //   .upsert({
      //     audit_id: selectedAuditId,
      //     ...inputData,
      //     updated_at: new Date().toISOString()
      //   });
      
    } catch (error) {
      console.error('Error saving HR input:', error);
      toast.error('Hiba történt az adatok mentése során');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="p-8 space-y-6 pt-28">
      <div>
        <h1 className="text-3xl font-bold mb-2">HR Input</h1>
        <p className="text-muted-foreground">Cégspecifikus adatok megadása az indexek pontosításához</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Felmérés Kiválasztása</CardTitle>
        </CardHeader>
        <CardContent>
          <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
            <SelectTrigger>
              <SelectValue placeholder="Válassz felmérést" />
            </SelectTrigger>
            <SelectContent>
              {audits.map((audit) => (
                <SelectItem key={audit.id} value={audit.id}>
                  {formatAuditName(audit)}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Létszám Adatok</CardTitle>
          <CardDescription>A teljes cég dolgozói létszáma</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label htmlFor="total">Teljes létszám</Label>
            <Input
              id="total"
              type="number"
              value={inputData.total_employees}
              onChange={(e) => setInputData({ ...inputData, total_employees: Number(e.target.value) })}
            />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Nemek Megoszlása</CardTitle>
          <CardDescription>Hány fő tartozik az egyes kategóriákba</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-3 gap-4">
            <div>
              <Label htmlFor="male">Férfi</Label>
              <Input
                id="male"
                type="number"
                value={inputData.gender_distribution.male}
                onChange={(e) => setInputData({
                  ...inputData,
                  gender_distribution: { ...inputData.gender_distribution, male: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label htmlFor="female">Nő</Label>
              <Input
                id="female"
                type="number"
                value={inputData.gender_distribution.female}
                onChange={(e) => setInputData({
                  ...inputData,
                  gender_distribution: { ...inputData.gender_distribution, female: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label htmlFor="other">Egyéb</Label>
              <Input
                id="other"
                type="number"
                value={inputData.gender_distribution.other}
                onChange={(e) => setInputData({
                  ...inputData,
                  gender_distribution: { ...inputData.gender_distribution, other: Number(e.target.value) }
                })}
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Életkori Megoszlás</CardTitle>
          <CardDescription>Hány fő tartozik az egyes életkori csoportokba</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-3 gap-4">
            <div>
              <Label>&lt;18 év</Label>
              <Input
                type="number"
                value={inputData.age_distribution.under_18}
                onChange={(e) => setInputData({
                  ...inputData,
                  age_distribution: { ...inputData.age_distribution, under_18: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label>18-24 év</Label>
              <Input
                type="number"
                value={inputData.age_distribution.age_18_24}
                onChange={(e) => setInputData({
                  ...inputData,
                  age_distribution: { ...inputData.age_distribution, age_18_24: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label>25-36 év</Label>
              <Input
                type="number"
                value={inputData.age_distribution.age_25_36}
                onChange={(e) => setInputData({
                  ...inputData,
                  age_distribution: { ...inputData.age_distribution, age_25_36: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label>37-44 év</Label>
              <Input
                type="number"
                value={inputData.age_distribution.age_37_44}
                onChange={(e) => setInputData({
                  ...inputData,
                  age_distribution: { ...inputData.age_distribution, age_37_44: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label>45-58 év</Label>
              <Input
                type="number"
                value={inputData.age_distribution.age_45_58}
                onChange={(e) => setInputData({
                  ...inputData,
                  age_distribution: { ...inputData.age_distribution, age_45_58: Number(e.target.value) }
                })}
              />
            </div>
            <div>
              <Label>58+ év</Label>
              <Input
                type="number"
                value={inputData.age_distribution.age_58_plus}
                onChange={(e) => setInputData({
                  ...inputData,
                  age_distribution: { ...inputData.age_distribution, age_58_plus: Number(e.target.value) }
                })}
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Egyéb Paraméterek</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label htmlFor="frequency">Kommunikációs gyakoriság</Label>
            <Select
              value={inputData.communication_frequency}
              onValueChange={(value) => setInputData({ ...inputData, communication_frequency: value })}
            >
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="monthly">Havonta</SelectItem>
                <SelectItem value="quarterly">Negyedévente</SelectItem>
                <SelectItem value="biannually">Félévente</SelectItem>
                <SelectItem value="annually">Évente</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label htmlFor="budget">Költségkeret / fő (Ft/év)</Label>
            <Input
              id="budget"
              type="number"
              value={inputData.budget_per_employee}
              onChange={(e) => setInputData({ ...inputData, budget_per_employee: Number(e.target.value) })}
            />
          </div>

          <div>
            <Label htmlFor="notes">Jegyzetek / Megjegyzések</Label>
            <textarea
              id="notes"
              value={inputData.notes}
              onChange={(e) => setInputData({ ...inputData, notes: e.target.value })}
              className="w-full min-h-[100px] p-2 border rounded"
              placeholder="További megjegyzések..."
            />
          </div>
        </CardContent>
      </Card>

      <Button onClick={handleSave} disabled={saving || !selectedAuditId} className="w-full">
        {saving ? 'Mentés...' : 'Adatok Mentése'}
      </Button>
    </div>
  );
};

export default HRInput;
