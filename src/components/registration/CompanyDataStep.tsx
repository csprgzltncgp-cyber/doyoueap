import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { RegistrationData } from './RegistrationWizard';
import { AlertCircle } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface CompanyDataStepProps {
  data: RegistrationData;
  updateData: (updates: Partial<RegistrationData>) => void;
}

const BLOCKED_DOMAINS = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com'];

export const CompanyDataStep = ({ data, updateData }: CompanyDataStepProps) => {
  const isEmailBlocked = data.contactEmail && BLOCKED_DOMAINS.some(domain => 
    data.contactEmail.toLowerCase().endsWith(`@${domain}`)
  );

  const domainMismatch = data.companyDomain && data.contactEmail && 
    !data.contactEmail.toLowerCase().endsWith(`@${data.companyDomain.toLowerCase()}`);

  return (
    <div className="space-y-6">
      {isEmailBlocked && (
        <Alert variant="destructive">
          <AlertCircle className="h-4 w-4" />
          <AlertDescription>
            Kérjük, céges email címmel regisztráljon. Privát email szolgáltatók (@gmail, @yahoo, stb.) nem engedélyezettek.
          </AlertDescription>
        </Alert>
      )}

      <div className="grid md:grid-cols-2 gap-4">
        <div className="space-y-2 md:col-span-2">
          <Label htmlFor="companyName">Cég neve *</Label>
          <Input
            id="companyName"
            value={data.companyName}
            onChange={(e) => updateData({ companyName: e.target.value })}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="country">Ország *</Label>
          <Select value={data.country} onValueChange={(value) => updateData({ country: value })}>
            <SelectTrigger>
              <SelectValue placeholder="Válasszon országot" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="HU">Magyarország</SelectItem>
              <SelectItem value="AT">Ausztria</SelectItem>
              <SelectItem value="DE">Németország</SelectItem>
              <SelectItem value="SK">Szlovákia</SelectItem>
              <SelectItem value="RO">Románia</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2">
          <Label htmlFor="vatId">Adószám / VAT ID *</Label>
          <Input
            id="vatId"
            value={data.vatId}
            onChange={(e) => updateData({ vatId: e.target.value })}
            placeholder="HU12345678"
            required
          />
        </div>

        <div className="space-y-2 md:col-span-2">
          <Label htmlFor="address">Székhely címe *</Label>
          <Input
            id="address"
            value={data.address}
            onChange={(e) => updateData({ address: e.target.value })}
            placeholder="Utca, házszám"
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="city">Város *</Label>
          <Input
            id="city"
            value={data.city}
            onChange={(e) => updateData({ city: e.target.value })}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="postalCode">Irányítószám *</Label>
          <Input
            id="postalCode"
            value={data.postalCode}
            onChange={(e) => updateData({ postalCode: e.target.value })}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="industry">Iparág *</Label>
          <Select value={data.industry} onValueChange={(value) => updateData({ industry: value })}>
            <SelectTrigger>
              <SelectValue placeholder="Válasszon iparágat" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="IT">Informatika</SelectItem>
              <SelectItem value="Manufacturing">Gyártás</SelectItem>
              <SelectItem value="Retail">Kereskedelem</SelectItem>
              <SelectItem value="Finance">Pénzügy</SelectItem>
              <SelectItem value="Healthcare">Egészségügy</SelectItem>
              <SelectItem value="Other">Egyéb</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2">
          <Label htmlFor="employeeCount">Létszám-kategória *</Label>
          <Select value={data.employeeCount} onValueChange={(value) => updateData({ employeeCount: value })}>
            <SelectTrigger>
              <SelectValue placeholder="Válasszon kategóriát" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="1-49">1-49 fő</SelectItem>
              <SelectItem value="50-249">50-249 fő</SelectItem>
              <SelectItem value="250-999">250-999 fő</SelectItem>
              <SelectItem value="1000+">1000+ fő</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2 md:col-span-2">
          <Label htmlFor="companyDomain">Céges domain *</Label>
          <Input
            id="companyDomain"
            value={data.companyDomain}
            onChange={(e) => updateData({ companyDomain: e.target.value })}
            placeholder="pelda.com"
            required
          />
        </div>

        <div className="space-y-2 md:col-span-2">
          <h3 className="font-medium">Elsődleges kapcsolattartó (HR admin)</h3>
        </div>

        <div className="space-y-2 md:col-span-2">
          <Label htmlFor="contactName">Név *</Label>
          <Input
            id="contactName"
            value={data.contactName}
            onChange={(e) => updateData({ contactName: e.target.value })}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="contactEmail">Céges email *</Label>
          <Input
            id="contactEmail"
            type="email"
            value={data.contactEmail}
            onChange={(e) => updateData({ contactEmail: e.target.value })}
            required
          />
          {domainMismatch && (
            <p className="text-sm text-yellow-600">
              A kapcsolattartó email domain-je nem egyezik a céges domain-nel. Kérjük, magyarázza meg az eltérést, vagy módosítsa az email címet.
            </p>
          )}
        </div>

        <div className="space-y-2">
          <Label htmlFor="contactPhone">Telefonszám *</Label>
          <Input
            id="contactPhone"
            type="tel"
            value={data.contactPhone}
            onChange={(e) => updateData({ contactPhone: e.target.value })}
            required
          />
        </div>
      </div>

      <div className="space-y-3 pt-4 border-t">
        <div className="flex items-start gap-2">
          <Checkbox
            id="terms"
            checked={data.termsAccepted}
            onCheckedChange={(checked) => updateData({ termsAccepted: checked as boolean })}
          />
          <Label htmlFor="terms" className="text-sm cursor-pointer">
            Elfogadom az <a href="#" className="text-primary underline">Általános Szerződési Feltételeket</a>
          </Label>
        </div>
        <div className="flex items-start gap-2">
          <Checkbox
            id="privacy"
            checked={data.privacyAccepted}
            onCheckedChange={(checked) => updateData({ privacyAccepted: checked as boolean })}
          />
          <Label htmlFor="privacy" className="text-sm cursor-pointer">
            Elfogadom az <a href="#" className="text-primary underline">Adatkezelési Tájékoztatót</a>
          </Label>
        </div>
        <div className="flex items-start gap-2">
          <Checkbox
            id="dpa"
            checked={data.dpaAccepted}
            onCheckedChange={(checked) => updateData({ dpaAccepted: checked as boolean })}
          />
          <Label htmlFor="dpa" className="text-sm cursor-pointer">
            Elfogadom az <a href="#" className="text-primary underline">Adatfeldolgozási Megállapodást</a>
          </Label>
        </div>
      </div>
    </div>
  );
};
