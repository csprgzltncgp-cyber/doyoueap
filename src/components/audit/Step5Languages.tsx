import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Languages } from "lucide-react";

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
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Nyelvek kiválasztása</h2>
        <p className="text-muted-foreground text-lg">
          Válaszd ki, mely nyelveken legyen elérhető a kérdőív
        </p>
      </div>

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <Languages className="h-6 w-6" />
            Elérhető nyelvek
          </CardTitle>
          <CardDescription className="text-base">
            A kiválasztott nyelvek jelennek meg a munkavállalók számára a kérdőív kitöltése előtt
          </CardDescription>
        </CardHeader>
        <CardContent className="pt-6">
          <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
            {AVAILABLE_LANGUAGES.map((lang) => (
              <button
                key={lang.code}
                type="button"
                onClick={() => toggleLanguage(lang.code)}
                disabled={selectedLanguages.length === 1 && selectedLanguages.includes(lang.code)}
                className={`
                  relative p-3 rounded-lg border-2 transition-all text-left
                  ${selectedLanguages.includes(lang.code)
                    ? 'border-primary bg-primary/5 shadow-md' 
                    : 'border-border hover:border-primary/50 hover:bg-muted/30'
                  }
                  ${selectedLanguages.length === 1 && selectedLanguages.includes(lang.code) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}
                  flex items-center gap-3
                `}
              >
                <Checkbox
                  checked={selectedLanguages.includes(lang.code)}
                  disabled={selectedLanguages.length === 1 && selectedLanguages.includes(lang.code)}
                  className="pointer-events-none"
                />
                <span className="text-sm font-medium">
                  {lang.name} <span className="text-muted-foreground">({lang.code})</span>
                </span>
              </button>
            ))}
          </div>
          <p className="text-sm text-muted-foreground mt-6">
            * Legalább egy nyelv kiválasztása kötelező
          </p>
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        <Button variant="outline" onClick={onBack} size="lg">
          Vissza
        </Button>
        <Button 
          onClick={onNext} 
          disabled={selectedLanguages.length === 0}
          size="lg"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};