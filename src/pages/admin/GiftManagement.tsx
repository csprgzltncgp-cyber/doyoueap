import { useState, useEffect, useRef } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { toast } from '@/hooks/use-toast';
import { Plus, Edit, Search, Upload, X, Trophy } from 'lucide-react';
import DrawHistory from './DrawHistory';

interface Gift {
  id: string;
  name: string;
  description: string | null;
  image_url: string | null;
  value_eur: number;
  is_default: boolean;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

const formatEUR = (value: number): string => {
  const formatted = new Intl.NumberFormat('hu-HU', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(value);
  return formatted.replace(/\s/g, ' ') + ' €';
};

const GiftManagement = () => {
  const [gifts, setGifts] = useState<Gift[]>([]);
  const [filteredGifts, setFilteredGifts] = useState<Gift[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState<'all' | 'active' | 'inactive'>('all');
  const [dialogOpen, setDialogOpen] = useState(false);
  const [editingGift, setEditingGift] = useState<Gift | null>(null);
  
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    image_url: '',
    value_eur: '',
    is_default: false,
    is_active: true,
  });

  const [uploadedImage, setUploadedImage] = useState<File | null>(null);
  const [imagePreview, setImagePreview] = useState<string | null>(null);
  const [uploading, setUploading] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    fetchGifts();
  }, []);

  useEffect(() => {
    filterGifts();
  }, [gifts, searchTerm, statusFilter]);

  const fetchGifts = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('gifts')
        .select('*')
        .order('created_at', { ascending: false });

      if (error) throw error;
      setGifts(data || []);
    } catch (error) {
      console.error('Error fetching gifts:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült betölteni az ajándékokat.',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const filterGifts = () => {
    let filtered = [...gifts];

    if (searchTerm) {
      filtered = filtered.filter(gift =>
        gift.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        gift.description?.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    if (statusFilter === 'active') {
      filtered = filtered.filter(gift => gift.is_active);
    } else if (statusFilter === 'inactive') {
      filtered = filtered.filter(gift => !gift.is_active);
    }

    setFilteredGifts(filtered);
  };

  const validateForm = (): boolean => {
    if (!formData.name.trim()) {
      toast({
        title: 'Hiányzó mező',
        description: 'A név kötelező.',
        variant: 'destructive',
      });
      return false;
    }

    const value = parseFloat(formData.value_eur);
    if (isNaN(value) || value < 1) {
      toast({
        title: 'Érvénytelen érték',
        description: 'Az érték legalább 1 € kell legyen.',
        variant: 'destructive',
      });
      return false;
    }

    return true;
  };

  const uploadImage = async (): Promise<string | null> => {
    console.log('uploadImage called:', { 
      hasUploadedImage: !!uploadedImage, 
      formDataImageUrl: formData.image_url 
    });
    
    if (!uploadedImage) {
      const existingUrl = formData.image_url.trim() || null;
      console.log('No new image to upload, using existing URL:', existingUrl);
      return existingUrl;
    }

    try {
      setUploading(true);
      console.log('Uploading new image:', uploadedImage.name);
      
      const fileExt = uploadedImage.name.split('.').pop();
      const fileName = `${Math.random().toString(36).substring(2)}-${Date.now()}.${fileExt}`;
      const filePath = `gifts/${fileName}`;

      console.log('Upload path:', filePath);

      const { error: uploadError } = await supabase.storage
        .from('audit-assets')
        .upload(filePath, uploadedImage);

      if (uploadError) {
        console.error('Upload error:', uploadError);
        throw uploadError;
      }

      const { data: { publicUrl } } = supabase.storage
        .from('audit-assets')
        .getPublicUrl(filePath);

      console.log('Image uploaded successfully, public URL:', publicUrl);
      return publicUrl;
    } catch (error) {
      console.error('Error uploading image:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült feltölteni a képet.',
        variant: 'destructive',
      });
      return null;
    } finally {
      setUploading(false);
    }
  };

  const handleSubmit = async () => {
    if (!validateForm()) return;

    try {
      console.log('Starting image upload...', { hasUploadedImage: !!uploadedImage, imageUrl: formData.image_url });
      const imageUrl = await uploadImage();
      console.log('Image upload result:', imageUrl);

      const payload = {
        name: formData.name.trim(),
        description: formData.description.trim() || null,
        image_url: imageUrl,
        value_eur: parseFloat(formData.value_eur),
        is_default: formData.is_default,
        is_active: formData.is_active,
      };

      console.log('Payload to save:', payload);

      if (editingGift) {
        const { error } = await supabase
          .from('gifts')
          .update(payload)
          .eq('id', editingGift.id);

        if (error) throw error;
        toast({ title: 'Sikeres módosítás', description: 'Az ajándék frissítve.' });
      } else {
        const { error } = await supabase
          .from('gifts')
          .insert([payload]);

        if (error) throw error;
        toast({ title: 'Sikeres létrehozás', description: 'Az ajándék létrehozva.' });
      }

      fetchGifts();
      handleCloseDialog();
    } catch (error) {
      console.error('Error saving gift:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült menteni az ajándékot.',
        variant: 'destructive',
      });
    }
  };

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      if (!file.type.startsWith('image/')) {
        toast({
          title: 'Érvénytelen fájl',
          description: 'Csak képfájlokat lehet feltölteni.',
          variant: 'destructive',
        });
        return;
      }

      setUploadedImage(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleRemoveImage = () => {
    setUploadedImage(null);
    setImagePreview(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  const handleEdit = (gift: Gift) => {
    setEditingGift(gift);
    setFormData({
      name: gift.name,
      description: gift.description || '',
      image_url: gift.image_url || '',
      value_eur: gift.value_eur.toString(),
      is_default: gift.is_default,
      is_active: gift.is_active,
    });
    setImagePreview(gift.image_url || null);
    setUploadedImage(null);
    setDialogOpen(true);
  };

  const handleCloseDialog = () => {
    setDialogOpen(false);
    setEditingGift(null);
    setFormData({
      name: '',
      description: '',
      image_url: '',
      value_eur: '',
      is_default: false,
      is_active: true,
    });
    setUploadedImage(null);
    setImagePreview(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  const toggleActive = async (gift: Gift) => {
    try {
      const { error } = await supabase
        .from('gifts')
        .update({ is_active: !gift.is_active })
        .eq('id', gift.id);

      if (error) throw error;
      
      toast({
        title: 'Státusz frissítve',
        description: `Az ajándék most ${!gift.is_active ? 'aktív' : 'inaktív'}.`,
      });
      
      fetchGifts();
    } catch (error) {
      console.error('Error toggling gift status:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült frissíteni a státuszt.',
        variant: 'destructive',
      });
    }
  };

  return (
    <Tabs defaultValue="gifts" className="space-y-6">
      <TabsList className="grid w-full grid-cols-2 max-w-md">
        <TabsTrigger value="gifts">Ajándékok</TabsTrigger>
        <TabsTrigger value="draws">Archív Sorsolások</TabsTrigger>
      </TabsList>

      <TabsContent value="gifts" className="space-y-6">
        <div className="flex justify-between items-center">
          <h2 className="text-2xl font-bold">Ajándékok kezelése</h2>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={() => setEditingGift(null)}>
              <Plus className="mr-2 h-4 w-4" />
              Új ajándék
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-md">
            <DialogHeader>
              <DialogTitle>
                {editingGift ? 'Ajándék szerkesztése' : 'Új ajándék'}
              </DialogTitle>
            </DialogHeader>
            <div className="space-y-4">
              <div>
                <Label htmlFor="name">Név *</Label>
                <Input
                  id="name"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  placeholder="pl. Amazon kupon"
                />
              </div>

              <div>
                <Label htmlFor="value_eur">Érték (EUR) *</Label>
                <Input
                  id="value_eur"
                  type="number"
                  step="0.01"
                  min="1"
                  value={formData.value_eur}
                  onChange={(e) => setFormData({ ...formData, value_eur: e.target.value })}
                  placeholder="50"
                />
              </div>

              <div>
                <Label htmlFor="description">Rövid leírás</Label>
                <Textarea
                  id="description"
                  value={formData.description}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                  placeholder="Opcionális leírás..."
                  rows={3}
                />
              </div>

              <div>
                <Label htmlFor="image">Kép feltöltése (opcionális)</Label>
                <div className="space-y-2">
                  <input
                    ref={fileInputRef}
                    type="file"
                    accept="image/*"
                    onChange={handleImageChange}
                    className="hidden"
                  />
                  
                  {imagePreview ? (
                    <div className="relative w-full h-48 border rounded-lg overflow-hidden">
                      <img
                        src={imagePreview}
                        alt="Előnézet"
                        className="w-full h-full object-cover"
                      />
                      <Button
                        type="button"
                        variant="destructive"
                        size="icon"
                        className="absolute top-2 right-2"
                        onClick={handleRemoveImage}
                      >
                        <X className="h-4 w-4" />
                      </Button>
                    </div>
                  ) : (
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => fileInputRef.current?.click()}
                      className="w-full"
                    >
                      <Upload className="mr-2 h-4 w-4" />
                      Kép feltöltése
                    </Button>
                  )}

                  <div className="text-sm text-muted-foreground">
                    vagy URL megadása:
                  </div>
                  <Input
                    value={formData.image_url}
                    onChange={(e) => setFormData({ ...formData, image_url: e.target.value })}
                    placeholder="https://example.com/image.jpg"
                    disabled={!!uploadedImage}
                  />
                </div>
              </div>

              <div className="flex items-center space-x-2">
                <Switch
                  id="is_default"
                  checked={formData.is_default}
                  onCheckedChange={(checked) => setFormData({ ...formData, is_default: checked })}
                />
                <Label htmlFor="is_default">Alapértelmezett</Label>
              </div>

              <div className="flex items-center space-x-2">
                <Switch
                  id="is_active"
                  checked={formData.is_active}
                  onCheckedChange={(checked) => setFormData({ ...formData, is_active: checked })}
                />
                <Label htmlFor="is_active">Aktív</Label>
              </div>

              <div className="flex gap-2 pt-4">
                <Button onClick={handleSubmit} className="flex-1" disabled={uploading}>
                  {uploading ? 'Feltöltés...' : editingGift ? 'Mentés' : 'Létrehozás'}
                </Button>
                <Button onClick={handleCloseDialog} variant="outline" className="flex-1" disabled={uploading}>
                  Mégse
                </Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Szűrők és keresés</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  placeholder="Keresés név vagy leírás alapján..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <Select value={statusFilter} onValueChange={(value: any) => setStatusFilter(value)}>
              <SelectTrigger className="w-48">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Összes</SelectItem>
                <SelectItem value="active">Aktív</SelectItem>
                <SelectItem value="inactive">Inaktív</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Ajándékok ({filteredGifts.length})</CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <p className="text-center text-muted-foreground">Betöltés...</p>
          ) : filteredGifts.length === 0 ? (
            <p className="text-center text-muted-foreground">Nincs megjeleníthető ajándék.</p>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Név</TableHead>
                  <TableHead>Érték</TableHead>
                  <TableHead>Leírás</TableHead>
                  <TableHead>Kép</TableHead>
                  <TableHead>Alapért.</TableHead>
                  <TableHead>Státusz</TableHead>
                  <TableHead className="text-right">Műveletek</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredGifts.map((gift) => (
                  <TableRow key={gift.id}>
                    <TableCell className="font-medium">{gift.name}</TableCell>
                    <TableCell>{formatEUR(gift.value_eur)}</TableCell>
                    <TableCell className="max-w-xs truncate">
                      {gift.description || '—'}
                    </TableCell>
                    <TableCell>
                      {gift.image_url ? (
                        <div className="h-10 w-10 rounded overflow-hidden bg-muted">
                          <img src={gift.image_url} alt={gift.name} className="h-full w-full object-cover" />
                        </div>
                      ) : (
                        <div className="h-10 w-10 rounded bg-muted flex items-center justify-center">
                          <Trophy className="h-5 w-5 text-muted-foreground" />
                        </div>
                      )}
                    </TableCell>
                    <TableCell>{gift.is_default ? '✓' : '—'}</TableCell>
                    <TableCell>
                      <span className={`px-2 py-1 rounded text-xs ${gift.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                        {gift.is_active ? 'Aktív' : 'Inaktív'}
                      </span>
                    </TableCell>
                    <TableCell className="text-right space-x-2">
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => handleEdit(gift)}
                      >
                        <Edit className="h-4 w-4" />
                      </Button>
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => toggleActive(gift)}
                      >
                        {gift.is_active ? 'Inaktiválás' : 'Aktiválás'}
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
      </TabsContent>

      <TabsContent value="draws" className="space-y-6">
        <DrawHistory />
      </TabsContent>
    </Tabs>
  );
};

export default GiftManagement;
