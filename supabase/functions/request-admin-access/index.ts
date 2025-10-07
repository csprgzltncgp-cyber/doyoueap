import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { Resend } from "https://esm.sh/resend@4.0.0";
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface AdminAccessRequest {
  email: string;
  fullName: string;
  userId: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { email, fullName, userId }: AdminAccessRequest = await req.json();
    
    console.log('Admin access request:', { email, fullName, userId });

    // Generate approval token
    const approvalToken = crypto.randomUUID();
    
    // Store pending approval in database
    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    const { error: dbError } = await supabaseClient
      .from('admin_approval_requests')
      .insert({
        user_id: userId,
        email: email,
        full_name: fullName,
        approval_token: approvalToken,
        expires_at: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(), // 24 hours
      });

    if (dbError) {
      console.error('Database error:', dbError);
      throw new Error('Failed to store approval request');
    }

    // Send approval email
    const baseUrl = 'https://6e44bc2c-27c6-473f-a4f2-cb11764cf132.lovableproject.com';
    const approvalUrl = `${baseUrl}/approve-admin?token=${approvalToken}&userId=${userId}`;
    
    // Send approval email to admin
    const adminEmailResponse = await resend.emails.send({
      from: "DoYouEAP <onboarding@resend.dev>",
      to: ["zoltan.csepregi@cgpeu.com"],
      subject: "Új Admin Regisztráció - Jóváhagyás Szükséges",
      html: `
        <h1>Új Admin Regisztráció</h1>
        <p><strong>Név:</strong> ${fullName}</p>
        <p><strong>Email:</strong> ${email}</p>
        <p><strong>User ID:</strong> ${userId}</p>
        <br>
        <p>Kattints az alábbi linkre a regisztráció jóváhagyásához:</p>
        <a href="${approvalUrl}" style="display: inline-block; padding: 12px 24px; background-color: #3572ef; color: white; text-decoration: none; border-radius: 6px;">
          Admin Jóváhagyása
        </a>
        <br><br>
        <p style="color: #666; font-size: 14px;">Ez a link 24 órán belül lejár.</p>
        <br>
        <p style="color: #999; font-size: 12px;">Link: ${approvalUrl}</p>
      `,
    });

    console.log("Admin email response:", JSON.stringify(adminEmailResponse));

    if (adminEmailResponse.error) {
      console.error("Resend error:", adminEmailResponse.error);
      throw new Error(`Admin email sending failed: ${JSON.stringify(adminEmailResponse.error)}`);
    }

    // Send confirmation email to user
    const userEmailResponse = await resend.emails.send({
      from: "DoYouEAP <onboarding@resend.dev>",
      to: [email],
      subject: "Admin Regisztráció Fogadva",
      html: `
        <h1>Regisztrációd Fogadva</h1>
        <p>Kedves ${fullName}!</p>
        <p>Admin regisztrációdat sikeresen fogadtuk.</p>
        <p>A rendszergazda hamarosan áttekinti a kérésedet, és email értesítést kapsz, amikor a hozzáférésed aktiválásra kerül.</p>
        <br>
        <p style="color: #666;">Köszönjük a türelmedet!</p>
        <p style="color: #666;">DoYouEAP Csapat</p>
      `,
    });

    console.log("User confirmation email sent:", userEmailResponse.data?.id);

    console.log("Approval email sent successfully:", adminEmailResponse.data?.id);

    return new Response(
      JSON.stringify({ success: true, message: "Approval request sent" }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in request-admin-access:", error);
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
