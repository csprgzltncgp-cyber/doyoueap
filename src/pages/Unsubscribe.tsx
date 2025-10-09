import { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { Loader2, CheckCircle2, XCircle } from "lucide-react";

const Unsubscribe = () => {
  const [searchParams] = useSearchParams();
  const [status, setStatus] = useState<"loading" | "success" | "error">("loading");
  const [message, setMessage] = useState("");

  useEffect(() => {
    const unsubscribe = async () => {
      const token = searchParams.get("token");
      
      if (!token) {
        setStatus("error");
        setMessage("Hiányzó leiratkozási token");
        return;
      }

      try {
        const response = await fetch(
          `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/unsubscribe-newsletter?token=${encodeURIComponent(token)}`
        );

        const result = await response.json();

        if (response.ok && result.success) {
          setStatus("success");
          setMessage(result.message || "Sikeresen leiratkozott a hírlevélről!");
        } else {
          setStatus("error");
          setMessage(result.error || "Hiba történt a leiratkozás során");
        }
      } catch (error) {
        console.error("Unsubscribe error:", error);
        setStatus("error");
        setMessage("Hiba történt a leiratkozás során");
      }
    };

    unsubscribe();
  }, [searchParams]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-muted/20 to-background p-4">
      <div className="max-w-md w-full bg-card rounded-lg shadow-lg p-8 text-center">
        {status === "loading" && (
          <>
            <Loader2 className="w-16 h-16 mx-auto mb-4 animate-spin text-primary" />
            <h1 className="text-2xl font-bold mb-2">Leiratkozás folyamatban...</h1>
            <p className="text-muted-foreground">Kérjük, várjon egy pillanatot.</p>
          </>
        )}

        {status === "success" && (
          <>
            <CheckCircle2 className="w-16 h-16 mx-auto mb-4 text-green-500" />
            <h1 className="text-2xl font-bold mb-2 text-foreground">Leiratkozás sikeres!</h1>
            <p className="text-muted-foreground">{message}</p>
            <p className="text-sm text-muted-foreground mt-4">
              Sajnáljuk, hogy nem szeretne több hírlevelet kapni tőlünk. Reméljük, hogy a jövőben újra találkozhatunk!
            </p>
          </>
        )}

        {status === "error" && (
          <>
            <XCircle className="w-16 h-16 mx-auto mb-4 text-destructive" />
            <h1 className="text-2xl font-bold mb-2 text-foreground">Hiba történt</h1>
            <p className="text-muted-foreground">{message}</p>
          </>
        )}

        <div className="mt-8 pt-6 border-t border-border">
          <p className="text-sm text-muted-foreground">
            <a href="/" className="text-primary hover:underline">
              Vissza a főoldalra
            </a>
          </p>
        </div>
      </div>
    </div>
  );
};

export default Unsubscribe;
