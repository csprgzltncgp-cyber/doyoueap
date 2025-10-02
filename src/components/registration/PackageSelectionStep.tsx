import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { RegistrationData } from './RegistrationWizard';
import { CheckCircle } from 'lucide-react';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';

interface PackageSelectionStepProps {
  data: RegistrationData;
  updateData: (updates: Partial<RegistrationData>) => void;
}

export const PackageSelectionStep = ({ data, updateData }: PackageSelectionStepProps) => {
  return (
    <div className="space-y-6">
      <div className="space-y-4">
        <Label>Válasszon számlázási ciklust</Label>
        <Tabs value={data.billingCycle} onValueChange={(value) => updateData({ billingCycle: value as 'monthly' | 'yearly' })}>
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="monthly">Havi fizetés</TabsTrigger>
            <TabsTrigger value="yearly">Éves fizetés (kedvezménnyel)</TabsTrigger>
          </TabsList>
        </Tabs>
      </div>

      <RadioGroup value={data.selectedPackage || ''} onValueChange={(value) => updateData({ selectedPackage: value as any })}>
        <div className="space-y-4">
          {/* Starter */}
          <div className="relative">
            <RadioGroupItem value="starter" id="starter" className="peer sr-only" />
            <Label htmlFor="starter" className="cursor-pointer">
              <Card className="peer-data-[state=checked]:border-primary peer-data-[state=checked]:ring-2 peer-data-[state=checked]:ring-primary">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle>Starter</CardTitle>
                      <CardDescription>Kis cégeknek, 50-250 fő</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold">
                        {data.billingCycle === 'monthly' ? '149 €/hó' : '1 490 €/év'}
                      </div>
                      {data.billingCycle === 'yearly' && (
                        <div className="text-sm text-green-600">124 €/hó átlagban</div>
                      )}
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <ul className="space-y-2">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Évente max. 1 audit</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Alap KPI-k (Awareness, Trust, Usage, Impact)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>PDF export</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Email support</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>

          {/* Pro */}
          <div className="relative">
            <RadioGroupItem value="pro" id="pro" className="peer sr-only" />
            <Label htmlFor="pro" className="cursor-pointer">
              <Card className="peer-data-[state=checked]:border-primary peer-data-[state=checked]:ring-2 peer-data-[state=checked]:ring-primary border-primary">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <div className="text-xs font-medium text-primary mb-1">NÉPSZERŰ</div>
                      <CardTitle>Pro</CardTitle>
                      <CardDescription>Közepes cégeknek, 250-1000 fő</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold">
                        {data.billingCycle === 'monthly' ? '399 €/hó' : '3 990 €/év'}
                      </div>
                      {data.billingCycle === 'yearly' && (
                        <div className="text-sm text-green-600">332 €/hó átlagban</div>
                      )}
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <ul className="space-y-2">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Negyedévente audit (ismétlés funkcióval)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Demográfiai bontás, trendek, összehasonlítás</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>PDF + Excel export</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Brandelhető User App (logó + 4 szín)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Kommunikációs támogatás (sablonok, QR generátor)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Email + chat support</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>

          {/* Enterprise */}
          <div className="relative">
            <RadioGroupItem value="enterprise" id="enterprise" className="peer sr-only" />
            <Label htmlFor="enterprise" className="cursor-pointer">
              <Card className="peer-data-[state=checked]:border-primary peer-data-[state=checked]:ring-2 peer-data-[state=checked]:ring-primary">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle>Enterprise</CardTitle>
                      <CardDescription>Nagyvállalatoknak, &gt;1000 fő</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold">Egyedi ajánlat</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <ul className="space-y-2">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Korlátlan audit</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Teljes funkcionalitás (KPI-k, trendek, benchmark, motivációk)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>PDF, Excel, PowerPoint export</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Teljes brandelés + saját domain opció</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>SSO integráció, jogosultságkezelés</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Dedikált account manager, SLA</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>
        </div>
      </RadioGroup>
    </div>
  );
};
