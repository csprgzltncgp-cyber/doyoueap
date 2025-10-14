import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Download, Mail, Link as LinkIcon, QrCode } from 'lucide-react';
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

  const handleDownloadZip = (zipUrl: string | null) => {
    if (!zipUrl) {
      toast.error('Nincs elérhető forrás fájl');
      return;
    }
    window.open(zipUrl, '_blank');
    toast.success('ZIP archívum letöltése megkezdődött!');
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
                  <div className="bg-muted/30 rounded-lg p-6 border space-y-4">
                    <div>
                      <div className="text-sm font-medium mb-1">Tárgy:</div>
                      <div className="text-sm text-muted-foreground">{emailTemplate.subject}</div>
                    </div>
                    
                    <div className="border-t pt-4">
                      <div className="text-sm whitespace-pre-wrap">{emailText}</div>
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

              {/* Download Buttons */}
              <div className="space-y-4">
                <div className="grid grid-cols-2 gap-3">
                  <Button 
                    onClick={() => handleDownloadText(qrCodeText, 'qr_kod_szoveg.txt')}
                    variant="outline"
                    className="w-full"
                  >
                    <Download className="mr-2 h-4 w-4" />
                    Plakát szöveg letöltése
                  </Button>

                  {posters
                    .filter(p => p.has_gift === qrCodeHasGift)
                    .map(poster => (
                      poster.source_file_url && (
                        <Button 
                          key={poster.id}
                          onClick={() => handleDownloadZip(poster.source_file_url)}
                          variant="outline"
                          className="w-full"
                        >
                          <Download className="mr-2 h-4 w-4" />
                          Plakát grafikák letöltése (ZIP)
                        </Button>
                      )
                    ))}
                </div>

                {/* Display poster images - view only */}
                {posters
                  .filter(p => p.has_gift === qrCodeHasGift)
                  .map(poster => (
                    poster.poster_images.length > 0 && (
                      <div key={poster.id} className="space-y-3">
                        <Label className="text-sm font-semibold">Elérhető plakátok:</Label>
                        <div className="grid grid-cols-2 gap-3">
                          {poster.poster_images.map((imageUrl, idx) => (
                            <img 
                              key={idx}
                              src={imageUrl} 
                              alt={`Plakát ${idx + 1}`}
                              className="w-full h-32 object-cover rounded-lg border"
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
    </div>
  );
};

export default CommunicationSupport;
