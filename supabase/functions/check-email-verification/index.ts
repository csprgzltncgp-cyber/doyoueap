import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';
import { Resend } from "https://esm.sh/resend@4.0.0";

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface CheckVerificationRequest {
  userId: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { userId }: CheckVerificationRequest = await req.json();
    
    console.log('Checking verification status for user:', userId);

    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    // Check if there's a pending approval with generated token
    const { data: approvalData, error: approvalError } = await supabaseClient
      .from('admin_approval_requests')
      .select('*')
      .eq('user_id', userId)
      .eq('pending_verification', false)
      .is('approval_token', 'not.null')
      .order('created_at', { ascending: false })
      .limit(1)
      .maybeSingle();

    if (approvalError || !approvalData) {
      console.log('No verified pending approval found');
      return new Response(
        JSON.stringify({ message: "No pending approval" }),
        {
          status: 200,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Check if approval email already sent
    if (approvalData.approval_email_sent) {
      console.log('Approval email already sent');
      return new Response(
        JSON.stringify({ message: "Approval email already sent" }),
        {
          status: 200,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Send approval email to admin
    const baseUrl = 'https://6e44bc2c-27c6-473f-a4f2-cb11764cf132.lovableproject.com';
    const approvalUrl = `${baseUrl}/approve-admin?token=${approvalData.approval_token}&userId=${userId}`;
    
    const adminEmailResponse = await resend.emails.send({
      from: "DoYouEAP <onboarding@resend.dev>",
      to: ["zoltan.csepregi@cgpeu.com"],
      subject: "Új Admin Regisztráció - Jóváhagyás Szükséges",
      html: `
        <h1>Új Admin Regisztráció</h1>
        <p><strong>Név:</strong> ${approvalData.full_name}</p>
        <p><strong>Email:</strong> ${approvalData.email}</p>
        <p><strong>User ID:</strong> ${userId}</p>
        <p><strong>Email megerősítve:</strong> ✅ Igen</p>
        <br>
        <p>Kattints az alábbi linkre a regisztráció jóváhagyásához:</p>
        <a href="${approvalUrl}" style="display: inline-block; padding: 12px 24px; background-color: #3572ef; color: white; text-decoration: none; border-radius: 6px;">
          Admin Jóváhagyása
        </a>
        <br><br>
        <p style="color: #666; font-size: 14px;">Ez a link 7 napon belül lejár.</p>
      `,
    });

    if (adminEmailResponse.error) {
      console.error("Email error:", adminEmailResponse.error);
      throw new Error(`Email sending failed: ${JSON.stringify(adminEmailResponse.error)}`);
    }

    // Mark email as sent
    await supabaseClient
      .from('admin_approval_requests')
      .update({ approval_email_sent: true })
      .eq('id', approvalData.id);

    console.log('Approval email sent successfully to admin');

    return new Response(
      JSON.stringify({ success: true, message: "Approval request sent to admin" }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in check-email-verification:", error);
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
