import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { Resend } from "https://esm.sh/resend@4.0.0";

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface FooterLink {
  text: string;
  url: string;
}

interface NewsletterTemplate {
  header_title: string;
  footer_text: string;
  footer_company: string;
  footer_address: string | null;
  primary_color: string;
  background_color: string;
  greeting_text: string;
  footer_links: FooterLink[];
  header_color: string;
  footer_color: string;
  header_gradient_1?: string | null;
  header_gradient_2?: string | null;
  greeting_text_color?: string | null;
  greeting_bg_color?: string | null;
  content_text_color?: string | null;
  content_text_size?: string | null;
  extra_content_text_color?: string | null;
  extra_content_text_size?: string | null;
  footer_gradient?: string | null;
  sender_email: string;
  sender_name: string;
  header_color_1?: string;
  header_color_2?: string;
  logo_url?: string | null;
  featured_image_url?: string | null;
  footer_logo_url?: string | null;
  footer_link_color?: string;
  extra_content_border_color?: string;
  extra_content_bg_color?: string;
  content_button_text?: string;
  content_button_text_color?: string;
  content_button_color?: string;
  content_button_shadow_color?: string;
  content_button_url?: string | null;
  show_content_button?: boolean;
  extra_button_text?: string;
  extra_button_text_color?: string;
  extra_button_color?: string;
  extra_button_shadow_color?: string;
  extra_button_url?: string | null;
  show_extra_button?: boolean;
}

interface NewsletterRequest {
  subject: string;
  content: string;
  extraContent?: string;
  subscribers: Array<{ email: string; name: string | null }>;
  template: NewsletterTemplate;
}

// Simple markdown-like formatting to HTML
const formatContent = (content: string): string => {
  // Split by paragraphs
  let formatted = content
    // Bold: **text** -> <strong>text</strong>
    .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
    // Italic: *text* -> <em>text</em>
    .replace(/\*([^*]+)\*/g, '<em>$1</em>')
    // Lists: lines starting with - or *
    .split('\n')
    .map(line => {
      const trimmed = line.trim();
      if (trimmed.startsWith('- ') || trimmed.startsWith('* ')) {
        return `<li>${trimmed.substring(2)}</li>`;
      }
      return trimmed ? `<p>${trimmed}</p>` : '';
    })
    .join('\n');

  // Wrap consecutive <li> in <ul>
  formatted = formatted.replace(/(<li>.*<\/li>\n?)+/g, match => `<ul>${match}</ul>`);
  
  return formatted;
};

const createNewsletterHTML = (
  recipientName: string | null, 
  content: string,
  extraContent: string | null,
  subject: string,
  template: NewsletterTemplate
): string => {
  const formattedContent = formatContent(content);
  const extraFormattedContent = extraContent ? formatContent(extraContent) : null;
  
  // Use header_color_1 for logo section, header_color_2 for title section
  const logoSectionBg = template.header_gradient_1 || template.header_color_1 || template.header_color;
  const titleSectionBg = template.header_gradient_2 || template.header_color_2 || template.header_color;
  const footerBg = template.footer_gradient || template.footer_color;
  const logoUrl = template.logo_url;
  const featuredImageUrl = template.featured_image_url;
  const footerLogoUrl = template.footer_logo_url || template.logo_url;
  
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
        background-color: ${template.background_color};
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        line-height: 1.6;
      }
      
      /* Container */
      .email-wrapper {
        width: 100%;
        background-color: ${template.background_color};
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
      
      /* Header with two-color sections */
      .email-header {
        position: relative;
      }
      
      .logo-section {
        background: ${logoSectionBg};
        padding: 30px 20px 20px 20px;
        text-align: center;
      }
      
      .logo-section img {
        max-width: 250px;
        height: auto;
        margin: 0 auto;
      }
      
      .header-title {
        background: ${titleSectionBg};
        padding: 20px;
      }
      
      .header-title h1 {
        color: #ffffff;
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
      }
      
      .header-subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 16px;
        margin-top: 8px;
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
        background: ${template.greeting_bg_color || template.header_color_1 || template.header_color};
        color: ${template.greeting_text_color || 'white'};
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
        color: ${template.header_color};
        font-size: 20px;
        margin: 28px 0 12px 0;
        font-weight: 600;
      }
      
      .email-content p {
        margin: 0 0 16px 0;
        color: ${template.content_text_color || '#4a5568'};
        font-size: ${template.content_text_size || '16px'};
        line-height: 1.7;
      }
      
      .email-content ul {
        margin: 20px 0;
        padding-left: 24px;
        list-style-type: disc;
      }
      
      .email-content li {
        margin: 12px 0;
        color: ${template.content_text_color || '#4a5568'};
        font-size: ${template.content_text_size || '16px'};
        line-height: 1.6;
      }
      
      .email-content strong {
        color: #1a1a1a;
        font-weight: 600;
      }
      
      .email-content em {
        font-style: italic;
      }
      
      .email-content a {
        color: ${template.header_color};
        text-decoration: none;
        font-weight: 500;
        border-bottom: 2px solid ${template.header_color}33;
        transition: border-color 0.3s;
      }
      
      .email-content a:hover {
        border-bottom-color: ${template.header_color};
      }
      
      /* Button styles */
      .email-button {
        display: inline-block;
        padding: 14px 32px;
        margin: 24px 0;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      
      .email-button:hover {
        transform: translateY(-2px);
      }
      
      .content-button {
        background: ${template.content_button_color || '#0ea5e9'};
        color: ${template.content_button_text_color || '#ffffff'};
        box-shadow: 0 4px 12px ${template.content_button_shadow_color || '#0ea5e933'};
      }
      
      .content-button:hover {
        box-shadow: 0 6px 16px ${template.content_button_shadow_color || '#0ea5e933'};
      }
      
      .extra-button {
        background: ${template.extra_button_color || '#0ea5e9'};
        color: ${template.extra_button_text_color || '#ffffff'};
        box-shadow: 0 4px 12px ${template.extra_button_shadow_color || '#0ea5e933'};
      }
      
      .extra-button:hover {
        box-shadow: 0 6px 16px ${template.extra_button_shadow_color || '#0ea5e933'};
      }
      
      /* Extra content box */
      .extra-content {
        background: ${template.extra_content_bg_color || `${template.header_color}15`};
        border-left: 4px solid ${template.extra_content_border_color || template.header_color};
        padding: 20px;
        margin: 30px 0;
        border-radius: 8px;
        color: ${template.extra_content_text_color || '#4a5568'};
        font-size: ${template.extra_content_text_size || '16px'};
      }
      
      .extra-content h3 {
        margin-top: 0;
        color: ${template.extra_content_border_color || template.header_color};
        font-size: 20px;
        font-weight: 600;
      }
      
      .extra-content p {
        margin: 8px 0;
        color: #4a5568;
        font-size: 16px;
        line-height: 1.7;
      }
      
      .extra-content strong {
        color: #1a1a1a;
        font-weight: 600;
      }
      
      /* Divider */
      .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        margin: 30px 0;
      }
      
      /* Footer */
      .email-footer {
        background: ${footerBg};
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
        color: ${template.footer_link_color || '#ffffff'};
        text-decoration: none;
        margin: 0 12px;
        font-size: 14px;
        font-weight: 500;
      }
      
      .footer-links a:hover {
        text-decoration: underline;
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
            <img src="${logoUrl}" alt="${template.footer_company}" />
          </div>
          ` : `
          <div class="logo-section">
            <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0;">${template.header_title}</h1>
          </div>
          `}
          <div class="header-title">
            <h1>${template.header_title}</h1>
          </div>
        </div>
        
        <!-- Featured Image (optional) -->
        ${featuredImageUrl ? `
        <img src="${featuredImageUrl}" alt="Featured" class="featured-image" />
        ` : ''}
        
        <!-- Main Content -->
        <div class="email-content">
          ${recipientName ? `<div class="greeting-badge">Kedves ${recipientName}!</div>` : `<div class="greeting-badge">${template.greeting_text}</div>`}
          
          ${formattedContent}
          
          ${template.show_content_button && template.content_button_url && template.content_button_url.trim() !== '' ? `
          <div style="text-align: center;">
            <a href="${template.content_button_url}" class="email-button content-button" style="background: ${template.content_button_color || '#0ea5e9'}; color: ${template.content_button_text_color || '#ffffff'}; box-shadow: 0 4px 12px ${template.content_button_shadow_color || '#0ea5e933'};">
              ${template.content_button_text || 'Olvass tovább'}
            </a>
          </div>
          ` : ''}
          
          ${extraFormattedContent ? `
          <div class="divider"></div>
          <div class="extra-content">
            ${extraFormattedContent}
          </div>
          ${template.show_extra_button && template.extra_button_url && template.extra_button_url.trim() !== '' ? `
          <div style="text-align: center; margin-top: 20px;">
            <a href="${template.extra_button_url}" class="email-button extra-button" style="background: ${template.extra_button_color || '#0ea5e9'}; color: ${template.extra_button_text_color || '#ffffff'}; box-shadow: 0 4px 12px ${template.extra_button_shadow_color || '#0ea5e933'};">
              ${template.extra_button_text || 'További információ'}
            </a>
          </div>
          ` : ''}
          ` : ''}
          
          <div class="divider"></div>
          
          <p style="text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px;">
            ${template.footer_text}
          </p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
          ${footerLogoUrl ? `
          <div class="footer-logo">
            <img src="${footerLogoUrl}" alt="${template.footer_company}" style="filter: brightness(0) invert(1);" />
          </div>
          ` : ''}
          
          ${template.footer_links && template.footer_links.length > 0 ? `
          <div class="footer-links">
            ${template.footer_links.map(link => `<a href="${link.url}">${link.text}</a>`).join('\n            ')}
          </div>
          ` : ''}
          
          <p><strong>${template.footer_company}</strong></p>
          ${template.footer_address ? `<p>${template.footer_address}</p>` : ''}
          
          <p class="copyright">© ${new Date().getFullYear()} Az EAP világ vezető szakfolyóirata. Minden jog fenntartva.</p>
          
          <p style="font-size: 11px; margin-top: 20px;">
            Ha le szeretne iratkozni hírlevelünkről, <a href="mailto:${template.sender_email}?subject=Leiratkozás" style="color: rgba(255,255,255,0.8); text-decoration: underline;">kattintson ide</a>.
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
    const { subject, content, extraContent, subscribers, template }: NewsletterRequest = await req.json();

    if (!subject || !content || !subscribers || subscribers.length === 0 || !template) {
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
      const html = createNewsletterHTML(subscriber.name, content, extraContent || null, subject, template);

      try {
        const emailResponse = await resend.emails.send({
          from: `${template.sender_name} <${template.sender_email}>`,
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
