import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';
import { Resend } from "https://esm.sh/resend@4.0.0";

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface ApprovalRequest {
  token: string;
  userId: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { token, userId }: ApprovalRequest = await req.json();
    
    console.log('Approving admin access:', { token, userId });

    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    // Verify approval token
    const { data: approvalData, error: approvalError } = await supabaseClient
      .from('admin_approval_requests')
      .select('*')
      .eq('approval_token', token)
      .eq('user_id', userId)
      .single();

    if (approvalError || !approvalData) {
      console.error('Invalid token:', approvalError);
      return new Response(
        JSON.stringify({ success: false, message: "Érvénytelen jóváhagyási link" }),
        {
          status: 400,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Check if expired
    if (new Date(approvalData.expires_at) < new Date()) {
      return new Response(
        JSON.stringify({ success: false, message: "A jóváhagyási link lejárt" }),
        {
          status: 400,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Check if already approved
    if (approvalData.approved) {
      return new Response(
        JSON.stringify({ success: false, message: "Ez a kérés már jóváhagyásra került" }),
        {
          status: 400,
          headers: { "Content-Type": "application/json", ...corsHeaders },
        }
      );
    }

    // Add admin role
    const { error: roleError } = await supabaseClient
      .from('user_roles')
      .insert({
        user_id: userId,
        role: 'admin',
      });

    if (roleError) {
      console.error('Error adding admin role:', roleError);
      throw new Error('Failed to assign admin role');
    }

    // Mark as approved
    await supabaseClient
      .from('admin_approval_requests')
      .update({ approved: true, approved_at: new Date().toISOString() })
      .eq('approval_token', token);

    // Send approval notification to user
    await resend.emails.send({
      from: "DoYouEAP <noreply@doyoueap.hu>",
      to: [approvalData.email],
      subject: "Admin Hozzáférés Aktiválva",
      html: `
        <h1>Admin Hozzáférésed Aktiválva!</h1>
        <p>Kedves ${approvalData.full_name}!</p>
        <p>Az admin hozzáférésed sikeresen jóváhagyásra került.</p>
        <p>Most már beléphetsz a rendszerbe az alábbi linken:</p>
        <a href="https://6e44bc2c-27c6-473f-a4f2-cb11764cf132.lovableproject.com/superadmin" style="display: inline-block; padding: 12px 24px; background-color: #3572ef; color: white; text-decoration: none; border-radius: 6px;">
          Bejelentkezés
        </a>
        <br><br>
        <p style="color: #666;">Üdvözlünk a DoYouEAP admin felületén!</p>
      `,
    });

    console.log('Admin access approved for user:', userId);

    return new Response(
      JSON.stringify({ success: true, message: "Admin jogosultság sikeresen jóváhagyva" }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in approve-admin-access:", error);
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
