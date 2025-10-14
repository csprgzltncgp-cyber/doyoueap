import { useState, useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { toast } from 'sonner';
import { Mail, Link, QrCode, Gift, Save, Plus, Pencil } from 'lucide-react';

interface Template {
  id: string;
  template_type: 'email' | 'public_link' | 'qr_code';
  has_gift: boolean;
  subject: string | null;
  content: string;
  created_at: string;
  updated_at: string;
}

const CommunicationTemplates = () => {
  const [templates, setTemplates] = useState<Template[]>([]);
  const [loading, setLoading] = useState(true);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [formData, setFormData] = useState<Partial<Template>>({});

  useEffect(() => {
    fetchTemplates();
  }, []);

  const fetchTemplates = async () => {
    try {
      const { data, error } = await supabase
        .from('communication_templates')
        .select('*')
        .order('template_type', { ascending: true })
        .order('has_gift', { ascending: true });

      if (error) throw error;
      setTemplates(data || []);
    } catch (error: any) {
      toast.error('Hiba történt a sablonok betöltésekor');
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    if (!formData.id) return;

    try {
      const { error } = await supabase
        .from('communication_templates')
        .update({
          subject: formData.subject,
          content: formData.content,
        })
        .eq('id', formData.id);

      if (error) throw error;

      toast.success('Sablon sikeresen mentve!');
      setEditingId(null);
      setFormData({});
      fetchTemplates();
    } catch (error: any) {
      toast.error('Hiba történt a mentés során');
      console.error(error);
    }
  };

  const startEdit = (template: Template) => {
    setEditingId(template.id);
    setFormData(template);
  };

  const cancelEdit = () => {
    setEditingId(null);
    setFormData({});
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'email': return <Mail className="h-5 w-5" />;
      case 'public_link': return <Link className="h-5 w-5" />;
      case 'qr_code': return <QrCode className="h-5 w-5" />;
      default: return null;
    }
  };

  const getTypeLabel = (type: string) => {
    switch (type) {
      case 'email': return 'Email sablon';
      case 'public_link': return 'Nyilvános link sablon';
      case 'qr_code': return 'QR kód sablon';
      default: return type;
    }
  };

  if (loading) {
    return <div className="flex items-center justify-center p-8">Betöltés...</div>;
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Kommunikációs sablonok</h2>
        <p className="text-muted-foreground">
          Szerkeszd a kommunikációs sablonokat, amiket a HR használni fog. Használd a {'{link}'} helyőrzőt.
        </p>
      </div>

      <div className="grid gap-6">
        {templates.map((template) => (
          <Card key={template.id} className="border-2">
            <CardHeader>
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  {getTypeIcon(template.template_type)}
                  <div>
                    <CardTitle className="flex items-center gap-2">
                      {getTypeLabel(template.template_type)}
                      {template.has_gift && (
                        <Badge variant="outline" className="gap-1">
                          <Gift className="h-3 w-3" />
                          Nyereményjátékkal
                        </Badge>
                      )}
                    </CardTitle>
                    <CardDescription className="mt-1">
                      {template.has_gift ? 'Nyereményjátékos verzió' : 'Alap verzió'}
                    </CardDescription>
                  </div>
                </div>
                {editingId === template.id ? (
                  <div className="flex gap-2">
                    <Button onClick={handleSave} size="sm">
                      <Save className="h-4 w-4 mr-1" />
                      Mentés
                    </Button>
                    <Button onClick={cancelEdit} variant="outline" size="sm">
                      Mégsem
                    </Button>
                  </div>
                ) : (
                  <Button onClick={() => startEdit(template)} variant="outline" size="sm">
                    <Pencil className="h-4 w-4 mr-1" />
                    Szerkesztés
                  </Button>
                )}
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              {editingId === template.id ? (
                <>
                  {template.template_type === 'email' && (
                    <div>
                      <Label htmlFor={`subject-${template.id}`}>Tárgy</Label>
                      <Input
                        id={`subject-${template.id}`}
                        value={formData.subject || ''}
                        onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                        placeholder="Email tárgy"
                      />
                    </div>
                  )}
                  <div>
                    <Label htmlFor={`content-${template.id}`}>Tartalom</Label>
                    <Textarea
                      id={`content-${template.id}`}
                      value={formData.content || ''}
                      onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                      rows={10}
                      className="font-mono text-sm"
                    />
                  </div>
                </>
              ) : (
                <>
                  {template.subject && (
                    <div>
                      <Label className="text-muted-foreground">Tárgy</Label>
                      <p className="text-sm mt-1 font-medium">{template.subject}</p>
                    </div>
                  )}
                  <div>
                    <Label className="text-muted-foreground">Tartalom</Label>
                    <pre className="text-sm mt-1 whitespace-pre-wrap font-sans bg-muted p-4 rounded-md">
                      {template.content}
                    </pre>
                  </div>
                </>
              )}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
};

export default CommunicationTemplates;
