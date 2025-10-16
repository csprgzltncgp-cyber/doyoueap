import { useState, useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Building2, Plus, Edit, Trash2, Users, BarChart3 } from 'lucide-react';
import { toast } from 'sonner';
import { useAuth } from '@/hooks/useAuth';

interface PartnerCompany {
  id: string;
  company_name: string;
  contact_email: string;
  contact_phone: string | null;
  industry: string | null;
  employee_count: string | null;
  subscription_status: string | null;
  subscription_package: string | null;
  created_at: string;
  notes: string | null;
}

const PartnerCenter = () => {
  const { user } = useAuth();
  const [companies, setCompanies] = useState<PartnerCompany[]>([]);
  const [loading, setLoading] = useState(true);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [editingCompany, setEditingCompany] = useState<PartnerCompany | null>(null);
  const [formData, setFormData] = useState({
    company_name: '',
    contact_email: '',
    contact_phone: '',
    industry: '',
    employee_count: '',
    subscription_status: 'active',
    subscription_package: '',
    notes: '',
  });

  useEffect(() => {
    if (user) {
      fetchCompanies();
    }
  }, [user]);

  const fetchCompanies = async () => {
    if (!user) return;
    
    try {
      const { data, error } = await supabase
        .from('companies')
        .select('*')
        .eq('partner_user_id', user.id)
        .order('created_at', { ascending: false });

      if (error) throw error;
      setCompanies(data || []);
    } catch (error) {
      console.error('Error fetching companies:', error);
      toast.error('Hiba az ügyfélcégek betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const resetForm = () => {
    setFormData({
      company_name: '',
      contact_email: '',
      contact_phone: '',
      industry: '',
      employee_count: '',
      subscription_status: 'active',
      subscription_package: '',
      notes: '',
    });
    setEditingCompany(null);
  };

  const handleOpenDialog = (company?: PartnerCompany) => {
    if (company) {
      setEditingCompany(company);
      setFormData({
        company_name: company.company_name,
        contact_email: company.contact_email,
        contact_phone: company.contact_phone || '',
        industry: company.industry || '',
        employee_count: company.employee_count || '',
        subscription_status: company.subscription_status || 'active',
        subscription_package: company.subscription_package || '',
        notes: company.notes || '',
      });
    } else {
      resetForm();
    }
    setDialogOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!user) return;

    try {
      if (editingCompany) {
        // Update existing company
        const { error } = await supabase
          .from('companies')
          .update({
            company_name: formData.company_name,
            contact_email: formData.contact_email,
            contact_phone: formData.contact_phone,
            industry: formData.industry,
            employee_count: formData.employee_count,
            subscription_status: formData.subscription_status,
            subscription_package: formData.subscription_package,
            notes: formData.notes,
          })
          .eq('id', editingCompany.id);

        if (error) throw error;
        toast.success('Ügyfélcég sikeresen frissítve');
      } else {
        // Create new company
        const { error } = await supabase
          .from('companies')
          .insert({
            partner_user_id: user.id,
            company_name: formData.company_name,
            contact_email: formData.contact_email,
            contact_phone: formData.contact_phone,
            industry: formData.industry,
            employee_count: formData.employee_count,
            subscription_status: formData.subscription_status,
            subscription_package: formData.subscription_package,
            notes: formData.notes,
          });

        if (error) throw error;
        toast.success('Ügyfélcég sikeresen létrehozva');
      }

      setDialogOpen(false);
      resetForm();
      fetchCompanies();
    } catch (error) {
      console.error('Error saving company:', error);
      toast.error('Hiba az ügyfélcég mentésekor');
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Biztosan törölni szeretnéd ezt az ügyfélcéget?')) return;

    try {
      const { error } = await supabase
        .from('companies')
        .delete()
        .eq('id', id);

      if (error) throw error;
      toast.success('Ügyfélcég sikeresen törölve');
      fetchCompanies();
    } catch (error) {
      console.error('Error deleting company:', error);
      toast.error('Hiba az ügyfélcég törlésekor');
    }
  };

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="text-3xl font-bold mb-2">Partner Központ</h2>
          <p className="text-muted-foreground">Betöltés...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-start">
        <div>
          <h2 className="text-3xl font-bold mb-2">Partner Központ</h2>
          <p className="text-muted-foreground">
            Ügyfélcégek kezelése és felmérések menedzselése
          </p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button 
              onClick={() => handleOpenDialog()}
              style={{ backgroundColor: '#3572ef' }}
              className="hover:opacity-90"
            >
              <Plus className="h-4 w-4 mr-2" />
              Új ügyfélcég
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>
                {editingCompany ? 'Ügyfélcég szerkesztése' : 'Új ügyfélcég hozzáadása'}
              </DialogTitle>
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
                  <Label htmlFor="contact_email">Email *</Label>
                  <Input
                    id="contact_email"
                    type="email"
                    value={formData.contact_email}
                    onChange={(e) => setFormData({ ...formData, contact_email: e.target.value })}
                    required
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="contact_phone">Telefonszám</Label>
                  <Input
                    id="contact_phone"
                    value={formData.contact_phone}
                    onChange={(e) => setFormData({ ...formData, contact_phone: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="industry">Iparág</Label>
                  <Select value={formData.industry} onValueChange={(value) => setFormData({ ...formData, industry: value })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz iparágat" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="tech">Technológia</SelectItem>
                      <SelectItem value="finance">Pénzügy</SelectItem>
                      <SelectItem value="healthcare">Egészségügy</SelectItem>
                      <SelectItem value="education">Oktatás</SelectItem>
                      <SelectItem value="retail">Kereskedelem</SelectItem>
                      <SelectItem value="manufacturing">Gyártás</SelectItem>
                      <SelectItem value="other">Egyéb</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="employee_count">Munkavállalók száma</Label>
                  <Select value={formData.employee_count} onValueChange={(value) => setFormData({ ...formData, employee_count: value })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz létszámot" />
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
                  <Label htmlFor="subscription_package">Csomag</Label>
                  <Select value={formData.subscription_package} onValueChange={(value) => setFormData({ ...formData, subscription_package: value })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz csomagot" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="starter">Starter</SelectItem>
                      <SelectItem value="professional">Professional</SelectItem>
                      <SelectItem value="enterprise">Enterprise</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="notes">Jegyzetek</Label>
                <Input
                  id="notes"
                  value={formData.notes}
                  onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                  placeholder="Opcionális jegyzetek az ügyfélről"
                />
              </div>

              <div className="flex justify-end gap-2 pt-4">
                <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>
                  Mégse
                </Button>
                <Button 
                  type="submit"
                  style={{ backgroundColor: '#3572ef' }}
                  className="hover:opacity-90"
                >
                  {editingCompany ? 'Mentés' : 'Létrehozás'}
                </Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      {/* Statistics Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-medium">Összes ügyfél</CardTitle>
            <Building2 className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold" style={{ color: '#3572ef' }}>
              {companies.length}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-medium">Aktív előfizetések</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold" style={{ color: '#3abef9' }}>
              {companies.filter(c => c.subscription_status === 'active').length}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-medium">Összes felmérés</CardTitle>
            <BarChart3 className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold" style={{ color: '#050c9c' }}>
              0
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Companies Table */}
      <Card>
        <CardHeader>
          <CardTitle>Ügyfélcégek</CardTitle>
          <CardDescription>
            Az általad kezelt ügyfélcégek listája
          </CardDescription>
        </CardHeader>
        <CardContent>
          {companies.length === 0 ? (
            <div className="text-center py-12">
              <Building2 className="h-12 w-12 mx-auto text-muted-foreground mb-4" />
              <h3 className="text-lg font-semibold mb-2">Még nincsenek ügyfélcégek</h3>
              <p className="text-muted-foreground mb-4">
                Kezdj el ügyfélcégeket hozzáadni a Partner Központban
              </p>
              <Button 
                onClick={() => handleOpenDialog()}
                style={{ backgroundColor: '#3572ef' }}
                className="hover:opacity-90"
              >
                <Plus className="h-4 w-4 mr-2" />
                Első ügyfél hozzáadása
              </Button>
            </div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Cégnév</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Iparág</TableHead>
                  <TableHead>Létszám</TableHead>
                  <TableHead>Csomag</TableHead>
                  <TableHead>Státusz</TableHead>
                  <TableHead className="text-right">Műveletek</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {companies.map((company) => (
                  <TableRow key={company.id}>
                    <TableCell className="font-medium">{company.company_name}</TableCell>
                    <TableCell>{company.contact_email}</TableCell>
                    <TableCell>{company.industry || '-'}</TableCell>
                    <TableCell>{company.employee_count || '-'}</TableCell>
                    <TableCell>
                      <span className="px-2 py-1 rounded-full text-xs font-medium bg-[#3572ef]/10 text-[#3572ef]">
                        {company.subscription_package || '-'}
                      </span>
                    </TableCell>
                    <TableCell>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        company.subscription_status === 'active' 
                          ? 'bg-green-100 text-green-800' 
                          : 'bg-gray-100 text-gray-800'
                      }`}>
                        {company.subscription_status === 'active' ? 'Aktív' : 'Inaktív'}
                      </span>
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleOpenDialog(company)}
                        >
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleDelete(company.id)}
                        >
                          <Trash2 className="h-4 w-4 text-destructive" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default PartnerCenter;
