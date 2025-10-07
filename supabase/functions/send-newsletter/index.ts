import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { Resend } from "https://esm.sh/resend@4.0.0";

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface NewsletterRequest {
  subject: string;
  content: string;
  subscribers: Array<{ email: string; name: string | null }>;
}

const handler = async (req: Request): Promise<Response> => {
  // Handle CORS preflight requests
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { subject, content, subscribers }: NewsletterRequest = await req.json();

    if (!subject || !content || !subscribers || subscribers.length === 0) {
      return new Response(
        JSON.stringify({ error: "Missing required fields" }),
        {
          status: 400,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    console.log(`Sending newsletter to ${subscribers.length} subscribers`);

    // Send emails to all subscribers
    const emailPromises = subscribers.map(async (subscriber) => {
      const htmlContent = `
        <!DOCTYPE html>
        <html>
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
              body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
              }
              .header {
                background-color: #3572ef;
                color: white;
                padding: 20px;
                text-align: center;
                border-radius: 8px 8px 0 0;
              }
              .content {
                background-color: #ffffff;
                padding: 30px;
                border: 1px solid #e5e7eb;
                border-top: none;
              }
              .footer {
                background-color: #f9fafb;
                padding: 20px;
                text-align: center;
                font-size: 12px;
                color: #6b7280;
                border-radius: 0 0 8px 8px;
              }
              .footer a {
                color: #3572ef;
                text-decoration: none;
              }
            </style>
          </head>
          <body>
            <div class="header">
              <h1 style="margin: 0;">doyoueap.com</h1>
              <p style="margin: 10px 0 0 0;">Az EAP világ szakfolyóirata</p>
            </div>
            <div class="content">
              ${subscriber.name ? `<p>Kedves ${subscriber.name}!</p>` : '<p>Kedves Feliratkozónk!</p>'}
              ${content.split('\n').map(p => `<p>${p}</p>`).join('')}
            </div>
            <div class="footer">
              <p>Ez egy automatikus üzenet a doyoueap.com hírleveléből.</p>
              <p>
                <a href="https://doyoueap.com">doyoueap.com</a> | 
                <a href="https://doyoueap.com/magazin">The Journalist!</a>
              </p>
              <p style="margin-top: 10px; font-size: 11px;">
                © ${new Date().getFullYear()} doyoueap.com. Minden jog fenntartva.
              </p>
            </div>
          </body>
        </html>
      `;

      try {
        const emailResponse = await resend.emails.send({
          from: "doyoueap <info@doyoueap.com>",
          to: [subscriber.email],
          subject: subject,
          html: htmlContent,
        });

        console.log(`Email sent to ${subscriber.email}:`, emailResponse);
        return { success: true, email: subscriber.email };
      } catch (error: any) {
        console.error(`Failed to send email to ${subscriber.email}:`, error);
        return { success: false, email: subscriber.email, error: error.message };
      }
    });

    const results = await Promise.all(emailPromises);
    const successful = results.filter(r => r.success).length;
    const failed = results.filter(r => !r.success).length;

    console.log(`Newsletter sent: ${successful} successful, ${failed} failed`);

    return new Response(
      JSON.stringify({
        success: true,
        sent: successful,
        failed: failed,
        results: results
      }),
      {
        status: 200,
        headers: {
          "Content-Type": "application/json",
          ...corsHeaders,
        },
      }
    );
  } catch (error: any) {
    console.error("Error in send-newsletter function:", error);
    return new Response(
      JSON.stringify({ error: error.message }),
      {
        status: 500,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  }
};

serve(handler);
