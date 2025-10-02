import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { toast } from "sonner";

interface Audit {
  id: string;
  company_name: string;
  created_at: string;
}

const Statistics = () => {
  const [audits, setAudits] = useState<Audit[]>([]);
  const [selectedAuditId, setSelectedAuditId] = useState<string>('');
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState("awareness");

  useEffect(() => {
    fetchAudits();
  }, []);

  const fetchAudits = async () => {
    try {
      const { data } = await supabase
        .from('audits')
        .select('id, company_name, created_at')
        .eq('is_active', true)
        .order('created_at', { ascending: false });

      if (data && data.length > 0) {
        setAudits(data);
        setSelectedAuditId(data[0].id);
      }
    } catch (error) {
      console.error('Error fetching audits:', error);
      toast.error('Hiba történt az auditek betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <p>Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="p-8 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Statisztikák</h1>
        {audits.length > 0 ? (
          <Select value={selectedAuditId} onValueChange={setSelectedAuditId}>
            <SelectTrigger className="w-80">
              <SelectValue placeholder="Válassz auditot" />
            </SelectTrigger>
            <SelectContent>
              {audits.map((audit) => (
                <SelectItem key={audit.id} value={audit.id}>
                  {audit.company_name} - {new Date(audit.created_at).toLocaleDateString('hu-HU')}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        ) : (
          <div className="text-sm text-muted-foreground">
            Nincs aktív audit - hozz létre egyet az adatok megjelenítéséhez
          </div>
        )}
      </div>

      <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
        <TabsList className="w-full justify-start border-b rounded-none h-auto p-0 bg-transparent">
          {/* Awareness – mennyien tudnak a program létezéséről */}
          <TabsTrigger 
            value="awareness" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Ismertség
          </TabsTrigger>
          <TabsTrigger 
            value="trust" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Bizalom & Hajlandóság
          </TabsTrigger>
          <TabsTrigger 
            value="usage" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Használat
          </TabsTrigger>
          <TabsTrigger 
            value="impact" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Hatás
          </TabsTrigger>
          <TabsTrigger 
            value="motivation" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Motiváció
          </TabsTrigger>
          <TabsTrigger 
            value="demographics" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Demográfia
          </TabsTrigger>
          <TabsTrigger 
            value="trends" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Trendek
          </TabsTrigger>
          <TabsTrigger 
            value="compare" 
            className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent"
          >
            Összehasonlítás
          </TabsTrigger>
        </TabsList>

        <TabsContent value="awareness" className="mt-6">
          <Card>
            <CardHeader>
              {/* Awareness – mennyien tudnak a program létezéséről */}
              <CardTitle>Ismertség Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-3 gap-4 mb-6">
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Használók</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">0</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Nem használók</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">0</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle className="text-lg">Összes</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">0</p>
                    <p className="text-sm text-muted-foreground">válaszadó</p>
                  </CardContent>
                </Card>
              </div>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="trust" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Bizalom & Hajlandóság Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="usage" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Használat Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="impact" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Hatás Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="motivation" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Motiváció Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="demographics" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Demográfia Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="trends" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Trendek Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="compare" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Összehasonlítás Riport</CardTitle>
              <CardDescription>
                {selectedAuditId ? 'Adatok betöltve' : 'Nincs kiválasztott audit'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-12 text-muted-foreground">
                {selectedAuditId ? 'Még nincs adat ehhez az audithoz' : 'Válassz ki egy auditot az adatok megjelenítéséhez'}
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default Statistics;