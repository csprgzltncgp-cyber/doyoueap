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
    <Card>
      <CardContent className="pt-6 space-y-6">
        <div className="space-y-2">
          <div className="flex items-center gap-2 mb-4">
            <Building2 className="w-5 h-5 text-primary" />
            <h3 className="text-lg font-semibold">Válaszd ki az ügyfélcéget</h3>
          </div>
          <p className="text-sm text-muted-foreground">
            Válaszd ki, hogy melyik regisztrált ügyfélcégednek szeretnél felmérést indítani.
          </p>
        </div>

        <div className="space-y-4">
          <div>
            <Select value={selectedCompanyId} onValueChange={onCompanySelect}>
              <SelectTrigger className="w-full">
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

        <div className="flex justify-end pt-4">
          <Button onClick={onNext} disabled={!selectedCompanyId || companies.length === 0}>
            Következő
          </Button>
        </div>
      </CardContent>
    </Card>
  );
};