import { useState, useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { toast } from '@/hooks/use-toast';
import { Search, Calendar, Users, Trophy } from 'lucide-react';

interface Draw {
  id: string;
  audit_id: string;
  company_name: string;
  winner_token: string;
  candidates_count: number;
  ts: string;
  report_url: string | null;
  seed: string;
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
                  <TableHead>Státusz</TableHead>
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
                      <Badge variant="secondary" className="gap-1">
                        <Trophy className="h-3 w-3" />
                        Lezárva
                      </Badge>
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
