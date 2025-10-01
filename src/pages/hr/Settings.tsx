import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AlertCircle, User, Building2, Lock, Mail, Bell, Database, Globe, Users, CreditCard } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Separator } from "@/components/ui/separator";

const Settings = () => {
  return (
    <div className="p-6 space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Beállítások</h1>
        <p className="text-muted-foreground mt-2">
          Fiók, cég adatok és rendszerbeállítások kezelése
        </p>
      </div>

      <Alert>
        <AlertCircle className="h-4 w-4" />
        <AlertDescription>
          <strong>Megjegyzés:</strong> Ez az oldal a regisztrációs folyamat elkészülte után lesz teljesen funkcionális.
          A regisztráció során megadott adatok itt lesznek szerkeszthetők.
        </AlertDescription>
      </Alert>

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
              <Input id="fullName" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="position">Pozíció</Label>
              <Input id="position" placeholder="Még nincs beállítva" disabled />
            </div>
            <Button disabled>Profil mentése</Button>
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
            <div className="space-y-2">
              <Label htmlFor="companyName">Cégnév</Label>
              <Input id="companyName" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="companySize">Létszám</Label>
              <Input id="companySize" type="number" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="industry">Iparág</Label>
              <Input id="industry" placeholder="Még nincs beállítva" disabled />
            </div>
            <Button disabled>Cég adatok mentése</Button>
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
              <Input id="currentEmail" type="email" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="newEmail">Új email</Label>
              <Input id="newEmail" type="email" placeholder="uj.email@example.com" disabled />
            </div>
            <Button disabled>Email módosítása</Button>
          </CardContent>
        </Card>

        {/* Jelszó módosítás */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Lock className="h-5 w-5" />
              Jelszó módosítása
            </CardTitle>
            <CardDescription>
              Bejelentkezési jelszó megváltoztatása
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="currentPassword">Jelenlegi jelszó</Label>
              <Input id="currentPassword" type="password" placeholder="••••••••" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="newPassword">Új jelszó</Label>
              <Input id="newPassword" type="password" placeholder="••••••••" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="confirmPassword">Új jelszó megerősítése</Label>
              <Input id="confirmPassword" type="password" placeholder="••••••••" disabled />
            </div>
            <Button disabled>Jelszó módosítása</Button>
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
            <div className="space-y-2">
              <Label htmlFor="billingCompany">Cégnév (számlázási)</Label>
              <Input id="billingCompany" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="taxNumber">Adószám</Label>
              <Input id="taxNumber" placeholder="12345678-1-23" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="billingAddress">Számlázási cím</Label>
              <Input id="billingAddress" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="billingEmail">Számlázási email</Label>
              <Input id="billingEmail" type="email" placeholder="szamlazas@company.com" disabled />
            </div>
            <Button disabled>Számlázási adatok mentése</Button>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default Settings;
