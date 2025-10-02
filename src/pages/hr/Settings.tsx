import { useEffect, useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AlertCircle, User, Building2, Lock, Mail, Bell, Database, Globe, Users, CreditCard } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Separator } from "@/components/ui/separator";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";


const Settings = () => {
  const { toast } = useToast();
  const [loading, setLoading] = useState(true);
  const [profile, setProfile] = useState<any>(null);
  const [userEmail, setUserEmail] = useState("");

  useEffect(() => {
    fetchProfileData();
  }, []);

  const fetchProfileData = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      
      if (user) {
        setUserEmail(user.email || "");
        
        const { data, error } = await supabase
          .from('profiles')
          .select('*')
          .eq('id', user.id)
          .single();

        if (error) throw error;
        setProfile(data);
      }
    } catch (error) {
      console.error('Error fetching profile:', error);
      toast({
        title: "Hiba",
        description: "Nem sikerült betölteni a profil adatokat",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="p-6 flex items-center justify-center">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Beállítások</h1>
        <p className="text-muted-foreground mt-2">
          Fiók, cég adatok és rendszerbeállítások kezelése
        </p>
      </div>


      {!profile && (
        <Alert>
          <AlertCircle className="h-4 w-4" />
          <AlertDescription>
            <strong>Figyelem:</strong> A profil adatok még nincsenek kitöltve. Kérjük, töltse ki a regisztráció során megadott adatokat.
          </AlertDescription>
        </Alert>
      )}

      <div className="grid gap-6">
        {/* Profil adatok */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Profil adatok
            </CardTitle>
            <CardDescription>
              Az Ön személyes adatai (regisztráció során megadva)
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="fullName">Teljes név</Label>
              <Input id="fullName" value={profile?.full_name || ""} readOnly />
            </div>
            <div className="space-y-2">
              <Label htmlFor="contactPhone">Telefonszám</Label>
              <Input id="contactPhone" value={profile?.contact_phone || ""} readOnly />
            </div>
          </CardContent>
        </Card>

        {/* Cég adatok */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Building2 className="h-5 w-5" />
              Cég adatok
            </CardTitle>
            <CardDescription>
              A cég adatai (regisztráció során megadva)
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="companyName">Cégnév</Label>
                <Input id="companyName" value={profile?.company_name || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="country">Ország</Label>
                <Input id="country" value={profile?.country || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="vatId">Adószám / VAT ID</Label>
                <Input id="vatId" value={profile?.vat_id || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="companyDomain">Céges domain</Label>
                <Input id="companyDomain" value={profile?.company_domain || ""} readOnly />
              </div>
              <div className="space-y-2 col-span-2">
                <Label htmlFor="address">Székhely címe</Label>
                <Input id="address" value={profile?.address || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="city">Város</Label>
                <Input id="city" value={profile?.city || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="postalCode">Irányítószám</Label>
                <Input id="postalCode" value={profile?.postal_code || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="industry">Iparág</Label>
                <Input id="industry" value={profile?.industry || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="employeeCount">Létszám</Label>
                <Input id="employeeCount" value={profile?.employee_count || ""} readOnly />
              </div>
            </div>
          </CardContent>
        </Card>

        <Separator />

        {/* Értesítések */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Bell className="h-5 w-5" />
              Értesítések
            </CardTitle>
            <CardDescription>
              Hogyan szeretne értesítéseket kapni az auditokról és eredményekről
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="notificationEmail">Értesítési email cím</Label>
              <Input 
                id="notificationEmail" 
                type="email" 
                placeholder="notifications@company.com" 
                disabled 
              />
            </div>
            <div className="space-y-3">
              <Label>Értesítési csatorna</Label>
              <RadioGroup defaultValue="email" disabled>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="email" id="notify-email" />
                  <Label htmlFor="notify-email" className="font-normal">Csak email</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="app" id="notify-app" />
                  <Label htmlFor="notify-app" className="font-normal">Csak alkalmazáson belül</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="both" id="notify-both" />
                  <Label htmlFor="notify-both" className="font-normal">Mindkettő</Label>
                </div>
              </RadioGroup>
            </div>
            <Button disabled>Értesítések mentése</Button>
          </CardContent>
        </Card>

        {/* Adatmegőrzés */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Database className="h-5 w-5" />
              Adatmegőrzés
            </CardTitle>
            <CardDescription>
              Audit adatok megőrzési időtartama
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="dataRetention">Megőrzési időtartam</Label>
              <Select disabled>
                <SelectTrigger id="dataRetention">
                  <SelectValue placeholder="Válasszon időtartamot" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="12">12 hónap</SelectItem>
                  <SelectItem value="24">24 hónap</SelectItem>
                  <SelectItem value="unlimited">Korlátlan</SelectItem>
                </SelectContent>
              </Select>
              <p className="text-sm text-muted-foreground">
                A kiválasztott időszaknál régebbi audit adatok automatikusan törlődnek
              </p>
            </div>
            <Button disabled>Megőrzés beállítása</Button>
          </CardContent>
        </Card>

        {/* Nyelvi beállítások */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Globe className="h-5 w-5" />
              Nyelvi beállítások
            </CardTitle>
            <CardDescription>
              A HR felület és kommunikáció nyelve
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="interfaceLanguage">Felület nyelve</Label>
              <Select disabled>
                <SelectTrigger id="interfaceLanguage">
                  <SelectValue placeholder="Magyar" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="hu">Magyar</SelectItem>
                  <SelectItem value="en">English</SelectItem>
                  <SelectItem value="de">Deutsch</SelectItem>
                  <SelectItem value="sk">Slovenčina</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <Button disabled>Nyelv mentése</Button>
          </CardContent>
        </Card>

        {/* Jogosultságkezelés */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5" />
              Jogosultságkezelés
            </CardTitle>
            <CardDescription>
              Több HR admin és szerepkörök kezelése
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label>HR adminisztrátorok</Label>
              <p className="text-sm text-muted-foreground">
                Itt jelennek meg a hozzáadott HR adminisztrátorok és a szerepköreik
              </p>
              <div className="border rounded-lg p-4 bg-muted/30">
                <p className="text-sm text-muted-foreground text-center py-4">
                  Még nincs további adminisztrátor hozzáadva
                </p>
              </div>
            </div>
            <Button disabled>+ Új adminisztrátor hozzáadása</Button>
          </CardContent>
        </Card>

        <Separator />

        {/* Email módosítás */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Mail className="h-5 w-5" />
              Bejelentkezési email módosítása
            </CardTitle>
            <CardDescription>
              A fiókhoz tartozó email cím megváltoztatása
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="currentEmail">Jelenlegi email</Label>
              <Input id="currentEmail" type="email" value={userEmail} readOnly />
            </div>
          </CardContent>
        </Card>


        {/* Számlázási adatok */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CreditCard className="h-5 w-5" />
              Számlázási adatok
            </CardTitle>
            <CardDescription>
              Cég számlázási és fizetési információi
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2 col-span-2">
                <Label htmlFor="selectedPackage">Kiválasztott csomag</Label>
                <Input 
                  id="selectedPackage" 
                  value={profile?.selected_package ? 
                    (profile.selected_package === 'starter' ? 'Starter' : 
                     profile.selected_package === 'pro' ? 'Pro' : 'Enterprise') : ''} 
                  readOnly 
                />
              </div>
              <div className="space-y-2 col-span-2">
                <Label htmlFor="billingCycle">Számlázási ciklus</Label>
                <Input 
                  id="billingCycle" 
                  value={profile?.billing_cycle === 'monthly' ? 'Havi' : 'Éves'} 
                  readOnly 
                />
              </div>
              <div className="space-y-2 col-span-2">
                <Label htmlFor="billingCompany">Cégnév (számlázási)</Label>
                <Input id="billingCompany" value={profile?.company_name || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label htmlFor="taxNumber">Adószám</Label>
                <Input id="taxNumber" value={profile?.vat_id || ""} readOnly />
              </div>
              <div className="space-y-2">
                <Label>Számlázási cím</Label>
                <Input 
                  value={profile?.billing_address_same_as_company ? 
                    "Megegyezik a székhellyel" : "Eltérő cím"} 
                  readOnly 
                />
              </div>
              {!profile?.billing_address_same_as_company && (
                <>
                  <div className="space-y-2 col-span-2">
                    <Label htmlFor="billingAddress">Számlázási cím (eltérő)</Label>
                    <Input id="billingAddress" value={profile?.billing_address || ""} readOnly />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="billingCity">Város</Label>
                    <Input id="billingCity" value={profile?.billing_city || ""} readOnly />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="billingPostalCode">Irányítószám</Label>
                    <Input id="billingPostalCode" value={profile?.billing_postal_code || ""} readOnly />
                  </div>
                </>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default Settings;
