import { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Code, Key, Copy, Trash2, Plus, AlertCircle, CheckCircle } from 'lucide-react';
import { supabase } from '@/integrations/supabase/client';
import { toast } from 'sonner';
import { useAuth } from '@/hooks/useAuth';

interface ApiKey {
  id: string;
  name: string;
  api_key: string;
  is_active: boolean;
  created_at: string;
  last_used_at: string | null;
  expires_at: string | null;
}

const API = () => {
  const { user } = useAuth();
  const [apiKeys, setApiKeys] = useState<ApiKey[]>([]);
  const [loading, setLoading] = useState(true);
  const [newKeyName, setNewKeyName] = useState('');
  const [expiresInDays, setExpiresInDays] = useState('');
  const [creating, setCreating] = useState(false);
  const [newApiKey, setNewApiKey] = useState<string | null>(null);

  useEffect(() => {
    fetchApiKeys();
  }, [user]);

  const fetchApiKeys = async () => {
    try {
      const { data, error } = await supabase
        .from('api_keys')
        .select('*')
        .order('created_at', { ascending: false });

      if (error) throw error;
      setApiKeys(data || []);
    } catch (error) {
      console.error('Error fetching API keys:', error);
      toast.error('Hiba az API kulcsok betöltésekor');
    } finally {
      setLoading(false);
    }
  };

  const createApiKey = async () => {
    if (!newKeyName.trim()) {
      toast.error('Adjon meg egy nevet az API kulcsnak');
      return;
    }

    setCreating(true);
    try {
      const { data, error } = await supabase.functions.invoke('generate-api-key', {
        body: {
          name: newKeyName,
          expiresInDays: expiresInDays ? parseInt(expiresInDays) : null,
        },
      });

      if (error) throw error;

      setNewApiKey(data.apiKey.api_key);
      setNewKeyName('');
      setExpiresInDays('');
      fetchApiKeys();
      toast.success('API kulcs sikeresen létrehozva');
    } catch (error) {
      console.error('Error creating API key:', error);
      toast.error('Hiba az API kulcs létrehozásakor');
    } finally {
      setCreating(false);
    }
  };

  const toggleApiKey = async (id: string, isActive: boolean) => {
    try {
      const { error } = await supabase
        .from('api_keys')
        .update({ is_active: !isActive })
        .eq('id', id);

      if (error) throw error;

      fetchApiKeys();
      toast.success(isActive ? 'API kulcs deaktiválva' : 'API kulcs aktiválva');
    } catch (error) {
      console.error('Error toggling API key:', error);
      toast.error('Hiba az API kulcs módosításakor');
    }
  };

  const deleteApiKey = async (id: string) => {
    if (!confirm('Biztosan törli ezt az API kulcsot?')) return;

    try {
      const { error } = await supabase
        .from('api_keys')
        .delete()
        .eq('id', id);

      if (error) throw error;

      fetchApiKeys();
      toast.success('API kulcs törölve');
    } catch (error) {
      console.error('Error deleting API key:', error);
      toast.error('Hiba az API kulcs törlésekor');
    }
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.success('Vágólapra másolva');
  };

  return (
    <div className="space-y-6 pt-20 md:pt-0">
      <div>
        <h2 className="text-3xl font-bold mb-2">API Integráció</h2>
        <p className="text-muted-foreground">
          EAP szolgáltatók számára fejlesztett REST API
        </p>
      </div>

      {newApiKey && (
        <Alert className="bg-green-50 border-green-200">
          <CheckCircle className="h-4 w-4 text-green-600" />
          <AlertDescription className="text-green-800">
            <div className="space-y-2">
              <p className="font-semibold">API kulcs sikeresen létrehozva!</p>
              <p className="text-sm">Másolja ki az alábbi kulcsot, mert később nem fog látszani:</p>
              <div className="flex items-center gap-2 p-2 bg-white rounded border">
                <code className="flex-1 text-sm">{newApiKey}</code>
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => copyToClipboard(newApiKey)}
                >
                  <Copy className="h-4 w-4" />
                </Button>
              </div>
              <Button
                size="sm"
                variant="outline"
                onClick={() => setNewApiKey(null)}
              >
                Rendben
              </Button>
            </div>
          </AlertDescription>
        </Alert>
      )}

      <Tabs defaultValue="keys" className="space-y-6">
        <TabsList>
          <TabsTrigger value="keys">API Kulcsok</TabsTrigger>
          <TabsTrigger value="docs">Dokumentáció</TabsTrigger>
          <TabsTrigger value="examples">Példakódok</TabsTrigger>
        </TabsList>

        <TabsContent value="keys" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Plus className="h-5 w-5" />
                Új API Kulcs
              </CardTitle>
              <CardDescription>
                Hozzon létre új API kulcsot a szolgáltatások integr��ciójához
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="keyName">Kulcs neve</Label>
                  <Input
                    id="keyName"
                    placeholder="pl. Termelési környezet"
                    value={newKeyName}
                    onChange={(e) => setNewKeyName(e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="expires">Lejárat (napokban, opcionális)</Label>
                  <Input
                    id="expires"
                    type="number"
                    placeholder="365"
                    value={expiresInDays}
                    onChange={(e) => setExpiresInDays(e.target.value)}
                  />
                </div>
              </div>
              <Button variant="cta" onClick={createApiKey} disabled={creating}>
                {creating ? 'Létrehozás...' : 'API Kulcs Generálása'}
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Key className="h-5 w-5" />
                Meglévő API Kulcsok
              </CardTitle>
              <CardDescription>
                Kezelje a meglévő API kulcsokat
              </CardDescription>
            </CardHeader>
            <CardContent>
              {loading ? (
                <p className="text-muted-foreground">Betöltés...</p>
              ) : apiKeys.length === 0 ? (
                <p className="text-muted-foreground">Még nincs API kulcs létrehozva</p>
              ) : (
                <div className="space-y-3">
                  {apiKeys.map((key) => (
                    <div
                      key={key.id}
                      className="flex items-center justify-between p-4 border rounded-lg"
                    >
                      <div className="space-y-1">
                        <div className="flex items-center gap-2">
                          <p className="font-medium">{key.name}</p>
                          <Badge variant={key.is_active ? 'default' : 'secondary'}>
                            {key.is_active ? 'Aktív' : 'Inaktív'}
                          </Badge>
                          {key.expires_at && new Date(key.expires_at) < new Date() && (
                            <Badge variant="destructive">Lejárt</Badge>
                          )}
                        </div>
                        <p className="text-sm text-muted-foreground">
                          Létrehozva: {new Date(key.created_at).toLocaleDateString()}
                        </p>
                        {key.last_used_at && (
                          <p className="text-sm text-muted-foreground">
                            Utoljára használva: {new Date(key.last_used_at).toLocaleDateString()}
                          </p>
                        )}
                        <code className="text-xs bg-muted px-2 py-1 rounded">
                          {key.api_key.substring(0, 20)}...
                        </code>
                      </div>
                      <div className="flex gap-2">
                        <Button
                          size="sm"
                          variant="outline"
                          onClick={() => copyToClipboard(key.api_key)}
                        >
                          <Copy className="h-4 w-4" />
                        </Button>
                        <Button
                          size="sm"
                          variant="outline"
                          onClick={() => toggleApiKey(key.id, key.is_active)}
                        >
                          {key.is_active ? 'Deaktiválás' : 'Aktiválás'}
                        </Button>
                        <Button
                          size="sm"
                          variant="destructive"
                          onClick={() => deleteApiKey(key.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="docs" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>API Dokumentáció</CardTitle>
              <CardDescription>
                REST API endpoint-ok az EAP Pulse rendszerrel való integrációhoz
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div>
                <h3 className="text-lg font-semibold mb-2">Authentikáció</h3>
                <p className="text-sm text-muted-foreground mb-2">
                  Minden API híváshoz szükséges az X-API-Key header:
                </p>
                <pre className="bg-muted p-4 rounded-lg overflow-x-auto">
                  <code>{`X-API-Key: your_api_key_here`}</code>
                </pre>
              </div>

              <div>
                <h3 className="text-lg font-semibold mb-2">Endpoint-ok</h3>
                
                <div className="space-y-4">
                  <div className="border rounded-lg p-4">
                    <div className="flex items-center gap-2 mb-2">
                      <Badge>GET</Badge>
                      <code>/api-get-audits</code>
                    </div>
                    <p className="text-sm text-muted-foreground mb-2">
                      Lekéri a céghez tartozó felméréseket
                    </p>
                    <pre className="bg-muted p-3 rounded text-xs overflow-x-auto">
{`Válasz:
{
  "audits": [
    {
      "id": "uuid",
      "company_name": "string",
      "program_name": "string",
      "access_token": "string",
      "status": "string",
      "is_active": boolean,
      "created_at": "timestamp",
      "target_responses": number
    }
  ]
}`}
                    </pre>
                  </div>

                  <div className="border rounded-lg p-4">
                    <div className="flex items-center gap-2 mb-2">
                      <Badge variant="secondary">POST</Badge>
                      <code>/api-submit-response</code>
                    </div>
                    <p className="text-sm text-muted-foreground mb-2">
                      Válasz beküldése egy felméréshez
                    </p>
                    <pre className="bg-muted p-3 rounded text-xs overflow-x-auto">
{`Kérés:
{
  "auditId": "uuid",
  "responses": {
    "question_id": "answer"
  },
  "employeeMetadata": {
    "department": "string",
    "age_group": "string"
  },
  "participantIdHash": "string"
}

Válasz:
{
  "success": true,
  "responseId": "uuid",
  "drawToken": "string"
}`}
                    </pre>
                  </div>
                </div>
              </div>

              <Alert>
                <AlertCircle className="h-4 w-4" />
                <AlertDescription>
                  Az API hívások száma naplózásra kerül. Ügyeljen a rate limit-ekre!
                </AlertDescription>
              </Alert>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="examples" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Példakódok</CardTitle>
              <CardDescription>
                Integrációs példák különböző programozási nyelveken
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div>
                <h3 className="text-lg font-semibold mb-2">JavaScript / Node.js</h3>
                <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
{`// Felmérések lekérdezése
const response = await fetch('${window.location.origin}/api-get-audits', {
  headers: {
    'X-API-Key': 'your_api_key_here'
  }
});
const data = await response.json();

// Válasz beküldése
const submitResponse = await fetch('${window.location.origin}/api-submit-response', {
  method: 'POST',
  headers: {
    'X-API-Key': 'your_api_key_here',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    auditId: 'audit_uuid',
    responses: {
      'question_1': 'answer_1'
    },
    employeeMetadata: {
      department: 'IT'
    }
  })
});`}
                </pre>
              </div>

              <div>
                <h3 className="text-lg font-semibold mb-2">Python</h3>
                <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
{`import requests

# Felmérések lekérdezése
response = requests.get(
    '${window.location.origin}/api-get-audits',
    headers={'X-API-Key': 'your_api_key_here'}
)
audits = response.json()

# Válasz beküldése
response = requests.post(
    '${window.location.origin}/api-submit-response',
    headers={
        'X-API-Key': 'your_api_key_here',
        'Content-Type': 'application/json'
    },
    json={
        'auditId': 'audit_uuid',
        'responses': {'question_1': 'answer_1'},
        'employeeMetadata': {'department': 'IT'}
    }
)`}
                </pre>
              </div>

              <div>
                <h3 className="text-lg font-semibold mb-2">cURL</h3>
                <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
{`# Felmérések lekérdezése
curl -X GET ${window.location.origin}/api-get-audits \\
  -H "X-API-Key: your_api_key_here"

# Válasz beküldése
curl -X POST ${window.location.origin}/api-submit-response \\
  -H "X-API-Key: your_api_key_here" \\
  -H "Content-Type: application/json" \\
  -d '{
    "auditId": "audit_uuid",
    "responses": {"question_1": "answer_1"},
    "employeeMetadata": {"department": "IT"}
  }'`}
                </pre>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default API;
