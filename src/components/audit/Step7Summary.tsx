import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { CheckCircle2, Link as LinkIcon, Upload, QrCode, Calendar, Languages, FileText, Gift, Palette } from "lucide-react";
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

  useEffect(() => {
    if (auditData.giftId) {
      fetchGift(auditData.giftId);
    }
  }, [auditData.giftId]);

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
        return <Upload className="h-4 w-4" />;
      case 'public_link':
        return <LinkIcon className="h-4 w-4" />;
      case 'qr_code':
        return <QrCode className="h-4 w-4" />;
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
          <Badge className="text-base px-4 py-2">{getAccessModeLabel(auditData.accessMode)}</Badge>
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
              Ajándéksorsolás
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
              <Badge variant="secondary" className="text-base px-4 py-2">
                {auditData.drawMode === 'auto' ? 'Automatikus záráskor' : 'Manuális indítás'}
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

      {auditData.logoFile && (
        <Card className="border-2">
          <CardHeader className="bg-muted/30">
            <CardTitle className="text-xl">Logó</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <p className="text-base">
              <CheckCircle2 className="inline h-5 w-5 mr-2 text-green-600" />
              {auditData.logoFile.name}
            </p>
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
            A felmérés teljesen anonim, az eredmények összesítve jelennek meg a HR felületén.
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
        >
          {loading ? 'Felmérés indítása...' : 'Felmérés indítása'}
        </Button>
      </div>
    </div>
  );
};