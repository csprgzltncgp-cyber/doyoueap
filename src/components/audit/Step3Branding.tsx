import { useState, useEffect } from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Upload, X, Palette, Image } from "lucide-react";
import { toast } from "sonner";

interface Step3Props {
  logoFile: File | null;
  customColors: { primary?: string };
  onLogoChange: (file: File | null) => void;
  onColorsChange: (colors: any) => void;
  onNext: () => void;
  onBack: () => void;
}

export const Step3Branding = ({
  logoFile,
  customColors,
  onLogoChange,
  onColorsChange,
  onNext,
  onBack,
}: Step3Props) => {
  const [logoPreview, setLogoPreview] = useState<string | null>(null);

  useEffect(() => {
    if (logoFile) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setLogoPreview(reader.result as string);
      };
      reader.readAsDataURL(logoFile);
    } else {
      setLogoPreview(null);
    }
  }, [logoFile]);

  const handleLogoUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      if (file.size > 2 * 1024 * 1024) {
        toast.error('A fájl mérete maximum 2MB lehet');
        return;
      }
      onLogoChange(file);
    }
  };

  const handleRemoveLogo = () => {
    onLogoChange(null);
    setLogoPreview(null);
  };

  const handleColorChange = (key: string, value: string) => {
    onColorsChange({ ...customColors, [key]: value });
  };

  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">User felület testreszabása</h2>
        <p className="text-muted-foreground text-lg">
          Személyre szabhatja a kérdőív megjelenését a cég brandjéhez igazítva
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Image className="h-6 w-6" />
            Logó feltöltése
          </CardTitle>
          <CardDescription className="text-base">
            Opcionális: Feltölthet saját logót (max 2MB, PNG/JPG/SVG)
          </CardDescription>
        </CardHeader>
        <CardContent className="pt-6">
          <div className="space-y-4">
            {logoPreview && (
              <div className="relative inline-block">
                <div className="border-2 border-dashed border-muted rounded-lg p-4 bg-muted/20">
                  <img 
                    src={logoPreview} 
                    alt="Logo előnézet" 
                    className="h-24 w-auto object-contain"
                  />
                </div>
                <Button
                  type="button"
                  variant="destructive"
                  size="icon"
                  className="absolute -top-2 -right-2 h-8 w-8 rounded-full"
                  onClick={handleRemoveLogo}
                >
                  <X className="h-4 w-4" />
                </Button>
              </div>
            )}
            <Input
              id="logo-upload"
              type="file"
              accept="image/*"
              onChange={handleLogoUpload}
              className="hidden"
            />
            <Button
              type="button"
              variant="outline"
              size="lg"
              onClick={() => document.getElementById('logo-upload')?.click()}
              className="w-full h-14"
            >
              <Upload className="mr-2 h-5 w-5" />
              {logoFile ? 'Logo cseréje' : 'Logo feltöltése'}
            </Button>
            <p className="text-sm text-muted-foreground">
              Ha nem tölt fel logót, az alapértelmezett doyoueap logó jelenik meg
            </p>
          </div>
        </CardContent>
      </Card>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Palette className="h-6 w-6" />
            Szín testreszabása
          </CardTitle>
          <CardDescription className="text-base">
            Válassza ki a kérdőív fő színét (gombok és státuszcsík)
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4 pt-6">
          <div className="space-y-2">
            <Label htmlFor="primary-color" className="text-base font-medium">Elsődleges szín</Label>
            <div className="flex gap-2">
              <Input
                id="primary-color"
                type="color"
                value={customColors.primary || '#3b82f6'}
                onChange={(e) => handleColorChange('primary', e.target.value)}
                className="w-16 h-12"
              />
              <Input
                type="text"
                value={customColors.primary || '#3b82f6'}
                onChange={(e) => handleColorChange('primary', e.target.value)}
                placeholder="#3b82f6"
                className="h-12 text-base"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext}
          size="lg"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};