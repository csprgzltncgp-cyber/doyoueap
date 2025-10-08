import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Label } from "@/components/ui/label";
import { Plus, Send, Trash2, Upload, X, Settings } from "lucide-react";
import * as XLSX from 'xlsx';

interface Subscriber {
  id: string;
  email: string;
  name: string | null;
  subscribed_at: string;
  is_active: boolean;
}

interface Campaign {
  id: string;
  subject: string;
  content: string;
  sent_at: string;
  recipient_count: number;
  status: string;
}

interface NewsletterTemplate {
  id: string;
  name: string;
  header_title: string;
  header_subtitle: string | null;
  footer_text: string;
  footer_company: string;
  footer_address: string | null;
  button_text: string;
  button_color: string;
  primary_color: string;
  background_color: string;
  is_default: boolean;
}

const NewsletterManagement = () => {
  const [subscribers, setSubscribers] = useState<Subscriber[]>([]);
  const [campaigns, setCampaigns] = useState<Campaign[]>([]);
  const [templates, setTemplates] = useState<NewsletterTemplate[]>([]);
  const [selectedTemplate, setSelectedTemplate] = useState<NewsletterTemplate | null>(null);
  const [isAddDialogOpen, setIsAddDialogOpen] = useState(false);
  const [isTemplateDialogOpen, setIsTemplateDialogOpen] = useState(false);
  const [newSubscriber, setNewSubscriber] = useState({ email: "", name: "" });
  const [newsletterSubject, setNewsletterSubject] = useState("");
  const [newsletterContent, setNewsletterContent] = useState("");
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [logoPreview, setLogoPreview] = useState<string | null>(null);
  const [featuredImage, setFeaturedImage] = useState<File | null>(null);
  const [featuredImagePreview, setFeaturedImagePreview] = useState<string | null>(null);
  const [templateForm, setTemplateForm] = useState({
    name: "",
    header_title: "DoYouEAP Hírlevél",
    header_subtitle: "",
    footer_text: "Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.",
    footer_company: "DoYouEAP",
    footer_address: "",
    button_text: "Tovább a cikkhez",
    button_color: "#0ea5e9",
    primary_color: "#0ea5e9",
    background_color: "#f8fafc",
  });

  useEffect(() => {
    fetchSubscribers();
    fetchCampaigns();
    fetchTemplates();
  }, []);

  const fetchSubscribers = async () => {
    const { data, error } = await supabase
      .from("newsletter_subscribers")
      .select("*")
      .eq("is_active", true)
      .order("subscribed_at", { ascending: false });

    if (error) {
      toast.error("Hiba a feliratkozók betöltésekor");
      return;
    }

    setSubscribers(data || []);
  };

  const fetchCampaigns = async () => {
    const { data, error } = await supabase
      .from("newsletter_campaigns")
      .select("*")
      .order("sent_at", { ascending: false });

    if (error) {
      toast.error("Hiba a kampányok betöltésekor");
      return;
    }

    setCampaigns(data || []);
  };

  const fetchTemplates = async () => {
    const { data, error } = await supabase
      .from("newsletter_templates")
      .select("*")
      .order("is_default", { ascending: false });

    if (error) {
      toast.error("Hiba a sablonok betöltésekor");
      return;
    }

    setTemplates(data || []);
    const defaultTemplate = data?.find(t => t.is_default);
    if (defaultTemplate) {
      setSelectedTemplate(defaultTemplate);
    }
  };

  const handleAddSubscriber = async () => {
    if (!newSubscriber.email) {
      toast.error("Kérem, adja meg az email címet");
      return;
    }

    const { error } = await supabase.from("newsletter_subscribers").insert({
      email: newSubscriber.email,
      name: newSubscriber.name || null,
    });

    if (error) {
      toast.error("Hiba a feliratkozó hozzáadásakor");
      return;
    }

    toast.success("Feliratkozó hozzáadva!");
    setNewSubscriber({ email: "", name: "" });
    setIsAddDialogOpen(false);
    fetchSubscribers();
  };

  const handleDeleteSubscriber = async (id: string) => {
    if (!confirm("Biztosan törölni szeretné ezt a feliratkozót?")) return;

    const { error } = await supabase
      .from("newsletter_subscribers")
      .update({ is_active: false })
      .eq("id", id);

    if (error) {
      toast.error("Hiba a törlés során");
      return;
    }

    toast.success("Feliratkozó törölve!");
    fetchSubscribers();
  };

  const handleExcelUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    try {
      const data = await file.arrayBuffer();
      const workbook = XLSX.read(data);
      const worksheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(worksheet) as any[];

      const subscribersToAdd = jsonData
        .filter((row: any) => row.email || row.Email)
        .map((row: any) => ({
          email: row.email || row.Email,
          name: row.name || row.Name || null,
        }));

      if (subscribersToAdd.length === 0) {
        toast.error("Nem található email cím az Excel fájlban");
        return;
      }

      const { error } = await supabase
        .from("newsletter_subscribers")
        .insert(subscribersToAdd);

      if (error) {
        toast.error("Hiba az importálás során");
        return;
      }

      toast.success(`${subscribersToAdd.length} feliratkozó importálva!`);
      fetchSubscribers();
    } catch (error) {
      console.error("Excel upload error:", error);
      toast.error("Hiba az Excel feldolgozása során");
    }
  };

  const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setLogoFile(file);
    const reader = new FileReader();
    reader.onloadend = () => setLogoPreview(reader.result as string);
    reader.readAsDataURL(file);
  };

  const handleFeaturedImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setFeaturedImage(file);
    const reader = new FileReader();
    reader.onloadend = () => setFeaturedImagePreview(reader.result as string);
    reader.readAsDataURL(file);
  };

  const uploadNewsletterAsset = async (file: File, type: string): Promise<string> => {
    const fileExt = file.name.split('.').pop();
    const fileName = `${type}-${Date.now()}.${fileExt}`;

    const { error: uploadError } = await supabase.storage
      .from('newsletter-assets')
      .upload(fileName, file);

    if (uploadError) throw uploadError;

    const { data } = supabase.storage
      .from('newsletter-assets')
      .getPublicUrl(fileName);

    return data.publicUrl;
  };

  const handleSendNewsletter = async () => {
    if (!newsletterSubject || !newsletterContent) {
      toast.error("Kérem, töltse ki a tárgy és tartalom mezőket");
      return;
    }

    if (!selectedTemplate) {
      toast.error("Kérem, válasszon sablont");
      return;
    }

    const activeSubscribers = subscribers.filter(s => s.is_active);
    if (activeSubscribers.length === 0) {
      toast.error("Nincsenek aktív feliratkozók");
      return;
    }

    try {
      let logoUrl = null;
      let featuredImageUrl = null;

      if (logoFile) {
        logoUrl = await uploadNewsletterAsset(logoFile, "logo");
      }

      if (featuredImage) {
        featuredImageUrl = await uploadNewsletterAsset(featuredImage, "featured");
      }

      const { error: functionError } = await supabase.functions.invoke(
        "send-newsletter",
        {
          body: {
            subject: newsletterSubject,
            content: newsletterContent,
            subscribers: activeSubscribers,
            logoUrl,
            featuredImageUrl,
            template: selectedTemplate,
          },
        }
      );

      if (functionError) throw functionError;

      const { data: userData } = await supabase.auth.getUser();
      await supabase.from("newsletter_campaigns").insert({
        subject: newsletterSubject,
        content: newsletterContent,
        recipient_count: activeSubscribers.length,
        sent_by: userData.user?.id,
        template_id: selectedTemplate.id,
      });

      toast.success(`Hírlevél sikeresen elküldve ${activeSubscribers.length} címzettnek!`);
      setNewsletterContent("");
      setLogoFile(null);
      setLogoPreview(null);
      setFeaturedImage(null);
      setFeaturedImagePreview(null);
      fetchCampaigns();
    } catch (error: any) {
      console.error("Error sending newsletter:", error);
      toast.error("Hiba a hírlevél küldése közben");
    }
  };

  const handleSaveTemplate = async () => {
    if (!templateForm.name) {
      toast.error("Kérem, adja meg a sablon nevét");
      return;
    }

    try {
      const { data: userData } = await supabase.auth.getUser();
      const { data, error } = await supabase
        .from("newsletter_templates")
        .insert({
          ...templateForm,
          created_by: userData.user?.id,
        })
        .select()
        .single();

      if (error) throw error;

      toast.success("Sablon sikeresen mentve!");
      setIsTemplateDialogOpen(false);
      setTemplateForm({
        name: "",
        header_title: "DoYouEAP Hírlevél",
        header_subtitle: "",
        footer_text: "Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.",
        footer_company: "DoYouEAP",
        footer_address: "",
        button_text: "Tovább a cikkhez",
        button_color: "#0ea5e9",
        primary_color: "#0ea5e9",
        background_color: "#f8fafc",
      });
      fetchTemplates();
    } catch (error: any) {
      console.error("Error saving template:", error);
      toast.error("Hiba a sablon mentésekor");
    }
  };

  return (
    <div className="container mx-auto py-8 space-y-8">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Hírlevél kezelés</h1>
        <Dialog open={isTemplateDialogOpen} onOpenChange={setIsTemplateDialogOpen}>
          <DialogTrigger asChild>
            <Button variant="outline">
              <Settings className="mr-2 h-4 w-4" />
              Sablon kezelés
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>Hírlevél sablon szerkesztése</DialogTitle>
            </DialogHeader>
            <Tabs defaultValue="content" className="w-full">
              <TabsList className="grid w-full grid-cols-3">
                <TabsTrigger value="content">Tartalom</TabsTrigger>
                <TabsTrigger value="style">Stílus</TabsTrigger>
                <TabsTrigger value="footer">Lábléc</TabsTrigger>
              </TabsList>
              
              <TabsContent value="content" className="space-y-4">
                <div>
                  <Label>Sablon neve</Label>
                  <Input
                    value={templateForm.name}
                    onChange={(e) => setTemplateForm({ ...templateForm, name: e.target.value })}
                    placeholder="pl. Alap sablon"
                  />
                </div>
                <div>
                  <Label>Fejléc cím</Label>
                  <Input
                    value={templateForm.header_title}
                    onChange={(e) => setTemplateForm({ ...templateForm, header_title: e.target.value })}
                  />
                </div>
                <div>
                  <Label>Fejléc alcím (opcionális)</Label>
                  <Input
                    value={templateForm.header_subtitle}
                    onChange={(e) => setTemplateForm({ ...templateForm, header_subtitle: e.target.value })}
                    placeholder="pl. Hírek és információk"
                  />
                </div>
                <div>
                  <Label>Gomb szövege</Label>
                  <Input
                    value={templateForm.button_text}
                    onChange={(e) => setTemplateForm({ ...templateForm, button_text: e.target.value })}
                  />
                </div>
              </TabsContent>

              <TabsContent value="style" className="space-y-4">
                <div>
                  <Label>Elsődleges szín</Label>
                  <div className="flex gap-2">
                    <Input
                      type="color"
                      value={templateForm.primary_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, primary_color: e.target.value })}
                      className="w-20 h-10"
                    />
                    <Input
                      value={templateForm.primary_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, primary_color: e.target.value })}
                      placeholder="#0ea5e9"
                    />
                  </div>
                </div>
                <div>
                  <Label>Gomb színe</Label>
                  <div className="flex gap-2">
                    <Input
                      type="color"
                      value={templateForm.button_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, button_color: e.target.value })}
                      className="w-20 h-10"
                    />
                    <Input
                      value={templateForm.button_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, button_color: e.target.value })}
                      placeholder="#0ea5e9"
                    />
                  </div>
                </div>
                <div>
                  <Label>Háttérszín</Label>
                  <div className="flex gap-2">
                    <Input
                      type="color"
                      value={templateForm.background_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, background_color: e.target.value })}
                      className="w-20 h-10"
                    />
                    <Input
                      value={templateForm.background_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, background_color: e.target.value })}
                      placeholder="#f8fafc"
                    />
                  </div>
                </div>
              </TabsContent>

              <TabsContent value="footer" className="space-y-4">
                <div>
                  <Label>Lábléc szöveg</Label>
                  <Textarea
                    value={templateForm.footer_text}
                    onChange={(e) => setTemplateForm({ ...templateForm, footer_text: e.target.value })}
                    rows={2}
                  />
                </div>
                <div>
                  <Label>Cégnév</Label>
                  <Input
                    value={templateForm.footer_company}
                    onChange={(e) => setTemplateForm({ ...templateForm, footer_company: e.target.value })}
                  />
                </div>
                <div>
                  <Label>Cím (opcionális)</Label>
                  <Input
                    value={templateForm.footer_address}
                    onChange={(e) => setTemplateForm({ ...templateForm, footer_address: e.target.value })}
                    placeholder="pl. 1234 Budapest, Példa utca 1."
                  />
                </div>
              </TabsContent>
            </Tabs>
            <div className="flex justify-end gap-2 pt-4">
              <Button variant="outline" onClick={() => setIsTemplateDialogOpen(false)}>
                Mégse
              </Button>
              <Button onClick={handleSaveTemplate}>Sablon mentése</Button>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Hírlevél küldése</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label>Sablon kiválasztása</Label>
            <select
              className="w-full p-2 border rounded-md"
              value={selectedTemplate?.id || ""}
              onChange={(e) => {
                const template = templates.find(t => t.id === e.target.value);
                setSelectedTemplate(template || null);
              }}
            >
              <option value="">Válasszon sablont...</option>
              {templates.map((template) => (
                <option key={template.id} value={template.id}>
                  {template.name} {template.is_default && "(Alapértelmezett)"}
                </option>
              ))}
            </select>
          </div>

          <div>
            <Label>Tárgy</Label>
            <Input
              value={newsletterSubject}
              onChange={(e) => setNewsletterSubject(e.target.value)}
              placeholder="Hírlevél tárgya..."
            />
          </div>

          <div>
            <Label>Tartalom</Label>
            <Textarea
              value={newsletterContent}
              onChange={(e) => setNewsletterContent(e.target.value)}
              placeholder="Írja ide a hírlevél tartalmát... Használjon egyszerű formázást: **félkövér**, *dőlt*, - lista"
              rows={10}
            />
            <p className="text-sm text-muted-foreground mt-1">
              Egyszerű formázás: **félkövér**, *dőlt*, új sor = új bekezdés
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label>Logo (opcionális)</Label>
              <div className="space-y-2">
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleLogoChange}
                  className="block w-full text-sm"
                />
                {logoPreview && (
                  <div className="relative">
                    <img src={logoPreview} alt="Logo előnézet" className="h-20 object-contain" />
                    <Button
                      variant="ghost"
                      size="sm"
                      className="absolute top-0 right-0"
                      onClick={() => {
                        setLogoFile(null);
                        setLogoPreview(null);
                      }}
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                )}
              </div>
            </div>

            <div>
              <Label>Kiemelt kép (opcionális)</Label>
              <div className="space-y-2">
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleFeaturedImageChange}
                  className="block w-full text-sm"
                />
                {featuredImagePreview && (
                  <div className="relative">
                    <img src={featuredImagePreview} alt="Kép előnézet" className="h-20 object-contain" />
                    <Button
                      variant="ghost"
                      size="sm"
                      className="absolute top-0 right-0"
                      onClick={() => {
                        setFeaturedImage(null);
                        setFeaturedImagePreview(null);
                      }}
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                )}
              </div>
            </div>
          </div>

          <Button onClick={handleSendNewsletter} className="w-full">
            <Send className="mr-2 h-4 w-4" />
            Hírlevél küldése ({subscribers.length} címzett)
          </Button>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <CardTitle>Feliratkozók ({subscribers.length})</CardTitle>
            <div className="flex gap-2">
              <Dialog open={isAddDialogOpen} onOpenChange={setIsAddDialogOpen}>
                <DialogTrigger asChild>
                  <Button>
                    <Plus className="mr-2 h-4 w-4" />
                    Új feliratkozó
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>Új feliratkozó hozzáadása</DialogTitle>
                  </DialogHeader>
                  <div className="space-y-4">
                    <div>
                      <Label>Email cím *</Label>
                      <Input
                        type="email"
                        value={newSubscriber.email}
                        onChange={(e) => setNewSubscriber({ ...newSubscriber, email: e.target.value })}
                        placeholder="email@example.com"
                      />
                    </div>
                    <div>
                      <Label>Név (opcionális)</Label>
                      <Input
                        value={newSubscriber.name}
                        onChange={(e) => setNewSubscriber({ ...newSubscriber, name: e.target.value })}
                        placeholder="Teljes név"
                      />
                    </div>
                    <Button onClick={handleAddSubscriber} className="w-full">
                      Hozzáadás
                    </Button>
                  </div>
                </DialogContent>
              </Dialog>

              <Button variant="outline">
                <Upload className="mr-2 h-4 w-4" />
                <label className="cursor-pointer">
                  Excel import
                  <input
                    type="file"
                    accept=".xlsx,.xls"
                    onChange={handleExcelUpload}
                    className="hidden"
                  />
                </label>
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Email</TableHead>
                <TableHead>Név</TableHead>
                <TableHead>Feliratkozás dátuma</TableHead>
                <TableHead>Műveletek</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {subscribers.map((subscriber) => (
                <TableRow key={subscriber.id}>
                  <TableCell>{subscriber.email}</TableCell>
                  <TableCell>{subscriber.name || "-"}</TableCell>
                  <TableCell>{new Date(subscriber.subscribed_at).toLocaleDateString()}</TableCell>
                  <TableCell>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDeleteSubscriber(subscriber.id)}
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

      <Card>
        <CardHeader>
          <CardTitle>Kampány előzmények</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Tárgy</TableHead>
                <TableHead>Küldés ideje</TableHead>
                <TableHead>Címzettek</TableHead>
                <TableHead>Státusz</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {campaigns.map((campaign) => (
                <TableRow key={campaign.id}>
                  <TableCell>{campaign.subject}</TableCell>
                  <TableCell>{new Date(campaign.sent_at).toLocaleString()}</TableCell>
                  <TableCell>{campaign.recipient_count}</TableCell>
                  <TableCell>{campaign.status}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
};

export default NewsletterManagement;
