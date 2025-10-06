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
              <Card className="peer-data-[state=checked]:border-primary peer-data-[state=checked]:ring-2 peer-data-[state=checked]:ring-primary h-full flex flex-col">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle>Starter</CardTitle>
                      <CardDescription>Kezdő csomagunk kisebb szervezeteknek</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold">290.000 Ft/év</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <ul className="space-y-2 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Legfeljebb 200 munkavállaló</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Éves EAP audit</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>4Score módszertan</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Alapvető riportok</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Email támogatás</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>

          {/* Professional */}
          <div className="relative">
            <RadioGroupItem value="professional" id="professional" className="peer sr-only" />
            <Label htmlFor="professional" className="cursor-pointer">
              <Card className="peer-data-[state=checked]:border-primary peer-data-[state=checked]:ring-2 peer-data-[state=checked]:ring-primary border-primary h-full flex flex-col">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <div className="text-xs font-medium text-primary mb-1">AJÁNLOTT</div>
                      <CardTitle>Professional</CardTitle>
                      <CardDescription>A legnépszerűbb választás</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold">490.000 Ft/év</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <ul className="space-y-2 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Legfeljebb 500 munkavállaló</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Féléves EAP auditok</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>4Score módszertan</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Részletes riportok és elemzések</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Trend-elemzés</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Prioritásos támogatás</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Testre szabható kérdőívek</span>
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
              <Card className="peer-data-[state=checked]:border-primary peer-data-[state=checked]:ring-2 peer-data-[state=checked]:ring-primary h-full flex flex-col">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div>
                      <CardTitle>Enterprise</CardTitle>
                      <CardDescription>Nagy szervezetek számára</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-2xl font-bold">Egyedi árazás</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <ul className="space-y-2 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Korlátlan munkavállaló</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Negyedéves EAP auditok</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>4Score módszertan</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Teljes körű elemzések</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Prediktív modellek</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Dedikált ügyfélmenedzser</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>API integráció</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>White-label lehetőség</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Oktatás és tanácsadás</span>
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
