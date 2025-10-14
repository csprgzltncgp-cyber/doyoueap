import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Download, Mail, Link as LinkIcon, QrCode, Gift, Sparkles, FileDown } from 'lucide-react';
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

const CommunicationSupport = () => {
  const [templates, setTemplates] = useState<Template[]>([]);
  const [loading, setLoading] = useState(true);

  // Email
  const [emailProgramName, setEmailProgramName] = useState('DoYouEAP');
  const [emailHasGift, setEmailHasGift] = useState(false);

  // Public Link
  const [publicLinkProgramName, setPublicLinkProgramName] = useState('DoYouEAP');
  const [publicLinkHasGift, setPublicLinkHasGift] = useState(false);

  // QR Code
  const [qrCodeProgramName, setQrCodeProgramName] = useState('DoYouEAP');
  const [qrCodeHasGift, setQrCodeHasGift] = useState(false);

  useEffect(() => {
    fetchTemplates();
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

  const getTemplate = (type: 'email' | 'public_link' | 'qr_code', hasGift: boolean) => {
    return templates.find(t => t.template_type === type && t.has_gift === hasGift);
  };

  const generateText = (type: 'email' | 'public_link' | 'qr_code', programName: string, hasGift: boolean) => {
    const template = getTemplate(type, hasGift);
    if (!template) return '';

    const exampleLink = 'https://survey.doyoueap.hu/abc123';
    return template.content
      .replace(/{programName}/g, programName)
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

  const handleDownloadZip = () => {
    window.open('/posters_A4.zip', '_blank');
    toast.success('ZIP archívum letöltése megkezdődött!');
  };

  const emailTemplate = getTemplate('email', emailHasGift);
  const publicLinkTemplate = getTemplate('public_link', publicLinkHasGift);
  const qrCodeTemplate = getTemplate('qr_code', qrCodeHasGift);

  const emailText = generateText('email', emailProgramName, emailHasGift);
  const publicLinkText = generateText('public_link', publicLinkProgramName, publicLinkHasGift);
  const qrCodeText = generateText('qr_code', qrCodeProgramName, qrCodeHasGift);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="text-center space-y-3">
          <Sparkles className="h-8 w-8 mx-auto animate-pulse text-primary" />
          <p className="text-muted-foreground">Betöltés...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-background via-background to-primary/5">
      <div className="max-w-7xl mx-auto p-8 space-y-8">
        {/* Hero Section */}
        <div className="text-center space-y-4 py-8">
          <div className="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-full mb-4">
            <Sparkles className="h-4 w-4 text-primary" />
            <span className="text-sm font-medium text-primary">Professzionális kommunikációs eszközök</span>
          </div>
          <h1 className="text-4xl md:text-5xl font-bold bg-gradient-to-r from-foreground to-foreground/60 bg-clip-text text-transparent">
            Kommunikációs támogatás
          </h1>
          <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
            Letölthető sablonok és anyagok a felmérések hatékony kommunikálásához
          </p>
        </div>

        {/* Email Template Card */}
        <Card className="border-2 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
          <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-500" />
          <CardHeader className="bg-gradient-to-br from-blue-50/50 to-transparent dark:from-blue-950/20">
            <div className="flex items-center gap-3 mb-2">
              <div className="p-3 bg-blue-500 rounded-lg">
                <Mail className="h-6 w-6 text-white" />
              </div>
              <div>
                <CardTitle className="text-2xl">Email sablon</CardTitle>
                <CardDescription>Személyre szabott email üzenet munkavállalóknak</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-6 pt-6">
            <div className="grid md:grid-cols-2 gap-4 p-4 bg-muted/30 rounded-lg">
              <div className="space-y-2">
                <Label htmlFor="email-program-name">Program neve</Label>
                <Input
                  id="email-program-name"
                  value={emailProgramName}
                  onChange={(e) => setEmailProgramName(e.target.value)}
                  placeholder="Pl. DoYouEAP"
                  className="bg-background"
                />
              </div>
              <div className="space-y-2 flex items-end">
                <div className="flex items-center gap-3 h-10">
                  <Switch
                    id="email-gift"
                    checked={emailHasGift}
                    onCheckedChange={setEmailHasGift}
                  />
                  <Label htmlFor="email-gift" className="flex items-center gap-2 cursor-pointer">
                    <Gift className="h-4 w-4 text-purple-500" />
                    Nyereményjátékkal
                  </Label>
                </div>
              </div>
            </div>

            {emailTemplate && (
              <div className="space-y-4">
                <div className="bg-white dark:bg-slate-900 border-2 rounded-lg p-6 space-y-4">
                  <div className="flex items-center justify-between border-b pb-3">
                    <span className="font-semibold">Tárgy:</span>
                    <span className="text-sm text-muted-foreground">Email előnézet</span>
                  </div>
                  <p className="font-medium text-lg">{emailTemplate.subject?.replace(/{programName}/g, emailProgramName)}</p>
                  <div className="border-t pt-4">
                    <pre className="whitespace-pre-wrap font-sans text-sm leading-relaxed">{emailText}</pre>
                  </div>
                </div>
                <Button 
                  onClick={() => handleDownloadText(emailText, 'email_sablon.txt')}
                  className="w-full"
                  size="lg"
                >
                  <Download className="h-5 w-5 mr-2" />
                  Email szöveg letöltése
                </Button>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Public Link Card */}
        <Card className="border-2 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
          <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-emerald-500" />
          <CardHeader className="bg-gradient-to-br from-green-50/50 to-transparent dark:from-green-950/20">
            <div className="flex items-center gap-3 mb-2">
              <div className="p-3 bg-green-500 rounded-lg">
                <LinkIcon className="h-6 w-6 text-white" />
              </div>
              <div>
                <CardTitle className="text-2xl">Nyilvános link sablon</CardTitle>
                <CardDescription>Intranet, hírlevél vagy közlemény szöveg</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-6 pt-6">
            <div className="grid md:grid-cols-2 gap-4 p-4 bg-muted/30 rounded-lg">
              <div className="space-y-2">
                <Label htmlFor="public-link-program-name">Program neve</Label>
                <Input
                  id="public-link-program-name"
                  value={publicLinkProgramName}
                  onChange={(e) => setPublicLinkProgramName(e.target.value)}
                  placeholder="Pl. DoYouEAP"
                  className="bg-background"
                />
              </div>
              <div className="space-y-2 flex items-end">
                <div className="flex items-center gap-3 h-10">
                  <Switch
                    id="public-link-gift"
                    checked={publicLinkHasGift}
                    onCheckedChange={setPublicLinkHasGift}
                  />
                  <Label htmlFor="public-link-gift" className="flex items-center gap-2 cursor-pointer">
                    <Gift className="h-4 w-4 text-purple-500" />
                    Nyereményjátékkal
                  </Label>
                </div>
              </div>
            </div>

            {publicLinkTemplate && (
              <div className="space-y-4">
                <div className="bg-white dark:bg-slate-900 border-2 rounded-lg p-6">
                  <pre className="whitespace-pre-wrap font-sans text-sm leading-relaxed">{publicLinkText}</pre>
                </div>
                <Button 
                  onClick={() => handleDownloadText(publicLinkText, 'nyilvanos_link_szoveg.txt')}
                  className="w-full"
                  size="lg"
                >
                  <Download className="h-5 w-5 mr-2" />
                  Szöveg letöltése
                </Button>
              </div>
            )}
          </CardContent>
        </Card>

        {/* QR Code Card */}
        <Card className="border-2 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
          <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500" />
          <CardHeader className="bg-gradient-to-br from-purple-50/50 to-transparent dark:from-purple-950/20">
            <div className="flex items-center gap-3 mb-2">
              <div className="p-3 bg-purple-500 rounded-lg">
                <QrCode className="h-6 w-6 text-white" />
              </div>
              <div>
                <CardTitle className="text-2xl">QR kód plakátok</CardTitle>
                <CardDescription>Nyomtatható plakátok céges helyiségekbe</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-6 pt-6">
            <div className="grid md:grid-cols-2 gap-4 p-4 bg-muted/30 rounded-lg">
              <div className="space-y-2">
                <Label htmlFor="qr-code-program-name">Program neve</Label>
                <Input
                  id="qr-code-program-name"
                  value={qrCodeProgramName}
                  onChange={(e) => setQrCodeProgramName(e.target.value)}
                  placeholder="Pl. DoYouEAP"
                  className="bg-background"
                />
              </div>
              <div className="space-y-2 flex items-end">
                <div className="flex items-center gap-3 h-10">
                  <Switch
                    id="qr-code-gift"
                    checked={qrCodeHasGift}
                    onCheckedChange={setQrCodeHasGift}
                  />
                  <Label htmlFor="qr-code-gift" className="flex items-center gap-2 cursor-pointer">
                    <Gift className="h-4 w-4 text-purple-500" />
                    Nyereményjátékkal
                  </Label>
                </div>
              </div>
            </div>

            {qrCodeTemplate && (
              <div className="space-y-4">
                <div className="bg-white dark:bg-slate-900 border-2 rounded-lg p-6">
                  <pre className="whitespace-pre-wrap font-sans text-sm leading-relaxed">{qrCodeText}</pre>
                </div>
                <Button 
                  onClick={() => handleDownloadText(qrCodeText, 'qr_kod_szoveg.txt')}
                  className="w-full"
                  size="lg"
                >
                  <Download className="h-5 w-5 mr-2" />
                  Szöveg letöltése
                </Button>
              </div>
            )}

            {/* Poster Gallery with Mockup */}
            <div className="space-y-6 mt-8">
              <div className="text-center">
                <h3 className="text-xl font-semibold mb-2">Plakát előnézet céges környezetben</h3>
                <p className="text-sm text-muted-foreground">Így nézhetnek ki a plakátok az irodában</p>
              </div>
              
              {/* Mockup Wall Display */}
              <div 
                className="relative rounded-xl overflow-hidden shadow-2xl"
                style={{
                  backgroundImage: `url(${posterWall})`,
                  backgroundSize: 'cover',
                  backgroundPosition: 'center',
                  minHeight: '400px'
                }}
              >
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                <div className="relative z-10 p-8 h-full flex items-center justify-center">
                  <div className="grid grid-cols-2 gap-6 max-w-3xl">
                    {qrCodeHasGift ? (
                      <>
                        <div className="transform hover:scale-105 transition-transform duration-300">
                          <img src={poster3} alt="Samsung Buds plakát" className="rounded-lg shadow-2xl" />
                        </div>
                        <div className="transform hover:scale-105 transition-transform duration-300">
                          <img src={poster4} alt="Lidl hűtő plakát" className="rounded-lg shadow-2xl" />
                        </div>
                      </>
                    ) : (
                      <>
                        <div className="transform hover:scale-105 transition-transform duration-300">
                          <img src={poster1} alt="Alap plakát 1" className="rounded-lg shadow-2xl" />
                        </div>
                        <div className="transform hover:scale-105 transition-transform duration-300">
                          <img src={poster2} alt="Alap plakát 2" className="rounded-lg shadow-2xl" />
                        </div>
                      </>
                    )}
                  </div>
                </div>
              </div>

              {/* Download Buttons */}
              <div className="grid md:grid-cols-2 gap-4">
                {qrCodeHasGift ? (
                  <>
                    <Button
                      onClick={() => handleDownloadPoster(poster3, 'samsung_buds_plakat.png')}
                      variant="outline"
                      size="lg"
                      className="w-full"
                    >
                      <Download className="h-4 w-4 mr-2" />
                      Samsung Buds plakát
                    </Button>
                    <Button
                      onClick={() => handleDownloadPoster(poster4, 'lidl_huto_plakat.png')}
                      variant="outline"
                      size="lg"
                      className="w-full"
                    >
                      <Download className="h-4 w-4 mr-2" />
                      Lidl hűtő plakát
                    </Button>
                  </>
                ) : (
                  <>
                    <Button
                      onClick={() => handleDownloadPoster(poster1, 'plakat_1.png')}
                      variant="outline"
                      size="lg"
                      className="w-full"
                    >
                      <Download className="h-4 w-4 mr-2" />
                      Plakát 1
                    </Button>
                    <Button
                      onClick={() => handleDownloadPoster(poster2, 'plakat_2.png')}
                      variant="outline"
                      size="lg"
                      className="w-full"
                    >
                      <Download className="h-4 w-4 mr-2" />
                      Plakát 2
                    </Button>
                  </>
                )}
              </div>

              <Button 
                onClick={handleDownloadZip}
                className="w-full"
                size="lg"
                variant="default"
              >
                <FileDown className="h-5 w-5 mr-2" />
                Összes A4-es plakát letöltése (ZIP)
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default CommunicationSupport;
