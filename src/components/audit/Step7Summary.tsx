import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Link as LinkIcon, Upload, QrCode, Calendar, Languages, FileText, Gift, Palette, Mail, MousePointerClick, CheckCircle, Trophy } from "lucide-react";
import { supabase } from '@/integrations/supabase/client';

interface Step7Props {
  auditData: {
    programName: string;
    accessMode: string;
    emailBody: string;
    logoFile: File | null;
    customColors: any;
    startDate: string;
    expiresAt: string;
    enableRecurrence: boolean;
    recurrenceFrequency: string;
    selectedLanguages: string[];
    eapProgramUrl: string;
    giftId?: string;
    drawMode?: 'auto' | 'manual';
  };
  onSubmit: () => void;
  onBack: () => void;
  loading: boolean;
}

export const Step7Summary = ({ auditData, onSubmit, onBack, loading }: Step7Props) => {
  const [giftName, setGiftName] = useState<string>('');
  const [giftValue, setGiftValue] = useState<number>(0);
  const [logoPreview, setLogoPreview] = useState<string | null>(null);

  useEffect(() => {
    if (auditData.giftId) {
      fetchGift(auditData.giftId);
    }
  }, [auditData.giftId]);

  useEffect(() => {
    if (auditData.logoFile) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setLogoPreview(reader.result as string);
      };
      reader.readAsDataURL(auditData.logoFile);
    } else {
      setLogoPreview(null);
    }
  }, [auditData.logoFile]);

  const fetchGift = async (giftId: string) => {
    const { data } = await supabase
      .from('gifts')
      .select('name, value_eur')
      .eq('id', giftId)
      .single();
    
    if (data) {
      setGiftName(data.name);
      setGiftValue(data.value_eur);
    }
  };

  const formatEUR = (value: number): string => {
    const formatted = new Intl.NumberFormat('hu-HU', {
      style: 'decimal',
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    }).format(value);
    return formatted.replace(/\s/g, ' ') + ' €';
  };

  const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('hu-HU', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const getAccessModeLabel = (mode: string) => {
    switch (mode) {
      case 'tokenes':
        return 'Egyedi tokenes link';
      case 'public_link':
        return 'Egységes nyilvános link';
      case 'qr_code':
        return 'QR kód / plakát';
      default:
        return mode;
    }
  };

  const getAccessModeIcon = (mode: string) => {
    switch (mode) {
      case 'tokenes':
        return <Mail className="h-3 w-3" />;
      case 'public_link':
        return <MousePointerClick className="h-3 w-3" />;
      case 'qr_code':
        return <CheckCircle className="h-3 w-3" />;
      default:
        return null;
    }
  };

  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Indítás összegzés</h2>
        <p className="text-muted-foreground text-lg">
          Ellenőrizze a felmérés beállításait indítás előtt
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <FileText className="h-6 w-6" />
            Alapadatok
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div>
            <p className="text-sm text-muted-foreground">Program elnevezése</p>
            <p className="font-medium text-base">{auditData.programName || 'EAP'}</p>
          </div>
          <div>
            <p className="text-sm text-muted-foreground">Weboldal címe</p>
            <p className="font-medium text-base">{auditData.eapProgramUrl || 'https://doyoueap.hu'}</p>
          </div>
        </CardContent>
      </Card>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            {getAccessModeIcon(auditData.accessMode)}
            Hozzáférési mód
          </CardTitle>
        </CardHeader>
        <CardContent className="pt-6">
          <Badge variant="outline" className="gap-1 text-base px-4 py-2">
            {getAccessModeIcon(auditData.accessMode)}
            {getAccessModeLabel(auditData.accessMode)}
          </Badge>
        </CardContent>
      </Card>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Calendar className="h-6 w-6" />
            Időzítés
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div>
            <p className="text-sm text-muted-foreground">Kezdő dátum</p>
            <p className="font-medium text-base">{formatDate(auditData.startDate)}</p>
          </div>
          <div>
            <p className="text-sm text-muted-foreground">Záró dátum</p>
            <p className="font-medium text-base">{formatDate(auditData.expiresAt)}</p>
          </div>
          {auditData.enableRecurrence && (
            <div>
              <p className="text-sm text-muted-foreground">Ismétlődés</p>
              <Badge variant="secondary" className="text-base px-4 py-2">
                {auditData.recurrenceFrequency === 'monthly' && 'Havonta'}
                {auditData.recurrenceFrequency === 'quarterly' && 'Negyedévente'}
                {auditData.recurrenceFrequency === 'biannually' && 'Félévente'}
                {auditData.recurrenceFrequency === 'annually' && 'Évente'}
              </Badge>
            </div>
          )}
        </CardContent>
      </Card>

      {auditData.giftId && (
        <Card className="border-2">
          <CardHeader className="bg-muted/30">
            <CardTitle className="flex items-center gap-2 text-xl">
              <Gift className="h-6 w-6" />
              Nyereményjáték
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4 pt-6">
            <div>
              <p className="text-sm text-muted-foreground">Ajándék</p>
              <p className="font-medium text-base">{giftName}</p>
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Érték</p>
              <p className="text-2xl font-bold text-primary">{formatEUR(giftValue)}</p>
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Sorsolás módja</p>
              <Badge 
                variant="outline" 
                className="gap-1 text-base px-4 py-2 bg-yellow-50 text-yellow-700 border-yellow-300"
              >
                <Trophy className="h-3 w-3" />
                {auditData.drawMode === 'auto' ? 'Automatikus sorsolással' : 'Manuális sorsolással'}
              </Badge>
            </div>
          </CardContent>
        </Card>
      )}

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Languages className="h-6 w-6" />
            Elérhető nyelvek
          </CardTitle>
        </CardHeader>
        <CardContent className="pt-6">
          <div className="flex flex-wrap gap-2">
            {auditData.selectedLanguages.map((lang) => (
              <Badge key={lang} variant="outline" className="text-base px-4 py-2">
                {lang}
              </Badge>
            ))}
          </div>
        </CardContent>
      </Card>

      {auditData.logoFile && logoPreview && (
        <Card className="border-2">
          <CardHeader className="bg-muted/30">
            <CardTitle className="text-xl">Logó</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <div className="border-2 border-dashed border-muted rounded-lg p-4 bg-muted/20 inline-block">
              <img 
                src={logoPreview} 
                alt="Logo előnézet" 
                className="h-24 w-auto object-contain"
              />
            </div>
          </CardContent>
        </Card>
      )}

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Palette className="h-6 w-6" />
            Elsődleges szín
          </CardTitle>
        </CardHeader>
        <CardContent className="pt-6">
          <div className="flex items-center gap-4">
            <div 
              className="w-16 h-16 rounded-lg border-2 border-border shadow-sm"
              style={{ backgroundColor: auditData.customColors?.primary || '#3b82f6' }}
            />
            <p className="font-mono text-base font-medium">
              {auditData.customColors?.primary || '#3b82f6'}
            </p>
          </div>
        </CardContent>
      </Card>

      <Card className="border-2 bg-primary/5 border-primary/20">
        <CardHeader className="bg-primary/10">
          <CardTitle className="text-xl text-primary">
            Biztonsági emlékeztető
          </CardTitle>
        </CardHeader>
        <CardContent className="pt-6">
          <p className="text-base leading-relaxed">
            A felmérés teljesen anonim, az eredmények összesítve jelennek meg a Riportok menüben.
            A munkavállalók személyes adatai védettek.
          </p>
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onSubmit} 
          disabled={loading}
          size="lg"
          variant="dark"
        >
          {loading ? 'Felmérés indítása...' : 'Felmérés indítása'}
        </Button>
      </div>
    </div>
  );
};