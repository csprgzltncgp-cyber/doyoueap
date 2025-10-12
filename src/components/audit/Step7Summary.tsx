import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { CheckCircle2, Link as LinkIcon, Upload, QrCode, Calendar, Languages, FileText, Gift } from "lucide-react";
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
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Indítás összegzés</h2>
        <p className="text-muted-foreground">
          Ellenőrizze a felmérés beállításait indítás előtt
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <FileText className="h-5 w-5" />
            Alapadatok
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-3">
          <div>
            <p className="text-sm text-muted-foreground">Program elnevezése</p>
            <p className="font-medium">{auditData.programName || 'EAP'}</p>
          </div>
          <div>
            <p className="text-sm text-muted-foreground">Weboldal címe</p>
            <p className="font-medium">{auditData.eapProgramUrl || 'https://doyoueap.hu'}</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            {getAccessModeIcon(auditData.accessMode)}
            Hozzáférési mód
          </CardTitle>
        </CardHeader>
        <CardContent>
          <Badge>{getAccessModeLabel(auditData.accessMode)}</Badge>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calendar className="h-5 w-5" />
            Időzítés
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-3">
          <div>
            <p className="text-sm text-muted-foreground">Kezdő dátum</p>
            <p className="font-medium">{formatDate(auditData.startDate)}</p>
          </div>
          <div>
            <p className="text-sm text-muted-foreground">Záró dátum</p>
            <p className="font-medium">{formatDate(auditData.expiresAt)}</p>
          </div>
          {auditData.enableRecurrence && (
            <div>
              <p className="text-sm text-muted-foreground">Ismétlődés</p>
              <Badge variant="secondary">
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
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Gift className="h-5 w-5" />
              Ajándéksorsolás
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-muted-foreground">Ajándék</p>
              <p className="font-medium">{giftName}</p>
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Érték</p>
              <p className="text-xl font-bold text-primary">{formatEUR(giftValue)}</p>
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Sorsolás módja</p>
              <Badge variant="secondary">
                {auditData.drawMode === 'auto' ? 'Automatikus záráskor' : 'Manuális indítás'}
              </Badge>
            </div>
          </CardContent>
        </Card>
      )}

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Languages className="h-5 w-5" />
            Elérhető nyelvek
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-2">
            {auditData.selectedLanguages.map((lang) => (
              <Badge key={lang} variant="outline">
                {lang}
              </Badge>
            ))}
          </div>
        </CardContent>
      </Card>

      {auditData.logoFile && (
        <Card>
          <CardHeader>
            <CardTitle>Logó</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-sm">
              <CheckCircle2 className="inline h-4 w-4 mr-1 text-green-600" />
              {auditData.logoFile.name}
            </p>
          </CardContent>
        </Card>
      )}

      <Card className="bg-blue-50 dark:bg-blue-950">
        <CardHeader>
          <CardTitle className="text-blue-900 dark:text-blue-100">
            Biztonsági emlékeztető
          </CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-sm text-blue-800 dark:text-blue-200">
            A felmérés teljesen anonim, az eredmények összesítve jelennek meg a HR felületén.
            A munkavállalók személyes adatai védettek.
          </p>
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button 
          onClick={onSubmit} 
          disabled={loading}
          style={{
            backgroundColor: '#000000',
            color: 'white'
          }}
          className="hover:opacity-90"
        >
          {loading ? 'Felmérés indítása...' : 'Felmérés indítása'}
        </Button>
      </div>
    </div>
  );
};