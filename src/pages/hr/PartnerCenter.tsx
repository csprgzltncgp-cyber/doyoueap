import { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Building2, Plus, Pencil, Trash2, Search, FileText, Eye, XCircle } from 'lucide-react';
import { supabase } from '@/integrations/supabase/client';
import { useToast } from '@/hooks/use-toast';
import { useAuth } from '@/hooks/useAuth';

interface Company {
  id: string;
  company_name: string;
  contact_email: string;
  contact_phone: string | null;
  industry_id: string | null;
  industry?: { name: string };
  employee_count: string | null;
  notes: string | null;
  created_at: string;
  running_audits?: number;
  closed_audits?: number;
}

const PartnerCenter = () => {
  const { toast } = useToast();
  const { user } = useAuth();
  const [companies, setCompanies] = useState<Company[]>([]);
  const [industries, setIndustries] = useState<Array<{ id: string; name: string }>>([]);
  const [loading, setLoading] = useState(true);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [editingCompany, setEditingCompany] = useState<Company | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [formData, setFormData] = useState({
    company_name: '',
    contact_email: '',
    contact_phone: '',
    industry_id: '',
    employee_count: '',
    notes: ''
  });

  useEffect(() => {
    if (user?.id) {
      fetchCompanies();
      fetchIndustries();
    }
  }, [user?.id]);

  const fetchIndustries = async () => {
    try {
      const { data, error } = await supabase
        .from('industries')
        .select('id, name')
        .eq('is_active', true)
        .order('name');

      if (error) throw error;
      setIndustries(data || []);
    } catch (error: any) {
      console.error('Error fetching industries:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült betölteni az iparágakat',
        variant: 'destructive'
      });
    }
  };

  const fetchCompanies = async () => {
    if (!user?.id) return;
    
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('companies')
        .select(`
          *,
          industry:industries(name)
        `)
        .eq('partner_user_id', user.id)
        .order('created_at', { ascending: false });

      if (error) throw error;
      
      // Fetch audit counts for each company
      const companiesWithAudits = await Promise.all(
        (data || []).map(async (company) => {
          const { count: runningCount } = await supabase
            .from('audits')
            .select('*', { count: 'exact', head: true })
            .eq('partner_company_id', company.id)
            .eq('is_active', true);

          const { count: closedCount } = await supabase
            .from('audits')
            .select('*', { count: 'exact', head: true })
            .eq('partner_company_id', company.id)
            .eq('is_active', false);

          return {
            ...company,
            running_audits: runningCount || 0,
            closed_audits: closedCount || 0
          };
        })
      );
      
      setCompanies(companiesWithAudits);
    } catch (error) {
      console.error('Error fetching companies:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült betölteni az ügyfélcégeket',
        variant: 'destructive'
      });
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (editingCompany) {
        const { error } = await supabase
          .from('companies')
          .update({
            company_name: formData.company_name,
            contact_email: formData.contact_email,
            contact_phone: formData.contact_phone || null,
            industry_id: formData.industry_id || null,
            employee_count: formData.employee_count || null,
            notes: formData.notes || null
          })
          .eq('id', editingCompany.id);

        if (error) throw error;

        toast({
          title: 'Sikeres mentés',
          description: 'Az ügyfélcég adatai frissítve lettek'
        });
      } else {
        const { error } = await supabase
          .from('companies')
          .insert({
            company_name: formData.company_name,
            contact_email: formData.contact_email,
            contact_phone: formData.contact_phone || null,
            industry_id: formData.industry_id || null,
            employee_count: formData.employee_count || null,
            notes: formData.notes || null,
            partner_user_id: user?.id
          });

        if (error) throw error;

        toast({
          title: 'Sikeres létrehozás',
          description: 'Az új ügyfélcég regisztrálva lett'
        });
      }

      resetForm();
      fetchCompanies();
      setDialogOpen(false);
    } catch (error) {
      console.error('Error saving company:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült menteni az ügyfélcéget',
        variant: 'destructive'
      });
    }
  };

  const handleDelete = async (id: string, companyName: string) => {
    if (!confirm(`Biztosan törölni szeretnéd a(z) "${companyName}" céget?`)) return;

    try {
      const { error } = await supabase
        .from('companies')
        .delete()
        .eq('id', id);

      if (error) throw error;

      toast({
        title: 'Sikeres törlés',
        description: 'Az ügyfélcég törölve lett'
      });

      fetchCompanies();
    } catch (error) {
      console.error('Error deleting company:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült törölni az ügyfélcéget',
        variant: 'destructive'
      });
    }
  };

  const resetForm = () => {
    setFormData({
      company_name: '',
      contact_email: '',
      contact_phone: '',
      industry_id: '',
      employee_count: '',
      notes: ''
    });
    setEditingCompany(null);
  };

  const openEditDialog = (company: Company) => {
    setEditingCompany(company);
    setFormData({
      company_name: company.company_name,
      contact_email: company.contact_email,
      contact_phone: company.contact_phone || '',
      industry_id: company.industry_id || '',
      employee_count: company.employee_count || '',
      notes: company.notes || ''
    });
    setDialogOpen(true);
  };

  const filteredCompanies = companies.filter(company =>
    company.company_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    company.contact_email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-3xl font-bold mb-2">Partner Központ</h2>
          <p className="text-muted-foreground">
            Ügyfélcégek kezelése
          </p>
        </div>

        <Dialog open={dialogOpen} onOpenChange={(open) => {
          setDialogOpen(open);
          if (!open) resetForm();
        }}>
          <DialogTrigger asChild>
            <Button className="bg-[#3572ef] hover:bg-[#050c9c]">
              <Plus className="h-4 w-4 mr-2" />
              Új ügyfélcég
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-2xl">
            <DialogHeader>
              <DialogTitle>
                {editingCompany ? 'Ügyfélcég szerkesztése' : 'Új ügyfélcég regisztrálása'}
              </DialogTitle>
              <DialogDescription>
                Add meg az ügyfélcég adatait
              </DialogDescription>
            </DialogHeader>

            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="company_name">Cégnév *</Label>
                  <Input
                    id="company_name"
                    value={formData.company_name}
                    onChange={(e) => setFormData({ ...formData, company_name: e.target.value })}
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="contact_email">Email cím *</Label>
                  <Input
                    id="contact_email"
                    type="email"
                    value={formData.contact_email}
                    onChange={(e) => setFormData({ ...formData, contact_email: e.target.value })}
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="contact_phone">Telefonszám</Label>
                  <Input
                    id="contact_phone"
                    value={formData.contact_phone}
                    onChange={(e) => setFormData({ ...formData, contact_phone: e.target.value })}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="industry_id">Iparág</Label>
                  <Select
                    value={formData.industry_id}
                    onValueChange={(value) =>
                      setFormData({ ...formData, industry_id: value })
                    }
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz iparágat" />
                    </SelectTrigger>
                    <SelectContent>
                      {industries.map((industry) => (
                        <SelectItem key={industry.id} value={industry.id}>
                          {industry.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="employee_count">Létszám</Label>
                  <Input
                    id="employee_count"
                    value={formData.employee_count}
                    onChange={(e) => setFormData({ ...formData, employee_count: e.target.value })}
                  />
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="notes">Jegyzetek</Label>
                <Input
                  id="notes"
                  value={formData.notes}
                  onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                />
              </div>

              <div className="flex justify-end gap-2">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => {
                    resetForm();
                    setDialogOpen(false);
                  }}
                >
                  Mégse
                </Button>
                <Button type="submit" className="bg-[#3572ef] hover:bg-[#050c9c]">
                  {editingCompany ? 'Mentés' : 'Létrehozás'}
                </Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <div>
              <CardTitle className="flex items-center gap-2">
                <Building2 className="h-5 w-5 text-foreground" />
                Regisztrált ügyfélcégek
              </CardTitle>
              <CardDescription>
                {companies.length} ügyfélcég a rendszerben
              </CardDescription>
            </div>
            <div className="relative w-64">
              <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Keresés..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-8"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          {loading ? (
            <p className="text-center py-8 text-muted-foreground">Betöltés...</p>
          ) : filteredCompanies.length === 0 ? (
            <p className="text-center py-8 text-muted-foreground">
              {searchTerm ? 'Nincs találat' : 'Még nincs regisztrált ügyfélcég'}
            </p>
          ) : (
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Cégnév</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Telefon</TableHead>
                    <TableHead>Iparág</TableHead>
                    <TableHead>Létszám</TableHead>
                    <TableHead className="text-center">Futó felmérések</TableHead>
                    <TableHead className="text-center">Lezárt felmérések</TableHead>
                    <TableHead className="text-right">Műveletek</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredCompanies.map((company) => (
                    <TableRow key={company.id}>
                      <TableCell className="font-medium">{company.company_name}</TableCell>
                    <TableCell>{company.contact_email}</TableCell>
                    <TableCell>{company.contact_phone || '-'}</TableCell>
                    <TableCell>{company.industry?.name || '-'}</TableCell>
                    <TableCell>{company.employee_count || '-'}</TableCell>
                      <TableCell className="text-center">
                        <span className="inline-flex items-center gap-1 text-green-600 font-semibold">
                          <FileText className="h-4 w-4" />
                          {company.running_audits || 0}
                        </span>
                      </TableCell>
                      <TableCell className="text-center">
                        <span className="inline-flex items-center gap-1 text-muted-foreground">
                          <XCircle className="h-4 w-4" />
                          {company.closed_audits || 0}
                        </span>
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          <Button
                            size="sm"
                            variant="outline"
                            onClick={() => window.location.href = `/?section=eap-pulse&sub=running-audits`}
                            title="Felmérések megtekintése"
                          >
                            <Eye className="h-4 w-4" />
                          </Button>
                          <Button
                            size="sm"
                            variant="outline"
                            onClick={() => openEditDialog(company)}
                          >
                            <Pencil className="h-4 w-4" />
                          </Button>
                          <Button
                            size="sm"
                            variant="outline"
                            onClick={() => handleDelete(company.id, company.company_name)}
                            className="text-destructive hover:text-destructive"
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default PartnerCenter;
