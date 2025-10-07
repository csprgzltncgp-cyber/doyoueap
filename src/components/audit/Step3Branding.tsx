import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Upload } from "lucide-react";
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

  const handleColorChange = (key: string, value: string) => {
    onColorsChange({ ...customColors, [key]: value });
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">User felület testreszabása</h2>
        <p className="text-muted-foreground">
          Személyre szabhatja a kérdőív megjelenését a cég brandjéhez igazítva
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Logó feltöltése</CardTitle>
          <CardDescription>
            Opcionális: Feltölthet saját logót (max 2MB, PNG/JPG/SVG)
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
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
              onClick={() => document.getElementById('logo-upload')?.click()}
            >
              <Upload className="mr-2 h-4 w-4" />
              {logoFile ? logoFile.name : 'Logo feltöltése'}
            </Button>
            <p className="text-sm text-muted-foreground">
              Ha nem tölt fel logót, az alapértelmezett doyoueap logó jelenik meg
            </p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Szín testreszabása</CardTitle>
          <CardDescription>
            Válassza ki a kérdőív fő színét (gombok és státuszcsík)
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            <Label htmlFor="primary-color">Elsődleges szín</Label>
            <div className="flex gap-2">
              <Input
                id="primary-color"
                type="color"
                value={customColors.primary || '#3b82f6'}
                onChange={(e) => handleColorChange('primary', e.target.value)}
                className="w-16 h-10"
              />
              <Input
                type="text"
                value={customColors.primary || '#3b82f6'}
                onChange={(e) => handleColorChange('primary', e.target.value)}
                placeholder="#3b82f6"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button 
          onClick={onNext}
          style={{
            backgroundColor: '#000000',
            color: 'white'
          }}
          className="hover:opacity-90"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};