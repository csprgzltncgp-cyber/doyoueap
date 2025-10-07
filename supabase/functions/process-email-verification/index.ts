import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { Resend } from "https://esm.sh/resend@4.0.0";
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface ProcessVerificationRequest {
  userId: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { userId }: ProcessVerificationRequest = await req.json();
    
    console.log('Processing email verification for user:', userId);

    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    // Get approval request
    const { data: approvalData, error: approvalError } = await supabaseClient
      .from('admin_approval_requests')
      .select('*')
      .eq('user_id', userId)
      .eq('pending_verification', false)
      .order('created_at', { ascending: false })
      .limit(1)
      .single();

    if (approvalError || !approvalData || !approvalData.approval_token) {
      console.error('No approval request found or no token:', approvalError);
      return new Response(
        JSON.stringify({ success: false, message: "Nincs jóváhagyási kérés" }),
        {
          status: 400,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    const baseUrl = 'https://6e44bc2c-27c6-473f-a4f2-cb11764cf132.lovableproject.com';
    const approvalUrl = `${baseUrl}/approve-admin?token=${approvalData.approval_token}&userId=${userId}`;
    
    // Send approval email to admin
    const adminEmailResponse = await resend.emails.send({
      from: "DoYouEAP <onboarding@resend.dev>",
      to: ["zoltan.csepregi@cgpeu.com"],
      subject: "Új Admin Regisztráció - Jóváhagyás Szükséges",
      html: `
        <h1>Új Admin Regisztráció</h1>
        <p><strong>Név:</strong> ${approvalData.full_name}</p>
        <p><strong>Email:</strong> ${approvalData.email}</p>
        <p><strong>User ID:</strong> ${userId}</p>
        <p><em>Email cím megerősítve ✓</em></p>
        <br>
        <p>Kattints az alábbi linkre a regisztráció jóváhagyásához:</p>
        <a href="${approvalUrl}" style="display: inline-block; padding: 12px 24px; background-color: #3572ef; color: white; text-decoration: none; border-radius: 6px;">
          Admin Jóváhagyása
        </a>
        <br><br>
        <p style="color: #666; font-size: 14px;">Ez a link 7 napon belül lejár.</p>
      `,
    });

    console.log("Admin approval email sent:", adminEmailResponse.data?.id);

    if (adminEmailResponse.error) {
      console.error("Resend error:", adminEmailResponse.error);
      throw new Error(`Admin email sending failed: ${JSON.stringify(adminEmailResponse.error)}`);
    }

    return new Response(
      JSON.stringify({ success: true }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in process-email-verification:", error);
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
