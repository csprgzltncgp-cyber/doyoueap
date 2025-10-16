import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Building2 } from "lucide-react";

interface Step0CompanySelectionProps {
  companies: Array<{ id: string; company_name: string }>;
  selectedCompanyId: string;
  onCompanySelect: (companyId: string) => void;
  onNext: () => void;
}

export const Step0CompanySelection = ({
  companies,
  selectedCompanyId,
  onCompanySelect,
  onNext,
}: Step0CompanySelectionProps) => {
  return (
    <div className="space-y-8 max-w-4xl mx-auto">
      <div className="text-center space-y-2">
        <h2 className="text-3xl font-bold">Ügyfélcég kiválasztása</h2>
        <p className="text-muted-foreground text-lg">
          Válaszd ki, hogy melyik regisztrált ügyfélcégednek szeretnél felmérést indítani
        </p>
      </div>

      <Card className="border-2">
        <CardContent className="pt-6 space-y-6">

          <div className="space-y-4">
            <div>
              <Label className="text-base font-medium">Ügyfélcég</Label>
              <Select value={selectedCompanyId} onValueChange={onCompanySelect}>
                <SelectTrigger className="w-full h-12 text-base mt-2">
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
                <p className="text-sm text-muted-foreground mt-2">
                  Menj a Partner Központba új ügyfélcég regisztrálásához.
                </p>
              )}
            </div>
          </div>

        </CardContent>
      </Card>

      <div className="flex justify-end pt-4">
        <Button
          onClick={onNext} 
          disabled={!selectedCompanyId || companies.length === 0}
          size="lg"
          variant="dark"
        >
          Következő lépés
        </Button>
      </div>
    </div>
  );
};