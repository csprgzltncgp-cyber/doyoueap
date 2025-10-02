import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RegistrationData } from './RegistrationWizard';
import { CreditCard, Link2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useState } from 'react';

interface PaymentStepProps {
  data: RegistrationData;
  updateData: (updates: Partial<RegistrationData>) => void;
}

export const PaymentStep = ({ data }: PaymentStepProps) => {
  const [useStripeLink, setUseStripeLink] = useState(false);

  const getPackagePrice = () => {
    if (data.selectedPackage === 'starter') {
      return data.billingCycle === 'monthly' ? '149 €/hó' : '1 490 €/év';
    }
    if (data.selectedPackage === 'pro') {
      return data.billingCycle === 'monthly' ? '399 €/hó' : '3 990 €/év';
    }
    return 'Egyedi ajánlat';
  };

  const getPackageName = () => {
    if (data.selectedPackage === 'starter') return 'Starter';
    if (data.selectedPackage === 'pro') return 'Pro';
    return 'Enterprise';
  };

  return (
    <div className="space-y-6">
      {/* Order Summary */}
      <Card>
        <CardHeader>
          <CardTitle>Rendelés összegzése</CardTitle>
        </CardHeader>
        <CardContent className="space-y-2">
          <div className="flex justify-between">
            <span className="font-medium">Kiválasztott csomag:</span>
            <span>{getPackageName()}</span>
          </div>
          <div className="flex justify-between">
            <span className="font-medium">Számlázási ciklus:</span>
            <span>{data.billingCycle === 'monthly' ? 'Havi' : 'Éves'}</span>
          </div>
          <div className="flex justify-between text-lg font-bold pt-2 border-t">
            <span>Fizetendő:</span>
            <span>{getPackagePrice()}</span>
          </div>
        </CardContent>
      </Card>

      {data.selectedPackage === 'enterprise' ? (
        <Card>
          <CardHeader>
            <CardTitle>Enterprise kapcsolatfelvétel</CardTitle>
            <CardDescription>
              Hamarosan felvesszük Önnel a kapcsolatot az egyedi ajánlattal kapcsolatban.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Az Enterprise csomag esetén egyedi szerződést készítünk az Ön igényei alapján. 
              Az értékesítési csapatunk 1-2 munkanapon belül kapcsolatba lép Önnel.
            </p>
          </CardContent>
        </Card>
      ) : (
        <Card>
          <CardHeader>
            <CardTitle>Fizetési adatok</CardTitle>
            <CardDescription>
              <CreditCard className="inline h-4 w-4 mr-1" />
              Biztonságos fizetés
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            {/* Stripe Link option */}
            <div className="flex items-center justify-between p-4 border rounded-lg">
              <div className="flex items-center gap-3">
                <Link2 className="h-5 w-5 text-primary" />
                <div>
                  <p className="font-medium">Link fiók használata</p>
                  <p className="text-sm text-muted-foreground">Gyors fizetés mentett Link fiókkal</p>
                </div>
              </div>
              <Button 
                variant={useStripeLink ? "default" : "outline"}
                onClick={() => setUseStripeLink(!useStripeLink)}
              >
                {useStripeLink ? 'Használatban' : 'Használom'}
              </Button>
            </div>

            {!useStripeLink && (
              <>
                <div className="space-y-2">
                  <Label htmlFor="cardNumber">Kártyaszám</Label>
                  <Input
                    id="cardNumber"
                    placeholder="1234 5678 9012 3456"
                    maxLength={19}
                  />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="expiry">Lejárat</Label>
                    <Input
                      id="expiry"
                      placeholder="MM/ÉÉ"
                      maxLength={5}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="cvc">CVC</Label>
                    <Input
                      id="cvc"
                      placeholder="123"
                      maxLength={3}
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="cardName">Kártyabirtokos neve</Label>
                  <Input
                    id="cardName"
                    placeholder="Ahogy a kártyán szerepel"
                  />
                </div>
              </>
            )}

            <p className="text-xs text-muted-foreground">
              A fizetési adatokat biztonságosan tároljuk és titkosítjuk. 
              {data.billingCycle === 'yearly' && ' Az éves előfizetés egyszeri terheléssel kerül felszámolásra.'}
              {data.billingCycle === 'monthly' && ' A havi díj automatikusan megújul minden hónapban.'}
            </p>
          </CardContent>
        </Card>
      )}

      <Card>
        <CardHeader>
          <CardTitle>Számlázási adatok</CardTitle>
        </CardHeader>
        <CardContent className="space-y-2 text-sm">
          <div className="grid grid-cols-2 gap-2">
            <span className="text-muted-foreground">Cég neve:</span>
            <span className="font-medium">{data.companyName}</span>
            
            <span className="text-muted-foreground">Ország:</span>
            <span className="font-medium">{data.country}</span>
            
            <span className="text-muted-foreground">Adószám / VAT ID:</span>
            <span className="font-medium">{data.vatId}</span>
            
            <span className="text-muted-foreground">Székhely címe:</span>
            <span className="font-medium">{data.address}</span>
            
            <span className="text-muted-foreground">Város:</span>
            <span className="font-medium">{data.city}</span>
            
            <span className="text-muted-foreground">Irányítószám:</span>
            <span className="font-medium">{data.postalCode}</span>
            
            <span className="text-muted-foreground">Kapcsolattartó:</span>
            <span className="font-medium">{data.contactName}</span>
            
            <span className="text-muted-foreground">Telefonszám:</span>
            <span className="font-medium">{data.contactPhone}</span>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
