import { useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const Unauthorized = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle>Hozzáférés megtagadva</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <p className="text-muted-foreground">
            Nincs jogosultsága az oldal megtekintéséhez.
          </p>
          <Button onClick={() => navigate('/')} className="w-full">
            Vissza a főoldalra
          </Button>
        </CardContent>
      </Card>
    </div>
  );
};

export default Unauthorized;
