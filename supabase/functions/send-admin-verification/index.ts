import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { Resend } from "https://esm.sh/resend@4.0.0";
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface VerificationRequest {
  email: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { email }: VerificationRequest = await req.json();
    
    console.log('Sending verification code to:', email);

    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    // Rate limiting: Check for recent verification attempts (max 3 per hour)
    const oneHourAgo = new Date(Date.now() - 3600000).toISOString();
    
    const { count: emailRateCount } = await supabaseClient
      .from('admin_verification_codes')
      .select('*', { count: 'exact', head: true })
      .eq('email', email)
      .gte('created_at', oneHourAgo);

    if (emailRateCount && emailRateCount >= 3) {
      console.log('Rate limit exceeded for email:', email);
      return new Response(
        JSON.stringify({ error: 'Túl sok próbálkozás. Kérjük, próbálja újra 1 óra múlva.' }),
        {
          status: 429,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Generate 6-digit code
    const verificationCode = Math.floor(100000 + Math.random() * 900000).toString();

    // Store verification code
    const { error: dbError } = await supabaseClient
      .from('admin_verification_codes')
      .insert({
        email: email,
        code: verificationCode,
        expires_at: new Date(Date.now() + 10 * 60 * 1000).toISOString(), // 10 minutes
      });

    if (dbError) {
      console.error('Database error:', dbError);
      throw new Error('Failed to store verification code');
    }

    // Send verification email
    const emailResponse = await resend.emails.send({
      from: "DoYouEAP <noreply@doyoueap.com>",
      to: [email],
      subject: "Admin Bejelentkezés - Ellenőrző Kód",
      html: `
        <h1>Admin Bejelentkezés</h1>
        <p>Az ellenőrző kódod:</p>
        <h2 style="font-size: 32px; letter-spacing: 5px; color: #3572ef;">${verificationCode}</h2>
        <p style="color: #666; font-size: 14px;">Ez a kód 10 percig érvényes.</p>
      `,
    });

    console.log("Verification email sent:", emailResponse);

    return new Response(
      JSON.stringify({ success: true }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in send-admin-verification:", error);
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
