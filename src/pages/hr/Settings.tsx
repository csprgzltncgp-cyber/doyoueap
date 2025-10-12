import { useEffect, useState } from "react";
import { useAuth } from "@/hooks/useAuth";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Checkbox } from "@/components/ui/checkbox";
import { toast } from "sonner";
import { Loader2, Plus, Trash2, Send, CreditCard, Link2, Check, Download, FileText } from "lucide-react";
import { Alert, AlertDescription } from "@/components/ui/alert";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";

interface ProfileData {
  id: string;
  email: string | null;
  full_name: string | null;
  company_name: string | null;
  country: string | null;
  vat_id: string | null;
  address: string | null;
  city: string | null;
  postal_code: string | null;
  industry: string | null;
  employee_count: string | null;
  contact_phone: string | null;
  billing_address_same_as_company: boolean | null;
  billing_address: string | null;
  billing_city: string | null;
  billing_postal_code: string | null;
  selected_package: string | null;
  billing_cycle: string | null;
  data_retention_days: number | null;
  preferred_languages: string[] | null;
}

interface NotificationEmail {
  id: string;
  email: string;
  is_verified: boolean;
}

interface CompanyUser {
  user_id: string;
  email: string;
  full_name: string;
  role: string;
}

const WORLD_LANGUAGES = [
  { code: "HU", name: "Magyar" },
  { code: "EN", name: "English" },
  { code: "DE", name: "Deutsch" },
  { code: "FR", name: "Français" },
  { code: "ES", name: "Español" },
  { code: "IT", name: "Italiano" },
  { code: "PT", name: "Português" },
  { code: "RO", name: "Română" },
  { code: "PL", name: "Polski" },
  { code: "NL", name: "Nederlands" },
  { code: "SV", name: "Svenska" },
  { code: "DA", name: "Dansk" },
  { code: "FI", name: "Suomi" },
  { code: "NO", name: "Norsk" },
  { code: "CS", name: "Čeština" },
  { code: "SK", name: "Slovenčina" },
  { code: "BG", name: "Български" },
  { code: "HR", name: "Hrvatski" },
  { code: "EL", name: "Ελληνικά" },
  { code: "ZH", name: "中文" },
  { code: "JA", name: "日本語" },
  { code: "KO", name: "한국어" },
  { code: "AR", name: "العربية" },
  { code: "RU", name: "Русский" },
  { code: "TR", name: "Türkçe" },
];

function Settings() {
  const { user } = useAuth();
  const [loading, setLoading] = useState(true);
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingCompany, setSavingCompany] = useState(false);
  const [savingBilling, setSavingBilling] = useState(false);
  const [savingPackage, setSavingPackage] = useState(false);
  const [profileData, setProfileData] = useState<ProfileData | null>(null);
  const [notificationEmails, setNotificationEmails] = useState<NotificationEmail[]>([]);
  const [newNotificationEmail, setNewNotificationEmail] = useState("");
  const [companyUsers, setCompanyUsers] = useState<CompanyUser[]>([]);
  const [newUserEmail, setNewUserEmail] = useState("");
  const [newUserRole, setNewUserRole] = useState("hr");
  const [oldPassword, setOldPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [useStripeLink, setUseStripeLink] = useState(false);
  const [paymentMethods, setPaymentMethods] = useState([
    { id: "1", type: "visa", last4: "4242", isDefault: true },
  ]);
  const [selectedPackageTemp, setSelectedPackageTemp] = useState<string | null>(null);
  const [selectedCycleTemp, setSelectedCycleTemp] = useState<string | null>(null);
  const [showPackageConfirm, setShowPackageConfirm] = useState(false);
  
  // Dummy invoices data
  const [invoices] = useState([
    {
      id: "INV-2025-001",
      date: "2025-01-01",
      amount: "3 990 €",
      status: "paid",
      period: "2025.01.01 - 2025.12.31",
      package: "Pro - Éves"
    },
    {
      id: "INV-2024-012",
      date: "2024-12-01",
      amount: "399 €",
      status: "paid",
      period: "2024.12.01 - 2024.12.31",
      package: "Pro - Havi"
    },
    {
      id: "INV-2024-011",
      date: "2024-11-01",
      amount: "399 €",
      status: "paid",
      period: "2024.11.01 - 2024.11.30",
      package: "Pro - Havi"
    },
  ]);


  useEffect(() => {
    if (user) {
      fetchAllData();
    }
  }, [user]);

  const fetchAllData = async () => {
    setLoading(true);
    try {
      await fetchProfile();
      await Promise.all([
        fetchNotificationEmails(),
        fetchCompanyUsers()
      ]);
    } catch (err) {
      console.error("Error in fetchAllData:", err);
    } finally {
      setLoading(false);
    }
  };

  const fetchProfile = async () => {
    if (!user) return;

    const { data, error } = await supabase
      .from("profiles")
      .select("*")
      .eq("id", user.id)
      .maybeSingle();

    if (error) {
      toast.error("Hiba a profil betöltésekor");
      console.error(error);
      return;
    }

    if (!data) {
      toast.error("Nincs profiladat");
      return;
    }

    setProfileData(data);
    setSelectedPackageTemp(data.selected_package);
    setSelectedCycleTemp(data.billing_cycle);
    
    // Fill billing address from company address if empty
    if (!data.billing_address && data.address) {
      setProfileData({
        ...data,
        billing_address: data.address,
        billing_city: data.city,
        billing_postal_code: data.postal_code,
      });
    }
  };

  const fetchNotificationEmails = async () => {
    if (!user) return;

    const { data, error } = await supabase
      .from("notification_emails")
      .select("*")
      .eq("user_id", user.id);

    if (error) {
      console.error(error);
      return;
    }

    setNotificationEmails(data || []);
  };

  const fetchCompanyUsers = async () => {
    if (!user || !profileData?.company_name) return;

    const { data, error } = await supabase
      .rpc("get_company_users", { company_name_param: profileData.company_name });

    if (error) {
      console.error(error);
      return;
    }

    setCompanyUsers(data || []);
  };

  const handleSaveProfile = async () => {
    if (!user || !profileData) return;

    setSavingProfile(true);
    const { error } = await supabase
      .from("profiles")
      .update({
        full_name: profileData.full_name,
        contact_phone: profileData.contact_phone,
      })
      .eq("id", user.id);

    if (error) {
      toast.error("Hiba a profil mentésekor");
      console.error(error);
    } else {
      toast.success("Sikeres mentés", {
        description: "A profil adatok sikeresen mentésre kerültek.",
      });
    }
    setSavingProfile(false);
  };

  const handleSaveCompanyData = async () => {
    if (!user || !profileData) return;

    setSavingCompany(true);
    const { error } = await supabase
      .from("profiles")
      .update({
        company_name: profileData.company_name,
        country: profileData.country,
        vat_id: profileData.vat_id,
        address: profileData.address,
        city: profileData.city,
        postal_code: profileData.postal_code,
        industry: profileData.industry,
        employee_count: profileData.employee_count,
      })
      .eq("id", user.id);

    if (error) {
      toast.error("Hiba a cégadatok mentésekor");
      console.error(error);
    } else {
      toast.success("Sikeres mentés", {
        description: "A cégadatok sikeresen mentésre kerültek.",
      });
    }
    setSavingCompany(false);
  };

  const handleAddNotificationEmail = async () => {
    if (!user || !newNotificationEmail) return;

    const { error } = await supabase
      .from("notification_emails")
      .insert({
        user_id: user.id,
        email: newNotificationEmail,
      });

    if (error) {
      toast.error("Hiba az email hozzáadásakor");
      console.error(error);
      return;
    }

    toast.success("Értesítési email hozzáadva");
    setNewNotificationEmail("");
    fetchNotificationEmails();
  };

  const handleDeleteNotificationEmail = async (id: string) => {
    const { error } = await supabase
      .from("notification_emails")
      .delete()
      .eq("id", id);

    if (error) {
      toast.error("Hiba az email törlésekor");
      console.error(error);
      return;
    }

    toast.success("Értesítési email törölve");
    fetchNotificationEmails();
  };

  const handleSaveDataRetention = async () => {
    if (!user || !newUserEmail || !profileData?.company_name) return;

    const token = Math.random().toString(36).substring(2, 15);
    const expiresAt = new Date();
    expiresAt.setDate(expiresAt.getDate() + 7);

    const { error } = await supabase
      .from("user_invitations")
      .insert({
        invited_by: user.id,
        company_id: user.id,
        email: newUserEmail,
        role: newUserRole,
        token: token,
        expires_at: expiresAt.toISOString(),
      });

    if (error) {
      toast.error("Hiba a meghívás küldésekor");
      console.error(error);
      return;
    }

    toast.success("Meghívó elküldve");
    setNewUserEmail("");
  };

  const handleChangePassword = async () => {
    if (!oldPassword) {
      toast.error("Add meg a jelenlegi jelszavad");
      return;
    }
    
    if (newPassword !== confirmPassword) {
      toast.error("Az új jelszavak nem egyeznek");
      return;
    }

    if (newPassword.length < 6) {
      toast.error("Az új jelszónak legalább 6 karakter hosszúnak kell lennie");
      return;
    }

    // First verify old password by trying to sign in
    const { error: signInError } = await supabase.auth.signInWithPassword({
      email: profileData?.email || "",
      password: oldPassword,
    });

    if (signInError) {
      toast.error("Hibás jelenlegi jelszó");
      return;
    }

    // If old password is correct, update to new password
    const { error } = await supabase.auth.updateUser({
      password: newPassword
    });

    if (error) {
      toast.error("Hiba a jelszó módosításakor");
      console.error(error);
      return;
    }

    toast.success("Jelszó sikeresen módosítva");
    setOldPassword("");
    setNewPassword("");
    setConfirmPassword("");
  };

  const handleSaveBillingData = async () => {
    if (!user || !profileData) return;

    setSavingBilling(true);
    const { error } = await supabase
      .from("profiles")
      .update({
        billing_address: profileData.billing_address,
        billing_city: profileData.billing_city,
        billing_postal_code: profileData.billing_postal_code,
      })
      .eq("id", user.id);

    if (error) {
      toast.error("Hiba a számlázási adatok mentésekor");
      console.error(error);
    } else {
      toast.success("Sikeres mentés", {
        description: "A számlázási adatok sikeresen mentésre kerültek.",
      });
    }
    setSavingBilling(false);
  };

  const handleSavePackage = async () => {
    if (!user || !selectedPackageTemp) return;

    setSavingPackage(true);
    const { error } = await supabase
      .from("profiles")
      .update({
        selected_package: selectedPackageTemp,
        billing_cycle: selectedCycleTemp,
      })
      .eq("id", user.id);

    if (error) {
      toast.error("Hiba a csomag mentésekor");
      console.error(error);
    } else {
      toast.success("Csomag sikeresen módosítva");
      setProfileData({
        ...profileData!,
        selected_package: selectedPackageTemp,
        billing_cycle: selectedCycleTemp || "yearly",
      });
      setShowPackageConfirm(false);
    }
    setSavingPackage(false);
  };

  const handlePackageClick = (pkg: string) => {
    if (pkg !== profileData?.selected_package) {
      setSelectedPackageTemp(pkg);
      setShowPackageConfirm(true);
    }
  };

  const handleCycleChange = (cycle: string) => {
    if (cycle !== profileData?.billing_cycle) {
      setSelectedCycleTemp(cycle);
      setShowPackageConfirm(true);
    }
  };

  const handleCancelPackageChange = () => {
    setSelectedPackageTemp(profileData?.selected_package || null);
    setSelectedCycleTemp(profileData?.billing_cycle || null);
    setShowPackageConfirm(false);
  };

  const handleAddPaymentMethod = () => {
    const newId = String(paymentMethods.length + 1);
    setPaymentMethods([
      ...paymentMethods.map(pm => ({ ...pm, isDefault: false })),
      { id: newId, type: "visa", last4: "0000", isDefault: true }
    ]);
    toast.success("Új fizetési mód hozzáadva");
  };

  const handleSetDefaultPayment = (id: string) => {
    setPaymentMethods(paymentMethods.map(pm => ({
      ...pm,
      isDefault: pm.id === id
    })));
    toast.success("Alapértelmezett fizetési mód beállítva");
  };



  const handleDownloadInvoice = (invoiceId: string) => {
    toast.success(`Számla letöltése: ${invoiceId}`);
    // In real app, this would download the PDF
  };

  const handleDeletePaymentMethod = (id: string) => {
    if (paymentMethods.length === 1) {
      toast.error("Legalább egy fizetési módnak lennie kell");
      return;
    }
    setPaymentMethods(paymentMethods.filter(pm => pm.id !== id));
    toast.success("Fizetési mód törölve");
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <Loader2 className="h-8 w-8 animate-spin" />
      </div>
    );
  }

  if (!profileData) {
    return (
      <Alert>
        <AlertDescription>Nincs elérhető profiladat</AlertDescription>
      </Alert>
    );
  }

  return (
    <div className="space-y-8">
      <h2 className="text-2xl font-bold">Beállítások</h2>

      {/* SZEMÉLYES ÉS CÉGES INFORMÁCIÓK */}
    <div className="space-y-6 pt-20 md:pt-0">
        <h2 className="text-xl font-semibold text-muted-foreground">Személyes és céges információk</h2>

      {/* Profil adatok */}
      <Card>
        <CardHeader>
          <CardTitle>Profil adatok</CardTitle>
          <CardDescription>Személyes adatok szerkesztése</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label>Email (nem módosítható)</Label>
            <Input value={profileData.email || ""} disabled />
          </div>
          <div>
            <Label>Teljes név</Label>
            <Input 
              value={profileData.full_name || ""} 
              onChange={(e) => setProfileData({...profileData, full_name: e.target.value})}
            />
          </div>
          <div>
            <Label>Telefonszám</Label>
            <Input 
              value={profileData.contact_phone || ""} 
              onChange={(e) => setProfileData({...profileData, contact_phone: e.target.value})}
            />
          </div>
          <Button onClick={handleSaveProfile} disabled={savingProfile}>
            {savingProfile && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            Mentés
          </Button>
        </CardContent>
      </Card>

      {/* Cégadatok */}
      <Card>
        <CardHeader>
          <CardTitle>Cégadatok</CardTitle>
          <CardDescription>Cég információk szerkesztése</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <Label>Cégnév</Label>
              <Input 
                value={profileData.company_name || ""} 
                onChange={(e) => setProfileData({...profileData, company_name: e.target.value})}
              />
            </div>
            <div>
              <Label>Ország</Label>
              <Input 
                value={profileData.country || ""} 
                onChange={(e) => setProfileData({...profileData, country: e.target.value})}
              />
            </div>
            <div>
              <Label>Adószám</Label>
              <Input 
                value={profileData.vat_id || ""} 
                onChange={(e) => setProfileData({...profileData, vat_id: e.target.value})}
              />
            </div>
            <div>
              <Label>Iparág</Label>
              <Input 
                value={profileData.industry || ""} 
                onChange={(e) => setProfileData({...profileData, industry: e.target.value})}
              />
            </div>
            <div>
              <Label>Munkavállalói létszám</Label>
              <Input 
                type="number"
                value={profileData.employee_count || ""} 
                onChange={(e) => setProfileData({...profileData, employee_count: e.target.value})}
              />
            </div>
            <div>
              <Label>Cím</Label>
              <Input 
                value={profileData.address || ""} 
                onChange={(e) => setProfileData({...profileData, address: e.target.value})}
              />
            </div>
            <div>
              <Label>Város</Label>
              <Input 
                value={profileData.city || ""} 
                onChange={(e) => setProfileData({...profileData, city: e.target.value})}
              />
            </div>
            <div>
              <Label>Irányítószám</Label>
              <Input 
                value={profileData.postal_code || ""} 
                onChange={(e) => setProfileData({...profileData, postal_code: e.target.value})}
              />
            </div>
          </div>
          <Button onClick={handleSaveCompanyData} disabled={savingCompany}>
            {savingCompany && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            Mentés
          </Button>
        </CardContent>
      </Card>
      </div>

      {/* ELŐFIZETÉS ÉS PÉNZÜGYEK */}
      <div className="space-y-6">
        <h2 className="text-xl font-semibold text-muted-foreground">Előfizetés és pénzügyek</h2>
      
      {/* Előfizetési csomag - ez lesz az első a pénzügyek között */}

      {/* Fizetési mód */}
      <Card>
        <CardHeader>
          <CardTitle>Fizetési mód</CardTitle>
          <CardDescription>Fizetési módok kezelése</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {/* Stripe Link option */}
          <div className="flex items-center justify-between p-4 border rounded-lg">
            <div className="flex items-center gap-3">
              <Link2 className="h-5 w-5 text-primary" />
              <div>
                <p className="font-medium">Link fiók</p>
                <p className="text-sm text-muted-foreground">
                  {useStripeLink ? "example@email.com" : "Gyors fizetés Link fiókkal"}
                </p>
              </div>
            </div>
            <Button 
              variant={useStripeLink ? "default" : "outline"}
              onClick={() => setUseStripeLink(!useStripeLink)}
              size="sm"
            >
              {useStripeLink ? 'Használatban' : 'Váltás Link-re'}
            </Button>
          </div>

          {!useStripeLink && (
            <>
              <div className="space-y-2">
                <Label>Mentett bankkártyák</Label>
                {paymentMethods.map((method) => (
                  <div key={method.id} className="flex items-center justify-between p-3 border rounded-lg">
                    <div className="flex items-center gap-3">
                      <CreditCard className="h-5 w-5" />
                      <div>
                        <p className="font-medium">
                          {method.type === "visa" ? "Visa" : method.type === "mastercard" ? "Mastercard" : "Kártya"} •••• {method.last4}
                        </p>
                        {method.isDefault && (
                          <p className="text-sm text-muted-foreground flex items-center gap-1">
                            <Check className="h-3 w-3" /> Alapértelmezett
                          </p>
                        )}
                      </div>
                    </div>
                    <div className="flex gap-2">
                      {!method.isDefault && (
                        <Button 
                          variant="outline" 
                          size="sm"
                          onClick={() => handleSetDefaultPayment(method.id)}
                        >
                          Alapértelmezés
                        </Button>
                      )}
                      <Button 
                        variant="destructive" 
                        size="sm"
                        onClick={() => handleDeletePaymentMethod(method.id)}
                      >
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
              <Button onClick={handleAddPaymentMethod} variant="outline" className="w-full">
                <Plus className="h-4 w-4 mr-2" />
                Új bankkártya hozzáadása
              </Button>
            </>
          )}
        </CardContent>
      </Card>

      {/* Számlázási adatok */}
      <Card>
        <CardHeader>
          <CardTitle>Számlázási adatok</CardTitle>
          <CardDescription>Számlázási cím információk</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <Label>Cégnév (számlázáshoz)</Label>
              <Input 
                value={profileData.company_name || ""} 
                onChange={(e) => setProfileData({...profileData, company_name: e.target.value})}
              />
            </div>
            <div>
              <Label>Adószám / VAT ID</Label>
              <Input 
                value={profileData.vat_id || ""} 
                onChange={(e) => setProfileData({...profileData, vat_id: e.target.value})}
              />
            </div>
            <div className="col-span-2">
              <Label>Számlázási cím</Label>
              <Input 
                value={profileData.billing_address || ""} 
                onChange={(e) => setProfileData({...profileData, billing_address: e.target.value})}
                placeholder="Utca, házszám"
              />
            </div>
            <div>
              <Label>Város</Label>
              <Input 
                value={profileData.billing_city || ""} 
                onChange={(e) => setProfileData({...profileData, billing_city: e.target.value})}
              />
            </div>
            <div>
              <Label>Irányítószám</Label>
              <Input 
                value={profileData.billing_postal_code || ""} 
                onChange={(e) => setProfileData({...profileData, billing_postal_code: e.target.value})}
              />
            </div>
            <div>
              <Label>Ország</Label>
              <Input 
                value={profileData.country || ""} 
                onChange={(e) => setProfileData({...profileData, country: e.target.value})}
              />
            </div>
          </div>
          <Button onClick={handleSaveBillingData} disabled={savingBilling}>
            {savingBilling && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            Mentés
          </Button>
        </CardContent>
      </Card>

      {/* Csomag és számlázási ciklus */}
      <Card>
        <CardHeader>
          <CardTitle>Előfizetési csomag</CardTitle>
          <CardDescription>Csomag váltása és számlázási ciklus kezelése</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {/* Current package - saved state */}
          <div className={`p-4 border-2 rounded-lg ${
            showPackageConfirm ? "border-amber-500 bg-amber-50 dark:bg-amber-950/20" : "border-primary bg-primary/5"
          }`}>
            <div className="flex items-center justify-between mb-2">
              <h3 className="text-lg font-semibold">
                {showPackageConfirm ? "Váltás előtti csomag: " : "Jelenlegi csomag: "}{
                  profileData.selected_package === "starter" ? "Starter" :
                  profileData.selected_package === "pro" ? "Pro" :
                  profileData.selected_package === "enterprise" ? "Enterprise" :
                  profileData.selected_package === "partner" ? "Partner Program" : "Nincs kiválasztva"
                }
              </h3>
              <span className="text-lg font-bold">
                {profileData.selected_package === "starter" && profileData.billing_cycle === "monthly" && "149 €/hó"}
                {profileData.selected_package === "starter" && profileData.billing_cycle === "yearly" && "1 490 €/év"}
                {profileData.selected_package === "pro" && profileData.billing_cycle === "monthly" && "399 €/hó"}
                {profileData.selected_package === "pro" && profileData.billing_cycle === "yearly" && "3 990 €/év"}
                {profileData.selected_package === "enterprise" && "Egyedi"}
                {profileData.selected_package === "partner" && "Egyedi"}
              </span>
            </div>
            <p className="text-sm text-muted-foreground mb-3">
              Számlázási ciklus: {profileData.billing_cycle === "monthly" ? "Havi" : "Éves"}
            </p>
            <ul className="text-sm space-y-1">
              {profileData.selected_package === "starter" && (
                <>
                  <li>✓ Évente max. 1 EAP Pulse felmérés</li>
                  <li>✓ Alap KPI-k (Awareness, Trust, Usage, Impact)</li>
                  <li>✓ PDF export</li>
                  <li>✓ Email support</li>
                </>
              )}
              {profileData.selected_package === "pro" && (
                <>
                  <li>✓ Negyedévente EAP Pulse felmérés (ismétlés funkcióval)</li>
                  <li>✓ Demográfiai bontás, trendek, összehasonlítás</li>
                  <li>✓ PDF + Excel export</li>
                  <li>✓ Brandelhető User App (logó + 4 szín)</li>
                  <li>✓ Kommunikációs támogatás (sablonok, QR generátor)</li>
                  <li>✓ Email + chat support</li>
                </>
              )}
              {profileData.selected_package === "enterprise" && (
                <>
                  <li>✓ Korlátlan EAP Pulse felmérés</li>
                  <li>✓ Teljes funkcionalitás (KPI-k, trendek, benchmark, motivációk)</li>
                  <li>✓ PDF, Excel, PowerPoint export</li>
                  <li>✓ Teljes brandelés + saját domain opció</li>
                  <li>✓ SSO integráció, jogosultságkezelés</li>
                  <li>✓ Dedikált account manager, SLA</li>
                </>
              )}
              {profileData.selected_package === "partner" && (
                <>
                  <li>✓ Korlátlan EAP Pulse felmérés</li>
                  <li>✓ Teljes funkcionalitás (KPI-k, trendek, benchmark, motivációk)</li>
                  <li>✓ PDF, Excel, PowerPoint export</li>
                  <li>✓ Teljes brandelés + saját domain opció</li>
                  <li>✓ SSO integráció, jogosultságkezelés</li>
                  <li>✓ Dedikált account manager, SLA</li>
                </>
              )}
            </ul>
          </div>

          {/* Billing cycle selector */}
          <div>
            <Label>Számlázási ciklus</Label>
            <Select 
              value={selectedCycleTemp || "yearly"}
              onValueChange={handleCycleChange}
            >
              <SelectTrigger className={showPackageConfirm && selectedCycleTemp !== profileData.billing_cycle ? "border-amber-500" : ""}>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="monthly">Havi</SelectItem>
                <SelectItem value="yearly">Éves (2 hónap megtakarítás)</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {/* Available packages */}
          <div className="space-y-3">
            <Label>Elérhető csomagok</Label>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              {/* Starter */}
              <div 
                className={`p-4 border-2 rounded-lg cursor-pointer transition-all ${
                  selectedPackageTemp === "starter" 
                    ? "border-primary bg-primary/10 ring-2 ring-primary" 
                    : profileData.selected_package === "starter"
                    ? "border-green-500 bg-green-50 dark:bg-green-950/20"
                    : "hover:border-primary/50"
                }`}
                onClick={() => handlePackageClick("starter")}
              >
                <div className="flex items-center justify-between mb-1">
                  <h4 className="font-semibold">Starter</h4>
                  {profileData.selected_package === "starter" && selectedPackageTemp !== "starter" && (
                    <span className="text-xs bg-green-500 text-white px-2 py-1 rounded">Aktív</span>
                  )}
                  {selectedPackageTemp === "starter" && selectedPackageTemp !== profileData.selected_package && (
                    <span className="text-xs bg-amber-500 text-white px-2 py-1 rounded">Kiválasztva</span>
                  )}
                </div>
                <p className="text-2xl font-bold mb-2">
                  {(selectedCycleTemp || profileData.billing_cycle) === "monthly" ? "149 €" : "1 490 €"}
                  <span className="text-sm font-normal text-muted-foreground">
                    /{(selectedCycleTemp || profileData.billing_cycle) === "monthly" ? "hó" : "év"}
                  </span>
                </p>
                <ul className="text-xs space-y-1">
                  <li>✓ Évente max. 1 EAP Pulse felmérés</li>
                  <li>✓ Alap KPI-k</li>
                  <li>✓ PDF export</li>
                  <li>✓ Email support</li>
                </ul>
              </div>

              {/* Pro */}
              <div 
                className={`p-4 border-2 rounded-lg cursor-pointer transition-all ${
                  selectedPackageTemp === "pro" 
                    ? "border-primary bg-primary/10 ring-2 ring-primary" 
                    : profileData.selected_package === "pro"
                    ? "border-green-500 bg-green-50 dark:bg-green-950/20"
                    : "hover:border-primary/50"
                }`}
                onClick={() => handlePackageClick("pro")}
              >
                <div className="flex items-center justify-between mb-1">
                  <h4 className="font-semibold">Pro</h4>
                  {profileData.selected_package === "pro" && selectedPackageTemp !== "pro" && (
                    <span className="text-xs bg-green-500 text-white px-2 py-1 rounded">Aktív</span>
                  )}
                  {selectedPackageTemp === "pro" && selectedPackageTemp !== profileData.selected_package && (
                    <span className="text-xs bg-amber-500 text-white px-2 py-1 rounded">Kiválasztva</span>
                  )}
                </div>
                <p className="text-2xl font-bold mb-2">
                  {(selectedCycleTemp || profileData.billing_cycle) === "monthly" ? "399 €" : "3 990 €"}
                  <span className="text-sm font-normal text-muted-foreground">
                    /{(selectedCycleTemp || profileData.billing_cycle) === "monthly" ? "hó" : "év"}
                  </span>
                </p>
                <ul className="text-xs space-y-1">
                  <li>✓ Negyedévente EAP Pulse felmérés</li>
                  <li>✓ Demográfia, trendek</li>
                  <li>✓ PDF + Excel export</li>
                  <li>✓ Brandelhető app</li>
                  <li>✓ Email + chat support</li>
                </ul>
              </div>

              {/* Enterprise */}
              <div 
                className={`p-4 border-2 rounded-lg cursor-pointer transition-all ${
                  selectedPackageTemp === "enterprise" 
                    ? "border-primary bg-primary/10 ring-2 ring-primary" 
                    : profileData.selected_package === "enterprise"
                    ? "border-green-500 bg-green-50 dark:bg-green-950/20"
                    : "hover:border-primary/50"
                }`}
                onClick={() => handlePackageClick("enterprise")}
              >
                <div className="flex items-center justify-between mb-1">
                  <h4 className="font-semibold">Enterprise</h4>
                  {profileData.selected_package === "enterprise" && selectedPackageTemp !== "enterprise" && (
                    <span className="text-xs bg-green-500 text-white px-2 py-1 rounded">Aktív</span>
                  )}
                  {selectedPackageTemp === "enterprise" && selectedPackageTemp !== profileData.selected_package && (
                    <span className="text-xs bg-amber-500 text-white px-2 py-1 rounded">Kiválasztva</span>
                  )}
                </div>
                <p className="text-2xl font-bold mb-2">
                  Egyedi
                </p>
                <ul className="text-xs space-y-1">
                  <li>✓ Korlátlan EAP Pulse felmérés</li>
                  <li>✓ Teljes funkcionalitás</li>
                  <li>✓ PDF, Excel, PPT export</li>
                  <li>✓ Teljes brandelés</li>
                  <li>✓ SSO, dedikált manager</li>
                </ul>
              </div>

              {/* Partner Program */}
              <div 
                className={`p-4 border-2 rounded-lg cursor-pointer transition-all ${
                  selectedPackageTemp === "partner" 
                    ? "border-primary bg-primary/10 ring-2 ring-primary" 
                    : profileData.selected_package === "partner"
                    ? "border-green-500 bg-green-50 dark:bg-green-950/20"
                    : "hover:border-primary/50"
                }`}
                onClick={() => handlePackageClick("partner")}
              >
                <div className="flex items-center justify-between mb-1">
                  <h4 className="font-semibold">Partner Program</h4>
                  {profileData.selected_package === "partner" && selectedPackageTemp !== "partner" && (
                    <span className="text-xs bg-green-500 text-white px-2 py-1 rounded">Aktív</span>
                  )}
                  {selectedPackageTemp === "partner" && selectedPackageTemp !== profileData.selected_package && (
                    <span className="text-xs bg-amber-500 text-white px-2 py-1 rounded">Kiválasztva</span>
                  )}
                </div>
                <p className="text-2xl font-bold mb-2">
                  Egyedi
                </p>
                <ul className="text-xs space-y-1">
                  <li>✓ Korlátlan EAP Pulse felmérés</li>
                  <li>✓ Teljes funkcionalitás</li>
                  <li>✓ PDF, Excel, PPT export</li>
                  <li>✓ Teljes brandelés</li>
                  <li>✓ SSO, dedikált manager</li>
                </ul>
              </div>
            </div>
          </div>

          {showPackageConfirm ? (
            <div className="flex gap-2">
              <Button onClick={handleCancelPackageChange} variant="outline" size="lg" className="flex-1">
                Mégse
              </Button>
              <Button onClick={handleSavePackage} disabled={savingPackage} size="lg" className="flex-1">
                {savingPackage && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Módosítás megerősítése
              </Button>
            </div>
          ) : (
            <p className="text-sm text-muted-foreground text-center">
              Kattints egy másik csomagra a váltáshoz
            </p>
          )}
        </CardContent>
      </Card>

      {/* Számlák */}
      <Card>
        <CardHeader>
          <CardTitle>Számlák</CardTitle>
          <CardDescription>Korábbi számlák megtekintése és letöltése</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {invoices.map((invoice) => (
              <div 
                key={invoice.id} 
                className="flex items-center justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <FileText className="h-5 w-5 text-muted-foreground" />
                  <div>
                    <p className="font-medium">{invoice.id}</p>
                    <p className="text-sm text-muted-foreground">
                      {invoice.package} · {invoice.period}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      Kiállítva: {new Date(invoice.date).toLocaleDateString('hu-HU')}
                    </p>
                  </div>
                </div>
                <div className="flex items-center gap-4">
                  <div className="text-right">
                    <p className="font-bold">{invoice.amount}</p>
                    <span className={`text-xs px-2 py-1 rounded ${
                      invoice.status === 'paid' 
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                    }`}>
                      {invoice.status === 'paid' ? 'Fizetve' : 'Függőben'}
                    </span>
                  </div>
                  <Button 
                    variant="outline" 
                    size="sm"
                    onClick={() => handleDownloadInvoice(invoice.id)}
                  >
                    <Download className="h-4 w-4 mr-2" />
                    Letöltés
                  </Button>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
      </div>

      {/* BIZTONSÁG */}
      <div className="space-y-6">
        <h2 className="text-xl font-semibold text-muted-foreground">Biztonság</h2>

      {/* Jelszó módosítás */}
      <Card>
        <CardHeader>
          <CardTitle>Jelszó módosítás</CardTitle>
          <CardDescription>Bejelentkezési jelszó megváltoztatása</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label>Jelenlegi jelszó</Label>
            <Input 
              type="password"
              value={oldPassword}
              onChange={(e) => setOldPassword(e.target.value)}
              placeholder="Add meg a jelenlegi jelszavad"
            />
          </div>
          <div>
            <Label>Új jelszó</Label>
            <Input 
              type="password"
              value={newPassword}
              onChange={(e) => setNewPassword(e.target.value)}
              placeholder="Legalább 6 karakter"
            />
          </div>
          <div>
            <Label>Új jelszó megerősítése</Label>
            <Input 
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              placeholder="Írd be újra az új jelszót"
            />
          </div>
          <Button onClick={handleChangePassword}>
            Jelszó módosítása
          </Button>
        </CardContent>
      </Card>
      </div>

      {/* PREFERENCIÁK */}
      <div className="space-y-6">
        <h2 className="text-xl font-semibold text-muted-foreground">Preferenciák és beállítások</h2>

      {/* Értesítési emailek */}
      <Card>
        <CardHeader>
          <CardTitle>Értesítési email címek</CardTitle>
          <CardDescription>Többféle email címre is kaphatsz értesítéseket</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {notificationEmails.map((email) => (
            <div key={email.id} className="flex items-center justify-between">
              <span>{email.email}</span>
              <Button 
                variant="destructive" 
                size="sm"
                onClick={() => handleDeleteNotificationEmail(email.id)}
              >
                <Trash2 className="h-4 w-4" />
              </Button>
            </div>
          ))}
          <div className="flex gap-2">
            <Input 
              type="email"
              placeholder="Új email cím"
              value={newNotificationEmail}
              onChange={(e) => setNewNotificationEmail(e.target.value)}
            />
            <Button onClick={handleAddNotificationEmail}>
              <Plus className="h-4 w-4" />
            </Button>
          </div>
        </CardContent>
      </Card>


      {/* Adatmegőrzés */}
      <Card>
        <CardHeader>
          <CardTitle>Adatmegőrzés</CardTitle>
          <CardDescription>Adatok megőrzési idejének beállítása</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label>Megőrzési idő (napokban)</Label>
            <Select 
              value={String(profileData.data_retention_days || 365)}
              onValueChange={(value) => setProfileData({...profileData, data_retention_days: parseInt(value)})}
            >
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="30">30 nap</SelectItem>
                <SelectItem value="90">90 nap</SelectItem>
                <SelectItem value="180">180 nap</SelectItem>
                <SelectItem value="365">1 év</SelectItem>
                <SelectItem value="730">2 év</SelectItem>
                <SelectItem value="1825">5 év</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <Button onClick={handleSaveDataRetention} disabled={false}>
            Mentés
          </Button>
        </CardContent>
      </Card>
      </div>

      {/* Confirmation Dialog */}
      <AlertDialog open={showPackageConfirm} onOpenChange={setShowPackageConfirm}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Csomag módosítása</AlertDialogTitle>
            <AlertDialogDescription>
              Biztosan át szeretnél váltani a{" "}
              <strong>
                {selectedPackageTemp === "starter" ? "Starter" : selectedPackageTemp === "pro" ? "Pro" : "Enterprise"}
              </strong>{" "}
              csomagra{" "}
              <strong>{selectedCycleTemp === "monthly" ? "havi" : "éves"}</strong> számlázással?
              <br /><br />
              Az új ár:{" "}
              <strong>
                {selectedPackageTemp === "starter" && selectedCycleTemp === "monthly" && "149 €/hó"}
                {selectedPackageTemp === "starter" && selectedCycleTemp === "yearly" && "1 490 €/év"}
                {selectedPackageTemp === "pro" && selectedCycleTemp === "monthly" && "399 €/hó"}
                {selectedPackageTemp === "pro" && selectedCycleTemp === "yearly" && "3 990 €/év"}
                {selectedPackageTemp === "enterprise" && "Egyedi ajánlat"}
              </strong>
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={handleCancelPackageChange}>Mégse</AlertDialogCancel>
            <AlertDialogAction onClick={handleSavePackage}>
              {savingPackage ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : null}
              Módosítás megerősítése
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}

export default Settings;
