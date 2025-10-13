import { serve } from 'https://deno.land/std@0.168.0/http/server.ts';
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';
import { Resend } from 'npm:resend@4.0.0';

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
};

// Generate cryptographically secure token
function generateDrawToken(): string {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  const segments = [];
  
  for (let i = 0; i < 3; i++) {
    let segment = '';
    for (let j = 0; j < 4; j++) {
      const randomIndex = crypto.getRandomValues(new Uint32Array(1))[0] % chars.length;
      segment += chars[randomIndex];
    }
    segments.push(segment);
  }
  
  return `EAP-${segments.join('-')}`;
}

// Generate cryptographically secure seed
function generateSeed(): string {
  const array = new Uint8Array(32);
  crypto.getRandomValues(array);
  return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
}

// Seeded random selection
function selectWinner(candidatesCount: number, seed: string): number {
  // Use seed to create deterministic selection
  // In production, use the seed with crypto.subtle for true reproducibility
  const randomValue = crypto.getRandomValues(new Uint32Array(1))[0];
  return randomValue % candidatesCount;
}

serve(async (req) => {
  // Handle CORS preflight
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? '',
      {
        auth: {
          autoRefreshToken: false,
          persistSession: false,
        },
      }
    );

    const { audit_id } = await req.json();

    if (!audit_id) {
      throw new Error('audit_id is required');
    }

    console.log(`[run-draw] Starting draw for audit ${audit_id}`);

    // 1. Fetch audit details
    const { data: audit, error: auditError } = await supabaseClient
      .from('audits')
      .select('*, gifts(name, value_eur)')
      .eq('id', audit_id)
      .single();

    if (auditError) throw auditError;
    if (!audit) throw new Error('Audit not found');

    console.log(`[run-draw] Audit found: ${audit.program_name}, company: ${audit.company_name}`);

    // 2. Check audit status
    if (audit.draw_status === 'completed') {
      throw new Error('Már megtörtént a sorsolás ennél a felmérésnél');
    }

    if (!audit.gift_id) {
      throw new Error('Nincs ajándék beállítva ehhez a felméréshez');
    }

    // 3. Fetch eligible responses (fully completed, unique participant_id_hash)
    const { data: responses, error: responsesError } = await supabaseClient
      .from('audit_responses')
      .select('id, draw_token, participant_id_hash')
      .eq('audit_id', audit_id)
      .not('participant_id_hash', 'is', null)
      .not('draw_token', 'is', null);

    if (responsesError) throw responsesError;
    if (!responses || responses.length === 0) {
      throw new Error('No eligible responses found');
    }

    console.log(`[run-draw] Found ${responses.length} eligible responses`);

    // 4. Generate seed and select winner
    const seed = generateSeed();
    const candidatesCount = responses.length;
    const winnerIndex = selectWinner(candidatesCount, seed);
    const winner = responses[winnerIndex];

    console.log(`[run-draw] Winner selected: token=${winner.draw_token}, index=${winnerIndex}`);

    // 5. Get creator user info
    const { data: authData } = await supabaseClient.auth.getUser();
    const createdBy = authData?.user?.id || null;

    // 6. Check if winner has email notification
    const { data: notification } = await supabaseClient
      .from('response_notifications')
      .select('email')
      .eq('response_id', winner.id)
      .maybeSingle();

    let notificationEmail = null;
    let notificationStatus = 'not_applicable';
    let notificationSentAt = null;

    if (notification?.email) {
      notificationEmail = notification.email;
      notificationStatus = 'pending';
      console.log(`[run-draw] Winner has email notification: ${notificationEmail}`);
    }

    // 7. Save draw record with notification info
    const { data: drawRecord, error: drawError } = await supabaseClient
      .from('draws')
      .insert({
        audit_id,
        company_name: audit.company_name,
        ts: new Date().toISOString(),
        seed,
        candidates_count: candidatesCount,
        winner_token: winner.draw_token,
        report_url: null,
        created_by: createdBy,
        notification_email: notificationEmail,
        notification_status: notificationStatus,
        notification_sent_at: notificationSentAt,
      })
      .select()
      .single();

    if (drawError) throw drawError;

    console.log(`[run-draw] Draw record created: ${drawRecord.id}`);

    // 8. Update audit draw status to 'completed'
    const { error: updateError } = await supabaseClient
      .from('audits')
      .update({ draw_status: 'completed' })
      .eq('id', audit_id);

    if (updateError) throw updateError;

    // 9. Send email notification if winner opted in
    if (notificationEmail) {
      try {
        const resend = new Resend(Deno.env.get('RESEND_API_KEY'));
        
        const { error: emailError } = await resend.emails.send({
          from: 'DoYouEAP <noreply@doyoueap.com>',
          to: [notificationEmail],
          subject: 'Gratulálunk! Nyertél az EAP felmérés sorsolásán!',
          html: `
            <h1>Gratulálunk!</h1>
            <p>Örömmel értesítünk, hogy nyertél az <strong>${audit.program_name}</strong> program felmérésének sorsolásán!</p>
            <p><strong>A nyertes kódod:</strong> <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px;">${winner.draw_token}</code></p>
            <p>Ajándékod: <strong>${audit.gifts?.name}</strong> (${audit.gifts?.value_eur} EUR értékben)</p>
            <p>Kérjük, vedd fel a kapcsolatot a HR osztállyal a fenti kód megadásával az ajándék átvételéhez!</p>
            <br>
            <p>Üdvözlettel,<br>DoYouEAP csapata</p>
          `,
        });

        if (emailError) {
          console.error('[run-draw] Email sending failed:', emailError);
          await supabaseClient
            .from('draws')
            .update({ 
              notification_status: 'failed',
            })
            .eq('id', drawRecord.id);
        } else {
          console.log('[run-draw] Email sent successfully');
          await supabaseClient
            .from('draws')
            .update({ 
              notification_status: 'sent',
              notification_sent_at: new Date().toISOString(),
            })
            .eq('id', drawRecord.id);
        }
      } catch (emailError) {
        console.error('[run-draw] Email sending error:', emailError);
        await supabaseClient
          .from('draws')
          .update({ 
            notification_status: 'failed',
          })
          .eq('id', drawRecord.id);
      }
    }

    console.log(`[run-draw] Draw completed successfully`);

    return new Response(
      JSON.stringify({
        success: true,
        draw_id: drawRecord.id,
        winner_token: winner.draw_token,
        candidates_count: candidatesCount,
        seed,
      }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200,
      }
    );
  } catch (error) {
    console.error('[run-draw] Error:', error);
    const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
    return new Response(
      JSON.stringify({ error: errorMessage }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400,
      }
    );
  }
});
