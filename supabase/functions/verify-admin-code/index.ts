import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface VerifyCodeRequest {
  email: string;
  code: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { email, code }: VerifyCodeRequest = await req.json();
    
    console.log('Verifying code for:', email);

    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    // Find matching verification code
    const { data: verificationData, error: verificationError } = await supabaseClient
      .from('admin_verification_codes')
      .select('*')
      .eq('email', email)
      .eq('code', code)
      .order('created_at', { ascending: false })
      .limit(1)
      .single();

    if (verificationError || !verificationData) {
      console.error('Invalid code:', verificationError);
      return new Response(
        JSON.stringify({ verified: false, message: "Érvénytelen kód" }),
        {
          status: 200,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Check if expired
    if (new Date(verificationData.expires_at) < new Date()) {
      return new Response(
        JSON.stringify({ verified: false, message: "A kód lejárt" }),
        {
          status: 200,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Check if already used
    if (verificationData.used) {
      return new Response(
        JSON.stringify({ verified: false, message: "Ezt a kódot már használták" }),
        {
          status: 200,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Mark as used
    await supabaseClient
      .from('admin_verification_codes')
      .update({ used: true, used_at: new Date().toISOString() })
      .eq('id', verificationData.id);

    console.log('Code verified successfully for:', email);

    return new Response(
      JSON.stringify({ verified: true }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in verify-admin-code:", error);
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
