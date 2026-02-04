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

    // Map database values to UI values
    let mappedPackage = data.selected_package;
    if (data.selected_package === 'pro') {
      mappedPackage = 'professional';
    } else if (data.selected_package === 'start') {
      mappedPackage = 'starter';
    }

    setProfileData({ ...data, selected_package: mappedPackage });
    setSelectedPackageTemp(mappedPackage);
    setSelectedCycleTemp(data.billing_cycle);
    
    // Fill billing address from company address if empty
    if (!data.billing_address && data.address) {
      setProfileData({
        ...data,
        selected_package: mappedPackage,
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

    // Map UI values to database values
    let dbPackageName = selectedPackageTemp;
    if (selectedPackageTemp === 'professional') {
      dbPackageName = 'pro';
    } else if (selectedPackageTemp === 'starter') {
      dbPackageName = 'start';
    }

    setSavingPackage(true);
    const { error } = await supabase
      .from("profiles")
      .update({
        selected_package: dbPackageName,
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

      {/* BIZTONSÁG - Jelszó módosítás */}
      <div className="space-y-6 pt-20 md:pt-0">
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
            <Button onClick={handleChangePassword} variant="dark">
              Jelszó módosítása
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
            <AlertDialogAction onClick={handleSavePackage} className="bg-foreground text-background hover:bg-foreground/90">
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
