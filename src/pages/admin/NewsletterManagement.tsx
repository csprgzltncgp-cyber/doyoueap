import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Mail, Plus, Send, Trash2, Users, Upload } from "lucide-react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { format } from "date-fns";

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

export default function NewsletterManagement() {
  const [subscribers, setSubscribers] = useState<Subscriber[]>([]);
  const [campaigns, setCampaigns] = useState<Campaign[]>([]);
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [newSubscriber, setNewSubscriber] = useState({ email: "", name: "" });
  const [newsletter, setNewsletter] = useState({ 
    subject: "", 
    content: `<h2>Üdvözöljük hírlevelünkben!</h2>

<p>Kedves Olvasóink!</p>

<div class="highlight-box">
  <p><strong>🎉 Fontos bejelentés:</strong> Megjelent magazinunk legújabb száma!</p>
</div>

<h3>📰 Új cikkek a The Journalist! magazinban</h3>

<p>Örömmel jelentjük be, hogy megjelent magazinunk januári száma, amely számos érdekes cikket tartalmaz az EAP világából:</p>

<ul>
  <li><strong>EAP Mítoszok és Tévhitek</strong> - Tisztázzuk a leggyakoribb félreértéseket az EAP programokról</li>
  <li><strong>ROI Számítás EAP Programokhoz</strong> - Hogyan mérjük a befektetés megtérülését?</li>
  <li><strong>Digitális Jólét a Munkahelyen</strong> - Modern megoldások a stressz kezelésére</li>
  <li><strong>Globális EAP Trendek 2025</strong> - Mit hoz az új év?</li>
</ul>

<p style="text-align: center;">
  <a href="https://doyoueap.com/magazin" class="cta-button">Olvassa el most! →</a>
</p>

<div class="divider"></div>

<h3>💡 EAP Pulse - Mérje programja hatékonyságát</h3>

<p>Tudta, hogy az EAP Pulse segítségével <strong>60+ extra statisztikai adattal</strong> bővítheti szolgáltatója riportjait? Szerezzen egyedi visszajelzéseket dolgozóitól és mutassa ki a program valódi értékét!</p>

<p>Üdvözlettel,<br><strong>A doyoueap csapata</strong></p>`,
    fromEmail: "noreply@doyoueap.com",
    logoUrl: "",
    featuredImageUrl: ""
  });
  const [dialogOpen, setDialogOpen] = useState(false);
  const [uploading, setUploading] = useState(false);

  useEffect(() => {
    fetchSubscribers();
    fetchCampaigns();
  }, []);

  const fetchSubscribers = async () => {
    try {
      const { data, error } = await supabase
        .from("newsletter_subscribers")
        .select("*")
        .eq("is_active", true)
        .order("subscribed_at", { ascending: false });

      if (error) throw error;
      setSubscribers(data || []);
    } catch (error) {
      console.error("Error fetching subscribers:", error);
      toast.error("Hiba a feliratkozók betöltésekor");
    } finally {
      setLoading(false);
    }
  };

  const fetchCampaigns = async () => {
    try {
      const { data, error } = await supabase
        .from("newsletter_campaigns")
        .select("*")
        .order("sent_at", { ascending: false })
        .limit(10);

      if (error) throw error;
      setCampaigns(data || []);
    } catch (error) {
      console.error("Error fetching campaigns:", error);
      toast.error("Hiba a kampányok betöltésekor");
    }
  };

  const handleAddSubscriber = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const { error } = await supabase
        .from("newsletter_subscribers")
        .insert([{
          email: newSubscriber.email,
          name: newSubscriber.name || null,
          source: 'admin'
        }]);

      if (error) throw error;
      
      toast.success("Feliratkozó hozzáadva!");
      setNewSubscriber({ email: "", name: "" });
      setDialogOpen(false);
      fetchSubscribers();
    } catch (error: any) {
      console.error("Error adding subscriber:", error);
      if (error.code === '23505') {
        toast.error("Ez az email cím már fel van iratkozva");
      } else {
        toast.error("Hiba történt a feliratkozó hozzáadásakor");
      }
    }
  };

  const handleDeleteSubscriber = async (id: string) => {
    if (!confirm("Biztosan törölni szeretnéd ezt a feliratkozót?")) return;

    try {
      const { error } = await supabase
        .from("newsletter_subscribers")
        .update({ is_active: false })
        .eq("id", id);

      if (error) throw error;
      
      toast.success("Feliratkozó törölve");
      fetchSubscribers();
    } catch (error) {
      console.error("Error deleting subscriber:", error);
      toast.error("Hiba történt a törlés során");
    }
  };

  const handleExcelUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setUploading(true);
    try {
      // Dynamically import xlsx
      const XLSX = await import('xlsx');
      
      const reader = new FileReader();
      reader.onload = async (event) => {
        try {
          const data = new Uint8Array(event.target?.result as ArrayBuffer);
          const workbook = XLSX.read(data, { type: 'array' });
          const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
          const jsonData = XLSX.utils.sheet_to_json(firstSheet) as Array<{
            email?: string;
            Email?: string;
            name?: string;
            Name?: string;
            Név?: string;
          }>;

          // Process and insert subscribers
          const subscribersToAdd = jsonData
            .map(row => ({
              email: (row.email || row.Email || '').trim().toLowerCase(),
              name: (row.name || row.Name || row.Név || '').trim() || null,
              source: 'excel_import'
            }))
            .filter(s => s.email && s.email.includes('@'));

          if (subscribersToAdd.length === 0) {
            toast.error("Nem található érvényes email cím az Excel fájlban");
            return;
          }

          // Insert all subscribers
          const { data: insertedData, error } = await supabase
            .from("newsletter_subscribers")
            .upsert(subscribersToAdd, { 
              onConflict: 'email',
              ignoreDuplicates: true 
            })
            .select();

          if (error) throw error;

          toast.success(`${subscribersToAdd.length} feliratkozó sikeresen feltöltve!`);
          fetchSubscribers();
        } catch (error: any) {
          console.error("Error processing Excel:", error);
          toast.error("Hiba az Excel feldolgozása közben");
        } finally {
          setUploading(false);
        }
      };
      reader.readAsArrayBuffer(file);
    } catch (error) {
      console.error("Error reading file:", error);
      toast.error("Hiba a fájl beolvasása közben");
      setUploading(false);
    }
  };

  const handleSendNewsletter = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (subscribers.length === 0) {
      toast.error("Nincs aktív feliratkozó!");
      return;
    }

    if (!newsletter.subject || !newsletter.content) {
      toast.error("Minden mezőt ki kell tölteni!");
      return;
    }

    setSending(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Nincs bejelentkezve");

      // Call edge function to send emails
      const { data, error } = await supabase.functions.invoke('send-newsletter', {
        body: {
          subject: newsletter.subject,
          content: newsletter.content,
          fromEmail: newsletter.fromEmail,
          subscribers: subscribers.map(s => ({ email: s.email, name: s.name })),
          logoUrl: newsletter.logoUrl || undefined,
          featuredImageUrl: newsletter.featuredImageUrl || undefined
        }
      });

      if (error) throw error;

      // Save campaign to database
      const { error: campaignError } = await supabase
        .from("newsletter_campaigns")
        .insert([{
          subject: newsletter.subject,
          content: newsletter.content,
          sent_by: user.id,
          recipient_count: subscribers.length,
          status: 'sent'
        }]);

      if (campaignError) throw campaignError;

      toast.success(`Hírlevél sikeresen elküldve ${subscribers.length} címzettnek!`);
      setNewsletter({ 
        subject: "", 
        content: "",
        fromEmail: "noreply@doyoueap.com",
        logoUrl: "",
        featuredImageUrl: ""
      });
      fetchCampaigns();
    } catch (error: any) {
      console.error("Error sending newsletter:", error);
      toast.error("Hiba történt a hírlevél küldésekor: " + (error.message || "Ismeretlen hiba"));
    } finally {
      setSending(false);
    }
  };

  if (loading) {
    return <div className="p-6">Betöltés...</div>;
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Hírlevél kezelés</h1>
        <div className="flex items-center gap-2">
          <div className="flex items-center gap-2 px-4 py-2 bg-muted rounded-lg">
            <Users className="h-4 w-4" />
            <span className="font-medium">{subscribers.length} feliratkozó</span>
          </div>
        </div>
      </div>

      {/* Send Newsletter Card */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Mail className="h-5 w-5" />
            Új hírlevél küldése
          </CardTitle>
          <CardDescription>
            Küldjön hírleveleket az összes aktív feliratkozónak
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSendNewsletter} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="fromEmail">Feladó email*</Label>
              <Input
                id="fromEmail"
                type="email"
                value={newsletter.fromEmail}
                onChange={(e) => setNewsletter({ ...newsletter, fromEmail: e.target.value })}
                placeholder="noreply@doyoueap.com"
                required
              />
              <p className="text-xs text-muted-foreground">
                A hírlevél feladója (pl. noreply@doyoueap.com vagy info@doyoueap.com)
              </p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="logoUrl">Logo URL (opcionális)</Label>
                <Input
                  id="logoUrl"
                  type="url"
                  value={newsletter.logoUrl}
                  onChange={(e) => setNewsletter({ ...newsletter, logoUrl: e.target.value })}
                  placeholder="https://example.com/logo.png"
                />
                <p className="text-xs text-muted-foreground">
                  A hírlevél tetején megjelenő logo (ajánlott méret: 180x60px)
                </p>
              </div>
              <div className="space-y-2">
                <Label htmlFor="featuredImageUrl">Kiemelt kép URL (opcionális)</Label>
                <Input
                  id="featuredImageUrl"
                  type="url"
                  value={newsletter.featuredImageUrl}
                  onChange={(e) => setNewsletter({ ...newsletter, featuredImageUrl: e.target.value })}
                  placeholder="https://example.com/image.jpg"
                />
                <p className="text-xs text-muted-foreground">
                  Nagy banner kép a címsor alatt (ajánlott méret: 600x300px)
                </p>
              </div>
            </div>
            <div className="space-y-2">
              <Label htmlFor="subject">Tárgy*</Label>
              <Input
                id="subject"
                value={newsletter.subject}
                onChange={(e) => setNewsletter({ ...newsletter, subject: e.target.value })}
                placeholder="pl. Új cikkeink januárban"
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="content">Üzenet (HTML)*</Label>
              <Textarea
                id="content"
                value={newsletter.content}
                onChange={(e) => setNewsletter({ ...newsletter, content: e.target.value })}
                rows={15}
                placeholder="HTML tartalom a hírlevélhez..."
                required
                className="font-mono text-sm"
              />
              <p className="text-xs text-muted-foreground">
                <strong>HTML formázási lehetőségek:</strong><br/>
                • Címsorok: &lt;h2&gt;, &lt;h3&gt;<br/>
                • Bekezdés: &lt;p&gt;<br/>
                • Felsorolás: &lt;ul&gt;&lt;li&gt;Elem&lt;/li&gt;&lt;/ul&gt;<br/>
                • Vastag szöveg: &lt;strong&gt;Szöveg&lt;/strong&gt;<br/>
                • Link: &lt;a href="URL"&gt;Szöveg&lt;/a&gt;<br/>
                • Gomb: &lt;a href="URL" class="cta-button"&gt;Kattints!&lt;/a&gt;<br/>
                • Kiemelő doboz: &lt;div class="highlight-box"&gt;&lt;p&gt;Szöveg&lt;/p&gt;&lt;/div&gt;<br/>
                • Elválasztó: &lt;div class="divider"&gt;&lt;/div&gt;
              </p>
            </div>
            <Button type="submit" disabled={sending || subscribers.length === 0}>
              {sending ? (
                <>Küldés folyamatban...</>
              ) : (
                <>
                  <Send className="mr-2 h-4 w-4" />
                  Hírlevél küldése ({subscribers.length} címzett)
                </>
              )}
            </Button>
          </form>
        </CardContent>
      </Card>

      {/* Subscribers Management */}
      <Card>
        <CardHeader>
            <div className="flex justify-between items-center">
              <div>
                <CardTitle>Feliratkozók</CardTitle>
                <CardDescription>Kezelheti a hírlevél feliratkozóit</CardDescription>
              </div>
              <div className="flex gap-2">
                <Button variant="outline" disabled={uploading} asChild>
                  <label className="cursor-pointer">
                    <Upload className="mr-2 h-4 w-4" />
                    {uploading ? "Feltöltés..." : "Excel feltöltés"}
                    <input
                      type="file"
                      accept=".xlsx,.xls"
                      onChange={handleExcelUpload}
                      className="hidden"
                    />
                  </label>
                </Button>
                <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
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
                    <form onSubmit={handleAddSubscriber} className="space-y-4">
                      <div className="space-y-2">
                        <Label htmlFor="email">Email cím*</Label>
                        <Input
                          id="email"
                          type="email"
                          value={newSubscriber.email}
                          onChange={(e) => setNewSubscriber({ ...newSubscriber, email: e.target.value })}
                          required
                        />
                      </div>
                      <div className="space-y-2">
                        <Label htmlFor="name">Név</Label>
                        <Input
                          id="name"
                          value={newSubscriber.name}
                          onChange={(e) => setNewSubscriber({ ...newSubscriber, name: e.target.value })}
                        />
                      </div>
                      <div className="flex gap-2 justify-end">
                        <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>
                          Mégse
                        </Button>
                        <Button type="submit">Hozzáadás</Button>
                      </div>
                    </form>
                  </DialogContent>
                </Dialog>
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
                <TableHead>Forrás</TableHead>
                <TableHead className="text-right">Műveletek</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {subscribers.map((subscriber) => (
                <TableRow key={subscriber.id}>
                  <TableCell className="font-medium">{subscriber.email}</TableCell>
                  <TableCell>{subscriber.name || '-'}</TableCell>
                  <TableCell>{format(new Date(subscriber.subscribed_at), 'yyyy.MM.dd')}</TableCell>
                  <TableCell>{subscriber.is_active ? 'Aktív' : 'Inaktív'}</TableCell>
                  <TableCell className="text-right">
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

      {/* Campaign History */}
      <Card>
        <CardHeader>
          <CardTitle>Küldési előzmények</CardTitle>
          <CardDescription>Az utoljára küldött hírlevelek</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Tárgy</TableHead>
                <TableHead>Küldés dátuma</TableHead>
                <TableHead>Címzettek</TableHead>
                <TableHead>Státusz</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {campaigns.map((campaign) => (
                <TableRow key={campaign.id}>
                  <TableCell className="font-medium">{campaign.subject}</TableCell>
                  <TableCell>{format(new Date(campaign.sent_at), 'yyyy.MM.dd HH:mm')}</TableCell>
                  <TableCell>{campaign.recipient_count}</TableCell>
                  <TableCell>
                    <span className="px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                      {campaign.status}
                    </span>
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
