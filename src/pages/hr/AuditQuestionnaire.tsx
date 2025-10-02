import { useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { ChevronLeft, ChevronRight, Info } from "lucide-react";
import { QuestionRenderer } from "@/components/survey/QuestionRenderer";

interface Question {
  id: string;
  type: string;
  question: string;
  required: boolean;
  category?: string;
  scale?: number;
  labels?: { [key: string]: string };
  options?: string[];
}

interface QuestionBlock {
  title: string;
  description?: string;
  questions: Question[];
}

interface Questionnaire {
  id: string;
  title: string;
  description: string;
  questions: {
    demographics: Question[];
    blocks: QuestionBlock[];
  };
}

export default function AuditQuestionnaire() {
  const [questionnaire, setQuestionnaire] = useState<Questionnaire | null>(null);
  const [loading, setLoading] = useState(true);
  const [currentBlockIndex, setCurrentBlockIndex] = useState(0);
  const [dummyResponses, setDummyResponses] = useState<Record<string, any>>({});

  useEffect(() => {
    fetchQuestionnaire();
  }, []);

  const fetchQuestionnaire = async () => {
    try {
      const { data, error } = await supabase
        .from("questionnaires")
        .select("*")
        .eq("is_active", true)
        .single();

      if (error) throw error;
      setQuestionnaire(data);
    } catch (error) {
      console.error("Error fetching questionnaire:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="container mx-auto p-6">
        <Card>
          <CardContent className="p-6">
            <p className="text-center">Kérdőív betöltése...</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!questionnaire) {
    return (
      <div className="container mx-auto p-6">
        <Card>
          <CardContent className="p-6">
            <p className="text-center">Nem található aktív kérdőív.</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  const currentBlock = questionnaire.questions.blocks[currentBlockIndex];
  const totalBlocks = questionnaire.questions.blocks.length;
  const progress = ((currentBlockIndex + 1) / totalBlocks) * 100;

  return (
    <div className="container mx-auto p-6 max-w-4xl">
      <div className="mb-6">
        <h1 className="text-3xl font-bold mb-2">Audit Kérdőív Előnézet</h1>
        <p className="text-muted-foreground">
          Így látják a munkavállalók a kérdőívet az audit során
        </p>
      </div>

      <Alert className="mb-6 border-info/50 bg-info/10">
        <Info className="h-4 w-4" />
        <AlertDescription>
          <strong>Fontos információ:</strong> Ez a kérdőív nem szerkeszthető ezen az oldalon. 
          A kérdések és szerkezet módosítása megváltoztatná az adatok kiértékelését és 
          összehasonlíthatóságát a korábbi auditokkal. Ha változtatásra van szükség, 
          kérjük, vegye fel a kapcsolatot a rendszergazdával.
        </AlertDescription>
      </Alert>

      <Card className="mb-6">
        <CardHeader>
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm text-muted-foreground">
              {currentBlockIndex + 1}. blokk / {totalBlocks}
            </span>
            <span className="text-sm font-medium">{Math.round(progress)}%</span>
          </div>
          <Progress value={progress} className="mb-4" />
          <CardTitle>{currentBlock.title}</CardTitle>
          {currentBlock.description && (
            <CardDescription>{currentBlock.description}</CardDescription>
          )}
        </CardHeader>
        <CardContent className="space-y-6">
          {currentBlock.questions.map((question) => (
            <div key={question.id} className="opacity-75 pointer-events-none">
              <QuestionRenderer
                question={question}
                value={dummyResponses[question.id]}
                onChange={(value) => {
                  setDummyResponses((prev) => ({
                    ...prev,
                    [question.id]: value,
                  }));
                }}
              />
              {question.required && (
                <p className="text-xs text-muted-foreground mt-1">* Kötelező kérdés</p>
              )}
            </div>
          ))}
        </CardContent>
      </Card>

      <div className="flex justify-between items-center">
        <Button
          variant="outline"
          onClick={() => setCurrentBlockIndex((prev) => Math.max(0, prev - 1))}
          disabled={currentBlockIndex === 0}
        >
          <ChevronLeft className="mr-2 h-4 w-4" />
          Előző
        </Button>

        <span className="text-sm text-muted-foreground">
          {currentBlockIndex + 1} / {totalBlocks} blokk
        </span>

        <Button
          onClick={() =>
            setCurrentBlockIndex((prev) => Math.min(totalBlocks - 1, prev + 1))
          }
          disabled={currentBlockIndex === totalBlocks - 1}
        >
          Következő
          <ChevronRight className="ml-2 h-4 w-4" />
        </Button>
      </div>

      <Alert className="mt-6 border-muted">
        <Info className="h-4 w-4" />
        <AlertDescription className="text-sm">
          A munkavállalók demográfiai adatokat is megadnak a kérdőív kezdetén (életkor, nem, 
          beosztás, stb.), amelyek segítenek a válaszok mélyebb elemzésében és szegmentálásában.
        </AlertDescription>
      </Alert>
    </div>
  );
}
