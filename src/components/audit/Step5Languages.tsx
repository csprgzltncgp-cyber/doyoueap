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
  { code: 'HU', name: 'Magyar' },
  { code: 'EN', name: 'English' },
  { code: 'DE', name: 'Deutsch' },
  { code: 'SK', name: 'Slovenčina' },
  { code: 'RO', name: 'Română' },
  { code: 'HR', name: 'Hrvatski' },
];

export const Step5Languages = ({
  selectedLanguages,
  onLanguagesChange,
  onNext,
  onBack,
}: Step5Props) => {
  const toggleLanguage = (langCode: string) => {
    if (selectedLanguages.includes(langCode)) {
      // Magyar nem távolítható el (minimum 1 nyelv kell)
      if (langCode === 'HU' && selectedLanguages.length === 1) return;
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
          <div className="space-y-4">
            {AVAILABLE_LANGUAGES.map((lang) => (
              <div key={lang.code} className="flex items-center space-x-2">
                <Checkbox
                  id={`lang-${lang.code}`}
                  checked={selectedLanguages.includes(lang.code)}
                  onCheckedChange={() => toggleLanguage(lang.code)}
                  disabled={lang.code === 'HU' && selectedLanguages.length === 1}
                />
                <Label htmlFor={`lang-${lang.code}`} className="cursor-pointer flex-1">
                  {lang.name} ({lang.code})
                </Label>
              </div>
            ))}
            <p className="text-sm text-muted-foreground mt-4">
              * A magyar nyelv kötelező és nem távolítható el
            </p>
          </div>
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