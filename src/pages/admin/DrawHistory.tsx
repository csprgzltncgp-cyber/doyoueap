import { useState, useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { toast } from '@/hooks/use-toast';
import { Search, Calendar, Users, Trophy, FileDown, Mail, CheckCircle2, XCircle, MinusCircle } from 'lucide-react';
import jsPDF from 'jspdf';

interface Draw {
  id: string;
  audit_id: string;
  company_name: string;
  winner_token: string;
  candidates_count: number;
  ts: string;
  report_url: string | null;
  seed: string;
  notification_email: string | null;
  notification_status: 'pending' | 'sent' | 'failed' | 'not_applicable';
  notification_sent_at: string | null;
}

const DrawHistory = () => {
  const [draws, setDraws] = useState<Draw[]>([]);
  const [filteredDraws, setFilteredDraws] = useState<Draw[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    fetchDraws();
  }, []);

  useEffect(() => {
    filterDraws();
  }, [draws, searchTerm]);

  const fetchDraws = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('draws')
        .select('*')
        .order('ts', { ascending: false });

      if (error) throw error;
      setDraws(data || []);
    } catch (error) {
      console.error('Error fetching draws:', error);
      toast({
        title: 'Hiba',
        description: 'Nem sikerült betölteni a sorsolásokat.',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const filterDraws = () => {
    let filtered = [...draws];

    if (searchTerm) {
      filtered = filtered.filter(draw =>
        draw.company_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        draw.winner_token.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    setFilteredDraws(filtered);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('hu-HU', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const getNotificationBadge = (draw: Draw) => {
    if (draw.notification_status === 'not_applicable') {
      return (
        <Badge variant="secondary" className="gap-1">
          <MinusCircle className="h-3 w-3" />
          Nincs email
        </Badge>
      );
    }
    if (draw.notification_status === 'sent') {
      return (
        <Badge variant="default" className="gap-1 bg-green-500">
          <CheckCircle2 className="h-3 w-3" />
          Kézbesítve
        </Badge>
      );
    }
    if (draw.notification_status === 'failed') {
      return (
        <Badge variant="destructive" className="gap-1">
          <XCircle className="h-3 w-3" />
          Sikertelen
        </Badge>
      );
    }
    if (draw.notification_status === 'pending') {
      return (
        <Badge variant="outline" className="gap-1">
          <Mail className="h-3 w-3" />
          Folyamatban
        </Badge>
      );
    }
    return null;
  };

  const downloadDrawReport = (draw: Draw) => {
    const doc = new jsPDF();
    
    // Header
    doc.setFontSize(18);
    doc.text('Sorsolási jegyzőkönyv', 105, 20, { align: 'center' });
    
    // Draw details
    doc.setFontSize(12);
    let yPos = 40;
    
    doc.setFont(undefined, 'bold');
    doc.text('Cég neve:', 20, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(draw.company_name, 70, yPos);
    yPos += 10;
    
    doc.setFont(undefined, 'bold');
    doc.text('Sorsolás időpontja:', 20, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(formatDate(draw.ts), 70, yPos);
    yPos += 10;
    
    doc.setFont(undefined, 'bold');
    doc.text('Jelentkezők száma:', 20, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(draw.candidates_count.toString(), 70, yPos);
    yPos += 10;
    
    doc.setFont(undefined, 'bold');
    doc.text('Nyertes token:', 20, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(draw.winner_token, 70, yPos);
    yPos += 15;
    
    // Seed info for transparency
    doc.setFontSize(10);
    doc.text('Kriptográfiai seed (audit célra):', 20, yPos);
    yPos += 5;
    doc.setFontSize(8);
    doc.text(draw.seed, 20, yPos, { maxWidth: 170 });
    
    // Footer
    doc.setFontSize(8);
    doc.text('EAP Pulse - Auditálható és átlátható sorsolási rendszer', 105, 280, { align: 'center' });
    doc.text(`Jegyzőkönyv készítve: ${new Date().toLocaleString('hu-HU')}`, 105, 285, { align: 'center' });
    
    doc.save(`sorsolas-jegyzokonyv-${draw.id.substring(0, 8)}.pdf`);
    toast({
      title: 'PDF letöltve',
      description: 'A sorsolási jegyzőkönyv sikeresen letöltve.',
    });
  };

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Trophy className="h-5 w-5" />
            Szűrés
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Keresés cég vagy nyertes token alapján..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-10"
            />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Archív Sorsolások ({filteredDraws.length})</CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <p className="text-center text-muted-foreground py-8">Betöltés...</p>
          ) : filteredDraws.length === 0 ? (
            <p className="text-center text-muted-foreground py-8">
              {searchTerm ? 'Nincs találat a keresési feltételeknek megfelelően.' : 'Még nincsenek sorsolások.'}
            </p>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Cég neve</TableHead>
                  <TableHead>Sorsolás időpontja</TableHead>
                  <TableHead>Jelentkezők száma</TableHead>
                  <TableHead>Nyertes token</TableHead>
                  <TableHead>Email értesítés</TableHead>
                  <TableHead>Státusz</TableHead>
                  <TableHead className="text-right">Műveletek</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredDraws.map((draw) => (
                  <TableRow key={draw.id}>
                    <TableCell className="font-medium">{draw.company_name}</TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Calendar className="h-4 w-4 text-muted-foreground" />
                        {formatDate(draw.ts)}
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Users className="h-4 w-4 text-muted-foreground" />
                        {draw.candidates_count}
                      </div>
                    </TableCell>
                    <TableCell>
                      <code className="px-2 py-1 bg-muted rounded text-xs">
                        {draw.winner_token.substring(0, 16)}...
                      </code>
                    </TableCell>
                    <TableCell>
                      {getNotificationBadge(draw)}
                      {draw.notification_email && (
                        <div className="text-xs text-muted-foreground mt-1">
                          {draw.notification_email}
                        </div>
                      )}
                      {draw.notification_sent_at && (
                        <div className="text-xs text-muted-foreground mt-1">
                          {formatDate(draw.notification_sent_at)}
                        </div>
                      )}
                    </TableCell>
                    <TableCell>
                      <Badge variant="secondary" className="gap-1">
                        <Trophy className="h-3 w-3" />
                        Lezárva
                      </Badge>
                    </TableCell>
                    <TableCell className="text-right">
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => downloadDrawReport(draw)}
                        className="gap-2"
                      >
                        <FileDown className="h-4 w-4" />
                        Jegyzőkönyv
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default DrawHistory;
