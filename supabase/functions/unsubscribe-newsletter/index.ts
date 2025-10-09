import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2.39.3";
import { Resend } from "https://esm.sh/resend@4.0.0";

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers":
    "authorization, x-client-info, apikey, content-type",
};

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const url = new URL(req.url);
    const encodedToken = url.searchParams.get("token");

    if (!encodedToken) {
      return new Response(
        JSON.stringify({ error: "Hiányzó leiratkozási token" }),
        {
          status: 400,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    const supabaseUrl = Deno.env.get("SUPABASE_URL")!;
    const supabaseServiceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!;
    const supabase = createClient(supabaseUrl, supabaseServiceKey);

    // Decode the token (it was URL encoded in the email)
    const token = decodeURIComponent(encodedToken);

    // Find subscriber by token
    const { data: subscriber, error: fetchError } = await supabase
      .from("newsletter_subscribers")
      .select("*")
      .eq("unsubscribe_token", token)
      .single();

    if (fetchError || !subscriber) {
      console.error("Subscriber not found:", fetchError);
      return new Response(
        JSON.stringify({ error: "Érvénytelen leiratkozási link" }),
        {
          status: 404,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Send confirmation email before deleting
    const emailHtml = `
      <!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
        </head>
        <body>
          <p>Kedves ${subscriber.name || "Feliratkozónk"}!</p>
          <p>Sajnáljuk, hogy nem szeretne több hírlevelet kapni tőlünk.</p>
          <p>Az Ön kérésének megfelelően <strong>${subscriber.email}</strong> email címét töröltük hírlevelünk listájáról.</p>
          <p>Reméljük, hogy a jövőben újra találkozhatunk!</p>
          <p>Ha véletlenül iratkozott le, bármikor újra feliratkozhat weboldalunkon.</p>
          <p>Üdvözlettel,<br>doyoueap</p>
        </body>
      </html>
    `;

    // Send confirmation email
    await resend.emails.send({
      from: "doyoueap <noreply@doyoueap.com>",
      to: [subscriber.email],
      subject: "Leiratkozás megerősítése - doyoueap Hírlevél",
      html: emailHtml,
    });

    // Delete subscriber
    const { error: deleteError } = await supabase
      .from("newsletter_subscribers")
      .delete()
      .eq("unsubscribe_token", token);

    if (deleteError) {
      console.error("Delete error:", deleteError);
      return new Response(
        JSON.stringify({ error: "Hiba a leiratkozás során" }),
        {
          status: 500,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    console.log(`Subscriber unsubscribed: ${subscriber.email}`);

    // Return success page HTML
    const successHtml = `
      <!DOCTYPE html>
      <html lang="hu">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Sikeres leiratkozás - DoYouEAP</title>
          <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
              font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              min-height: 100vh;
              display: flex;
              align-items: center;
              justify-content: center;
              padding: 20px;
            }
            .container {
              background: white;
              border-radius: 16px;
              padding: 48px 32px;
              max-width: 500px;
              text-align: center;
              box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
            h1 {
              color: #1a202c;
              font-size: 32px;
              margin-bottom: 24px;
              font-weight: 700;
            }
            .logo {
              width: 120px;
              height: auto;
              margin: 0 auto;
              display: block;
            }
            .message {
              color: #4a5568;
              font-size: 18px;
              margin-top: 16px;
            }
            .checkmark {
              width: 64px;
              height: 64px;
              margin: 0 auto 24px;
              background: #48bb78;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 40px;
              color: white;
            }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="checkmark">✓</div>
            <h1>Sikeres leiratkozás!</h1>
            <p class="message">Leiratkozott hírlevelünkről.</p>
            <img src="https://xvtglebdgoxqwxunjrqs.supabase.co/storage/v1/object/public/newsletter-assets/doyoueap-logo.png" alt="DoYouEAP" class="logo" style="margin-top: 32px;">
          </div>
        </body>
      </html>
    `;
    
    return new Response(successHtml, {
      status: 200,
      headers: { 
        "Content-Type": "text/html; charset=utf-8",
        ...corsHeaders 
      },
    });
  } catch (error: any) {
    console.error("Error in unsubscribe function:", error);
    return new Response(JSON.stringify({ error: error.message }), {
      status: 500,
      headers: { "Content-Type": "application/json", ...corsHeaders },
    });
  }
};

serve(handler);
