import { Card, CardContent } from "@/components/ui/card";
import { Construction } from "lucide-react";

const CrisisInterventions = () => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold">Krízis Intervenciók</h2>
        <p className="text-muted-foreground">
          Krízishelyzetek kezelése és nyomon követése
        </p>
      </div>

      <Card className="border-dashed border-2">
        <CardContent className="py-16 text-center">
          <Construction className="h-12 w-12 text-muted-foreground/40 mx-auto mb-4" />
          <p className="text-lg font-medium text-muted-foreground mb-2">
            Hamarosan elérhető
          </p>
          <p className="text-sm text-muted-foreground">
            Ez a funkció fejlesztés alatt áll.
          </p>
        </CardContent>
      </Card>
    </div>
  );
};

export default CrisisInterventions;
