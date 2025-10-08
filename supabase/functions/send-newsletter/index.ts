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
  logoUrl?: string;
  featuredImageUrl?: string;
}

const createNewsletterHTML = (
  recipientName: string | null, 
  content: string, 
  subject: string,
  logoUrl?: string,
  featuredImageUrl?: string
): string => {
  return `<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>${subject}</title>
    <style>
      /* Reset styles */
      body, p, h1, h2, h3, h4, h5, h6 { margin: 0; padding: 0; }
      img { border: 0; display: block; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
      
      body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: #f5f5f5;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        line-height: 1.6;
      }
      
      /* Container */
      .email-wrapper {
        width: 100%;
        background-color: #f5f5f5;
        padding: 40px 20px;
      }
      
      .email-container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      }
      
      /* Header with gradient */
      .email-header {
        background: linear-gradient(135deg, #3572ef 0%, #3abef9 100%);
        padding: 0;
        text-align: center;
        position: relative;
      }
      
      .logo-section {
        padding: 30px 20px 20px 20px;
      }
      
      .logo-section img {
        max-width: 180px;
        height: auto;
        margin: 0 auto;
      }
      
      .header-title {
        background: rgba(255,255,255,0.15);
        padding: 20px;
        backdrop-filter: blur(10px);
      }
      
      .header-title h1 {
        color: #ffffff;
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
      }
      
      /* Featured Image */
      .featured-image {
        width: 100%;
        height: auto;
        display: block;
      }
      
      /* Content sections */
      .email-content {
        padding: 40px 30px;
        color: #333333;
      }
      
      .greeting-badge {
        display: inline-block;
        background: linear-gradient(135deg, #3572ef 0%, #3abef9 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 24px;
      }
      
      .email-content h2 {
        color: #1a1a1a;
        font-size: 26px;
        margin: 0 0 20px 0;
        font-weight: 700;
        line-height: 1.3;
      }
      
      .email-content h3 {
        color: #3572ef;
        font-size: 20px;
        margin: 28px 0 12px 0;
        font-weight: 600;
      }
      
      .email-content p {
        margin: 0 0 16px 0;
        color: #4a5568;
        font-size: 16px;
        line-height: 1.7;
      }
      
      .email-content ul, .email-content ol {
        margin: 20px 0;
        padding-left: 24px;
      }
      
      .email-content li {
        margin: 12px 0;
        color: #4a5568;
        font-size: 16px;
        line-height: 1.6;
      }
      
      .email-content strong {
        color: #1a1a1a;
        font-weight: 600;
      }
      
      .email-content a {
        color: #3572ef;
        text-decoration: none;
        font-weight: 500;
        border-bottom: 2px solid rgba(53,114,239,0.3);
        transition: border-color 0.3s;
      }
      
      .email-content a:hover {
        border-bottom-color: #3572ef;
      }
      
      /* CTA Button */
      .cta-button {
        display: inline-block;
        background: linear-gradient(135deg, #3572ef 0%, #3abef9 100%);
        color: white !important;
        text-decoration: none !important;
        padding: 14px 32px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        margin: 20px 0;
        box-shadow: 0 4px 12px rgba(53,114,239,0.3);
        border: none !important;
      }
      
      /* Divider */
      .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        margin: 30px 0;
      }
      
      /* Quote/Highlight box */
      .highlight-box {
        background: linear-gradient(135deg, rgba(53,114,239,0.05) 0%, rgba(58,190,249,0.05) 100%);
        border-left: 4px solid #3572ef;
        padding: 20px;
        margin: 24px 0;
        border-radius: 8px;
      }
      
      /* Footer */
      .email-footer {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #ffffff;
        padding: 40px 30px;
        text-align: center;
      }
      
      .footer-logo {
        margin-bottom: 20px;
      }
      
      .footer-logo img {
        max-width: 140px;
        height: auto;
        margin: 0 auto;
        opacity: 0.9;
      }
      
      .footer-links {
        margin: 20px 0;
        padding: 20px 0;
        border-top: 1px solid rgba(255,255,255,0.1);
        border-bottom: 1px solid rgba(255,255,255,0.1);
      }
      
      .footer-links a {
        color: #3abef9;
        text-decoration: none;
        margin: 0 12px;
        font-size: 14px;
        font-weight: 500;
      }
      
      .footer-links a:hover {
        text-decoration: underline;
      }
      
      .social-links {
        margin: 20px 0;
      }
      
      .social-links a {
        display: inline-block;
        margin: 0 8px;
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        line-height: 36px;
        text-align: center;
        color: white;
        text-decoration: none;
      }
      
      .email-footer p {
        margin: 8px 0;
        font-size: 13px;
        color: rgba(255,255,255,0.7);
        line-height: 1.6;
      }
      
      .copyright {
        margin-top: 20px;
        font-size: 12px;
        color: rgba(255,255,255,0.5);
      }
      
      /* Responsive */
      @media only screen and (max-width: 600px) {
        .email-wrapper { padding: 20px 10px !important; }
        .email-content { padding: 30px 20px !important; }
        .email-footer { padding: 30px 20px !important; }
        .email-content h2 { font-size: 22px !important; }
        .header-title h1 { font-size: 24px !important; }
      }
    </style>
  </head>
  <body>
    <div class="email-wrapper">
      <div class="email-container">
        <!-- Header -->
        <div class="email-header">
          ${logoUrl ? `
          <div class="logo-section">
            <img src="${logoUrl}" alt="DoYouEAP" />
          </div>
          ` : `
          <div class="logo-section">
            <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0;">doyoueap</h1>
          </div>
          `}
          <div class="header-title">
            <h1>${subject}</h1>
          </div>
        </div>
        
        <!-- Featured Image (optional) -->
        ${featuredImageUrl ? `
        <img src="${featuredImageUrl}" alt="Featured" class="featured-image" />
        ` : ''}
        
        <!-- Main Content -->
        <div class="email-content">
          ${recipientName ? `<div class="greeting-badge">Kedves ${recipientName}!</div>` : '<div class="greeting-badge">Kedves Feliratkozónk!</div>'}
          
          ${content}
          
          <div class="divider"></div>
          
          <p style="text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px;">
            Ez egy automatikus üzenet. Ha bármilyen kérdése van, válaszoljon erre az emailre.
          </p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
          <div class="footer-logo">
            ${logoUrl ? `<img src="${logoUrl}" alt="DoYouEAP" style="filter: brightness(0) invert(1);" />` : ''}
          </div>
          
          <div class="footer-links">
            <a href="https://doyoueap.com">Főoldal</a>
            <a href="https://doyoueap.com/magazin">The Journalist!</a>
            <a href="https://doyoueap.com/bemutatkozas">EAP Pulse</a>
          </div>
          
          <p>Az EAP világ vezető szakfolyóirata</p>
          <p>Segítünk mérni és javítani EAP programját</p>
          
          <p class="copyright">© ${new Date().getFullYear()} doyoueap.com. Minden jog fenntartva.</p>
          
          <p style="font-size: 11px; margin-top: 20px;">
            Ha le szeretne iratkozni hírlevelünkről, kérjük írjon nekünk.
          </p>
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
    const { subject, content, fromEmail, subscribers, logoUrl, featuredImageUrl }: NewsletterRequest = await req.json();

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
      const html = createNewsletterHTML(subscriber.name, content, subject, logoUrl, featuredImageUrl);

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
