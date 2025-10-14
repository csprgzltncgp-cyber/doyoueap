import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import CompanyManagement from './admin/CompanyManagement';
import MagazineManagement from './admin/MagazineManagement';
import NewsletterManagement from './admin/NewsletterManagement';
import GiftManagement from './admin/GiftManagement';
import CommunicationTemplates from './admin/CommunicationTemplates';

const AdminDashboard = () => {
  const { user, signOut } = useAuth();

  return (
    <div className="min-h-screen bg-background">
      <div className="border-b">
        <div className="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
          <h1 className="text-2xl font-bold">Admin Felület</h1>
          <Button onClick={signOut} variant="outline">
            Kijelentkezés
          </Button>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-8 py-6">
        <Tabs defaultValue="companies" className="space-y-6">
          <TabsList className="grid w-full grid-cols-5 max-w-3xl">
            <TabsTrigger value="companies">Cégek</TabsTrigger>
            <TabsTrigger value="gifts">Nyereményjáték</TabsTrigger>
            <TabsTrigger value="magazine">Magazin</TabsTrigger>
            <TabsTrigger value="newsletter">Hírlevél</TabsTrigger>
            <TabsTrigger value="communication">Kommunikáció</TabsTrigger>
          </TabsList>

          <TabsContent value="companies" className="space-y-4">
            <CompanyManagement />
          </TabsContent>

          <TabsContent value="gifts" className="space-y-4">
            <GiftManagement />
          </TabsContent>

          <TabsContent value="magazine" className="space-y-4">
            <MagazineManagement />
          </TabsContent>

          <TabsContent value="newsletter" className="space-y-4">
            <NewsletterManagement />
          </TabsContent>

          <TabsContent value="communication" className="space-y-4">
            <CommunicationTemplates />
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default AdminDashboard;
