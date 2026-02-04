import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Switch } from "@/components/ui/switch";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { toast } from "sonner";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Plus, Edit, Trash2, Eye, Upload, X } from "lucide-react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { format } from "date-fns";

interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  author: string;
  published_date: string;
  image_url: string | null;
  category: string;
  is_featured: boolean;
  is_published: boolean;
  created_at: string;
}

export default function MagazineManagement() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [editingArticle, setEditingArticle] = useState<Article | null>(null);
  const [uploading, setUploading] = useState(false);
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [imagePreview, setImagePreview] = useState<string | null>(null);
  const [formData, setFormData] = useState({
    title: "",
    slug: "",
    excerpt: "",
    content: "",
    author: "",
    published_date: new Date().toISOString().split('T')[0],
    image_url: "",
    category: "EAP",
    is_featured: false,
    is_published: true,
  });

  useEffect(() => {
    fetchArticles();
  }, []);

  const fetchArticles = async () => {
    try {
      const { data, error } = await supabase
        .from("magazine_articles")
        .select("*")
        .order("published_date", { ascending: false });

      if (error) throw error;
      setArticles(data || []);
    } catch (error) {
      console.error("Error fetching articles:", error);
      toast.error("Hiba a cikkek betöltésekor");
    } finally {
      setLoading(false);
    }
  };

  const generateSlug = (title: string) => {
    return title
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/\s+/g, "-")
      .replace(/-+/g, "-")
      .trim();
  };

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    console.log('handleImageChange called');
    const file = e.target.files?.[0];
    console.log('Selected file:', file);
    
    if (file) {
      if (file.size > 5 * 1024 * 1024) {
        toast.error("A kép mérete maximum 5MB lehet");
        return;
      }
      if (!file.type.startsWith('image/')) {
        toast.error("Csak képfájlokat lehet feltölteni");
        return;
      }
      
      console.log('Setting image file:', file.name);
      setImageFile(file);
      
      const reader = new FileReader();
      reader.onloadend = () => {
        console.log('Preview loaded');
        setImagePreview(reader.result as string);
      };
      reader.readAsDataURL(file);
    } else {
      console.log('No file selected');
    }
  };

  const uploadImage = async (file: File): Promise<string> => {
    const fileExt = file.name.split('.').pop();
    const fileName = `${Math.random().toString(36).substring(2)}-${Date.now()}.${fileExt}`;
    const filePath = `${fileName}`;

    const { error: uploadError } = await supabase.storage
      .from('magazine-images')
      .upload(filePath, file, {
        cacheControl: '3600',
        upsert: false
      });

    if (uploadError) throw uploadError;

    const { data: { publicUrl } } = supabase.storage
      .from('magazine-images')
      .getPublicUrl(filePath);

    return publicUrl;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setUploading(true);

    try {
      let imageUrl = formData.image_url;

      // Upload new image if selected
      if (imageFile) {
        console.log('Uploading image:', imageFile.name);
        imageUrl = await uploadImage(imageFile);
        console.log('Image uploaded, URL:', imageUrl);
      }

      const slug = formData.slug || generateSlug(formData.title);
      const dataToSave = { ...formData, slug, image_url: imageUrl };

      if (editingArticle) {
        const { error } = await supabase
          .from("magazine_articles")
          .update(dataToSave)
          .eq("id", editingArticle.id);

        if (error) throw error;
        toast.success("Cikk sikeresen frissítve");
      } else {
        const { error } = await supabase
          .from("magazine_articles")
          .insert([dataToSave]);

        if (error) throw error;
        toast.success("Cikk sikeresen létrehozva");
      }

      setOpen(false);
      await fetchArticles();
      resetForm();
    } catch (error) {
      console.error("Error saving article:", error);
      toast.error("Hiba történt a cikk mentése közben");
    } finally {
      setUploading(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm("Biztosan törölni szeretnéd ezt a cikket?")) return;

    try {
      const { error } = await supabase
        .from("magazine_articles")
        .delete()
        .eq("id", id);

      if (error) throw error;
      toast.success("Cikk sikeresen törölve");
      fetchArticles();
    } catch (error) {
      console.error("Error deleting article:", error);
      toast.error("Hiba a cikk törlésekor");
    }
  };

  const resetForm = () => {
    console.log('resetForm called');
    setFormData({
      title: "",
      slug: "",
      excerpt: "",
      content: "",
      author: "",
      published_date: new Date().toISOString().split('T')[0],
      image_url: "",
      category: "EAP",
      is_featured: false,
      is_published: true,
    });
    setEditingArticle(null);
    setImageFile(null);
    setImagePreview(null);
  };

  const openEditDialog = (article: Article) => {
    console.log('openEditDialog called with article:', article);
    setEditingArticle(article);
    setFormData({
      title: article.title,
      slug: article.slug,
      excerpt: article.excerpt,
      content: article.content,
      author: article.author,
      published_date: article.published_date.split('T')[0],
      image_url: article.image_url || "",
      category: article.category,
      is_featured: article.is_featured,
      is_published: article.is_published,
    });
    // Set existing image as preview if available
    if (article.image_url) {
      console.log('Setting existing image preview:', article.image_url);
      setImagePreview(article.image_url);
    } else {
      setImagePreview(null);
    }
    setImageFile(null);
    setOpen(true);
  };

  if (loading) {
    return <div className="p-6">Betöltés...</div>;
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Magazin cikkek kezelése</h1>
        <Dialog open={open} onOpenChange={(isOpen) => {
          setOpen(isOpen);
          if (!isOpen) {
            resetForm();
          }
        }}>
          <DialogTrigger asChild>
            <Button onClick={() => {
              resetForm();
              setOpen(true);
            }}>
              <Plus className="mr-2 h-4 w-4" />
              Új cikk
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>{editingArticle ? "Cikk szerkesztése" : "Új cikk hozzáadása"}</DialogTitle>
            </DialogHeader>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="title">Cím*</Label>
                <Input
                  id="title"
                  value={formData.title}
                  onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                  required
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="slug">Slug (automatikusan generálódik ha üres)</Label>
                <Input
                  id="slug"
                  value={formData.slug}
                  onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                  placeholder={generateSlug(formData.title)}
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="category">Kategória*</Label>
                  <Select
                    value={formData.category}
                    onValueChange={(value) => setFormData({ ...formData, category: value })}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Válassz kategóriát" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Alapok">Alapok</SelectItem>
                      <SelectItem value="Mérés">Mérés</SelectItem>
                      <SelectItem value="Kultúra">Kultúra</SelectItem>
                      <SelectItem value="Jövő">Jövő</SelectItem>
                      <SelectItem value="EAP">EAP</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="author">Szerző*</Label>
                  <Input
                    id="author"
                    value={formData.author}
                    onChange={(e) => setFormData({ ...formData, author: e.target.value })}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="published_date">Publikálás dátuma*</Label>
                  <Input
                    id="published_date"
                    type="date"
                    value={formData.published_date}
                    onChange={(e) => setFormData({ ...formData, published_date: e.target.value })}
                    required
                  />
                </div>
              </div>
              <div className="space-y-2">
                <Label htmlFor="image">Borítókép</Label>
                <div className="flex flex-col gap-3">
                  {(imagePreview || formData.image_url) && (
                    <div className="relative w-full aspect-video rounded-lg overflow-hidden border">
                      <img 
                        src={imagePreview || formData.image_url} 
                        alt="Preview" 
                        className="w-full h-full object-cover"
                      />
                      <Button
                        type="button"
                        variant="destructive"
                        size="icon"
                        className="absolute top-2 right-2"
                        onClick={() => {
                          setImageFile(null);
                          setImagePreview(null);
                          setFormData({ ...formData, image_url: "" });
                        }}
                      >
                        <X className="h-4 w-4" />
                      </Button>
                    </div>
                  )}
                  <div className="flex items-center gap-2">
                    <Input
                      id="image"
                      type="file"
                      accept="image/*"
                      onChange={handleImageChange}
                      className="flex-1"
                    />
                    <Upload className="h-5 w-5 text-muted-foreground" />
                  </div>
                  <p className="text-xs text-muted-foreground">
                    {(imagePreview || formData.image_url) ? 'Új kép feltöltése lecseréli a meglévőt' : 'Maximum 5MB, JPG, PNG vagy WEBP formátum'}
                  </p>
                </div>
              </div>
              <div className="space-y-2">
                <Label htmlFor="excerpt">Kivonat*</Label>
                <Textarea
                  id="excerpt"
                  value={formData.excerpt}
                  onChange={(e) => setFormData({ ...formData, excerpt: e.target.value })}
                  rows={3}
                  required
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="content">Tartalom*</Label>
                <Textarea
                  id="content"
                  value={formData.content}
                  onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                  rows={10}
                  required
                />
              </div>
              <div className="flex gap-6">
                <div className="flex items-center space-x-2">
                  <Switch
                    id="is_featured"
                    checked={formData.is_featured}
                    onCheckedChange={(checked) => setFormData({ ...formData, is_featured: checked })}
                  />
                  <Label htmlFor="is_featured">Kiemelt cikk</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <Switch
                    id="is_published"
                    checked={formData.is_published}
                    onCheckedChange={(checked) => setFormData({ ...formData, is_published: checked })}
                  />
                  <Label htmlFor="is_published">Publikált</Label>
                </div>
              </div>
              <div className="flex gap-2 justify-end">
                <Button type="button" variant="outline" onClick={() => setOpen(false)}>
                  Mégse
                </Button>
                <Button type="submit" disabled={uploading}>
                  {uploading ? "Mentés..." : editingArticle ? "Frissítés" : "Létrehozás"}
                </Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Cikkek</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Cím</TableHead>
                <TableHead>Kategória</TableHead>
                <TableHead>Szerző</TableHead>
                <TableHead>Dátum</TableHead>
                <TableHead>Státusz</TableHead>
                <TableHead>Kiemelt</TableHead>
                <TableHead className="text-right">Műveletek</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {articles.map((article) => (
                <TableRow key={article.id}>
                  <TableCell className="font-medium">{article.title}</TableCell>
                  <TableCell>
                    <Badge variant="outline">{article.category}</Badge>
                  </TableCell>
                  <TableCell>{article.author}</TableCell>
                  <TableCell>{format(new Date(article.published_date), 'yyyy.MM.dd')}</TableCell>
                  <TableCell>
                    <span className={`px-2 py-1 rounded text-xs ${
                      article.is_published ? 'bg-chart-2/20 text-chart-1' : 'bg-muted text-muted-foreground'
                    }`}>
                      {article.is_published ? 'Publikált' : 'Piszkozat'}
                    </span>
                  </TableCell>
                  <TableCell>
                    {article.is_featured && (
                      <span className="px-2 py-1 rounded text-xs bg-[hsl(var(--magazine-yellow))]/20 text-[hsl(var(--chart-1))]">
                        Kiemelt
                      </span>
                    )}
                  </TableCell>
                  <TableCell className="text-right">
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => window.open(`/magazin/${article.slug}`, '_blank')}
                    >
                      <Eye className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => openEditDialog(article)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDelete(article.id)}
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
