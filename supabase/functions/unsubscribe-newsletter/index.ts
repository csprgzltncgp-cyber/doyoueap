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
          <style>
            body {
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
              line-height: 1.6;
              color: #333;
              max-width: 600px;
              margin: 0 auto;
              padding: 20px;
            }
            .container {
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              padding: 40px;
              border-radius: 10px;
              text-align: center;
            }
            .content {
              background: white;
              padding: 30px;
              border-radius: 8px;
              margin-top: 20px;
            }
            h1 {
              color: white;
              margin: 0;
              font-size: 28px;
            }
            p {
              margin: 15px 0;
              font-size: 16px;
            }
            .footer {
              margin-top: 30px;
              padding-top: 20px;
              border-top: 1px solid #eee;
              font-size: 14px;
              color: #666;
            }
          </style>
        </head>
        <body>
          <div class="container">
            <h1>Leiratkozás megerősítve</h1>
            <div class="content">
              <p><strong>Kedves ${subscriber.name || "Feliratkozónk"}!</strong></p>
              <p>Sajnáljuk, hogy nem szeretne több hírlevelet kapni tőlünk.</p>
              <p>Az Ön kérésének megfelelően <strong>${subscriber.email}</strong> email címét töröltük hírlevelünk listájáról.</p>
              <p>Reméljük, hogy a jövőben újra találkozhatunk!</p>
              <p>Ha véletlenül iratkozott le, bármikor újra feliratkozhat weboldalunkon.</p>
              <div class="footer">
                <p>Üdvözlettel,<br><strong>DoYouEAP csapata</strong></p>
              </div>
            </div>
          </div>
        </body>
      </html>
    `;

    // Send confirmation email
    await resend.emails.send({
      from: "DoYouEAP <noreply@doyoueap.com>",
      to: [subscriber.email],
      subject: "Leiratkozás megerősítése - DoYouEAP Hírlevél",
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

    // Return HTML success page
    const successHtml = `
      <!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Leiratkozás sikeres</title>
          <style>
            body {
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
            }
            .container {
              background: white;
              padding: 40px;
              border-radius: 10px;
              text-align: center;
              max-width: 500px;
              box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            }
            h1 {
              color: #764ba2;
              margin-bottom: 20px;
            }
            p {
              color: #333;
              line-height: 1.6;
              margin: 10px 0;
            }
            .checkmark {
              font-size: 60px;
              color: #4CAF50;
              margin-bottom: 20px;
            }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="checkmark">✓</div>
            <h1>Sikeresen leiratkozott!</h1>
            <p>Email címét töröltük hírlevelünk listájáról.</p>
            <p>Megerősítő emailt küldtünk Önnek.</p>
            <p style="margin-top: 30px; color: #666;">Reméljük még visszatér! 💜</p>
          </div>
        </body>
      </html>
    `;

    return new Response(successHtml, {
      status: 200,
      headers: { "Content-Type": "text/html", ...corsHeaders },
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
