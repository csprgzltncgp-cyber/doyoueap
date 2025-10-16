import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { FileText, Globe, Building2 } from "lucide-react";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

interface Step6Props {
  programName: string;
  eapProgramUrl: string;
  onProgramNameChange: (name: string) => void;
  onEapProgramUrlChange: (url: string) => void;
  onNext: () => void;
  onBack: () => void;
  isPartner?: boolean;
  companies?: Array<{ id: string; company_name: string }>;
  selectedCompanyId?: string;
  onCompanySelect?: (companyId: string) => void;
}

export const Step6ProgramName = ({
  programName,
  eapProgramUrl,
  onProgramNameChange,
  onEapProgramUrlChange,
  onNext,
  onBack,
  isPartner = false,
  companies = [],
  selectedCompanyId = '',
  onCompanySelect = () => {},
}: Step6Props) => {
  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Program adatai</h2>
        <p className="text-muted-foreground text-lg">
          Add meg az EAP program alapvető információit
        </p>
      </div>

      {isPartner && (
        <Card className="border-2">
          <CardHeader className="bg-muted/30">
            <CardTitle className="flex items-center gap-2 text-xl">
              <Building2 className="h-6 w-6" />
              Ügyfélcég kiválasztása
            </CardTitle>
            <CardDescription className="text-base mt-4 text-foreground">
              Válaszd ki, hogy melyik regisztrált ügyfélcégednek szeretnél felmérést indítani.
            </CardDescription>
          </CardHeader>
          <CardContent className="pt-6">
            <div className="space-y-2">
              <Label htmlFor="company-select" className="text-base font-medium">Ügyfélcég</Label>
              <Select value={selectedCompanyId} onValueChange={onCompanySelect}>
                <SelectTrigger id="company-select" className="h-12 text-base">
                  <SelectValue placeholder="Válassz egy ügyfélcéget" />
                </SelectTrigger>
                <SelectContent>
                  {companies.length === 0 ? (
                    <div className="p-2 text-sm text-muted-foreground">
                      Még nincs regisztrált ügyfélcég
                    </div>
                  ) : (
                    companies.map((company) => (
                      <SelectItem key={company.id} value={company.id}>
                        {company.company_name}
                      </SelectItem>
                    ))
                  )}
                </SelectContent>
              </Select>
              {companies.length === 0 && (
                <p className="text-sm text-muted-foreground">
                  Menj a Partner Központba új ügyfélcég regisztrálásához.
                </p>
              )}
            </div>
          </CardContent>
        </Card>
      )}

      <Card className="border-2">
        <CardHeader className="bg-muted/30">
          <CardTitle className="flex items-center gap-2 text-xl">
            <FileText className="h-6 w-6" />
            Program elnevezése
          </CardTitle>
          <CardDescription className="text-base space-y-3 mt-4 text-foreground">
            <p className="leading-relaxed">
              Add meg az EAP program cégeden belüli elnevezését (maximum 60 karakter).
            </p>
            <p className="leading-relaxed">
              Ez az elnevezés csak a kérdőív legelső kérdésében fog megjelenni:
            </p>
            <p className="italic leading-relaxed pl-4 border-l-2 border-primary/30">
              „Tudtad, hogy a munkahelyeden elérhető egy támogatási program, amit &lt;program neve&gt; néven ismerhetsz? 
              Ez a szolgáltatás segítséget nyújt neked és családodnak különböző munkahelyi vagy magánéleti kihívások kezeléséhez, 
              például stresszhelyzetekben, konfliktusok megoldásában vagy akár pénzügyi tanácsadásban is."
            </p>
            <p className="leading-relaxed">
              A kérdőív további részében mindig általános „program" megnevezéssel hivatkozunk rá, 
              így biztosítva az egységes és könnyen érthető kommunikációt.
            </p>
            <p className="leading-relaxed text-sm">
              Ha üresen hagyod, akkor „EAP" lesz az alapértelmezett elnevezés.
            </p>
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6 pt-6">
          <div className="space-y-2">
            <Label htmlFor="program-name" className="text-base font-medium">Program neve</Label>
            <Input
              id="program-name"
              value={programName}
              onChange={(e) => onProgramNameChange(e.target.value)}
              maxLength={60}
              placeholder="pl. Munkavállalói Támogatási Program"
              className="h-12 text-base"
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="program-url" className="text-base font-medium">Program weboldala</Label>
            <Input
              id="program-url"
              type="url"
              value={eapProgramUrl}
              onChange={(e) => onEapProgramUrlChange(e.target.value)}
              placeholder="https://"
              className="h-12 text-base"
            />
            <p className="text-sm text-muted-foreground">
              Erre a weboldalra irányítjuk azokat a munkavállalókat, akik még nem ismerik a programot.
            </p>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-between pt-4">
        {!isPartner && (
          <Button variant="outline" onClick={onBack} size="lg">
            Vissza
          </Button>
        )}
        <Button 
          onClick={onNext}
          disabled={!programName || (isPartner && (!selectedCompanyId || companies.length === 0))}
          size="lg"
          variant="dark"
          className={isPartner ? "ml-auto" : ""}
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};
