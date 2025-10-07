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
  fromEmail: string;
  subscribers: Array<{ email: string; name: string | null }>;
}

const createNewsletterHTML = (recipientName: string | null, content: string, subject: string): string => {
  return `<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>${subject}</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: #f6f9fc;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }
      .email-wrapper {
        width: 100%;
        background-color: #f6f9fc;
        padding: 20px 0;
      }
      .email-container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
      .email-header {
        background: linear-gradient(135deg, #3572ef 0%, #2563eb 100%);
        color: #ffffff;
        padding: 40px 30px;
        text-align: center;
      }
      .email-header h1 {
        margin: 0 0 10px 0;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: -0.5px;
      }
      .email-header p {
        margin: 0;
        font-size: 14px;
        opacity: 0.95;
        letter-spacing: 0.5px;
      }
      .email-content {
        padding: 40px 30px;
        color: #333333;
        line-height: 1.6;
      }
      .email-content h2 {
        color: #1a1a1a;
        font-size: 24px;
        margin: 0 0 16px 0;
        font-weight: 600;
      }
      .email-content h3 {
        color: #333333;
        font-size: 18px;
        margin: 24px 0 12px 0;
        font-weight: 600;
      }
      .email-content p {
        margin: 0 0 16px 0;
        color: #4a5568;
        font-size: 16px;
      }
      .email-content ul, .email-content ol {
        margin: 16px 0;
        padding-left: 24px;
      }
      .email-content li {
        margin: 8px 0;
        color: #4a5568;
        font-size: 16px;
      }
      .email-content strong {
        color: #1a1a1a;
        font-weight: 600;
      }
      .email-content a {
        color: #3572ef;
        text-decoration: underline;
      }
      .greeting {
        font-size: 16px;
        color: #1a1a1a;
        margin-bottom: 24px;
        font-weight: 500;
      }
      .email-footer {
        background-color: #f9fafb;
        padding: 30px 20px;
        text-align: center;
        border-top: 1px solid #e5e7eb;
      }
      .email-footer hr {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 0 0 20px 0;
      }
      .email-footer p {
        margin: 10px 0;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
      }
      .email-footer a {
        color: #3572ef;
        text-decoration: none;
      }
      .email-footer a:hover {
        text-decoration: underline;
      }
      .copyright {
        margin-top: 20px;
        font-size: 11px;
        color: #9ca3af;
      }
    </style>
  </head>
  <body>
    <div class="email-wrapper">
      <div class="email-container">
        <div class="email-header">
          <h1>doyoueap.com</h1>
          <p>Az EAP világ szakfolyóirata</p>
        </div>
        <div class="email-content">
          ${recipientName ? `<p class="greeting">Kedves ${recipientName}!</p>` : '<p class="greeting">Kedves Feliratkozónk!</p>'}
          ${content}
        </div>
        <div class="email-footer">
          <p>Ez egy automatikus üzenet a doyoueap.com hírleveléből.</p>
          <p>
            <a href="https://doyoueap.com">doyoueap.com</a> | 
            <a href="https://doyoueap.com/magazin">The Journalist!</a>
          </p>
          <p class="copyright">© ${new Date().getFullYear()} doyoueap.com. Minden jog fenntartva.</p>
        </div>
      </div>
    </div>
  </body>
</html>`;
};

const handler = async (req: Request): Promise<Response> => {
  // Handle CORS preflight requests
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { subject, content, fromEmail, subscribers }: NewsletterRequest = await req.json();

    if (!subject || !content || !subscribers || subscribers.length === 0) {
      return new Response(
        JSON.stringify({ error: "Missing required fields" }),
        {
          status: 400,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    console.log(`Sending newsletter to ${subscribers.length} subscribers from ${fromEmail}`);

    // Send emails to all subscribers
    const emailPromises = subscribers.map(async (subscriber) => {
      const html = createNewsletterHTML(subscriber.name, content, subject);

      try {
        const emailResponse = await resend.emails.send({
          from: `doyoueap <${fromEmail}>`,
          to: [subscriber.email],
          subject: subject,
          html: html,
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
