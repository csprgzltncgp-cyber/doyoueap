import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Plus, Edit, Trash2 } from "lucide-react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

interface Company {
  id: string;
  company_name: string;
  email: string;
  contact_phone: string | null;
  industry: string | null;
  employee_count: string | null;
  selected_package: string | null;
  created_at: string;
  full_name: string | null;
}

export default function CompanyManagement() {
  const [companies, setCompanies] = useState<Company[]>([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [editingCompany, setEditingCompany] = useState<Company | null>(null);
  const [formData, setFormData] = useState({
    company_name: "",
    contact_email: "",
    contact_phone: "",
    industry: "",
    employee_count: "",
    subscription_status: "active",
    subscription_package: "",
    subscription_start_date: "",
    subscription_end_date: "",
    notes: "",
  });

  useEffect(() => {
    fetchCompanies();
  }, []);

  const fetchCompanies = async () => {
    try {
      const { data, error } = await supabase
        .from("profiles")
        .select("id, company_name, email, contact_phone, industry, employee_count, selected_package, created_at, full_name")
        .not("company_name", "is", null)
        .order("created_at", { ascending: false });

      if (error) throw error;
      setCompanies(data || []);
    } catch (error) {
      console.error("Error fetching companies:", error);
      toast.error("Hiba a cégek betöltésekor");
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      // Only allow editing existing companies
      if (!editingCompany) {
        toast.error("Új cég csak regisztrációval hozható létre");
        return;
      }

      const { error } = await supabase
        .from("profiles")
        .update({
          company_name: formData.company_name,
          contact_phone: formData.contact_phone,
          industry: formData.industry,
          employee_count: formData.employee_count,
          selected_package: formData.subscription_package,
        })
        .eq("id", editingCompany.id);

      if (error) throw error;
      toast.success("Cég sikeresen frissítve");

      setOpen(false);
      resetForm();
      fetchCompanies();
    } catch (error) {
      console.error("Error saving company:", error);
      toast.error("Hiba a cég mentésekor");
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm("Biztosan törölni szeretnéd ezt a céget? Ez törölni fogja a felhasználót is!")) return;

    try {
      // Delete user - this will cascade to profiles due to foreign key
      const { error } = await supabase.auth.admin.deleteUser(id);

      if (error) throw error;
      toast.success("Cég és felhasználó sikeresen törölve");
      fetchCompanies();
    } catch (error) {
      console.error("Error deleting company:", error);
      toast.error("Hiba a cég törlésekor");
    }
  };

  const resetForm = () => {
    setFormData({
      company_name: "",
      contact_email: "",
      contact_phone: "",
      industry: "",
      employee_count: "",
      subscription_status: "active",
      subscription_package: "",
      subscription_start_date: "",
      subscription_end_date: "",
      notes: "",
    });
    setEditingCompany(null);
  };

  const openEditDialog = (company: Company) => {
    setEditingCompany(company);
    setFormData({
      company_name: company.company_name,
      contact_email: company.email,
      contact_phone: company.contact_phone || "",
      industry: company.industry || "",
      employee_count: company.employee_count || "",
      subscription_status: "active",
      subscription_package: company.selected_package || "",
      subscription_start_date: "",
      subscription_end_date: "",
      notes: "",
    });
    setOpen(true);
  };

  if (loading) {
    return <div className="p-6">Betöltés...</div>;
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Cégek kezelése</h1>
        <p className="text-sm text-muted-foreground">Új cég csak regisztrációval hozható létre</p>
      </div>

      <Dialog open={open} onOpenChange={setOpen}>
          <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>{editingCompany ? "Cég szerkesztése" : "Új cég hozzáadása"}</DialogTitle>
            </DialogHeader>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="company_name">Cégnév*</Label>
                  <Input
                    id="company_name"
                    value={formData.company_name}
                    onChange={(e) => setFormData({ ...formData, company_name: e.target.value })}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="contact_email">Email*</Label>
                  <Input
                    id="contact_email"
                    type="email"
                    value={formData.contact_email}
                    onChange={(e) => setFormData({ ...formData, contact_email: e.target.value })}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="contact_phone">Telefon</Label>
                  <Input
                    id="contact_phone"
                    value={formData.contact_phone}
                    onChange={(e) => setFormData({ ...formData, contact_phone: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="industry">Iparág</Label>
                  <Input
                    id="industry"
                    value={formData.industry}
                    onChange={(e) => setFormData({ ...formData, industry: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="employee_count">Létszám</Label>
                  <Select
                    value={formData.employee_count}
                    onValueChange={(value) => setFormData({ ...formData, employee_count: value })}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="1-50">1-50</SelectItem>
                      <SelectItem value="51-200">51-200</SelectItem>
                      <SelectItem value="201-500">201-500</SelectItem>
                      <SelectItem value="501-1000">501-1000</SelectItem>
                      <SelectItem value="1000+">1000+</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="subscription_status">Előfizetés státusz</Label>
                  <Select
                    value={formData.subscription_status}
                    onValueChange={(value) => setFormData({ ...formData, subscription_status: value })}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="active">Aktív</SelectItem>
                      <SelectItem value="trial">Próba</SelectItem>
                      <SelectItem value="expired">Lejárt</SelectItem>
                      <SelectItem value="cancelled">Lemondva</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="subscription_package">Csomag</Label>
                  <Input
                    id="subscription_package"
                    value={formData.subscription_package}
                    onChange={(e) => setFormData({ ...formData, subscription_package: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="subscription_start_date">Kezdő dátum</Label>
                  <Input
                    id="subscription_start_date"
                    type="date"
                    value={formData.subscription_start_date}
                    onChange={(e) => setFormData({ ...formData, subscription_start_date: e.target.value })}
                  />
                </div>
                <div className="space-y-2 col-span-2">
                  <Label htmlFor="subscription_end_date">Lejárat dátuma</Label>
                  <Input
                    id="subscription_end_date"
                    type="date"
                    value={formData.subscription_end_date}
                    onChange={(e) => setFormData({ ...formData, subscription_end_date: e.target.value })}
                  />
                </div>
                <div className="space-y-2 col-span-2">
                  <Label htmlFor="notes">Megjegyzések</Label>
                  <Textarea
                    id="notes"
                    value={formData.notes}
                    onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                    rows={3}
                  />
                </div>
              </div>
              <div className="flex gap-2 justify-end">
                <Button type="button" variant="outline" onClick={() => setOpen(false)}>
                  Mégse
                </Button>
                <Button type="submit">
                  {editingCompany ? "Mentés" : "Létrehozás"}
                </Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>

      <Card>
        <CardHeader>
          <CardTitle>Regisztrált cégek</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Cégnév</TableHead>
                <TableHead>Kapcsolattartó</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Telefon</TableHead>
                <TableHead>Iparág</TableHead>
                <TableHead>Létszám</TableHead>
                <TableHead>Csomag</TableHead>
                <TableHead className="text-right">Műveletek</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {companies.map((company) => (
                <TableRow key={company.id}>
                  <TableCell className="font-medium">{company.company_name}</TableCell>
                  <TableCell>{company.full_name || "-"}</TableCell>
                  <TableCell>{company.email}</TableCell>
                  <TableCell>{company.contact_phone || "-"}</TableCell>
                  <TableCell>{company.industry || "-"}</TableCell>
                  <TableCell>{company.employee_count || "-"}</TableCell>
                  <TableCell>{company.selected_package || "-"}</TableCell>
                  <TableCell className="text-right">
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => openEditDialog(company)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDelete(company.id)}
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
