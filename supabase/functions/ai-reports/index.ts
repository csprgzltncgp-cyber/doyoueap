 import { serve } from "https://deno.land/std@0.168.0/http/server.ts";
 
 const corsHeaders = {
   "Access-Control-Allow-Origin": "*",
   "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type, x-supabase-client-platform, x-supabase-client-platform-version, x-supabase-client-runtime, x-supabase-client-runtime-version",
 };
 
 const SYSTEM_PROMPT = `Te egy EAP program riport asszisztens vagy. A felhasználók kérhetnek tőled egyedi riportokat a program adatairól.
 
 A válaszaidat JSON formátumban add meg, ami tartalmazza:
 1. "text": Rövid szöveges magyarázat a riportról
 2. "charts": Egy tömb a generálandó diagramokkal
 
 Minden chart objektum tartalmazhat:
 - "type": "bar" | "pie" | "line" | "progress" | "gauge"
 - "title": A diagram címe
 - "data": Az adatok tömbje
 
 Példa válasz formátum:
{
    "text": "Itt van a kért riport a probléma típusokról...",
    "charts": [
       {
         "type": "pie",
         "title": "Probléma típusok megoszlása",
         "data": [
           {"name": "Pszichológia", "value": 45},
           {"name": "Jog", "value": 25},
           {"name": "Pénzügy", "value": 20},
           {"name": "Egyéb", "value": 10}
         ]
      }
    ]
  }

KÖRDIAGRAM SZÍNEZÉSI SZABÁLY (NE ADJ MEG SZÍNEKET, A FRONTEND KEZELI):
- 1. körcikk: sötétzöld (#04565f)
- 2. körcikk: világoszöld (#82f5ae)
- 3. körcikk: mélyzöld (#004144)
- 4+ körcikk: a világoszöld egyre halványabb verziói
 
 Progress bar esetén:
 {
   "type": "progress",
   "title": "Program használati arány",
   "data": [{"name": "Használat", "value": 68, "max": 100}]
 }
 
 Gauge chart esetén:
 {
   "type": "gauge", 
   "title": "Elégedettségi index",
   "data": [{"value": 85, "max": 100}]
 }
 
 Bar chart esetén:
 {
   "type": "bar",
   "title": "Havi használat",
   "data": [
     {"name": "Jan", "value": 120},
     {"name": "Feb", "value": 145},
     {"name": "Már", "value": 160}
   ]
 }
 
 Line chart esetén:
 {
   "type": "line",
   "title": "Trend",
   "data": [
     {"name": "Q1", "value": 100},
     {"name": "Q2", "value": 120},
     {"name": "Q3", "value": 150}
   ]
 }
 
Használj valósághű demo adatokat, amelyek egy tipikus EAP programra jellemzőek. KÖRDIAGRAMOKNÁL NE ADJ MEG SZÍNEKET - a frontend automatikusan kezeli a színezést a következő szabály szerint: 1. sötétzöld, 2. világoszöld, 3. mélyzöld, 4+ halványított zöldek.

 Mindig CSAK VALID JSON-t adj vissza, semmi mást. Ne használj markdown code block-ot.`;
 
 serve(async (req) => {
   if (req.method === "OPTIONS") {
     return new Response(null, { headers: corsHeaders });
   }
 
   try {
     const { messages } = await req.json();
     const LOVABLE_API_KEY = Deno.env.get("LOVABLE_API_KEY");
     
     if (!LOVABLE_API_KEY) {
       throw new Error("LOVABLE_API_KEY is not configured");
     }
 
     const response = await fetch("https://ai.gateway.lovable.dev/v1/chat/completions", {
       method: "POST",
       headers: {
         Authorization: `Bearer ${LOVABLE_API_KEY}`,
         "Content-Type": "application/json",
       },
       body: JSON.stringify({
         model: "google/gemini-3-flash-preview",
         messages: [
           { role: "system", content: SYSTEM_PROMPT },
           ...messages,
         ],
       }),
     });
 
     if (!response.ok) {
       if (response.status === 429) {
         return new Response(JSON.stringify({ error: "Túl sok kérés. Kérjük, próbálja újra később." }), {
           status: 429,
           headers: { ...corsHeaders, "Content-Type": "application/json" },
         });
       }
       if (response.status === 402) {
         return new Response(JSON.stringify({ error: "Fizetés szükséges. Kérjük, töltse fel a kreditet." }), {
           status: 402,
           headers: { ...corsHeaders, "Content-Type": "application/json" },
         });
       }
       const errorText = await response.text();
       console.error("AI gateway error:", response.status, errorText);
       return new Response(JSON.stringify({ error: "AI szolgáltatás hiba" }), {
         status: 500,
         headers: { ...corsHeaders, "Content-Type": "application/json" },
       });
     }
 
     const data = await response.json();
     const content = data.choices?.[0]?.message?.content || "";
     
     // Parse the JSON response from AI
     let parsedContent;
     try {
       parsedContent = JSON.parse(content);
     } catch {
       // If not valid JSON, return as text
       parsedContent = { text: content, charts: [] };
     }
 
     return new Response(JSON.stringify(parsedContent), {
       headers: { ...corsHeaders, "Content-Type": "application/json" },
     });
   } catch (e) {
     console.error("AI reports error:", e);
     return new Response(JSON.stringify({ error: e instanceof Error ? e.message : "Ismeretlen hiba" }), {
       status: 500,
       headers: { ...corsHeaders, "Content-Type": "application/json" },
     });
   }
 });