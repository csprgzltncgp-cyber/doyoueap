import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { RegistrationData } from './RegistrationWizard';
import { CheckCircle, Circle } from 'lucide-react';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { cn } from '@/lib/utils';

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
            <Label htmlFor="starter" className="cursor-pointer block">
              <Card className={cn(
                "h-full flex flex-col transition-all hover:shadow-md border-2",
                data.selectedPackage === 'starter' 
                  ? "border-primary ring-2 ring-primary bg-primary/5" 
                  : "border-border hover:border-primary/50"
              )}>
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-2">
                        {data.selectedPackage === 'starter' ? (
                          <CheckCircle className="h-5 w-5 text-primary" />
                        ) : (
                          <Circle className="h-5 w-5 text-muted-foreground" />
                        )}
                        <CardTitle>Starter</CardTitle>
                      </div>
                      <CardDescription>Alapvető betekintés az EAP-programba</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-3xl font-bold">150 €<span className="text-lg font-normal text-muted-foreground">/hó</span></div>
                      <div className="text-sm text-muted-foreground mt-1">1 800 € / év</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <ul className="space-y-2 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>2 felmérés / év (félévente)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Ajándéksorsolás minden felmérés végén</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>4Score riport (Tudatosság · Használat · Bizalom & Hajlandóság · Hatás)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Alap dashboard + letölthető riportok</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>

          {/* Professional */}
          <div className="relative">
            <RadioGroupItem value="professional" id="professional" className="peer sr-only" />
            <Label htmlFor="professional" className="cursor-pointer block">
              <Card className={cn(
                "h-full flex flex-col transition-all hover:shadow-md border-2",
                data.selectedPackage === 'professional' 
                  ? "border-primary ring-2 ring-primary bg-primary/5" 
                  : "border-primary/30 hover:border-primary/50"
              )}>
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <div className="text-xs font-medium text-primary mb-1">AJÁNLOTT</div>
                      <div className="flex items-center gap-2 mb-2">
                        {data.selectedPackage === 'professional' ? (
                          <CheckCircle className="h-5 w-5 text-primary" />
                        ) : (
                          <Circle className="h-5 w-5 text-muted-foreground" />
                        )}
                        <CardTitle>Professional</CardTitle>
                      </div>
                      <CardDescription>Rendszeres és mélyreható elemzés</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-3xl font-bold">325 €<span className="text-lg font-normal text-muted-foreground">/hó</span></div>
                      <div className="text-sm text-muted-foreground mt-1">3 900 € / év</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <ul className="space-y-2 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>3 felmérés / év</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Ajándéksorsolás minden felmérés végén</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Teljes riportkészlet (minden statisztika + 4Score)</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Testreszabható kérdőív-design</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Trendösszevetések a felmérések között</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>

          {/* Enterprise */}
          <div className="relative">
            <RadioGroupItem value="enterprise" id="enterprise" className="peer sr-only" />
            <Label htmlFor="enterprise" className="cursor-pointer block">
              <Card className={cn(
                "h-full flex flex-col transition-all hover:shadow-md border-2",
                data.selectedPackage === 'enterprise' 
                  ? "border-primary ring-2 ring-primary bg-primary/5" 
                  : "border-border hover:border-primary/50"
              )}>
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-2">
                        {data.selectedPackage === 'enterprise' ? (
                          <CheckCircle className="h-5 w-5 text-primary" />
                        ) : (
                          <Circle className="h-5 w-5 text-muted-foreground" />
                        )}
                        <CardTitle>Enterprise</CardTitle>
                      </div>
                      <CardDescription>Maximális átláthatóság és integráció</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-3xl font-bold">480 €<span className="text-lg font-normal text-muted-foreground">/hó</span></div>
                      <div className="text-sm text-muted-foreground mt-1">5 760 € / év</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <ul className="space-y-2 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>4 felmérés / év</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Ajándéksorsolás minden felmérés végén</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>Teljes riportkészlet + trend- és összehasonlító elemzés</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>White-label lehetőség</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-primary mt-0.5" />
                      <span>API integráció</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </Label>
          </div>

          {/* Partner Program */}
          <div className="relative">
            <RadioGroupItem value="partner" id="partner" className="peer sr-only" />
            <Label htmlFor="partner" className="cursor-pointer block">
              <Card className={cn(
                "h-full flex flex-col transition-all hover:shadow-md border-2 bg-gradient-to-br from-[#3572ef]/5 to-[#3abef9]/5",
                data.selectedPackage === 'partner' 
                  ? "border-[#3572ef] ring-2 ring-[#3572ef]" 
                  : "border-[#3572ef]/30 hover:border-[#3572ef]/50"
              )}>
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <div className="text-xs font-medium text-[#3572ef] mb-1">SZOLGÁLTATÓKNAK</div>
                      <div className="flex items-center gap-2 mb-2">
                        {data.selectedPackage === 'partner' ? (
                          <CheckCircle className="h-5 w-5 text-[#3572ef]" />
                        ) : (
                          <Circle className="h-5 w-5 text-muted-foreground" />
                        )}
                        <CardTitle>Partner Program</CardTitle>
                      </div>
                      <CardDescription>Emeld új szintre az ügyfélriportjaidat</CardDescription>
                    </div>
                    <div className="text-right">
                      <div className="text-3xl font-bold">Egyedi ajánlat</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="flex-1 flex flex-col justify-between">
                  <p className="text-sm font-medium mb-3">
                    Bővítsd szolgáltatásportfóliód olyan riportadatokkal, amelyek egyedivé teszik ajánlatodat.
                  </p>
                  <ul className="space-y-3 mb-4">
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-[#3572ef] mt-0.5" />
                      <div>
                        <p className="font-medium">REST API integráció</p>
                        <p className="text-sm text-muted-foreground">Zökkenőmentes adatkapcsolat rendszereiddel</p>
                      </div>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-[#3572ef] mt-0.5" />
                      <div>
                        <p className="font-medium">White-label megoldás</p>
                        <p className="text-sm text-muted-foreground">Saját arculatoddal, a te nevedben</p>
                      </div>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-[#3572ef] mt-0.5" />
                      <div>
                        <p className="font-medium">Partner központ</p>
                        <p className="text-sm text-muted-foreground">Összes ügyfeled átlátható menedzselése</p>
                      </div>
                    </li>
                    <li className="flex items-start gap-2">
                      <CheckCircle className="h-5 w-5 text-[#3572ef] mt-0.5" />
                      <p className="font-medium">Dedikált technikai támogatás</p>
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
