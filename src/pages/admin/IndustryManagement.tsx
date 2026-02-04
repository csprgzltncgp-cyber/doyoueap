import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Switch } from "@/components/ui/switch";
import { toast } from "sonner";
import { Plus, Pencil, Trash2 } from "lucide-react";

interface Industry {
  id: string;
  name: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export default function IndustryManagement() {
  const [industries, setIndustries] = useState<Industry[]>([]);
  const [loading, setLoading] = useState(true);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [formData, setFormData] = useState({
    id: "",
    name: "",
    is_active: true,
  });

  useEffect(() => {
    fetchIndustries();
  }, []);

  const fetchIndustries = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from("industries")
        .select("*")
        .order("name");

      if (error) throw error;
      setIndustries(data || []);
    } catch (error: any) {
      console.error("Error fetching industries:", error);
      toast.error("Hiba az iparágak betöltése során");
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (isEditing) {
        const { error } = await supabase
          .from("industries")
          .update({
            name: formData.name,
            is_active: formData.is_active,
          })
          .eq("id", formData.id);

        if (error) throw error;
        toast.success("Iparág sikeresen frissítve");
      } else {
        const { error } = await supabase.from("industries").insert({
          name: formData.name,
          is_active: formData.is_active,
        });

        if (error) throw error;
        toast.success("Iparág sikeresen létrehozva");
      }

      setIsDialogOpen(false);
      resetForm();
      fetchIndustries();
    } catch (error: any) {
      console.error("Error saving industry:", error);
      toast.error(error.message || "Hiba az iparág mentése során");
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm("Biztosan törölni szeretnéd ezt az iparágat?")) return;

    try {
      const { error } = await supabase.from("industries").delete().eq("id", id);

      if (error) throw error;
      toast.success("Iparág sikeresen törölve");
      fetchIndustries();
    } catch (error: any) {
      console.error("Error deleting industry:", error);
      toast.error("Hiba az iparág törlése során");
    }
  };

  const resetForm = () => {
    setFormData({ id: "", name: "", is_active: true });
    setIsEditing(false);
  };

  const openEditDialog = (industry: Industry) => {
    setFormData({
      id: industry.id,
      name: industry.name,
      is_active: industry.is_active,
    });
    setIsEditing(true);
    setIsDialogOpen(true);
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-8">
        <div>
          <h1 className="text-3xl font-bold mb-2">Iparágak Kezelése</h1>
          <p className="text-muted-foreground">
            Iparágak létrehozása, szerkesztése és törlése
          </p>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={resetForm}>
              <Plus className="w-4 h-4 mr-2" />
              Új Iparág
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>
                {isEditing ? "Iparág Szerkesztése" : "Új Iparág Hozzáadása"}
              </DialogTitle>
            </DialogHeader>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Label htmlFor="name">Iparág Neve *</Label>
                <Input
                  id="name"
                  value={formData.name}
                  onChange={(e) =>
                    setFormData({ ...formData, name: e.target.value })
                  }
                  required
                />
              </div>
              <div className="flex items-center justify-between">
                <Label htmlFor="is_active">Aktív</Label>
                <Switch
                  id="is_active"
                  checked={formData.is_active}
                  onCheckedChange={(checked) =>
                    setFormData({ ...formData, is_active: checked })
                  }
                />
              </div>
              <div className="flex gap-2 justify-end">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => setIsDialogOpen(false)}
                >
                  Mégse
                </Button>
                <Button type="submit">
                  {isEditing ? "Mentés" : "Létrehozás"}
                </Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      {loading ? (
        <p>Betöltés...</p>
      ) : (
        <div className="bg-card rounded-lg border">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Név</TableHead>
                <TableHead>Állapot</TableHead>
                <TableHead>Létrehozva</TableHead>
                <TableHead className="text-right">Műveletek</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {industries.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={4} className="text-center py-8">
                    Még nincsenek iparágak
                  </TableCell>
                </TableRow>
              ) : (
                industries.map((industry) => (
                  <TableRow key={industry.id}>
                    <TableCell className="font-medium">
                      {industry.name}
                    </TableCell>
                    <TableCell>
                      <span
                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                          industry.is_active
                            ? "bg-chart-2/20 text-chart-1"
                            : "bg-muted text-muted-foreground"
                        }`}
                      >
                        {industry.is_active ? "Aktív" : "Inaktív"}
                      </span>
                    </TableCell>
                    <TableCell>
                      {new Date(industry.created_at).toLocaleDateString("hu-HU")}
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex gap-2 justify-end">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => openEditDialog(industry)}
                        >
                          <Pencil className="w-4 h-4" />
                        </Button>
                        <Button
                          variant="destructive"
                          size="sm"
                          onClick={() => handleDelete(industry.id)}
                        >
                          <Trash2 className="w-4 h-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </div>
      )}
    </div>
  );
}