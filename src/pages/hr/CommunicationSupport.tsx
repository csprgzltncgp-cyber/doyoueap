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
            <CardHeader>
              <CardTitle>Email sablon</CardTitle>
              <CardDescription>
                Használd ezt a sablont, ha emailben szeretnéd megosztani a felmérést a munkatársaiddal.
              </CardDescription>
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
                  <div className="bg-background rounded-lg border-2 shadow-lg overflow-hidden">
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
                        <span className="text-sm text-muted-foreground italic">munkatársak@cég.hu</span>
                      </div>
                      
                      <div className="flex items-center gap-3 pb-2 border-b">
                        <span className="text-sm text-muted-foreground w-20">Tárgy:</span>
                        <span className="text-sm font-medium">{emailTemplate.subject}</span>
                      </div>
                    </div>

                    {/* Email Body */}
                    <div className="px-6 pb-6">
                      <div className="rounded-md p-6 min-h-[200px] border">
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

                  <Button
                    onClick={() => handleDownloadText(emailText, 'email_sablon.txt')}
                    variant="outline"
                    size="sm"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    Szöveg letöltése
                  </Button>
                </>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* Public Link Template Tab */}
        <TabsContent value="link">
          <Card>
            <CardHeader>
              <CardTitle>Publikus link sablon</CardTitle>
              <CardDescription>
                Használd ezt a sablont, ha publikus linken keresztül szeretnéd megosztani a felmérést.
              </CardDescription>
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
                  <div className="bg-muted/30 rounded-lg p-6 border">
                    <div className="text-sm whitespace-pre-wrap">{publicLinkText}</div>
                  </div>

                  <Button 
                    onClick={() => handleDownloadText(publicLinkText, 'nyilvanos_link_szoveg.txt')}
                    variant="outline"
                    size="sm"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    Szöveg letöltése
                  </Button>
                </>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* QR Code Posters Tab */}
        <TabsContent value="qr">
          <Card>
            <CardHeader>
              <CardTitle>QR kód plakátok</CardTitle>
              <CardDescription>
                Töltsd le és nyomtasd ki ezeket a plakátokat, hogy elhelyezd őket irodában, konyhában vagy bármilyen közösségi térben.
              </CardDescription>
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
                        <div className="flex justify-center gap-6 flex-wrap">
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

                {/* Download Buttons */}
                <div className="flex gap-3 justify-start max-w-md">
                  <Button 
                    onClick={() => handleDownloadText(qrCodeText, 'qr_kod_szoveg.txt')}
                    variant="outline"
                    size="sm"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    Plakát szöveg letöltése
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
                    Plakát grafikák letöltése (ZIP)
                  </Button>
                </div>
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
