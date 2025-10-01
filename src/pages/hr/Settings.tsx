import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AlertCircle, User, Building2, Lock, Mail } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const Settings = () => {
  return (
    <div className="p-6 space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Beállítások</h1>
        <p className="text-muted-foreground mt-2">
          Fiók és cég adatok kezelése
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
              <Input id="companySize" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="industry">Iparág</Label>
              <Input id="industry" placeholder="Még nincs beállítva" disabled />
            </div>
          </CardContent>
        </Card>

        {/* Email módosítás */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Mail className="h-5 w-5" />
              Email cím módosítása
            </CardTitle>
            <CardDescription>
              Bejelentkezési email cím megváltoztatása
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="currentEmail">Jelenlegi email</Label>
              <Input id="currentEmail" type="email" placeholder="Még nincs beállítva" disabled />
            </div>
            <div className="space-y-2">
              <Label htmlFor="newEmail">Új email</Label>
              <Input id="newEmail" type="email" placeholder="Új email cím" disabled />
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
      </div>
    </div>
  );
};

export default Settings;
