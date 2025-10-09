import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

export default function CustomSurvey() {
  return (
    <div className="space-y-6 pt-20">
      <div className="mb-6">
        <h2 className="text-2xl font-bold mb-2">Egyedi közvélemény-kutatás indítása</h2>
        <p className="text-muted-foreground">
          Hamarosan elérhető lesz az egyedi kérdőív létrehozása
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Fejlesztés alatt</CardTitle>
          <CardDescription>
            Ez a funkció még nem került implementálásra
          </CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground">
            Itt tudsz majd egyedi kérdőíveket létrehozni tetszőleges témában, 
            teljesen testreszabható kérdésekkel.
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
