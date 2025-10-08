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
import { Checkbox } from "@/components/ui/checkbox";
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

interface FooterLink {
  text: string;
  url: string;
}

interface NewsletterTemplate {
  id: string;
  name: string;
  header_title: string;
  header_subtitle: string | null;
  footer_text: string;
  footer_company: string;
  footer_address: string | null;
  primary_color: string;
  background_color: string;
  is_default: boolean;
  greeting_text: string;
  footer_links: FooterLink[];
  header_color: string;
  footer_color: string;
  header_gradient_1?: string | null;
  header_gradient_2?: string | null;
  greeting_text_color?: string | null;
  greeting_bg_color?: string | null;
  footer_gradient?: string | null;
  sender_email: string;
  sender_name: string;
  header_color_1: string;
  header_color_2: string;
  logo_url: string | null;
  featured_image_url: string | null;
  footer_logo_url: string | null;
  footer_link_color: string;
  extra_content_border_color: string;
  extra_content_bg_color: string;
  content_button_text?: string;
  content_button_text_color?: string;
  content_button_color?: string;
  content_button_shadow_color?: string;
  content_button_url?: string | null;
  show_content_button?: boolean;
  extra_button_text?: string;
  extra_button_text_color?: string;
  extra_button_color?: string;
  extra_button_shadow_color?: string;
  extra_button_url?: string | null;
  show_extra_button?: boolean;
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
  const [newsletterExtraContent, setNewsletterExtraContent] = useState("");
  const [editingTemplateId, setEditingTemplateId] = useState<string | null>(null);
  const [templateLogoFile, setTemplateLogoFile] = useState<File | null>(null);
  const [templateLogoPreview, setTemplateLogoPreview] = useState<string | null>(null);
  const [templateFeaturedImage, setTemplateFeaturedImage] = useState<File | null>(null);
  const [templateFeaturedImagePreview, setTemplateFeaturedImagePreview] = useState<string | null>(null);
  const [templateFooterLogoFile, setTemplateFooterLogoFile] = useState<File | null>(null);
  const [templateFooterLogoPreview, setTemplateFooterLogoPreview] = useState<string | null>(null);
  const [templateForm, setTemplateForm] = useState({
    name: "",
    header_title: "DoYouEAP Hírlevél",
    footer_text: "Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.",
    footer_company: "DoYouEAP",
    footer_address: "",
    primary_color: "#0ea5e9",
    background_color: "#f8fafc",
    greeting_text: "Kedves Feliratkozónk!",
    footer_link_color: "#ffffff",
    extra_content_border_color: "#0ea5e9",
    extra_content_bg_color: "#0ea5e915",
    footer_links: [] as FooterLink[],
    header_color: "#0ea5e9",
    footer_color: "#1a1a1a",
    header_gradient_1: "",
    header_gradient_2: "",
    greeting_text_color: "#ffffff",
    greeting_bg_color: "#0ea5e9",
    footer_gradient: "",
    sender_email: "noreply@doyoueap.com",
    sender_name: "DoYouEAP",
    header_color_1: "#0ea5e9",
    header_color_2: "#0ea5e9",
    content_button_text: "Olvass tovább",
    content_button_text_color: "#ffffff",
    content_button_color: "#0ea5e9",
    content_button_shadow_color: "#0ea5e933",
    content_button_url: "",
    show_content_button: false,
    extra_button_text: "További információ",
    extra_button_text_color: "#ffffff",
    extra_button_color: "#0ea5e9",
    extra_button_shadow_color: "#0ea5e933",
    extra_button_url: "",
    show_extra_button: false,
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

  const handleDeleteTemplate = async (id: string) => {
    if (!confirm("Biztosan törölni szeretné ezt a sablont?")) {
      return;
    }

    const { error } = await supabase
      .from("newsletter_templates")
      .delete()
      .eq("id", id);

    if (error) {
      toast.error("Hiba a sablon törlése során");
      return;
    }

    toast.success("Sablon törölve!");
    fetchTemplates();
  };

  const handleDeleteCampaign = async (id: string) => {
    if (!confirm("Biztosan törölni szeretné ezt a kampányt?")) {
      return;
    }

    const { error } = await supabase
      .from("newsletter_campaigns")
      .delete()
      .eq("id", id);

    if (error) {
      toast.error("Hiba a kampány törlése során");
      return;
    }

    toast.success("Kampány törölve!");
    fetchCampaigns();
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

  const handleTemplateLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setTemplateLogoFile(file);
    const reader = new FileReader();
    reader.onloadend = () => setTemplateLogoPreview(reader.result as string);
    reader.readAsDataURL(file);
  };

  const handleTemplateFeaturedImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setTemplateFeaturedImage(file);
    const reader = new FileReader();
    reader.onloadend = () => setTemplateFeaturedImagePreview(reader.result as string);
    reader.readAsDataURL(file);
  };

  const handleTemplateFooterLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setTemplateFooterLogoFile(file);
    const reader = new FileReader();
    reader.onloadend = () => setTemplateFooterLogoPreview(reader.result as string);
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
      const { error: functionError } = await supabase.functions.invoke(
        "send-newsletter",
        {
          body: {
            subject: newsletterSubject,
            content: newsletterContent,
            extraContent: newsletterExtraContent,
            subscribers: activeSubscribers,
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
      setNewsletterExtraContent("");
      setNewsletterSubject("");
      fetchCampaigns();
    } catch (error: any) {
      console.error("Error sending newsletter:", error);
      toast.error("Hiba a hírlevél küldése közben");
    }
  };

  const handleSaveTemplate = async () => {
    console.log("handleSaveTemplate started", { templateForm, editingTemplateId });
    
    if (!templateForm.name) {
      toast.error("Kérem, adja meg a sablon nevét");
      return;
    }

    try {
      const { data: userData } = await supabase.auth.getUser();
      console.log("User data:", userData);
      
      let logoUrl = editingTemplateId ? templates.find(t => t.id === editingTemplateId)?.logo_url : null;
      let featuredImageUrl = editingTemplateId ? templates.find(t => t.id === editingTemplateId)?.featured_image_url : null;
      let footerLogoUrl = editingTemplateId ? templates.find(t => t.id === editingTemplateId)?.footer_logo_url : null;

      console.log("Initial URLs:", { logoUrl, featuredImageUrl, footerLogoUrl });

      if (templateLogoFile) {
        console.log("Uploading template logo...");
        logoUrl = await uploadNewsletterAsset(templateLogoFile, "template-logo");
        console.log("Logo uploaded:", logoUrl);
      }

      if (templateFeaturedImage) {
        console.log("Uploading featured image...");
        featuredImageUrl = await uploadNewsletterAsset(templateFeaturedImage, "template-featured");
        console.log("Featured image uploaded:", featuredImageUrl);
      }

      if (templateFooterLogoFile) {
        console.log("Uploading footer logo...");
        footerLogoUrl = await uploadNewsletterAsset(templateFooterLogoFile, "template-footer-logo");
        console.log("Footer logo uploaded:", footerLogoUrl);
      }
      
      const templateData = {
        ...templateForm,
        footer_links: templateForm.footer_links,
        logo_url: logoUrl,
        featured_image_url: featuredImageUrl,
        footer_logo_url: footerLogoUrl,
      };

      console.log("Template data to save:", templateData);

      if (editingTemplateId) {
        // Update existing template
        console.log("Updating template:", editingTemplateId);
        const { error } = await supabase
          .from("newsletter_templates")
          .update(templateData as any)
          .eq("id", editingTemplateId);

        if (error) {
          console.error("Update error:", error);
          throw error;
        }
        toast.success("Sablon sikeresen frissítve!");
      } else {
        // Insert new template
        console.log("Inserting new template");
        const { error } = await supabase
          .from("newsletter_templates")
          .insert({
            ...templateData,
            created_by: userData.user?.id,
          } as any);

        if (error) {
          console.error("Insert error:", error);
          throw error;
        }
        toast.success("Sablon sikeresen mentve!");
      }

      setIsTemplateDialogOpen(false);
      setEditingTemplateId(null);
      setTemplateLogoFile(null);
      setTemplateLogoPreview(null);
      setTemplateFeaturedImage(null);
      setTemplateFeaturedImagePreview(null);
      setTemplateFooterLogoFile(null);
      setTemplateFooterLogoPreview(null);
      setTemplateForm({
        name: "",
        header_title: "DoYouEAP Hírlevél",
        footer_text: "Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.",
        footer_company: "DoYouEAP",
        footer_address: "",
        primary_color: "#0ea5e9",
        background_color: "#f8fafc",
        greeting_text: "Kedves Feliratkozónk!",
        footer_links: [],
        header_color: "#0ea5e9",
        footer_color: "#1a1a1a",
        header_gradient_1: "",
        header_gradient_2: "",
        greeting_text_color: "#ffffff",
        greeting_bg_color: "#0ea5e9",
        footer_gradient: "",
        sender_email: "noreply@doyoueap.com",
        sender_name: "DoYouEAP",
        header_color_1: "#0ea5e9",
        header_color_2: "#0ea5e9",
        footer_link_color: "#ffffff",
        extra_content_border_color: "#0ea5e9",
        extra_content_bg_color: "#0ea5e915",
        content_button_text: "Olvass tovább",
        content_button_text_color: "#ffffff",
        content_button_color: "#0ea5e9",
        content_button_shadow_color: "#0ea5e933",
        content_button_url: "",
        show_content_button: false,
        extra_button_text: "További információ",
        extra_button_text_color: "#ffffff",
        extra_button_color: "#0ea5e9",
        extra_button_shadow_color: "#0ea5e933",
        extra_button_url: "",
        show_extra_button: false,
      });
      
      // Reload templates and update selectedTemplate if it was edited
      await fetchTemplates();
      
      // If we edited the currently selected template, refresh it
      if (editingTemplateId && selectedTemplate?.id === editingTemplateId) {
        const { data: updatedTemplate } = await supabase
          .from("newsletter_templates")
          .select("*")
          .eq("id", editingTemplateId)
          .single();
        
        if (updatedTemplate) {
          setSelectedTemplate(updatedTemplate as any);
        }
      }
      
      console.log("Template save completed successfully");
    } catch (error: any) {
      console.error("Error saving template:", error);
      toast.error(`Hiba a sablon mentésekor: ${error.message || 'Ismeretlen hiba'}`);
    }
  };

  const handleEditTemplate = (template: NewsletterTemplate) => {
    setEditingTemplateId(template.id);
    setTemplateForm({
      name: template.name,
      header_title: template.header_title,
      footer_text: template.footer_text,
      footer_company: template.footer_company,
      footer_address: template.footer_address || "",
      primary_color: template.primary_color,
      background_color: template.background_color,
      greeting_text: template.greeting_text || "Kedves Feliratkozónk!",
      footer_links: template.footer_links || [],
      header_color: template.header_color || template.primary_color,
      footer_color: template.footer_color || "#1a1a1a",
      header_gradient_1: (template as any).header_gradient_1 || "",
      header_gradient_2: (template as any).header_gradient_2 || "",
      greeting_text_color: (template as any).greeting_text_color || "#ffffff",
      greeting_bg_color: (template as any).greeting_bg_color || "#0ea5e9",
      footer_gradient: template.footer_gradient || "",
      sender_email: template.sender_email || "noreply@doyoueap.com",
      sender_name: template.sender_name || "DoYouEAP",
      header_color_1: template.header_color_1 || template.header_color || "#0ea5e9",
      header_color_2: template.header_color_2 || template.header_color || "#0ea5e9",
      footer_link_color: template.footer_link_color || "#ffffff",
      extra_content_border_color: template.extra_content_border_color || "#0ea5e9",
      extra_content_bg_color: template.extra_content_bg_color || "#0ea5e915",
      content_button_text: template.content_button_text || "Olvass tovább",
      content_button_text_color: template.content_button_text_color || "#ffffff",
      content_button_color: template.content_button_color || "#0ea5e9",
      content_button_shadow_color: template.content_button_shadow_color || "#0ea5e933",
      content_button_url: template.content_button_url || "",
      show_content_button: template.show_content_button || false,
      extra_button_text: template.extra_button_text || "További információ",
      extra_button_text_color: template.extra_button_text_color || "#ffffff",
      extra_button_color: template.extra_button_color || "#0ea5e9",
      extra_button_shadow_color: template.extra_button_shadow_color || "#0ea5e933",
      extra_button_url: template.extra_button_url || "",
      show_extra_button: template.show_extra_button || false,
    });
    setTemplateLogoPreview(template.logo_url);
    setTemplateFeaturedImagePreview(template.featured_image_url);
    setTemplateFooterLogoPreview(template.footer_logo_url);
    setIsTemplateDialogOpen(true);
  };

  const handleAddFooterLink = () => {
    setTemplateForm({
      ...templateForm,
      footer_links: [...templateForm.footer_links, { text: "", url: "" }]
    });
  };

  const handleRemoveFooterLink = (index: number) => {
    setTemplateForm({
      ...templateForm,
      footer_links: templateForm.footer_links.filter((_, i) => i !== index)
    });
  };

  const handleUpdateFooterLink = (index: number, field: 'text' | 'url', value: string) => {
    const newLinks = [...templateForm.footer_links];
    newLinks[index][field] = value;
    setTemplateForm({
      ...templateForm,
      footer_links: newLinks
    });
  };

  return (
    <div className="container mx-auto py-8 space-y-8">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Hírlevél kezelés</h1>
      <div className="flex gap-2">
        <Dialog open={isTemplateDialogOpen} onOpenChange={(open) => {
          setIsTemplateDialogOpen(open);
          if (!open) {
            setEditingTemplateId(null);
            setTemplateForm({
              name: "",
              header_title: "DoYouEAP Hírlevél",
              footer_text: "Ez egy automatikus üzenet. Kérjük, ne válaszoljon erre az emailre.",
              footer_company: "DoYouEAP",
              footer_address: "",
              primary_color: "#0ea5e9",
              background_color: "#f8fafc",
              greeting_text: "Kedves Feliratkozónk!",
              footer_links: [],
              header_color: "#0ea5e9",
              footer_color: "#1a1a1a",
              header_gradient_1: "",
              header_gradient_2: "",
              greeting_text_color: "#ffffff",
              greeting_bg_color: "#0ea5e9",
              footer_gradient: "",
              sender_email: "noreply@doyoueap.com",
              sender_name: "DoYouEAP",
              header_color_1: "#0ea5e9",
              header_color_2: "#0ea5e9",
              footer_link_color: "#ffffff",
              extra_content_border_color: "#0ea5e9",
              extra_content_bg_color: "#0ea5e915",
              content_button_text: "Olvass tovább",
              content_button_text_color: "#ffffff",
              content_button_color: "#0ea5e9",
              content_button_shadow_color: "#0ea5e933",
              content_button_url: "",
              show_content_button: false,
              extra_button_text: "További információ",
              extra_button_text_color: "#ffffff",
              extra_button_color: "#0ea5e9",
              extra_button_shadow_color: "#0ea5e933",
              extra_button_url: "",
              show_extra_button: false,
            });
          }
        }}>
          <DialogTrigger asChild>
            <Button variant="outline" onClick={() => setEditingTemplateId(null)}>
              <Plus className="mr-2 h-4 w-4" />
              Új sablon
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>{editingTemplateId ? "Sablon szerkesztése" : "Új sablon létrehozása"}</DialogTitle>
            </DialogHeader>
            <Tabs defaultValue="content" className="w-full">
              <TabsList className="grid w-full grid-cols-4">
                <TabsTrigger value="content">Tartalom</TabsTrigger>
                <TabsTrigger value="style">Színek</TabsTrigger>
                <TabsTrigger value="buttons">Gombok</TabsTrigger>
                <TabsTrigger value="footer">Lábléc</TabsTrigger>
              </TabsList>
              
              <TabsContent value="content" className="space-y-4">
                <div>
                  <Label>Sablon neve *</Label>
                  <Input
                    value={templateForm.name}
                    onChange={(e) => setTemplateForm({ ...templateForm, name: e.target.value })}
                    placeholder="pl. Alap sablon"
                  />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label>Feladó név *</Label>
                    <Input
                      value={templateForm.sender_name}
                      onChange={(e) => setTemplateForm({ ...templateForm, sender_name: e.target.value })}
                      placeholder="pl. DoYouEAP"
                    />
                  </div>
                  <div>
                    <Label>Feladó email *</Label>
                    <Input
                      type="email"
                      value={templateForm.sender_email}
                      onChange={(e) => setTemplateForm({ ...templateForm, sender_email: e.target.value })}
                      placeholder="noreply@doyoueap.com"
                    />
                  </div>
                </div>
                <div className="border-t pt-4 mt-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Megszólítás</Label>
                      <Input
                        value={templateForm.greeting_text}
                        onChange={(e) => setTemplateForm({ ...templateForm, greeting_text: e.target.value })}
                        placeholder="pl. Kedves Feliratkozónk!"
                      />
                      <p className="text-xs text-muted-foreground mt-1">Ha a címzett nevét ismerjük, automatikusan "Kedves [Név]!" lesz</p>
                    </div>
                  </div>
                </div>
                <div className="border-t pt-4 mt-4">
                  <div>
                    <Label>Fejléc cím</Label>
                    <Input
                      value={templateForm.header_title}
                      onChange={(e) => setTemplateForm({ ...templateForm, header_title: e.target.value })}
                    />
                  </div>
                </div>
                <div className="border-t pt-4 mt-4">
                  <h3 className="text-sm font-semibold mb-4">Képek</h3>
                  <div className="space-y-4">
                    <div>
                      <Label>Logo (fejléchez)</Label>
                      <div className="space-y-2">
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handleTemplateLogoChange}
                          className="block w-full text-sm"
                        />
                        {templateLogoPreview && (
                          <div className="relative">
                            <img src={templateLogoPreview} alt="Logo előnézet" className="h-20 object-contain" />
                            <Button
                              variant="ghost"
                              size="sm"
                              className="absolute top-0 right-0"
                              onClick={() => {
                                setTemplateLogoFile(null);
                                setTemplateLogoPreview(null);
                              }}
                            >
                              <X className="h-4 w-4" />
                            </Button>
                          </div>
                        )}
                      </div>
                    </div>
                    <div>
                      <Label>Kiemelt kép</Label>
                      <div className="space-y-2">
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handleTemplateFeaturedImageChange}
                          className="block w-full text-sm"
                        />
                        {templateFeaturedImagePreview && (
                          <div className="relative">
                            <img src={templateFeaturedImagePreview} alt="Kiemelt kép előnézet" className="h-20 object-contain" />
                            <Button
                              variant="ghost"
                              size="sm"
                              className="absolute top-0 right-0"
                              onClick={() => {
                                setTemplateFeaturedImage(null);
                                setTemplateFeaturedImagePreview(null);
                              }}
                            >
                              <X className="h-4 w-4" />
                            </Button>
                          </div>
                        )}
                      </div>
                    </div>
                    <div>
                      <Label>Lábléc logo (opcionális)</Label>
                      <p className="text-xs text-muted-foreground mb-2">Ha nincs megadva, az alap logót használja</p>
                      <div className="space-y-2">
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handleTemplateFooterLogoChange}
                          className="block w-full text-sm"
                        />
                        {templateFooterLogoPreview && (
                          <div className="relative">
                            <img src={templateFooterLogoPreview} alt="Lábléc logo előnézet" className="h-20 object-contain" />
                            <Button
                              variant="ghost"
                              size="sm"
                              className="absolute top-0 right-0"
                              onClick={() => {
                                setTemplateFooterLogoFile(null);
                                setTemplateFooterLogoPreview(null);
                              }}
                            >
                              <X className="h-4 w-4" />
                            </Button>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </TabsContent>

              <TabsContent value="style" className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label>Fejléc 1 szín (logó mögött)</Label>
                    <div className="flex gap-2">
                      <Input
                        type="color"
                        value={templateForm.header_color_1}
                        onChange={(e) => setTemplateForm({ ...templateForm, header_color_1: e.target.value })}
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.header_color_1}
                        onChange={(e) => setTemplateForm({ ...templateForm, header_color_1: e.target.value })}
                        placeholder="#0ea5e9"
                      />
                    </div>
                  </div>
                  <div>
                    <Label>Fejléc 2 szín (szöveg alatti)</Label>
                    <div className="flex gap-2">
                      <Input
                        type="color"
                        value={templateForm.header_color_2}
                        onChange={(e) => setTemplateForm({ ...templateForm, header_color_2: e.target.value })}
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.header_color_2}
                        onChange={(e) => setTemplateForm({ ...templateForm, header_color_2: e.target.value })}
                        placeholder="#0ea5e9"
                      />
                    </div>
                  </div>
                </div>
                <div>
                  <Label>Fejléc 1 gradient (logó mögött) (opcionális, felülírja a fejléc színét)</Label>
                  <Input
                    value={templateForm.header_gradient_1}
                    onChange={(e) => setTemplateForm({ ...templateForm, header_gradient_1: e.target.value })}
                    placeholder="pl: linear-gradient(135deg, #667eea 0%, #764ba2 100%)"
                  />
                  <p className="text-xs text-muted-foreground mt-1">CSS gradient szintaxis</p>
                </div>
                <div>
                  <Label>Fejléc 2 gradient (cím mögött) (opcionális, felülírja a fejléc színét)</Label>
                  <Input
                    value={templateForm.header_gradient_2}
                    onChange={(e) => setTemplateForm({ ...templateForm, header_gradient_2: e.target.value })}
                    placeholder="pl: linear-gradient(135deg, #667eea 0%, #764ba2 100%)"
                  />
                  <p className="text-xs text-muted-foreground mt-1">CSS gradient szintaxis</p>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label>Megszólítás szövegének színe</Label>
                    <div className="flex gap-2">
                      <Input
                        type="color"
                        value={templateForm.greeting_text_color}
                        onChange={(e) => setTemplateForm({ ...templateForm, greeting_text_color: e.target.value })}
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.greeting_text_color}
                        onChange={(e) => setTemplateForm({ ...templateForm, greeting_text_color: e.target.value })}
                        placeholder="#ffffff"
                      />
                    </div>
                  </div>
                  <div>
                    <Label>Megszólítás mögötti flekk színe</Label>
                    <div className="flex gap-2">
                      <Input
                        type="color"
                        value={templateForm.greeting_bg_color}
                        onChange={(e) => setTemplateForm({ ...templateForm, greeting_bg_color: e.target.value })}
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.greeting_bg_color}
                        onChange={(e) => setTemplateForm({ ...templateForm, greeting_bg_color: e.target.value })}
                        placeholder="#0ea5e9"
                      />
                    </div>
                  </div>
                </div>
                <div>
                  <Label>Lábléc háttérszíne</Label>
                  <div className="flex gap-2">
                    <Input
                      type="color"
                      value={templateForm.footer_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, footer_color: e.target.value })}
                      className="w-20 h-10"
                    />
                    <Input
                      value={templateForm.footer_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, footer_color: e.target.value })}
                      placeholder="#1a1a1a"
                    />
                  </div>
                </div>
                <div>
                  <Label>Lábléc gradient (opcionális, felülírja a lábléc színét)</Label>
                  <Input
                    value={templateForm.footer_gradient}
                    onChange={(e) => setTemplateForm({ ...templateForm, footer_gradient: e.target.value })}
                    placeholder="pl: linear-gradient(135deg, #1a1a1a 0%, #2d3748 100%)"
                  />
                </div>
                <div>
                  <Label>Háttérszín (email külső háttere)</Label>
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
                <div className="border-t pt-4 mt-4">
                  <h3 className="text-sm font-semibold mb-4">Extra tartalom színek</h3>
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Extra tartalom szegély színe</Label>
                      <div className="flex gap-2">
                        <Input
                          type="color"
                          value={templateForm.extra_content_border_color}
                          onChange={(e) => setTemplateForm({ ...templateForm, extra_content_border_color: e.target.value })}
                          className="w-20 h-10"
                        />
                        <Input
                          value={templateForm.extra_content_border_color}
                          onChange={(e) => setTemplateForm({ ...templateForm, extra_content_border_color: e.target.value })}
                          placeholder="#0ea5e9"
                        />
                      </div>
                    </div>
                    <div>
                      <Label>Extra tartalom háttérszíne</Label>
                      <div className="flex gap-2">
                        <Input
                          type="color"
                          value={templateForm.extra_content_bg_color}
                          onChange={(e) => setTemplateForm({ ...templateForm, extra_content_bg_color: e.target.value })}
                          className="w-20 h-10"
                        />
                        <Input
                          value={templateForm.extra_content_bg_color}
                          onChange={(e) => setTemplateForm({ ...templateForm, extra_content_bg_color: e.target.value })}
                          placeholder="#0ea5e915"
                        />
                      </div>
                    </div>
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
                <div>
                  <Label>Lábléc link szövegek színe</Label>
                  <div className="flex gap-2">
                    <Input
                      type="color"
                      value={templateForm.footer_link_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, footer_link_color: e.target.value })}
                      className="w-20 h-10"
                    />
                    <Input
                      value={templateForm.footer_link_color}
                      onChange={(e) => setTemplateForm({ ...templateForm, footer_link_color: e.target.value })}
                      placeholder="#ffffff"
                    />
                  </div>
                </div>
                <div>
                  <div className="flex justify-between items-center mb-2">
                    <Label>Lábléc linkek</Label>
                    <Button type="button" variant="outline" size="sm" onClick={handleAddFooterLink}>
                      <Plus className="h-4 w-4 mr-1" />
                      Link hozzáadása
                    </Button>
                  </div>
                  <div className="space-y-2">
                    {templateForm.footer_links.map((link, index) => (
                      <div key={index} className="flex gap-2 items-start p-3 border rounded-md">
                        <div className="flex-1 space-y-2">
                          <Input
                            value={link.text}
                            onChange={(e) => handleUpdateFooterLink(index, 'text', e.target.value)}
                            placeholder="Link szövege"
                          />
                          <Input
                            value={link.url}
                            onChange={(e) => handleUpdateFooterLink(index, 'url', e.target.value)}
                            placeholder="https://..."
                          />
                        </div>
                        <Button
                          type="button"
                          variant="ghost"
                          size="sm"
                          onClick={() => handleRemoveFooterLink(index)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    ))}
                  </div>
                </div>
              </TabsContent>

              <TabsContent value="buttons" className="space-y-4">
                <div className="space-y-4 p-4 border rounded-lg">
                  <h4 className="font-semibold">Tartalom gomb</h4>
                  
                  <div className="flex items-center space-x-2">
                    <Checkbox
                      id="show_content_button"
                      checked={templateForm.show_content_button}
                      onCheckedChange={(checked) =>
                        setTemplateForm({ ...templateForm, show_content_button: checked as boolean })
                      }
                    />
                    <Label htmlFor="show_content_button">Tartalom gomb megjelenítése</Label>
                  </div>

                  <div>
                    <Label htmlFor="content_button_text">Tartalom gomb szövege</Label>
                    <Input
                      id="content_button_text"
                      value={templateForm.content_button_text}
                      onChange={(e) =>
                        setTemplateForm({ ...templateForm, content_button_text: e.target.value })
                      }
                      placeholder="Olvass tovább"
                    />
                  </div>

                  <div>
                    <Label htmlFor="content_button_url">Tartalom gomb URL linkje</Label>
                    <Input
                      id="content_button_url"
                      type="url"
                      value={templateForm.content_button_url}
                      onChange={(e) =>
                        setTemplateForm({ ...templateForm, content_button_url: e.target.value })
                      }
                      placeholder="https://..."
                    />
                  </div>

                  <div>
                    <Label htmlFor="content_button_text_color">Tartalom gomb szövegének színe</Label>
                    <div className="flex gap-2">
                      <Input
                        id="content_button_text_color"
                        type="color"
                        value={templateForm.content_button_text_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, content_button_text_color: e.target.value })
                        }
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.content_button_text_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, content_button_text_color: e.target.value })
                        }
                        placeholder="#ffffff"
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="content_button_color">Tartalom gomb színe</Label>
                    <div className="flex gap-2">
                      <Input
                        id="content_button_color"
                        type="color"
                        value={templateForm.content_button_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, content_button_color: e.target.value })
                        }
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.content_button_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, content_button_color: e.target.value })
                        }
                        placeholder="#0ea5e9"
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="content_button_shadow_color">Tartalom gomb árnyékának színe</Label>
                    <div className="flex gap-2">
                      <Input
                        id="content_button_shadow_color"
                        type="color"
                        value={templateForm.content_button_shadow_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, content_button_shadow_color: e.target.value })
                        }
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.content_button_shadow_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, content_button_shadow_color: e.target.value })
                        }
                        placeholder="#0ea5e933"
                      />
                    </div>
                  </div>
                </div>

                <div className="space-y-4 p-4 border rounded-lg">
                  <h4 className="font-semibold">Extra tartalom gomb</h4>
                  
                  <div className="flex items-center space-x-2">
                    <Checkbox
                      id="show_extra_button"
                      checked={templateForm.show_extra_button}
                      onCheckedChange={(checked) =>
                        setTemplateForm({ ...templateForm, show_extra_button: checked as boolean })
                      }
                    />
                    <Label htmlFor="show_extra_button">Extra tartalom gomb megjelenítése</Label>
                  </div>

                  <div>
                    <Label htmlFor="extra_button_text">Extra tartalom gomb szövege</Label>
                    <Input
                      id="extra_button_text"
                      value={templateForm.extra_button_text}
                      onChange={(e) =>
                        setTemplateForm({ ...templateForm, extra_button_text: e.target.value })
                      }
                      placeholder="További információ"
                    />
                  </div>

                  <div>
                    <Label htmlFor="extra_button_url">Extra tartalom gomb URL linkje</Label>
                    <Input
                      id="extra_button_url"
                      type="url"
                      value={templateForm.extra_button_url}
                      onChange={(e) =>
                        setTemplateForm({ ...templateForm, extra_button_url: e.target.value })
                      }
                      placeholder="https://..."
                    />
                  </div>

                  <div>
                    <Label htmlFor="extra_button_text_color">Extra tartalom gomb szövegének színe</Label>
                    <div className="flex gap-2">
                      <Input
                        id="extra_button_text_color"
                        type="color"
                        value={templateForm.extra_button_text_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, extra_button_text_color: e.target.value })
                        }
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.extra_button_text_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, extra_button_text_color: e.target.value })
                        }
                        placeholder="#ffffff"
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="extra_button_color">Extra tartalom gomb színe</Label>
                    <div className="flex gap-2">
                      <Input
                        id="extra_button_color"
                        type="color"
                        value={templateForm.extra_button_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, extra_button_color: e.target.value })
                        }
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.extra_button_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, extra_button_color: e.target.value })
                        }
                        placeholder="#0ea5e9"
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="extra_button_shadow_color">Extra tartalom gomb árnyékának színe</Label>
                    <div className="flex gap-2">
                      <Input
                        id="extra_button_shadow_color"
                        type="color"
                        value={templateForm.extra_button_shadow_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, extra_button_shadow_color: e.target.value })
                        }
                        className="w-20 h-10"
                      />
                      <Input
                        value={templateForm.extra_button_shadow_color}
                        onChange={(e) =>
                          setTemplateForm({ ...templateForm, extra_button_shadow_color: e.target.value })
                        }
                        placeholder="#0ea5e933"
                      />
                    </div>
                  </div>
                </div>
              </TabsContent>
            </Tabs>
            <div className="flex justify-end gap-2 pt-4">
              <Button variant="outline" onClick={() => setIsTemplateDialogOpen(false)}>
                Mégse
              </Button>
              <Button onClick={handleSaveTemplate}>{editingTemplateId ? "Módosítások mentése" : "Sablon mentése"}</Button>
            </div>
          </DialogContent>
        </Dialog>
      </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Meglévő sablonok</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid gap-3">
            {templates.map((template) => (
              <div key={template.id} className="flex justify-between items-center p-4 border rounded-md hover:bg-muted/50 transition-colors">
                <div>
                  <p className="font-medium text-lg">{template.name}</p>
                  {template.is_default && <span className="text-xs text-muted-foreground">(Alapértelmezett sablon)</span>}
                  <p className="text-sm text-muted-foreground mt-1">
                    Fejléc: {template.header_title}
                  </p>
                </div>
                <div className="flex gap-2">
                  <Button
                    variant="outline"
                    onClick={() => handleEditTemplate(template)}
                  >
                    <Settings className="h-4 w-4 mr-2" />
                    Szerkesztés
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => handleDeleteTemplate(template.id)}
                  >
                    <Trash2 className="h-4 w-4" />
                  </Button>
                </div>
              </div>
            ))}
            {templates.length === 0 && (
              <p className="text-center text-muted-foreground py-8">
                Még nincs egyetlen sablon sem. Hozzon létre egyet az "Új sablon" gombbal.
              </p>
            )}
          </div>
        </CardContent>
      </Card>

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

          <div>
            <Label>Extra tartalom</Label>
            <Textarea
              value={newsletterExtraContent}
              onChange={(e) => setNewsletterExtraContent(e.target.value)}
              placeholder="EAP Pulse - Mérje programja hatékonyságát&#10;&#10;Tudta, hogy az EAP Pulse segítségével..."
              rows={6}
            />
            <p className="text-sm text-muted-foreground mt-1">
              Ez a tartalom a fő tartalom után, külön dobozban jelenik meg. Formázás: **félkövér**, *dőlt*
            </p>
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
                <TableHead>Műveletek</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {campaigns.map((campaign) => (
                <TableRow key={campaign.id}>
                  <TableCell>{campaign.subject}</TableCell>
                  <TableCell>{new Date(campaign.sent_at).toLocaleString()}</TableCell>
                  <TableCell>{campaign.recipient_count}</TableCell>
                  <TableCell>{campaign.status}</TableCell>
                  <TableCell>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDeleteCampaign(campaign.id)}
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
};

export default NewsletterManagement;
