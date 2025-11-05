import { serve } from 'https://deno.land/std@0.168.0/http/server.ts';
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2.38.4';

// Input validation interface
interface SubmitResponseRequest {
  audit_id: string;
  responses: Record<string, unknown>;
  participant_id?: string;
  email?: string;
  email_consent?: boolean;
  employee_metadata?: Record<string, unknown>;
}

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
};

// CRITICAL: SERVER_SECRET is required for secure participant hashing
const SERVER_SECRET = Deno.env.get('SERVER_SECRET');
if (!SERVER_SECRET) {
  console.error('CRITICAL: SERVER_SECRET environment variable not configured');
}

// Generate draw token (12 char alfanumeric with prefix)
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

// Hash participant ID with server secret
async function hashParticipantId(participantId: string): Promise<string> {
  const encoder = new TextEncoder();
  const data = encoder.encode(participantId + SERVER_SECRET);
  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

// Generate anonymized hash for internal reference
async function generateAnonymizedHash(responseId: string): Promise<string> {
  const encoder = new TextEncoder();
  const randomSalt = crypto.randomUUID();
  const data = encoder.encode(randomSalt + responseId);
  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

serve(async (req) => {
  // Handle CORS preflight
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    // Validate SERVER_SECRET is configured
    if (!SERVER_SECRET) {
      console.error('Server configuration error: SERVER_SECRET not set');
      return new Response(
        JSON.stringify({ error: 'Server configuration error. Please contact the administrator.' }),
        { status: 500, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

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

    // Parse and validate request body
    const body = await req.json();
    
    // Validate required fields
    if (!body.audit_id || typeof body.audit_id !== 'string') {
      return new Response(
        JSON.stringify({ error: 'Invalid request: audit_id is required and must be a string' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    if (!body.responses || typeof body.responses !== 'object') {
      return new Response(
        JSON.stringify({ error: 'Invalid request: responses is required and must be an object' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    // UUID validation regex
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
    if (!uuidRegex.test(body.audit_id)) {
      return new Response(
        JSON.stringify({ error: 'Invalid request: audit_id must be a valid UUID' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    // Validate email format if provided
    if (body.email && typeof body.email === 'string') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(body.email) || body.email.length > 255) {
        return new Response(
          JSON.stringify({ error: 'Invalid request: email must be a valid email address' }),
          { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
        );
      }
    }

    // Validate participant_id length if provided
    if (body.participant_id && (typeof body.participant_id !== 'string' || body.participant_id.length > 255)) {
      return new Response(
        JSON.stringify({ error: 'Invalid request: participant_id must be a string with max 255 characters' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    // Validate JSON payload size (max 50KB)
    const bodySize = JSON.stringify(body).length;
    if (bodySize > 50000) {
      return new Response(
        JSON.stringify({ error: 'Request too large: maximum 50KB allowed' }),
        { status: 413, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    const { audit_id, responses, participant_id, email, email_consent, employee_metadata } = body as SubmitResponseRequest;

    console.log(`[submit-response] Submitting response for audit ${audit_id}`);
    console.log(`[submit-response] participant_id:`, participant_id);

    // 1. Fetch audit to check if lottery is enabled
    const { data: audit, error: auditError } = await supabaseClient
      .from('audits')
      .select('gift_id, status, is_active, expires_at')
      .eq('id', audit_id)
      .maybeSingle();

    if (auditError) throw auditError;
    if (!audit) throw new Error('Audit not found');
    
    console.log(`[submit-response] Audit data:`, JSON.stringify(audit));

    // 2. Check if audit is open
    if (!audit.is_active || audit.status === 'closed') {
      throw new Error('Audit is closed');
    }

    if (audit.expires_at && new Date(audit.expires_at) < new Date()) {
      throw new Error('Audit has expired');
    }

    // 3. Prepare lottery fields
    let participantIdHash = null;
    let drawToken = null;

    console.log(`[submit-response] Checking lottery - gift_id: ${audit.gift_id}, participant_id: ${participant_id}`);

    if (audit.gift_id && participant_id) {
      participantIdHash = await hashParticipantId(participant_id);
      drawToken = generateDrawToken();
      console.log(`[submit-response] Lottery enabled, generated token: ${drawToken}`);
    } else {
      console.log(`[submit-response] No lottery - gift_id: ${audit.gift_id}, participant_id: ${participant_id}`);
    }

    // 4. Insert response
    const { data: response, error: responseError } = await supabaseClient
      .from('audit_responses')
      .insert({
        audit_id,
        responses,
        employee_metadata: employee_metadata || {},
        participant_id_hash: participantIdHash,
        draw_token: drawToken,
        anonymized_hash: null, // Will be set after insert
      })
      .select()
      .single();

    if (responseError) {
      // Check if it's a duplicate participant error
      if (responseError.code === '23505' && responseError.message.includes('participant')) {
        throw new Error('Már leadtál egy választ erre a felmérésre.');
      }
      throw responseError;
    }

    console.log(`[submit-response] Response created: ${response.id}`);

    // 5. Update with anonymized hash
    const anonymizedHash = await generateAnonymizedHash(response.id);
    await supabaseClient
      .from('audit_responses')
      .update({ anonymized_hash: anonymizedHash })
      .eq('id', response.id);

    // 6. Save email notification if provided and consented
    if (audit.gift_id && email && email_consent) {
      const { error: notifError } = await supabaseClient
        .from('response_notifications')
        .insert({
          response_id: response.id,
          email,
          consent_ts: new Date().toISOString(),
        });

      if (notifError) {
        console.error('[submit-response] Failed to save email notification:', notifError);
        // Don't fail the whole response if email save fails
      } else {
        console.log(`[submit-response] Email notification saved for winner notification`);
      }
    }

    console.log(`[submit-response] Response submitted successfully`);

    return new Response(
      JSON.stringify({
        success: true,
        response_id: response.id,
        draw_token: drawToken,
        has_lottery: !!audit.gift_id,
      }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200,
      }
    );
  } catch (error) {
    console.error('[submit-response] Error:', error);
    
    // Return user-friendly error message without exposing internal details
    const userMessage = error instanceof Error && error.message.includes('duplicate')
      ? 'Már leadta ezt a felmérést'
      : 'Hiba történt a felmérés beküldésekor. Kérjük, próbálja újra később.';
    
    return new Response(
      JSON.stringify({ 
        error: 'SUBMISSION_FAILED',
        message: userMessage 
      }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400,
      }
    );
  }
});
