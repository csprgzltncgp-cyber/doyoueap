import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Download, Mail, Link as LinkIcon, QrCode, Type, Paperclip, Smile, Image as ImageIcon, AlignLeft } from 'lucide-react';
import { toast } from 'sonner';
import { supabase } from '@/integrations/supabase/client';
import poster1 from '@/assets/poster-1.png';
import poster2 from '@/assets/poster-2.png';
import poster3 from '@/assets/poster-3.png';
import poster4 from '@/assets/poster-4.png';
import posterWall from '@/assets/poster-wall-mockup.jpg';
import slackLogo from '@/assets/slack-logo.avif';
import slackLogoColor from '@/assets/slack-logo-color.png';

interface Template {
  id: string;
  template_type: 'email' | 'public_link' | 'qr_code';
  has_gift: boolean;
  subject: string | null;
  content: string;
}

interface Poster {
  id: string;
  has_gift: boolean;
  poster_images: string[];
  source_file_url: string | null;
}

const CommunicationSupport = () => {
  const [templates, setTemplates] = useState<Template[]>([]);
  const [posters, setPosters] = useState<Poster[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState<string | null>(null);

  // Email
  const [emailHasGift, setEmailHasGift] = useState(false);

  // Public Link
  const [publicLinkHasGift, setPublicLinkHasGift] = useState(false);

  // QR Code
  const [qrCodeHasGift, setQrCodeHasGift] = useState(false);

  useEffect(() => {
    fetchTemplates();
    fetchPosters();
  }, []);

  const fetchTemplates = async () => {
    try {
      const { data, error } = await supabase
        .from('communication_templates')
        .select('*');

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
        .select('*');

      if (error) throw error;
      setPosters(data || []);
    } catch (error: any) {
      toast.error('Hiba történt a plakátok betöltésekor');
      console.error(error);
    }
  };

  const getTemplate = (type: 'email' | 'public_link' | 'qr_code', hasGift: boolean) => {
    return templates.find(t => t.template_type === type && t.has_gift === hasGift);
  };

  const generateText = (type: 'email' | 'public_link' | 'qr_code', hasGift: boolean) => {
    const template = getTemplate(type, hasGift);
    if (!template) return '';

    const exampleLink = 'https://survey.doyoueap.hu/abc123';
    return template.content
      .replace(/{link}/g, exampleLink);
  };

  const handleDownloadText = (text: string, filename: string) => {
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    toast.success('Szöveg letöltve!');
  };

  const handleDownloadPoster = async (posterSrc: string, filename: string) => {
    try {
      const response = await fetch(posterSrc);
      const blob = await response.blob();
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
      toast.success('Plakát letöltve!');
    } catch (error) {
      toast.error('Hiba történt a letöltés során');
    }
  };

  const handleDownloadPosterImage = async (imageUrl: string, index: number) => {
    try {
      const response = await fetch(imageUrl);
      const blob = await response.blob();
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `plakat-${index + 1}.png`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
      toast.success('Plakát letöltve!');
    } catch (error) {
      toast.error('Hiba történt a letöltés során');
      console.error(error);
    }
  };

  const handleDownloadZip = async (zipUrl: string | null) => {
    if (!zipUrl) {
      toast.error('Nincs elérhető forrás fájl');
      return;
    }
    try {
      // Extract original filename from URL
      const originalFileName = zipUrl.split('/').pop()?.split('?')[0] || 'plakatok.zip';
      
      const response = await fetch(zipUrl);
      const blob = await response.blob();
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = originalFileName;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
      toast.success('ZIP archívum letöltve!');
    } catch (error) {
      toast.error('Hiba történt a letöltés során');
      console.error(error);
    }
  };

  const emailTemplate = getTemplate('email', emailHasGift);
  const publicLinkTemplate = getTemplate('public_link', publicLinkHasGift);
  const qrCodeTemplate = getTemplate('qr_code', qrCodeHasGift);

  const emailText = generateText('email', emailHasGift);
  const publicLinkText = generateText('public_link', publicLinkHasGift);
  const qrCodeText = generateText('qr_code', qrCodeHasGift);

  if (loading) {
    return (
      <div className="p-8 flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="text-left space-y-2">
        <h1 className="text-2xl font-bold">Kommunikációs sablonok</h1>
        <p className="text-muted-foreground">
          Töltsd le a szövegeket és plakátokat, amiket felhasználhatsz a felmérés népszerűsítésére.
        </p>
      </div>

      <Tabs defaultValue="email" className="space-y-4">
        <TabsList>
          <TabsTrigger value="email">
            <Mail className="h-4 w-4 mr-2" />
            Email sablon
          </TabsTrigger>
          <TabsTrigger value="link">
            <LinkIcon className="h-4 w-4 mr-2" />
            Publikus link
          </TabsTrigger>
          <TabsTrigger value="qr">
            <QrCode className="h-4 w-4 mr-2" />
            QR kód plakátok
          </TabsTrigger>
        </TabsList>

        {/* Email Template Tab */}
        <TabsContent value="email">
          <Card>
            <CardHeader className="flex flex-row items-start justify-between space-y-0">
              <div className="space-y-1.5">
                <CardTitle>Email sablon</CardTitle>
                <CardDescription>
                  Használd ezt a sablont, ha emailben szeretnéd megosztani a felmérést a munkatársaiddal.
                </CardDescription>
              </div>
              {emailTemplate && (
                <Button
                  onClick={() => handleDownloadText(emailText, 'email_sablon.txt')}
                  variant="outline"
                  size="sm"
                  className="flex-shrink-0"
                >
                  <Download className="mr-2 h-4 w-4" />
                  Szöveg letöltése
                </Button>
              )}
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center gap-2">
                <Switch
                  id="email-gift"
                  checked={emailHasGift}
                  onCheckedChange={setEmailHasGift}
                />
                <Label htmlFor="email-gift">Sorsolásos verzió (főnyeremény említése)</Label>
              </div>

              {emailTemplate && (
                <>
                  <div className="bg-background rounded-lg border-2 shadow-lg overflow-hidden max-w-5xl mx-auto">
                    {/* Email Header */}
                    <div className="bg-blue-600 px-6 py-3 flex items-center justify-between border-b">
                      <span className="font-medium text-white">Új üzenet</span>
                      <div className="flex gap-2">
                        <div className="w-3 h-3 rounded-full bg-white/30" />
                        <div className="w-3 h-3 rounded-full bg-white/30" />
                        <div className="w-3 h-3 rounded-full bg-white/30" />
                      </div>
                    </div>

                    {/* Email Fields */}
                    <div className="bg-background p-6 space-y-3">
                      <div className="flex items-center gap-3 pb-2 border-b">
                        <span className="text-sm text-muted-foreground w-20">Címzett:</span>
                        <span className="text-sm text-muted-foreground italic">munkatárs@cég.hu</span>
                      </div>
                      
                      <div className="flex items-center gap-3 pb-2 border-b">
                        <span className="text-sm text-muted-foreground w-20">Tárgy:</span>
                        <span className="text-sm font-medium">{emailTemplate.subject}</span>
                      </div>
                    </div>

                    {/* Email Body */}
                    <div className="px-6 pb-6">
                      <div className="rounded-md p-6 min-h-[200px]">
                        <div className="text-sm whitespace-pre-wrap leading-relaxed">{emailText}</div>
                      </div>
                    </div>

                    {/* Email Footer Toolbar */}
                    <div className="bg-muted/50 px-6 py-3 flex items-center justify-between border-t">
                      <div className="flex gap-2">
                        <Type className="h-4 w-4 text-muted-foreground" />
                        <Paperclip className="h-4 w-4 text-muted-foreground" />
                        <Smile className="h-4 w-4 text-muted-foreground" />
                        <ImageIcon className="h-4 w-4 text-muted-foreground" />
                        <AlignLeft className="h-4 w-4 text-muted-foreground" />
                      </div>
                      <div className="flex gap-2">
                        <Button size="sm" variant="outline" className="h-8">Mentés</Button>
                        <Button size="sm" className="h-8 bg-blue-600 text-white hover:bg-blue-700">Küldés</Button>
                      </div>
                    </div>
                  </div>
                </>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* Public Link Template Tab */}
        <TabsContent value="link">
          <Card>
            <CardHeader className="flex flex-row items-start justify-between space-y-0">
              <div className="space-y-1.5">
                <CardTitle>Publikus link sablon</CardTitle>
                <CardDescription>
                  Használd ezt a sablont Slack-ben, Teamsen vagy más üzenetküldő platformon a felmérés megosztására.
                </CardDescription>
              </div>
              {publicLinkTemplate && (
                <Button 
                  onClick={() => handleDownloadText(publicLinkText, 'slack_uzenet_szoveg.txt')}
                  variant="outline"
                  size="sm"
                  className="flex-shrink-0"
                >
                  <Download className="mr-2 h-4 w-4" />
                  Üzenet letöltése
                </Button>
              )}
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center gap-2">
                <Switch
                  id="link-gift"
                  checked={publicLinkHasGift}
                  onCheckedChange={setPublicLinkHasGift}
                />
                <Label htmlFor="link-gift">Sorsolásos verzió (főnyeremény említése)</Label>
              </div>

              {publicLinkTemplate && (
                <>
                  {/* Slack-like Message Preview */}
                  <div className="bg-background rounded-lg border shadow-lg overflow-hidden max-w-5xl mx-auto">
                    {/* Slack Header */}
                    <div className="bg-[#350d36] px-4 py-2.5 flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <span className="text-white font-bold text-lg">Slack</span>
                      </div>
                      <div className="flex gap-1.5">
                        <div className="w-2.5 h-2.5 rounded-full bg-white/20" />
                        <div className="w-2.5 h-2.5 rounded-full bg-white/20" />
                        <div className="w-2.5 h-2.5 rounded-full bg-white/20" />
                      </div>
                    </div>

                    <div className="flex">
                      {/* Slack Sidebar */}
                      <div className="bg-[#3f0e40] w-56 flex-shrink-0 p-3 space-y-1 flex flex-col">
                        <div className="flex-1">
                          {/* Channels Header */}
                          <div className="text-white/70 text-xs font-semibold px-3 py-1.5 flex items-center justify-between">
                            <span>Csatornák</span>
                            <span className="text-lg leading-none">▼</span>
                          </div>

                          {/* Channel List */}
                          <div className="space-y-0.5">
                            <div className="text-white/70 px-3 py-1 rounded text-sm hover:bg-white/10 cursor-pointer flex items-center gap-2">
                              <span className="text-white/50">#</span>
                              <span>vezetőség</span>
                            </div>
                            <div className="bg-[#1164a3] text-white px-3 py-1 rounded text-sm cursor-pointer flex items-center gap-2 font-medium">
                              <span className="text-white/90">#</span>
                              <span>általános</span>
                            </div>
                            <div className="text-white/70 px-3 py-1 rounded text-sm hover:bg-white/10 cursor-pointer flex items-center gap-2">
                              <span className="text-white/50">#</span>
                              <span>hr-csapat</span>
                            </div>
                            <div className="text-white/70 px-3 py-1 rounded text-sm hover:bg-white/10 cursor-pointer flex items-center gap-2">
                              <span className="text-white/50">#</span>
                              <span>közlemények</span>
                            </div>
                          </div>

                          {/* Direct Messages */}
                          <div className="pt-3">
                            <div className="text-white/70 text-xs font-semibold px-3 py-1.5 flex items-center justify-between">
                              <span>Közvetlen üzenetek</span>
                              <span className="text-lg leading-none">▼</span>
                            </div>
                            <div className="space-y-0.5">
                              <div className="text-white/70 px-3 py-1 rounded text-sm hover:bg-white/10 cursor-pointer flex items-center gap-2">
                                <div className="w-2 h-2 rounded-full bg-green-500" />
                                <span>Anna Kiss</span>
                              </div>
                              <div className="text-white/70 px-3 py-1 rounded text-sm hover:bg-white/10 cursor-pointer flex items-center gap-2">
                                <div className="w-2 h-2 rounded-full bg-gray-400" />
                                <span>Kovács Péter</span>
                              </div>
                            </div>
                          </div>
                        </div>

                        {/* Slack Logo at Bottom */}
                        <div className="pt-4 pb-2 border-t border-white/10 flex justify-center">
                          <img src={slackLogoColor} alt="Slack" className="h-10 w-auto" />
                        </div>
                      </div>

                      {/* Main Content Area */}
                      <div className="flex-1 flex flex-col min-w-0">
                        {/* Channel Header */}
                        <div className="bg-white border-b border-[#e0e0e0] px-5 py-3 flex items-center justify-between">
                          <div className="flex items-center gap-2">
                            <span className="font-bold text-[#1d1c1d]"># általános</span>
                            <span className="text-xs text-[#616061]">45 tag</span>
                          </div>
                        </div>

                        {/* Message Content */}
                        <div className="bg-white p-5 flex-1">
                          <div className="flex gap-3">
                            {/* Avatar */}
                            <div className="w-9 h-9 rounded bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                              HR
                            </div>
                            
                            {/* Message */}
                            <div className="flex-1 min-w-0">
                              <div className="flex items-baseline gap-2 mb-1">
                                <span className="font-bold text-[#1d1c1d] text-sm">HR csapat</span>
                                <span className="text-xs text-[#616061]">9:30</span>
                              </div>
                              
                              {/* Message Text with Link Preview */}
                              <div className="space-y-3">
                                <div className="text-[#1d1c1d] text-sm leading-relaxed whitespace-pre-wrap">
                                  {publicLinkText.split(/(https?:\/\/[^\s]+)/g).map((part, idx) => {
                                    if (part.match(/^https?:\/\//)) {
                                      return <span key={idx} className="text-[#1264a3] underline cursor-pointer hover:text-[#0b4c8c]">{part}</span>;
                                    }
                                    return part;
                                  })}
                                </div>

                                {/* Link Preview Card */}
                                <div className="border border-[#e0e0e0] rounded-lg overflow-hidden hover:border-[#1264a3] transition-colors cursor-pointer max-w-md">
                                  <div className="h-1 bg-gradient-to-r from-blue-500 to-purple-600" />
                                  <div className="p-3 space-y-1.5">
                                    <div className="flex items-center gap-2">
                                      <div className="w-4 h-4 rounded bg-gradient-to-br from-blue-500 to-purple-600" />
                                      <span className="text-xs text-[#616061]">survey.doyoueap.hu</span>
                                    </div>
                                    <div className="font-semibold text-sm text-[#1d1c1d]">
                                      EAP Pulse Felmérés
                                    </div>
                                    <div className="text-xs text-[#616061]">
                                      Töltsd ki a felmérést és segíts nekünk fejleszteni a munkahelyi jóllétet
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        {/* Message Actions */}
                        <div className="bg-white border-t border-[#e0e0e0] px-5 py-2 flex items-center gap-4 text-xs text-[#616061]">
                          <button className="hover:bg-[#f8f8f8] px-2 py-1 rounded transition-colors">
                            💬 Válasz szálban
                          </button>
                          <button className="hover:bg-[#f8f8f8] px-2 py-1 rounded transition-colors">
                            😊 Reakció hozzáadása
                          </button>
                          <button className="hover:bg-[#f8f8f8] px-2 py-1 rounded transition-colors">
                            ⋯ Több
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* QR Code Posters Tab */}
        <TabsContent value="qr">
          <Card>
            <CardHeader className="flex flex-row items-start justify-between space-y-0">
              <div className="space-y-1.5">
                <CardTitle>QR kód plakátok</CardTitle>
                <CardDescription>
                  Töltsd le és nyomtasd ki ezeket a plakátokat, hogy elhelyezd őket irodában, konyhában vagy bármilyen közösségi térben.
                </CardDescription>
              </div>
              <div className="flex gap-2 flex-shrink-0">
                <Button 
                  onClick={() => handleDownloadText(qrCodeText, 'qr_kod_szoveg.txt')}
                  variant="outline"
                  size="sm"
                >
                  <Download className="mr-2 h-4 w-4" />
                  Szöveg
                </Button>
                <Button 
                  onClick={() => {
                    const poster = posters.find(p => p.has_gift === qrCodeHasGift);
                    handleDownloadZip(poster?.source_file_url || null);
                  }}
                  variant="outline"
                  size="sm"
                >
                  <Download className="mr-2 h-4 w-4" />
                  ZIP
                </Button>
              </div>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="flex items-center gap-2">
                <Switch
                  id="qr-gift"
                  checked={qrCodeHasGift}
                  onCheckedChange={setQrCodeHasGift}
                />
                <Label htmlFor="qr-gift">Sorsolásos verzió (főnyeremény említése)</Label>
              </div>

              <div className="space-y-4">
                {/* Display poster images - view only */}
                {posters
                  .filter(p => p.has_gift === qrCodeHasGift)
                  .map(poster => (
                    poster.poster_images.length > 0 && (
                      <div key={poster.id} className="space-y-3">
                        <Label className="text-sm font-semibold">Elérhető plakátok:</Label>
                        <div className="flex justify-center gap-6 flex-wrap py-8">
                          {poster.poster_images.map((imageUrl, idx) => (
                            <img 
                              key={idx}
                              src={imageUrl} 
                              alt={`Plakát ${idx + 1}`}
                              className="w-full max-w-[390px] rounded-lg border shadow-lg cursor-pointer hover:opacity-80 transition-opacity"
                              onClick={() => setSelectedImage(imageUrl)}
                            />
                          ))}
                        </div>
                      </div>
                    )
                  ))}

              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <Dialog open={!!selectedImage} onOpenChange={() => setSelectedImage(null)}>
        <DialogContent className="max-w-2xl">
          {selectedImage && (
            <img 
              src={selectedImage} 
              alt="Plakát nagyítva" 
              className="w-full h-auto"
            />
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default CommunicationSupport;
