import { serve } from "https://deno.land/std@0.190.0/http/server.ts";
import { Resend } from "https://esm.sh/resend@4.0.0";

const resend = new Resend(Deno.env.get("RESEND_API_KEY"));

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

interface ConfirmationEmailRequest {
  email: string;
  fullName: string;
}

const handler = async (req: Request): Promise<Response> => {
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const { email, fullName }: ConfirmationEmailRequest = await req.json();
    
    console.log('Sending registration confirmation to:', email);

    // Send confirmation email to user
    const userEmailResponse = await resend.emails.send({
      from: "DoYouEAP <onboarding@resend.dev>",
      to: [email],
      subject: "Email Címed Megerősítése Szükséges",
      html: `
        <h1>Köszönjük a Regisztrációt!</h1>
        <p>Kedves ${fullName}!</p>
        <p>Kérjük, erősítsd meg az email címedet a regisztrációs emailben kapott linkre kattintva.</p>
        <p>Az email cím megerősítése után automatikusan értesítjük a rendszergazdát a regisztrációdról.</p>
        <br>
        <p style="color: #666;">Ha nem kaptad meg a megerősítő emailt, kérjük ellenőrizd a spam mappát.</p>
        <p style="color: #666;">DoYouEAP Csapat</p>
      `,
    });

    console.log("Confirmation email sent:", userEmailResponse.data?.id);

    if (userEmailResponse.error) {
      console.error("Resend error:", userEmailResponse.error);
      throw new Error(`Email sending failed: ${JSON.stringify(userEmailResponse.error)}`);
    }

    return new Response(
      JSON.stringify({ success: true, message: "Confirmation email sent" }),
      {
        status: 200,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      }
    );
  } catch (error: any) {
    console.error("Error in send-registration-confirmation:", error);
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
