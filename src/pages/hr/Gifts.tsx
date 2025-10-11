import { useState, useEffect, useRef } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/components/ui/alert-dialog';
import { toast } from '@/hooks/use-toast';
import { Plus, Upload, X, Trash2, Gift as GiftIcon } from 'lucide-react';

interface Gift {
  id: string;
  name: string;
  description: string | null;
  image_url: string | null;
  value_eur: number;
  is_default: boolean;
  is_active: boolean;
  created_by: string | null;
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

const Gifts = () => {
  const { user } = useAuth();
  const [gifts, setGifts] = useState<Gift[]>([]);
  const [loading, setLoading] = useState(true);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [giftToDelete, setGiftToDelete] = useState<Gift | null>(null);
  
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    image_url: '',
    value_eur: '',
    is_active: true,
  });

  const [uploadedImage, setUploadedImage] = useState<File | null>(null);
  const [imagePreview, setImagePreview] = useState<string | null>(null);
  const [uploading, setUploading] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    fetchGifts();
  }, []);

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
    if (!uploadedImage) return formData.image_url.trim() || null;

    try {
      setUploading(true);
      const fileExt = uploadedImage.name.split('.').pop();
      const fileName = `${Math.random().toString(36).substring(2)}-${Date.now()}.${fileExt}`;
      const filePath = `gifts/${fileName}`;

      const { error: uploadError } = await supabase.storage
        .from('audit-assets')
        .upload(filePath, uploadedImage);

      if (uploadError) throw uploadError;

      const { data: { publicUrl } } = supabase.storage
        .from('audit-assets')
        .getPublicUrl(filePath);

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

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      if (file.size > 5 * 1024 * 1024) {
        toast({
          title: 'Túl nagy fájl',
          description: 'A kép mérete maximum 5MB lehet.',
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

  const resetForm = () => {
    setFormData({
      name: '',
      description: '',
      image_url: '',
      value_eur: '',
      is_active: true,
    });
    setUploadedImage(null);
    setImagePreview(null);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) return;

    try {
      const imageUrl = await uploadImage();

      const { error } = await supabase
        .from('gifts')
        .insert({
          name: formData.name,
          description: formData.description || null,
          image_url: imageUrl,
          value_eur: parseFloat(formData.value_eur),
          is_active: formData.is_active,
          is_default: false,
          created_by: user?.id,
        });

      if (error) throw error;

      toast({
        title: 'Siker',
        description: 'Az ajándék sikeresen létrehozva.',
      });

      setDialogOpen(false);
      resetForm();
      fetchGifts();
    } catch (error: any) {
      console.error('Error creating gift:', error);
      toast({
        title: 'Hiba',
        description: error.message || 'Nem sikerült létrehozni az ajándékot.',
        variant: 'destructive',
      });
    }
  };

  const handleDelete = async () => {
    if (!giftToDelete) return;

    try {
      const { error } = await supabase
        .from('gifts')
        .delete()
        .eq('id', giftToDelete.id);

      if (error) throw error;

      toast({
        title: 'Siker',
        description: 'Az ajándék sikeresen törölve.',
      });

      setDeleteDialogOpen(false);
      setGiftToDelete(null);
      fetchGifts();
    } catch (error: any) {
      console.error('Error deleting gift:', error);
      toast({
        title: 'Hiba',
        description: error.message || 'Nem sikerült törölni az ajándékot.',
        variant: 'destructive',
      });
    }
  };

  const canDeleteGift = (gift: Gift) => {
    return gift.created_by === user?.id && gift.created_by !== null;
  };

  if (loading) {
    return (
      <div className="p-6">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-3xl font-bold mb-2">Ajándékok</h2>
        <p className="text-muted-foreground">
          Tekintse meg az elérhető ajándékokat és adjon hozzá egyedi ajándékot a sorsolásokhoz.
        </p>
      </div>

      {/* Egyedi ajándék hozzáadása */}
      <Card>
        <CardHeader>
          <CardTitle>Egyedi ajándék hozzáadása</CardTitle>
          <CardDescription>
            Hozzon létre saját egyedi ajándékot, amelyet használhat a sorsolásokhoz.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
            <DialogTrigger asChild>
              <Button className="w-full sm:w-auto">
                <Plus className="mr-2 h-4 w-4" />
                Új ajándék hozzáadása
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
              <DialogHeader>
                <DialogTitle>Új ajándék létrehozása</DialogTitle>
              </DialogHeader>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <Label htmlFor="name">Név *</Label>
                  <Input
                    id="name"
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="pl. Wellness hétvége"
                    required
                  />
                </div>

                <div>
                  <Label htmlFor="value">Érték (€) *</Label>
                  <Input
                    id="value"
                    type="number"
                    step="0.01"
                    min="1"
                    value={formData.value_eur}
                    onChange={(e) => setFormData({ ...formData, value_eur: e.target.value })}
                    placeholder="pl. 100"
                    required
                  />
                </div>

                <div>
                  <Label htmlFor="description">Leírás</Label>
                  <Textarea
                    id="description"
                    value={formData.description}
                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                    placeholder="Rövid leírás az ajándékról..."
                    rows={3}
                  />
                </div>

                <div>
                  <Label>Kép</Label>
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
                          onClick={() => {
                            setUploadedImage(null);
                            setImagePreview(null);
                            if (fileInputRef.current) {
                              fileInputRef.current.value = '';
                            }
                          }}
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
                    id="is_active"
                    checked={formData.is_active}
                    onCheckedChange={(checked) => setFormData({ ...formData, is_active: checked })}
                  />
                  <Label htmlFor="is_active">Aktív</Label>
                </div>

                <div className="flex gap-2 justify-end">
                  <Button type="button" variant="outline" onClick={() => {
                    setDialogOpen(false);
                    resetForm();
                  }}>
                    Mégse
                  </Button>
                  <Button type="submit" disabled={uploading}>
                    {uploading ? 'Feltöltés...' : 'Létrehozás'}
                  </Button>
                </div>
              </form>
            </DialogContent>
          </Dialog>
        </CardContent>
      </Card>

      {/* Ajándékok listája */}
      <div>
        <h3 className="text-xl font-semibold mb-4">Elérhető ajándékok</h3>
        {gifts.length === 0 ? (
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12">
                <GiftIcon className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                <p className="text-muted-foreground">Még nincsenek ajándékok</p>
              </div>
            </CardContent>
          </Card>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {gifts.map((gift) => (
              <Card key={gift.id} className="overflow-hidden">
                <div className="h-48 overflow-hidden bg-muted flex items-center justify-center">
                  {gift.image_url ? (
                    <img
                      src={gift.image_url}
                      alt={gift.name}
                      className="w-full h-full object-cover"
                    />
                  ) : (
                    <GiftIcon className="h-16 w-16 text-muted-foreground" />
                  )}
                </div>
                <CardHeader>
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <CardTitle className="text-lg">{gift.name}</CardTitle>
                      <div className="text-2xl font-bold text-primary mt-2">
                        {formatEUR(gift.value_eur)}
                      </div>
                    </div>
                    {canDeleteGift(gift) && (
                      <Button
                        variant="ghost"
                        size="icon"
                        className="text-destructive hover:text-destructive"
                        onClick={() => {
                          setGiftToDelete(gift);
                          setDeleteDialogOpen(true);
                        }}
                      >
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    )}
                  </div>
                </CardHeader>
                {gift.description && (
                  <CardContent>
                    <p className="text-sm text-muted-foreground">{gift.description}</p>
                  </CardContent>
                )}
                <CardContent className="pt-0">
                  <div className="flex gap-2 flex-wrap">
                    {gift.is_default && (
                      <span className="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                        Alapértelmezett
                      </span>
                    )}
                    {gift.is_active ? (
                      <span className="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                        Aktív
                      </span>
                    ) : (
                      <span className="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                        Inaktív
                      </span>
                    )}
                    {canDeleteGift(gift) && (
                      <span className="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                        Egyedi
                      </span>
                    )}
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>

      {/* Delete confirmation dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Biztosan törli az ajándékot?</AlertDialogTitle>
            <AlertDialogDescription>
              Ez a művelet nem vonható vissza. Az ajándék véglegesen törlődik.
              {giftToDelete && (
                <div className="mt-2 font-medium">
                  Törlendő: {giftToDelete.name} ({formatEUR(giftToDelete.value_eur)})
                </div>
              )}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={() => setGiftToDelete(null)}>
              Mégse
            </AlertDialogCancel>
            <AlertDialogAction onClick={handleDelete} className="bg-destructive text-destructive-foreground hover:bg-destructive/90">
              Törlés
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
};

export default Gifts;
