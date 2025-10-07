import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Mail, Plus, Send, Trash2, Users } from "lucide-react";
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
  const [newsletter, setNewsletter] = useState({ subject: "", content: "" });
  const [dialogOpen, setDialogOpen] = useState(false);

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
          subscribers: subscribers.map(s => ({ email: s.email, name: s.name }))
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
      setNewsletter({ subject: "", content: "" });
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
              <Label htmlFor="content">Üzenet*</Label>
              <Textarea
                id="content"
                value={newsletter.content}
                onChange={(e) => setNewsletter({ ...newsletter, content: e.target.value })}
                rows={10}
                placeholder="Írja meg a hírlevél tartalmát..."
                required
              />
              <p className="text-xs text-muted-foreground">
                HTML formázás támogatott. A hírlevélben automatikusan megjelenik a doyoueap branding.
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
