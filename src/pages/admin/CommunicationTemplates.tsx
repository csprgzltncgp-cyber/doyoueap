import { useState, useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { toast } from 'sonner';
import { Mail, Link, QrCode, Gift, Save, Pencil, Upload, X, Image } from 'lucide-react';

interface Template {
  id: string;
  template_type: 'email' | 'public_link' | 'qr_code';
  has_gift: boolean;
  subject: string | null;
  content: string;
  created_at: string;
  updated_at: string;
}

interface Poster {
  id: string;
  has_gift: boolean;
  poster_images: string[];
  source_file_url: string | null;
  created_at: string;
  updated_at: string;
}

const CommunicationTemplates = () => {
  const [templates, setTemplates] = useState<Template[]>([]);
  const [posters, setPosters] = useState<Poster[]>([]);
  const [loading, setLoading] = useState(true);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [formData, setFormData] = useState<Partial<Template>>({});
  const [uploadingPosters, setUploadingPosters] = useState<{ hasGift: boolean; files: File[] } | null>(null);
  const [uploadingZip, setUploadingZip] = useState<{ hasGift: boolean; file: File | null } | null>(null);

  useEffect(() => {
    fetchTemplates();
    fetchPosters();
  }, []);

  const fetchTemplates = async () => {
    try {
      const { data, error } = await supabase
        .from('communication_templates')
        .select('*')
        .order('template_type', { ascending: true })
        .order('has_gift', { ascending: true });

      if (error) throw error;
      setTemplates(data || []);
    } catch (error: any) {
      toast.error('Hiba történt a sablonok betöltésekor');
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const fetchPosters = async () => {
    try {
      const { data, error } = await supabase
        .from('communication_posters')
        .select('*')
        .order('has_gift', { ascending: true });

      if (error) throw error;
      setPosters(data || []);
    } catch (error: any) {
      toast.error('Hiba történt a plakátok betöltésekor');
      console.error(error);
    }
  };

  const handleSave = async () => {
    if (!formData.id) return;

    try {
      const { error } = await supabase
        .from('communication_templates')
        .update({
          subject: formData.subject,
          content: formData.content,
        })
        .eq('id', formData.id);

      if (error) throw error;

      toast.success('Sablon sikeresen mentve!');
      setEditingId(null);
      setFormData({});
      fetchTemplates();
    } catch (error: any) {
      toast.error('Hiba történt a mentés során');
      console.error(error);
    }
  };

  const startEdit = (template: Template) => {
    setEditingId(template.id);
    setFormData(template);
  };

  const cancelEdit = () => {
    setEditingId(null);
    setFormData({});
  };

  const handlePosterUpload = async (hasGift: boolean, files: FileList | null) => {
    if (!files || files.length === 0) return;

    const fileArray = Array.from(files);
    setUploadingPosters({ hasGift, files: fileArray });

    try {
      const uploadPromises = fileArray.map(async (file) => {
        const fileExt = file.name.split('.').pop();
        const fileName = `poster-${hasGift ? 'gift' : 'no-gift'}-${Date.now()}-${Math.random()}.${fileExt}`;
        const filePath = `posters/${fileName}`;

        const { error: uploadError } = await supabase.storage
          .from('audit-assets')
          .upload(filePath, file);

        if (uploadError) throw uploadError;

        const { data: { publicUrl } } = supabase.storage
          .from('audit-assets')
          .getPublicUrl(filePath);

        return publicUrl;
      });

      const uploadedUrls = await Promise.all(uploadPromises);

      // Check if poster record exists for this gift type
      const existingPoster = posters.find(p => p.has_gift === hasGift);

      if (existingPoster) {
        // Update existing
        const { error } = await supabase
          .from('communication_posters')
          .update({
            poster_images: [...existingPoster.poster_images, ...uploadedUrls],
          })
          .eq('id', existingPoster.id);

        if (error) throw error;
      } else {
        // Create new
        const { error } = await supabase
          .from('communication_posters')
          .insert({
            has_gift: hasGift,
            poster_images: uploadedUrls,
          });

        if (error) throw error;
      }

      toast.success('Plakátok sikeresen feltöltve!');
      fetchPosters();
    } catch (error: any) {
      toast.error('Hiba történt a feltöltés során');
      console.error(error);
    } finally {
      setUploadingPosters(null);
    }
  };

  const handleZipUpload = async (hasGift: boolean, file: File | null) => {
    if (!file) return;

    setUploadingZip({ hasGift, file });

    try {
      const fileName = `source-${hasGift ? 'gift' : 'no-gift'}-${Date.now()}.zip`;
      const filePath = `posters/sources/${fileName}`;

      const { error: uploadError } = await supabase.storage
        .from('audit-assets')
        .upload(filePath, file);

      if (uploadError) throw uploadError;

      const { data: { publicUrl } } = supabase.storage
        .from('audit-assets')
        .getPublicUrl(filePath);

      // Check if poster record exists for this gift type
      const existingPoster = posters.find(p => p.has_gift === hasGift);

      if (existingPoster) {
        const { error } = await supabase
          .from('communication_posters')
          .update({ source_file_url: publicUrl })
          .eq('id', existingPoster.id);

        if (error) throw error;
      } else {
        const { error } = await supabase
          .from('communication_posters')
          .insert({
            has_gift: hasGift,
            poster_images: [],
            source_file_url: publicUrl,
          });

        if (error) throw error;
      }

      toast.success('Forrás fájl sikeresen feltöltve!');
      fetchPosters();
    } catch (error: any) {
      toast.error('Hiba történt a feltöltés során');
      console.error(error);
    } finally {
      setUploadingZip(null);
    }
  };

  const handleDeletePosterImage = async (posterId: string, imageUrl: string) => {
    try {
      const poster = posters.find(p => p.id === posterId);
      if (!poster) return;

      const updatedImages = poster.poster_images.filter(url => url !== imageUrl);

      const { error } = await supabase
        .from('communication_posters')
        .update({ poster_images: updatedImages })
        .eq('id', posterId);

      if (error) throw error;

      // Delete from storage
      const filePath = imageUrl.split('/audit-assets/')[1];
      if (filePath) {
        await supabase.storage.from('audit-assets').remove([filePath]);
      }

      toast.success('Kép törölve!');
      fetchPosters();
    } catch (error: any) {
      toast.error('Hiba történt a törlés során');
      console.error(error);
    }
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'email': return <Mail className="h-5 w-5" />;
      case 'public_link': return <Link className="h-5 w-5" />;
      case 'qr_code': return <QrCode className="h-5 w-5" />;
      default: return null;
    }
  };

  const getTypeLabel = (type: string) => {
    switch (type) {
      case 'email': return 'Email sablon';
      case 'public_link': return 'Nyilvános link sablon';
      case 'qr_code': return 'QR kód sablon';
      default: return type;
    }
  };

  if (loading) {
    return <div className="flex items-center justify-center p-8">Betöltés...</div>;
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Kommunikációs sablonok</h2>
        <p className="text-muted-foreground">
          Szerkeszd a kommunikációs sablonokat, amiket a HR használni fog. Használd a {'{link}'} helyőrzőt.
        </p>
      </div>

      <div className="grid gap-6">
        {templates.map((template) => (
          <Card key={template.id} className="border-2">
            <CardHeader>
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  {getTypeIcon(template.template_type)}
                  <div>
                    <CardTitle className="flex items-center gap-2">
                      {getTypeLabel(template.template_type)}
                      {template.has_gift && (
                        <Badge variant="outline" className="gap-1">
                          <Gift className="h-3 w-3" />
                          Nyereményjátékkal
                        </Badge>
                      )}
                    </CardTitle>
                    <CardDescription className="mt-1">
                      {template.has_gift ? 'Nyereményjátékos verzió' : 'Alap verzió'}
                    </CardDescription>
                  </div>
                </div>
                {editingId === template.id ? (
                  <div className="flex gap-2">
                    <Button onClick={handleSave} size="sm">
                      <Save className="h-4 w-4 mr-1" />
                      Mentés
                    </Button>
                    <Button onClick={cancelEdit} variant="outline" size="sm">
                      Mégsem
                    </Button>
                  </div>
                ) : (
                  <Button onClick={() => startEdit(template)} variant="outline" size="sm">
                    <Pencil className="h-4 w-4 mr-1" />
                    Szerkesztés
                  </Button>
                )}
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              {editingId === template.id ? (
                <>
                  {template.template_type === 'email' && (
                    <div>
                      <Label htmlFor={`subject-${template.id}`}>Tárgy</Label>
                      <Input
                        id={`subject-${template.id}`}
                        value={formData.subject || ''}
                        onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                        placeholder="Email tárgy"
                      />
                    </div>
                  )}
                  <div>
                    <Label htmlFor={`content-${template.id}`}>Tartalom</Label>
                    <Textarea
                      id={`content-${template.id}`}
                      value={formData.content || ''}
                      onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                      rows={10}
                      className="font-mono text-sm"
                    />
                  </div>
                </>
              ) : (
                <>
                  {template.subject && (
                    <div>
                      <Label className="text-muted-foreground">Tárgy</Label>
                      <p className="text-sm mt-1 font-medium">{template.subject}</p>
                    </div>
                  )}
                  <div>
                    <Label className="text-muted-foreground">Tartalom</Label>
                    <pre className="text-sm mt-1 whitespace-pre-wrap font-sans bg-muted p-4 rounded-md">
                      {template.content}
                    </pre>
                  </div>
                </>
              )}

              {/* QR Code poster upload section */}
              {template.template_type === 'qr_code' && editingId !== template.id && (
                <div className="mt-6 pt-6 border-t space-y-4">
                  <div>
                    <Label className="text-base font-semibold">Plakát képek</Label>
                    <p className="text-sm text-muted-foreground">
                      Töltsd fel a plakát képeket, amiket a HR letölthet
                    </p>
                  </div>

                  {/* Display existing posters */}
                  {posters
                    .filter(p => p.has_gift === template.has_gift)
                    .map(poster => (
                      <div key={poster.id} className="space-y-3">
                        {poster.poster_images.length > 0 ? (
                          <>
                            <Label className="text-sm font-medium">Feltöltött plakátok:</Label>
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                              {poster.poster_images.map((imageUrl, idx) => (
                                <div key={idx} className="relative group">
                                  <img 
                                    src={imageUrl} 
                                    alt={`Plakát ${idx + 1}`}
                                    className="w-full aspect-[3/4] object-contain rounded-lg border bg-muted"
                                  />
                                  <Button
                                    size="icon"
                                    variant="destructive"
                                    className="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity h-6 w-6"
                                    onClick={() => handleDeletePosterImage(poster.id, imageUrl)}
                                  >
                                    <X className="h-3 w-3" />
                                  </Button>
                                </div>
                              ))}
                            </div>
                          </>
                        ) : (
                          <p className="text-sm text-muted-foreground italic">
                            Még nincsenek feltöltött plakátok
                          </p>
                        )}
                      </div>
                    ))}

                  {/* Show message if no poster record exists */}
                  {posters.filter(p => p.has_gift === template.has_gift).length === 0 && (
                    <p className="text-sm text-muted-foreground italic">
                      Még nincsenek feltöltött plakátok
                    </p>
                  )}

                  {/* Upload buttons */}
                  <div className="flex gap-3">
                    <Button
                      variant="outline"
                      size="sm"
                      disabled={uploadingPosters?.hasGift === template.has_gift}
                      onClick={() => {
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        input.multiple = true;
                        input.onchange = (e) => {
                          const target = e.target as HTMLInputElement;
                          handlePosterUpload(template.has_gift, target.files);
                        };
                        input.click();
                      }}
                    >
                      <Upload className="h-4 w-4 mr-1" />
                      {uploadingPosters?.hasGift === template.has_gift ? 'Feltöltés...' : 'Új plakátok feltöltése'}
                    </Button>

                    <Button
                      variant="outline"
                      size="sm"
                      disabled={uploadingZip?.hasGift === template.has_gift}
                      onClick={() => {
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.accept = '.zip';
                        input.onchange = (e) => {
                          const target = e.target as HTMLInputElement;
                          const file = target.files?.[0] || null;
                          handleZipUpload(template.has_gift, file);
                        };
                        input.click();
                      }}
                    >
                      <Upload className="h-4 w-4 mr-1" />
                      {uploadingZip?.hasGift === template.has_gift ? 'Feltöltés...' : 'Forrás ZIP feltöltése'}
                    </Button>
                  </div>

                  {posters.find(p => p.has_gift === template.has_gift)?.source_file_url && (
                    <p className="text-sm text-muted-foreground">
                      Forrás fájl feltöltve ✓
                    </p>
                  )}
                </div>
              )}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
};

export default CommunicationTemplates;
