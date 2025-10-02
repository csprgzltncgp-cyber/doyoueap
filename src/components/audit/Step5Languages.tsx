import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

interface Step5Props {
  selectedLanguages: string[];
  onLanguagesChange: (languages: string[]) => void;
  onNext: () => void;
  onBack: () => void;
}

const AVAILABLE_LANGUAGES = [
  { code: "HU", name: "Magyar" },
  { code: "EN", name: "English" },
  { code: "DE", name: "Deutsch" },
  { code: "FR", name: "Français" },
  { code: "ES", name: "Español" },
  { code: "IT", name: "Italiano" },
  { code: "PT", name: "Português" },
  { code: "RO", name: "Română" },
  { code: "PL", name: "Polski" },
  { code: "NL", name: "Nederlands" },
  { code: "SV", name: "Svenska" },
  { code: "DA", name: "Dansk" },
  { code: "FI", name: "Suomi" },
  { code: "NO", name: "Norsk" },
  { code: "CS", name: "Čeština" },
  { code: "SK", name: "Slovenčina" },
  { code: "BG", name: "Български" },
  { code: "HR", name: "Hrvatski" },
  { code: "EL", name: "Ελληνικά" },
  { code: "ZH", name: "中文" },
  { code: "JA", name: "日本語" },
  { code: "KO", name: "한국어" },
  { code: "AR", name: "العربية" },
  { code: "RU", name: "Русский" },
  { code: "TR", name: "Türkçe" },
];

export const Step5Languages = ({
  selectedLanguages,
  onLanguagesChange,
  onNext,
  onBack,
}: Step5Props) => {
  const toggleLanguage = (langCode: string) => {
    if (selectedLanguages.includes(langCode)) {
      // Minimum 1 nyelv kell
      if (selectedLanguages.length === 1) return;
      onLanguagesChange(selectedLanguages.filter((l) => l !== langCode));
    } else {
      onLanguagesChange([...selectedLanguages, langCode]);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Nyelvek kiválasztása</h2>
        <p className="text-muted-foreground">
          Válassza ki, mely nyelveken legyen elérhető a kérdőív
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Elérhető nyelvek</CardTitle>
          <CardDescription>
            A kiválasztott nyelvek jelennek meg a munkavállalók számára a kérdőív kitöltése előtt
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
            {AVAILABLE_LANGUAGES.map((lang) => (
              <div key={lang.code} className="flex items-center space-x-2">
                <Checkbox
                  id={`lang-${lang.code}`}
                  checked={selectedLanguages.includes(lang.code)}
                  onCheckedChange={() => toggleLanguage(lang.code)}
                  disabled={selectedLanguages.length === 1 && selectedLanguages.includes(lang.code)}
                />
                <Label htmlFor={`lang-${lang.code}`} className="cursor-pointer flex-1">
                  {lang.name} ({lang.code})
                </Label>
              </div>
            ))}
          </div>
          <p className="text-sm text-muted-foreground mt-4">
            * Legalább egy nyelv kiválasztása kötelező
          </p>
        </CardContent>
      </Card>

      <div className="flex justify-between">
        <Button variant="outline" onClick={onBack}>
          Vissza
        </Button>
        <Button onClick={onNext} disabled={selectedLanguages.length === 0}>
          Következő lépés
        </Button>
      </div>
    </div>
  );
};